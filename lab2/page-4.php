<?php
// Текстові дані
$x = "Page 4";
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
                
            <div class="b3 btext">
                <ul>
                    <li>One</li>
                    <li>Two</li>
                    <li>Three</li>
                </ul>
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
</body>
</html>
