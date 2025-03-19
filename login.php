<?php
session_start();
include 'db_connect.php';

if (isset($_POST['login'])) {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT id, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id, $hashed_password);
    $stmt->fetch();

    if ($stmt->num_rows > 0) {
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            header("Location: index.php");
            exit();
        } else {
            $error = "Senha incorreta!";
        }
    } else {
        $error = "Usuário não encontrado!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Login - Meu E-commerce</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <div class="container">
        <h1>Login</h1>
    </div>
</header>

<div class="container">
    <?php if(isset($_GET['success'])) echo "<p style='color:green;'>Cadastro realizado com sucesso! Faça login.</p>"; ?>
    <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    
    <form action="" method="POST">
        <label for="email">E-mail:</label><br>
        <input type="email" name="email" required><br><br>

        <label for="password">Senha:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit" name="login">Entrar</button>
    </form>
    <p>Não tem conta? <a href="register.php">Cadastre-se</a></p>
</div>

<script src="js/script.js"></script>
</body>
</html>
