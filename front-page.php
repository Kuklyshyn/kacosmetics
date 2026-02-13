<?php
/**
 * The front page template file
 *
 * @package kacosmetics
 */

get_header();

// Get hero banner slides
$hero_slides = kacosmetics_get_hero_slides();
$hero_height = get_theme_mod('hero_banner_height', '500');
$hero_height_mobile = get_theme_mod('hero_banner_height_mobile', '300');
$hero_autoplay = get_theme_mod('hero_banner_autoplay', true);
$hero_interval = get_theme_mod('hero_banner_interval', 5000);

if (!empty($hero_slides)) :
	$is_single = count($hero_slides) === 1;
?>
<style>
	.hero-banner-container {
		--hero-height: <?php echo esc_attr($hero_height); ?>px;
		--hero-height-mobile: <?php echo esc_attr($hero_height_mobile); ?>px;
	}
</style>
<div class="hero-banner<?php echo $is_single ? ' single-slide' : ''; ?>"
     data-autoplay="<?php echo $hero_autoplay ? 'true' : 'false'; ?>"
     data-interval="<?php echo esc_attr($hero_interval); ?>">
	<div class="hero-banner-container">
		<div class="hero-slides">
			<?php foreach ($hero_slides as $index => $slide) :
				$has_content = !empty($slide['title']) || !empty($slide['subtitle']) || !empty($slide['button']);
				$has_mobile = !empty($slide['image_mobile']);
			?>
				<div class="hero-slide<?php echo $index === 0 ? ' active' : ''; ?>">
					<?php if (!empty($slide['link'])) : ?>
						<a href="<?php echo esc_url($slide['link']); ?>" class="hero-slide-link">
					<?php endif; ?>

					<?php if ($has_mobile) : ?>
						<img src="<?php echo esc_url($slide['image']); ?>"
						     alt="<?php echo esc_attr($slide['title']); ?>"
						     class="hero-slide-image desktop-image"
						     loading="<?php echo $index === 0 ? 'eager' : 'lazy'; ?>">
						<img src="<?php echo esc_url($slide['image_mobile']); ?>"
						     alt="<?php echo esc_attr($slide['title']); ?>"
						     class="hero-slide-image mobile-image"
						     loading="<?php echo $index === 0 ? 'eager' : 'lazy'; ?>">
					<?php else : ?>
						<img src="<?php echo esc_url($slide['image']); ?>"
						     alt="<?php echo esc_attr($slide['title']); ?>"
						     class="hero-slide-image desktop-image no-mobile"
						     loading="<?php echo $index === 0 ? 'eager' : 'lazy'; ?>">
					<?php endif; ?>

					<?php if ($has_content) : ?>
						<div class="hero-slide-content">
							<?php if (!empty($slide['title'])) : ?>
								<h2 class="hero-slide-title"><?php echo esc_html($slide['title']); ?></h2>
							<?php endif; ?>
							<?php if (!empty($slide['subtitle'])) : ?>
								<p class="hero-slide-subtitle"><?php echo esc_html($slide['subtitle']); ?></p>
							<?php endif; ?>
							<?php if (!empty($slide['button']) && !empty($slide['link'])) : ?>
								<span class="hero-slide-button"><?php echo esc_html($slide['button']); ?></span>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<?php if (!empty($slide['link'])) : ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>

		<?php if (!$is_single) : ?>
			<!-- Navigation arrows -->
			<button class="hero-nav hero-nav-prev" aria-label="<?php esc_attr_e('Previous slide', 'kacosmetics'); ?>">
				<svg viewBox="0 0 24 24"><polyline points="15,18 9,12 15,6"></polyline></svg>
			</button>
			<button class="hero-nav hero-nav-next" aria-label="<?php esc_attr_e('Next slide', 'kacosmetics'); ?>">
				<svg viewBox="0 0 24 24"><polyline points="9,6 15,12 9,18"></polyline></svg>
			</button>

			<!-- Dots navigation -->
			<div class="hero-dots">
				<?php for ($i = 0; $i < count($hero_slides); $i++) : ?>
					<button class="hero-dot<?php echo $i === 0 ? ' active' : ''; ?>"
					        aria-label="<?php printf(esc_attr__('Go to slide %d', 'kacosmetics'), $i + 1); ?>"></button>
				<?php endfor; ?>
			</div>
		<?php endif; ?>
	</div>
</div>
<?php endif; ?>

<?php
// Bestseller Products Slider
$bestseller_query = new WP_Query(array(
    'post_type'      => 'product',
    'posts_per_page' => 12,
    'meta_query'     => array(
        array(
            'key'   => '_product_badge',
            'value' => 'bestseller',
        ),
    ),
    'orderby' => 'date',
    'order'   => 'DESC',
));

if ($bestseller_query->have_posts()) :
?>
<section class="bestseller-slider-section">
    <div class="bestseller-slider-container">
        <h2 class="bestseller-slider-title"><?php esc_html_e('Bestseller', 'kacosmetics'); ?></h2>

        <div class="bestseller-slider-wrapper">
            <div class="bestseller-slider-overflow">
            <div class="bestseller-slider">
                <?php while ($bestseller_query->have_posts()) : $bestseller_query->the_post();
                    global $product;
                ?>
                    <div class="bestseller-slide">
                        <div class="product-card">
                            <div class="product-badges">
                                <span class="badge badge-bestseller"><?php esc_html_e('Bestseller', 'kacosmetics'); ?></span>
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
                    </div>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
            </div><!-- .bestseller-slider-overflow -->

            <!-- Slider Navigation -->
            <button class="bestseller-nav bestseller-nav-prev" aria-label="<?php esc_attr_e('Previous', 'kacosmetics'); ?>">
                <svg viewBox="0 0 24 24" width="24" height="24"><polyline points="15,18 9,12 15,6" fill="none" stroke="currentColor" stroke-width="2"></polyline></svg>
            </button>
            <button class="bestseller-nav bestseller-nav-next" aria-label="<?php esc_attr_e('Next', 'kacosmetics'); ?>">
                <svg viewBox="0 0 24 24" width="24" height="24"><polyline points="9,6 15,12 9,18" fill="none" stroke="currentColor" stroke-width="2"></polyline></svg>
            </button>
        </div>
    </div>
</section>
<?php endif; ?>

<?php
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
