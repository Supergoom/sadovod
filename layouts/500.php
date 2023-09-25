<?php
get_header();
?>

<main id="error" class="site-error not-empty">
    <div id="error-post">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <?php outputBreadcrumbs(); ?>
                </div>
                <div class="col-sm-12 error-info">
                    <h1><?= __('Error 500', 'sadovod-dialogs'); ?></h1>
                    <h2><?= __('Something went wrong', 'sadovod-dialogs'); ?></h2>
                    <p><?= __('We can’t handle this request!<br>Try to use search form or return to the main page.', 'sadovod-dialogs'); ?></p>
                    <a class="btn btn-primary" href="/"><?= __('Back to Home', 'sadovod-dialogs'); ?></a>
                </div>
            </div>
        </div>
    </div>
</main><!-- #main -->

<?php
get_footer();
