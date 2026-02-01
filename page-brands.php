<?php
/**
 * Template Name: All Brands
 *
 * The template for displaying all brands page
 *
 * @package kacosmetics
 */

get_header();

// Get brands for current language (Polylang filters automatically)
$brands = get_terms(array(
    'taxonomy' => 'product_brand',
    'hide_empty' => false,
    'orderby' => 'name',
    'order' => 'ASC',
));
?>

<div id="primary" class="content-area brands-page">
    <main id="main" class="site-main">

        <!-- Page Header -->
        <div class="brands-page-header">
            <h1 class="brands-page-title"><?php esc_html_e('Our Brands', 'kacosmetics'); ?></h1>
            <p class="brands-page-subtitle"><?php esc_html_e('Discover our curated collection of premium beauty brands', 'kacosmetics'); ?></p>
        </div>

        <!-- Brands Grid -->
        <?php if ($brands && !is_wp_error($brands)) : ?>
            <div class="brands-grid-container">
                <div class="brands-grid">
                    <?php foreach ($brands as $brand) :
                        // Build brand link manually with correct "brands" slug
                        // home_url() already includes language prefix, so just add brands/slug
                        $home_url = home_url('/');
                        $brand_link = trailingslashit($home_url) . 'brands/' . $brand->slug . '/';

                        // Get product count
                        $product_count = $brand->count;

                        // Get brand thumbnail (if using a plugin like YITH Brand)
                        $thumbnail_id = get_term_meta($brand->term_id, 'thumbnail_id', true);
                        $brand_image = '';
                        if ($thumbnail_id) {
                            $brand_image = wp_get_attachment_image_url($thumbnail_id, 'medium');
                        }
                    ?>
                        <div class="brand-card">
                            <a href="<?php echo esc_url($brand_link); ?>" class="brand-card-link">
                                <?php if ($brand_image) : ?>
                                    <div class="brand-image-wrapper">
                                        <img src="<?php echo esc_url($brand_image); ?>"
                                             alt="<?php echo esc_attr($brand->name); ?>"
                                             class="brand-image">
                                    </div>
                                <?php else : ?>
                                    <div class="brand-image-wrapper brand-image-placeholder">
                                        <span class="brand-initial"><?php echo esc_html(mb_substr($brand->name, 0, 1)); ?></span>
                                    </div>
                                <?php endif; ?>

                                <div class="brand-info">
                                    <h2 class="brand-name"><?php echo esc_html($brand->name); ?></h2>

                                    <?php if ($brand->description) : ?>
                                        <p class="brand-description"><?php echo esc_html(wp_trim_words($brand->description, 12)); ?></p>
                                    <?php endif; ?>

                                    <div class="brand-meta">
                                        <span class="brand-product-count">
                                            <?php
                                            printf(
                                                _n('%s product', '%s products', $product_count, 'kacosmetics'),
                                                number_format_i18n($product_count)
                                            );
                                            ?>
                                        </span>
                                    </div>

                                    <span class="brand-view-link">
                                        <?php esc_html_e('View Collection', 'kacosmetics'); ?>
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                            <path d="M6 3L11 8L6 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else : ?>
            <div class="no-brands-message">
                <p><?php esc_html_e('No brands found at the moment. Please check back later.', 'kacosmetics'); ?></p>
            </div>
        <?php endif; ?>

    </main>
</div>

<?php
get_footer();
