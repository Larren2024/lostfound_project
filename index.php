<?php
session_start();
require_once "includes/db.php";


// Auth Protection

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];


//Fetch User
$stmt = $conn->prepare("
    SELECT user_id, full_name, role 
    FROM users 
    WHERE user_id = ? 
    LIMIT 1
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit();
}

/* Redirect admins */
if ($user['role'] === 'admin') {
    header("Location: admin/dashboard.php");
    exit();
}


//Search Filters

$search   = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

$where = "WHERE 1=1";
$params = [];

if ($search) {
    $where .= " AND item_title LIKE ?";
    $params[] = "%$search%";
}

if ($category) {
    $where .= " AND category = ?";
    $params[] = $category;
}


// Combined Query
$sql = "
SELECT * FROM (
    SELECT 
        lost_id AS item_id,
        'Lost' AS type,
        item_title,
        category,
        lost_location AS location,
        status,
        image,
        created_at
    FROM lost_items
    $where

    UNION ALL

    SELECT 
        found_id AS item_id,
        'Found' AS type,
        item_title,
        category,
        found_location AS location,
        status,
        image,
        created_at
    FROM found_items
    $where
) AS items
ORDER BY created_at DESC
LIMIT 30
";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $types = str_repeat("s", count($params));
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$items = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Lost & Found System</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link rel="shortcut icon" href="assets/images/logo2.png" style="size: 500px;" />

<style>
body { background:#f4f7fc; }
.card { border-radius:12px; box-shadow:0 4px 10px rgba(0,0,0,.06); }
img { border-radius:6px; }
</style>
</head>

<body>
<div class="container py-5">

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="fw-bold">📦 Lost & Found System</h1>
        <h1 class="text-muted">Welcome, <?= htmlspecialchars($user['full_name']) ?></h1>
    </div>
    <a href="logout.php" class="btn btn-outline-danger">
        <i class="bi bi-box-arrow-right"></i> Logout
    </a>
</div>

<!-- ACTION BUTTONS -->
<div class="d-flex justify-content-center gap-3 mb-4">
    <a href="report_lost.php" class="btn btn-danger btn-lg">
        <i class="bi bi-exclamation-circle"></i> Report Lost Item
    </a>
    <a href="report_found.php" class="btn btn-success btn-lg">
        <i class="bi bi-check-circle"></i> Report Found Item
    </a>
</div>

<!-- SEARCH -->
<div class="card p-4 mb-4">
    <h5 class="mb-3"><i class="bi bi-search"></i> Search Items</h5>
    <form method="GET" class="row g-3">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control"
                   value="<?= htmlspecialchars($search) ?>"
                   placeholder="Item name">
        </div>
        <div class="col-md-4">
            <select name="category" class="form-select">
                <option value="">All Categories</option>
                <?php
                $cats = ['Phone','Wallet','Keys','Documents','Electronics','Other'];
                foreach ($cats as $c) {
                    $sel = ($category === $c) ? 'selected' : '';
                    echo "<option value='$c' $sel>$c</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-md-4">
            <button class="btn btn-primary w-100">
                <i class="bi bi-search"></i> Search
            </button>
        </div>
    </form>
</div>

<!-- ITEMS TABLE -->
<div class="card p-4">
    <h5 class="mb-3"><i class="bi bi-list-check"></i> Reported Items</h5>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Item</th>
                    <th>Category</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Image</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($items->num_rows > 0): ?>
                <?php while ($row = $items->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['item_id'] ?></td>

                    <td>
                        <span class="badge <?= $row['type']==='Lost'?'bg-danger':'bg-success' ?>">
                            <?= $row['type'] ?>
                        </span>
                    </td>

                    <td><?= htmlspecialchars($row['item_title']) ?></td>
                    <td><?= htmlspecialchars($row['category']) ?></td>
                    <td><?= htmlspecialchars($row['location']) ?></td>

                    <td>
                        <span class="badge bg-info"><?= $row['status'] ?></span>
                    </td>

                    <td>
                        <?= $row['image']
                            ? "<img src='{$row['image']}' width='60'>"
                            : "<span class='text-muted'>No Image</span>" ?>
                    </td>

                    <td>
                        <?php if (in_array($row['status'], ['Lost','Found'])): ?>
                            <a href="claim_item.php?id=<?= $row['item_id'] ?>&type=<?= $row['type'] ?>"
                               class="btn btn-sm btn-primary">
                               <i class="bi bi-hand-index-thumb"></i> Claim
                            </a>
                        <?php else: ?>
                            <span class="text-success fw-bold">Resolved</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center text-muted">
                        No items found
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<?php include("includes/footer.php"); ?>
</body>
</html>
