<?php
session_start();
include 'db_connect.php';

if (!isset($_GET['id'])) {
    die("Produto não encontrado!");
}

$product_id = (int)$_GET['id'];

$sql = "SELECT p.*, c.category_name 
        FROM products p
        JOIN categories c ON p.category_id = c.id
        WHERE p.id = $product_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("Produto não encontrado!");
}

$product = $result->fetch_assoc();

if (isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
    $user_id = $_SESSION['user_id'];
    $checkSql = "SELECT id, quantity FROM cart WHERE user_id=? AND product_id=?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($cart_id, $cart_quantity);
    $stmt->fetch();

    if ($stmt->num_rows > 0) {
        $newQuantity = $cart_quantity + 1;
        $updateSql = "UPDATE cart SET quantity=? WHERE id=?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("ii", $newQuantity, $cart_id);
        $updateStmt->execute();
    } else {
        $insertSql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->bind_param("ii", $user_id, $product_id);
        $insertStmt->execute();
    }

    header("Location: cart.php");
    exit();
}

if (isset($_POST['add_to_wishlist'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
    $user_id = $_SESSION['user_id'];

    $checkSql = "SELECT id FROM wishlist WHERE user_id=? AND product_id=?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        $insertSql = "INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->bind_param("ii", $user_id, $product_id);
        $insertStmt->execute();
    }
    header("Location: wishlist.php");
    exit();
}

if (isset($_POST['add_to_compare'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
    $user_id = $_SESSION['user_id'];

    $checkSql = "SELECT id FROM compare WHERE user_id=? AND product_id=?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        $insertSql = "INSERT INTO compare (user_id, product_id) VALUES (?, ?)";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->bind_param("ii", $user_id, $product_id);
        $insertStmt->execute();
    }
    header("Location: compare.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Detalhes do Produto</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="container">
    <h1><?php echo $product['name']; ?></h1>
</header>

<div class="container">
    <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" style="max-width:300px;">
    <p><strong>Categoria:</strong> <?php echo $product['category_name']; ?></p>
    <p><strong>Preço:</strong> R$ <?php echo number_format($product['price'], 2, ',', '.'); ?></p>
    <p><strong>Descrição:</strong> <?php echo $product['description']; ?></p>

    <form method="post">
        <button type="submit" name="add_to_cart">Adicionar ao Carrinho</button>
        <button type="submit" name="add_to_wishlist">Adicionar à Wishlist</button>
        <button type="submit" name="add_to_compare">Comparar</button>
    </form>

    <hr>
    <h3>Avaliações</h3>
    <?php
    $reviewSql = "SELECT r.*, u.name FROM reviews r
                  JOIN users u ON r.user_id = u.id
                  WHERE r.product_id = $product_id";
    $reviewRes = $conn->query($reviewSql);

    if ($reviewRes->num_rows > 0) {
        while($rev = $reviewRes->fetch_assoc()) {
            echo "<p><strong>" . $rev['name'] . ":</strong> ";
            echo "Nota: " . $rev['rating'] . "/5<br>";
            echo $rev['comment'] . "</p><hr>";
        }
    } else {
        echo "<p>Nenhuma avaliação ainda.</p>";
    }

    if (isset($_SESSION['user_id'])) {
        ?>
        <h4>Deixe sua avaliação:</h4>
        <form action="product_detail.php?id=<?php echo $product_id; ?>" method="post">
            <label for="rating">Nota (1 a 5):</label>
            <input type="number" name="rating" min="1" max="5" required><br><br>
            <label for="comment">Comentário:</label><br>
            <textarea name="comment" required></textarea><br><br>
            <button type="submit" name="submit_review">Enviar Avaliação</button>
        </form>
        <?php
    } else {
        echo "<p>Faça <a href='login.php'>login</a> para avaliar.</p>";
    }

    if (isset($_POST['submit_review'])) {
        $user_id = $_SESSION['user_id'];
        $rating  = $_POST['rating'];
        $comment = $_POST['comment'];

        $insertReview = "INSERT INTO reviews (user_id, product_id, rating, comment) 
                         VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insertReview);
        $stmt->bind_param("iiis", $user_id, $product_id, $rating, $comment);
        $stmt->execute();

        header("Location: product_detail.php?id=$product_id");
        exit();
    }
    ?>

</div>

<script src="js/script.js"></script>
</body>
</html>
