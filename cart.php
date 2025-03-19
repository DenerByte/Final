
<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['update_cart'])) {
    foreach ($_POST['qty'] as $cart_id => $quantity) {
        $quantity = (int)$quantity;
        if ($quantity > 0) {
            $updateSql = "UPDATE cart SET quantity=? WHERE id=?";
            $stmt = $conn->prepare($updateSql);
            $stmt->bind_param("ii", $quantity, $cart_id);
            $stmt->execute();
        }
    }
}

if (isset($_GET['remove'])) {
    $cart_id = (int)$_GET['remove'];
    $deleteSql = "DELETE FROM cart WHERE id=? AND user_id=?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("ii", $cart_id, $user_id);
    $stmt->execute();
    header("Location: cart.php");
    exit();
}

$sql = "SELECT cart.id as cart_id, products.*, cart.quantity
        FROM cart
        JOIN products ON cart.product_id = products.id
        WHERE cart.user_id = $user_id";
$result = $conn->query($sql);

$frete = 25.00;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Meu Carrinho</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <div class="container">
        <h1>Meu Carrinho</h1>
    </div>
</header>
<div class="container">
    <form method="post">
        <table border="1" cellpadding="8" cellspacing="0">
            <tr>
                <th>Produto</th>
                <th>Preço</th>
                <th>Quantidade</th>
                <th>Subtotal</th>
                <th>Remover</th>
            </tr>
            <?php
            $total = 0;
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $subtotal = $row['price'] * $row['quantity'];
                    $total += $subtotal;
                    ?>
                    <tr>
                        <td><?php echo $row['name']; ?></td>
                        <td>R$ <?php echo number_format($row['price'], 2, ',', '.'); ?></td>
                        <td>
                            <input type="number" name="qty[<?php echo $row['cart_id']; ?>]" value="<?php echo $row['quantity']; ?>" min="1">
                        </td>
                        <td>R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></td>
                        <td><a href="cart.php?remove=<?php echo $row['cart_id']; ?>">X</a></td>
                    </tr>
                    <?php
                }
            } else {
                echo "<tr><td colspan='5'>Carrinho vazio</td></tr>";
            }
            ?>
        </table>
        <button type="submit" name="update_cart">Atualizar Carrinho</button>
    </form>

    <div style="margin-top:20px;">
        <p>Subtotal: R$ <?php echo number_format($total, 2, ',', '.'); ?></p>
        <p>Frete: R$ <?php echo number_format($frete, 2, ',', '.'); ?></p>
        <p><strong>Total: R$ <?php echo number_format($total + $frete, 2, ',', '.'); ?></strong></p>
        <button onclick="alert('Checkout ainda não implementado!')">Finalizar Compra</button>
    </div>
</div>
<script src="js/script.js"></script>
</body>
</html>
