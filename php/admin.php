<?php
session_start();
 if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: index.php");
    exit;}
?>

<!DOCTYPE html>
<html>
<head><title>Admin Panel</title></head>
 <body>
  <h1>Admin Panel</h1>
   <p>Hoşgeldin
    <?php echo $_SESSION['name'];?> |
   <a href="index.php">Ana Sayfa</a> | <a href="logout.php">Çıkış</a></p>
  <h2>Yönetim</h2>
  <ul>
   <li><a href="manage_companies.php">Firma Yönetimi</a></li>
    <li><a href="manage_admins.php">Admin Yönetimi</a></li>
   <li><a href="manage_coupons.php">Kupon Yönetimi</a></li>
  </ul>
 </body>
</html>
