<?php
// Проверяем авторизацию
$logged_in = isset($_SESSION['login']);
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
        
        .credentials-box {
            background: #e8f5e9;
            border: 1px solid #4caf50;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .credentials-box h3 {
            margin-top: 0;
            color: #2e7d32;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        input[type="text"],
        input[type="tel"],
        input[type="email"],
        input[type="date"],
        select,
        textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        
        select[multiple] {
            height: 120px;
        }
        
        .error {
            border-color: #f44336;
        }
        
        .error-message {
            color: #f44336;
            font-size: 0.8em;
        }
        
        button {
            padding: 10px 15px;
            background: #2196F3;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .auth-info {
            padding: 10px;
            background: #e3f2fd;
            margin-bottom: 15px;
            border-radius: 4px;
        }
    </style>
</head>
<body>

<?php if ($logged_in): ?>
    <div class="auth-info">
        Вы вошли как <?= htmlspecialchars($_SESSION['login']) ?> 
        (<a href="login.php?action=logout">Выйти</a>)
    </div>
<?php else: ?>
    <div class="auth-info">
        <a href="login.php">Войти</a>
    </div>
<?php endif; ?>

<?php if (!empty($_SESSION['generated_credentials'])): ?>
    <div class="credentials-box">
        <h3>Ваши данные для входа</h3>
        <p><strong>Логин:</strong> <?= htmlspecialchars($_SESSION['generated_credentials']['login']) ?></p>
        <p><strong>Пароль:</strong> <?= htmlspecialchars($_SESSION['generated_credentials']['password']) ?></p>
        <p style="color: #f44336; font-weight: bold;">Сохраните эти данные!</p>
    </div>
    <?php unset($_SESSION['generated_credentials']); ?>
<?php endif; ?>

<form method="POST">
    <div class="form-group">
        <label for="full_name">ФИО*</label>
        <input type="text" id="full_name" name="full_name" 
               class="<?= $errors['full_name'] ? 'error' : '' ?>" 
               value="<?= htmlspecialchars($values['full_name']) ?>" required>
        <?php if ($errors['full_name']): ?>
        <div class="error-message">Неверный формат ФИО</div>
        <?php endif; ?>
    </div>
    
    <!-- Остальные поля формы аналогично -->
    
    <div class="form-group">
        <button type="submit">Отправить</button>
    </div>
</form>

</body>
</html>
