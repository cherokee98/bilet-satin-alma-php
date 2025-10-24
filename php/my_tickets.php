<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$db = new SQLite3('../db/database.db');
$user_id = $_SESSION['user_id'];

// Bilet iptal
if (isset($_GET['cancel'])) {
    $ticket_id = $_GET['cancel'];
    $ticket = $db->querySingle("SELECT * FROM ticket WHERE id = $ticket_id AND user_id = $user_id", true);
    
    if ($ticket) {
        $trip = $db->querySingle("SELECT departure_time FROM trip WHERE id = {$ticket['trip_id']}", true);
        $time_diff = strtotime($trip['departure_time']) - time();
        
        if ($time_diff > 3600) { // 1 saatten fazla
            $db->exec("UPDATE ticket SET status = 'CANCELLED' WHERE id = $ticket_id");
            $db->exec("DELETE FROM booked_seats WHERE ticket_id = $ticket_id");
            $db->exec("UPDATE users SET balance = balance + {$ticket['total_price']} WHERE id = $user_id");
            $success = "Bilet iptal edildi, {$ticket['total_price']} TL iade edildi.";
        } else {
            $error = "Kalkışa 1 saatten az kaldı, iptal edilemez!";
        }
    }
}

$tickets = $db->query("SELECT ticket.*, trip.departure_city, trip.arrival_city, trip.departure_time, buscompany.name as company 
                       FROM ticket 
                       JOIN trip ON ticket.trip_id = trip.id 
                       JOIN buscompany ON trip.company_id = buscompany.id 
                       WHERE ticket.user_id = $user_id 
                       ORDER BY ticket.created_at DESC");
?>
<!DOCTYPE html>
<html>
<head><title>Biletlerim</title></head>
<body>
    <h1>Biletlerim</h1>
    <p>Hoşgeldin <?php echo $_SESSION['name']; ?> | <a href="index.php">Ana Sayfa</a> | <a href="logout.php">Çıkış</a></p>
    
    <?php if (isset($success)): ?>
        <p style="color:green;"><?php echo $success; ?></p>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>
    
    <table border="1">
        <tr>
            <th>Firma</th>
            <th>Güzergah</th>
            <th>Kalkış</th>
            <th>Koltuk</th>
            <th>Fiyat</th>
            <th>Durum</th>
            <th>İşlem</th>
        </tr>
        <?php while ($ticket = $tickets->fetchArray(SQLITE3_ASSOC)): ?>
        <tr>
            <td><?php echo $ticket['company']; ?></td>
            <td><?php echo $ticket['departure_city']; ?> → <?php echo $ticket['arrival_city']; ?></td>
            <td><?php echo $ticket['departure_time']; ?></td>
            <td><?php echo $ticket['seat_number']; ?></td>
            <td><?php echo $ticket['total_price']; ?> TL</td>
            <td><?php echo $ticket['status']; ?></td>
            <td>
                <?php if ($ticket['status'] == 'ACTIVE'): ?>
                    <a href="?cancel=<?php echo $ticket['id']; ?>" onclick="return confirm('İptal etmek istediğinize emin misiniz?')">İptal</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
