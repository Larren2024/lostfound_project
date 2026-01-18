<?php
require_once "../includes/db.php";
require_once "admin-auth.php";

// Dashboard statistics
$stats = [
    'users'       => $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0],
    'lost'        => $conn->query("SELECT COUNT(*) FROM lost_items")->fetch_row()[0],
    'found'       => $conn->query("SELECT COUNT(*) FROM found_items")->fetch_row()[0],
    'recovered'   => $conn->query("SELECT COUNT(*) FROM lost_items WHERE status='Recovered'")->fetch_row()[0],
    'pending_claims' => $conn->query("SELECT COUNT(*) FROM lost_items WHERE status='Pending'")->fetch_row()[0],
];
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

    <style>
        .card { border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .dashboard-stats .card-body { font-size: 1.1rem; }
        .dashboard-stats .card-body strong { font-size: 1.6rem; display: block; margin-top: 8px; }
        .table img { width: 60px; border-radius: 6px; }
        .badge-status { font-size: 0.9rem; }
    </style>
</head>
<body class="sb-nav-fixed">

<?php include_once('includes/navbar.php'); ?>

<div id="layoutSidenav">
    <?php include_once('includes/sidenav.php'); ?>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Admin Dashboard</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item active">Overview of Lost & Found System</li>
                </ol>

                <!-- Dashboard Cards -->
                <div class="row dashboard-stats mb-4">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body text-center">
                                👤 Users
                                <strong><?= $stats['users'] ?></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card text-dark bg-warning">
                            <div class="card-body text-center">
                                📦 Lost Items
                                <strong><?= $stats['lost'] ?></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card text-white bg-info">
                            <div class="card-body text-center">
                                🎒 Found Items
                                <strong><?= $stats['found'] ?></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card text-white bg-success">
                            <div class="card-body text-center">
                                ✅ Recovered
                                <strong><?= $stats['recovered'] ?></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card text-white bg-danger">
                            <div class="card-body text-center">
                                ⏳ Pending Claims
                                <strong><?= $stats['pending_claims'] ?></strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Lost & Found Items Table -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-list"></i>
                        Recent Reported Items
                    </div>
                    <div class="card-body table-responsive">
                        <table id="datatablesSimple" class="table table-striped table-bordered align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Type</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Photo</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Fetch recent 30 items
                                $sql_items = "SELECT * FROM (
                                    SELECT lost_id AS id, 'Lost' AS type, item_title, category, lost_location AS location, status, image, created_at
                                    FROM lost_items
                                    UNION ALL
                                    SELECT found_id AS id, 'Found' AS type, item_title, category, found_location AS location, status, image, created_at
                                    FROM found_items
                                ) AS combined
                                ORDER BY created_at DESC
                                LIMIT 30";

                                $res = $conn->query($sql_items);
                                if($res && $res->num_rows > 0){
                                    while($row = $res->fetch_assoc()){
                                        $badgeColor = ($row['type']=='Lost') ? 'bg-danger' : 'bg-success';
                                        echo "<tr>
                                            <td>{$row['id']}</td>
                                            <td><span class='badge $badgeColor'>{$row['type']}</span></td>
                                            <td>{$row['item_title']}</td>
                                            <td>{$row['category']}</td>
                                            <td>{$row['location']}</td>
                                            <td><span class='badge bg-info badge-status'>{$row['status']}</span></td>
                                            <td>".($row['image'] ? "<img src='{$row['image']}' alt='Item'>" : "-")."</td>
                                            <td>
                                                <a href='claim_item.php?id={$row['id']}&type={$row['type']}' class='btn btn-sm btn-primary'>
                                                    <i class='fas fa-hand-paper'></i> Claim
                                                </a>
                                            </td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='8' class='text-center text-muted'>No items found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
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
