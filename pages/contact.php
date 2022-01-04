<?php 
$block_form = false;

if(isset($_COOKIE["code"]) && isset($_POST['title']) && isset($_POST['text'])){
    if(isset($_POST['re_code'])){
        if($_POST['re_code']==$_COOKIE['code']){
            // Przepisany kod jest poprawny
            // Jeżeli mail jest poprawny
            if(email_validation($_POST['title'])) {
                $data = [
                    'title'=>'Formularz kontaktowy od: '.$_POST['title'],
                    'text'=>$_POST['text']
                ];
                send_mail($data,admin_mail());
                $alert = 'E-mail został wysłany';
                $block_form = true;
            }
            // Jeżeli mail jest niepoprawny
            else {
                $alert = 'Niepoprawny format e-maila';
            }
        }
        else {
            $alert = 'Nie przepisano poprawnie kodu';
        }
    }
    else {
        $alert = 'Nie przepisano poprawnie kodu';
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontakt - <?php echo DB::get_settings('website_title') ?></title>
    <link rel="icon" type="image/png" href="<?php echo DB::get_settings('website_icon') ?>"/>
    <link rel="stylesheet" href="/css/global.min.css">
</head>
<body>
    <div class="content">
    
        <?php toolbar(); ?>

        <div class="mini_slider" title="<?php echo DB::get_settings('website_title') ?>" style="background-image: url('<?php echo DB::get_component('home_slider')[0]['img']; ?>')">
            KONTAKT
        </div>

        <div class="contact_form">

            <?php if(isset($alert)) : ?>
                <div class="alert">
                    <?php echo $alert; ?>
                </div>
            <?php endif; ?>
        
            <form autocomplete="off" action="" method="post">
                <h4>Mail</h4>
                <input name="title" type="email" placeholder="Podaj twój email" value="<?php if(isset($_POST['title'])) echo $_POST['title']; ?>"  <?php if($block_form) echo 'disabled="disabled"'?> >

                <h4>Treść</h4>
                <textarea name="text" <?php if($block_form) echo 'disabled="disabled"'?>><?php if(isset($_POST['text'])) echo $_POST['text']; ?></textarea>

                <?php 
                    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    $how_much_characters = rand(6, 8);
                    $characters_length = strlen($characters);
                    $code = '';
                    $randomString = '';
                    for ($i = 0; $i < $how_much_characters; $i++) {
                        $code .= $characters[rand(0, $characters_length - 1)];
                    }

                    // Ustawianie cookies dla wygenerowanego kodu
                    $cookie_name = "code";
                    $cookie_value = $code;
                    setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 dzień
                ?>

                <div class="code">
                    <div>
                        <canvas id="code" width="250" height="75" style="border:1px solid #d3d3d3;"></canvas>
                        <script>
                            var c = document.getElementById("code");
                            var ctx = c.getContext("2d");
                            ctx.font = "30px Arial";
                            ctx.strokeText("<?php echo $code; ?>", 10, 50);
                        </script> 
                    </div>
                    <div>
                        <h4>Przepisz kod obok</h4>
                        <input type="text" name="re_code">
                    </div>
                </div>

                <input type="submit" value="Wyślij wiadomość"  <?php if($block_form) echo 'disabled'?>>
            </form>
            
        </div>
            
    </div>
    
    <?php footer(); ?>
</body>
</html>
