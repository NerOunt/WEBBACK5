<?php
header('Content-Type: text/html; charset=UTF-8');

session_start();

$db_host = 'localhost';
$db_name = 'u68895';
$db_user = 'u68895';
$db_pass = '1562324';

$messages = array();


if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header('Location: index.php');
    exit();
}


if (!empty($_SESSION['login'])) {
    header('Location: index.php');
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login'];
    $pass = $_POST['pass'];
    
    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
      
        $stmt = $pdo->prepare("SELECT id, pass_hash FROM users WHERE login = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($pass, $user['pass_hash'])) {
           
            $_SESSION['login'] = $login;
            $_SESSION['uid'] = $user['id'];
            
            header('Location: index.php');
            exit();
        } else {
           
            $messages[] = '<div class="error">Неверный логин или пароль</div>';
        }
    } catch (PDOException $e) {
        $messages[] = '<div class="error">Ошибка базы данных</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход</title>
    <style>
         body {
            font-family: Arial, sans-serif;
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .error {
            color: red;
            margin-bottom: 15px;
            padding: 10px;
            background: #fff0f0;
            border: 1px solid red;
            border-radius: 4px;
        }
        button {
            padding: 8px 15px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background: #45a049;
        }
        
    </style>
</head>
<body>
    <h1>Вход</h1>
    
    <?php if (!empty($messages)): ?>
        <?php foreach ($messages as $message): ?>
            <?= $message ?>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <form method="POST" action="login.php">
        <div class="form-group">
            <label for="login">Логин</label>
            <input type="text" id="login" name="login" required>
        </div>
        
        <div class="form-group">
            <label for="pass">Пароль</label>
            <input type="password" id="pass" name="pass" required>
        </div>
        
        <div class="form-group">
            <button type="submit">Войти</button>
        </div>
    </form>
    
    <p>Ещё нет аккаунта? <a href="index.php">Заполните форму</a> чтобы создать его.</p>
</body>
</html>
