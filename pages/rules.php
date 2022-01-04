<?php 
    $text = DB::get_component('rules')[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regulamin - <?php echo DB::get_settings('website_title') ?></title>
    <link rel="icon" type="image/png" href="<?php echo DB::get_settings('website_icon') ?>"/>
    <link rel="stylesheet" href="/css/global.min.css">
</head>
<body>
    <div class="content">
    
        <?php toolbar(); ?>

        <div class="mini_slider" title="<?php echo DB::get_settings('website_title') ?>" style="background-image: url('<?php echo DB::get_component('home_slider')[0]['img']; ?>')">
            REGULAMIN
        </div>
        
        <div class="container">
            <div class="rules_page">
                <?php echo $text['text']; ?>
            </div>
        </div>
            
    </div>
    
    <?php footer(); ?>
</body>
</html>
