<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=giris_gerekli");
    exit;
}

$db = new SQLite3('../db/database.db');
$trip_id = $_GET['trip_id'];

// Sefer bilgilerini al
$trip = $db->querySingle("SELECT trip.*, buscompany.name as company_name 
                          FROM trip 
                          JOIN buscompany ON trip.company_id = buscompany.id 
                          WHERE trip.id = $trip_id", true);

// Dolu koltukları al
$booked_result = $db->query("SELECT seat_number FROM booked_seats WHERE trip_id = $trip_id");
$booked_seats = [];
while ($row = $booked_result->fetchArray(SQLITE3_ASSOC)) {
    $booked_seats[] = $row['seat_number'];
}

// Bilet satın alma
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $seat = $_POST['seat_number'];
    $user_id = $_SESSION['user_id'];
    
    // Kullanıcının bakiyesini kontrol et
    $user = $db->querySingle("SELECT balance FROM users WHERE id = $user_id", true);
    
    if ($user['balance'] < $trip['price']) {
        $error = "Yetersiz bakiye!";
    } elseif (in_array($seat, $booked_seats)) {
        $error = "Bu koltuk dolu!";
    } else {
        // Bilet oluştur
        $sql = "INSERT INTO ticket (trip_id, user_id, seat_number, total_price) 
                VALUES ($trip_id, $user_id, $seat, {$trip['price']})";
        $db->exec($sql);
        $ticket_id = $db->lastInsertRowID();
        
        // Koltuğu rezerve et
        $db->exec("INSERT INTO booked_seats (trip_id, seat_number, ticket_id) 
                   VALUES ($trip_id, $seat, $ticket_id)");
        
        // Bakiyeden düş
        $new_balance = $user['balance'] - $trip['price'];
        $db->exec("UPDATE users SET balance = $new_balance WHERE id = $user_id");
        
        header("Location: my_tickets.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Bilet Al</title></head>
<body>
    <h1>Bilet Al</h1>
    <p><a href="index.php">Ana Sayfa</a> | <a href="logout.php">Çıkış</a></p>
    <h2><?php echo $trip['company_name']; ?></h2>
    <p><?php echo $trip['departure_city']; ?> → <?php echo $trip['arrival_city']; ?></p>
    <p>Kalkış: <?php echo $trip['departure_time']; ?></p>
    <p>Fiyat: <?php echo $trip['price']; ?> TL</p>
    <p>Bakiyeniz: <?php echo $db->querySingle("SELECT balance FROM users WHERE id = {$_SESSION['user_id']}"); ?> TL</p>
    <?php if (isset($error)): ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <h2>Koltuk Seçin</h2>
    <form method="POST">
        <?php for ($i = 1; $i <= $trip['seat_qty']; $i++): ?>
            <button type="submit" name="seat_number" value="<?php echo $i; ?>" 
                    <?php echo in_array($i, $booked_seats) ? 'disabled' : ''; ?>>
                <?php echo $i; ?>
            </button>
        <?php endfor; ?>
    </form>
</body>
</html>
