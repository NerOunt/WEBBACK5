<?php
header('Content-Type: text/html; charset=UTF-8');

$values = $_SESSION['form_data'] ?? [];
$errors = $_SESSION['errors'] ?? [];
$generated_credentials = $_SESSION['generated_credentials'] ?? null;
$login = $_SESSION['login'] ?? null;

try {
    $db_host = 'localhost';
    $db_name = 'u68895';
    $db_user = 'u68895';
    $db_pass = '1562324';
    
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $stmt = $pdo->query("SELECT * FROM programming_languages");
    $languages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $languages = [];
}

$is_edit_mode = !empty($login);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Анкета</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            line-height: 1.6;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, select, textarea {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        select[multiple] {
            height: 120px;
        }
        .error {
            border-color: red;
        }
        .error-message {
            color: red;
            font-size: 0.8em;
        }
        .credentials {
            background: #f0f8ff;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .success-message {
            color: green;
            margin-bottom: 15px;
            padding: 10px;
            background: #f0fff0;
            border: 1px solid #a0d8a0;
            border-radius: 4px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .input-label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        .input-group {
            margin-top: 5px;
        }
        .input-option {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }
        .input-option input[type="radio"],
        .input-option input[type="checkbox"] {
            width: auto;
            margin: 0 10px 0 0;
            transform: scale(1.2);
        }
        .option-label {
            font-weight: normal;
            cursor: pointer;
            user-select: none;
            margin-bottom: 0;
        }
        .login-prompt {
            background: #fffacd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #e6db55;
        }
    </style>
</head>
<body>
    <?php if (!empty($login)): ?>
        <p>Вы вошли как: <?= htmlspecialchars($login) ?> (<a href="login.php?action=logout">Выйти</a>)</p>
    <?php else: ?>
        <p><a href="login.php">Войти</a></p>
    <?php endif; ?>

    <?php if (!empty($_SESSION['update_success'])): ?>
        <div class="success-message">Данные успешно обновлены!</div>
        <?php unset($_SESSION['update_success']); ?>
    <?php endif; ?>

    <?php if (!empty($generated_credentials) && empty($login)): ?>
        <div class="login-prompt">
            <h3>Ваша анкета успешно сохранена!</h3>
            <p>Для доступа к вашим данным используйте сгенерированные учетные данные.</p>
            <p><a href="login.php">Перейти на страницу входа</a></p>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php">
        <?php if ($is_edit_mode): ?>
            <input type="hidden" name="update" value="1">
        <?php endif; ?>

        <div class="form-group">
            <label for="full_name">ФИО*</label>
            <input type="text" id="full_name" name="full_name" 
                   value="<?= htmlspecialchars($values['full_name'] ?? '') ?>"
                   class="<?= !empty($errors['full_name']) ? 'error' : '' ?>" required>
            <?php if (!empty($errors['full_name'])): ?>
                <div class="error-message">Введите корректное ФИО</div>
            <?php endif; ?>
        </div>

        <!-- Остальные поля формы остаются без изменений -->
        
        <button type="submit"><?= $is_edit_mode ? 'Обновить данные' : 'Отправить' ?></button>
    </form>
</body>
</html>
<?php
unset($_SESSION['errors'], $_SESSION['form_data']);
?>
