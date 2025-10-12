<?php
// Текстові дані
$x = "Page 2";
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
?>



<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title><?= $x; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php $start_time_php = microtime(true); // Час на початку генерації сторінки на сервері ?>

<div class="container">

    <div class="block b1 editable" data-key="block-b1">
        <div class="inner editable" data-key="block-b1-inner"><?= $x; ?></div>
        <?= "<p class='editable' data-key='block-b1-p'>$p1</p>"; ?>
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
                    <li>One</li>
                    <li>Two</li>
                    <li>Three</li>
                </ul>
            </div>
        </div>

        <div class="side-block-col">
            <div class="b4 btext editable" data-key="block-b4-p"><?= "<p>$p2</p>"; ?></div>
            <div class="inner-bottom">
                <div class="b5 btext editable" data-key="block-b5-p"><?= "<p>$p3</p>"; ?></div>
                <div class="b6 btext">
                    <img src="images/img1.jpg" alt="Image 1" width="200">
                    <div class="editable" data-key="block-b6-text">Image description</div> </div>
            </div>
        </div>
    </div>

    <div class="block b7 editable" data-key="block-b7">
        <?= "<p class='editable' data-key='block-b7-p'>$p2</p>"; ?>
        <div class="inner editable" data-key="block-b7-inner"><?= $y; ?></div>
    </div>

</div>

<?php $end_time_php = microtime(true); // Час на кінець генерації сторінки на сервері ?>

<script>
    // Обчислення часу
    const phpGenerationTime = <?= $end_time_php - $start_time_php; ?>; // Час генерації на сервері
    let localStorageTime = 0; 

    // Блок редагування та використання localStorage
    document.addEventListener('DOMContentLoaded', () => {
        const pageKey = window.location.pathname.split('/').pop().replace('.php', ''); // Унікальний ключ сторінки

        // Перевірка та підтягування даних з localStorage
        const lsStart = performance.now();
        const editableElements = document.querySelectorAll('.editable');
        editableElements.forEach(element => {
            const dataKey = element.getAttribute('data-key');
            const storageKey = `${pageKey}-${dataKey}`;
            const storedValue = localStorage.getItem(storageKey);
            
            if (storedValue) {
                element.innerHTML = storedValue;
            }
        });
        localStorageTime = performance.now() - lsStart; // Час за який підтягуються дані з localStorage 
        
        // Відображення часу завантаження
        const totalLoadTime = phpGenerationTime + (localStorageTime / 1000); 
        console.log(`Час генерації PHP: ${phpGenerationTime.toFixed(4)} сек`);
        console.log(`Час підтягування з localStorage: ${localStorageTime.toFixed(4)} мс`);
        console.log(`Загальний час формування сторінки: ${totalLoadTime.toFixed(4)} сек`);
        

        // Обробка кліку для редагування
        editableElements.forEach(element => {
            element.style.cursor = 'pointer';

            element.addEventListener('click', function(event) {
                event.stopPropagation(); // Запобігти багаторазовому запуску при вкладених елементах

                // Якщо вже є форма, ігноруємо клік
                if (element.querySelector('form')) return;

                const originalContent = element.innerHTML.trim();
                const form = document.createElement('form');
                const textarea = document.createElement('textarea');
                const saveButton = document.createElement('button');
                const cancelButton = document.createElement('button');
                
                // Налаштування елементів форми
                textarea.value = originalContent;
                textarea.style.width = '100%';
                textarea.style.minHeight = '100px';
                
                saveButton.textContent = 'Зберегти';
                saveButton.type = 'submit';

                // Збереження вмісту
                form.onsubmit = function(e) {
                    e.preventDefault();
                    const newContent = textarea.value.trim();
                    const dataKey = element.getAttribute('data-key');
                    const storageKey = `${pageKey}-${dataKey}`;

                    element.innerHTML = newContent;

                    localStorage.setItem(storageKey, newContent);
                };

                // Збірка форми
                form.appendChild(textarea);
                form.appendChild(saveButton);
                
                // Заміщення вмісту елемента формою
                element.innerHTML = '';
                element.appendChild(form);
            }, { once: false }); // Можливість повторного кліку
        });
    });
</script>
</body>
</html>
