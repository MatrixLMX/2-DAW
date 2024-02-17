<?php


if(isset($_SESSION['intentos']) && $_SESSION['intentos'] >= 3) {
    echo "Has excedido el número de intentos. Reinicia el navegador.";
    exit();
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'app/config/configDB.php'; // Incluye el archivo de configuración de la base de datos

    $login = $_POST['login'];
    $password = $_POST['password'];

    try {
        $dbh = new PDO("mysql:host=".DB_SERVER.";dbname=".DATABASE, DB_USER, DB_PASSWD);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $dbh->prepare("SELECT * FROM User WHERE login = :login");
        $stmt->bindParam(':login', $login);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            $_SESSION['intentos'] = 0;
            header('Location: dashboard.php'); // Redirecciona al dashboard o página principal de la aplicación
            exit();
        } else {
            if(isset($_SESSION['intentos'])) {
                $_SESSION['intentos']++;
            } else {
                $_SESSION['intentos'] = 1;
            }
            echo "Credenciales inválidas. Intentos restantes: " . (3 - $_SESSION['intentos']);
        }
    } catch(PDOException $e) {
        echo "Error de conexión: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
</head>
<body>
    <h2>Iniciar sesión</h2>
    <form method="POST">
        <label for="login">Usuario:</label><br>
        <input type="text" id="login" name="login" required><br>

        <label for="password">Contraseña:</label><br>
        <input type="password" id="password" name="password" required><br>

        <button type="submit" name="entrar">Iniciar sesión</button>
    </form>
</body>
</html>