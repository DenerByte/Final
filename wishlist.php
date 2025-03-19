
<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['remove'])) {
    $wish_id = (int)$_GET['remove'];
    $deleteSql = "DELETE FROM wishlist WHERE id=? AND user_id=?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("ii", $wish_id, $user_id);
    $stmt->execute();
    header("Location: wishlist.php");
    exit();
}

$sql = "SELECT wishlist.id as wish_id, products.*
        FROM wishlist
        JOIN products ON wishlist.product_id = products.id
        WHERE wishlist.user_id = $user_id";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Minha Wishlist</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="container">
    <h1>Minha Wishlist</h1>
</header>
<div class="container">
    <?php
    if ($result->num_rows > 0) {
        echo "<ul>";
        while($row = $result->fetch_assoc()) {
            echo "<li>";
            echo $row['name']." - R$ ".number_format($row['price'], 2, ',', '.');
            echo " <a href='wishlist.php?remove=".$row['wish_id']."'>Remover</a>";
            echo "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>Sua Wishlist está vazia.</p>";
    }
    ?>
</div>
<script src="js/script.js"></script>
</body>
</html>
