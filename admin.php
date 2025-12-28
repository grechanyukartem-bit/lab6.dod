<?php
session_start();
require_once 'config.php';
$db = get_db_connection();

// Авторизація
if (isset($_POST['login'])) {
    if ($_POST['user'] === 'admin' && $_POST['pass'] === '123') {
        $_SESSION['admin_logged_in'] = true;
    }
}

// Вихід
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
}

// Видалення
if (isset($_GET['delete']) && isset($_SESSION['admin_logged_in'])) {
    $stmt = $db->prepare("DELETE FROM responses WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
}

// Експорт у CSV
if (isset($_GET['export']) && isset($_SESSION['admin_logged_in'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=survey_results.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Ім’я', 'Email', 'Дата', 'Відповіді']);
    
    $data = $db->query("SELECT * FROM responses")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($data as $row) fputcsv($output, $row);
    exit;
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Адмін-панель</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>
    <?php if (!isset($_SESSION['admin_logged_in'])): ?>
        <form method="POST">
            <h2>Вхід для адміна</h2>
            <input type="text" name="user" placeholder="Логін" required>
            <input type="password" name="pass" placeholder="Пароль" required>
            <button type="submit" name="login">Увійти</button>
        </form>
    <?php else: ?>
        <nav>
            <a href="?export=1">📥 Експорт у CSV</a> | 
            <a href="?logout=1">🚪 Вийти</a>
        </nav>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Ім'я</th><th>Email</th><th>Відповіді</th><th>Дія</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $res = $db->query("SELECT * FROM responses ORDER BY id DESC");
                while($row = $res->fetch(PDO::FETCH_ASSOC)):
                    $answers = json_decode($row['answers'], true);
                ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td>
                        <details>
                            <summary>Переглянути</summary>
                            <ul>
                            <?php foreach($answers as $q => $a): ?>
                                <li><strong><?= $q ?>:</strong> <?= htmlspecialchars($a) ?></li>
                            <?php endforeach; ?>
                            </ul>
                        </details>
                    </td>
                    <td><a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Видалити?')">❌</a></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
