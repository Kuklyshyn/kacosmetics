<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package kacosmetics
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'kacosmetics' ); ?></a>

	<!-- Mobile Menu Sidebar -->
	<div class="mobile-menu-overlay"></div>
	<div class="mobile-menu-sidebar">
		<div class="mobile-menu-header">
			<button class="mobile-menu-close" aria-label="Close menu">×</button>
		</div>

		<div class="mobile-menu-content">
			

			<!-- Main Navigation -->
			<nav class="mobile-menu-nav">
				<ul>
					

					<?php
					// Get main product categories
					$main_categories = array('Fragrance', 'Makeup', 'Skincare', 'Spa');

					foreach ($main_categories as $cat_name) :
						// Find the category by name
						$category = get_term_by('name', $cat_name, 'product_cat');

						if ($category) :
							// Get subcategories
							$subcategories = get_terms(array(
								'taxonomy' => 'product_cat',
								'hide_empty' => false,
								'parent' => $category->term_id,
							));
							?>
							<li class="has-submenu">
								<a href="<?php echo get_term_link($category); ?>" class="menu-item-link">
									<?php echo esc_html($category->name); ?>
								</a>
								<button class="submenu-toggle" aria-label="Toggle submenu">
									<svg width="12" height="12" viewBox="0 0 12 12" fill="none">
										<path d="M4 2L8 6L4 10" stroke="currentColor" stroke-width="1.5"/>
									</svg>
								</button>
								<?php if ($subcategories && !is_wp_error($subcategories)) : ?>
									<ul class="submenu">
										<li><a href="<?php echo get_term_link($category); ?>">All <?php echo esc_html($category->name); ?></a></li>
										<?php foreach ($subcategories as $subcat) : ?>
											<li><a href="<?php echo get_term_link($subcat); ?>"><?php echo esc_html($subcat->name); ?></a></li>
										<?php endforeach; ?>
									</ul>
								<?php endif; ?>
							</li>
							<?php
						endif;
					endforeach;
					?>

					<?php
					// Get Brands (check multiple possible taxonomies)
					$brand_taxonomies = array('product_brand', 'yith_product_brand', 'pwb-brand');
					$brands = array();
					$brand_taxonomy = '';

					foreach ($brand_taxonomies as $taxonomy) {
						if (taxonomy_exists($taxonomy)) {
							$brand_taxonomy = $taxonomy;
							$brands = get_terms(array(
								'taxonomy' => $taxonomy,
								'hide_empty' => false,
							));
							break;
						}
					}

					if ($brands && !is_wp_error($brands) && !empty($brands)) :
						?>
						<li class="has-submenu">
							<a href="<?php echo home_url('/brands'); ?>" class="menu-item-link">
								Brands
							</a>
							<button class="submenu-toggle" aria-label="Toggle submenu">
								<svg width="12" height="12" viewBox="0 0 12 12" fill="none">
									<path d="M4 2L8 6L4 10" stroke="currentColor" stroke-width="1.5"/>
								</svg>
							</button>
							<ul class="submenu">
								<li><a href="<?php echo home_url('/brands'); ?>">All Brands</a></li>
								<?php foreach ($brands as $brand) : ?>
									<li><a href="<?php echo get_term_link($brand); ?>"><?php echo esc_html($brand->name); ?></a></li>
								<?php endforeach; ?>
							</ul>
						</li>
						<?php
					endif;
					?>

					
				</ul>
			</nav>

			<!-- My Account Section -->
			<div class="mobile-menu-section">
				<h3>MY ACCOUNT</h3>
				<ul>
					<?php if (is_user_logged_in()) : ?>
						<li><a href="<?php echo wc_get_page_permalink('myaccount'); ?>">My Account</a></li>
						<li><a href="<?php echo wp_logout_url(home_url()); ?>">Logout</a></li>
					<?php else : ?>
						<li><a href="<?php echo wc_get_page_permalink('myaccount'); ?>">Sign In</a></li>
					<?php endif; ?>
				</ul>
			</div>

			<!-- Additional Links -->
			<div class="mobile-menu-section">
				<ul>
					<li><a href="<?php echo home_url('/contact'); ?>">Contact us</a></li>
					<li><a href="<?php echo home_url('/stores'); ?>">Find a boutique</a></li>
				</ul>

				<!-- Accessibility Toggle -->
				<div class="accessibility-toggle">
					<label class="accessibility-label">
						<span>Accessibility: Better contrast</span>
						<input type="checkbox" id="high-contrast-toggle">
						<span class="toggle-switch"></span>
					</label>
				</div>
			</div>
		</div>
	</div>

	

	<!-- Top Black Banner -->
	<div class="top-banner">
		<div class="banner-content">
			<p>EXCLUSIVITY: Discover our new collection. <a href="<?php echo home_url('/shop'); ?>">Shop now.</a></p>
		</div>
		<button class="banner-close" aria-label="Close banner">×</button>
	</div>

	<header id="masthead" class="site-header dior-style-header">
		<div class="header-container">
			<div class="header-main">
				<button class="mobile-menu-toggle" aria-label="Menu">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none">
						<path d="M3 12H21M3 6H21M3 18H21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
					</svg>
				</button>

				<div class="site-branding">
					<?php
					the_custom_logo();
					if ( is_front_page() && is_home() ) :
						?>
						<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
						<?php
					else :
						?>
						<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
						<?php
					endif;
					?>
				</div><!-- .site-branding -->

				<div class="header-actions">
					<button class="search-toggle" aria-label="Search">
						<svg width="20" height="20" viewBox="0 0 20 20" fill="none">
							<circle cx="9" cy="9" r="7" stroke="currentColor" stroke-width="1.5"/>
							<path d="M14 14L18 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
						</svg>
					</button>
					<a href="<?php echo home_url('/my-account'); ?>" class="account-icon" aria-label="Account">
						<svg width="20" height="20" viewBox="0 0 20 20" fill="none">
							<circle cx="10" cy="7" r="4" stroke="currentColor" stroke-width="1.5"/>
							<path d="M4 18C4 14.6863 6.68629 12 10 12C13.3137 12 16 14.6863 16 18" stroke="currentColor" stroke-width="1.5"/>
						</svg>
					</a>
					<div class="cart-icon-wrapper">
						<a href="<?php echo wc_get_cart_url(); ?>" class="cart-icon" aria-label="Cart">
							<svg width="20" height="20" viewBox="0 0 20 20" fill="none">
								<path d="M4 6H16L15 15H5L4 6Z" stroke="currentColor" stroke-width="1.5"/>
								<path d="M7 6V4C7 2.89543 7.89543 2 9 2H11C12.1046 2 13 2.89543 13 4V6" stroke="currentColor" stroke-width="1.5"/>
							</svg>
							<?php if ( function_exists('WC') && WC()->cart->get_cart_contents_count() > 0 ) : ?>
								<span class="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
							<?php endif; ?>
						</a>

						<?php if ( function_exists('WC') && WC()->cart->get_cart_contents_count() > 0 ) : ?>
							<div class="mini-cart-dropdown">
								<div class="mini-cart-header">
									<h3>Your Cart <span class="mini-cart-count">(<?php echo WC()->cart->get_cart_contents_count(); ?> items)</span></h3>
								</div>

								<div class="mini-cart-items">
									<?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) :
										$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
										$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

										if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) :
											$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
											?>
											<div class="mini-cart-item">
												<div class="mini-cart-item-image">
													<?php
													$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
													if ( ! $product_permalink ) {
														echo $thumbnail;
													} else {
														printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail );
													}
													?>
												</div>

												<div class="mini-cart-item-details">
													<a href="<?php echo esc_url( $product_permalink ); ?>" class="mini-cart-item-name">
														<?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) ); ?>
													</a>

													<div class="mini-cart-item-meta">
														<span class="mini-cart-item-quantity">Qty: <?php echo $cart_item['quantity']; ?></span>
														<span class="mini-cart-item-price">
															<?php echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); ?>
														</span>
													</div>
												</div>

												<a href="<?php echo esc_url( wc_get_cart_remove_url( $cart_item_key ) ); ?>"
												   class="mini-cart-item-remove"
												   aria-label="Remove this item"
												   data-product_id="<?php echo esc_attr( $product_id ); ?>"
												   data-cart_item_key="<?php echo esc_attr( $cart_item_key ); ?>">
													×
												</a>
											</div>
											<?php
										endif;
									endforeach; ?>
								</div>

								<div class="mini-cart-footer">
									<div class="mini-cart-subtotal">
										<span class="mini-cart-subtotal-label">Subtotal:</span>
										<span class="mini-cart-subtotal-amount"><?php echo WC()->cart->get_cart_subtotal(); ?></span>
									</div>

									<div class="mini-cart-buttons">
										<a href="<?php echo wc_get_cart_url(); ?>" class="mini-cart-button mini-cart-view-cart">View Cart</a>
										<a href="<?php echo wc_get_checkout_url(); ?>" class="mini-cart-button mini-cart-checkout">Checkout</a>
									</div>
								</div>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div><!-- .header-main -->
		</div><!-- .header-container -->
	</header><!-- #masthead -->
