<?php 
    // Czy blokować formularz
    $block_form = false;

    // Sprawdzanie czy funkcja SMS_GATE jest włączona
    $sms_gate_status = DB::get_settings('sms_gate');
    $sms_gate_text = DB::get_settings('sms_gate_text');

    $website_url = '';
    $website_title = '';
    $website_keywords = '';
    $website_description = '';
    $website_email = '';
    if(isset($_POST["website"])) $website_url = $_POST["website"];
    if(isset($_POST["title"])) $website_title = $_POST["title"];
    if(isset($_POST["keywords"])) $website_keywords = $_POST["keywords"];
    if(isset($_POST["description"])) $website_description = $_POST["description"];
    if(isset($_POST["email"])) $website_email = $_POST["email"];

    // Sprawdzanie czy strona już nie widnieje w bazie
    $website_doesnt_exist = true;
    if($website_url){
        // Usuwanie http:// oraz https://
        $pure_url = preg_replace( "#^[^:/.]*[:/]+#i", "", $_POST["website"] );
        $similar_posts = DB::query('SELECT * FROM posts WHERE url LIKE :url',['url'=>'%'.$pure_url.'%']);
        if(count($similar_posts)){
            $alert = 'Taka strona już jest dodana';
            $website_doesnt_exist = false;
        }

        // Sprawdzanie czy URL jest prawidłowy
        // Czy użytkownik dodał na początku url https:// lub http
        // Jeżeli nie dodał dodaj http://
        if(!preg_match("@^https?://@", $website_url)){
            $website_url = 'http://'.$website_url;
        }
        if (filter_var($website_url, FILTER_VALIDATE_URL) === FALSE) {
            $alert = 'Nieprawidłowy link';
            $website_doesnt_exist = false;
        }
    }
    // Sprawdzanie czy poprawnie przepisano kod (kod generowany losowo)
    if(isset($_COOKIE["code"]) && isset($_POST["website"]) && isset($_POST["title"]) && isset($_POST["description"]) && isset($_POST["keywords"]) && isset($_POST["email"])) {
        if(isset($_POST['re_code'])){
            if($_POST['re_code']!=$_COOKIE['code']){
                $alert = 'Nie przepisano poprawnie kodu';
                $website_doesnt_exist = false;
            }
        }
        else {
            $alert = 'Nie przepisano poprawnie kodu';
            $website_doesnt_exist = false;
        }

        // Jeżeli mail jest poprawny
        if(!email_validation($_POST['email']) && $website_doesnt_exist) {
            $alert = 'Niepoprawny format e-maila';
            $website_doesnt_exist = false;
        }
    }
    // Sprawdzanie czy wypelniono formularz - jezeli tak rozpoczynam dodawnie
    if($website_doesnt_exist && isset($_POST["website"]) && isset($_POST["title"]) && isset($_POST["description"]) && isset($_POST["keywords"]) && isset($_POST["email"])){


        $website_url = $_POST["website"];
        $website_title = $_POST["title"];
        $website_description = $_POST["description"];
        $keywords = $_POST["keywords"];
        $email = $_POST["email"];

        // Jezeli moderacja jest włączona nowe wpisy mają iść do moderacji 
        if(DB::get_settings('moderator_mode')) $mod_accepted = 0;
        else $mod_accepted = 1;

        // Czy użytkownik dodał na początku url https:// lub http
        // Jeżeli nie dodał dodaj http://
        if(!preg_match("@^https?://@", $website_url)){
            $website_url = 'http://'.$website_url;
        }

        // Jeżeli SMS GATE jest włączone - Sprawdzanie poprawności kodu
        if($sms_gate_status) {
            if(isset($_POST["code"])) {
                $code = $_POST["code"];
                
                // Sprawdzanie poprawności kodu
                $valid = DB::query('SELECT * FROM codes WHERE code=:code',['code'=>$code]);
                // Jeżeli kod widnieje w bazie
                if(count($valid)){
                    // Sprawdź czy nie został użyty
                    if(!$valid[0]['used']){
                        // Kod się zgadza oraz jeszcze nie został wykorzystany
                        try {
                            add_new_post([
                                "title"=>$website_title,
                                "url"=>$website_url,
                                "ratings"=>5,
                                "votes"=>1,
                                "description"=>$website_description,
                                "keywords"=>$keywords,
                                "recommended"=>0,
                                "accepted"=>$mod_accepted,
                                "email"=>$email
                            ]);
    
                            // Zmiana kodu na użyty
                            DB::query('UPDATE codes SET used=1 WHERE id='.$valid[0]['id']);
                            $alert = 'Strona została dodana';
                            $block_form = true;
                        }
                        catch (Exception $e){
                            $alert = 'Nie można dodać strony, proszę zgłosić się do administracji. Zachowaj kod, nie został on zużyty!';
                        }
                    }
                    else {
                        $alert = 'Podany kod został już użyty wcześniej';
                    }
                }
                else {
                    $alert = 'Kod jest nieprawidłowy';
                }
            }
            else {
                $alert = 'Proszę podać kod';
            }
        }
        // Jeżeli SMS GATE jest wyłączony
        else {
            try {
                add_new_post([
                    "title"=>$website_title,
                    "url"=>$website_url,
                    "ratings"=>5,
                    "votes"=>1,
                    "description"=>$website_description,
                    "keywords"=>$keywords,
                    "recommended"=>0,
                    "accepted"=>$mod_accepted,
                    "email"=>$email
                ]);

                $alert = 'Strona została dodana';
                $block_form = true;
            }
            catch (Exception $e){
                $alert = 'Nie można dodać strony, proszę zgłosić się do administracji. Zachowaj kod, nie został on zużyty!';
            }
        }

    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dodaj stronę - <?php echo DB::get_settings('website_title') ?></title>
    <link rel="icon" type="image/png" href="<?php echo DB::get_settings('website_icon') ?>"/>
    <link rel="stylesheet" href="/css/global.min.css">
</head>
<body>
<?php toolbar(); ?>

<div class="add_new">
    
    <?php if(isset($alert)) : ?>
        <div class="alert">
            <?php echo $alert ?>
        </div>
    <?php endif; ?>

    <form action="dodaj" method="post" autocomplete="off">
        <h4>Adres strony: </h4>
        <input required="required" minlength="3" name="website" type="text" <?php if($block_form) echo 'disabled="disabled"'?> <?php if($website_url) echo 'value="'.$website_url.'"'; ?> placeholder="https://przykladowa-strona.pl">
    
        <h4>Tytuł - powinien mieć od 5 do 60 znaków </h4>
        <input required="required" minlength="5" name="title" type="text" <?php if($block_form) echo 'disabled="disabled"'?> <?php if($website_title) echo 'value="'.$website_title.'"'; ?> >
        
        <h4>Opis - powinien mieć od 200 do 1000 znaków </h4>
        <textarea required="required" minlength="200" name="description" <?php if($block_form) echo 'disabled="disabled"'?> ><?php if($website_description) echo $website_description; ?></textarea>

        <h4>Słowa kluczowe powinny mieć 15 - 100 znaków (oddzielać przecinkami [,]) </h4>
        <textarea required="required" minlength="15" name="keywords" <?php if($block_form) echo 'disabled="disabled"'?> ><?php if($website_keywords) echo $website_keywords; ?></textarea>
        
        <h4>Adres e-mail</h4>
        <input required="required" minlength="5" type="email" name="email" <?php if($block_form) echo 'disabled="disabled"'?> <?php if($website_email) echo 'value="'.$website_email.'"'; ?> >
        
        <?php if($sms_gate_status) : ?>
            <h4>Podaj kod</h4>
            <input type="text" name="code">
            <p>
                <?php echo $sms_gate_text; ?>
            </p>
        <?php endif; ?>

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


        <input type="submit" value="DODAJ STRONĘ!"  <?php if($block_form) echo 'disabled'?>>
    </form>
    
</div>
    
    <?php footer(); ?>
</body>
</html>
