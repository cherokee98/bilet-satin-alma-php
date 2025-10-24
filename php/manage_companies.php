<?php
session_start();

 if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit;}

 $db = new SQLite3('../db/database.db');

 if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['add_company'])){
    $name = $_POST['company_name'];
    $sql  = "INSERT INTO buscompany (name) VALUES (:name)";
    $stmt = $db->prepare($sql);
    $stmt ->bindvalue(':name', $name, SQLITE3_TEXT);
    $stmt ->execute();
    header("Location: manage_companies.php");
    exit;}

 if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $db ->exec("DELETE FROM buscompany WHERE id=$id");
    header("Location: manage_companies.php");
    exit;}

 $companies = $db->query('SELECT * FROM buscompany ORDER BY name');
?>

<!DOCTYPE html>
 <html>
  <head><title>Firma Yönetimi</title></head>
   <body>
    <h1>Firma Yönetimi</h1>
     <p><a href="admin.php">Admin Panel</a> | <a href="logout.php">Çıkış</a></p>
      <h2>Firmalar</h2>
       <table border="1">
        <tr>
         <th>ID</th>
          <th>Firma Adı</th>
           <th>İşlem</th>
            </tr>
             <?php while($company = $companies->fetchArray(SQLITE3_ASSOC)):?>
              <tr>
             <td><?php echo $company['id'];?></td>
            <td><?php echo $company['name'];?></td>
          <td><a href="?delete=<?php echo $company['id'];?>"onclick="return confirm('Silmek istediğinize emin misiniz?')">Sil</a></td>
         </tr>
        <?php endwhile;?>
       </table>
      <h2>Yeni Firma Ekle</h2>
     <form method="POST">
    <input type="text" name="company_name" placeholder="Firma Adı" required>
   <button type="submit" name="add_company">Ekle</button>
  </form>
 </body>
</html>
