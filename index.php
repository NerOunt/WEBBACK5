<?php
session_start();
ob_start();

header('Content-Type: text/html; charset=UTF-8');

// Конфигурация базы данных
$db_host = 'localhost';
$db_name = 'u68895';
$db_user = 'u68895';
$db_pass = '1562324';

// Инициализация переменных
$messages = [];
$errors = [
    'full_name' => '',
    'phone' => '',
    'email' => '',
    'birth_date' => '',
    'gender' => '',
    'languages' => '',
    'contract_agreed' => ''
];

// Обработка GET запроса
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!empty($_SESSION['form_data'])) {
        $values = $_SESSION['form_data'];
    } else {
        $values = [
            'full_name' => $_COOKIE['full_name_value'] ?? '',
            'phone' => $_COOKIE['phone_value'] ?? '',
            'email' => $_COOKIE['email_value'] ?? '',
            'birth_date' => $_COOKIE['birth_date_value'] ?? '',
            'gender' => $_COOKIE['gender_value'] ?? '',
            'biography' => $_COOKIE['biography_value'] ?? '',
            'contract_agreed' => isset($_COOKIE['contract_agreed_value']) ? (bool)$_COOKIE['contract_agreed_value'] : false,
            'languages' => isset($_COOKIE['languages_value']) ? explode(',', $_COOKIE['languages_value']) : []
        ];
    }
    
    include('form.php');
    exit();
}

// Обработка POST запроса
$values = $_POST;
$_SESSION['form_data'] = $values;

// Валидация данных
$validation_failed = false;

if (empty($values['full_name']) || !preg_match('/^[а-яА-ЯёЁa-zA-Z\s\-]{2,150}$/u', $values['full_name'])) {
    $errors['full_name'] = 'Введите корректное ФИО';
    $validation_failed = true;
}

if (empty($values['phone']) || !preg_match('/^\+?\d{10,15}$/', $values['phone'])) {
    $errors['phone'] = 'Введите корректный телефон';
    $validation_failed = true;
}

if (empty($values['email']) || !filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Введите корректный email';
    $validation_failed = true;
}

$today = new DateTime();
$birthdate = DateTime::createFromFormat('Y-m-d', $values['birth_date']);
if (empty($values['birth_date']) || !$birthdate || $birthdate > $today) {
    $errors['birth_date'] = 'Введите корректную дату рождения';
    $validation_failed = true;
}

if (empty($values['gender']) || !in_array($values['gender'], ['male', 'female', 'other'])) {
    $errors['gender'] = 'Выберите пол';
    $validation_failed = true;
}

if (empty($values['languages'])) {
    $errors['languages'] = 'Выберите хотя бы один язык';
    $validation_failed = true;
}

if (empty($values['contract_agreed'])) {
    $errors['contract_agreed'] = 'Необходимо согласие с контрактом';
    $validation_failed = true;
}

if ($validation_failed) {
    $_SESSION['errors'] = $errors;
    header('Location: index.php');
    exit();
}

// Подключение к БД с обработкой ошибок
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    
    // Установка режима ошибок (совместимый способ)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, constant('PDO::ERRMODE_EXCEPTION'));

    $stmt = $pdo->prepare("INSERT INTO applications (full_name, phone, email, birth_date, gender, biography, contract_agreed) 
                          VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $values['full_name'],
        $values['phone'],
        $values['email'],
        $values['birth_date'],
        $values['gender'],
        $values['biography'],
        isset($values['contract_agreed']) ? 1 : 0
    ]);
    
    $app_id = $pdo->lastInsertId();
    
    $stmt = $pdo->prepare("INSERT INTO application_languages (application_id, language_id) VALUES (?, ?)");
    foreach ($values['languages'] as $lang_id) {
        $stmt->execute([$app_id, (int)$lang_id]);
    }
    
    $_SESSION['generated_credentials'] = [
        'login' => 'user_' . substr(md5(time()), 0, 8),
        'password' => substr(md5(uniqid()), 0, 8)
    ];
    
    // Очистка данных
    foreach ($values as $key => $value) {
        setcookie($key.'_value', '', time() - 3600, '/');
    }
    setcookie('languages_value', '', time() - 3600, '/');
    setcookie('contract_agreed_value', '', time() - 3600, '/');
    
    unset($_SESSION['form_data']);
    unset($_SESSION['errors']);
    
    header("Location: index.php");
    exit();
    
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Ошибка базы данных. Пожалуйста, попробуйте позже.";
    error_log("DB Error: " . $e->getMessage());
    header("Location: index.php");
    exit();
}
?>
