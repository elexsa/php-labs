<?php
// Текстові дані
$x = "Main Page";
$y = "Variable Y";

// Абзаци тексту
$p1 = "Lorem ipsum dolor sit amet, consectetur adipiscing elit.";
$p2 = "Donec felis eros, posuere et dui sit amet, pellentesque facilisis turpis.";
$p3 = "Integer efficitur est in vestibulum porta.";

// Меню
$menu = [
    "Main" => "index.php",
    "Page1" => "page-1.php",
    "Page2" => "page-2.php",
    "Page3" => "page-3.php",
    "Page4" => "page-4.php"
];

// Підключення до БД
require_once 'db_config.php'; 

// Унікальний ключ сторінки
$page_key = basename($_SERVER['PHP_SELF'], '.php'); 

// Завантаження всіх збережених змін для поточної сторінки
$edits = [];
$db_start_time = microtime(true);
$sql_select = "SELECT element_key, content FROM content_edits WHERE page_key = ?";

// Використання підготовлених запитів для безпеки
if ($stmt = $conn->prepare($sql_select)) {
    $stmt->bind_param("s", $page_key);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        // Зберігаємо дані у вигляді: ['block-b1-inner' => 'Новий вміст']
        $edits[$row['element_key']] = $row['content'];
    }
    $stmt->close();
} else {
    echo "Помилка при підготовці запиту: " . $conn->error;
}
$db_end_time = microtime(true); 

// Допоміжна функція для підтягування контенту з БД або використання початкового
function get_content($key, $default_content, $edits) {
    // Якщо ключ знайдено в масиві $edits (дані з БД), повертаємо його.
    // Інакше повертаємо початковий вміст.
    return $edits[$key] ?? $default_content;
}

?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title><?= $x ?></title> <link rel="stylesheet" href="style.css">
</head>
<body>
<?php 
$start_time_php = microtime(true); 
?>

<div class="container">

    <div class="block b1 editable" data-key="block-b1">
        <div class="inner editable" data-key="block-b1-inner">
            <?= get_content('block-b1-inner', $x, $edits); ?>
        </div>
        <p class='editable' data-key='block-b1-p'>
            <?= get_content('block-b1-p', $p1, $edits); ?>
        </p>
    </div>

    <div class="middle">
        <div class="side-block-row">
            <div class="b2 menu">
                <ul>
                    <?php foreach($menu as $name=>$link){
                        echo "<li><a href=\"$link\">$name</a></li>";
                    } ?>
                </ul>
            </div>
                
            <div class="b3 btext">
                <ul class="editable" data-key="block-b3-ul">
                    <?= get_content('block-b3-ul', "<li>One</li><li>Two</li><li>Three</li>", $edits); ?>
                </ul>
            </div>
        </div>

        <div class="side-block-col">
            <div class="b4 btext editable" data-key="block-b4-p">
                <?= get_content('block-b4-p', "<p>$p2</p>", $edits); ?>
            </div>
            <div class="inner-bottom">
                <div class="b5 btext editable" data-key="block-b5-p">
                    <?= get_content('block-b5-p', "<p>$p3</p>", $edits); ?>
                </div>
                <div class="b6 btext">
                    <img src="images/img1.jpg" alt="Image 1" width="200">
                    <div class="editable" data-key="block-b6-text">
                        <?= get_content('block-b6-text', "Image description", $edits); ?>
                    </div> 
                </div>
            </div>
        </div>
    </div>

    <div class="block b7 editable" data-key="block-b7">
        <p class='editable' data-key='block-b7-p'>
            <?= get_content('block-b7-p', $p2, $edits); ?>
        </p>
        <div class="inner editable" data-key="block-b7-inner">
            <?= get_content('block-b7-inner', $y, $edits); ?>
        </div>
    </div>

</div>
<?php 

$end_time_php = microtime(true); 
$phpGenerationTime = $end_time_php - $start_time_php;
$dbQueryTime = $db_end_time - $db_start_time;

?>

<script>
    // Обчислення часу
    const phpGenerationTime = <?= number_format($phpGenerationTime, 6, '.', ''); ?>;
    const dbQueryTime = <?= number_format($dbQueryTime, 6, '.', ''); ?>;

    // Дані для порівняння з Практикумом №2 (LocalStorage)
    const lsGenTime = 0.004; 
    const lsLoadTime = 0.4; 
    const lsTotalTime = lsGenTime + (lsLoadTime / 1000);

    document.addEventListener('DOMContentLoaded', () => {

        // Отримання ключа сторінки, згенерованого PHP
        const pageKey = '<?= $page_key ?>'; 
        const editableElements = document.querySelectorAll('.editable');
        
        // У моделі БД загальний час формування = час PHP (який включає БД)
        const totalLoadTime = phpGenerationTime+dbQueryTime; 

        // Відображення таблиці з часом
        const timeDisplay = document.createElement('div');
        timeDisplay.innerHTML = `

            <h3 style="text-align: center; margin-top: 20px;">Обчислення Часу Завантаження Практикум 3</h3>
            <table style="width: 80%; margin: 10px auto; border-collapse: collapse;">
                <tr><td style="border: 1px solid #ccc; padding: 5px;">Час генерації PHP:</td><td style="border: 1px solid #ccc; padding: 5px; font-weight: bold;">${phpGenerationTime.toFixed(6)} сек</td></tr>
                <tr><td style="border: 1px solid #ccc; padding: 5px;">Загальний час формування сторінки:</td><td style="border: 1px solid #ccc; padding: 5px; font-weight: bold;">${totalLoadTime.toFixed(6)} сек</td></tr>
            </table>
            
            <h3 style="text-align: center;">Порівняння з практикумом 2</h3>
            <table style="width: 80%; margin: 10px auto; border-collapse: collapse;">
                <tr><td style="border: 1px solid #ccc; padding: 5px;">Час PHP Gen (LS):</td><td style="border: 1px solid #ccc; padding: 5px;">${lsGenTime.toFixed(6)} сек</td></tr>
                <tr><td style="border: 1px solid #ccc; padding: 5px;">Час підтягування (LS):</td><td style="border: 1px solid #ccc; padding: 5px;">${lsLoadTime.toFixed(4)} мс</td></tr>
                <tr><td style="border: 1px solid #ccc; padding: 5px;">Загальний час (LS):</td><td style="border: 1px solid #ccc; padding: 5px; font-weight: bold;">${lsTotalTime.toFixed(6)} сек</td></tr>
                <tr><td style="border: 1px solid #ccc; padding: 5px;">Різниця (БД - LS):</td><td style="border: 1px solid #ccc; padding: 5px; font-weight: bold;">${(totalLoadTime - lsTotalTime).toFixed(6)} сек</td></tr>
            </table>
        `;
        document.querySelector('.container').appendChild(timeDisplay);

        // Обробка кліку для редагування
        editableElements.forEach(element => {
            element.style.cursor = 'pointer';

            element.addEventListener('click', function(event) {
                event.stopPropagation(); 

                // Якщо вже є форма, ігноруємо клік
                if (element.querySelector('form')) return;

                const currentContent = element.innerHTML.trim(); 
                const dataKey = element.getAttribute('data-key');
                const form = document.createElement('form');
                const textarea = document.createElement('textarea');
                const saveButton = document.createElement('button');
                const cancelButton = document.createElement('button');
                
                // Налаштування елементів форми
                textarea.value = currentContent;
                textarea.style.width = '100%';
                textarea.style.minHeight = '100px';
                textarea.style.boxSizing = 'border-box';
                
                saveButton.textContent = 'Зберегти';
                saveButton.type = 'submit'; 
                
                // AJAX-Збереження на Сервер (замість localStorage)
                form.onsubmit = function(e) {
                    e.preventDefault();
                    const newContent = textarea.value.trim();

                    // Надсилання даних на сервер
                    const xhr = new XMLHttpRequest();
                    xhr.open("POST", "save_content.php", true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    
                    xhr.onload = function () {
                        if (xhr.status === 200) {
                            // Успішно збережено в MySQL, оновлюємо вміст
                            element.innerHTML = newContent;
                        } else {
                            alert('Помилка збереження даних на сервері. Статус: ' + xhr.status);
                            // Якщо помилка, відновлюємо попередній вміст
                            element.innerHTML = currentContent; 
                        }
                    };
                    
                    // Формування POST-даних
                    const data = `page_key=${pageKey}&element_key=${dataKey}&content=${encodeURIComponent(newContent)}`;
                    xhr.send(data);
                };

                // Збірка та відображення форми
                form.appendChild(textarea);
                form.appendChild(saveButton);

                element.innerHTML = '';
                element.appendChild(form);
                textarea.focus();
            }, { once: false });
        });
    });
</script>
</body>
</html>