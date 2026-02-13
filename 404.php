<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package kacosmetics
 */

get_header();
?>

<main id="primary" class="site-main">

    <section class="error-404 not-found">
        <header class="page-header">
            <span class="error-code">404</span>
            <h1 class="page-title"><?php esc_html_e('Page not found', 'kacosmetics'); ?></h1>
        </header>

        <div class="page-content">
            <p><?php esc_html_e('Sorry, the page you are looking for does not exist or has been moved.', 'kacosmetics'); ?></p>

            <div class="error-actions">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">
                    <?php esc_html_e('Go to Homepage', 'kacosmetics'); ?>
                </a>
                <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="btn btn-secondary">
                    <?php esc_html_e('Browse Products', 'kacosmetics'); ?>
                </a>
            </div>

            <div class="error-search">
                <p><?php esc_html_e('Or try searching:', 'kacosmetics'); ?></p>
                <?php get_search_form(); ?>
            </div>
        </div>
    </section>

</main>

<?php
get_footer();
