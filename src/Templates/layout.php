<!doctype html>
<html>
<head>
    <title>Epic morpion</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo SITE_URL ?>favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo SITE_URL ?>favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo SITE_URL ?>favicons/favicon-16x16.png">
    <link rel="manifest" href="<?php echo SITE_URL ?>favicons/manifest.json">
    <meta name="apple-mobile-web-app-title" content="Epic Morpion">
    <meta name="application-name" content="Epic Morpion">
    <meta name="theme-color" content="#e03a3e">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo SITE_URL ?>styles/main.css">
</head>
<body>
<div class="home-container">
    <aside id="aside">
        <header id="header">
            <a href="<?php echo SITE_URL ?>">
                <img class="header-logo" src="<?php echo SITE_URL ?>images/logo.svg" alt="Logo epic morpion">
            </a>
            <a class="header-title" href="<?php echo SITE_URL ?>">
                <h1>Epic Morpion</h1>
            </a>
        </header>

        <footer id="footer">
            <div class="footer-links">
                <a href="https://etu.univ-lyon1.fr/">UCBL1</a>
                -
                <a href="http://liris.cnrs.fr/fabien.duchateau/BDW1">BDW1</a>
            </div>
            <div class="footer-year">
                <span>2017-2018</span>
            </div>
        </footer>
    </aside>
    <main id="main">
        <?php if(isset($content)) echo $content; ?>
    </main>
</div>
</body>
</html>
