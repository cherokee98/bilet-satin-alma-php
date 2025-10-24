<?php
session_start();
 if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit;}

 $db = new SQLite3('../db/database.db');

 if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_firma_admin'])) {
    $name    = $_POST['name'];
    $mail    = $_POST['mail'];
    $pass    = password_hash($_POST['pass'], PASSWORD_DEFAULT);
    $comp_id = $_POST['comp_id'];
    $sql     = "INSERT INTO users (name, mail, pass, role, comp_id) VALUES (:name, :mail, :pass, 'firma_admin', :comp_id)";
    $stmt    = $db->prepare($sql);
    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
    $stmt->bindValue(':mail', $mail, SQLITE3_TEXT);
    $stmt->bindValue(':pass', $pass, SQLITE3_TEXT);
    $stmt->bindValue(':comp_id', $comp_id, SQLITE3_INTEGER);
    $stmt->execute();
    header("Location: manage_admins.php");
    exit;}

 if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $db->exec("DELETE FROM users WHERE id = $id");
    header("Location: manage_admins.php");
    exit;}

 $firma_admins = $db->query("SELECT users.*, buscompany.name as company_name FROM users LEFT JOIN buscompany ON users.comp_id = buscompany.id WHERE users.role = 'firma_admin' ORDER BY users.name");

 $companies = $db->query('SELECT * FROM buscompany ORDER BY name');

?>

<!DOCTYPE html>
<html>
 <head><title>Firma Admin Yönetimi</title></head>
  <body>
   <h1>Firma Admin Yönetimi</h1>
    <p><a href="admin.php">Admin Panel</a> | <a href="logout.php">Çıkış</a></p>
    <h2>Firma Adminler</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>İsim</th>
            <th>Email</th>
            <th>Firma</th>
            <th>İşlem</th>
        </tr>
        <?php while ($admin = $firma_admins->fetchArray(SQLITE3_ASSOC)): ?>
        <tr>
            <td><?php echo $admin['id']; ?></td>
            <td><?php echo $admin['name']; ?></td>
            <td><?php echo $admin['mail']; ?></td>
            <td><?php echo $admin['company_name']; ?></td>
            <td><a href="?delete=<?php echo $admin['id']; ?>" onclick="return confirm('Silmek istediğinize emin misiniz?')">Sil</a></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <h2>Yeni Firma Admin Ekle</h2>
    <form method="POST">
        <input type="text" name="name" placeholder="İsim" required><br>
        <input type="email" name="mail" placeholder="Email" required><br>
        <input type="password" name="pass" placeholder="Şifre" required><br>
        <label>Firma:</label>
        <select name="comp_id" required>
            <?php while ($company = $companies->fetchArray(SQLITE3_ASSOC)): ?>
                <option value="<?php echo $company['id']; ?>"><?php echo $company['name']; ?></option>
            <?php endwhile; ?>
        </select><br><br>
        <button type="submit" name="add_firma_admin">Ekle</button>
    </form>
</body>
</html>
