<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');

$db_host = 'localhost';
$db_name = 'u68895';
$db_user = 'u68895';
$db_pass = '1562324';

// Инициализация переменных
$errors = [];
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

// Если пользователь авторизован - загружаем его данные
if (!empty($_SESSION['user_id'])) {
    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
        
        // Загружаем данные анкеты
        $stmt = $pdo->prepare("SELECT a.* FROM applications a JOIN users u ON a.id = u.application_id WHERE u.id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $app_data = $stmt->fetch();
        
        if ($app_data) {
            // Загружаем выбранные языки
            $stmt = $pdo->prepare("SELECT language_id FROM application_languages WHERE application_id = ?");
            $stmt->execute([$app_data['id']]);
            $langs = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            // Заполняем массив значений для формы
            $values = [
                'full_name' => $app_data['full_name'],
                'phone' => $app_data['phone'],
                'email' => $app_data['email'],
                'birth_date' => $app_data['birth_date'],
                'gender' => $app_data['gender'],
                'biography' => $app_data['biography'],
                'contract_agreed' => (bool)$app_data['contract_agreed'],
                'languages' => $langs
            ];
        }
    } catch (PDOException $e) {
        die("Ошибка базы данных: " . $e->getMessage());
    }
}

// GET запрос - отображаем форму
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    include('form.php');
    exit();
}

// POST запрос - обработка данных формы
$values = $_POST;
$values['languages'] = $_POST['languages'] ?? [];
$values['contract_agreed'] = isset($_POST['contract_agreed']);

// Валидация данных
$validation_failed = false;

if (empty($values['full_name']) || !preg_match('/^[а-яА-ЯёЁa-zA-Z\s\-]{2,150}$/u', $values['full_name'])) {
    $errors['full_name'] = true;
    $validation_failed = true;
}

// ... (остальные проверки валидации)

if ($validation_failed) {
    $_SESSION['errors'] = $errors;
    $_SESSION['form_data'] = $values;
    header('Location: index.php');
    exit();
}

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (!empty($_SESSION['user_id']) && isset($_POST['update'])) {
        // Обновление существующей анкеты
        $stmt = $pdo->prepare("UPDATE applications SET 
            full_name = ?, phone = ?, email = ?, birth_date = ?, 
            gender = ?, biography = ?, contract_agreed = ? 
            WHERE id = (SELECT application_id FROM users WHERE id = ?)");
        $stmt->execute([
            $values['full_name'], $values['phone'], $values['email'],
            $values['birth_date'], $values['gender'], $values['biography'],
            $values['contract_agreed'] ? 1 : 0, $_SESSION['user_id']
        ]);
        
        // Обновляем языки
        $pdo->prepare("DELETE FROM application_languages WHERE application_id = (SELECT application_id FROM users WHERE id = ?)")
            ->execute([$_SESSION['user_id']]);
        
        $stmt = $pdo->prepare("INSERT INTO application_languages (application_id, language_id) VALUES ((SELECT application_id FROM users WHERE id = ?), ?)");
        foreach ($values['languages'] as $lang_id) {
            $stmt->execute([$_SESSION['user_id'], $lang_id]);
        }
        
        $_SESSION['update_success'] = true;
    } else {
        // Создание новой анкеты
        $stmt = $pdo->prepare("INSERT INTO applications 
            (full_name, phone, email, birth_date, gender, biography, contract_agreed) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $values['full_name'], $values['phone'], $values['email'],
            $values['birth_date'], $values['gender'], $values['biography'],
            $values['contract_agreed'] ? 1 : 0
        ]);
        
        $app_id = $pdo->lastInsertId();
        
        // Сохраняем языки
        $stmt = $pdo->prepare("INSERT INTO application_languages (application_id, language_id) VALUES (?, ?)");
        foreach ($values['languages'] as $lang_id) {
            $stmt->execute([$app_id, $lang_id]);
        }
        
        // Генерируем учетные данные
        $login = 'user_' . substr(md5(time()), 0, 8);
        $password = substr(md5(uniqid()), 0, 8);
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO users (login, password, application_id) VALUES (?, ?, ?)");
        $stmt->execute([$login, $password_hash, $app_id]);
        
        // Сохраняем сгенерированные данные для показа пользователю
        $_SESSION['generated_credentials'] = [
            'login' => $login,
            'password' => $password
        ];
    }
    
    header("Location: index.php");
    exit();
} catch (PDOException $e) {
    die("Ошибка базы данных: " . $e->getMessage());
}
?>
