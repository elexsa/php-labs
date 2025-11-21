<?php
require_once 'db_config.php';
header('Content-Type: application/json');

$sql = "SELECT menu_data, UNIX_TIMESTAMP(last_modified) as modified_time FROM dropdown_menu WHERE id = 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    // Якщо запитуються повні дані (для оновлення)
    if (isset($_GET['get_full_data']) && $_GET['get_full_data'] == '1') {
        echo json_encode([
            'status' => 'success',
            'menu_data' => $row['menu_data']
        ]);
        
    } else {
        // Запит для періодичної перевірки (повертаємо лише мітку часу)
        echo json_encode(['modified_time' => (int)$row['modified_time']]);
    }
    
} else {
    // Якщо запис не знайдено, повертаємо нуль
    echo json_encode(['modified_time' => 0, 'menu_data' => '[]']);
}

$conn->close();
?>