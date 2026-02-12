<?php
/**
 * The template for displaying product brand archives
 *
 * @package kacosmetics
 */

get_header();
?>
<!-- Brand Archive Styles -->
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/brand-archive-style.css?ver=<?php echo _S_VERSION; ?>">
<?php

// Get current brand from URL
$current_brand = get_queried_object();

// If brand not found by normal WordPress query, try to find it manually
if ( ! $current_brand || is_wp_error( $current_brand ) || ! isset( $current_brand->term_id ) ) {
    // Try to extract brand slug from URL
    $request_uri = $_SERVER['REQUEST_URI'];
    if ( preg_match( '#/brands/([^/]+)/?$#', $request_uri, $matches ) ) {
        $slug = $matches[1];

        // Try to find brand by slug in any language
        $brands = get_terms( array(
            'taxonomy'   => 'product_brand',
            'slug'       => $slug,
            'hide_empty' => false,
            'lang'       => '', // Search in all languages
        ) );

        if ( ! empty( $brands ) && ! is_wp_error( $brands ) ) {
            $current_brand = $brands[0];
            // Set as queried object
            $GLOBALS['wp_query']->queried_object = $current_brand;
            $GLOBALS['wp_query']->queried_object_id = $current_brand->term_id;
        }
    }
}

// If still not found, show 404
if ( ! $current_brand || is_wp_error( $current_brand ) || ! isset( $current_brand->term_id ) ) {
    status_header( 404 );
    get_template_part( '404' );
    get_footer();
    exit;
}

// Get brand thumbnail
$thumbnail_id = get_term_meta($current_brand->term_id, 'thumbnail_id', true);
$brand_image = '';
if ($thumbnail_id) {
    $brand_image = wp_get_attachment_image_url($thumbnail_id, 'large');
}

// Get product count for current language
$count_query = new WP_Query( array(
    'post_type'      => 'product',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'fields'         => 'ids',
    'tax_query'      => array(
        array(
            'taxonomy' => 'product_brand',
            'field'    => 'term_id',
            'terms'    => $current_brand->term_id,
        ),
    ),
) );
$product_count = $count_query->found_posts;
wp_reset_postdata();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">

        <!-- Brand Header -->
        <div class="brand-archive-header">
            <div class="brand-archive-header-inner">
                <div class="brand-archive-logo-wrapper">
                    <?php if ($brand_image) : ?>
                        <img src="<?php echo esc_url($brand_image); ?>"
                             alt="<?php echo esc_attr($current_brand->name); ?>"
                             class="brand-archive-logo">
                    <?php else : ?>
                        <div class="brand-archive-logo-placeholder">
                            <span><?php echo esc_html(mb_substr($current_brand->name, 0, 1)); ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="brand-archive-info">
                    <h1 class="brand-archive-title"><?php echo esc_html($current_brand->name); ?></h1>

                    <?php if ($current_brand->description) : ?>
                        <div class="brand-archive-description">
                            <?php echo wp_kses_post($current_brand->description); ?>
                        </div>
                    <?php endif; ?>

                    <div class="brand-archive-meta">
                        <div class="brand-meta-item">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M3 6H17L16 17H4L3 6Z" stroke="currentColor" stroke-width="1.5"/>
                                <path d="M7 6V4C7 2.89543 7.89543 2 9 2H11C12.1046 2 13 2.89543 13 4V6" stroke="currentColor" stroke-width="1.5"/>
                            </svg>
                            <span>
                                <strong><?php echo number_format_i18n($product_count); ?></strong>
                                <?php echo _n('product', 'products', $product_count, 'kacosmetics'); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="brand-filter-bar">
            <div class="brand-filter-left">
                <span class="filter-label"><?php esc_html_e('Sort by:', 'kacosmetics'); ?></span>
                <select class="filter-select" id="product-sort">
                    <option value="date-desc"><?php esc_html_e('Newest First', 'kacosmetics'); ?></option>
                    <option value="date-asc"><?php esc_html_e('Oldest First', 'kacosmetics'); ?></option>
                    <option value="title-asc"><?php esc_html_e('Name: A-Z', 'kacosmetics'); ?></option>
                    <option value="title-desc"><?php esc_html_e('Name: Z-A', 'kacosmetics'); ?></option>
                    <option value="price-asc"><?php esc_html_e('Price: Low to High', 'kacosmetics'); ?></option>
                    <option value="price-desc"><?php esc_html_e('Price: High to Low', 'kacosmetics'); ?></option>
                </select>
            </div>

            <div class="brand-filter-right">
                <div class="view-toggle">
                    <button class="view-toggle-btn active" data-view="grid">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <rect x="1" y="1" width="6" height="6" stroke="currentColor" stroke-width="1.5"/>
                            <rect x="9" y="1" width="6" height="6" stroke="currentColor" stroke-width="1.5"/>
                            <rect x="1" y="9" width="6" height="6" stroke="currentColor" stroke-width="1.5"/>
                            <rect x="9" y="9" width="6" height="6" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                    </button>
                    <button class="view-toggle-btn" data-view="list">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <line x1="1" y1="3" x2="15" y2="3" stroke="currentColor" stroke-width="1.5"/>
                            <line x1="1" y1="8" x2="15" y2="8" stroke="currentColor" stroke-width="1.5"/>
                            <line x1="1" y1="13" x2="15" y2="13" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="products-container">
            <div class="products-grid active" id="brand-products">
                <?php
                // Query products for current brand (Polylang will filter by current language automatically)
                $args = array(
                    'post_type'      => 'product',
                    'posts_per_page' => -1,
                    'tax_query'      => array(
                        array(
                            'taxonomy' => 'product_brand',
                            'field'    => 'term_id',
                            'terms'    => $current_brand->term_id,
                        ),
                    ),
                    'orderby' => 'date',
                    'order'   => 'DESC',
                );
                $products_query = new WP_Query( $args );

                if ($products_query->have_posts()) :
                    while ($products_query->have_posts()) : $products_query->the_post();
                        global $product;
                        ?>
                        <div class="product-card" data-date="<?php echo get_the_date('U'); ?>" data-title="<?php echo esc_attr(get_the_title()); ?>" data-price="<?php echo $product ? esc_attr($product->get_price()) : '0'; ?>">
                            <div class="product-badges">
                                <?php
                                $product_badge = get_post_meta(get_the_ID(), '_product_badge', true);
                                if ($product_badge) :
                                    $badge_labels = array(
                                        'bestseller' => __('Bestseller', 'kacosmetics'),
                                        'must-try' => __('Must Try', 'kacosmetics'),
                                        'new' => __('New', 'kacosmetics'),
                                    );
                                    $label = isset($badge_labels[$product_badge]) ? $badge_labels[$product_badge] : ucfirst(str_replace('-', ' ', $product_badge));
                                ?>
                                    <span class="badge badge-<?php echo esc_attr($product_badge); ?>"><?php echo esc_html($label); ?></span>
                                <?php endif; ?>
                                <?php if (get_post_meta(get_the_ID(), '_is_new', true)) : ?>
                                    <span class="badge badge-new"><?php esc_html_e('New', 'kacosmetics'); ?></span>
                                <?php endif; ?>
                                <?php if (get_post_meta(get_the_ID(), '_is_exclusive', true)) : ?>
                                    <span class="badge badge-exclusive"><?php esc_html_e('Exclusive', 'kacosmetics'); ?></span>
                                <?php endif; ?>
                                <?php if ($product && $product->is_on_sale()) : ?>
                                    <span class="badge badge-sale"><?php esc_html_e('Sale', 'kacosmetics'); ?></span>
                                <?php endif; ?>
                            </div>

                            <a href="<?php the_permalink(); ?>" class="product-image-link">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('large', array('class' => 'product-image')); ?>
                                <?php else : ?>
                                    <div class="product-image placeholder-image"></div>
                                <?php endif; ?>
                            </a>

                            <div class="product-info">
                                <h3 class="product-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                <?php if ($product && method_exists($product, 'get_short_description')) : ?>
                                    <p class="product-description"><?php echo wp_trim_words($product->get_short_description(), 12); ?></p>
                                <?php endif; ?>
                                <?php if ($product && method_exists($product, 'get_price_html')) : ?>
                                    <p class="product-price"><?php echo $product->get_price_html(); ?></p>
                                <?php endif; ?>
                            </div>

                            <div class="product-actions">
                                <button class="product-icon-button quick-shop" data-product-id="<?php echo get_the_ID(); ?>">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                        <path d="M3 5H13L12 13H4L3 5Z" stroke="currentColor" stroke-width="1"/>
                                        <path d="M6 5V3C6 2.44772 6.44772 2 7 2H9C9.55228 2 10 2.44772 10 3V5" stroke="currentColor" stroke-width="1"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                    ?>
                    <div class="no-products-brand">
                        <svg width="64" height="64" viewBox="0 0 64 64" fill="none">
                            <path d="M16 20H48L44 52H20L16 20Z" stroke="currentColor" stroke-width="2"/>
                            <path d="M24 20V16C24 12.6863 26.6863 10 30 10H34C37.3137 10 40 12.6863 40 16V20" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        <h3><?php esc_html_e('No products found', 'kacosmetics'); ?></h3>
                        <p><?php esc_html_e('This brand has no products at the moment. Please check back later.', 'kacosmetics'); ?></p>
                    </div>
                    <?php
                endif;
                ?>
            </div>
        </div>

    </main>
</div>

<script>
(function() {
    'use strict';

    // Sort functionality
    const sortSelect = document.getElementById('product-sort');
    const productsGrid = document.getElementById('brand-products');

    if (sortSelect && productsGrid) {
        sortSelect.addEventListener('change', function() {
            const sortValue = this.value;
            const products = Array.from(productsGrid.querySelectorAll('.product-card'));

            products.sort((a, b) => {
                switch(sortValue) {
                    case 'date-desc':
                        return parseInt(b.dataset.date) - parseInt(a.dataset.date);
                    case 'date-asc':
                        return parseInt(a.dataset.date) - parseInt(b.dataset.date);
                    case 'title-asc':
                        return a.dataset.title.localeCompare(b.dataset.title);
                    case 'title-desc':
                        return b.dataset.title.localeCompare(a.dataset.title);
                    case 'price-asc':
                        return parseFloat(a.dataset.price || 0) - parseFloat(b.dataset.price || 0);
                    case 'price-desc':
                        return parseFloat(b.dataset.price || 0) - parseFloat(a.dataset.price || 0);
                    default:
                        return 0;
                }
            });

            products.forEach(product => productsGrid.appendChild(product));
        });
    }

    // View toggle functionality
    const viewToggleBtns = document.querySelectorAll('.view-toggle-btn');

    viewToggleBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const view = this.dataset.view;

            // Update active button
            viewToggleBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            // Update grid view
            if (view === 'list') {
                productsGrid.classList.add('list-view');
            } else {
                productsGrid.classList.remove('list-view');
            }
        });
    });
})();
</script>

<?php
get_footer();
