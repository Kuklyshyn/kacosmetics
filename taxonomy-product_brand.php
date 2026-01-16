<?php
/**
 * The template for displaying product brand archives
 *
 * @package kacosmetics
 */

get_header();

// Get current brand
$current_brand = get_queried_object();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main new-arrivals-main">

        <?php
        // Get all brands
        $all_brands = get_terms(array(
            'taxonomy' => 'product_brand',
            'hide_empty' => true,
            'number' => 10,
        ));

        if ($all_brands && !is_wp_error($all_brands) && count($all_brands) > 1) :
        ?>
            <!-- Brand Tabs -->
            <div class="category-tabs-wrapper">
                <div class="category-tabs">
                    <?php foreach ($all_brands as $brand) : ?>
                        <button class="tab-button <?php echo ($brand->term_id === $current_brand->term_id) ? 'active' : ''; ?>"
                                data-category="brand-<?php echo esc_attr($brand->slug); ?>">
                            <?php echo esc_html($brand->name); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Products Grid -->
        <div class="products-container">

            <!-- Current Brand Products -->
            <div class="products-grid active" id="brand-<?php echo esc_attr($current_brand->slug); ?>-products">
                <?php
                $args = array(
                    'post_type' => 'product',
                    'posts_per_page' => 12,
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'product_brand',
                            'field' => 'term_id',
                            'terms' => $current_brand->term_id
                        )
                    ),
                    'orderby' => 'date',
                    'order' => 'DESC'
                );
                $products_query = new WP_Query($args);

                if ($products_query->have_posts()) :
                    while ($products_query->have_posts()) : $products_query->the_post();
                        global $product;
                        ?>
                        <div class="product-card">
                            <div class="product-badges">
                                <?php if (get_post_meta(get_the_ID(), '_is_new', true)) : ?>
                                    <span class="badge badge-new">New</span>
                                <?php endif; ?>
                                <?php if (get_post_meta(get_the_ID(), '_is_exclusive', true)) : ?>
                                    <span class="badge badge-exclusive">Exclusive</span>
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
                    echo '<p class="no-products">No products found for this brand.</p>';
                endif;
                ?>
            </div>

            <?php
            // Create grids for other brands
            if ($all_brands && !is_wp_error($all_brands)) :
                foreach ($all_brands as $brand) :
                    if ($brand->term_id === $current_brand->term_id) continue;
                    ?>
                    <div class="products-grid" id="brand-<?php echo esc_attr($brand->slug); ?>-products">
                        <?php
                        $brand_args = array(
                            'post_type' => 'product',
                            'posts_per_page' => 12,
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'product_brand',
                                    'field' => 'term_id',
                                    'terms' => $brand->term_id
                                )
                            ),
                            'orderby' => 'date',
                            'order' => 'DESC'
                        );
                        $brand_query = new WP_Query($brand_args);

                        if ($brand_query->have_posts()) :
                            while ($brand_query->have_posts()) : $brand_query->the_post();
                                global $product;
                                ?>
                                <div class="product-card">
                                    <div class="product-badges">
                                        <?php if (get_post_meta(get_the_ID(), '_is_new', true)) : ?>
                                            <span class="badge badge-new">New</span>
                                        <?php endif; ?>
                                        <?php if (get_post_meta(get_the_ID(), '_is_exclusive', true)) : ?>
                                            <span class="badge badge-exclusive">Exclusive</span>
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
                        endif;
                        ?>
                    </div>
                    <?php
                endforeach;
            endif;
            ?>

        </div><!-- .products-container -->

    </main><!-- #main -->
</div><!-- #primary -->

<?php
get_footer();
