<?php
session_start();
include('./includes/db.php');

// register.php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);// Hash the password
    $status = $_POST['status'];

    $sql = "INSERT INTO users (full_name ,email ,phone , role, password, status) VALUES (?, ?, ?, ?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $full_name, $email,$phone, $role, $password,$status);

    if ($stmt->execute()) {
        echo "Registration successful!";
        header("Location:login.php");

    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>LostFound Projects</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="assets/vendors/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="assets/images/logo2.png" style="size: 500px;" />
  </head>
  <body>
   
<div class="container-scroller">
  <div class="container-fluid page-body-wrapper full-page-wrapper">
    <div class="content-wrapper d-flex align-items-center auth"style="
    background-image: url('assets/images/register_page.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    min-height: 100vh;
  ">
      <div class="row flex-grow">
        <div class="col-lg-4 mx-auto">
          <div class="auth-form-light text-center p-5">
            <div class="brand-logo">
              <img src="assets/images/logo2.png">
            </div>
            <h4>New here?</h4>
            <h6 class="font-weight-light">Signing up is easy. It only takes a few steps</h6>
               <?php
            if (!empty($message)) {
            echo "<p style='color: green; text-align: center;'>$message</p>";
            } ?>

            <!-- Registration Form -->
            <form class="pt-3" method="POST" action="">

              <div class="form-group">
                <input type="text" class="form-control form-control-lg" name="full_name" placeholder="Full Names" required>
              </div>

              <div class="form-group">
                <input type="email" class="form-control form-control-lg" name="email" placeholder="Email" required>
              </div>


              <div class="form-group">
                <input type="text" class="form-control form-control-lg" name="phone" placeholder="Phone" required>
              </div>

              <div class="form-group">
                <select class="form-select form-select-lg" name="role" required>
                  <option value="user">local User</option>
                  <option value="admin">Admin</option>
                </select>
              </div>

              <div class="form-group">
                <input type="password" class="form-control form-control-lg" name="password" placeholder="Password" required>
              </div>

              <div class="form-group">
                <select class="form-select form-select-lg" name="status" required>
                  <option value="active">active</option>
                  <option value="suspended">suspended</option>
                </select>
              </div>



              <div class="mt-3 d-grid gap-2">
                <button type="submit" class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn">
                  SIGN UP
                </button>
              </div>

              <div class="text-center mt-4 font-weight-light">
                Already have an account? <a href="login.php" class="text-primary">Login</a>
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>


    <!-- container-scroller -->

    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/misc.js"></script>
    <script src="assets/js/settings.js"></script>
    <script src="assets/js/todolist.js"></script>
    <script src="assets/js/jquery.cookie.js"></script>
    <!-- endinject -->
  </body>
</html>