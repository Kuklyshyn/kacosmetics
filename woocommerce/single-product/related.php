<?php
/**
 * Related Products
 *
 * @package kacosmetics
 */

defined( 'ABSPATH' ) || exit;

if ( $related_products ) : ?>

	<section class="related products">

		<?php
		$heading = apply_filters( 'woocommerce_product_related_products_heading', __( 'Related products', 'woocommerce' ) );

		if ( $heading ) :
			?>
			<h2><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>

		<?php woocommerce_product_loop_start(); ?>

			<?php foreach ( $related_products as $related_product ) : ?>

				<?php
				$post_object = get_post( $related_product->get_id() );

				setup_postdata( $GLOBALS['post'] =& $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found

				global $product;
				?>

				<li <?php wc_product_class( '', $product ); ?>>
					<div class="product-card">
						<?php
						/**
						 * Product badges
						 */
						?>
						<div class="product-badges">
							<?php if ( $product->is_on_sale() ) : ?>
								<span class="badge badge-new">Sale</span>
							<?php endif; ?>
						</div>

						<a href="<?php the_permalink(); ?>" class="product-image-link">
							<?php if ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'large', array( 'class' => 'product-image' ) ); ?>
							<?php else : ?>
								<div class="product-image placeholder-image">
									<span>No image</span>
								</div>
							<?php endif; ?>
						</a>

						<div class="product-info">
							<h3 class="product-title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h3>
							<?php if ( $product->get_short_description() ) : ?>
								<p class="product-description"><?php echo wp_trim_words( $product->get_short_description(), 8 ); ?></p>
							<?php endif; ?>
							<?php if ( $product->get_price_html() ) : ?>
								<p class="product-price"><?php echo $product->get_price_html(); ?></p>
							<?php endif; ?>
						</div>
					</div>
				</li>

			<?php endforeach; ?>

		<?php woocommerce_product_loop_end(); ?>

	</section>
	<?php
endif;

wp_reset_postdata();
