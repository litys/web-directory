<?php 
    $page_id = $request[2];
    // Dziś już oceniono tą stronę
    if(isset($_COOKIE["page".$page_id]) && isset($_GET['rating'])) {
        $alert = 'Dziś już oceniono tę stronę';
    }
    else if(isset($_GET['rating'])){
        $r = $_GET['rating']; // Zmienna pomocnicza
        // Zabezpieczenie - tylko 1-5 mozna oceniac
        if($r == 1 || $r == 2 || $r == 3 || $r == 4 || $r == 5){
            $cookie_name = "page".$page_id;
            $cookie_value = "True";
            setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 dzień
            
            $actual_rating = $post['ratings'] + $_GET['rating'];
            $actual_votes = $post['votes'] + 1;

            DB::query('UPDATE posts SET ratings=:rating, votes=:vote WHERE id=:id',["rating"=>$actual_rating,"vote"=>$actual_votes,"id"=>$page_id]);
        }
        else {
            $alert = 'Jeżeli chcesz ocenić niestandardowo stronę, napisz do nas w kontakcie';
        }

        $alert = 'Oceniono stronę';
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $post['title'] ?> - <?php echo DB::get_settings('website_title') ?></title>
    <link rel="icon" type="image/png" href="<?php echo DB::get_settings('website_icon') ?>"/>
    <link rel="stylesheet" href="/css/global.min.css">
</head>
<body>
<div class="content">

    <?php toolbar(); ?>

    <div class="container posts">
        <div class="two_column_section">
            <div class="info">
                
                <div class="text_region">
                    <h2><a href="<?php echo $post['url']; ?>" title="<?php echo $post['title'] ?>"><?php echo $post['title'] ?></a></h2>
                    <?php if(isset($alert)) : ?>
                        <div class="stars">
                            <div class="alert">
                                <?php echo $alert ?>
                            </div>
                        </div>
                    <?php else : ?>
                    <div class="stars">
                        <span id="s_1" onmouseover="over(1)" onmouseout="reset()" onclick="pick(1)">
                            <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="star" class="svg-inline--fa fa-star fa-w-18" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z"></path></svg>
                        </span>
                        <span id="s_2" onmouseover="over(2)" onmouseout="reset()" onclick="pick(2)">
                            <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="star" class="svg-inline--fa fa-star fa-w-18" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z"></path></svg>
                        </span>
                        <span id="s_3" onmouseover="over(3)" onmouseout="reset()" onclick="pick(3)">
                            <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="star" class="svg-inline--fa fa-star fa-w-18" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z"></path></svg>
                        </span>
                        <span id="s_4" onmouseover="over(4)" onmouseout="reset()" onclick="pick(4)">
                            <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="star" class="svg-inline--fa fa-star fa-w-18" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z"></path></svg>
                        </span>
                        <span id="s_5" onmouseover="over(5)" onmouseout="reset()" onclick="pick(5)">
                            <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="star" class="svg-inline--fa fa-star fa-w-18" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z"></path></svg>
                        </span>
                        <?php 
                            $stars = $post['ratings'] / $post['votes'];
                            $stars = intval($stars);
                            for($i=1;$i<=$stars;$i++) :
                                ?>
                                <script>
                                    document.getElementById('s_<?php echo $i ?>').className = 'active';
                                </script>
                                <?php
                            endfor;
                        ?>
                        <script>
                            function over(n){
                                for(let i=1;i<=n;i++){
                                    document.getElementById('s_'+i).style.color = 'yellow';
                                }
                            }
                            function reset(){
                                for(let i=1;i<=5;i++){
                                    document.getElementById('s_'+i).style.color = '';
                                }
                            }
                            function pick(n){
                                window.location.href = "?rating="+n;
                            }
                        </script>
                    </div>
                    <?php endif; ?>
                    <p class="description">
                        <?php echo $post['description']; ?>
                    </p>
                </div>

                
                <div class="more">
                    <table style="width:100%">
                        <tr>
                            <td>Numer ID:</td>
                            <td><?php echo $post['id']; ?></td>
                        </tr>
                        <tr>
                            <td>Adres www:</td>
                            <td><a href="<?php echo $post['url']; ?>" title="<?php echo $post['title'] ?>"><?php echo $post['url'] ?></a></td>
                        </tr>
                        <tr>
                            <td>Słowa kluczowe:</td>
                            <td>
                                <?php
                                    $de_keywords = json_decode($post['keywords']);
                                    foreach($de_keywords as $keyword) :
                                        $content = DB::query('SELECT * FROM tags WHERE id=:keyword',["keyword"=>$keyword])[0];
                                        ?>
                                            <a href="/tag/<?php echo $content['slug'] ?>" title="<?php echo $content['name'] ?>"><?php echo $content['name'] ?></a>
                                        <?php
                                    endforeach;
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>IP strony:</td>
                            <td>
                                <?php 
                                // Usuwanie http://
                                $domain = $post['url'];
                                $domain = preg_replace( "#^[^:/.]*[:/]+#i", "", $domain );
				                $domain = explode('/',$domain);
                                $ip = gethostbyname($domain[0]);
                                echo $ip;
                                ?>
                            </td>
                        </tr>
                    </table> 
                </div>


                <div class="copy_link">
                    <p class="info_header">Podlinkuj stronę:</p>
                    <hr>
                    <?php 
                        if(isset($_SERVER['HTTPS'])) $s_link = 'https://';
                        else $s_link = 'http://';
                        $s_link .= $_SERVER['HTTP_HOST'];
                    ?>
                    <textarea><a href="<?php echo $s_link; ?>/strona/<?php echo $post['id']; ?>/<?php echo $post['slug'] ?>" target="_blank" title="<?php echo $post['title']; ?>" ><strong><?php echo $post['title']; ?></strong></a></textarea>
                </div>

                
                <div class="more_posts">
                    <p class="info_header">Zobacz również
                    <?php
                        $posts = DB::query('SELECT * FROM posts AS r1 JOIN
                            (SELECT CEIL(RAND() *
                                (SELECT MAX(id)
                                    FROM posts)) AS id)
                              AS r2
                       WHERE r1.id >= r2.id
                       ORDER BY r1.id ASC
                       LIMIT 3
                      ');
                    ?>
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
                    </div>
                </div>

            </div>

            <?php sidebar(); ?>
        </div>
    </div>
</div>

<?php footer(); ?>
</body>
</html>
