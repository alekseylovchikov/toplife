<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8" />
    <title>TopLife</title>
    <!-- scripts -->
    <script src="js/jquery-2.1.4.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/ripples.min.js"></script>
    <script src="js/material.min.js"></script>
    <!-- end scripts -->

    <!-- stylesheets -->
    <link rel="stylesheet" href="css/bootstrap.css" />
    <link rel="stylesheet" href="css/main.css" />
    <!-- end stylesheets -->
</head>
<body>
    <nav class="navbar navbar-default">
        <div class="container">
            <?php if($entry->is_admin()) { ?>
                <a href="logout.php" class="btn btn-primary navbar-btn">Выйти</a>
                <!--<button type="button" class="btn btn-danger navbar-btn">Выйти</button>-->
            <?php } else { ?>
                <button type="button" data-toggle="modal" data-target="#login" class="btn btn-success navbar-btn">Войти</button>
            <?php } ?>
        </div>
    </nav>

    <header class="text-center">
        <img src="img/logo.png" alt="Top Life" title="Top Life" />
    </header>
