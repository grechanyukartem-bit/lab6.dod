<?php
require_once 'config.php';
$stage = 'registration';
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db = get_db_connection();

    if (isset($_POST['action']) && $_POST['action'] == 'register') {
        $user_data = [
            'name' => htmlspecialchars($_POST['name']),
            'email' => htmlspecialchars($_POST['email']),
            'timestamp' => date("Y-m-d H:i:s")
        ];
        $stage = 'survey';
    } 
    elseif (isset($_POST['action']) && $_POST['action'] == 'submit_survey') {
        $name = $_POST['user_name'];
        $email = $_POST['user_email'];
        $timestamp = $_POST['user_timestamp'];
        $answers = [];

        foreach ($survey_config as $id => $q) {
            $key = "q{$id}";
            $val = $_POST[$key] ?? '';
            if ($val === 'custom') {
                $answers[$q['text']] = "Своя: " . ($_POST[$key."_custom"] ?? '');
            } else {
                $answers[$q['text']] = $val;
            }
        }

        $stmt = $db->prepare("INSERT INTO responses (name, email, answers, created_at) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, json_encode($answers, JSON_UNESCAPED_UNICODE), $timestamp]);
        $stage = 'thank_you';
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Опитування Євробачення</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css"> </head>
<body>
    <h1>Онлайн Опитування</h1>

    <?php if ($stage === 'registration'): ?>
        <form method="POST">
            <input type="hidden" name="action" value="register">
            <label>Ваше ім'я: <input type="text" name="name" required></label>
            <label>Email: <input type="email" name="email" required></label>
            <button type="submit">Почати</button>
        </form>

    <?php elseif ($stage === 'survey'): ?>
        <form method="POST">
            <input type="hidden" name="action" value="submit_survey">
            <input type="hidden" name="user_name" value="<?= $user_data['name'] ?>">
            <input type="hidden" name="user_email" value="<?= $user_data['email'] ?>">
            <input type="hidden" name="user_timestamp" value="<?= $user_data['timestamp'] ?>">

            <?php foreach ($survey_config as $id => $data): ?>
                <fieldset>
                    <legend><strong><?= $id ?>. <?= $data['text'] ?></strong></legend>
                    <?php foreach ($data['options'] as $opt): ?>
                        <label><input type="radio" name="q<?= $id ?>" value="<?= $opt ?>" required> <?= $opt ?></label>
                    <?php endforeach; ?>
                    <label>
                        <input type="radio" name="q<?= $id ?>" value="custom"> Інше:
                        <input type="text" name="q<?= $id ?>_custom">
                    </label>
                </fieldset>
            <?php endforeach; ?>
            <button type="submit">Надіслати</button>
        </form>

    <?php else: ?>
        <h2>Дякуємо! Ваші відповіді збережено в базі даних.</h2>
        <a href="index.php">Назад</a>
    <?php endif; ?>
</body>
</html>
