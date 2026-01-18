<?php
session_start();
require_once "./includes/db.php";

$message = "";

//---Auth checks
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'] ?? 'User';
$role = $_SESSION['role'] ?? 'user';

//Handle form submissions
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $item_title     = trim($_POST['item_title'] ?? '');
    $category       = trim($_POST['category'] ?? '');
    $found_location = trim($_POST['found_location'] ?? '');
    $found_date     = trim($_POST['found_date'] ?? '');
    $description    = trim($_POST['description'] ?? '');
    $image_path     = null;

    // Basic validation
    if (
        empty($item_title) || empty($category) ||
        empty($found_location) || empty($found_date)
    ) {
        $message = "<div class='alert alert-warning'>⚠ Please fill in all required fields.</div>";
    }

//upload image
    if (empty($message) && !empty($_FILES['image']['name'])) {

        $upload_dir = "uploads/found_items/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($ext, $allowed)) {
            $message = "<div class='alert alert-danger'>❌ Invalid image format.</div>";
        } else {
            $file_name = time() . "_" . uniqid() . "." . $ext;
            $image_path = $upload_dir . $file_name;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
                $image_path = null;
                $message = "<div class='alert alert-danger'>❌ Failed to upload image.</div>";
            }
        }
    }

    if (empty($message)) {

        $sql = "
            INSERT INTO found_items
            (user_id, item_title, description, category, found_location, found_date, image, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'Found')
        ";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            $message = "<div class='alert alert-danger'>❌ Prepare failed: " . htmlspecialchars($conn->error) . "</div>";
        } else {
            $stmt->bind_param(
                "issssss",
                $user_id,
                $item_title,
                $description,
                $category,
                $found_location,
                $found_date,
                $image_path
            );

            if ($stmt->execute()) {
                $message = "<div class='alert alert-success'>✅ Found item reported successfully.</div>";
            } else {
                $message = "<div class='alert alert-danger'>❌ Execute failed: " . htmlspecialchars($stmt->error) . "</div>";
            }

            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Report Found Item</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background: #f8f9fa; }
    .card { border-radius: 16px; box-shadow: 0 6px 18px rgba(0,0,0,0.08); }
    .btn-custom { border-radius: 8px; padding: 12px; font-size: 1.1rem; }
  </style>
</head>
<body>

<div class="container py-5">
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold"><i class="bi bi-journal-check"></i> Report Found Item</h2>
    <div>
      <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-house"></i> Home</a>
      <a href="logout.php" class="btn btn-outline-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>
  </div>

  <!-- Status Message -->
  <?php echo $message; ?>

<!-- Form Card -->
<div class="card p-5 shadow-sm">
  <form method="POST" enctype="multipart/form-data" class="row g-4">

    <!-- Item Title -->
    <div class="col-md-6">
      <label class="form-label fw-semibold">Item Title</label>
      <input 
        type="text" 
        name="item_title" 
        class="form-control" 
        placeholder="e.g. Samsung Galaxy Phone" 
        required>
    </div>

    <!-- Category -->
    <div class="col-md-6">
      <label class="form-label fw-semibold">Category</label>
      <select name="category" class="form-select" required>
        <option value="">Select category</option>
        <option value="Phone">📱 Phone</option>
        <option value="Wallet">👛 Wallet</option>
        <option value="Keys">🔑 Keys</option>
        <option value="Documents">📄 Documents</option>
        <option value="Electronics">💻 Electronics</option>
        <option value="Other">📦 Other</option>
      </select>
    </div>

    <!-- Date Found -->
    <div class="col-md-6">
      <label class="form-label fw-semibold">Date Found</label>
      <input 
        type="date" 
        name="found_date" 
        class="form-control" 
        required>
    </div>

    <!-- Found Location -->
    <div class="col-12">
      <label class="form-label fw-semibold">Found Location</label>
      <input 
        type="text" 
        name="found_location" 
        class="form-control" 
        placeholder="e.g. Kampala Taxi Park" 
        required>
    </div>

    <!-- Description -->
    <div class="col-12">
      <label class="form-label fw-semibold">Description</label>
      <textarea 
        name="description" 
        class="form-control" 
        rows="4"
        placeholder="Color, brand, unique marks, condition, etc."></textarea>
    </div>

    <!-- Image Upload -->
    <div class="col-12">
      <label class="form-label fw-semibold">Upload Item Image (optional)</label>
      <input 
        type="file" 
        name="image" 
        class="form-control" 
        accept="image/*">
    </div>

    <!-- Submit Button -->
    <div class="col-12 text-center">
      <button type="submit" class="btn btn-success w-50">
        <i class="bi bi-check-circle"></i> Submit Found Item Report
      </button>
    </div>

  </form>
</div>

</div>
 <!-- Footer -->
  <?php include('includes/footer.php'); ?>
</body>
</html>
