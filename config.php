<?php
$survey_config = [
    1 => [
        'text' => "Чи слідкуєте за Євробаченям?",
        'options' => ['Так, щороку', 'Дивлюся лише фінал', 'Ні, не цікавлюся']
    ],
    2 => [
        'text' => "Який був ваш фаворит на Євробаченні?",
        'options' => ['Хорватія (Baby Lasagna)', 'Швейцарія (Nemo)', 'Україна (alyona alyona & Jerry Heil)', 'Інша країна'] 
    ],
    3 => [
        'text' => "Кого хотіли б побачити на Євробаченні 2026?",
        'options' => ['The Hardkiss', 'ONUKA', 'MONATIK', 'Іншого виконавця']
    ],
    4 => [
        'text' => "Чи сподобався виступ Ziferblat?",
        'options' => ['Так, це було дуже стильно', 'Ні, не мій формат', 'Я не дивився(-лася)']
    ],
    5 => [
        'text' => "Яке місце вони мали б отримати на вашу думку?",
        'options' => ['Топ 5', '6 - 10 місце', 'Нижче 10-го місця']
    ],
    6 => [
        'text' => "Чи вважаєте Євробачення не потрібним?",
        'options' => ['Ні, це важливий культурний обмін', 'Так, це політизоване шоу', 'Мені байдуже']
    ],
    7 => [
        'text' => "Як ви вважаєте чому Україна не потрапила в топ 3 на Євробаченні?",
        'options' => ['Слабкий номер', 'Политичні причини', 'Недостатня промоція', 'Інша причина']
    ]
];

// Функція для підключення до БД
function get_db_connection() {
    $db = new PDO('sqlite:survey_db.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec("CREATE TABLE IF NOT EXISTS responses (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT,
        email TEXT,
        answers TEXT,
        created_at DATETIME
    )");
    return $db;
}
?>
