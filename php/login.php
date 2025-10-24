<?php
 session_start();
 if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $mail = $_POST['mail'];
    $pass = $_POST['pass'];
      $db = new SQLite3('../db/database.db');
     $sql = "SELECT * FROM users WHERE mail = :mail";
    $stmt = $db->prepare($sql);
    $stmt ->bindValue(':mail', $mail, SQLITE3_TEXT);
  $result = $stmt->execute();
    $user = $result->fetchArray(SQLITE3_ASSOC);

 if($user && password_verify($pass, $user['pass'])){

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['comp_id'] = $user['comp_id'];

    if ($user['role'] == 'admin') {
    header("Location: admin.php");
    exit;}
    elseif ($user['role'] == 'firma_admin') {
    header("Location: firma_panel.php");
    exit;}
    else {
    header("Location: index.php");
    exit;}}
    else {echo "E-Mail veya Şifre Yanlış!";}}
?>

<html>
 <head>
  <title>Giriş Yap</title>
 </head>
  <body>
   <h2>Giriş Yap</h2>
     <?php
      if (isset($_GET['error']) && $_GET['error'] == 'giris_gerekli') {
          echo "<p style='color:red;'>Lütfen önce giriş yapın!</p>";}
       ?>
      <form method="POST">
     <input type= text name="mail" placeholder="E-Mail" required><br>
     <input type= password name="pass" placeholder="Şifre" required><br>
    <button type="submit">Giriş Yap</button>
   </form>
  </body>
 </html>
