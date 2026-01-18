<?php
require_once "../includes/db.php";
require_once "admin-auth.php"; // ensures admin access

$message = "";

/* ===============================
   HANDLE ADMIN ACTIONS
=================================*/
if (isset($_GET['action'], $_GET['id'])) {
    $user_id = (int)$_GET['id'];
    $action  = $_GET['action'];

    switch ($action) {

        case 'activate':
            $stmt = $conn->prepare("UPDATE users SET status='Active' WHERE user_id=?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $message = "✅ User account activated";
            break;

        case 'suspend':
            $stmt = $conn->prepare("UPDATE users SET status='Suspended' WHERE user_id=?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $message = "⚠️ User account suspended";
            break;

        case 'make_admin':
            $stmt = $conn->prepare("UPDATE users SET role='admin' WHERE user_id=?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $message = "👑 User promoted to admin";
            break;

        case 'delete':
            $stmt = $conn->prepare("DELETE FROM users WHERE user_id=?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $message = "🗑️ User deleted successfully";
            break;
    }
}

/* ===============================
   FETCH USERS
=================================*/
$result = $conn->query("
    SELECT user_id, full_name, email, role, status, created_at
    FROM users
    ORDER BY created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Admin Dashboard - Lost & Found System</title>

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet" />

    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">

<?php include_once('includes/navbar.php'); ?>

<div id="layoutSidenav">
    <?php include_once('includes/sidenav.php'); ?>
    <div id="layoutSidenav_content">

    <div class="container-fluid px-4 mt-4">
    <h2 class="mb-4">👤 User Management</h2>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">

            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Date Joined</th>
                        <th width="260">Actions</th>
                    </tr>
                </thead>

                <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($u = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $u['user_id'] ?></td>
                        <td><?= htmlspecialchars($u['full_name']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>

                        <td>
                            <span class="badge bg-<?= $u['role'] === 'admin' ? 'primary' : 'secondary' ?>">
                                <?= ucfirst($u['role']) ?>
                            </span>
                        </td>

                        <td>
                            <span class="badge bg-<?= $u['status'] === 'Active' ? 'success' : 'danger' ?>">
                                <?= $u['status'] ?>
                            </span>
                        </td>

                        <td><?= date('d M Y', strtotime($u['created_at'])) ?></td>

                        <td>
                            <?php if ($u['status'] === 'Active'): ?>
                                <a href="?action=suspend&id=<?= $u['user_id'] ?>" 
                                   class="btn btn-warning btn-sm">Suspend</a>
                            <?php else: ?>
                                <a href="?action=activate&id=<?= $u['user_id'] ?>" 
                                   class="btn btn-success btn-sm">Activate</a>
                            <?php endif; ?>

                            <?php if ($u['role'] !== 'admin'): ?>
                                <a href="?action=make_admin&id=<?= $u['user_id'] ?>" 
                                   class="btn btn-primary btn-sm">Make Admin</a>
                            <?php endif; ?>

                            <a href="?action=delete&id=<?= $u['user_id'] ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Delete this user permanently?')">
                               Delete
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">No users found</td>
                    </tr>
                <?php endif; ?>
                </tbody>

            </table>

        </div>
    </div>

</div>
        <?php include("includes/footer.php"); ?>
    </div>
</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"></script>
<script>
    const dataTable = new simpleDatatables.DataTable("#datatablesSimple");
</script>
</body>
</html>