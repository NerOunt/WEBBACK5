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
            color: #333;
        }
        
        .form-container {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #444;
        }
        
        input[type="text"],
        input[type="tel"],
        input[type="email"],
        input[type="date"],
        input[type="password"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }
        
        textarea {
            height: 100px;
            resize: vertical;
        }
        
        select[multiple] {
            height: 120px;
            padding: 5px;
        }
        
        .error {
            border-color: #e74c3c;
            background-color: #fdecea;
        }
        
        .error-message {
            color: #e74c3c;
            font-size: 0.85em;
            margin-top: 5px;
        }
        
        .success {
            color: #27ae60;
            margin-bottom: 20px;
            padding: 12px;
            background: #e8f5e9;
            border: 1px solid #27ae60;
            border-radius: 4px;
        }
        
        .credentials-message {
            margin: 20px 0;
            padding: 15px;
            background: #e3f2fd;
            border: 1px solid #2196f3;
            border-radius: 4px;
        }
        
        .credentials-message h3 {
            margin-top: 0;
            color: #0d47a1;
        }
        
        .credentials-message .warning {
            color: #d32f2f;
            font-weight: bold;
        }
        
        .radio-group {
            display: flex;
            gap: 20px;
            margin-top: 8px;
        }
        
        .radio-option {
            display: flex;
            align-items: center;
        }
        
        .radio-option input[type="radio"] {
            margin-right: 8px;
        }
        
        .checkbox-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .checkbox-container input[type="checkbox"] {
            width: auto;
            margin: 0;
        }
        
        .checkbox-container label {
            margin-bottom: 0;
            font-weight: normal;
        }
        
        .auth-info {
            margin-bottom: 20px;
            padding: 12px;
            background: #e3f2fd;
            border-radius: 4px;
            border-left: 4px solid #2196f3;
        }
        
        .auth-info a {
            color: #0d47a1;
            text-decoration: none;
        }
        
        .auth-info a:hover {
            text-decoration: underline;
        }
        
        button {
            padding: 12px 24px;
            background-color: #2196f3;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        
        button:hover {
            background-color: #0d47a1;
        }
        
        option {
            padding: 8px;
        }
        
        option:hover {
            background-color: #e3f2fd;
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
        
        <?php if (!empty($messages)): ?>
            <?php foreach ($messages as $message): ?>
                <?= $message ?>
            <?php endforeach; ?>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="full_name">ФИО*</label>
                <input type="text" id="full_name" name="full_name" 
                       class="<?= $errors['full_name'] ? 'error' : '' ?>" 
                       value="<?= htmlspecialchars($values['full_name']) ?>">
                <?php if ($errors['full_name']): ?>
                <div class="error-message">Допустимы только буквы, пробелы и дефисы (2-150 символов)</div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="phone">Телефон*</label>
                <input type="tel" id="phone" name="phone" 
                       class="<?= $errors['phone'] ? 'error' : '' ?>" 
                       value="<?= htmlspecialchars($values['phone']) ?>">
                <?php if ($errors['phone']): ?>
                <div class="error-message">Введите 10-15 цифр, можно с + в начале</div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="email">Email*</label>
                <input type="email" id="email" name="email" 
                       class="<?= $errors['email'] ? 'error' : '' ?>" 
                       value="<?= htmlspecialchars($values['email']) ?>">
                <?php if ($errors['email']): ?>
                <div class="error-message">Введите корректный email</div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="birth_date">Дата рождения*</label>
                <input type="date" id="birth_date" name="birth_date" 
                       class="<?= $errors['birth_date'] ? 'error' : '' ?>" 
                       value="<?= htmlspecialchars($values['birth_date']) ?>">
                <?php if ($errors['birth_date']): ?>
                <div class="error-message">Дата должна быть в прошлом</div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Пол*</label>
                <div class="radio-group">
                    <div class="radio-option">
                        <input type="radio" id="male" name="gender" value="male" 
                               <?= $values['gender'] === 'male' ? 'checked' : '' ?>
                               class="<?= $errors['gender'] ? 'error' : '' ?>">
                        <label for="male">Мужской</label>
                    </div>
                    <div class="radio-option">
                        <input type="radio" id="female" name="gender" value="female" 
                               <?= $values['gender'] === 'female' ? 'checked' : '' ?>
                               class="<?= $errors['gender'] ? 'error' : '' ?>">
                        <label for="female">Женский</label>
                    </div>
                </div>
                <?php if ($errors['gender']): ?>
                <div class="error-message">Укажите пол</div>
                <?php endif; ?>
            </div>

           <div class="form-group">
    <label for="languages">Любимые языки программирования*</label>
    <select id="languages" name="languages[]" multiple 
            class="<?= $errors['languages'] ? 'error' : '' ?>">
        <?php
        try {
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $pdo->query("SELECT id, name FROM programming_languages");
            $languages = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($languages as $lang): ?>
                <option value="<?= $lang['id'] ?>" 
                    <?= in_array($lang['id'], $values['languages']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($lang['name']) ?>
                </option>
            <?php endforeach;
        } catch (PDOException $e) {
            
            error_log('Database error: ' . $e->getMessage());
            
           
            echo '<option value="">Ошибка загрузки списка языков</option>';
            
          
            $fallbackLanguages = [
                1 => 'Pascal', 2 => 'C', 3 => 'C++', 4 => 'JavaScript',
                5 => 'PHP', 6 => 'Python', 7 => 'Java', 8 => 'Haskell',
                9 => 'Clojure', 10 => 'Prolog', 11 => 'Scala', 12 => 'Go'
            ];
            
            foreach ($fallbackLanguages as $id => $name): ?>
                <option value="<?= $id ?>" 
                    <?= in_array($id, $values['languages']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($name) ?>
                </option>
            <?php endforeach;
        }
        ?>
    </select>
    <?php if ($errors['languages']): ?>
    <div class="error-message">Выберите хотя бы один язык</div>
    <?php endif; ?>
</div>
                </select>
                <?php if ($errors['languages']): ?>
                <div class="error-message">Выберите хотя бы один язык</div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="biography">Биография</label>
                <textarea id="biography" name="biography"><?= htmlspecialchars($values['biography']) ?></textarea>
            </div>

            <div class="form-group">
                <div class="checkbox-container">
                    <input type="checkbox" id="contract_agreed" name="contract_agreed" value="1"
                           <?= $values['contract_agreed'] ? 'checked' : '' ?>
                           class="<?= $errors['contract_agreed'] ? 'error' : '' ?>">
                    <label for="contract_agreed">С контрактом ознакомлен(а)*</label>
                </div>
                <?php if ($errors['contract_agreed']): ?>
                <div class="error-message">Необходимо подтвердить ознакомление</div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <button type="submit">Отправить</button>
            </div>
        </form>
    </div>
</body>
</html>
