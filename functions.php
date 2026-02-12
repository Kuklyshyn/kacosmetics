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
	define( '_S_VERSION', '1.0.10' );
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
	if ( function_exists( 'pll_current_language' ) ) {
		$lang = pll_current_language();

		if ( $lang === 'ua' ) {
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
				// Archive / Shop
				'All Products'       => 'Усі товари',
				'Filters'            => 'Фільтри',
				'Categories'         => 'Категорії',
				'Brands'             => 'Бренди',
				'New'                => 'Новинка',
				'Exclusive'          => 'Ексклюзив',
				'No products found.' => 'Товари не знайдено.',
				'Buy'                => 'Купити',
				// Product tabs
				'Specifications'     => 'Характеристики',
				'Composition'        => 'Склад',
			);
			if ( isset( $translations[ $text ] ) ) {
				return $translations[ $text ];
			}
		}

		if ( $lang !== 'ua' ) {
			$translations = array(
				// Archive / Shop
				'All Products'       => 'Všetky produkty',
				'Filters'            => 'Filtre',
				'Categories'         => 'Kategórie',
				'Brands'             => 'Značky',
				'New'                => 'Novinka',
				'Exclusive'          => 'Exkluzívne',
				'No products found.' => 'Neboli nájdené žiadne produkty.',
				'Buy'                => 'Kúpiť',
				// Product tabs
				'Specifications'     => 'Vlastnosti',
				'Composition'        => 'Zloženie',
			);
			if ( isset( $translations[ $text ] ) ) {
				return $translations[ $text ];
			}
		}
	}
	return $translated;
}
add_filter( 'gettext', 'kac_custom_translations', 999, 3 );
add_filter( 'gettext_woocommerce', 'kac_custom_translations', 999, 3 );
add_filter( 'gettext_woo-gutenberg-products-block', 'kac_custom_translations', 999, 3 );
add_filter( 'gettext_kacosmetics', 'kac_custom_translations', 999, 3 );

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

	// Enqueue hero banner styles and script for front page
	if ( is_front_page() ) {
		wp_enqueue_style( 'kacosmetics-hero-banner', get_template_directory_uri() . '/css/hero-banner.css', array(), _S_VERSION );
		wp_enqueue_script( 'kacosmetics-hero-banner', get_template_directory_uri() . '/js/hero-banner.js', array(), _S_VERSION, true );
	}

	// Enqueue shop filters script for shop page, category pages, and brand pages
	if ( is_shop() || is_post_type_archive( 'product' ) || is_product_category() || is_tax( 'product_brand' ) ) {
		wp_enqueue_script( 'kacosmetics-shop-filters', get_template_directory_uri() . '/js/shop-filters.js', array( 'jquery' ), _S_VERSION, true );

		// Pass WooCommerce settings to JavaScript
		if ( function_exists( 'get_woocommerce_currency_symbol' ) ) {
			wp_localize_script( 'kacosmetics-shop-filters', 'kacShopFilters', array(
				'currencySymbol'   => get_woocommerce_currency_symbol(),
				'currencyPosition' => get_option( 'woocommerce_currency_pos', 'left' ),
				'priceDecimals'    => wc_get_price_decimals(),
				'decimalSeparator' => wc_get_price_decimal_separator(),
				'thousandSeparator' => wc_get_price_thousand_separator(),
			) );
		}
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

	// Enqueue contact page styles
	if ( is_page_template( 'page-contact.php' ) ) {
		wp_enqueue_style( 'kacosmetics-contact-style', get_template_directory_uri() . '/css/contact-style.css', array(), _S_VERSION );
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
 * Add Specifications tab to product page
 */
function kac_specifications_product_tab( $tabs ) {
	global $product;

	if ( $product && $product->has_attributes() ) {
		$tabs['specifications'] = array(
			'title'    => esc_html__( 'Specifications', 'kacosmetics' ),
			'priority' => 15,
			'callback' => 'kac_specifications_tab_content',
		);
	}

	return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'kac_specifications_product_tab' );

function kac_specifications_tab_content() {
	global $product;

	if ( ! $product ) {
		return;
	}

	$attributes = $product->get_attributes();

	if ( ! $attributes ) {
		return;
	}

	echo '<table class="shop_attributes specifications-table">';
	foreach ( $attributes as $attribute ) {
		$name = wc_attribute_label( $attribute->get_name() );

		if ( $attribute->is_taxonomy() ) {
			$values = wc_get_product_terms( $product->get_id(), $attribute->get_name(), array( 'fields' => 'names' ) );
			$value  = implode( ', ', $values );
		} else {
			$value = implode( ', ', $attribute->get_options() );
		}

		if ( $value ) {
			echo '<tr><th>' . esc_html( $name ) . '</th><td>' . esc_html( $value ) . '</td></tr>';
		}
	}
	echo '</table>';
}

/**
 * Add Composition (Zlozenie) tab to product page
 */
function kac_composition_product_tab( $tabs ) {
	global $product;

	if ( ! $product ) {
		return $tabs;
	}

	$composition = kac_get_composition( $product->get_id() );

	if ( ! empty( $composition ) ) {
		$tabs['composition'] = array(
			'title'    => esc_html__( 'Composition', 'kacosmetics' ),
			'priority' => 12, // After description (10), before specifications (15)
			'callback' => 'kac_composition_tab_content',
		);
	}

	return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'kac_composition_product_tab' );

/**
 * Get composition value from various possible meta keys
 */
function kac_get_composition( $product_id ) {
	// Try different possible meta keys
	$possible_keys = array(
		'zlozenie',
		'_zlozenie',
		'Zlozenie',
		'_Zlozenie',
		'Zlozenie(INCI)',
		'_Zlozenie(INCI)',
		'zlozenie_inci',
		'_zlozenie_inci',
		'composition',
		'_composition',
		'ingredients',
		'_ingredients',
		'inci',
		'_inci',
	);

	foreach ( $possible_keys as $key ) {
		$value = get_post_meta( $product_id, $key, true );
		if ( ! empty( $value ) ) {
			return $value;
		}
	}

	return '';
}

function kac_composition_tab_content() {
	global $product;

	if ( ! $product ) {
		return;
	}

	$composition = kac_get_composition( $product->get_id() );

	if ( ! empty( $composition ) ) {
		echo '<div class="product-composition">';
		echo wp_kses_post( wpautop( $composition ) );
		echo '</div>';
	}
}

/**
 * Add Zlozenie (INCI) field to product edit page in admin
 */
function kac_add_zlozenie_field() {
	global $post;

	echo '<div class="options_group">';

	woocommerce_wp_textarea_input( array(
		'id'          => 'zlozenie',
		'label'       => __( 'Zlozenie (INCI)', 'kacosmetics' ),
		'placeholder' => __( 'Enter product composition/ingredients...', 'kacosmetics' ),
		'description' => __( 'Product composition or INCI ingredients list. Will be displayed in a separate tab on the product page.', 'kacosmetics' ),
		'desc_tip'    => true,
		'rows'        => 5,
	) );

	echo '</div>';
}
add_action( 'woocommerce_product_options_general_product_data', 'kac_add_zlozenie_field' );

/**
 * Save Zlozenie (INCI) field
 */
function kac_save_zlozenie_field( $post_id ) {
	$zlozenie = isset( $_POST['zlozenie'] ) ? wp_kses_post( $_POST['zlozenie'] ) : '';
	update_post_meta( $post_id, 'zlozenie', $zlozenie );
}
add_action( 'woocommerce_process_product_meta', 'kac_save_zlozenie_field' );

/**
 * Add Zlozenie field to WooCommerce CSV Import mapping options
 */
function kac_add_zlozenie_import_column( $options ) {
	$options['zlozenie'] = __( 'Zlozenie (INCI)', 'kacosmetics' );
	return $options;
}
add_filter( 'woocommerce_csv_product_import_mapping_options', 'kac_add_zlozenie_import_column' );

/**
 * Add default column mapping for Zlozenie
 */
function kac_add_zlozenie_import_default_column( $columns ) {
	$columns[ __( 'Zlozenie (INCI)', 'kacosmetics' ) ] = 'zlozenie';
	$columns['zlozenie'] = 'zlozenie';
	$columns['Zlozenie'] = 'zlozenie';
	$columns['Zlozenie(INCI)'] = 'zlozenie';
	$columns['INCI'] = 'zlozenie';
	return $columns;
}
add_filter( 'woocommerce_csv_product_import_mapping_default_columns', 'kac_add_zlozenie_import_default_column' );

/**
 * Process Zlozenie field during import
 */
function kac_process_zlozenie_import( $object, $data ) {
	if ( ! empty( $data['zlozenie'] ) ) {
		$object->update_meta_data( 'zlozenie', wp_kses_post( $data['zlozenie'] ) );
	}
	return $object;
}
add_filter( 'woocommerce_product_import_pre_insert_product_object', 'kac_process_zlozenie_import', 10, 2 );

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

/**
 * Add Contact Information Settings to WordPress Customizer
 */
function kacosmetics_contact_customizer($wp_customize) {
	// Add Contact Information Section
	$wp_customize->add_section('kacosmetics_contact_info', array(
		'title' => __('Contact Information', 'kacosmetics'),
		'priority' => 30,
	));

	// Phone
	$wp_customize->add_setting('kacosmetics_contact_phone', array(
		'default' => '+421 123 456 789',
		'sanitize_callback' => 'sanitize_text_field',
	));
	$wp_customize->add_control('kacosmetics_contact_phone', array(
		'label' => __('Phone Number', 'kacosmetics'),
		'section' => 'kacosmetics_contact_info',
		'type' => 'text',
	));

	// Email
	$wp_customize->add_setting('kacosmetics_contact_email', array(
		'default' => 'info@kosmo.sk',
		'sanitize_callback' => 'sanitize_email',
	));
	$wp_customize->add_control('kacosmetics_contact_email', array(
		'label' => __('Email Address', 'kacosmetics'),
		'section' => 'kacosmetics_contact_info',
		'type' => 'email',
	));

	// Address
	$wp_customize->add_setting('kacosmetics_contact_address', array(
		'default' => 'Adresa ulica 123, 811 01 Bratislava, Slovensko',
		'sanitize_callback' => 'sanitize_textarea_field',
	));
	$wp_customize->add_control('kacosmetics_contact_address', array(
		'label' => __('Address', 'kacosmetics'),
		'section' => 'kacosmetics_contact_info',
		'type' => 'textarea',
	));

	// Working Hours
	$wp_customize->add_setting('kacosmetics_working_hours', array(
		'default' => "Pondelok - Piatok: 9:00 - 18:00\nSobota: 10:00 - 14:00\nNedeľa: Zatvorené",
		'sanitize_callback' => 'sanitize_textarea_field',
	));
	$wp_customize->add_control('kacosmetics_working_hours', array(
		'label' => __('Working Hours', 'kacosmetics'),
		'section' => 'kacosmetics_contact_info',
		'type' => 'textarea',
	));

	// Map Latitude
	$wp_customize->add_setting('kacosmetics_map_latitude', array(
		'default' => '48.1486',
		'sanitize_callback' => 'sanitize_text_field',
	));
	$wp_customize->add_control('kacosmetics_map_latitude', array(
		'label' => __('Map Latitude', 'kacosmetics'),
		'section' => 'kacosmetics_contact_info',
		'type' => 'text',
	));

	// Map Longitude
	$wp_customize->add_setting('kacosmetics_map_longitude', array(
		'default' => '17.1077',
		'sanitize_callback' => 'sanitize_text_field',
	));
	$wp_customize->add_control('kacosmetics_map_longitude', array(
		'label' => __('Map Longitude', 'kacosmetics'),
		'section' => 'kacosmetics_contact_info',
		'type' => 'text',
	));

	// Add Company Information Section
	$wp_customize->add_section('kacosmetics_company_info', array(
		'title' => __('Company Information', 'kacosmetics'),
		'priority' => 31,
	));

	// Company Name
	$wp_customize->add_setting('kacosmetics_company_name', array(
		'default' => 'K&A Cosmetics s.r.o.',
		'sanitize_callback' => 'sanitize_text_field',
	));
	$wp_customize->add_control('kacosmetics_company_name', array(
		'label' => __('Company Name', 'kacosmetics'),
		'section' => 'kacosmetics_company_info',
		'type' => 'text',
	));

	// IČO
	$wp_customize->add_setting('kacosmetics_company_ico', array(
		'default' => '12345678',
		'sanitize_callback' => 'sanitize_text_field',
	));
	$wp_customize->add_control('kacosmetics_company_ico', array(
		'label' => __('IČO (Company ID)', 'kacosmetics'),
		'section' => 'kacosmetics_company_info',
		'type' => 'text',
	));

	// DIČ
	$wp_customize->add_setting('kacosmetics_company_dic', array(
		'default' => '1234567890',
		'sanitize_callback' => 'sanitize_text_field',
	));
	$wp_customize->add_control('kacosmetics_company_dic', array(
		'label' => __('DIČ (Tax ID)', 'kacosmetics'),
		'section' => 'kacosmetics_company_info',
		'type' => 'text',
	));

	// IČ DPH
	$wp_customize->add_setting('kacosmetics_company_icdph', array(
		'default' => 'SK1234567890',
		'sanitize_callback' => 'sanitize_text_field',
	));
	$wp_customize->add_control('kacosmetics_company_icdph', array(
		'label' => __('IČ DPH (VAT ID)', 'kacosmetics'),
		'section' => 'kacosmetics_company_info',
		'type' => 'text',
	));

	// Company Register
	$wp_customize->add_setting('kacosmetics_company_register', array(
		'default' => 'Obchodný register Okresného súdu Bratislava I, oddiel: Sro, vložka č. 12345/B',
		'sanitize_callback' => 'sanitize_textarea_field',
	));
	$wp_customize->add_control('kacosmetics_company_register', array(
		'label' => __('Company Register', 'kacosmetics'),
		'section' => 'kacosmetics_company_info',
		'type' => 'textarea',
	));
}
add_action('customize_register', 'kacosmetics_contact_customizer');

/**
 * Handle Contact Form Submission
 */
function kacosmetics_handle_contact_form() {
	// Verify nonce
	if (!isset($_POST['contact_nonce']) || !wp_verify_nonce($_POST['contact_nonce'], 'kacosmetics_contact_form')) {
		wp_die(__('Security check failed', 'kacosmetics'));
	}

	// Sanitize form data
	$name = sanitize_text_field($_POST['contact_name']);
	$email = sanitize_email($_POST['contact_email']);
	$phone = sanitize_text_field($_POST['contact_phone']);
	$subject = sanitize_text_field($_POST['contact_subject']);
	$message = sanitize_textarea_field($_POST['contact_message']);

	// Validate required fields
	if (empty($name) || empty($email) || empty($subject) || empty($message)) {
		wp_redirect(add_query_arg('contact', 'error', wp_get_referer()));
		exit;
	}

	// Send email to site admin
	$to = get_theme_mod('kacosmetics_contact_email', get_option('admin_email'));
	$email_subject = sprintf(__('Contact Form: %s', 'kacosmetics'), $subject);
	$email_message = sprintf(
		__("New contact form submission:\n\nName: %s\nEmail: %s\nPhone: %s\nSubject: %s\n\nMessage:\n%s", 'kacosmetics'),
		$name,
		$email,
		$phone,
		$subject,
		$message
	);

	$headers = array(
		'From: ' . $name . ' <' . $email . '>',
		'Reply-To: ' . $email,
	);

	// Send email
	$sent = wp_mail($to, $email_subject, $email_message, $headers);

	// Redirect with success or error message
	if ($sent) {
		wp_redirect(add_query_arg('contact', 'success', wp_get_referer()));
	} else {
		wp_redirect(add_query_arg('contact', 'error', wp_get_referer()));
	}
	exit;
}
add_action('admin_post_kacosmetics_contact_form', 'kacosmetics_handle_contact_form');
add_action('admin_post_nopriv_kacosmetics_contact_form', 'kacosmetics_handle_contact_form');

/**
 * Flush rewrite rules for contact page
 */
function kac_flush_contact_page_rewrite() {
	$version = '3'; // Increment to force flush
	$current = get_option('kac_contact_flush_version');
	
	if ($current !== $version) {
		flush_rewrite_rules(false);
		update_option('kac_contact_flush_version', $version);
	}
}
add_action('init', 'kac_flush_contact_page_rewrite');

/**
 * Get Contact Page URL with Polylang support
 */
function kac_get_contact_page_url() {
	// Try to find contact page
	$contact_page = get_page_by_path('contact');
	
	if (!$contact_page) {
		// Fallback to kac_url
		return kac_url('contact/');
	}
	
	// Get translated version if Polylang is active
	if (function_exists('pll_get_post')) {
		$translated_id = pll_get_post($contact_page->ID);
		if ($translated_id) {
			return get_permalink($translated_id);
		}
	}
	
	return get_permalink($contact_page->ID);
}

/**
 * Hero Banner Customizer Settings
 */
function kacosmetics_hero_banner_customizer($wp_customize) {
	// Hero Banner Section
	$wp_customize->add_section('kacosmetics_hero_banner', array(
		'title'    => __('Hero Banner', 'kacosmetics'),
		'priority' => 25,
	));

	// Number of slides
	$wp_customize->add_setting('hero_banner_count', array(
		'default'           => 1,
		'sanitize_callback' => 'absint',
	));
	$wp_customize->add_control('hero_banner_count', array(
		'label'       => __('Number of Slides', 'kacosmetics'),
		'description' => __('Choose 1-5 slides. Save and refresh to see new slide options.', 'kacosmetics'),
		'section'     => 'kacosmetics_hero_banner',
		'type'        => 'select',
		'choices'     => array(
			0 => __('Disabled', 'kacosmetics'),
			1 => '1',
			2 => '2',
			3 => '3',
			4 => '4',
			5 => '5',
		),
	));

	// Autoplay setting
	$wp_customize->add_setting('hero_banner_autoplay', array(
		'default'           => true,
		'sanitize_callback' => 'wp_validate_boolean',
	));
	$wp_customize->add_control('hero_banner_autoplay', array(
		'label'   => __('Autoplay Slider', 'kacosmetics'),
		'section' => 'kacosmetics_hero_banner',
		'type'    => 'checkbox',
	));

	// Autoplay interval
	$wp_customize->add_setting('hero_banner_interval', array(
		'default'           => 5000,
		'sanitize_callback' => 'absint',
	));
	$wp_customize->add_control('hero_banner_interval', array(
		'label'       => __('Autoplay Interval (ms)', 'kacosmetics'),
		'section'     => 'kacosmetics_hero_banner',
		'type'        => 'number',
		'input_attrs' => array(
			'min'  => 2000,
			'max'  => 10000,
			'step' => 500,
		),
	));

	// Banner height
	$wp_customize->add_setting('hero_banner_height', array(
		'default'           => '500',
		'sanitize_callback' => 'sanitize_text_field',
	));
	$wp_customize->add_control('hero_banner_height', array(
		'label'       => __('Banner Height (px)', 'kacosmetics'),
		'section'     => 'kacosmetics_hero_banner',
		'type'        => 'number',
		'input_attrs' => array(
			'min'  => 200,
			'max'  => 800,
			'step' => 50,
		),
	));

	// Mobile banner height
	$wp_customize->add_setting('hero_banner_height_mobile', array(
		'default'           => '300',
		'sanitize_callback' => 'sanitize_text_field',
	));
	$wp_customize->add_control('hero_banner_height_mobile', array(
		'label'       => __('Mobile Banner Height (px)', 'kacosmetics'),
		'section'     => 'kacosmetics_hero_banner',
		'type'        => 'number',
		'input_attrs' => array(
			'min'  => 150,
			'max'  => 500,
			'step' => 25,
		),
	));

	// Individual slide settings
	$slide_count = get_theme_mod('hero_banner_count', 1);
	for ($i = 1; $i <= 5; $i++) {
		// Slide Image
		$wp_customize->add_setting("hero_slide_{$i}_image", array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		));
		$wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, "hero_slide_{$i}_image", array(
			'label'   => sprintf(__('Slide %d - Image', 'kacosmetics'), $i),
			'section' => 'kacosmetics_hero_banner',
		)));

		// Slide Mobile Image
		$wp_customize->add_setting("hero_slide_{$i}_image_mobile", array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		));
		$wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, "hero_slide_{$i}_image_mobile", array(
			'label'       => sprintf(__('Slide %d - Mobile Image (optional)', 'kacosmetics'), $i),
			'description' => __('Leave empty to use main image', 'kacosmetics'),
			'section'     => 'kacosmetics_hero_banner',
		)));

		// Slide Link
		$wp_customize->add_setting("hero_slide_{$i}_link", array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		));
		$wp_customize->add_control("hero_slide_{$i}_link", array(
			'label'   => sprintf(__('Slide %d - Link URL', 'kacosmetics'), $i),
			'section' => 'kacosmetics_hero_banner',
			'type'    => 'url',
		));

		// Slide Title (optional overlay text)
		$wp_customize->add_setting("hero_slide_{$i}_title", array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		));
		$wp_customize->add_control("hero_slide_{$i}_title", array(
			'label'   => sprintf(__('Slide %d - Title (optional)', 'kacosmetics'), $i),
			'section' => 'kacosmetics_hero_banner',
			'type'    => 'text',
		));

		// Slide Subtitle
		$wp_customize->add_setting("hero_slide_{$i}_subtitle", array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		));
		$wp_customize->add_control("hero_slide_{$i}_subtitle", array(
			'label'   => sprintf(__('Slide %d - Subtitle (optional)', 'kacosmetics'), $i),
			'section' => 'kacosmetics_hero_banner',
			'type'    => 'text',
		));

		// Slide Button Text
		$wp_customize->add_setting("hero_slide_{$i}_button", array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		));
		$wp_customize->add_control("hero_slide_{$i}_button", array(
			'label'   => sprintf(__('Slide %d - Button Text (optional)', 'kacosmetics'), $i),
			'section' => 'kacosmetics_hero_banner',
			'type'    => 'text',
		));
	}
}
add_action('customize_register', 'kacosmetics_hero_banner_customizer');

/**
 * Get Hero Banner Slides
 */
function kacosmetics_get_hero_slides() {
	$slide_count = get_theme_mod('hero_banner_count', 1);
	$slides = array();

	if ($slide_count < 1) {
		return $slides;
	}

	for ($i = 1; $i <= $slide_count; $i++) {
		$image = get_theme_mod("hero_slide_{$i}_image", '');
		if (!empty($image)) {
			$slides[] = array(
				'image'        => $image,
				'image_mobile' => get_theme_mod("hero_slide_{$i}_image_mobile", ''),
				'link'         => get_theme_mod("hero_slide_{$i}_link", ''),
				'title'        => get_theme_mod("hero_slide_{$i}_title", ''),
				'subtitle'     => get_theme_mod("hero_slide_{$i}_subtitle", ''),
				'button'       => get_theme_mod("hero_slide_{$i}_button", ''),
			);
		}
	}

	return $slides;
}

// Автоматично вибрати безкоштовну доставку
add_action( 'wp_footer', 'force_free_shipping_default', 999 );
function force_free_shipping_default() {
    if ( is_cart() || is_checkout() ) {
        ?>
        <script>
        jQuery(document).ready(function($){
            function selectFreeShipping() {
                // Шукаємо всі radio buttons з доставкою
                var freeShip = $('input[type="radio"]').filter(function() {
                    var label = $(this).closest('label').text().toLowerCase();
                    return label.includes('free') || label.includes('безкоштовн');
                });
                
                if (freeShip.length > 0 && !freeShip.is(':checked')) {
                    freeShip.first().prop('checked', true).trigger('change');
                    $('body').trigger('update_checkout');
                }
            }
            
            // Виконати зараз
            selectFreeShipping();
            
            // При оновленні кошика/checkout
            $(document.body).on('updated_cart_totals updated_checkout', selectFreeShipping);
            
            // Додатково через пів секунди
            setTimeout(selectFreeShipping, 500);
        });
        </script>
        <?php
    }
}

// Автоматично вибрати самовивіз (Local Pickup)
add_action( 'wp_footer', 'auto_select_local_pickup', 999 );
function auto_select_local_pickup() {
    if ( is_checkout() ) {
        ?>
        <script>
        jQuery(document).ready(function($){

            
            function selectLocalPickup() {
                // Знаходимо кнопки вибору доставки
                var deliveryButtons = $('.wc-block-checkout__shipping-option, [class*="shipping-option"], button[class*="shipping"]');
                
                
                
                // Шукаємо кнопку "Osobné vyzdvihnutie"
                var pickupButton = deliveryButtons.filter(function() {
                    var text = $(this).text().toLowerCase();
                    return text.includes('osobné') || text.includes('vyzdvih') || text.includes('pickup');
                });
                
                if (pickupButton.length > 0 && !pickupButton.hasClass('is-active')) {
                   
                    pickupButton.first().click();
                    return true;
                } else if (pickupButton.hasClass('is-active')) {
              
                    return true;
                }
                
               
                return false;
            }
            
            // Спроби
            var attempts = 0;
            function trySelect() {
                attempts++;
               
                
                if (selectLocalPickup()) {
                    console.log('✅ SUCCESS!');
                } else if (attempts < 15) {
                    setTimeout(trySelect, 500);
                }
            }
            
            setTimeout(trySelect, 500);
            
            // При оновленні checkout
            $(document.body).on('updated_checkout', function() {
               
                setTimeout(selectLocalPickup, 300);
            });
        });
        </script>
        <?php
    }
}

// ОСТАТОЧНЕ РІШЕННЯ: Хак для WooCommerce Store API
add_action( 'rest_api_init', 'disable_postcode_validation_completely' );
function disable_postcode_validation_completely() {
    // Видалити валідацію з полів
    add_filter( 'woocommerce_default_address_fields', function( $fields ) {
        if ( isset( $fields['postcode'] ) ) {
            $fields['postcode']['required'] = false;
            $fields['postcode']['validate'] = array();
        }
        return $fields;
    }, 9999 );
    
    // Для Store API endpoints
    remove_all_filters( 'woocommerce_rest_check_permissions' );
    add_filter( 'woocommerce_rest_check_permissions', '__return_true', 9999 );
}

// Модифікувати запит перед обробкою
add_filter( 'rest_request_before_callbacks', 'modify_checkout_request_data', 10, 3 );
function modify_checkout_request_data( $response, $handler, $request ) {
    $route = $request->get_route();
    
    if ( strpos( $route, 'checkout' ) !== false || strpos( $route, 'orders' ) !== false ) {
        $body = $request->get_json_params();
        
        if ( isset( $body['billing_address'] ) && empty( $body['billing_address']['postcode'] ) ) {
            $body['billing_address']['postcode'] = '82104';
        }
        
        if ( isset( $body['shipping_address'] ) && empty( $body['shipping_address']['postcode'] ) ) {
            $body['shipping_address']['postcode'] = '82104';
        }
        
        $request->set_body( json_encode( $body ) );
    }
    
    return $response;
}

// Автоматично вибрати самовивіз при завантаженні (але дозволити змінити)
add_action( 'wp_footer', 'auto_select_local_pickup_once', 999 );
function auto_select_local_pickup_once() {
    if ( is_checkout() ) {
        ?>
        <script>
        jQuery(document).ready(function($){
            console.log('=== PICKUP AUTO-SELECT (ONE TIME) ===');
            
            var userHasInteracted = false; // Прапорець чи користувач вже клікав
            
            function selectPickup() {
                // Якщо користувач вже вибирав - не чіпаємо
                if (userHasInteracted) {
                    console.log('⏸️ Користувач вже вибирав доставку, пропускаємо');
                    return true;
                }
                
                var shippingOptions = $('.wc-block-checkout__shipping-method-option');
                
                if (shippingOptions.length === 0) {
                    console.log('⏳ Опції доставки ще не завантажені');
                    return false;
                }
                
                console.log('✓ Знайдено опцій:', shippingOptions.length);
                
                var pickupOption = null;
                
                shippingOptions.each(function() {
                    var text = $(this).text().toLowerCase();
                    if (text.includes('osobné') || text.includes('vyzdvih') || text.includes('pickup')) {
                        pickupOption = $(this);
                        return false;
                    }
                });
                
                if (pickupOption && !pickupOption.hasClass('wc-block-checkout__shipping-method-option--selected')) {
                    console.log('✓ Вибираю самовивіз автоматично...');
                    pickupOption.click();
                    return true;
                } else if (pickupOption && pickupOption.hasClass('wc-block-checkout__shipping-method-option--selected')) {
                    console.log('✓ Самовивіз вже обраний');
                    return true;
                }
                
                return false;
            }
            
            // Відстежуємо клік користувача на будь-яку опцію доставки
            $(document).on('click', '.wc-block-checkout__shipping-method-option', function() {
                console.log('👆 Користувач вибрав доставку вручну');
                userHasInteracted = true; // Більше не втручаємось
            });
            
            // Спроби вибрати самовивіз (тільки якщо користувач ще не клікав)
            var attempts = 0;
            function trySelect() {
                if (userHasInteracted) {
                    return; // Припинити спроби
                }
                
                attempts++;
                console.log('Спроба #' + attempts);
                
                if (selectPickup()) {
                    console.log('✅ SUCCESS: Самовивіз обрано автоматично');
                } else if (attempts < 10) {
                    setTimeout(trySelect, 500);
                }
            }
            
            // Почати через 500ms
            setTimeout(trySelect, 500);
        });
        </script>
        <?php
    }
}