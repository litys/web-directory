<?php 
    $slider = DB::get_component('home_slider')[0]['img'];
    $stats = DB::get_component('stats')[0];
    $stats['pages'] = DB::query('SELECT COUNT(id) AS items FROM posts')[0]['items'];
    $posts = DB::query('SELECT * FROM posts WHERE accepted=1 ORDER BY id DESC LIMIT 15'); 
    $firsth_ad = DB::get_component('ad_home')[0]['html'];

    // Aktualizowanie statystyk
    $tmp = [];
    // Jeżeli wejdzie googlebot
    if(strstr(strtolower($_SERVER['HTTP_USER_AGENT']), "googlebot")) $s_bot = true;
    if(isset($s_bot)) {
        $tmp = [
            [
                'bots'=>$stats['bots']+1,
                'visits'=>$stats['visits']
            ]
        ];
    }
    // Jeżeli wejdzie zwykły użytkownik
    else {
        $tmp = [
            [
                'bots'=>$stats['bots'],
                'visits'=>$stats['visits']+1
            ]
        ];
    }
    $tmp_enc = json_encode($tmp);
    DB::query('UPDATE components SET data=:stats WHERE name="stats"',["stats"=>$tmp_enc]);   
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo DB::get_settings('website_title') ?></title>
    <meta name="description" content="<?php echo DB::get_settings('website_description') ?>">
    <link rel="icon" type="image/png" href="<?php echo DB::get_settings('website_icon') ?>"/>
    <link rel="stylesheet" href="/css/global.min.css">
</head>
<body>

<div class="content">

    <?php toolbar(); ?>

    <div class="home_slider" title="<?php echo DB::get_settings('website_title') ?>" style="background-image: url('<?php echo $slider; ?>')">
        <div>
            <div class="img">
                <img src="<?php echo DB::get_component('head')[0]['logo']; ?>" alt="Logo">
            </div>
            <div class="add_input">
                <form action="dodaj" method="post" autocomplete="off">
                    <input id="website" name="website" type="text" placeholder="PODJ LINK DO STRONY"><input type="submit" value="DODAJ STRONĘ!">
                </form>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="firsth_add">
            <?php echo $firsth_ad; ?>
        </div>
    </div>

    <div class="container display_all_posts">
        <div class="two_column_section">
            <div class="last_websites">
                <p class="info_header">OSTATNIO DODANE STRONY</p>
                <?php foreach($posts as $post) : ?>
                    <div class="single_website">
                        <div class="img">
                            <a href="/strona/<?php echo $post['id'].'/'.$post['slug'] ?>" title="<?php echo $post['title']?>">
                                <img src="<?php echo $post['img']?>" alt="<?php echo $post['title']?>">
                            </a>
                        </div>
                        <div class="text">
                            <a href="/strona/<?php echo $post['id'].'/'.$post['slug'] ?>" title="<?php echo $post['title']?>">
                                <h2><?php echo $post['title']?></h2>
                            </a>
                            <p>
                                <?php echo $post['description']?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="btn_more">
                    <a href="/strony">Przeglądaj więcej!</a>
                </div>
            </div>

            <?php sidebar(); ?>
            
        </div>
    </div>

    <div class="stats">
        <p class="header">STATYSTYKI</p>
        <div class="container info_stats">
            <div class="single_stats">
                <img src="assets/images/pc-smartphone.svg" alt="Stron">
                <p>
                    <?php echo $stats['pages']; ?> stron
                </p>
            </div>
            <div class="single_stats">
                <img src="assets/images/google.svg" alt="Google bot">
                <p>
                    <?php echo $stats['bots']; ?> wizyt Google
                </p>
            </div>
            <div class="single_stats">
                <img src="assets/images/group.svg" alt="Odwiedzin">
                <p>
                    <?php echo $stats['visits']; ?> odwiedzin
                </p>
            </div>
        </div>
    </div>

</div>

<?php footer(); ?>
</body>
</html>
