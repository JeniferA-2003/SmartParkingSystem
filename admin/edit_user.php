<?php
session_start();
include 'Config.php'; // Ensure database connection file is included
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Fetch user details
$userId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user = null;

if ($userId > 0) {
    $stmt = $conn->prepare("SELECT * FROM admin WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
    } else {
        die("<script>alert('User not found.'); window.location.href = 'manage_users.php';</script>");
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username'], $_POST['email'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    // Update the user details
    $stmt = $conn->prepare("UPDATE admin SET username = ?, email = ?, role = ? WHERE id = ?");
    $stmt->bind_param("sssi", $username, $email, $role, $userId);
    if ($stmt->execute()) {
        echo "<script>alert('User updated successfully.'); window.location.href = 'manage_users.php';</script>";
    } else {
        echo "<script>alert('Error updating user.');</script>";
    }
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
        <h2> <a href="javascript:history.back();" style="text-decoration: none; color: inherit;"><i class="fas fa-arrow-left"> </i> </a>Edit User</h2>
        <?php if ($user): ?>
        <form action="" method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Username:</label>
                <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role:</label>
                <input type="text" class="form-control" id="role" name="role" value="<?= $user['role'] ?>">
            </div>
            <button type="submit" class="btn btn-info">Update User</button>
        </form>
        <?php endif; ?>
    </div>
    </div>
    </div>
    <?php include("include/footer.php");?>
  <!-- footer section -->
</body>




</html>

