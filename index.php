<?php
header('Content-Type: text/html; charset=UTF-8');

session_start();

$db_host = 'localhost';
$db_name = 'u68895';
$db_user = 'u68895';
$db_pass = '1562324';

$messages = array();
$errors = array();
$values = array();


$logged_in = !empty($_SESSION['login']);
$user_id = $logged_in ? $_SESSION['uid'] : null;

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  
    if (!empty($_COOKIE['save'])) {
        setcookie('save', '', time() - 3600);
        $messages[] = '<div class="success">Результаты сохранены.</div>';
    }
    
   
    if (!empty($_COOKIE['login_credentials'])) {
        $credentials = json_decode($_COOKIE['login_credentials'], true);
        $messages[] = '<div class="success">Ваши данные для входа:<br>Логин: '.htmlspecialchars($credentials['login']).'<br>Пароль: '.htmlspecialchars($credentials['pass']).'</div>';
        setcookie('login_credentials', '', time() - 3600);
    }

  
    $error_fields = array(
        'full_name', 'phone', 'email', 'birth_date', 
        'gender', 'languages', 'contract_agreed'
    );
    
    foreach ($error_fields as $field) {
        $errors[$field] = !empty($_COOKIE[$field.'_error']);
        if ($errors[$field]) {
            setcookie($field.'_error', '', time() - 3600);
        }
    }

 
    $value_fields = array(
        'full_name', 'phone', 'email', 'birth_date', 
        'gender', 'biography', 'contract_agreed'
    );
    
    if ($logged_in) {
        try {
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stmt = $pdo->prepare("SELECT * FROM applications WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $user_data = $stmt->fetch();
            
            if ($user_data) {
                $values['full_name'] = $user_data['full_name'];
                $values['phone'] = $user_data['phone'];
                $values['email'] = $user_data['email'];
                $values['birth_date'] = $user_data['birth_date'];
                $values['gender'] = $user_data['gender'];
                $values['biography'] = $user_data['biography'];
                $values['contract_agreed'] = $user_data['contract_agreed'];
                
               
                $stmt = $pdo->prepare("SELECT language_id FROM application_languages WHERE application_id = ?");
                $stmt->execute([$user_data['id']]);
                $values['languages'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
            }
        } catch (PDOException $e) {
            $messages[] = '<div class="error">Ошибка базы данных</div>';
        }
    } else {
        foreach ($value_fields as $field) {
            $values[$field] = empty($_COOKIE[$field.'_value']) ? '' : $_COOKIE[$field.'_value'];
        }
        $values['languages'] = empty($_COOKIE['languages_value']) ? array() : explode(',', $_COOKIE['languages_value']);
        $values['contract_agreed'] = !empty($_COOKIE['contract_agreed_value']);
    }
    
    include('form.php');
} else {
   
    $validation_failed = false;

   
    if (empty($_POST['full_name']) || !preg_match('/^[а-яА-ЯёЁa-zA-Z\s\-]{2,150}$/u', $_POST['full_name'])) {
        setcookie('full_name_error', '1', 0); 
        $validation_failed = true;
    }
    setcookie('full_name_value', $_POST['full_name'], time() + 365 * 24 * 60 * 60);

    if (empty($_POST['phone']) || !preg_match('/^\+?\d{10,15}$/', $_POST['phone'])) {
        setcookie('phone_error', '1', 0);
        $validation_failed = true;
    }
    setcookie('phone_value', $_POST['phone'], time() + 365 * 24 * 60 * 60);

    if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        setcookie('email_error', '1', 0);
        $validation_failed = true;
    }
    setcookie('email_value', $_POST['email'], time() + 365 * 24 * 60 * 60);

    $today = new DateTime();
    $birthdate = DateTime::createFromFormat('Y-m-d', $_POST['birth_date']);
    if (empty($_POST['birth_date']) || !$birthdate || $birthdate > $today) {
        setcookie('birth_date_error', '1', 0);
        $validation_failed = true;
    }
    setcookie('birth_date_value', $_POST['birth_date'], time() + 365 * 24 * 60 * 60);

    if (empty($_POST['gender']) || !in_array($_POST['gender'], ['male', 'female'])) {
        setcookie('gender_error', '1', 0);
        $validation_failed = true;
    }
    setcookie('gender_value', $_POST['gender'], time() + 365 * 24 * 60 * 60);

    $allowedLanguages = range(1, 12);
    if (empty($_POST['languages'])) {
        setcookie('languages_error', '1', 0);
        $validation_failed = true;
    } else {
        foreach ($_POST['languages'] as $langId) {
            if (!in_array($langId, $allowedLanguages)) {
                setcookie('languages_error', '1', 0);
                $validation_failed = true;
                break;
            }
        }
    }
    setcookie('languages_value', implode(',', $_POST['languages']), time() + 365 * 24 * 60 * 60);

    if (empty($_POST['contract_agreed'])) {
        setcookie('contract_agreed_error', '1', 0);
        $validation_failed = true;
    }
    setcookie('contract_agreed_value', isset($_POST['contract_agreed']) ? '1' : '', time() + 365 * 24 * 60 * 60);

    if ($validation_failed) {
        header('Location: index.php');
        exit();
    } else {
        
        $error_cookies = array(
            'full_name_error', 'phone_error', 'email_error', 
            'birth_date_error', 'gender_error', 'languages_error', 
            'contract_agreed_error'
        );
        
        foreach ($error_cookies as $cookie) {
            setcookie($cookie, '', time() - 3600);
        }

        try {
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            if ($logged_in) {
               
                $stmt = $pdo->prepare("UPDATE applications SET full_name = ?, phone = ?, email = ?, birth_date = ?, gender = ?, biography = ?, contract_agreed = ? WHERE user_id = ?");
                $stmt->execute([
                    $_POST['full_name'],
                    $_POST['phone'],
                    $_POST['email'],
                    $_POST['birth_date'],
                    $_POST['gender'],
                    $_POST['biography'],
                    isset($_POST['contract_agreed']) ? 1 : 0,
                    $user_id
                ]);
                
               
                $stmt = $pdo->prepare("SELECT id FROM applications WHERE user_id = ?");
                $stmt->execute([$user_id]);
                $appId = $stmt->fetchColumn();
                
             
                $stmt = $pdo->prepare("DELETE FROM application_languages WHERE application_id = ?");
                $stmt->execute([$appId]);
                
                
                $stmt = $pdo->prepare("INSERT INTO application_languages (application_id, language_id) VALUES (?, ?)");
                foreach ($_POST['languages'] as $langId) {
                    $stmt->execute([$appId, $langId]);
                }
                
                setcookie('save', '1', time() + 24 * 60 * 60);
                header('Location: index.php');
            } else {
                
               
                $login = bin2hex(random_bytes(8));
                $pass = bin2hex(random_bytes(4));
                $pass_hash = password_hash($pass, PASSWORD_DEFAULT);
                
              
                $stmt = $pdo->prepare("INSERT INTO users (login, pass_hash) VALUES (?, ?)");
                $stmt->execute([$login, $pass_hash]);
                $user_id = $pdo->lastInsertId();
                
                
                $stmt = $pdo->prepare("INSERT INTO applications (user_id, full_name, phone, email, birth_date, gender, biography, contract_agreed) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $user_id,
                    $_POST['full_name'],
                    $_POST['phone'],
                    $_POST['email'],
                    $_POST['birth_date'],
                    $_POST['gender'],
                    $_POST['biography'],
                    isset($_POST['contract_agreed']) ? 1 : 0
                ]);
                
                $appId = $pdo->lastInsertId();
                
               
                $stmt = $pdo->prepare("INSERT INTO application_languages (application_id, language_id) VALUES (?, ?)");
                foreach ($_POST['languages'] as $langId) {
                    $stmt->execute([$appId, $langId]);
                }
                
                
                setcookie('login_credentials', json_encode(['login' => $login, 'pass' => $pass]), time() + 24 * 60 * 60);
                setcookie('save', '1', time() + 24 * 60 * 60);
                header('Location: index.php');
            }
        } catch (PDOException $e) {
            setcookie('database_error', '1', time() + 24 * 60 * 60);
            header('Location: index.php');
            exit();
        }
    }
}
?>