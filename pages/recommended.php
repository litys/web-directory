<?php 
    // Pobieranie ustawień (ile postów ma zawierać 1 strona)
    $per_page = DB::get_settings('posts_pagination');

    // Paginacja
    $page = 1;
    // Jeżeli uzytkownik wybral strone
    if(isset($_GET['s'])) {
        // Jeżeli jest liczbą
        if(is_numeric($_GET['s'])) $page = $_GET['s'];
        else {
            include_once('404.php');
            exit;
        }
    }

    // Pobieranie ilości postów
    $how_much_posts = DB::query('SELECT COUNT(id) AS items FROM posts WHERE recommended=1');
    $total_items = $how_much_posts[0]['items'];

    // Wyswietlanie paginacji - paginacja open-source autorstwa jasongrimes/php-paginator
    $totalItems = $total_items;
    $itemsPerPage = $per_page;
    $currentPage = $page;
    $urlPattern = '?s=(:num)';

    $paginator = new Paginator($totalItems, $itemsPerPage, $currentPage, $urlPattern);

    // pobieranie danych
    $start_from = $page * $per_page - $per_page;
    $posts = DB::query('SELECT * FROM posts WHERE recommended=1 LIMIT '.$start_from.','.$per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Polecane - <?php echo DB::get_settings('website_title') ?></title>
    <link rel="icon" type="image/png" href="<?php echo DB::get_settings('website_icon') ?>"/>
    <link rel="stylesheet" href="/css/global.min.css">
</head>
<body>
<div class="content">
    <?php toolbar(); ?>

    <div class="container posts">
        <div class="two_column_section">
            <div class="last_websites">
                <?php foreach($posts as $post) : ?>
                    <div class="single_website">
                        <div class="img">
                            <a href="/strona/<?php echo $post['id'].'/'.$post['slug'] ?>" title="<?php echo $post['title']?>">
                                <img src="<?php echo $post['img']?>" alt="<?php echo $post['title']?>">
                            </a>
                        </div>
                        <div class="text">
                            <a href="strona/<?php echo $post['id'].'/'.$post['slug'] ?>" title="<?php echo $post['title']?>">
                                <h2><?php echo $post['title']; ?></h2>
                            </a>
                            <p>
                                <?php echo $post['description']; ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
                

                <?php echo $paginator; ?>


            </div>

            <?php sidebar(); ?>
            
        </div>
    </div>

</div>

<?php footer(); ?>
</body>
</html>
