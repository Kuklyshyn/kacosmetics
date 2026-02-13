<?php
/**
 * The template for displaying product archives (shop page)
 *
 * @package kacosmetics
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main new-arrivals-main">

        <?php
        // Get all product categories for tabs
        $product_categories = get_terms(array(
            'taxonomy' => 'product_cat',
            'hide_empty' => true,
            'exclude' => get_option('default_product_cat'),
            'number' => 10, // Limit to 10 categories
        ));

        if ($product_categories && !is_wp_error($product_categories)) :
        ?>
            <!-- Category Tabs -->
            <div class="category-tabs-wrapper">
                <div class="category-tabs">
                    <button class="tab-button active" data-category="all">
                        <?php esc_html_e('All Products', 'kacosmetics'); ?>
                    </button>
                    <?php foreach ($product_categories as $index => $category) : ?>
                        <button class="tab-button" data-category="<?php echo esc_attr($category->slug); ?>">
                            <?php echo esc_html($category->name); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Shop Container with Filters -->
        <div class="shop-container">

            <!-- Filters Sidebar -->
            <aside class="shop-sidebar">
                <div class="sidebar-inner">

                    <!-- Filter Toggle for Mobile -->
                    <button class="filters-toggle">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M4 6H16M4 10H16M4 14H16" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                        <?php esc_html_e('Filters', 'kacosmetics'); ?>
                    </button>

                    <div class="filters-content">
                        <h3 class="sidebar-title"><?php esc_html_e('Filters', 'kacosmetics'); ?></h3>

                        <?php if (is_active_sidebar('shop-filters')) : ?>
                            <?php dynamic_sidebar('shop-filters'); ?>
                        <?php else : ?>

                            <!-- Price Filter -->
                            <?php if (class_exists('WC_Widget_Price_Filter')) : ?>
                                <div class="widget woocommerce widget_price_filter">
                                    <h4 class="widget-title"><?php esc_html_e('Price', 'kacosmetics'); ?></h4>
                                    <?php the_widget('WC_Widget_Price_Filter'); ?>
                                </div>
                            <?php endif; ?>

                            <!-- Brand Filter -->
                            <?php
                            $brands = get_terms(array(
                                'taxonomy' => 'product_brand',
                                'hide_empty' => true,
                            ));
                            if ($brands && !is_wp_error($brands)) :
                            ?>
                                <div class="widget woocommerce">
                                    <h4 class="widget-title"><?php esc_html_e('Brands', 'kacosmetics'); ?></h4>
                                    <ul class="product-brands">
                                        <?php foreach ($brands as $brand) : ?>
                                            <li>
                                                <a href="<?php echo get_term_link($brand); ?>">
                                                    <?php echo esc_html($brand->name); ?>
                                                    <span class="count">(<?php echo $brand->count; ?>)</span>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                        <?php endif; ?>
                    </div>
                </div>
            </aside>

            <!-- Products Grid -->
            <div class="products-container">

            <!-- All Products -->
            <div class="products-grid active" id="all-products">
                <?php
                $paged = get_query_var('paged') ? get_query_var('paged') : 1;
                $all_args = array(
                    'post_type' => 'product',
                    'posts_per_page' => 12,
                    'paged' => $paged,
                    'orderby' => 'date',
                    'order' => 'DESC'
                );

                // Add price filter support
                if ( isset( $_GET['min_price'] ) || isset( $_GET['max_price'] ) ) {
                    $all_args['meta_query'] = array( 'relation' => 'AND' );

                    if ( isset( $_GET['min_price'] ) ) {
                        $all_args['meta_query'][] = array(
                            'key'     => '_price',
                            'value'   => floatval( $_GET['min_price'] ),
                            'compare' => '>=',
                            'type'    => 'NUMERIC'
                        );
                    }

                    if ( isset( $_GET['max_price'] ) ) {
                        $all_args['meta_query'][] = array(
                            'key'     => '_price',
                            'value'   => floatval( $_GET['max_price'] ),
                            'compare' => '<=',
                            'type'    => 'NUMERIC'
                        );
                    }
                }

                $all_query = new WP_Query($all_args);

                if ($all_query->have_posts()) :
                    while ($all_query->have_posts()) : $all_query->the_post();
                        global $product;
                        ?>
                        <div class="product-card">
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
                    ?>

                    <?php if ($all_query->max_num_pages > 1) : ?>
                    <nav class="woocommerce-pagination">
                        <?php
                        echo paginate_links(array(
                            'base' => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
                            'format' => '?paged=%#%',
                            'current' => max(1, $paged),
                            'total' => $all_query->max_num_pages,
                            'prev_text' => '&larr;',
                            'next_text' => '&rarr;',
                            'type' => 'list',
                        ));
                        ?>
                    </nav>
                    <?php endif; ?>

                    <?php
                    wp_reset_postdata();
                else :
                    echo '<p class="no-products">' . esc_html__('No products found.', 'kacosmetics') . '</p>';
                endif;
                ?>
            </div>

            <?php
            // Create product grids for each category
            if ($product_categories && !is_wp_error($product_categories)) :
                foreach ($product_categories as $category) :
                    ?>
                    <!-- <?php echo esc_html($category->name); ?> Products -->
                    <div class="products-grid" id="<?php echo esc_attr($category->slug); ?>-products">
                        <?php
                        $category_args = array(
                            'post_type' => 'product',
                            'posts_per_page' => 12,
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'product_cat',
                                    'field' => 'slug',
                                    'terms' => $category->slug
                                )
                            ),
                            'orderby' => 'date',
                            'order' => 'DESC'
                        );

                        // Add price filter support
                        if ( isset( $_GET['min_price'] ) || isset( $_GET['max_price'] ) ) {
                            $category_args['meta_query'] = array( 'relation' => 'AND' );

                            if ( isset( $_GET['min_price'] ) ) {
                                $category_args['meta_query'][] = array(
                                    'key'     => '_price',
                                    'value'   => floatval( $_GET['min_price'] ),
                                    'compare' => '>=',
                                    'type'    => 'NUMERIC'
                                );
                            }

                            if ( isset( $_GET['max_price'] ) ) {
                                $category_args['meta_query'][] = array(
                                    'key'     => '_price',
                                    'value'   => floatval( $_GET['max_price'] ),
                                    'compare' => '<=',
                                    'type'    => 'NUMERIC'
                                );
                            }
                        }

                        $category_query = new WP_Query($category_args);

                        if ($category_query->have_posts()) :
                            while ($category_query->have_posts()) : $category_query->the_post();
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
                            echo '<p class="no-products">' . sprintf(esc_html__('No products found in %s category.', 'kacosmetics'), esc_html($category->name)) . '</p>';
                        endif;
                        ?>
                    </div>
                    <?php
                endforeach;
            endif;
            ?>

        </div><!-- .products-container -->

        </div><!-- .shop-container -->

    </main><!-- #main -->
</div><!-- #primary -->

<?php
get_footer();
