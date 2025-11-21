<?php
require_once 'db_config.php'; // Підключення до БД

// Забезпечуємо, що відповідь буде у форматі JSON
header('Content-Type: application/json');

// Отримання сирих JSON-даних
$json_data = file_get_contents('php://input');
$menu_data = json_decode($json_data, true);

if (empty($menu_data) || !is_array($menu_data)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Некоректні дані меню.']);
    exit;
}

// Кодуємо масив назад у JSON для зберігання в полі menu_data
$menu_json = json_encode($menu_data);

// UPSERT: Оновлюємо запис з id=1. Якщо його немає, вставляємо.
$sql = "INSERT INTO dropdown_menu (id, menu_data) VALUES (1, ?)
        ON DUPLICATE KEY UPDATE menu_data = VALUES(menu_data)";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("s", $menu_json);
    
    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(['status' => 'success', 'message' => 'Меню успішно збережено.']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Помилка виконання SQL.']);
    }
    $stmt->close();
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Помилка підготовки SQL.']);
}

$conn->close();
?>