
<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];


if (isset($_GET['remove'])) {
    $comp_id = (int)$_GET['remove'];
    $deleteSql = "DELETE FROM compare WHERE id=? AND user_id=?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("ii", $comp_id, $user_id);
    $stmt->execute();
    header("Location: compare.php");
    exit();
}


$sql = "SELECT compare.id as comp_id, products.*
        FROM compare
        JOIN products ON compare.product_id = products.id
        WHERE compare.user_id = $user_id";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Comparar Produtos</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="container">
    <h1>Comparação de Produtos</h1>
</header>
<div class="container">
    <?php
    if ($result->num_rows > 0) {
        echo "<table border='1' cellpadding='8' cellspacing='0'>";
        echo "<tr>";
        while($row = $result->fetch_assoc()) {
            echo "<td>";
            echo "<img src='".$row['image']."' alt='".$row['name']."' style='max-width:150px;'><br>";
            echo "<strong>".$row['name']."</strong><br>";
            echo "R$ ".number_format($row['price'], 2, ',', '.')."<br>";
            echo "<p>".$row['description']."</p>";
            echo "<a href='compare.php?remove=".$row['comp_id']."'>Remover</a>";
            echo "</td>";
        }
        echo "</tr>";
        echo "</table>";
    } else {
        echo "<p>Nenhum produto selecionado para comparação.</p>";
    }
    ?>
</div>
<script src="js/script.js"></script>
</body>
</html>
