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

					<!-- Instagram -->
					<div class="product-social-share">
						<div class="share-buttons">
							<a href="https://www.instagram.com/kacosmetics.sk?igsh=eXF2Nm1mdnVleXJn" target="_blank" class="share-btn instagram" aria-label="Instagram">
								<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
									<path d="M12 2.163c3.204 0 3.584.012 4.85.07 1.17.054 1.97.24 2.43.403a4.088 4.088 0 011.47.96c.458.458.779.924.96 1.47.163.46.349 1.26.403 2.43.058 1.266.07 1.646.07 4.85s-.012 3.584-.07 4.85c-.054 1.17-.24 1.97-.403 2.43a4.088 4.088 0 01-.96 1.47 4.088 4.088 0 01-1.47.96c-.46.163-1.26.349-2.43.403-1.266.058-1.646.07-4.85.07s-3.584-.012-4.85-.07c-1.17-.054-1.97-.24-2.43-.403a4.088 4.088 0 01-1.47-.96 4.088 4.088 0 01-.96-1.47c-.163-.46-.349-1.26-.403-2.43C2.175 15.584 2.163 15.204 2.163 12s.012-3.584.07-4.85c.054-1.17.24-1.97.403-2.43a4.088 4.088 0 01.96-1.47 4.088 4.088 0 011.47-.96c.46-.163 1.26-.349 2.43-.403C8.416 2.175 8.796 2.163 12 2.163M12 0C8.741 0 8.333.014 7.053.072 5.775.13 4.902.333 4.14.63a5.882 5.882 0 00-2.126 1.384A5.882 5.882 0 00.63 4.14C.333 4.902.13 5.775.072 7.053.014 8.333 0 8.741 0 12s.014 3.667.072 4.947c.058 1.278.261 2.151.558 2.913a5.882 5.882 0 001.384 2.126 5.882 5.882 0 002.126 1.384c.762.297 1.635.5 2.913.558C8.333 23.986 8.741 24 12 24s3.667-.014 4.947-.072c1.278-.058 2.151-.261 2.913-.558a5.882 5.882 0 002.126-1.384 5.882 5.882 0 001.384-2.126c.297-.762.5-1.635.558-2.913.058-1.28.072-1.688.072-4.947s-.014-3.667-.072-4.947c-.058-1.278-.261-2.151-.558-2.913a5.882 5.882 0 00-1.384-2.126A5.882 5.882 0 0019.86.63C19.098.333 18.225.13 16.947.072 15.667.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
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
