<?php
/**
 * The header for our theme
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 * @package kacosmetics
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e('Skip to content', 'kacosmetics'); ?></a>

	<!-- Mobile Menu Sidebar -->
	<div class="mobile-menu-overlay"></div>
	<div class="mobile-menu-sidebar">
		<div class="mobile-menu-header">
			<button class="mobile-menu-close" aria-label="<?php esc_attr_e('Close menu', 'kacosmetics'); ?>">&times;</button>
		</div>

		<div class="mobile-menu-content">
			<div class="mobile-menu-section">
				<ul>
					<li><a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"><?php esc_html_e('Shop', 'kacosmetics'); ?></a></li>
					<li><a href="<?php echo kac_url('brands/'); ?>"><?php esc_html_e('Brands', 'kacosmetics'); ?></a></li>
				</ul>
				<h3 style="margin-top: 20px;"><?php esc_html_e('MY ACCOUNT', 'kacosmetics'); ?></h3>
				<ul>
					<?php if (is_user_logged_in()) : ?>
						<li><a href="<?php echo wc_get_page_permalink('myaccount'); ?>"><?php esc_html_e('My Account', 'kacosmetics'); ?></a></li>
						<li><a href="<?php echo wp_logout_url(kac_url()); ?>"><?php esc_html_e('Logout', 'kacosmetics'); ?></a></li>
					<?php else : ?>
						<li><a href="<?php echo wc_get_page_permalink('myaccount'); ?>"><?php esc_html_e('Sign In', 'kacosmetics'); ?></a></li>
					<?php endif; ?>
				</ul>
			</div>

			<div class="mobile-menu-section">
				<ul>
					<li><a href="<?php echo kac_url('contact/'); ?>"><?php esc_html_e('Contact us', 'kacosmetics'); ?></a></li>
					<li><a href="<?php echo kac_url('stores/'); ?>"><?php esc_html_e('Find a boutique', 'kacosmetics'); ?></a></li>
				</ul>
			</div>
		</div>
	</div>

	<!-- Top Black Banner -->
	<div class="top-banner">
		<div class="banner-content">
			<p><?php esc_html_e('EXCLUSIVITY: Discover our new collection.', 'kacosmetics'); ?> <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"><?php esc_html_e('Shop now.', 'kacosmetics'); ?></a></p>
		</div>
		<button class="banner-close" aria-label="<?php esc_attr_e('Close banner', 'kacosmetics'); ?>">&times;</button>
	</div>

	<header id="masthead" class="site-header dior-style-header">
		<div class="header-container">
			<div class="header-main">
				<button class="mobile-menu-toggle" aria-label="<?php esc_attr_e('Menu', 'kacosmetics'); ?>">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none">
						<path d="M3 12H21M3 6H21M3 18H21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
					</svg>
				</button>

				<div class="site-branding">
					<?php the_custom_logo(); ?>
					<?php if (is_front_page() && is_home()) : ?>
						<h1 class="site-title"><a href="<?php echo kac_url(); ?>" rel="home"><?php bloginfo('name'); ?></a></h1>
					<?php else : ?>
						<p class="site-title"><a href="<?php echo kac_url(); ?>" rel="home"><?php bloginfo('name'); ?></a></p>
					<?php endif; ?>
				</div>

				<div class="header-actions">
					<?php if (function_exists('pll_the_languages')) : ?>
						<div class="language-switcher">
							<?php
							$languages = pll_the_languages(array(
								'show_flags' => 1,
								'show_names' => 0,
								'echo' => 0,
								'hide_current' => 0,
							));
							if ($languages) {
								echo '<ul class="lang-list">' . $languages . '</ul>';
							}
							?>
						</div>
					<?php endif; ?>

					<button class="search-toggle" aria-label="<?php esc_attr_e('Search', 'kacosmetics'); ?>">
						<svg width="20" height="20" viewBox="0 0 20 20" fill="none">
							<circle cx="9" cy="9" r="7" stroke="currentColor" stroke-width="1.5"/>
							<path d="M14 14L18 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
						</svg>
					</button>

					<a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" class="account-icon" aria-label="<?php esc_attr_e('Account', 'kacosmetics'); ?>">
						<svg width="20" height="20" viewBox="0 0 20 20" fill="none">
							<circle cx="10" cy="7" r="4" stroke="currentColor" stroke-width="1.5"/>
							<path d="M4 18C4 14.6863 6.68629 12 10 12C13.3137 12 16 14.6863 16 18" stroke="currentColor" stroke-width="1.5"/>
						</svg>
					</a>

					<div class="cart-icon-wrapper">
						<a href="<?php echo wc_get_cart_url(); ?>" class="cart-icon" aria-label="<?php esc_attr_e('Cart', 'kacosmetics'); ?>">
							<svg width="20" height="20" viewBox="0 0 20 20" fill="none">
								<path d="M4 6H16L15 15H5L4 6Z" stroke="currentColor" stroke-width="1.5"/>
								<path d="M7 6V4C7 2.89543 7.89543 2 9 2H11C12.1046 2 13 2.89543 13 4V6" stroke="currentColor" stroke-width="1.5"/>
							</svg>
							<?php if (function_exists('WC') && WC()->cart->get_cart_contents_count() > 0) : ?>
								<span class="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
							<?php endif; ?>
						</a>

						<?php if (function_exists('WC') && WC()->cart->get_cart_contents_count() > 0) : ?>
							<div class="mini-cart-dropdown">
								<div class="mini-cart-header">
									<h3><?php esc_html_e('Your Cart', 'kacosmetics'); ?> <span class="mini-cart-count">(<?php echo WC()->cart->get_cart_contents_count(); ?> <?php esc_html_e('items', 'kacosmetics'); ?>)</span></h3>
								</div>

								<div class="mini-cart-items">
									<?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) :
										$_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
										$product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

										if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) :
											$product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
											?>
											<div class="mini-cart-item">
												<div class="mini-cart-item-image">
													<?php
													$thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);
													if (!$product_permalink) {
														echo $thumbnail;
													} else {
														printf('<a href="%s">%s</a>', esc_url($product_permalink), $thumbnail);
													}
													?>
												</div>

												<div class="mini-cart-item-details">
													<a href="<?php echo esc_url($product_permalink); ?>" class="mini-cart-item-name">
														<?php echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key)); ?>
													</a>

													<div class="mini-cart-item-meta">
														<span class="mini-cart-item-quantity"><?php esc_html_e('Qty:', 'kacosmetics'); ?> <?php echo $cart_item['quantity']; ?></span>
														<span class="mini-cart-item-price">
															<?php echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key); ?>
														</span>
													</div>
												</div>

												<a href="<?php echo esc_url(wc_get_cart_remove_url($cart_item_key)); ?>"
												   class="mini-cart-item-remove"
												   aria-label="<?php esc_attr_e('Remove this item', 'kacosmetics'); ?>"
												   data-product_id="<?php echo esc_attr($product_id); ?>"
												   data-cart_item_key="<?php echo esc_attr($cart_item_key); ?>">
													&times;
												</a>
											</div>
										<?php endif;
									endforeach; ?>
								</div>

								<div class="mini-cart-footer">
									<div class="mini-cart-subtotal">
										<span class="mini-cart-subtotal-label"><?php esc_html_e('Subtotal:', 'kacosmetics'); ?></span>
										<span class="mini-cart-subtotal-amount"><?php echo WC()->cart->get_cart_subtotal(); ?></span>
									</div>

									<div class="mini-cart-buttons">
										<a href="<?php echo wc_get_cart_url(); ?>" class="mini-cart-button mini-cart-view-cart"><?php esc_html_e('View Cart', 'kacosmetics'); ?></a>
										<a href="<?php echo wc_get_checkout_url(); ?>" class="mini-cart-button mini-cart-checkout"><?php esc_html_e('Checkout', 'kacosmetics'); ?></a>
									</div>
								</div>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</header>
