<?php
session_start();
include 'db_connect.php';

if (isset($_POST['register'])) {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $checkSql = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if($stmt->num_rows > 0) {
        $error = "Este e-mail já está registrado!";
    } else {
        $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $email, $password);

        if ($stmt->execute()) {
            header("Location: login.php?success=1");
            exit();
        } else {
            $error = "Erro ao cadastrar. Tente novamente!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Cadastro - Meu E-commerce</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <div class="container">
        <h1>Cadastro</h1>
    </div>
</header>

<div class="container">
    <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form action="" method="POST">
        <label for="name">Nome:</label><br>
        <input type="text" name="name" id="name" required><br><br>

        <label for="email">E-mail:</label><br>
        <input type="email" name="email" id="email" required><br><br>

        <label for="password">Senha:</label><br>
        <input type="password" name="password" id="password" required><br><br>

        <button type="submit" name="register">Cadastrar</button>
    </form>
    <p>Já tem conta? <a href="login.php">Faça login</a></p>
</div>

<script src="js/script.js"></script>
</body>
</html>
