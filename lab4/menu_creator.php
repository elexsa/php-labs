<?php
// Текстові дані
$x = "Dropdown Menu Creator";
$y = "Variable Y";

// Абзаци тексту та Меню
$p1 = "На цій сторінці створюється структура меню, яка буде відображена на сторінці Dropdown Viewer.";
$p2 = "Введіть головні пункти меню, їх посилання, та додайте необхідну кількість підпунктів для кожного.";
$p3 = "Після натискання 'Зберегти' дані надсилаються в БД.";
$menu = [
    "Main" => "index.php",
    "Creator" => "menu_creator.php", 
    "Viewer" => "dropdown_viewer.php", 
    "Page3" => "page-3.php",
    "Page4" => "page-4.php"
];

require_once 'db_config.php'; 

$initial_menu_json = '[]'; 

// Завантаження даних з БД (записи з id=1)
$sql = "SELECT menu_data FROM dropdown_menu WHERE id = 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Використовуємо addslashes для екранування лапок, які можуть порушити JS-рядок
    $initial_menu_json = addslashes($row['menu_data']); 
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title><?= $x; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php $start_time_php = microtime(true); ?>

<div class="container">

    <div class="block b1">
        <div class="inner"><?= $x; ?></div>
        <?= "<p>$p1</p>"; ?>
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
                
            <div class="b3 btext" style="width: 100%;">
                <p><b>Dropdown Settings</b></p>
                <div id="menu-creator-container">
                    </div>
                <button id="add-main-item" style="margin-top: 15px;">+ Add Item</button>
                <button id="save-menu-btn" style="background: #a9d18e; border: none; padding: 10px; margin-top: 20px;">Save to server</button>
                <div id="save-status" style="margin-top: 10px; min-height: 20px;"></div>
            </div>
        </div>
        <div class="side-block-col">
            <div class="b4 btext"><?= "<p>$p2</p>"; ?></div>
            <div class="inner-bottom">
                <div class="b5 btext"><?= "<p>$p3</p>"; ?></div>
                <div class="b6 btext"><img src="images/img1.jpg" alt="Image 1" width="200"></div>
            </div>
        </div>
    </div>

    <div class="block b7">
        <?= "<p>$p2</p>"; ?>
        <div class="inner"><?= $y; ?></div>
    </div>

</div>

<?php $end_time_php = microtime(true); ?>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const container = document.getElementById('menu-creator-container');
        const addMainBtn = document.getElementById('add-main-item');
        const saveBtn = document.getElementById('save-menu-btn');
        const statusDiv = document.getElementById('save-status');
        let itemIdCounter = 0;

        // 💡Функція створення головного пункту
        function createMainItem(initialText = null, initialUrl = null) {
            itemIdCounter++;
            const mainId = `main-${itemIdCounter}`;
            const div = document.createElement('div');
            div.className = 'main-item-group';
            div.id = mainId;
            div.style.border = '1px solid #b3cde0';
            div.style.padding = '10px';
            div.style.marginBottom = '10px';
            
            // Використовуємо початкові значення, або значення за замовчуванням
            const textValue = initialText !== null ? initialText : `Пункт ${itemIdCounter}`;
            const urlValue = initialUrl !== null ? initialUrl : `#url${itemIdCounter}`;
            
            div.innerHTML = `
                <h4 style="margin: 5px 0;">Пункт ID: ${itemIdCounter} <button class="remove-main-item" data-id="${mainId}" style="float: right;">X</button></h4>
                <label>Текст: <input type="text" name="main-text" value="${textValue}"></label>
                <label>Посилання: <input type="url" name="main-url" value="${urlValue}"></label><br>
                <div class="sub-items-container" style="margin-top: 10px;"></div>
                <button type="button" class="add-sub-item" data-main-id="${mainId}">+ Додати Підпункт</button>
            `;
            
            // Повертаємо div, щоб функція-виклик вирішила, чи додавати його
            return div;
        }

        // 💡 Функція створення підпункту
        function createSubItem(mainContainer, subIndex, initialText = '', initialUrl = '') {
            const subDiv = document.createElement('div');
            const subId = mainContainer.closest('.main-item-group').id + `-sub-${subIndex}`;
            subDiv.className = 'sub-item';
            subDiv.id = subId;
            subDiv.style.marginLeft = '20px';
            subDiv.style.padding = '5px';
            subDiv.style.borderLeft = '2px solid #ccc';
            
            // Використовуємо початкові значення, або порожні рядки
            const textValue = initialText; 
            const urlValue = initialUrl;
            
            subDiv.innerHTML = `
                <label>Підпункт ${subIndex} Текст: <input type="text" name="sub-text" value="${textValue}"></label>
                <label>Посилання: <input type="url" name="sub-url" value="${urlValue}"></label>
                <button type="button" class="remove-sub-item" style="margin-left: 10px;">X</button>
            `;
            mainContainer.appendChild(subDiv);

            subDiv.querySelector('.remove-sub-item').addEventListener('click', function() {
                subDiv.remove();
            });
        }
        
        // 💡Функція для завантаження даних із БД у форму
        function loadInitialMenu(menuData) {
            // Якщо дані порожні, додаємо один пункт за замовчуванням
            if (!menuData || menuData.length === 0) {
                container.appendChild(createMainItem());
                return;
            }

            // Відтворюємо кожен пункт меню у формі
            menuData.forEach(item => {
                // Створюємо головний пункт, передаючи збережені значення
                const mainDiv = createMainItem(item.text, item.url);
                
                const subContainer = mainDiv.querySelector('.sub-items-container');

                // Відтворюємо підпункти
                if (item.sub && item.sub.length > 0) {
                    item.sub.forEach((subItem, index) => {
                        // Створюємо підпункт
                        createSubItem(subContainer, index + 1, subItem.text, subItem.url);
                    });
                }
                
                // Додаємо повністю зібраний головний пункт до контейнера
                container.appendChild(mainDiv);
            });
        }


        // ----------------------------------------------------------------------------------
        // Обробники
        // ----------------------------------------------------------------------------------

        // 💡 Завантаження початкових даних з PHP
        const initialMenuDataJson = '<?= $initial_menu_json; ?>';
        let initialMenuData = [];
        try {
            // Парс JSON, якщо він не порожній
            if (initialMenuDataJson && initialMenuDataJson !== '[]') {
                initialMenuData = JSON.parse(initialMenuDataJson);
            }
        } catch (e) {
            console.error("Помилка парсингу початкових даних меню:", e);
        }

        // Завантажуємо дані або створюємо початковий елемент
        loadInitialMenu(initialMenuData); 

        // Обробник для кнопки "Add Item"
        addMainBtn.addEventListener('click', () => {
            container.appendChild(createMainItem());
        });

        // Обробник для кнопок "Add Sub Item" та "Remove Main Item"
        container.addEventListener('click', (e) => {
            if (e.target.classList.contains('add-sub-item')) {
                const subContainer = e.target.closest('.main-item-group').querySelector('.sub-items-container');
                const subIndex = subContainer.children.length + 1;
                createSubItem(subContainer, subIndex);
            } else if (e.target.classList.contains('remove-main-item')) {
                if (confirm('Видалити цей пункт меню?')) {
                    e.target.closest('.main-item-group').remove();
                }
            }
        });

        // Збереження на сервер 
        saveBtn.addEventListener('click', () => {
            const menuData = [];
            
            document.querySelectorAll('.main-item-group').forEach(mainDiv => {
                const mainText = mainDiv.querySelector('input[name="main-text"]').value;
                const mainUrl = mainDiv.querySelector('input[name="main-url"]').value;
                const subItems = [];

                mainDiv.querySelectorAll('.sub-item').forEach(subDiv => {
                    subItems.push({
                        text: subDiv.querySelector('input[name="sub-text"]').value,
                        url: subDiv.querySelector('input[name="sub-url"]').value,
                    });
                });

                menuData.push({
                    text: mainText,
                    url: mainUrl,
                    sub: subItems
                });
            });

            statusDiv.innerHTML = '... Збереження ...';
            
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "save_menu.php", true);
            xhr.setRequestHeader("Content-Type", "application/json");

            xhr.onload = function () {
                if (xhr.status === 200) {
                    statusDiv.innerHTML = 'Успішно збережено!';
                } else {
                    statusDiv.innerHTML = `Помилка збереження: ${xhr.status} ${xhr.responseText}`;
                }
            };
            xhr.onerror = function() {
                statusDiv.innerHTML = 'Мережева помилка.';
            };
            xhr.send(JSON.stringify(menuData));
        });
    });
</script>
</body>
</html>