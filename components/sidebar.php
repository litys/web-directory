<?php 
    $sidebar_text = DB::get_component('sidebar_text')[0]['html'];
    $swiper = DB::get_component('sidebar_swiper');
    $second_ad = DB::get_component('ad_sidebar')[0]['html'];
?>

<div class="sidebar">
    <p class="info_header">POLECANE</p>

    <script>
        let slider = <?php echo json_encode($swiper); ?>;
        m = 0;
        setInterval(() => { 
            if(m==2) m=0;
            else m++;
            // Nie jest puste
            if(slider[m].img.length) {
                document.getElementById('slider_img').style.opacity = 0;
                document.getElementById('slider_title').style.opacity = 0;
                document.getElementById('slider_description').style.opacity = 0;
                setTimeout( ()=> {
                    document.getElementById('slider_img').src = slider[m].img;
                    document.getElementById('slider_title').innerHTML = slider[m].title;
                    document.getElementById('slider_description').innerHTML = slider[m].description;
                    document.getElementById('slider_img').style.opacity = 1;
                    document.getElementById('slider_title').style.opacity = 1;
                    document.getElementById('slider_description').style.opacity = 1;
                },1000);
            }
        }, 6000);
    </script>

    <div class="single_website sidebar_slideshow">
        <div class="img">
            <img id="slider_img" src="<?php echo $swiper[0]['img'] ?>" alt="<?php echo $swiper[0]['title'] ?>">
        </div>
        <div class="text">
            <h2 id="slider_title"><?php echo $swiper[0]['title'] ?></h2>
            <p id="slider_description"><?php echo $swiper[0]['description'] ?></p>
        </div>
    </div>

    <div class="second_add">
        <?php echo $second_ad ?>
    </div>

    <p class="info_header">Sprawdź także</p>

    <div class="promo_links">
        <?php echo $sidebar_text; ?>
    </div>

</div>