<?php
session_start();

// 🔐 Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once "./includes/db.php";

$message = "";

// 📝 Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $user_id        = $_SESSION['user_id'];
    $item_title     = trim($_POST['item_title']);
    $category       = trim($_POST['category']);
    $lost_location  = trim($_POST['lost_location']);
    $lost_date      = $_POST['lost_date'];
    $description    = trim($_POST['description']);
    $image_path     = null;

    // ✅ Basic validation
    if (empty($item_title) || empty($category) || empty($lost_location) || empty($lost_date)) {
        $message = "<div class='alert alert-danger'>All required fields must be filled.</div>";
    }

    // 📸 Image upload handling
    if (empty($message) && !empty($_FILES['image']['name'])) {
        $upload_dir = "uploads/lost_items/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $allowed_ext = ['jpg','jpeg','png'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed_ext)) {
            $message = "<div class='alert alert-danger'>Invalid image format.</div>";
        } else {
            $image_name = uniqid("lost_", true) . "." . $ext;
            $image_path = $upload_dir . $image_name;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
                $image_path = null;
                $message = "<div class='alert alert-warning'>Image upload failed.</div>";
            }
        }
    }

    // 💾 Insert into database
    if (empty($message)) {
        $sql = "INSERT INTO lost_items 
                (user_id, item_title, description, category, lost_date, lost_location, image , status)
                VALUES (?, ?, ?, ?, ?, ?, ?,'Lost')";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            $message = "<div class='alert alert-danger'>DB Error: {$conn->error}</div>";
        } else {
            $stmt->bind_param(
                "issssss",
                $user_id,
                $item_title,
                $description,
                $category,
                $lost_date,
                $lost_location,
                $image_path,
            );

            if ($stmt->execute()) {
                $message = "<div class='alert alert-success'>✅ Lost item reported successfully.</div>";
            } else {
                $message = "<div class='alert alert-danger'>Insert failed.</div>";
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
  <title>Report Lost Item</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background: #f8f9fa;
    }
    .card {
      border-radius: 16px;
      box-shadow: 0 6px 18px rgba(0,0,0,0.08);
    }
    .btn-custom {
      border-radius: 8px;
      padding: 12px;
      font-size: 1.1rem;
    }
  </style>
</head>
<body>

<div class="container py-5">

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">
      <i class="bi bi-journal-text"></i> Report Lost Item
    </h2>
    <div>
      <a href="index.php" class="btn btn-outline-secondary">
        <i class="bi bi-house"></i> Home
      </a>
      <a href="logout.php" class="btn btn-outline-danger">
        <i class="bi bi-box-arrow-right"></i> Logout
      </a>
    </div>
  </div>

  <!-- Status Message -->
  <?php if (!empty($message)) echo $message; ?>

  <!-- Form Card -->
  <div class="card shadow-sm p-5">
    <form method="POST" enctype="multipart/form-data" class="row g-4">

      <!-- Item Title -->
      <div class="col-md-6">
        <label class="form-label fw-semibold">Item Title</label>
        <input type="text" name="item_title" class="form-control"
               placeholder="e.g. Samsung Galaxy S21" required>
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
          <option value="Bag">🎒 Bag</option>
          <option value="Money">💵 Money</option>
          <option value="Other">📦 Other</option>
        </select>
      </div>

      <!-- Date Lost -->
      <div class="col-md-6">
        <label class="form-label fw-semibold">Date Lost</label>
        <input type="date" name="lost_date" class="form-control" required>
      </div>

      <!-- Location Lost -->
      <div class="col-md-6">
        <label class="form-label fw-semibold">Location Lost</label>
        <input type="text" name="lost_location" class="form-control"
               placeholder="e.g. Kampala Taxi Park" required>
      </div>

      <!-- Description -->
      <div class="col-12">
        <label class="form-label fw-semibold">Item Description</label>
        <textarea name="description" class="form-control" rows="4"
                  placeholder="Color, brand, model, condition, etc." required></textarea>
      </div>

      <!-- Image Upload -->
      <div class="col-12">
        <label class="form-label fw-semibold">Upload Item Image (optional)</label>
        <input type="file" name="image" class="form-control" accept="image/*">
      </div>

      <!-- Submit -->
      <div class="col-12 text-center mt-3">
        <button type="submit" class="btn btn-danger w-50 py-2">
          <i class="bi bi-flag"></i> Submit Lost Item
        </button>
      </div>

    </form>
  </div>
</div>

<?php include('includes/footer.php'); ?>

</body>
</html>
