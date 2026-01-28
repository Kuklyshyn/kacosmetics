<?php
/**
 * The front page template file
 *
 * @package kacosmetics
 */

get_header();

// Get top-level product categories that have products
$categories = get_terms(array(
    'taxonomy'   => 'product_cat',
    'hide_empty' => true,
    'parent'     => 0,
    'exclude'    => get_option('default_product_cat'),
    'orderby'    => 'name',
    'order'      => 'ASC',
));

if (is_wp_error($categories)) {
    $categories = array();
}
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main new-arrivals-main">

        <?php if (!empty($categories)) : ?>
            <!-- Category Tabs -->
            <div class="category-tabs-wrapper">
                <div class="category-tabs">
                    <?php foreach ($categories as $index => $cat) : ?>
                        <button class="tab-button<?php echo $index === 0 ? ' active' : ''; ?>" data-category="<?php echo esc_attr($cat->slug); ?>">
                            <?php echo esc_html($cat->name); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="products-container">
                <?php foreach ($categories as $index => $cat) :
                    $products_query = new WP_Query(array(
                        'post_type'      => 'product',
                        'posts_per_page' => 12,
                        'tax_query'      => array(
                            array(
                                'taxonomy' => 'product_cat',
                                'field'    => 'slug',
                                'terms'    => $cat->slug,
                            ),
                        ),
                        'orderby' => 'date',
                        'order'   => 'DESC',
                    ));
                ?>
                    <div class="products-grid<?php echo $index === 0 ? ' active' : ''; ?>" id="<?php echo esc_attr($cat->slug); ?>-products">
                        <?php if ($products_query->have_posts()) :
                            while ($products_query->have_posts()) : $products_query->the_post();
                                global $product;
                        ?>
                                <div class="product-card">
                                    <div class="product-badges">
                                        <?php if (get_post_meta(get_the_ID(), '_is_new', true)) : ?>
                                            <span class="badge badge-new"><?php esc_html_e('New', 'kacosmetics'); ?></span>
                                        <?php endif; ?>
                                        <?php if (get_post_meta(get_the_ID(), '_is_exclusive', true)) : ?>
                                            <span class="badge badge-exclusive"><?php esc_html_e('Exclusive', 'kacosmetics'); ?></span>
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
                                            <p class="product-description"><?php echo wp_trim_words($product->get_short_description(), 8); ?></p>
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
                            <p class="no-products"><?php printf(esc_html__('No products found in %s category.', 'kacosmetics'), esc_html($cat->name)); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div><!-- .products-container -->
        <?php endif; ?>

    </main><!-- #main -->
</div><!-- #primary -->

<?php
get_footer();
