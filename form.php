<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');

$values = $_SESSION['form_data'] ?? [];
$errors = $_SESSION['errors'] ?? [];
$generated_credentials = $_SESSION['generated_credentials'] ?? null;
$login = $_SESSION['login'] ?? null;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Анкета</title>
    <style>
        /* Стили остаются без изменений */
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; }
        .form-container { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .error { border-color: #f44336; background-color: #ffebee; }
        .error-message { color: #f44336; font-size: 0.85em; }
    </style>
</head>
<body>
    <div class="form-container">
        <?php if (!empty($login)): ?>
            <div>Вы вошли как <?= htmlspecialchars($login) ?> (<a href="login.php?action=logout">Выйти</a>)</div>
        <?php endif; ?>

        <?php if (!empty($generated_credentials)): ?>
            <div style="background: #e8f5e9; padding: 15px; margin-bottom: 20px;">
                <h3>Ваши данные для входа</h3>
                <p><strong>Логин:</strong> <?= htmlspecialchars($generated_credentials['login']) ?></p>
                <p><strong>Пароль:</strong> <?= htmlspecialchars($generated_credentials['password']) ?></p>
                <p style="color: #f44336;">Сохраните эти данные!</p>
            </div>
            <?php unset($_SESSION['generated_credentials']); ?>
        <?php endif; ?>

        <form method="POST" action="index.php">
            <!-- Все поля формы с выводом ошибок -->
            <div>
                <label>ФИО*</label>
                <input type="text" name="full_name" value="<?= htmlspecialchars($values['full_name'] ?? '') ?>"
                       class="<?= !empty($errors['full_name']) ? 'error' : '' ?>" required>
                <?php if (!empty($errors['full_name'])): ?>
                    <div class="error-message"><?= htmlspecialchars($errors['full_name']) ?></div>
                <?php endif; ?>
            </div>
            
            <!-- Остальные поля аналогично -->
            
            <button type="submit">Отправить</button>
        </form>
    </div>
</body>
</html>
<?php
unset($_SESSION['form_data']);
unset($_SESSION['errors']);
?>
