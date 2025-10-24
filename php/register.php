<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name  = $_POST['name'];
    $mail = $_POST['mail'];
    $pass  = password_hash($_POST['pass'], PASSWORD_DEFAULT);

    $db    = new SQLite3 ('../db/database.db');

    $sql   = "INSERT INTO users(name, mail, pass) VALUES (:name, :mail, :pass)";
    $stmt  = $db->prepare ($sql);
    $stmt -> bindValue (':name', $name, SQLITE3_TEXT);
    $stmt -> bindValue (':mail', $mail, SQLITE3_TEXT);
    $stmt -> bindValue (':pass', $pass, SQLITE3_TEXT);

    if ($stmt -> execute ()){
       echo "Kayıt Başarılı !";}
    else{
       echo "Hata!: " . $db->lastErrorMsg();}
}
?>

<!DOCTYPE html>
<html>
<head>
       <title>Kayıt Ol</title>
</head>
 <body>
  <h2>Kayıt Ol</h2>
   <form method="POST">
    <input type=text name="name" placeholder="İsim" required><br>
    <input type=text name="mail" placeholder="E-Mail" required><br>
    <input type=text name="pass" placeholder="Şifre" required><br>
   <button type="submit">Kayıt Ol</button>
  </form>
 </body>
</html>
