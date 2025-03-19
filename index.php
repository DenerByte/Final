<?php
session_start();
include 'db_connect.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Meu E-commerce</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
    <div class="container">
        <div class="logo">Meu E-commerce</div>
        <nav>
            <ul>
                <li><a href="index.php">Início</a></li>
                <li><a href="product_list.php">Produtos</a></li>
                <li><a href="cart.php">Carrinho</a></li>
                <li><a href="wishlist.php">Wishlist</a></li>
                <li><a href="compare.php">Comparar</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="logout.php">Sair</a></li>
                <?php else: ?>
                    <li><a href="login.php">Entrar</a></li>
                    <li><a href="register.php">Cadastrar</a></li>
                <?php endif; ?>
                <li>
                    <button onclick="toggleDarkMode()">Modo Escuro/Claro</button>
                </li>
            </ul>
        </nav>
        <div>
            <input type="text" placeholder="Busque produtos..." onkeyup="searchProducts(this.value)">
            <div id="autocomplete-results" style="background:#fff; position:absolute; z-index:999;"></div>
        </div>
    </div>
</header>

<div class="container">
    <h1>Bem-vindo(a) ao Meu E-commerce!</h1>
    <p>Veja nossas categorias:</p>
    <?php
    
    $sqlCat = "SELECT * FROM categories WHERE parent_id IS NULL";
    $resCat = $conn->query($sqlCat);

    if ($resCat->num_rows > 0) {
        echo "<ul>";
        while($rowCat = $resCat->fetch_assoc()) {
            echo "<li>" . $rowCat['category_name'];
            

            $parentId = $rowCat['id'];
            $sqlSubCat = "SELECT * FROM categories WHERE parent_id = $parentId";
            $resSubCat = $conn->query($sqlSubCat);
            if ($resSubCat->num_rows > 0) {
                echo "<ul>";
                while($rowSubCat = $resSubCat->fetch_assoc()) {
                    echo "<li><a href='product_list.php?cat_id=".$rowSubCat['id']."'>".$rowSubCat['category_name']."</a></li>";
                }
                echo "</ul>";
            }

            echo "</li>";
        }
        echo "</ul>";
    }
    ?>
</div>

<script src="js/script.js"></script>
</body>
</html>
