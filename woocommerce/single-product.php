<?php
/**
 * The Template for displaying all single products
 *
 * @package kacosmetics
 */

defined( 'ABSPATH' ) || exit;

get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main single-product-main">

		<?php while ( have_posts() ) : ?>
			<?php the_post(); ?>

			<?php
			global $product;
			?>

			<div class="single-product-container">

				<!-- Product Gallery -->
				<div class="product-gallery-section">
					<?php
					/**
					 * Product images
					 */
					if ( has_post_thumbnail() ) {
						$attachment_ids = $product->get_gallery_image_ids();
						array_unshift( $attachment_ids, get_post_thumbnail_id() );

						if ( $attachment_ids ) :
						?>
							<div class="product-main-image">
								<?php echo wp_get_attachment_image( $attachment_ids[0], 'full', false, array('class' => 'main-product-img') ); ?>
							</div>

							<?php if ( count( $attachment_ids ) > 1 ) : ?>
								<div class="product-thumbnails">
									<?php foreach ( $attachment_ids as $attachment_id ) : ?>
										<div class="thumbnail-item">
											<?php echo wp_get_attachment_image( $attachment_id, 'medium', false, array('class' => 'thumbnail-img') ); ?>
										</div>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
						<?php
						endif;
					} else {
						echo '<div class="product-main-image"><div class="placeholder-image">No image</div></div>';
					}
					?>
				</div>

				<!-- Product Info -->
				<div class="product-info-section">

					<!-- Breadcrumbs -->
					<?php if ( function_exists( 'woocommerce_breadcrumb' ) ) : ?>
						<div class="product-breadcrumbs">
							<?php woocommerce_breadcrumb(); ?>
						</div>
					<?php endif; ?>

					<!-- Product Title -->
					<h1 class="product-title-single"><?php the_title(); ?></h1>

					<!-- Product Price -->
					<div class="product-price-single">
						<?php echo $product->get_price_html(); ?>
					</div>

					<!-- Product Short Description -->
					<?php if ( $product->get_short_description() ) : ?>
						<div class="product-short-description">
							<?php echo $product->get_short_description(); ?>
						</div>
					<?php endif; ?>

					<!-- Add to Cart Form -->
					<div class="product-add-to-cart">
						<?php woocommerce_template_single_add_to_cart(); ?>
					</div>

					<!-- Product Meta -->
					<div class="product-meta-info">
						<?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>
							<div class="product-meta-item">
								<span class="meta-label">SKU:</span>
								<span class="meta-value"><?php echo $product->get_sku() ? $product->get_sku() : __( 'N/A', 'kacosmetics' ); ?></span>
							</div>
						<?php endif; ?>

						<?php
						$categories = get_the_terms( $product->get_id(), 'product_cat' );
						if ( $categories && ! is_wp_error( $categories ) ) :
						?>
							<div class="product-meta-item">
								<span class="meta-label">Category:</span>
								<span class="meta-value">
									<?php
									$cat_names = array();
									foreach ( $categories as $category ) {
										$cat_names[] = '<a href="' . get_term_link( $category ) . '">' . $category->name . '</a>';
									}
									echo implode( ', ', $cat_names );
									?>
								</span>
							</div>
						<?php endif; ?>

						<?php
						$brands = get_the_terms( $product->get_id(), 'product_brand' );
						if ( $brands && ! is_wp_error( $brands ) ) :
						?>
							<div class="product-meta-item">
								<span class="meta-label">Brand:</span>
								<span class="meta-value">
									<?php
									$brand_names = array();
									foreach ( $brands as $brand ) {
										$brand_names[] = '<a href="' . get_term_link( $brand ) . '">' . $brand->name . '</a>';
									}
									echo implode( ', ', $brand_names );
									?>
								</span>
							</div>
						<?php endif; ?>
					</div>

					<!-- Social Share -->
					<div class="product-social-share">
						<span class="share-label">Share:</span>
						<div class="share-buttons">
							<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode( get_permalink() ); ?>" target="_blank" class="share-btn facebook" aria-label="Share on Facebook">
								<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
									<path d="M10 0C4.477 0 0 4.477 0 10c0 4.991 3.657 9.128 8.438 9.879V12.89h-2.54V10h2.54V7.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V10h2.773l-.443 2.89h-2.33v6.989C16.343 19.128 20 14.991 20 10c0-5.523-4.477-10-10-10z"/>
								</svg>
							</a>
							<a href="https://twitter.com/intent/tweet?url=<?php echo urlencode( get_permalink() ); ?>&text=<?php echo urlencode( get_the_title() ); ?>" target="_blank" class="share-btn twitter" aria-label="Share on Twitter">
								<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
									<path d="M6.29 18.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0020 3.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.073 4.073 0 01.8 7.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 010 16.407a11.616 11.616 0 006.29 1.84"/>
								</svg>
							</a>
							<a href="https://pinterest.com/pin/create/button/?url=<?php echo urlencode( get_permalink() ); ?>&media=<?php echo urlencode( get_the_post_thumbnail_url() ); ?>&description=<?php echo urlencode( get_the_title() ); ?>" target="_blank" class="share-btn pinterest" aria-label="Share on Pinterest">
								<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
									<path d="M10 0C4.477 0 0 4.477 0 10c0 4.237 2.636 7.855 6.356 9.312-.088-.791-.167-2.005.035-2.868.181-.78 1.172-4.97 1.172-4.97s-.299-.6-.299-1.486c0-1.39.806-2.428 1.81-2.428.852 0 1.264.64 1.264 1.408 0 .858-.546 2.14-.828 3.33-.236.995.5 1.807 1.48 1.807 1.778 0 3.144-1.874 3.144-4.58 0-2.393-1.72-4.068-4.176-4.068-2.845 0-4.516 2.135-4.516 4.34 0 .859.331 1.781.745 2.281a.3.3 0 01.069.288l-.278 1.133c-.044.183-.145.223-.335.134-1.249-.581-2.03-2.407-2.03-3.874 0-3.154 2.292-6.052 6.608-6.052 3.469 0 6.165 2.473 6.165 5.776 0 3.447-2.173 6.22-5.19 6.22-1.013 0-1.965-.525-2.291-1.148l-.623 2.378c-.226.869-.835 1.958-1.244 2.621.937.29 1.931.446 2.962.446 5.523 0 10-4.477 10-10S15.523 0 10 0z"/>
								</svg>
							</a>
						</div>
					</div>

				</div>

			</div>

			<!-- Product Tabs / Description -->
			<div class="product-details-tabs">
				<?php woocommerce_output_product_data_tabs(); ?>
			</div>

			<!-- Related Products -->
			<div class="related-products-section">
				<?php woocommerce_output_related_products(); ?>
			</div>

		<?php endwhile; ?>

	</main>
</div>

<?php
get_footer();
