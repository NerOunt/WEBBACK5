<?php
// Сессия уже запущена в index.php
header('Content-Type: text/html; charset=UTF-8');

$values = $_SESSION['form_data'] ?? [];
$errors = $_SESSION['errors'] ?? [];
$generated_credentials = $_SESSION['generated_credentials'] ?? null;
$login = $_SESSION['login'] ?? null;

// Получаем список языков из БД
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
    </style>
</head>
<body>
    <?php if (!empty($login)): ?>
        <p>Вы вошли как: <?= htmlspecialchars($login) ?> (<a href="login.php?action=logout">Выйти</a>)</p>
    <?php else: ?>
        <p><a href="login.php">Войти</a></p>
    <?php endif; ?>

    <?php if (!empty($generated_credentials)): ?>
        <div class="credentials">
            <h3>Ваши данные для входа:</h3>
            <p><strong>Логин:</strong> <?= htmlspecialchars($generated_credentials['login']) ?></p>
            <p><strong>Пароль:</strong> <?= htmlspecialchars($generated_credentials['password']) ?></p>
            <p style="color: red;">Сохраните эти данные!</p>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php">
        <div class="form-group">
            <label for="full_name">ФИО*</label>
            <input type="text" id="full_name" name="full_name" 
                   value="<?= htmlspecialchars($values['full_name'] ?? '') ?>"
                   class="<?= !empty($errors['full_name']) ? 'error' : '' ?>" required>
            <?php if (!empty($errors['full_name'])): ?>
                <div class="error-message">Введите корректное ФИО</div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="phone">Телефон*</label>
            <input type="tel" id="phone" name="phone" 
                   value="<?= htmlspecialchars($values['phone'] ?? '') ?>"
                   class="<?= !empty($errors['phone']) ? 'error' : '' ?>" required>
            <?php if (!empty($errors['phone'])): ?>
                <div class="error-message">Введите корректный телефон</div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="email">Email*</label>
            <input type="email" id="email" name="email" 
                   value="<?= htmlspecialchars($values['email'] ?? '') ?>"
                   class="<?= !empty($errors['email']) ? 'error' : '' ?>" required>
            <?php if (!empty($errors['email'])): ?>
                <div class="error-message">Введите корректный email</div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="birth_date">Дата рождения*</label>
            <input type="date" id="birth_date" name="birth_date" 
                   value="<?= htmlspecialchars($values['birth_date'] ?? '') ?>"
                   class="<?= !empty($errors['birth_date']) ? 'error' : '' ?>" required>
            <?php if (!empty($errors['birth_date'])): ?>
                <div class="error-message">Введите корректную дату</div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label>Пол*</label>
            <div>
                <label><input type="radio" name="gender" value="male" 
                    <?= ($values['gender'] ?? '') === 'male' ? 'checked' : '' ?> required> Мужской</label>
                <label><input type="radio" name="gender" value="female"
                    <?= ($values['gender'] ?? '') === 'female' ? 'checked' : '' ?>> Женский</label>
                <label><input type="radio" name="gender" value="other"
                    <?= ($values['gender'] ?? '') === 'other' ? 'checked' : '' ?>> Другой</label>
            </div>
            <?php if (!empty($errors['gender'])): ?>
                <div class="error-message">Выберите пол</div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="languages">Языки программирования*</label>
            <select id="languages" name="languages[]" multiple 
                    class="<?= !empty($errors['languages']) ? 'error' : '' ?>" required>
                <?php foreach ($languages as $lang): ?>
                    <option value="<?= $lang['id'] ?>"
                        <?= in_array($lang['id'], $values['languages'] ?? []) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($lang['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (!empty($errors['languages'])): ?>
                <div class="error-message">Выберите хотя бы один язык</div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="biography">Биография</label>
            <textarea id="biography" name="biography"><?= htmlspecialchars($values['biography'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="contract_agreed"
                    <?= ($values['contract_agreed'] ?? false) ? 'checked' : '' ?> required>
                Согласен с условиями*
            </label>
            <?php if (!empty($errors['contract_agreed'])): ?>
                <div class="error-message">Необходимо согласие</div>
            <?php endif; ?>
        </div>

        <button type="submit">Отправить</button>
    </form>
</body>
</html>
<?php
// Очищаем данные после отображения
unset($_SESSION['errors'], $_SESSION['generated_credentials']);
?>
