<?php
session_start();

header('Content-Type: text/html; charset=UTF-8');

$db_host = 'localhost';
$db_name = 'u68895';
$db_user = 'u68895';
$db_pass = '1562324';

// Выход из системы
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header('Location: index.php');
    exit();
}

// Если уже авторизован - редирект
if (!empty($_SESSION['login'])) {
    header('Location: index.php');
    exit();
}

// Обработка входа
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';
    
    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $pdo->prepare("SELECT id, pass_hash FROM users WHERE login = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['pass_hash'])) {
            $_SESSION['login'] = $login;
            $_SESSION['uid'] = $user['id'];
            header('Location: index.php');
            exit();
        } else {
            $error = "Неверный логин или пароль";
        }
    } catch (PDOException $e) {
        $error = "Ошибка базы данных";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
    <style>
        /* Стили аналогичные form.php */
    </style>
</head>
<body>
    <h1>Вход</h1>
    
    <?php if (!empty($error)): ?>
        <div class="error-message"><?= $error ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="form-group">
            <label for="login">Логин</label>
            <input type="text" id="login" name="login" required>
        </div>
        
        <div class="form-group">
            <label for="password">Пароль</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <button type="submit">Войти</button>
    </form>
    
    <p>Ещё нет аккаунта? <a href="index.php">Заполните анкету</a></p>
</body>
</html>
