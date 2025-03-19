<?php
session_start();
include 'db_connect.php';

$cat_id = isset($_GET['cat_id']) ? (int)$_GET['cat_id'] : 0;

$sql = "SELECT p.*, c.category_name 
        FROM products p
        JOIN categories c ON p.category_id = c.id";

if ($cat_id > 0) {
    $sql .= " WHERE p.category_id = $cat_id";
}

$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Lista de Produtos</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <div class="container">
        <h1>Produtos</h1>
    </div>
</header>
<div class="container">
    <div>
        <h2>Filtrando por: <?php 
            if($cat_id > 0) {
                $catSql = "SELECT category_name FROM categories WHERE id = $cat_id";
                $catRes = $conn->query($catSql);
                $catRow = $catRes->fetch_assoc();
                echo $catRow['category_name'];
            } else {
                echo "Todos os produtos";
            }
        ?></h2>
    </div>
    <div style="display:flex; flex-wrap:wrap; gap:20px;">
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                ?>
                <div style="border:1px solid #ccc; padding:10px; width:200px;">
                    <img src="<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>" style="width:100%;">
                    <h3><?php echo $row['name']; ?></h3>
                    <p>R$ <?php echo number_format($row['price'], 2, ',', '.'); ?></p>
                    <a href="product_detail.php?id=<?php echo $row['id']; ?>">Ver Detalhes</a>
                </div>
                <?php
            }
        } else {
            echo "<p>Nenhum produto encontrado.</p>";
        }
        ?>
    </div>
</div>

<script src="js/script.js"></script>
</body>
</html>
