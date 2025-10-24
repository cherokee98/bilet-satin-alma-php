<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'firma_admin') {
    header("Location: index.php");
    exit;
}

$db = new SQLite3('../db/database.db');
$comp_id = $_SESSION['comp_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_trip'])) {
    $dep_city = $_POST['departure_city'];
    $arr_city = $_POST['arrival_city'];
    $dep_time = $_POST['departure_time'];
    $arr_time = $_POST['arrival_time'];
    $seats = $_POST['seat_qty'];
    $price = $_POST['price'];
    $sql = "INSERT INTO trip (company_id, departure_city, arrival_city, departure_time, arrival_time, seat_qty, price) 
            VALUES (:comp_id, :dep, :arr, :dep_time, :arr_time, :seats, :price)";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':comp_id', $comp_id, SQLITE3_INTEGER);
    $stmt->bindValue(':dep', $dep_city, SQLITE3_TEXT);
    $stmt->bindValue(':arr', $arr_city, SQLITE3_TEXT);
    $stmt->bindValue(':dep_time', $dep_time, SQLITE3_TEXT);
    $stmt->bindValue(':arr_time', $arr_time, SQLITE3_TEXT);
    $stmt->bindValue(':seats', $seats, SQLITE3_INTEGER);
    $stmt->bindValue(':price', $price, SQLITE3_REAL);
    $stmt->execute();
    header("Location: firma_panel.php");
    exit;
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $db->exec("DELETE FROM trip WHERE id = $id AND company_id = $comp_id");
    header("Location: firma_panel.php");
    exit;
}

$trips = $db->query("SELECT * FROM trip WHERE company_id = $comp_id ORDER BY departure_time");
$cities = $db->query('SELECT * FROM cities ORDER BY name');
?>

<!DOCTYPE html>
<html>
<head><title>Firma Paneli</title></head>
<body>
    <h1>Firma Paneli</h1>
    <p>Hoşgeldin <?php echo $_SESSION['name']; ?> | <a href="index.php">Ana Sayfa</a> | <a href="logout.php">Çıkış</a></p>

    <h2>Seferlerim</h2>
    <table border="1">
        <tr>
            <th>Kalkış</th>
            <th>Varış</th>
            <th>Kalkış Saati</th>
            <th>Varış Saati</th>
            <th>Koltuk</th>
            <th>Fiyat</th>
            <th>İşlem</th>
        </tr>
        <?php while ($trip = $trips->fetchArray(SQLITE3_ASSOC)): ?>
        <tr>
            <td><?php echo $trip['departure_city']; ?></td>
            <td><?php echo $trip['arrival_city']; ?></td>
            <td><?php echo $trip['departure_time']; ?></td>
            <td><?php echo $trip['arrival_time']; ?></td>
            <td><?php echo $trip['seat_qty']; ?></td>
            <td><?php echo $trip['price']; ?> TL</td>
            <td><a href="?delete=<?php echo $trip['id']; ?>">Sil</a></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h2>Yeni Sefer Ekle</h2>
    <form method="POST">
        <select name="departure_city" required>
            <?php 
            $cities_dep = $db->query('SELECT * FROM cities ORDER BY name');
            while ($city = $cities_dep->fetchArray(SQLITE3_ASSOC)): ?>
                <option value="<?php echo $city['name']; ?>"><?php echo $city['name']; ?></option>
            <?php endwhile; ?>
        </select>
        →
        <select name="arrival_city" required>
            <?php
            $cities_arr = $db->query('SELECT * FROM cities ORDER BY name');
            while ($city = $cities_arr->fetchArray(SQLITE3_ASSOC)): ?>
                <option value="<?php echo $city['name']; ?>"><?php echo $city['name']; ?></option>
            <?php endwhile; ?>
        </select><br><br>

        Kalkış: <input type="datetime-local" name="departure_time" required><br>
        Varış: <input type="datetime-local" name="arrival_time" required><br>
        Koltuk Sayısı: <input type="number" name="seat_qty" required><br>
        Fiyat: <input type="number" step="0.01" name="price" required><br><br>
        <button type="submit" name="add_trip">Sefer Ekle</button>
    </form>
</body>
</html>
