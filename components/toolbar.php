<?php
$logo_url = DB::get_component('head')[0]['logo'];
?>
<div class="toolbar">
    <div class="container inner">
        <div>
            <a href="/">
                <img class="logo" src="<?php echo $logo_url?>" alt="Logo">
            </a>
        </div>
        <div class="menu">
            <a href="/">Strona główna</a>
            <a href="/strony">Wpisy</a>
            <a href="/tagi">Tagi</a>
            <a href="/polecamy">Polecamy</a>
            <a href="/regulamin">Regulamin</a>
            <a href="/kontakt">Kontakt</a>
        </div>
        <div class="menu_mobile_icon" onclick="open_mobile()">
            <div class="line"></div>
            <div class="line"></div>
            <div class="line"></div>
        </div>
    </div>
</div>

<div id="menu" class="mobile_menu">
    <div class="close_menu" onclick="open_mobile()">
        <div class="line"></div>
        <div class="line"></div>
    </div>
    <div class="menu">
        <a href="/">Strona główna</a>
        <a href="/strony">Wpisy</a>
        <a href="/tagi">Tagi</a>
        <a href="/polecamy">Polecamy</a>
        <a href="/regulamin">Regulamin</a>
        <a href="/kontakt">Kontakt</a>
    </div>
</div>

<script>
    var menu_closed = true;
    function open_mobile(){
        if(menu_closed)
            document.getElementById('menu').className="active_menu";
        else
            document.getElementById('menu').className="mobile_menu";
        menu_closed=!menu_closed;
    }
</script>