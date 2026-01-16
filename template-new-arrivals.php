<?php
/**
 * Template Name: New Arrivals (Dior Style)
 *
 * @package kacosmetics
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main new-arrivals-main">

        <!-- Category Tabs -->
        <div class="category-tabs-wrapper">
            <div class="category-tabs">
                <button class="tab-button active" data-category="accessories">
                    Accessories
                </button>
                <button class="tab-button" data-category="hoodies">
                    Hoodies
                </button>
                <button class="tab-button" data-category="tshirts">
                    Tshirts
                </button>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="products-container">

            <!-- Accessories Products -->
            <div class="products-grid active" id="accessories-products">
                <?php
                // Query для товарів категорії Accessories
                $accessories_args = array(
                    'post_type' => 'product',
                    'posts_per_page' => 12,
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'product_cat',
                            'field' => 'slug',
                            'terms' => 'accessories'
                        )
                    ),
                    'orderby' => 'date',
                    'order' => 'DESC'
                );
                $accessories_query = new WP_Query($accessories_args);

                if ($accessories_query->have_posts()) :
                    while ($accessories_query->have_posts()) : $accessories_query->the_post();
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
                                    <img src="<?php echo get_template_directory_uri(); ?>/images/placeholder.png" alt="<?php the_title(); ?>" class="product-image">
                                <?php endif; ?>
                            </a>

                            <div class="product-info">
                                <h3 class="product-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                <?php if ($product) : ?>
                                    <p class="product-price"><?php echo $product->get_price_html(); ?></p>
                                <?php endif; ?>
                            </div>

                            <a href="<?php the_permalink(); ?>" class="product-button">Buy</a>
                        </div>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                    echo '<p class="no-products">No products found in Accessories category.</p>';
                endif;
                ?>
            </div>

            <!-- Hoodies Products -->
            <div class="products-grid" id="hoodies-products">
                <?php
                $hoodies_args = array(
                    'post_type' => 'product',
                    'posts_per_page' => 12,
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'product_cat',
                            'field' => 'slug',
                            'terms' => 'hoodies'
                        )
                    ),
                    'orderby' => 'date',
                    'order' => 'DESC'
                );
                $hoodies_query = new WP_Query($hoodies_args);

                if ($hoodies_query->have_posts()) :
                    while ($hoodies_query->have_posts()) : $hoodies_query->the_post();
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
                                    <img src="<?php echo get_template_directory_uri(); ?>/images/placeholder.png" alt="<?php the_title(); ?>" class="product-image">
                                <?php endif; ?>
                            </a>

                            <div class="product-info">
                                <h3 class="product-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                <?php if ($product) : ?>
                                    <p class="product-price"><?php echo $product->get_price_html(); ?></p>
                                <?php endif; ?>
                            </div>

                            <a href="<?php the_permalink(); ?>" class="product-button">Buy</a>
                        </div>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                    echo '<p class="no-products">No products found in Hoodies category.</p>';
                endif;
                ?>
            </div>

            <!-- Tshirts Products -->
            <div class="products-grid" id="tshirts-products">
                <?php
                $tshirts_args = array(
                    'post_type' => 'product',
                    'posts_per_page' => 12,
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'product_cat',
                            'field' => 'slug',
                            'terms' => 'tshirts'
                        )
                    ),
                    'orderby' => 'date',
                    'order' => 'DESC'
                );
                $tshirts_query = new WP_Query($tshirts_args);

                if ($tshirts_query->have_posts()) :
                    while ($tshirts_query->have_posts()) : $tshirts_query->the_post();
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
                                    <img src="<?php echo get_template_directory_uri(); ?>/images/placeholder.png" alt="<?php the_title(); ?>" class="product-image">
                                <?php endif; ?>
                            </a>

                            <div class="product-info">
                                <h3 class="product-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                <?php if ($product) : ?>
                                    <p class="product-price"><?php echo $product->get_price_html(); ?></p>
                                <?php endif; ?>
                            </div>

                            <a href="<?php the_permalink(); ?>" class="product-button">Buy</a>
                        </div>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                    echo '<p class="no-products">No products found in Tshirts category.</p>';
                endif;
                ?>
            </div>

        </div><!-- .products-container -->

    </main><!-- #main -->
</div><!-- #primary -->

<?php
get_footer();
