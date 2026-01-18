<?php
session_start();
require_once "includes/db.php";

// Initialize message
$message = "";

// ✅ Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ✅ Get item ID and type
$item_id = $_GET['id'] ?? null;
$type = strtolower($_GET['type'] ?? ''); // 'lost' or 'found'

if (!$item_id || !in_array($type, ['lost', 'found'])) {
    die("<div class='alert alert-danger text-center mt-5'>Invalid item request.</div>");
}

// ✅ Determine table and columns
$table = ($type === 'lost') ? 'lost_items' : 'found_items';
$location_col = ($type === 'lost') ? 'lost_location' : 'found_location';
$image_col = 'image';

// ✅ Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $notes = trim($_POST['notes'] ?? '');
    $current_description = $item['description'] ?? '';
    $new_description = $current_description . "\nClaim notes: " . $notes;

    $stmt = $conn->prepare("
        UPDATE $table
        SET status = 'Claimed', description = ?
        WHERE " . ($type === 'lost' ? 'lost_id' : 'found_id') . " = ?
    ");

    $stmt->bind_param("si", $new_description, $item_id);

    if ($stmt->execute()) {
        $message = "<div class='alert alert-success text-center mt-3'>
                        <i class='bi bi-check-circle'></i> Item successfully claimed!
                    </div>";
        // Update $item description to show immediately
        $item['description'] = $new_description;
        $item['status'] = 'Claimed';
    } else {
        $message = "<div class='alert alert-danger text-center mt-3'>
                        <i class='bi bi-x-circle'></i> Error: " . htmlspecialchars($stmt->error) . "
                    </div>";
    }

    $stmt->close();
}

// ✅ Fetch item details
$stmt = $conn->prepare("
    SELECT *, " . ($type === 'lost' ? 'lost_id' : 'found_id') . " AS item_id
    FROM $table
    WHERE " . ($type === 'lost' ? 'lost_id' : 'found_id') . " = ?
");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();
$stmt->close();

if (!$item) {
    die("<div class='alert alert-warning text-center mt-5'>Item not found.</div>");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Claim Item</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background: #f4f7fc; }
    .card { border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
    img { border-radius: 6px; }
</style>
</head>
<body>
<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="bi bi-hand-index-thumb"></i> Claim <?= ucfirst($type) ?> Item</h2>
        <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-house"></i> Home</a>
    </div>

    <!-- Status Message -->
    <?= $message ?>

    <!-- Item Details -->
    <div class="card p-4 mb-4">
        <h5 class="mb-3">Item Details</h5>
        <div class="row">
            <div class="col-md-4">
                <?= $item[$image_col] ? "<img src='{$item[$image_col]}' class='img-fluid'>" : "<span class='text-muted'>No image</span>" ?>
            </div>
            <div class="col-md-8">
                <p><strong>Name:</strong> <?= htmlspecialchars($item['item_title']) ?></p>
                <p><strong>Category:</strong> <?= htmlspecialchars($item['category']) ?></p>
                <p><strong>Location:</strong> <?= htmlspecialchars($item[$location_col]) ?></p>
                <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($item['description'])) ?></p>
                <p><strong>Status:</strong> <span class="badge bg-info"><?= htmlspecialchars($item['status']) ?></span></p>
            </div>
        </div>
    </div>

    <!-- Claim Form -->
    <?php if ($item['status'] !== 'Claimed'): ?>
    <div class="card p-4">
        <h5 class="mb-3">Submit Claim</h5>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-semibold">Claim Notes (optional)</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Add any notes..."></textarea>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary w-50">
                    <i class="bi bi-check-circle"></i> Claim Item
                </button>
            </div>
        </form>
    </div>
    <?php else: ?>
        <div class="alert alert-success text-center mt-3">This item has already been claimed.</div>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<?php include("includes/footer.php"); ?>
</body>
</html>
