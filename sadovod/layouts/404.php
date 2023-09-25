<?php
get_header();
?>

<main id="error" class="site-error not-empty">
    <section id="404">
        <div class="page-404"></div>
        <div class="container">
            <h1 class="page-404-main-title">Такой страницы не существует</h1>
            <h3 class="page-404-subtitle">Возможно она устарела, была удалена
                или был введен неверный адрес в адресной строке</h3>
            <button class="page-404-button"><a class="page-404-link" href="https://test.sadovod-oleg.ru/">Перейти на
                    главную</a></button>
            <img class="page-404-background" data-src="<?php echo get_template_directory_uri(); ?>/assets/img/error404.png"
                src="<?php echo get_template_directory_uri(); ?>/assets/img/error404.png" alt="404">
        </div>
    </section>
</main><!-- #main -->

<?php
get_footer();