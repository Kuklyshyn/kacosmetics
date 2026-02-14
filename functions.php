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
	define( '_S_VERSION', '2.4.2' );

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
 * Map Ukrainian category names to Slovak slugs
 * Since categories may not be properly linked in Polylang, we use a manual mapping
 */
function kac_get_category_slug_mapping() {
	return array(
		// Ukrainian slug => Slovak slug
		'догляд-за-тілом'       => 'starostlivost-o-telo',
		'догляд-за-обличчям'    => 'starostlivost-o-plet',
		'догляд-за-шкірою'      => 'starostlivost-o-plet',
		'парфуми'               => 'vone',
		// URL-decoded versions (just in case)
		'%D0%B4%D0%BE%D0%B3%D0%BB%D1%8F%D0%B4-%D0%B7%D0%B0-%D1%82%D1%96%D0%BB%D0%BE%D0%BC' => 'starostlivost-o-telo',
		// Additional variations
		'dohliad-za-tilom'      => 'starostlivost-o-telo',
		'dohliad-za-oblychchiam'=> 'starostlivost-o-plet',
		'dohliad-za-shkiroju'   => 'starostlivost-o-plet',
		'parfumy'               => 'vone',
	);
}

/**
 * Normalize slug for comparison (handle URL encoding and case)
 */
function kac_normalize_slug( $slug ) {
	$slug = urldecode( $slug );
	$slug = strtolower( $slug );
	$slug = trim( $slug );
	return $slug;
}

/**
 * Get the default language (Slovak) version of a category term
 * This helps when products are only in Slovak but we need to query them from Ukrainian pages
 */
function kac_get_default_lang_category( $term ) {
	if ( ! function_exists( 'pll_default_language' ) || ! function_exists( 'pll_current_language' ) ) {
		return $term;
	}

	$default_lang = pll_default_language();
	$current_lang = pll_current_language();

	// If already in default language, return as is
	if ( $current_lang === $default_lang ) {
		return $term;
	}

	// First try Polylang translation
	if ( function_exists( 'pll_get_term' ) ) {
		$default_term_id = pll_get_term( $term->term_id, $default_lang );

		if ( $default_term_id && $default_term_id !== $term->term_id ) {
			$default_term = get_term( $default_term_id, 'product_cat' );
			if ( $default_term && ! is_wp_error( $default_term ) ) {
				return $default_term;
			}
		}
	}

	// Fallback to manual mapping
	$mapping = kac_get_category_slug_mapping();
	$term_slug = strtolower( $term->slug );

	if ( isset( $mapping[ $term_slug ] ) ) {
		$slovak_term = get_term_by( 'slug', $mapping[ $term_slug ], 'product_cat' );
		if ( $slovak_term && ! is_wp_error( $slovak_term ) ) {
			return $slovak_term;
		}
	}

	return $term;
}

/**
 * Get default language category slug from any language category slug
 */
function kac_get_default_lang_category_slug( $slug ) {
	// First check manual mapping
	$mapping = kac_get_category_slug_mapping();
	$normalized_slug = kac_normalize_slug( $slug );

	// Check direct match
	if ( isset( $mapping[ $normalized_slug ] ) ) {
		return $mapping[ $normalized_slug ];
	}

	// Check all mapping keys with normalization
	foreach ( $mapping as $ua_slug => $sk_slug ) {
		if ( kac_normalize_slug( $ua_slug ) === $normalized_slug ) {
			return $sk_slug;
		}
	}

	if ( ! function_exists( 'pll_get_term' ) || ! function_exists( 'pll_default_language' ) ) {
		return $slug;
	}

	// Get term by slug
	$term = get_term_by( 'slug', $slug, 'product_cat' );
	if ( ! $term ) {
		// Try URL-decoded slug
		$term = get_term_by( 'slug', urldecode( $slug ), 'product_cat' );
	}
	if ( ! $term ) {
		return $slug;
	}

	$default_term = kac_get_default_lang_category( $term );
	return $default_term->slug;
}

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
				'No products found in this category.' => 'У цій категорії товари не знайдено.',
				'Buy'                => 'Купити',
				// Product categories
				'Vône'               => 'Парфуми',
				'Starostlivosť O Pleť' => 'Догляд за обличчям',
				'Starostlivosť O Telo' => 'Догляд за тілом',
				'Starostlivosť o pleť' => 'Догляд за обличчям',
				'Starostlivosť o telo' => 'Догляд за тілом',
				// Product tabs
				'Specifications'     => 'Характеристики',
				'Composition'        => 'Склад',
				// Footer
				'Customer Service'   => 'Обслуговування клієнтів',
				'Contact Us'         => 'Зв\'язатися з нами',
				'Privacy Policy'     => 'Політика конфіденційності',
				'Terms & Conditions' => 'Умови використання',
				'Shipping & Delivery' => 'Доставка',
				'Returns & Refunds'  => 'Повернення та обмін',
				'My Account'         => 'Мій акаунт',
				'Contact'            => 'Контакти',
				'Secure Payment:'    => 'Безпечна оплата:',
				'All rights reserved.' => 'Всі права захищені.',
				'ID:'                => 'ІПН:',
				'Tax ID:'            => 'ЄДРПОУ:',
				'VAT:'               => 'ПДВ:',
				'Privacy'            => 'Конфіденційність',
				'Terms'              => 'Умови',
				'Cookies'            => 'Cookies',
				'Bestseller'         => 'Бестселер',
				// 404 Page
				'Page not found'     => 'Сторінку не знайдено',
				'Sorry, the page you are looking for does not exist or has been moved.' => 'Вибачте, сторінка, яку ви шукаєте, не існує або була переміщена.',
				'Go to Homepage'     => 'Перейти на головну',
				'Browse Products'    => 'Переглянути товари',
				'Or try searching:'  => 'Або спробуйте пошук:',
				// Footer
				'Developed by'       => 'Розроблено',
				// Cookie Consent
				'Privacy Settings'   => 'Налаштування конфіденційності',
				'On our website and in the application, we use cookies and SDK (Software Development Kit) tools. Some of them are necessary, while others help us improve this website and your user experience. For ad personalization, we process personal data together with our partners using cookies and advertising identifiers. We also use these technologies for non-personalized advertising. Do you agree to the use of cookies and SDK tools?' => 'На нашому веб-сайті та в додатку ми використовуємо файли cookie та інструменти SDK (Software Development Kit). Деякі з них необхідні, тоді як інші допомагають нам покращити цей веб-сайт та ваш користувацький досвід. Для персоналізації реклами ми обробляємо персональні дані разом з нашими партнерами за допомогою файлів cookie та рекламних ідентифікаторів. Ми також використовуємо ці технології для неперсоналізованої реклами. Чи погоджуєтесь ви на використання файлів cookie та інструментів SDK?',
				'Cookie Information' => 'Інформація про cookies',
				'Accept and continue' => 'Прийняти та продовжити',
				'Cookie Settings'    => 'Налаштування cookie',
				'Only necessary cookies' => 'Тільки необхідні cookies',
				'Necessary cookies'  => 'Необхідні cookies',
				'(Required)'         => '(Обов\'язково)',
				'These cookies are essential for the website to function properly.' => 'Ці файли cookie необхідні для належного функціонування веб-сайту.',
				'Analytics cookies'  => 'Аналітичні cookies',
				'These cookies help us understand how visitors interact with our website.' => 'Ці файли cookie допомагають нам зрозуміти, як відвідувачі взаємодіють з нашим веб-сайтом.',
				'Marketing cookies'  => 'Маркетингові cookies',
				'These cookies are used to show you relevant ads and track their effectiveness.' => 'Ці файли cookie використовуються для показу вам релевантної реклами та відстеження її ефективності.',
				'Save settings'      => 'Зберегти налаштування',
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
				'No products found in this category.' => 'V tejto kategórii neboli nájdené žiadne produkty.',
				'Buy'                => 'Kúpiť',
				// Product tabs
				'Specifications'     => 'Vlastnosti',
				'Composition'        => 'Zloženie',
				// Footer
				'Customer Service'   => 'Zákaznícky servis',
				'Contact Us'         => 'Kontaktujte nás',
				'Privacy Policy'     => 'Ochrana súkromia',
				'Terms & Conditions' => 'Obchodné podmienky',
				'Shipping & Delivery' => 'Doprava a doručenie',
				'Returns & Refunds'  => 'Vrátenie a výmena',
				'My Account'         => 'Môj účet',
				'Contact'            => 'Kontakt',
				'Secure Payment:'    => 'Bezpečná platba:',
				'All rights reserved.' => 'Všetky práva vyhradené.',
				'ID:'                => 'IČO:',
				'Tax ID:'            => 'DIČ:',
				'VAT:'               => 'IČ DPH:',
				'Privacy'            => 'Súkromie',
				'Terms'              => 'Podmienky',
				'Cookies'            => 'Cookies',
				'Bestseller'         => 'Bestseller',
				// 404 Page
				'Page not found'     => 'Stránka nenájdená',
				'Sorry, the page you are looking for does not exist or has been moved.' => 'Prepáčte, stránka, ktorú hľadáte, neexistuje alebo bola presunutá.',
				'Go to Homepage'     => 'Prejsť na hlavnú',
				'Browse Products'    => 'Prehliadať produkty',
				'Or try searching:'  => 'Alebo skúste hľadať:',
				// Footer
				'Developed by'       => 'Vytvoril',
				// Cookie Consent
				'Privacy Settings'   => 'Nastavenie súkromia',
				'On our website and in the application, we use cookies and SDK (Software Development Kit) tools. Some of them are necessary, while others help us improve this website and your user experience. For ad personalization, we process personal data together with our partners using cookies and advertising identifiers. We also use these technologies for non-personalized advertising. Do you agree to the use of cookies and SDK tools?' => 'Na našich webových stránkach a v aplikácii používame súbory cookies a nástroje SDK (Software Development Kit). Niektoré z nich sú nevyhnutné, zatiaľ čo iné nám pomáhajú vylepšiť tento web a váš používateľský zážitok. Na personalizáciu reklám spracúvame spolu s našimi partnermi osobné údaje pomocou súborov cookie a reklamných identifikátorov. Tieto technológie používame aj na nepersonalizované reklamy. Súhlasíte s používaním súborov cookies a nástrojov SDK?',
				'Cookie Information' => 'Informácie o súboroch cookies',
				'Accept and continue' => 'Súhlasím a pokračovať',
				'Cookie Settings'    => 'Podrobné nastavenie cookies',
				'Only necessary cookies' => 'Iba nevyhnutné cookies',
				'Necessary cookies'  => 'Nevyhnutné cookies',
				'(Required)'         => '(Povinné)',
				'These cookies are essential for the website to function properly.' => 'Tieto súbory cookie sú nevyhnutné pre správne fungovanie webovej stránky.',
				'Analytics cookies'  => 'Analytické cookies',
				'These cookies help us understand how visitors interact with our website.' => 'Tieto súbory cookie nám pomáhajú pochopiť, ako návštevníci interagujú s našou webovou stránkou.',
				'Marketing cookies'  => 'Marketingové cookies',
				'These cookies are used to show you relevant ads and track their effectiveness.' => 'Tieto súbory cookie sa používajú na zobrazovanie relevantných reklám a sledovanie ich účinnosti.',
				'Save settings'      => 'Uložiť nastavenia',
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
 * Translate category names (for use in templates)
 */
function kac_translate_category_name( $name ) {
	if ( function_exists( 'pll_current_language' ) && pll_current_language() === 'ua' ) {
		$translations = array(
			'Vône'                 => 'Парфуми',
			'vône'                 => 'Парфуми',
			'VÔNE'                 => 'Парфуми',
			'Starostlivosť o pleť' => 'Догляд за обличчям',
			'Starostlivosť O Pleť' => 'Догляд за обличчям',
			'Starostlivosť o telo' => 'Догляд за тілом',
			'Starostlivosť O Telo' => 'Догляд за тілом',
		);

		if ( isset( $translations[ $name ] ) ) {
			return $translations[ $name ];
		}
	}
	return $name;
}

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

	// Enqueue cookie consent script (GDPR compliance)
	wp_enqueue_script( 'kacosmetics-cookie-consent', get_template_directory_uri() . '/js/cookie-consent.js', array(), _S_VERSION, true );

	// Enqueue category tabs script for front page, New Arrivals template, shop page, category pages, and brand pages
	if ( is_front_page() || is_page_template( 'template-new-arrivals.php' ) || is_shop() || is_post_type_archive( 'product' ) || is_product_category() || is_tax( 'product_brand' ) ) {
		wp_enqueue_script( 'kacosmetics-category-tabs', get_template_directory_uri() . '/js/category-tabs.js', array(), _S_VERSION, true );

		// Localize script for AJAX
		wp_localize_script( 'kacosmetics-category-tabs', 'kacCategoryAjax', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'kac_category_nonce' ),
		) );
	}

	// Enqueue hero banner styles and script for front page
	if ( is_front_page() ) {
		wp_enqueue_style( 'kacosmetics-hero-banner', get_template_directory_uri() . '/css/hero-banner.css', array(), _S_VERSION );
		wp_enqueue_script( 'kacosmetics-hero-banner', get_template_directory_uri() . '/js/hero-banner.js', array(), _S_VERSION, true );
		wp_enqueue_script( 'kacosmetics-bestseller-slider', get_template_directory_uri() . '/js/bestseller-slider.js', array(), _S_VERSION, true );
	}

	// Enqueue shop filters script for shop page, category pages, and brand pages
	if ( is_shop() || is_post_type_archive( 'product' ) || is_product_category() || is_tax( 'product_brand' ) ) {
		// Enqueue WooCommerce price slider scripts
		if ( function_exists( 'WC' ) ) {
			wp_enqueue_script( 'wc-price-slider' );
			wp_enqueue_script( 'jquery-ui-slider' );
			wp_enqueue_script( 'jquery-ui-touch-punch' );

			// Get min/max prices for the slider
			global $wpdb;
			$min_price = $wpdb->get_var( "SELECT MIN( CAST( meta_value AS DECIMAL(10,2) ) ) FROM {$wpdb->postmeta} WHERE meta_key = '_price' AND meta_value != ''" );
			$max_price = $wpdb->get_var( "SELECT MAX( CAST( meta_value AS DECIMAL(10,2) ) ) FROM {$wpdb->postmeta} WHERE meta_key = '_price' AND meta_value != ''" );

			$min_price = floor( $min_price );
			$max_price = ceil( $max_price );

			wp_localize_script( 'wc-price-slider', 'woocommerce_price_slider_params', array(
				'currency_format_num_decimals' => 0,
				'currency_format_symbol'       => get_woocommerce_currency_symbol(),
				'currency_format_decimal_sep'  => wc_get_price_decimal_separator(),
				'currency_format_thousand_sep' => wc_get_price_thousand_separator(),
				'currency_format'              => str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format() ),
				'min_price'                    => isset( $_GET['min_price'] ) ? esc_attr( $_GET['min_price'] ) : $min_price,
				'max_price'                    => isset( $_GET['max_price'] ) ? esc_attr( $_GET['max_price'] ) : $max_price,
			) );
		}

		wp_enqueue_script( 'kacosmetics-shop-filters', get_template_directory_uri() . '/js/shop-filters.js', array( 'jquery', 'jquery-ui-slider' ), _S_VERSION, true );

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

	// Add About Us & Social Media Section
	$wp_customize->add_section('kacosmetics_about_social', array(
		'title' => __('About Us & Social Media', 'kacosmetics'),
		'priority' => 32,
	));

	// About Us Text
	$wp_customize->add_setting('kacosmetics_about_text', array(
		'default' => 'K&A Cosmetics je slovenská kozmetická spoločnosť zameraná na kvalitné produkty pre starostlivosť o pleť a telo. Naším poslaním je prinášať vám tie najlepšie kozmetické produkty za dostupné ceny.',
		'sanitize_callback' => 'sanitize_textarea_field',
	));
	$wp_customize->add_control('kacosmetics_about_text', array(
		'label' => __('About Us Text', 'kacosmetics'),
		'section' => 'kacosmetics_about_social',
		'type' => 'textarea',
	));

	// Facebook URL
	$wp_customize->add_setting('kacosmetics_facebook_url', array(
		'default' => '',
		'sanitize_callback' => 'esc_url_raw',
	));
	$wp_customize->add_control('kacosmetics_facebook_url', array(
		'label' => __('Facebook URL', 'kacosmetics'),
		'section' => 'kacosmetics_about_social',
		'type' => 'url',
	));

	// Instagram URL
	$wp_customize->add_setting('kacosmetics_instagram_url', array(
		'default' => '',
		'sanitize_callback' => 'esc_url_raw',
	));
	$wp_customize->add_control('kacosmetics_instagram_url', array(
		'label' => __('Instagram URL', 'kacosmetics'),
		'section' => 'kacosmetics_about_social',
		'type' => 'url',
	));

	// TikTok URL
	$wp_customize->add_setting('kacosmetics_tiktok_url', array(
		'default' => '',
		'sanitize_callback' => 'esc_url_raw',
	));
	$wp_customize->add_control('kacosmetics_tiktok_url', array(
		'label' => __('TikTok URL', 'kacosmetics'),
		'section' => 'kacosmetics_about_social',
		'type' => 'url',
	));

	// YouTube URL
	$wp_customize->add_setting('kacosmetics_youtube_url', array(
		'default' => '',
		'sanitize_callback' => 'esc_url_raw',
	));
	$wp_customize->add_control('kacosmetics_youtube_url', array(
		'label' => __('YouTube URL', 'kacosmetics'),
		'section' => 'kacosmetics_about_social',
		'type' => 'url',
	));

	// Contact Form Settings Section
	$wp_customize->add_section('kacosmetics_contact_form', array(
		'title' => __('Contact Form', 'kacosmetics'),
		'priority' => 33,
	));

	// Contact Form ID
	$wp_customize->add_setting('kacosmetics_contact_form_id', array(
		'default' => '',
		'sanitize_callback' => 'sanitize_text_field',
	));
	$wp_customize->add_control('kacosmetics_contact_form_id', array(
		'label' => __('Contact Form ID', 'kacosmetics'),
		'description' => __('Enter the WPForms or Contact Form 7 form ID. Leave empty to use built-in form.', 'kacosmetics'),
		'section' => 'kacosmetics_contact_form',
		'type' => 'text',
	));
}
add_action('customize_register', 'kacosmetics_contact_customizer');

/**
 * Legal Pages Customizer Settings
 */
function kacosmetics_legal_pages_customizer($wp_customize) {
	// Section
	$wp_customize->add_section('kacosmetics_legal_pages', array(
		'title' => __('Legal Pages', 'kacosmetics'),
		'priority' => 35,
		'description' => __('Select pages for legal links in footer', 'kacosmetics'),
	));

	// Privacy Policy Page
	$wp_customize->add_setting('kacosmetics_privacy_page', array(
		'default' => '',
		'sanitize_callback' => 'absint',
	));
	$wp_customize->add_control('kacosmetics_privacy_page', array(
		'label' => __('Privacy Policy Page', 'kacosmetics'),
		'section' => 'kacosmetics_legal_pages',
		'type' => 'dropdown-pages',
	));

	// Terms & Conditions Page
	$wp_customize->add_setting('kacosmetics_terms_page', array(
		'default' => '',
		'sanitize_callback' => 'absint',
	));
	$wp_customize->add_control('kacosmetics_terms_page', array(
		'label' => __('Terms & Conditions Page', 'kacosmetics'),
		'section' => 'kacosmetics_legal_pages',
		'type' => 'dropdown-pages',
	));

	// Cookies Page
	$wp_customize->add_setting('kacosmetics_cookies_page', array(
		'default' => '',
		'sanitize_callback' => 'absint',
	));
	$wp_customize->add_control('kacosmetics_cookies_page', array(
		'label' => __('Cookies Page', 'kacosmetics'),
		'section' => 'kacosmetics_legal_pages',
		'type' => 'dropdown-pages',
	));

	// Shipping & Delivery Page
	$wp_customize->add_setting('kacosmetics_shipping_page', array(
		'default' => '',
		'sanitize_callback' => 'absint',
	));
	$wp_customize->add_control('kacosmetics_shipping_page', array(
		'label' => __('Shipping & Delivery Page', 'kacosmetics'),
		'section' => 'kacosmetics_legal_pages',
		'type' => 'dropdown-pages',
	));

	// Returns & Refunds Page
	$wp_customize->add_setting('kacosmetics_returns_page', array(
		'default' => '',
		'sanitize_callback' => 'absint',
	));
	$wp_customize->add_control('kacosmetics_returns_page', array(
		'label' => __('Returns & Refunds Page', 'kacosmetics'),
		'section' => 'kacosmetics_legal_pages',
		'type' => 'dropdown-pages',
	));
}
add_action('customize_register', 'kacosmetics_legal_pages_customizer');

/**
 * Get legal page URL with Polylang support
 */
function kac_get_legal_page_url($setting_name) {
	$page_id = get_theme_mod($setting_name, '');

	if (empty($page_id)) {
		return '';
	}

	// Get translated page if Polylang is active
	if (function_exists('pll_get_post')) {
		$translated_id = pll_get_post($page_id);
		if ($translated_id) {
			$page_id = $translated_id;
		}
	}

	return get_permalink($page_id);
}

/**
 * Register Customizer strings for Polylang translation
 */
function kacosmetics_register_polylang_strings() {
	if (function_exists('pll_register_string')) {
		// About Us text
		$about_text = get_theme_mod('kacosmetics_about_text', '');
		if (!empty($about_text)) {
			pll_register_string('about_us_text', $about_text, 'Theme: Contact Page', true);
		}

		// Contact information
		$contact_address = get_theme_mod('kacosmetics_contact_address', '');
		if (!empty($contact_address)) {
			pll_register_string('contact_address', $contact_address, 'Theme: Contact Page', true);
		}

		$working_hours = get_theme_mod('kacosmetics_working_hours', '');
		if (!empty($working_hours)) {
			pll_register_string('working_hours', $working_hours, 'Theme: Contact Page', true);
		}

		// Company information
		$company_register = get_theme_mod('kacosmetics_company_register', '');
		if (!empty($company_register)) {
			pll_register_string('company_register', $company_register, 'Theme: Contact Page', true);
		}
	}
}
add_action('init', 'kacosmetics_register_polylang_strings');

/**
 * Helper function to get translated theme mod
 */
function kacosmetics_get_translated_mod($mod_name, $default = '') {
	$value = get_theme_mod($mod_name, $default);
	if (function_exists('pll__') && !empty($value)) {
		return pll__($value);
	}
	return $value;
}

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

/**
 * Product Badges (Bestseller, Must Try)
 */

// Display badge on shop loop and single product
add_action('woocommerce_before_shop_loop_item_title', 'kacosmetics_display_product_badge', 10);
add_action('woocommerce_before_single_product_summary', 'kacosmetics_display_product_badge', 5);

function kacosmetics_display_product_badge() {
    global $product;

    if (!$product) {
        return;
    }

    $badge = get_post_meta($product->get_id(), '_product_badge', true);

    if ($badge) {
        $badge_labels = array(
            'bestseller' => __('Bestseller', 'kacosmetics'),
            'must-try' => __('Must Try', 'kacosmetics'),
            'new' => __('New', 'kacosmetics'),
            'sale' => __('Sale', 'kacosmetics'),
        );

        $label = isset($badge_labels[$badge]) ? $badge_labels[$badge] : ucfirst(str_replace('-', ' ', $badge));
        echo '<span class="product-badge badge-' . esc_attr($badge) . '">' . esc_html($label) . '</span>';
    }
}

// Add badge field to product admin
add_action('woocommerce_product_options_general_product_data', 'kacosmetics_add_badge_field');

function kacosmetics_add_badge_field() {
    woocommerce_wp_select(array(
        'id' => '_product_badge',
        'label' => __('Product Badge', 'kacosmetics'),
        'desc_tip' => true,
        'description' => __('Select a badge to display on this product.', 'kacosmetics'),
        'options' => array(
            '' => __('None', 'kacosmetics'),
            'bestseller' => __('Bestseller', 'kacosmetics'),
            'must-try' => __('Must Try', 'kacosmetics'),
            'new' => __('New', 'kacosmetics'),
        )
    ));
}

// Save badge field
add_action('woocommerce_process_product_meta', 'kacosmetics_save_badge_field');

function kacosmetics_save_badge_field($post_id) {
    $badge = isset($_POST['_product_badge']) ? sanitize_text_field($_POST['_product_badge']) : '';
    update_post_meta($post_id, '_product_badge', $badge);
}

/**
 * AJAX handler for loading category products with pagination
 */
add_action( 'wp_ajax_load_category_products', 'kac_load_category_products' );
add_action( 'wp_ajax_nopriv_load_category_products', 'kac_load_category_products' );

function kac_load_category_products() {
    // Verify nonce
    if ( ! wp_verify_nonce( $_POST['nonce'], 'kac_category_nonce' ) ) {
        wp_send_json_error( 'Invalid nonce' );
    }

    $category = sanitize_text_field( $_POST['category'] );
    $page = intval( $_POST['page'] );

    if ( empty( $category ) || $page < 1 ) {
        wp_send_json_error( 'Invalid parameters' );
    }

    // Get default language category slug for querying products
    $query_slug = kac_get_default_lang_category_slug( $category );

    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => 12,
        'paged'          => $page,
        'tax_query'      => array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => $query_slug,
            ),
        ),
        'orderby' => 'date',
        'order'   => 'DESC',
    );

    // Add price filter support
    if ( isset( $_POST['min_price'] ) || isset( $_POST['max_price'] ) ) {
        $args['meta_query'] = array( 'relation' => 'AND' );

        if ( isset( $_POST['min_price'] ) && $_POST['min_price'] !== '' ) {
            $args['meta_query'][] = array(
                'key'     => '_price',
                'value'   => floatval( $_POST['min_price'] ),
                'compare' => '>=',
                'type'    => 'NUMERIC',
            );
        }

        if ( isset( $_POST['max_price'] ) && $_POST['max_price'] !== '' ) {
            $args['meta_query'][] = array(
                'key'     => '_price',
                'value'   => floatval( $_POST['max_price'] ),
                'compare' => '<=',
                'type'    => 'NUMERIC',
            );
        }
    }

    $query = new WP_Query( $args );

    ob_start();

    if ( $query->have_posts() ) :
        while ( $query->have_posts() ) :
            $query->the_post();
            global $product;
            ?>
            <div class="product-card">
                <div class="product-badges">
                    <?php
                    $product_badge = get_post_meta( get_the_ID(), '_product_badge', true );
                    if ( $product_badge ) :
                        $badge_labels = array(
                            'bestseller' => __( 'Bestseller', 'kacosmetics' ),
                            'must-try'   => __( 'Must Try', 'kacosmetics' ),
                            'new'        => __( 'New', 'kacosmetics' ),
                        );
                        $label = isset( $badge_labels[ $product_badge ] ) ? $badge_labels[ $product_badge ] : ucfirst( str_replace( '-', ' ', $product_badge ) );
                        ?>
                        <span class="badge badge-<?php echo esc_attr( $product_badge ); ?>"><?php echo esc_html( $label ); ?></span>
                    <?php endif; ?>
                    <?php if ( get_post_meta( get_the_ID(), '_is_new', true ) ) : ?>
                        <span class="badge badge-new"><?php esc_html_e( 'New', 'kacosmetics' ); ?></span>
                    <?php endif; ?>
                    <?php if ( get_post_meta( get_the_ID(), '_is_exclusive', true ) ) : ?>
                        <span class="badge badge-exclusive"><?php esc_html_e( 'Exclusive', 'kacosmetics' ); ?></span>
                    <?php endif; ?>
                </div>

                <a href="<?php the_permalink(); ?>" class="product-image-link">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <?php the_post_thumbnail( 'large', array( 'class' => 'product-image' ) ); ?>
                    <?php else : ?>
                        <div class="product-image placeholder-image"></div>
                    <?php endif; ?>
                </a>

                <div class="product-info">
                    <h3 class="product-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h3>
                    <?php if ( $product && method_exists( $product, 'get_short_description' ) ) : ?>
                        <p class="product-description"><?php echo wp_trim_words( $product->get_short_description(), 8 ); ?></p>
                    <?php endif; ?>
                    <?php if ( $product && method_exists( $product, 'get_price_html' ) ) : ?>
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
        echo '<p class="no-products">' . esc_html__( 'No products found in this category.', 'kacosmetics' ) . '</p>';
    endif;

    $products_html = ob_get_clean();

    // Generate pagination
    ob_start();
    if ( $query->max_num_pages > 1 ) {
        echo paginate_links( array(
            'total'     => $query->max_num_pages,
            'current'   => $page,
            'prev_text' => '&larr;',
            'next_text' => '&rarr;',
            'type'      => 'list',
        ) );
    }
    $pagination_html = ob_get_clean();

    wp_send_json_success( array(
        'products'   => $products_html,
        'pagination' => $pagination_html,
        'max_pages'  => $query->max_num_pages,
        'current'    => $page,
    ) );
}

/**
 * Meta Pixel (Facebook Pixel) Integration
 * Events: PageView, ViewContent, AddToCart, InitiateCheckout, Purchase
 */

// Add Meta Pixel ID setting to Customizer
add_action('customize_register', 'kacosmetics_meta_pixel_customizer');
function kacosmetics_meta_pixel_customizer($wp_customize) {
    $wp_customize->add_section('kacosmetics_meta_pixel', array(
        'title'    => __('Meta Pixel', 'kacosmetics'),
        'priority' => 35,
    ));

    $wp_customize->add_setting('meta_pixel_id', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('meta_pixel_id', array(
        'label'       => __('Meta Pixel ID', 'kacosmetics'),
        'description' => __('Enter your Meta (Facebook) Pixel ID', 'kacosmetics'),
        'section'     => 'kacosmetics_meta_pixel',
        'type'        => 'text',
    ));
}

// Output Meta Pixel base code in head
add_action('wp_head', 'kacosmetics_meta_pixel_base_code', 1);
function kacosmetics_meta_pixel_base_code() {
    $pixel_id = get_theme_mod('meta_pixel_id', '');

    if (empty($pixel_id)) {
        return;
    }
    ?>
    <!-- Meta Pixel Code -->
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '<?php echo esc_js($pixel_id); ?>');
    fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=<?php echo esc_attr($pixel_id); ?>&ev=PageView&noscript=1"
    /></noscript>
    <!-- End Meta Pixel Code -->
    <?php
}

// ViewContent event on single product page
add_action('wp_footer', 'kacosmetics_meta_pixel_view_content');
function kacosmetics_meta_pixel_view_content() {
    $pixel_id = get_theme_mod('meta_pixel_id', '');

    if (empty($pixel_id) || !is_product()) {
        return;
    }

    global $product;

    if (!$product) {
        return;
    }

    $categories = wp_get_post_terms($product->get_id(), 'product_cat', array('fields' => 'names'));
    $category = !empty($categories) ? $categories[0] : '';
    ?>
    <script>
    fbq('track', 'ViewContent', {
        content_name: '<?php echo esc_js($product->get_name()); ?>',
        content_category: '<?php echo esc_js($category); ?>',
        content_ids: ['<?php echo esc_js($product->get_id()); ?>'],
        content_type: 'product',
        value: <?php echo esc_js($product->get_price()); ?>,
        currency: '<?php echo esc_js(get_woocommerce_currency()); ?>'
    });
    </script>
    <?php
}

// AddToCart event via JavaScript
add_action('wp_footer', 'kacosmetics_meta_pixel_add_to_cart');
function kacosmetics_meta_pixel_add_to_cart() {
    $pixel_id = get_theme_mod('meta_pixel_id', '');

    if (empty($pixel_id)) {
        return;
    }
    ?>
    <script>
    jQuery(document).ready(function($) {
        // Standard add to cart button
        $(document.body).on('added_to_cart', function(event, fragments, cart_hash, $button) {
            var productId = $button.data('product_id');
            var productName = $button.data('product_name') || '';
            var productPrice = $button.data('product_price') || 0;

            fbq('track', 'AddToCart', {
                content_ids: [productId],
                content_type: 'product',
                content_name: productName,
                value: parseFloat(productPrice),
                currency: '<?php echo esc_js(get_woocommerce_currency()); ?>'
            });
        });

        // Single product page add to cart
        $('form.cart').on('submit', function() {
            var productId = $(this).find('button[name="add-to-cart"]').val() || $('input[name="add-to-cart"]').val();
            var quantity = $(this).find('input[name="quantity"]').val() || 1;

            <?php if (is_product()) :
                global $product;
                if ($product) :
            ?>
            fbq('track', 'AddToCart', {
                content_ids: ['<?php echo esc_js($product->get_id()); ?>'],
                content_type: 'product',
                content_name: '<?php echo esc_js($product->get_name()); ?>',
                value: <?php echo esc_js($product->get_price()); ?> * parseInt(quantity),
                currency: '<?php echo esc_js(get_woocommerce_currency()); ?>'
            });
            <?php endif; endif; ?>
        });
    });
    </script>
    <?php
}

// InitiateCheckout event on checkout page
add_action('wp_footer', 'kacosmetics_meta_pixel_initiate_checkout');
function kacosmetics_meta_pixel_initiate_checkout() {
    $pixel_id = get_theme_mod('meta_pixel_id', '');

    if (empty($pixel_id) || !is_checkout() || is_order_received_page()) {
        return;
    }

    $cart = WC()->cart;
    if (!$cart) {
        return;
    }

    $product_ids = array();
    $product_names = array();

    foreach ($cart->get_cart() as $cart_item) {
        $product_ids[] = $cart_item['product_id'];
        $product_names[] = $cart_item['data']->get_name();
    }
    ?>
    <script>
    fbq('track', 'InitiateCheckout', {
        content_ids: <?php echo json_encode($product_ids); ?>,
        content_type: 'product',
        num_items: <?php echo esc_js($cart->get_cart_contents_count()); ?>,
        value: <?php echo esc_js($cart->get_cart_contents_total()); ?>,
        currency: '<?php echo esc_js(get_woocommerce_currency()); ?>'
    });
    </script>
    <?php
}

// Purchase event on thank you page
add_action('woocommerce_thankyou', 'kacosmetics_meta_pixel_purchase', 10, 1);
function kacosmetics_meta_pixel_purchase($order_id) {
    $pixel_id = get_theme_mod('meta_pixel_id', '');

    if (empty($pixel_id) || !$order_id) {
        return;
    }

    // Prevent duplicate tracking
    $tracked = get_post_meta($order_id, '_meta_pixel_tracked', true);
    if ($tracked) {
        return;
    }

    $order = wc_get_order($order_id);
    if (!$order) {
        return;
    }

    $product_ids = array();
    $contents = array();

    foreach ($order->get_items() as $item) {
        $product_id = $item->get_product_id();
        $product_ids[] = $product_id;
        $contents[] = array(
            'id' => $product_id,
            'quantity' => $item->get_quantity(),
        );
    }

    // Mark as tracked
    update_post_meta($order_id, '_meta_pixel_tracked', true);
    ?>
    <script>
    fbq('track', 'Purchase', {
        content_ids: <?php echo json_encode($product_ids); ?>,
        contents: <?php echo json_encode($contents); ?>,
        content_type: 'product',
        num_items: <?php echo esc_js($order->get_item_count()); ?>,
        value: <?php echo esc_js($order->get_total()); ?>,
        currency: '<?php echo esc_js($order->get_currency()); ?>'
    });
    </script>
    <?php
}