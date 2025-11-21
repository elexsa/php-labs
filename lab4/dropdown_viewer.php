<?php
// Текстові дані
$x = "Dropdown Viewer";
$y = "Variable Y";

// Абзаци тексту та Меню
$p1 = "Lorem ipsum dolor sit amet, consectetur adipiscing elit.";
$p2 = "Donec felis eros, posuere et dui sit amet, pellentesque facilisis turpis.";
$p3 = "Integer efficitur est in vestibulum porta.";

$menu = [
    "Main" => "index.php",
    "Creator" => "menu_creator.php", 
    "Viewer" => "dropdown_viewer.php", 
    "Page1" => "page-1.php",
    "Page2" => "page-2.php",
    "Page3" => "page-3.php",
    "Page4" => "page-4.php"
];

require_once 'db_config.php'; 

$menu_data = [];
$sql_dropdown = "SELECT menu_data FROM dropdown_menu WHERE id = 1";
$result_dropdown = $conn->query($sql_dropdown);

if ($result_dropdown && $result_dropdown->num_rows > 0) {
    $row_dropdown = $result_dropdown->fetch_assoc();
    $menu_data = json_decode($row_dropdown['menu_data'], true);
}

$menu_data_json = json_encode($menu_data);


$last_checked = date('H:i:s'); 

$conn->close();
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title><?= $x; ?></title>
    <link rel="stylesheet" href="style.css">
    
<style>

</style>
</head>
<body>
<div class="container">

    <div class="block b1">
        <div class="inner"><?= $x; ?></div>
        <div id="dropdown-viewer-container">
                    </div>
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
                
            <div class="b3 btext">
                
                
                <div style="margin-top: 20px; border-top: 1px solid #ccc; padding-top: 10px;">
                    <p style="margin: 0;" id="update-status-b3">
                        Меню актуальне. Час перевірки: <?= $last_checked; ?>
                    </p>
                </div>
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

<script>

    const lastModifiedTime = '<?= $last_checked; ?>'; 
    const container = document.getElementById('dropdown-viewer-container');
    const updateStatus = document.getElementById('update-status-b3');

    function generateDropdownHtml(menuData) {
        if (!menuData || menuData.length === 0) {
            return '<p style="padding: 10px;">Меню ще не налаштовано.</p>';
        }

        let html = '<div class="custom-dropdown"><ul class="main-menu">';

        menuData.forEach(item => {
            let subHtml = '';
            const hasSub = item.sub && item.sub.length > 0;
            
            if (hasSub) {
                subHtml = '<ul class="submenu">';
                item.sub.forEach(subItem => {
                    subHtml += `<li><a href="${subItem.url || '#'}">${subItem.text || 'Підпункт'}</a></li>`;
                });
                subHtml += '</ul>';
            }

            // Головний елемент
            const itemContent = item.url 
                ? `<a href="${item.url}">${item.text}</a>` 
                : `<span>${item.text}</span>`;

            html += `
                <li ${hasSub ? 'class="has-submenu"' : ''}>
                    ${itemContent}
                    ${subHtml}
                </li>
            `;
        });

        html += '</ul></div>';
        return html;
    }

    // ----------------------------------------------------
    // АСИНХРОННИЙ КОНТРОЛЬ ЗМІН
    // ----------------------------------------------------

    function fetchMenuDataAndRender() {
        // ... (Ваша AJAX-логіка для завантаження даних меню) ...
        fetch('check_menu.php?get_full_data=1')
             .then(response => response.json())
             .then(data => {
                 if (data.status === 'success' && data.menu_data) {
                     const menuJson = JSON.parse(data.menu_data);
                     container.innerHTML = generateDropdownHtml(menuJson);
                     updateStatus.innerHTML = `Меню успішно оновлено. (${new Date().toLocaleTimeString()})`;
                 }
             })
             .catch(error => {
                 console.error('Помилка при отриманні повних даних:', error);
                 container.innerHTML = '<p style="color: red; padding: 10px;">Не вдалося завантажити дані меню.</p>';
             });
    }

    function checkMenuUpdates() {
        // ... (Ваша AJAX-логіка перевірки мітки часу) ...
        fetch('check_menu.php')
            .then(response => response.json())
            .then(data => {
                const now = new Date().toLocaleTimeString();
                if (data.modified_time && data.modified_time > lastModifiedTime) {
                    updateStatus.innerHTML = `**Виявлено зміни!** Завантаження нових даних (${now})...`;
                    fetchMenuDataAndRender();
                    lastModifiedTime = data.modified_time;
                } else {
                    updateStatus.innerHTML = `Меню актуальне. (Остання перевірка: ${now})`;
                }
            })
            .catch(error => {
                updateStatus.innerHTML = `Помилка зв'язку із сервером.`;
            });
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Початкова генерація при завантаженні сторінки
        const initialMenuData = JSON.parse('<?= $menu_data_json; ?>');
        container.innerHTML = generateDropdownHtml(initialMenuData);
        updateStatus.innerHTML = `Меню завантажено. (Час PHP: ${new Date().toLocaleTimeString()})`;

        // Періодичний контроль (кожні 5 секунд)
        setInterval(checkMenuUpdates, 5000); 
    });
</script>
</body>
</html>