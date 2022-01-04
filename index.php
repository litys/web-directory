<?php
    // Jeżeli nie zainstalowano katalogu przenies do instalatora
    if(file_exists('install.php')) header('Location: install.php');
    include_once('functions.php');
    include_once('router.php');
?>
