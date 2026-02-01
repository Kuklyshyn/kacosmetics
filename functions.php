<?php
/**
 * kacosmetics functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package kacosmetics
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.5' );
}

/**
 * Get language-aware URL for internal pages (Polylang compatible)
 */
if ( ! function_exists( 'kac_url' ) ) {
	function kac_url( $path = '' ) {
		$base = home_url( '/' );
		$path = ltrim( $path, '/' );

		// Check if Polylang is active and get current language
		if ( function_exists( 'pll_current_language' ) ) {
			$current_lang = pll_current_language();
			$default_lang = pll_default_language();

			// If not default language (Ukrainian)
			if ( $current_lang && $current_lang !== $default_lang ) {
				$base = home_url( '/' . $current_lang . '/' );

				// Add -ua suffix to page slug if path is provided
				if ( $path && ! empty( $path ) ) {
					// Remove trailing slash, add suffix, restore slash
					$path = rtrim( $path, '/' );
					if ( substr( $path, -3 ) !== '-ua' ) {
						$path .= '-ua';
					}
					$path .= '/';
				}
			}
		}

		return esc_url( $base . $path );
	}
}

/**
 * Make WooCommerce pages work with Polylang translations
 */
if ( ! function_exists( 'kac_wc_polylang_page_id' ) ) {
	function kac_wc_polylang_page_id( $page_id ) {
		if ( function_exists( 'pll_get_post' ) && $page_id ) {
			$translated_id = pll_get_post( $page_id );
			if ( $translated_id ) {
				return $translated_id;
			}
		}
		return $page_id;
	}
	add_filter( 'woocommerce_get_cart_page_id', 'kac_wc_polylang_page_id' );
	add_filter( 'woocommerce_get_checkout_page_id', 'kac_wc_polylang_page_id' );
	add_filter( 'woocommerce_get_myaccount_page_id', 'kac_wc_polylang_page_id' );
	add_filter( 'woocommerce_get_shop_page_id', 'kac_wc_polylang_page_id' );
	add_filter( 'woocommerce_get_terms_page_id', 'kac_wc_polylang_page_id' );
}

/**
 * Force WooCommerce to use Polylang locale
 */
function kac_polylang_wc_locale( $locale ) {
	if ( function_exists( 'pll_current_language' ) ) {
		$lang = pll_current_language( 'locale' );
		if ( $lang ) {
			return $lang;
		}
	}
	return $locale;
}
add_filter( 'locale', 'kac_polylang_wc_locale', 100 );
add_filter( 'plugin_locale', 'kac_polylang_wc_locale', 100 );

/**
 * Force product_brand taxonomy to use "brands" slug for all languages
 */
function kac_fix_brand_taxonomy_slug() {
	global $wp_taxonomies;

	if ( isset( $wp_taxonomies['product_brand'] ) ) {
		// Override the rewrite slug to be "brands" for all languages
		$wp_taxonomies['product_brand']->rewrite = array(
			'slug'         => 'brands',
			'with_front'   => false,
			'hierarchical' => false,
		);
	}
}
add_action( 'init', 'kac_fix_brand_taxonomy_slug', 999 );

/**
 * Prevent Polylang from translating product_brand taxonomy slug
 */
function kac_prevent_brand_slug_translation( $translated_slugs ) {
	// Remove product_brand from translated slugs so it always uses "brands"
	if ( isset( $translated_slugs['product_brand'] ) ) {
		unset( $translated_slugs['product_brand'] );
	}
	return $translated_slugs;
}
add_filter( 'pll_translated_slugs', 'kac_prevent_brand_slug_translation', 999 );

/**
 * Force all brand term links to use "brands" slug
 */
function kac_fix_brand_term_link( $termlink, $term, $taxonomy ) {
	if ( $taxonomy === 'product_brand' ) {
		// Replace any translated slug (značka, бренд, etc) with "brands"
		$termlink = preg_replace( '#/(značka|бренд|product_brand|product-brand)/#', '/brands/', $termlink );
	}
	return $termlink;
}
add_filter( 'term_link', 'kac_fix_brand_term_link', 999, 3 );

/**
 * Flush rewrite rules when needed
 */
function kac_fix_brand_rewrite_rules() {
	$version = '2'; // Increment this to force flush again
	$current_version = get_option( 'kac_brand_slug_version' );

	// Flush if version changed
	if ( $current_version !== $version ) {
		flush_rewrite_rules( false );
		update_option( 'kac_brand_slug_version', $version );
	}

	// Flush rewrite rules on admin_init if needed
	if ( is_admin() && isset( $_GET['page'] ) && $_GET['page'] === 'mlang' ) {
		flush_rewrite_rules( false );
	}
}
add_action( 'admin_init', 'kac_fix_brand_rewrite_rules' );

/**
 * Make sure brand taxonomy template is loaded correctly
 * Handle brand URLs that might be 404 but should work
 */
function kac_brand_template_include( $template ) {
	// Check if this looks like a brand URL
	$request_uri = $_SERVER['REQUEST_URI'];
	if ( preg_match( '#/brands/([^/]+)/?$#', $request_uri, $matches ) ) {
		// Force load the brand taxonomy template
		$theme_template = locate_template( array( 'taxonomy-product_brand.php' ) );
		if ( $theme_template ) {
			// Tell WordPress this is a taxonomy page
			global $wp_query;
			$wp_query->is_tax = true;
			$wp_query->is_archive = true;
			$wp_query->is_404 = false;
			status_header( 200 );

			return $theme_template;
		}
	}

	// Standard brand taxonomy check
	if ( is_tax( 'product_brand' ) ) {
		$theme_template = locate_template( array( 'taxonomy-product_brand.php' ) );
		if ( $theme_template ) {
			return $theme_template;
		}
	}

	return $template;
}
add_filter( 'template_include', 'kac_brand_template_include', 99 );

/**
 * Custom translations for WooCommerce strings
 */
function kac_custom_translations( $translated, $text, $domain ) {
	if ( function_exists( 'pll_current_language' ) && pll_current_language() === 'ua' ) {
		$translations = array(
			// Various cases
			'Add coupons'        => 'Додати купон',
			'Add Coupons'        => 'Додати купон',
			'ADD COUPONS'        => 'Додати купон',
			'Free shipping'      => 'Безкоштовна доставка',
			'Free Shipping'      => 'Безкоштовна доставка',
			'FREE SHIPPING'      => 'Безкоштовна доставка',
			'Estimated total'    => 'Орієнтовна сума',
			'Estimated Total'    => 'Орієнтовна сума',
			'ESTIMATED TOTAL'    => 'Орієнтовна сума',
			'Subtotal'           => 'Проміжний підсумок',
			'Total'              => 'Всього',
			'Coupon code'        => 'Код купона',
			'Apply coupon'       => 'Застосувати купон',
			'Update cart'        => 'Оновити кошик',
			'Cart totals'        => 'Підсумки кошика',
			'Proceed to checkout' => 'Перейти до оформлення',
			// Filter
			'Price'              => 'Ціна',
			'PRICE'              => 'ЦІНА',
			'Filter'             => 'Фільтр',
			'FILTER'             => 'ФІЛЬТР',
			'Filter by price'    => 'Фільтрувати за ціною',
		);
		if ( isset( $translations[ $text ] ) ) {
			return $translations[ $text ];
		}
	}
	return $translated;
}
add_filter( 'gettext', 'kac_custom_translations', 999, 3 );
add_filter( 'gettext_woocommerce', 'kac_custom_translations', 999, 3 );
add_filter( 'gettext_woo-gutenberg-products-block', 'kac_custom_translations', 999, 3 );

/**
 * Translate WooCommerce Block strings
 */
function kac_translate_wc_block_strings( $content ) {
	if ( function_exists( 'pll_current_language' ) && pll_current_language() === 'ua' ) {
		$replacements = array(
			'Add coupons'     => 'Додати купон',
			'Free shipping'   => 'Безкоштовна доставка',
			'Estimated total' => 'Орієнтовна сума',
			'PRICE'           => 'ЦІНА',
			'Price'           => 'Ціна',
			'>Price<'         => '>Ціна<',
		);
		$content = str_replace( array_keys( $replacements ), array_values( $replacements ), $content );
	}
	return $content;
}
add_filter( 'the_content', 'kac_translate_wc_block_strings', 999 );
add_filter( 'render_block', 'kac_translate_wc_block_strings', 999 );


/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function kacosmetics_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on kacosmetics, use a find and replace
		* to change 'kacosmetics' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'kacosmetics', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'kacosmetics' ),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'kacosmetics_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'kacosmetics_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function kacosmetics_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'kacosmetics_content_width', 640 );
}
add_action( 'after_setup_theme', 'kacosmetics_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function kacosmetics_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'kacosmetics' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'kacosmetics' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);

	// Register Shop Filters Sidebar
	register_sidebar(
		array(
			'name'          => esc_html__( 'Shop Filters', 'kacosmetics' ),
			'id'            => 'shop-filters',
			'description'   => esc_html__( 'Add WooCommerce filter widgets here.', 'kacosmetics' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		)
	);
}
add_action( 'widgets_init', 'kacosmetics_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function kacosmetics_scripts() {
	wp_enqueue_style( 'kacosmetics-style', get_stylesheet_uri(), array(), _S_VERSION );
	wp_style_add_data( 'kacosmetics-style', 'rtl', 'replace' );

	wp_enqueue_script( 'kacosmetics-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true );

	// Enqueue mobile menu script
	wp_enqueue_script( 'kacosmetics-mobile-menu', get_template_directory_uri() . '/js/mobile-menu.js', array(), _S_VERSION, true );

	// Enqueue banner script
	wp_enqueue_script( 'kacosmetics-banner', get_template_directory_uri() . '/js/banner.js', array(), _S_VERSION, true );

	// Enqueue category tabs script for front page, New Arrivals template, shop page, category pages, and brand pages
	if ( is_front_page() || is_page_template( 'template-new-arrivals.php' ) || is_shop() || is_post_type_archive( 'product' ) || is_product_category() || is_tax( 'product_brand' ) ) {
		wp_enqueue_script( 'kacosmetics-category-tabs', get_template_directory_uri() . '/js/category-tabs.js', array(), _S_VERSION, true );
	}

	// Enqueue shop filters script for shop page, category pages, and brand pages
	if ( is_shop() || is_post_type_archive( 'product' ) || is_product_category() || is_tax( 'product_brand' ) ) {
		wp_enqueue_script( 'kacosmetics-shop-filters', get_template_directory_uri() . '/js/shop-filters.js', array(), _S_VERSION, true );
	}

	// Enqueue single product script for product pages
	if ( is_product() ) {
		wp_enqueue_script( 'kacosmetics-single-product', get_template_directory_uri() . '/js/single-product.js', array(), _S_VERSION, true );
	}

	// Enqueue cart styles and script for cart page
	if ( is_cart() ) {
		wp_enqueue_style( 'kacosmetics-cart-style', get_template_directory_uri() . '/css/cart-style.css', array(), _S_VERSION );
		wp_enqueue_script( 'kacosmetics-cart', get_template_directory_uri() . '/js/cart.js', array( 'jquery' ), _S_VERSION, true );
	}

	// Enqueue checkout styles for checkout page
	if ( is_checkout() ) {
		wp_enqueue_style( 'kacosmetics-checkout-style', get_template_directory_uri() . '/css/checkout-style.css', array(), _S_VERSION );
	}

	// Enqueue my account styles for my account page
	if ( is_account_page() ) {
		wp_enqueue_style( 'kacosmetics-my-account-style', get_template_directory_uri() . '/css/my-account-style.css', array(), _S_VERSION );
	}

	// Enqueue brands page styles
	if ( is_page_template( 'page-brands.php' ) ) {
		wp_enqueue_style( 'kacosmetics-brands-style', get_template_directory_uri() . '/css/brands-style.css', array(), _S_VERSION );
	}

	// Enqueue brand archive styles for brand taxonomy pages
	if ( is_tax( 'product_brand' ) ) {
		wp_enqueue_style( 'kacosmetics-brand-archive-style', get_template_directory_uri() . '/css/brand-archive-style.css', array(), _S_VERSION );
	}

	// Enqueue cart dropdown styles and script (global - appears in header on all pages)
	wp_enqueue_style( 'kacosmetics-cart-dropdown', get_template_directory_uri() . '/css/cart-dropdown.css', array(), _S_VERSION );
	wp_enqueue_script( 'kacosmetics-cart-dropdown', get_template_directory_uri() . '/js/cart-dropdown.js', array( 'jquery' ), _S_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'kacosmetics_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Add WooCommerce support
 */
function kacosmetics_add_woocommerce_support() {
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'kacosmetics_add_woocommerce_support' );

/**
 * Change number of related products output
 */
function kacosmetics_related_products_args( $args ) {
	$args['posts_per_page'] = 4;
	$args['columns'] = 4;
	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'kacosmetics_related_products_args' );

/**
 * Remove default WooCommerce product loop hooks for related products
 */
function kacosmetics_customize_related_products() {
	// Remove sale flash
	remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
	// Remove rating
	remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
	// Remove add to cart button
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
}
add_action( 'woocommerce_before_template_part', 'kacosmetics_customize_related_products' );






/**
 * Add custom body class for cart page
 */
function kacosmetics_cart_body_class( $classes ) {
	if ( is_cart() ) {
		$classes[] = 'kacosmetics-cart-page';
	}
	return $classes;
}
add_filter( 'body_class', 'kacosmetics_cart_body_class' );

/**
 * AJAX handler for removing item from cart
 */
function kacosmetics_remove_from_cart() {
	if ( isset( $_POST['cart_item_key'] ) ) {
		$cart_item_key = sanitize_text_field( $_POST['cart_item_key'] );

		if ( WC()->cart->remove_cart_item( $cart_item_key ) ) {
			wp_send_json_success( array(
				'message' => 'Item removed from cart',
				'cart_count' => WC()->cart->get_cart_contents_count()
			) );
		} else {
			wp_send_json_error( array(
				'message' => 'Failed to remove item from cart'
			) );
		}
	} else {
		wp_send_json_error( array(
			'message' => 'Invalid cart item key'
		) );
	}
}
add_action( 'wp_ajax_remove_from_cart', 'kacosmetics_remove_from_cart' );
add_action( 'wp_ajax_nopriv_remove_from_cart', 'kacosmetics_remove_from_cart' );
