<?php
// НЕ запускаем сессию здесь, она уже запущена в index.php
header('Content-Type: text/html; charset=UTF-8');

// Получаем данные из сессии
$values = $_SESSION['form_data'] ?? [];
$errors = $_SESSION['errors'] ?? [];
$generated_credentials = $_SESSION['generated_credentials'] ?? null;
$login = $_SESSION['login'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;

// Список языков программирования
$languages_list = [
    1 => 'Pascal', 2 => 'C', 3 => 'C++', 4 => 'JavaScript',
    5 => 'PHP', 6 => 'Python', 7 => 'Java', 8 => 'Haskell',
    9 => 'Clojure', 10 => 'Prolog', 11 => 'Scala', 12 => 'Go'
];
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
            background-color: #f5f5f5;
        }
        .form-container {
            background-color: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .credentials-box {
            background: #e8f5e9;
            border: 1px solid #4caf50;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="tel"],
        input[type="email"],
        input[type="date"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }
        select[multiple] {
            height: 120px;
        }
        .error {
            border-color: #f44336;
            background-color: #ffebee;
        }
        .error-message {
            color: #f44336;
            font-size: 0.85em;
            margin-top: 5px;
        }
        button {
            padding: 12px 24px;
            background-color: #2196F3;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .warning {
            color: #f44336;
            font-weight: bold;
        }
        .auth-info {
            padding: 10px;
            background: #e3f2fd;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .radio-group {
            display: flex;
            gap: 15px;
        }
        .radio-group label {
            font-weight: normal;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <?php if (!empty($login)): ?>
            <div class="auth-info">
                Вы вошли как <?= htmlspecialchars($login) ?> 
                (<a href="login.php?action=logout">Выйти</a>)
            </div>
        <?php else: ?>
            <div class="auth-info">
                <a href="login.php">Войти</a>
            </div>
        <?php endif; ?>

        <?php if (!empty($generated_credentials)): ?>
            <div class="credentials-box">
                <h3>Ваши данные для входа</h3>
                <p><strong>Логин:</strong> <?= htmlspecialchars($generated_credentials['login']) ?></p>
                <p><strong>Пароль:</strong> <?= htmlspecialchars($generated_credentials['password']) ?></p>
                <p class="warning">Сохраните эти данные!</p>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="error-message">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php">
            <div class="form-group">
                <label for="full_name">ФИО*</label>
                <input type="text" id="full_name" name="full_name" 
                       value="<?= htmlspecialchars($values['full_name'] ?? '') ?>"
                       class="<?= !empty($errors['full_name']) ? 'error' : '' ?>" 
                       required>
                <?php if (!empty($errors['full_name'])): ?>
                    <div class="error-message"><?= htmlspecialchars($errors['full_name']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="phone">Телефон*</label>
                <input type="tel" id="phone" name="phone" 
                       value="<?= htmlspecialchars($values['phone'] ?? '') ?>"
                       class="<?= !empty($errors['phone']) ? 'error' : '' ?>" 
                       required>
                <?php if (!empty($errors['phone'])): ?>
                    <div class="error-message"><?= htmlspecialchars($errors['phone']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="email">Email*</label>
                <input type="email" id="email" name="email" 
                       value="<?= htmlspecialchars($values['email'] ?? '') ?>"
                       class="<?= !empty($errors['email']) ? 'error' : '' ?>" 
                       required>
                <?php if (!empty($errors['email'])): ?>
                    <div class="error-message"><?= htmlspecialchars($errors['email']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="birth_date">Дата рождения*</label>
                <input type="date" id="birth_date" name="birth_date" 
                       value="<?= htmlspecialchars($values['birth_date'] ?? '') ?>"
                       class="<?= !empty($errors['birth_date']) ? 'error' : '' ?>" 
                       required>
                <?php if (!empty($errors['birth_date'])): ?>
                    <div class="error-message"><?= htmlspecialchars($errors['birth_date']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Пол*</label>
                <div class="radio-group">
                    <label>
                        <input type="radio" name="gender" value="male" 
                            <?= ($values['gender'] ?? '') === 'male' ? 'checked' : '' ?> required>
                        Мужской
                    </label>
                    <label>
                        <input type="radio" name="gender" value="female" 
                            <?= ($values['gender'] ?? '') === 'female' ? 'checked' : '' ?>>
                        Женский
                    </label>
                    <label>
                        <input type="radio" name="gender" value="other" 
                            <?= ($values['gender'] ?? '') === 'other' ? 'checked' : '' ?>>
                        Другой
                    </label>
                </div>
                <?php if (!empty($errors['gender'])): ?>
                    <div class="error-message"><?= htmlspecialchars($errors['gender']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="languages">Любимые языки программирования*</label>
                <select id="languages" name="languages[]" multiple 
                        class="<?= !empty($errors['languages']) ? 'error' : '' ?>" 
                        required>
                    <?php foreach ($languages_list as $id => $name): ?>
                        <option value="<?= $id ?>" 
                            <?= in_array($id, $values['languages'] ?? []) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (!empty($errors['languages'])): ?>
                    <div class="error-message"><?= htmlspecialchars($errors['languages']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="biography">Биография</label>
                <textarea id="biography" name="biography" rows="4"><?= htmlspecialchars($values['biography'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="contract_agreed" 
                        <?= ($values['contract_agreed'] ?? false) ? 'checked' : '' ?>
                        class="<?= !empty($errors['contract_agreed']) ? 'error' : '' ?>" 
                        required>
                    С контрактом ознакомлен(а)*
                </label>
                <?php if (!empty($errors['contract_agreed'])): ?>
                    <div class="error-message"><?= htmlspecialchars($errors['contract_agreed']) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <button type="submit">Отправить</button>
            </div>
        </form>
    </div>
</body>
</html>
<?php
// Очищаем сообщения об ошибках после отображения
unset($_SESSION['error_message']);
unset($_SESSION['errors']);
unset($_SESSION['generated_credentials']);
?>
