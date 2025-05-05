<?php
session_start();
ob_start();

header('Content-Type: text/html; charset=UTF-8');

$db_host = 'localhost';
$db_name = 'u68895';
$db_user = 'u68895';
$db_pass = '1562324';

// Инициализация переменных
$messages = [];
$values = [
    'full_name' => '',
    'phone' => '',
    'email' => '',
    'birth_date' => '',
    'gender' => '',
    'biography' => '',
    'contract_agreed' => false,
    'languages' => []
];

$errors = array_fill_keys(['full_name', 'phone', 'email', 'birth_date', 'gender', 'languages', 'contract_agreed'], false);

// Обработка GET запроса
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Загрузка данных из кук
    foreach ($values as $key => &$value) {
        if (isset($_COOKIE[$key.'_value'])) {
            $value = $_COOKIE[$key.'_value'];
        }
    }
    
    if (isset($_COOKIE['languages_value'])) {
        $values['languages'] = explode(',', $_COOKIE['languages_value']);
    }
    
    if (isset($_COOKIE['contract_agreed_value'])) {
        $values['contract_agreed'] = (bool)$_COOKIE['contract_agreed_value'];
    }
    
    include('form.php');
    exit();
}

// Обработка POST запроса
// Валидация данных
$validation_failed = false;

if (empty($_POST['full_name']) || !preg_match('/^[а-яА-ЯёЁa-zA-Z\s\-]{2,150}$/u', $_POST['full_name'])) {
    $errors['full_name'] = true;
    $validation_failed = true;
}

if (empty($_POST['phone']) || !preg_match('/^\+?\d{10,15}$/', $_POST['phone'])) {
    $errors['phone'] = true;
    $validation_failed = true;
}

if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = true;
    $validation_failed = true;
}

$today = new DateTime();
$birthdate = DateTime::createFromFormat('Y-m-d', $_POST['birth_date']);
if (empty($_POST['birth_date']) || !$birthdate || $birthdate > $today) {
    $errors['birth_date'] = true;
    $validation_failed = true;
}

if (empty($_POST['gender']) || !in_array($_POST['gender'], ['male', 'female', 'other'])) {
    $errors['gender'] = true;
    $validation_failed = true;
}

if (empty($_POST['languages'])) {
    $errors['languages'] = true;
    $validation_failed = true;
}

if (empty($_POST['contract_agreed'])) {
    $errors['contract_agreed'] = true;
    $validation_failed = true;
}

if ($validation_failed) {
    // Сохраняем введенные значения в куки
    foreach ($values as $key => $value) {
        setcookie($key.'_value', $_POST[$key] ?? '', time() + 3600, '/');
    }
    setcookie('languages_value', implode(',', $_POST['languages'] ?? []), time() + 3600, '/');
    setcookie('contract_agreed_value', isset($_POST['contract_agreed']) ? '1' : '', time() + 3600, '/');
    
    header('Location: index.php');
    exit();
}

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Сохраняем анкету
    $stmt = $pdo->prepare("INSERT INTO applications (full_name, phone, email, birth_date, gender, biography, contract_agreed) 
                          VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['full_name'],
        $_POST['phone'],
        $_POST['email'],
        $_POST['birth_date'],
        $_POST['gender'],
        $_POST['biography'],
        isset($_POST['contract_agreed']) ? 1 : 0
    ]);
    
    $app_id = $pdo->lastInsertId();
    
    // Сохраняем языки программирования
    $stmt = $pdo->prepare("INSERT INTO application_languages (application_id, language_id) VALUES (?, ?)");
    foreach ($_POST['languages'] as $lang_id) {
        $stmt->execute([$app_id, $lang_id]);
    }
    
    // Генерируем демо-учетные данные (без таблицы users)
    $_SESSION['generated_credentials'] = [
        'login' => 'user_' . substr(md5(time()), 0, 8),
        'password' => substr(md5(uniqid()), 0, 8)
    ];
    
    // Очищаем куки
    foreach ($values as $key => $value) {
        setcookie($key.'_value', '', time() - 3600, '/');
    }
    setcookie('languages_value', '', time() - 3600, '/');
    setcookie('contract_agreed_value', '', time() - 3600, '/');
    
    header("Location: index.php");
    exit();
    
} catch (PDOException $e) {
    die("Ошибка базы данных: " . $e->getMessage());
}
?>
