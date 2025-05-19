<?php
error_reporting(E_ALL); // Report all types of errors including E_NOTICE and E_WARNING
ini_set('display_errors', '1');
session_start();
include 'Config.php';  // Assume Config.php contains your database connection settings

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect input from the form
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO admin (username, email, password_hash) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashed_password);
    
    if ($stmt->execute()) {
        echo "<script>alert('Admin added successfully!'); window.location = 'admin_list.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
    
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Basic -->
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <!-- Mobile Metas -->
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <!-- Site Metas -->
  <meta name="keywords" content="" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <link rel="shortcut icon" href="images/favicon.png" type="image/x-icon">

  <title>Paspark</title>


  <!-- bootstrap core css -->
  <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />

  <!-- fonts style -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">

  <!-- nice selecy -->
  <link rel="stylesheet" href="css/nice-select.min.css">

  <!--owl slider stylesheet -->
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

  <!-- font awesome style -->
  <link href="css/font-awesome.min.css" rel="stylesheet" />

  <!-- Custom styles for this template -->
  <link href="css/style.css" rel="stylesheet" />
  <link href="css/styles.css" rel="stylesheet" />
  <!-- responsive style -->
  <link href="css/responsive.css" rel="stylesheet" />


</head>

<body class="sub_page">
<div class="hero_area">
    <div class="bg-box">
      <img src="images/slider-bg.jpg" alt="">
    </div>
    <!-- header section strats -->
    <?php include("include/header.php");?>
    <!-- end header section -->
  </div>
<div class="container mt-5">
<div class="row">
    <div class="col-md-6">
    <h2><a href="javascript:history.back();" style="text-decoration: none; color: inherit;"><i class="fas fa-arrow-left"> </i> </a>Add User</h2>
    <form method="POST">
    <div class="mb-3">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" class="form-control" required>
        </div>

        <div class="mb-3">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
        <button type="submit" class="btn btn-info">Add Admin</button>
        </div>
    </form>
</div>
</div>
</div>
<?php include("include/footer.php");?>
  <!-- footer section -->
</body>




</html>


