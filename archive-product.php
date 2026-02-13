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
                            <?php echo esc_html( kac_translate_category_name( $category->name ) ); ?>
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
                            <?php
                            global $wpdb;
                            $min_price = $wpdb->get_var( "SELECT MIN( CAST( meta_value AS DECIMAL(10,2) ) ) FROM {$wpdb->postmeta} WHERE meta_key = '_price' AND meta_value != ''" );
                            $max_price = $wpdb->get_var( "SELECT MAX( CAST( meta_value AS DECIMAL(10,2) ) ) FROM {$wpdb->postmeta} WHERE meta_key = '_price' AND meta_value != ''" );

                            $min_price = floor( $min_price );
                            $max_price = ceil( $max_price );

                            $current_min = isset( $_GET['min_price'] ) ? floatval( $_GET['min_price'] ) : $min_price;
                            $current_max = isset( $_GET['max_price'] ) ? floatval( $_GET['max_price'] ) : $max_price;

                            if ( $min_price < $max_price ) :
                            ?>
                                <div class="widget woocommerce widget_price_filter">
                                    <h4 class="widget-title"><?php esc_html_e('Price', 'kacosmetics'); ?></h4>
                                    <form method="get" action="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
                                        <div class="price_slider_wrapper">
                                            <div class="price_slider" style="display:none;"></div>
                                            <div class="price_slider_amount" data-step="1">
                                                <input type="text" id="min_price" name="min_price" value="<?php echo esc_attr( $current_min ); ?>" data-min="<?php echo esc_attr( $min_price ); ?>" placeholder="<?php echo esc_attr__( 'Min price', 'woocommerce' ); ?>" />
                                                <input type="text" id="max_price" name="max_price" value="<?php echo esc_attr( $current_max ); ?>" data-max="<?php echo esc_attr( $max_price ); ?>" placeholder="<?php echo esc_attr__( 'Max price', 'woocommerce' ); ?>" />
                                                <button type="submit" class="button"><?php echo esc_html__( 'Filter', 'woocommerce' ); ?></button>
                                                <div class="price_label" style="display:none;">
                                                    <?php echo esc_html__( 'Price:', 'woocommerce' ); ?> <span class="from"><?php echo wc_price( $current_min ); ?></span> &mdash; <span class="to"><?php echo wc_price( $current_max ); ?></span>
                                                </div>
                                                <?php echo wc_query_string_form_fields( null, array( 'min_price', 'max_price', 'paged' ), '', true ); ?>
                                                <div class="clear"></div>
                                            </div>
                                        </div>
                                    </form>
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
                    // Get page number for this category from URL
                    $cat_paged = isset($_GET['cat_page_' . $category->slug]) ? intval($_GET['cat_page_' . $category->slug]) : 1;

                    // Get default language category for querying products
                    $default_lang_category = kac_get_default_lang_category($category);
                    $query_slug = $default_lang_category->slug;
                    ?>
                    <!-- <?php echo esc_html($category->name); ?> Products -->
                    <div class="products-grid" id="<?php echo esc_attr($category->slug); ?>-products" data-category="<?php echo esc_attr($category->slug); ?>" data-query-slug="<?php echo esc_attr($query_slug); ?>">
                        <?php
                        $category_args = array(
                            'post_type' => 'product',
                            'posts_per_page' => 12,
                            'paged' => $cat_paged,
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'product_cat',
                                    'field' => 'slug',
                                    'terms' => $query_slug
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

                        if ($category_query->have_posts()) : ?>
                        <div class="category-products-inner">
                            <?php while ($category_query->have_posts()) : $category_query->the_post();
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
                        </div><!-- .category-products-inner -->

                        <?php if ($category_query->max_num_pages > 1) : ?>
                        <nav class="woocommerce-pagination category-pagination" data-category="<?php echo esc_attr($category->slug); ?>" data-max-pages="<?php echo esc_attr($category_query->max_num_pages); ?>">
                            <?php
                            $pagination_args = array(
                                'total' => $category_query->max_num_pages,
                                'current' => $cat_paged,
                                'prev_text' => '&larr;',
                                'next_text' => '&rarr;',
                                'type' => 'list',
                            );
                            echo paginate_links($pagination_args);
                            ?>
                        </nav>
                        <?php endif; ?>

                        <?php wp_reset_postdata();
                        else : ?>
                        <div class="category-products-inner">
                            <p class="no-products"><?php esc_html_e('No products found in this category.', 'kacosmetics'); ?></p>
                        </div>
                        <?php endif; ?>
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
