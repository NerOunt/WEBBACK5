<?php
session_start();
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
        
        .credentials-box h3 {
            margin-top: 0;
            color: #2e7d32;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
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
            transition: background-color 0.3s;
        }
        
        button:hover {
            background-color: #0d8bf2;
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
    </style>
</head>
<body>
    <div class="form-container">
        <?php if (!empty($_SESSION['login'])): ?>
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
                <p class="warning">Сохраните эти данные!</p>
            </div>
            <?php unset($_SESSION['generated_credentials']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['error_message'])): ?>
            <div class="error-message">
                <?= htmlspecialchars($_SESSION['error_message']) ?>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <form method="POST" action="index.php">
            <div class="form-group">
                <label for="full_name">ФИО*</label>
                <input type="text" id="full_name" name="full_name" 
                       value="<?= htmlspecialchars($_SESSION['form_data']['full_name'] ?? '') ?>" 
                       required>
            </div>

            <div class="form-group">
                <label for="phone">Телефон*</label>
                <input type="tel" id="phone" name="phone" 
                       value="<?= htmlspecialchars($_SESSION['form_data']['phone'] ?? '') ?>" 
                       required>
            </div>

            <div class="form-group">
                <label for="email">Email*</label>
                <input type="email" id="email" name="email" 
                       value="<?= htmlspecialchars($_SESSION['form_data']['email'] ?? '') ?>" 
                       required>
            </div>

            <div class="form-group">
                <label for="birth_date">Дата рождения*</label>
                <input type="date" id="birth_date" name="birth_date" 
                       value="<?= htmlspecialchars($_SESSION['form_data']['birth_date'] ?? '') ?>" 
                       required>
            </div>

            <div class="form-group">
                <label>Пол*</label>
                <div style="display: flex; gap: 15px;">
                    <label>
                        <input type="radio" name="gender" value="male" 
                            <?= ($_SESSION['form_data']['gender'] ?? '') === 'male' ? 'checked' : '' ?> required>
                        Мужской
                    </label>
                    <label>
                        <input type="radio" name="gender" value="female" 
                            <?= ($_SESSION['form_data']['gender'] ?? '') === 'female' ? 'checked' : '' ?>>
                        Женский
                    </label>
                    <label>
                        <input type="radio" name="gender" value="other" 
                            <?= ($_SESSION['form_data']['gender'] ?? '') === 'other' ? 'checked' : '' ?>>
                        Другой
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label for="languages">Любимые языки программирования*</label>
                <select id="languages" name="languages[]" multiple required>
                    <?php
                    $languages = [
                        1 => 'Pascal', 2 => 'C', 3 => 'C++', 4 => 'JavaScript',
                        5 => 'PHP', 6 => 'Python', 7 => 'Java', 8 => 'Haskell',
                        9 => 'Clojure', 10 => 'Prolog', 11 => 'Scala', 12 => 'Go'
                    ];
                    
                    foreach ($languages as $id => $name) {
                        $selected = in_array($id, $_SESSION['form_data']['languages'] ?? []) ? 'selected' : '';
                        echo "<option value=\"$id\" $selected>".htmlspecialchars($name)."</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="biography">Биография</label>
                <textarea id="biography" name="biography"><?= htmlspecialchars($_SESSION['form_data']['biography'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="contract_agreed" 
                        <?= ($_SESSION['form_data']['contract_agreed'] ?? false) ? 'checked' : '' ?> required>
                    С контрактом ознакомлен(а)*
                </label>
            </div>
            
            <div class="form-group">
                <button type="submit">Отправить</button>
            </div>
        </form>
    </div>
</body>
</html>
<?php
// Очищаем данные формы после отображения
if (isset($_SESSION['form_data'])) {
    unset($_SESSION['form_data']);
}
?>
