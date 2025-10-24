<?php
session_start();
 $db = new SQLite3('../db/database.db');
 $cities_result = $db->query('SELECT * FROM cities ORDER BY name');
 $trips = [];
  if($_SERVER['REQUEST_METHOD'] == 'POST') {
     $departure = $_POST['departure_city'];
     $arrival = $_POST['arrival_city'];
     $date = $_POST['trip_date'];
     $sql = "SELECT trip.*, buscompany.name as company_name
             FROM trip
             JOIN buscompany ON trip.company_id = buscompany.id
             WHERE trip.departure_city = :departure
             AND trip.arrival_city = :arrival
             AND DATE(trip.departure_time) = :date";
     $stmt = $db->prepare($sql);
     $stmt->bindValue(':departure', $departure, SQLITE3_TEXT);
     $stmt->bindValue(':arrival', $arrival, SQLITE3_TEXT);
     $stmt->bindValue(':date', $date, SQLITE3_TEXT);
     $result = $stmt->execute();
     while ($trip = $result->fetchArray(SQLITE3_ASSOC)) {
     $trips[] = $trip;}}
?>

<!DOCTYPE html>
<html>
<head>
 <title>Bilet Satın Alma</title>
</head>
<body>
 <h1>Otobüs Bileti</h1>
<?php if (isset($_SESSION['user_id'])): ?>
 <p>Hoşgeldin <?php echo $_SESSION['name']; ?> | <a href="my_tickets.php">Biletlerim</a> | <a href="logout.php">Çıkış</a></p>
<?php else: ?>
 <p><a href="login.php">Giriş Yap</a> | <a href="register.php">Kayıt Ol</a></p>
<?php endif; ?>
<h2>Sefer Ara</h2>
 <form method="POST">
  <label>Kalkış:</label>
<select name="departure_city" required>
    <option value="">Şehir Seçin</option>
<?php
  while ($city = $cities_result->fetchArray(SQLITE3_ASSOC)) {
         echo "<option value='{$city['name']}'>{$city['name']}</option>";}
?>
</select>
 <br><br>
  <label>Varış:</label>
<select name="arrival_city" required>
 <option value="">Şehir Seçin</option>
<?php
 $cities_result2 = $db->query('SELECT * FROM cities ORDER BY name');
   while ($city = $cities_result2->fetchArray(SQLITE3_ASSOC)) {
     echo "<option value='{$city['name']}'>{$city['name']}</option>";}
?>
</select>

<br><br>
<label>Tarih:</label>
<input type="date" name="trip_date" required>
<br><br>
<button type="submit">Sefer Ara</button>
</form>

<?php if (count($trips) > 0): ?>
    <h2>Bulunan Seferler</h2>
    <?php foreach ($trips as $trip): ?>
        <div style="border: 1px solid #ccc; padding: 10px; margin: 10px 0;">
            <strong><?php echo $trip['company_name']; ?></strong><br>
            <?php echo $trip['departure_city']; ?> → <?php echo $trip['arrival_city']; ?><br>
            Kalkış: <?php echo $trip['departure_time']; ?><br>
            Fiyat: <?php echo $trip['price']; ?> TL<br>
            Koltuk: <?php echo $trip['seat_qty']; ?><br>
            <a href="buy_ticket.php?trip_id=<?php echo $trip['id']; ?>">Bilet Al</a>
        </div>
    <?php endforeach; ?>
<?php elseif ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
    <p>Sefer bulunamadı.</p>
<?php endif; ?>

</body>
</html>
