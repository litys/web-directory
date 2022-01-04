<?php 
    include_once('config.php');
    $login = $website_login;
    $password = $website_pass;

   session_start();
   if(isset($_SESSION['logged'])) header('Location: panel');
   
   //Logowanie do panelu
   if(isset($_POST['login']) && isset($_POST['password'])){
       if($_POST['login'] == $login && $_POST['password'] == $password) {
            $_SESSION['logged'] = true;
            header('Location: panel');
       }
       else {
           $alert = 'Nieprawidłowe dane';
       }
   }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie - <?php echo DB::get_settings('website_title') ?></title>
    <link rel="icon" type="image/png" href="<?php echo DB::get_settings('website_icon') ?>"/>
    <link rel="stylesheet" href="/css/global.min.css">
</head>
<body>
<div class="content">
    <?php toolbar(); ?>

    <div class="login_container">
        <div class="login_card">
            <form action="" method="POST">
                <?php if(isset($alert)) : ?>
                    <div class="alert">
                        <?php echo $alert ?>
                    </div>
                <?php endif; ?>
                <input type="text" name="login" placeholder="Login">
                <input type="password" name="password" placeholder="Hasło">
                <input type="submit" value="Zaloguj">
            </form>
        </div>
    </div>
    
</div>

<?php footer(); ?>
</body>
</html>
