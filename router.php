<?php
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

$request = explode('/',$path);

if($request[1]=='') include_once('pages/index.php');
else if($request[1]=='dodaj') include_once('pages/add_post.php');
else if($request[1]=='strony') include_once('pages/posts.php');
else if($request[1]=='regulamin') include_once('pages/rules.php');
else if($request[1]=='kontakt') include_once('pages/contact.php');
else if($request[1]=='tagi') include_once('pages/tags.php');
else if($request[1]=='tag') {
    if(isset($request[2])){
        $tag = $request[2];
        include_once('pages/tag.php');
    }
    else include_once('pages/404.php');
}
else if($request[1]=='polecamy') include_once('pages/recommended.php');
else if($request[1]=='db_run') include_once('data_creator.php');
// Jeżeli wchodzimy do wpisów
else if(isset($request[2]) && isset($request[3])){
    if($request[1]=='strona') {
        $post = DB::query('SELECT * FROM posts WHERE id=:id AND slug=:slug',['id'=>$request[2],'slug'=>$request[3]]); 
        if(count($post)) {
            $post = $post[0];
            include_once('pages/post.php');
        }
        else include_once('pages/404.php');
    }
}
else if($request[1]=='panel') include_once('pages/panel.php');
else if($request[1]=='login') include_once('pages/login.php');
else if($request[1]=='404') include_once('pages/404.php');
else header('Location: /404');