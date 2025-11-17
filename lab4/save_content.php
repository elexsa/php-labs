<?php
// db_config.php для підключення до БД
require_once 'db_config.php';

// Перевірка даних для POST-запиту
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['page_key'], $_POST['element_key'], $_POST['content'])) {
    
    $page_key = $_POST['page_key'];
    $element_key = $_POST['element_key'];
    $content = $_POST['content'];

    // Якщо запис існує (за page_key та element_key), оновлюємо його
    // Інакше, вставляємо новий запис
    $sql_insert = "INSERT INTO content_edits (page_key, element_key, content) 
                   VALUES (?, ?, ?)
                   ON DUPLICATE KEY UPDATE content = VALUES(content)";

    $stmt = $conn->prepare($sql_insert);
    $stmt->bind_param("sss", $page_key, $element_key, $content);
    
    if ($stmt->execute()) {
        http_response_code(200); // Успішно
        echo "Дані збережено.";
    } else {
        http_response_code(500); 
        echo "Помилка при збереженні: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
    exit;

} else {
    http_response_code(400); // Помилка запиту
    echo "Некоректний запит.";
}
?>