<?php 
    $alert = '';

   session_start();
   if(!$_SESSION['logged']) header('Location: login');
   if(isset($_GET['logout'])) {
       if($_GET['logout']=='true') {
            session_unset(); 
            session_destroy();
            header('Location: /');
       }
   }

   // Routing w pannelu
   if(isset($_GET['c'])) $panle_page = $_GET['c'];
   else $panle_page = 'home';


   // Przesylanie danych do zmiany
   if(isset($_POST['type'])){
        // Ustawienia główne
        if($_POST['type']=='head'){

            // Edycja nazwy witryny
            DB::query('UPDATE settings SET data=:data WHERE name="website_title"',["data"=>$_POST['website_title']]);
            // Edycja opisu witryny
            DB::query('UPDATE settings SET data=:data WHERE name="website_description"',["data"=>$_POST['website_description']]);
            // Edycja linku do ikony (favicon)
            DB::query('UPDATE settings SET data=:data WHERE name="website_icon"',["data"=>$_POST['website_icon']]);
            
            // Edycja 'moderator mode'
            if(isset($_POST['moderator_mode']))
                DB::query('UPDATE settings SET data=1 WHERE name="moderator_mode"');
            else
            DB::query('UPDATE settings SET data=0 WHERE name="moderator_mode"');

            // Edycja slidera (URL zdjęcia)
            $tmp = [
                [
                    "img"=>$_POST['img']
                ]
            ];
            $enc_img = json_encode($tmp);
            DB::query('UPDATE components SET data=:data WHERE name="home_slider"',["data"=>$enc_img]);
            

            // Edycja url loga oraz text stopki
            $tmp = [
                [
                    "logo"=>$_POST['logo'],
                    "footer"=>$_POST['footer']
                ]
            ];
            $enc = json_encode($tmp);
            DB::query('UPDATE components SET data=:data WHERE name="head"',["data"=>$enc]);
            

            $alert = 'Zaktualizowano';
        }

        // Edycja ustawień postów
        else if($_POST['type']=='posts'){
            // Aktualizowanie ilości postów / tagów na stronie
            DB::query('UPDATE settings SET data=:pages WHERE name="posts_pagination"',["pages"=>$_POST['per_page']]);

            // Aktualizowanie SMS GATE
            if(isset($_POST['sms_gate']))
                DB::query('UPDATE settings SET data=1 WHERE name="sms_gate"');
            else
            DB::query('UPDATE settings SET data=0 WHERE name="sms_gate"');

            // Aktualizacja Tekstu SMS GATE
            DB::query('UPDATE settings SET data=:data WHERE name="sms_gate_text"',['data'=>$_POST['sms_text']]);
        
            // Aktualizacja polecanych wpisów
            if($_POST['recommended_id'] > 0) {
                if(isset($_POST['recommended_check']))
                    DB::query('UPDATE posts SET recommended=1 WHERE id=:id',["id"=>$_POST['recommended_id']]);
                else
                    DB::query('UPDATE posts SET recommended=0 WHERE id=:id',["id"=>$_POST['recommended_id']]);
            }
            $alert = 'Zaktualizowano';
        }

        // Edycja regulaminu
        else if($_POST['type']=='rules'){
            $tmp = [
                [
                    "text"=>$_POST['rules_text']
                ]
            ];
            $enc_img = json_encode($tmp);
            DB::query('UPDATE components SET data=:data WHERE name="rules"',["data"=>$enc_img]);
            $alert = 'Zaktualizowano';
        }

        // Edycja reklam
        else if($_POST['type']=='ads'){
            // Edycja reklamy głównej
            $tmp = [
                [
                    "html"=>$_POST['ad_home']
                ]
            ];
            $enc_img = json_encode($tmp);
            DB::query('UPDATE components SET data=:data WHERE name="ad_home"',["data"=>$enc_img]);
            // Edycja reklamy na sidebarze
            $tmp = [
                [
                    "html"=>$_POST['ad_sidebar']
                ]
            ];
            $enc_img = json_encode($tmp);
            DB::query('UPDATE components SET data=:data WHERE name="ad_sidebar"',["data"=>$enc_img]);
            $alert = 'Zaktualizowano';
        }

        // Edycja sidebara
        else if($_POST['type']=='sidebar'){
            // Edytowanie swipera -  slider w sidebarze
            $tmp = [
                [
                    "img"=>$_POST['swiper1_img'],
                    "title"=>$_POST['swiper1_title'],
                    "description"=>$_POST['swiper1_description']
                ],
                [
                    "img"=>$_POST['swiper2_img'],
                    "title"=>$_POST['swiper2_title'],
                    "description"=>$_POST['swiper2_description']
                ],
                [
                    "img"=>$_POST['swiper3_img'],
                    "title"=>$_POST['swiper3_title'],
                    "description"=>$_POST['swiper3_description']
                ],
            ];
            $enc = json_encode($tmp);
            DB::query('UPDATE components SET data=:data WHERE name="sidebar_swiper"',["data"=>$enc]);
            
            // Edycja tekstu w sidebarze
            $tmp = [
                [
                    "html"=>$_POST['sidebar_text']
                ]
            ];
            $enc = json_encode($tmp);
            DB::query('UPDATE components SET data=:data WHERE name="sidebar_text"',["data"=>$enc]);
            $alert = 'Zaktualizowano';
        }

        // Edycja kodów
        else if($_POST['type']=='codes'){
            // Rozdzielanie kodów

            $codes_list = explode(PHP_EOL, $_POST['list_codes']);

            foreach($codes_list as $code) :

                DB::query('INSERT INTO codes (code,used) VALUES ("'.$code.'",0)');

            endforeach;

            $alert = 'Zaktualizowano';
        }
   }

   // Edycja postów
   if($panle_page == 'edit_post') :
        // Jeżeli usuwamy posta
        if(isset($_GET['delete'])) :
            // Jeżeli zostało potwiedzone
            if(isset($_GET['confirmed'])) :
                DB::query('DELETE FROM posts WHERE id=:id',['id'=>$_GET['delete']]);
                $alert = 'Usunięto post';
            endif;
        endif; 
        // Jezęli usuwamy taga
        if(isset($_GET['tag_delete'])) :
            // Jeżeli zostało potwierdzone
            if(isset($_GET['confirmed'])) :
                // Rozdzielanie tagów
                $data = DB::query('SELECT * FROM posts WHERE id=:id',["id"=>$_GET['edit']])[0];
                $de_data = json_decode($data['keywords']);
                $tmp_array = [];
                foreach($de_data as $id_tag) :
                    if($id_tag != $_GET['tag_delete']) array_push($tmp_array,$id_tag);
                endforeach;
                $tmp_array = json_encode($tmp_array);

                // Wysylanie na serwer zmienionych tagow
                DB::query('UPDATE posts SET keywords=:keywords WHERE id=:id',['keywords'=>$tmp_array,'id'=>$_GET['edit']]);

                $alert = 'Usunięto tag z postu';
            endif;
        endif;
        // Edycja posta
        if(isset($_GET['edit'])) :
            if(isset($_GET['confirmed']) && !isset($_GET['tag_delete'])) :
                DB::query('UPDATE posts SET title=:title, description=:description WHERE id=:id',["title"=>$_POST['title'],"description"=>$_POST['description'],"id"=>$_GET['edit']]);
                $alert = 'Zaktualizowano post';
            endif;
        endif;
        // Akceptacja posta
        if(isset($_GET['accept'])) :
            DB::query('UPDATE posts SET accepted=1 WHERE id=:id',['id'=>$_GET['accept']]);
            $alert = 'Zaakceptowano post';
        endif;
   endif;

   // Aktualizacja sitemap
   if($panle_page == 'sitemap') :
        sitemap_generator();
        $alert = 'Zaktualizowano';
   endif;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel - <?php echo DB::get_settings('website_title') ?></title>
    <link rel="icon" type="image/png" href="<?php echo DB::get_settings('website_icon') ?>"/>
    <link rel="stylesheet" href="/css/global.min.css">
</head>
<body>
<div class="content">
    
    <div class="toolbar">
        <div class="container inner">
            <?php $logo_url = DB::get_component('head')[0]['logo']; ?>
            <div>
                <a href="/">
                    <img class="logo" src="<?php echo $logo_url?>" alt="Logo">
                </a>
            </div>
            <div class="menu">
                <a href="/">Strona główna</a>
                <a href="?logout=true">Wyloguj</a>
            </div>
        </div>
    </div>

    <div class="container dashboard">

        <div class="dash_menu">
            <a href="?c=home">Panel</a>
            
            <a href="?c=head">Ustawienia główne</a>
            <a href="?c=all_posts">Wszystkie wpisy</a>
            <a href="?c=sidebar">Edytuj sidebar</a>
            <a href="?c=posts">Ustawienia postów</a>
            <a href="?c=rules">Regulamin</a>
            <a href="?c=ads">Reklamy</a>
            <a href="?c=codes">Kody</a>
            <a href="?c=sitemap">Aktualizuj sitemap</a>
        </div>

        <?php if(strlen($alert)) : ?>
            <div class="alert"><?php echo $alert; ?></div>
        <?php endif; ?>

        <?php if($panle_page=='home') : ?>
            <?php 
                // Pobieranie wpisów które czekają na akceptacje
                $not_accepted = DB::query('SELECT * FROM posts WHERE accepted=0');
            ?>
            <div class="home">

            <?php foreach($not_accepted as $post) : ?>
                <div class="website_not_accepted">
                    <div class="website_data">
                        <div class="website_img">
                            <img src="<?php echo $post['img']?>" alt="<?php echo $post['title']?>">
                        </div>
                        <div class="website_info">
                            <h2>Tagi</h2>
                            <?php 
                                $de_keywords = json_decode($post['keywords']);
                                foreach($de_keywords as $keyword) :
                                    $content = DB::query('SELECT * FROM tags WHERE id=:keyword',["keyword"=>$keyword])[0];
                                    ?>
                                        <a href="/tag/<?php echo $content['slug'] ?>" title="<?php echo $content['name'] ?>"><?php echo $content['name'] ?></a>
                                    <?php
                                endforeach;
                            ?>
                            <h2>ID w bazie</h2>
                            <?php echo $post['id']; ?>
                        </div>
                    </div>
                    <div class="text">
                        <h2><?php echo $post['title']; ?></h2>
                        <p>
                            <?php echo $post['description']; ?>
                        </p>
                    </div>
                    <div class="info_btn">
                        <a href="?c=edit_post&accept=<?php echo $post['id']; ?>" class="w_edit">
                            <img src="assets/images/check.svg" alt="Zaakceptuj">
                        </a>
                        <a href="strona/<?php echo $post['id'].'/'.$post['slug'] ?>" class="w_edit">
                            <img src="assets/images/eye.svg" alt="Zobacz">
                        </a>
                        <a href="?c=edit_post&edit=<?php echo $post['id']; ?>" class="w_edit">
                            <img src="assets/images/edit.svg" alt="Edytuj">
                        </a>
                        <a href="?c=edit_post&delete=<?php echo $post['id']; ?>" class="w_edit">
                            <img src="assets/images/trash.svg" alt="Usuń">
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>

            </div>
        <?php endif; ?>
        
        <?php if($panle_page=='edit_post') : // Pełna obsługa edycji wpisów ?>
            <?php 
                // Jeżeli usuwamy
                if(isset($_GET['delete'])) :
                    // Jeżeli zostało potwiedzone
                    if(isset($_GET['confirmed'])) :
                        echo '<a href="?c=home">Powrót</a>';
                    else :
                        echo '<h2>Czy napewno chcesz usunąc post z numerem: '.$_GET['delete'].'?</h2>';
                        echo '<a href="?c=edit_post&delete='.$_GET['delete'].'&confirmed=true">Potwiedzam</a>';
                    endif;
                endif; 

                // Zaakceptowano posta (zmionono status w accepted)
                if(isset($_GET['accept'])):
                    echo '<a href="?c=home">Powrót</a>';
                endif;
                if(isset($_GET['edit'])):
                    // Pobieranie postu
                    $data = DB::query('SELECT * FROM posts WHERE id=:id',["id"=>$_GET['edit']])[0];

                    // Jeżeli już usunelismy tag z postu i zostało potwierdzone
                    if(isset($_GET['confirmed'])) :
                        echo '<a href="?c=home">Powrót</a>';
                    // Jeżeli usuwamy tag z postu, wyswietlamy potwierdzenie
                    elseif(isset($_GET['tag_delete'])) :
                        echo '<h2>Czy napewno chcesz usunąc tag z numerem: '.$_GET['tag_delete'].'?</h2>';
                        echo '<a href="?c=edit_post&edit='.$_GET['edit'].'&tag_delete='.$_GET['tag_delete'].'&confirmed=true">Potwiedzam</a>';
                    // Wyswietlamy pełną edytcje postu
                    else :
                        ?>
                        <form action="?c=edit_post&edit=<?php echo $_GET['edit'] ?>&confirmed=true" method="POST" class="edit_posts">
                            <h4>Tagi (kliknij by usunąć)</h4>
                            <?php
                            // Pobieranie tagów
                            $de_data = json_decode($data['keywords']);
                            foreach($de_data as $keyword) :
                                $content = DB::query('SELECT * FROM tags WHERE id=:keyword',["keyword"=>$keyword])[0];
                                ?>
                                    <a href="?c=edit_post&edit=<?php echo $data['id'] ?>&tag_delete=<?php echo $content['id'] ?>"><?php echo $content['name'] ?></a>
                                <?php
                            endforeach;
                            ?>
                            <h4>Tytuł witryny</h4>
                            <input type="text" name="title" value="<?php echo $data['title'] ?>">
                            <h4>Opis wytryny</h4>
                            <textarea name="description"><?php echo $data['description'] ?></textarea>
                            <h4>Link do strony</h4>
                            <a href="<?php echo $data['url'] ?>"><?php echo $data['url'] ?></a>
                            <br>
                            <input type="submit" value="Zapisz">
                        </form>
                        <?php

                    endif;
                endif;
            ?>
        <?php endif; ?>

        <?php if($panle_page=='head') : ?>
            <?php
                $website_title = DB::get_settings('website_title');
                $website_description = DB::get_settings('website_description');
                $website_icon = DB::get_settings('website_icon');
                $moderator_mode = DB::get_settings('moderator_mode');
                $slider = DB::get_component('home_slider')[0]['img'];
                $head = DB::get_component('head')[0];
            ?>
            <form action="" method="POST" class="head">
                <input type="text" name="type" value="head" style="display: none">
                <h4>Nazwa witryny</h4>
                <input type="text" name="website_title" placeholder="Tytuł" value="<?php echo $website_title ?>">
                    
                <h4>Opis strony (głównej)</h4>
                <textarea name="website_description"><?php echo $website_description ?></textarea>
                
                <h4>Link do ikonki witryny (favicon)</h4>
                <input type="text" name="website_icon" placeholder="URL do ikonki" value="<?php echo $website_icon ?>">

                <div class="moderator_mode">
                    <input type="checkbox" id="moderator_mode" name="moderator_mode" <?php if($moderator_mode) echo 'checked'; ?> >
                    <label for="moderator_mode">Nowo dodawane strony wymagają akceptacji moderatora</label>
                </div>

                <h4>Link do zdjęcia na sliderze (strona główna)</h4>
                <input type="text" name="img" placeholder="URL do zdjęcia" value="<?php echo $slider ?>">
                
                <h4>Link do loga</h4>
                <input type="text" name="logo" placeholder="URL do zdjęcia" value="<?php echo $head['logo'] ?>">
                <h4>Stopka - kod HTML</h4>
                <textarea name="footer"><?php echo $head['footer']; ?></textarea>
                <input type="submit" value="Zapisz">
            </form>
        <?php endif; ?>

        <?php if($panle_page=='all_posts') : ?>
            <?php
                $posts = DB::query('SELECT * FROM posts ORDER BY id DESC');
            ?>
            <table class="all_posts">
                <tr>
                    <th>ID</th>
                    <th>Tytuł</th>
                    <th>Url</th>
                    <th>Średnia ocena</th>
                    <th>Polecany</th>
                    <th>Email</th>
                    <th>Zaakceptowano</th>
                </tr>
                <?php foreach($posts as $post) : ?>
                    <tr onclick="location.replace('?c=edit_post&edit=<?php echo $post['id']; ?>')">
                        <td><?php echo $post['id']; ?></td>
                        <td><?php echo $post['title']; ?></td>
                        <td><?php echo $post['url']; ?></td>
                        <td>
                            <?php 
                                echo round($post['ratings'] / $post['votes'],2); 
                            ?>
                        </td>
                        <td><?php echo $post['recommended'] ? 'TAK':'NIE' ?></td>
                        <td><?php echo $post['email']; ?></td>
                        <td><?php echo $post['accepted'] ? 'TAK':'NIE' ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

        <?php if($panle_page=='posts') : ?>
            <?php
                $per_page = DB::get_settings('posts_pagination');
                $sms_gate = DB::get_settings('sms_gate');
                $sms_gate_text = DB::get_settings('sms_gate_text');

                $recommended_posts = DB::query('SELECT id FROM posts WHERE recommended=1');
            ?>
            <form action="" method="POST" class="posts">
                <input type="text" name="type" value="posts" style="display: none">
                <div>
                    <h4>Jak dużo wyświetlać postów / tagów na stronie</h4>
                    <input type="number" name="per_page" value="<?php echo $per_page ?>">
                </div>
                <div class="sms_pay">
                    <input type="checkbox" id="sms_gate" name="sms_gate" <?php if($sms_gate) echo 'checked'; ?> >
                    <label for="sms_gate">Wymagana opłata sms przy dodawaniu strony</label>
                    <h4>Tekst wyświetlany podczas dodawania strony</h4>
                    <textarea name="sms_text"><?php echo $sms_gate_text ?></textarea>
                </div>
                
                <div class="recommended_post">
                    <h4>Zmiana statusu 'polecany' w poście</h4>
                    <input placeholder="Wpisz ID posta" type="number" name="recommended_id">
                    <input type="checkbox" id="recommended_check" name="recommended_check">
                    <label for="recommended_check">Zaznacz jeżeli polecany</label>
                </div>

                <div class="list_recommended_posts">
                    <h4>Lista ID postów które są już polecane</h4>
                    <?php 
                        foreach($recommended_posts as $item) :
                            echo $item['id'].' ';
                        endforeach;
                    ?>
                </div>
                
                <input type="submit" value="Zapisz">
            </form>
        <?php endif; ?>

        <?php if($panle_page=='rules') : ?>
            <?php
                $rules_text = DB::get_component('rules')[0]['text'];
            ?>
            <form action="" method="POST" class="rules">
                <input type="text" name="type" value="rules" style="display: none">
                <h4>Regulamin serwisu (Kod HTML)</h4>
                <textarea name="rules_text"><?php echo $rules_text; ?></textarea>
                <input type="submit" value="Zapisz">
            </form>
        <?php endif; ?>

        <?php if($panle_page=='ads') : ?>
            <?php
                $ad_home = DB::get_component('ad_home')[0]['html'];
                $ad_sidebar = DB::get_component('ad_sidebar')[0]['html'];
            ?>
            <form action="" method="POST" class="ads">
                <input type="text" name="type" value="ads" style="display: none">
                <h4>Kod HTML pierwszej reklamy (głównej)</h4>
                <textarea name="ad_home" ><?php echo $ad_home; ?></textarea>
                <h4>Kod HTML drugiej reklamy (na sidebarze - niewidoczna dla telefonów)</h4>
                <textarea name="ad_sidebar"><?php echo $ad_sidebar; ?></textarea>
                <input type="submit" value="Zapisz">
            </form>
        <?php endif; ?>

        <?php if($panle_page=='sidebar') : ?>
            <?php
                $swiper = DB::get_component('sidebar_swiper');
                $text = DB::get_component('sidebar_text')[0]['html'];
            ?>
            <form action="" method="POST" class="sidebar">
                <input type="text" name="type" value="sidebar" style="display: none">
                <h4>Slider w sidebarze</h4>
                <div class="sidebar_slider">
                    <div class="single">
                        <h4>1 Slider</h4>
                        <p>URL zdjęcia</p>
                        <input type="text" name="swiper1_img" value="<?php echo $swiper[0]['img'] ?>">
                        <p>Tytuł</p>
                        <input type="text" name="swiper1_title" value="<?php echo $swiper[0]['title'] ?>">
                        <p>Opis</p>
                        <textarea name="swiper1_description" ><?php echo $swiper[0]['description'] ?></textarea>
                    </div>
                    <div class="single">
                        <h4>2 Slider</h4>
                        <p>URL zdjęcia</p>
                        <input type="text" name="swiper2_img" value="<?php echo $swiper[1]['img'] ?>">
                        <p>Tytuł</p>
                        <input type="text" name="swiper2_title" value="<?php echo $swiper[1]['title'] ?>">
                        <p>Opis</p>
                        <textarea name="swiper2_description" ><?php echo $swiper[1]['description'] ?></textarea>
                    </div>
                    <div class="single">
                        <h4>3 Slider</h4>
                        <p>URL zdjęcia</p>
                        <input type="text" name="swiper3_img" value="<?php echo $swiper[2]['img'] ?>">
                        <p>Tytuł</p>
                        <input type="text" name="swiper3_title" value="<?php echo $swiper[2]['title'] ?>">
                        <p>Opis</p>
                        <textarea name="swiper3_description" ><?php echo $swiper[2]['description'] ?></textarea>
                    </div>

                </div>
                
                
                <h4>Tekst w sidebarze - Kod HTML</h4>
                <textarea name="sidebar_text"><?php echo $text ?></textarea>
                <input type="submit" value="Zapisz">
            </form>
        <?php endif; ?>

        <?php if($panle_page=='codes') : ?>
            <form action="" method="POST" class="codes">
                <input type="text" name="type" value="codes" style="display: none">
                <h4>Wklej kody (Każdy kod w nowej linii)</h4>
                <textarea name="list_codes"></textarea>
                <input type="submit" value="Zapisz">
            </form>

            <div class="list_of_codes">
                <?php 
                    $unused_codes = DB::query('SELECT * FROM codes WHERE used=0');
                    $used_codes = DB::query('SELECT * FROM codes WHERE used=1');
                ?>
                <div class="unused_codes">
                    <h3>Kody nie użyte</h3>
                    <table>
                        <tr>
                            <th>ID</th>
                            <th>Kod</th>
                        </tr>
                        <?php foreach($unused_codes as $unused) :?>
                        <tr>
                            <td><?php echo $unused['id']; ?></td>
                            <td><?php echo $unused['code']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table> 
                </div>
                <div class="used_codes">
                    <h3>Kody użyte</h3>
                    <table>
                        <tr>
                            <th>ID</th>
                            <th>Kod</th>
                        </tr>
                        <?php foreach($used_codes as $unused) :?>
                        <tr>
                            <td><?php echo $unused['id']; ?></td>
                            <td><?php echo $unused['code']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table> 
                </div>
            </div>
        <?php endif; ?>

        <?php if($panle_page=='sitemap') : ?>
            <?php
                // Generowanie linku do sitemapy
                if(isset($_SERVER['HTTPS'])) $ench = 'https://';
                    else $ench = 'http://';
            ?>
            <a href="/sitemap.xml" target="_blank">Podejrzyj sitemap</a>
            <a href="panel?c=home">Powrót</a>
        <?php endif; ?>

    </div>
    
</div>

<?php footer(); ?>
</body>
</html>
