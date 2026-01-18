<?php
session_start();
require_once './includes/db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $full_name = trim($_POST['full_name'] ?? '');
    $password  = trim($_POST['password'] ?? '');

    if (empty($full_name) || empty($password)) {
        $message = "<div class='alert alert-warning'>⚠ Please fill in all fields.</div>";
    } else {

        $sql = "SELECT user_id, full_name, password, role, status
                FROM users
                WHERE full_name = ?
                LIMIT 1";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $full_name);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();

            if (!password_verify($password, $user['password'])) {
                $message = "<div class='alert alert-danger'>❌ Invalid password.</div>";
            } elseif ($user['status'] !== 'active') {
                $message = "<div class='alert alert-danger'>❌ Account inactive.</div>";
            } else {

                // 🔐 Secure session
                session_regenerate_id(true);

                $_SESSION['user_id']   = $user['user_id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role']      = $user['role'];

                header("Location: index.php");
                exit;
            }

        } else {
            $message = "<div class='alert alert-warning'>⚠ User not found.</div>";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>LostFound Project</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="assets/vendors/font-awesome/css/font-awesome.min.css">
    <!-- Layout styles -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="assets/images/logo2.png" style="size: 500px;" />
  </head>
  <body>
  <div class="container-scroller">
    <div class="content-wrapper d-flex align-items-center auth"  style="
    background-image: url('assets/images/login_page.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    min-height: 100vh;
  ">
      <div class="row flex-grow justify-content-center">
        <div class="col-lg-4">
          <div class="auth-form-light text-center p-5">

            <!-- Logo -->
            <div class="brand-logo mb-4">
              <img 
                src="assets/images/logo2.png" 
                alt="Lost & Found Logo"
                class="img-fluid"
                style="max-width: 900px;"
              >
            </div>

            <h4>Hello! let's get started</h4>
            <h6 class="font-weight-light mb-4">Sign in to continue.</h6>

            <?php
            if (!empty($message)) {
              echo "<p class='text-success text-center'>$message</p>";
            }
            ?>

            <form class="pt-3" method="POST" action="login.php">
              <div class="form-group mb-3">
                <input type="text" class="form-control form-control-lg"
                  name="full_name" placeholder="Full Name" required>
              </div>

              <div class="form-group mb-3">
                <input type="password" class="form-control form-control-lg"
                  name="password" placeholder="Password" required>
              </div>

              <div class="mt-3 d-grid">
                <button type="submit"
                  class="btn btn-danger btn-lg font-weight-medium auth-form-btn">
                  Login
                </button>
              </div>

              <div class="my-3 d-flex justify-content-between align-items-center">
                <div class="form-check">
                  <label class="form-check-label text-muted">
                    <input type="checkbox" class="form-check-input"> Keep me signed in
                  </label>
                </div>
                <a href="#" class="auth-link text-primary">Forgot password?</a>
              </div>

              <div class="text-center mt-4 font-weight-light">
                Don't have an account?
                <a href="register.php" class="text-primary">Create</a>
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>
  
</div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/misc.js"></script>
    <script src="assets/js/settings.js"></script>
    <script src="assets/js/todolist.js"></script>
    <script src="assets/js/jquery.cookie.js"></script>
    <!-- endinject -->
  </body>
</html>