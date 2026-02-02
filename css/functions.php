
/**
 * Clean brand names from unknown characters for mobile devices
 */
function kac_clean_brand_name($name) {
	// Remove invisible characters and zero-width spaces
	$name = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $name);
	
	// Remove any non-printable characters except basic punctuation
	$name = preg_replace('/[^\p{L}\p{N}\p{P}\p{Zs}]/u', '', $name);
	
	// Normalize whitespace
	$name = preg_replace('/\s+/', ' ', $name);
	
	return trim($name);
}

/**
 * Filter brand names before output
 */
add_filter('term_name', function($name, $term) {
	if (isset($term->taxonomy) && $term->taxonomy === 'product_brand') {
		return kac_clean_brand_name($name);
	}
	return $name;
}, 10, 2);
