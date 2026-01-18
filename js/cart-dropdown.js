/**
 * Cart Dropdown - AJAX functionality
 */

(function($) {
	'use strict';

	/**
	 * Update cart dropdown via AJAX
	 */
	function updateMiniCart() {
		$.ajax({
			url: wc_cart_fragments_params.ajax_url,
			type: 'POST',
			data: {
				action: 'get_refreshed_fragments'
			},
			success: function(response) {
				if (response && response.fragments) {
					// Update cart fragments
					$.each(response.fragments, function(key, value) {
						$(key).replaceWith(value);
					});

					// Update cart count
					if (response.cart_hash) {
						var cartCount = $(response.fragments['.cart-icon .cart-count']).text();
						$('.cart-count').text(cartCount);
					}
				}
			}
		});
	}

	/**
	 * Handle remove item from mini cart
	 */
	$(document).on('click', '.mini-cart-item-remove', function(e) {
		e.preventDefault();

		var $removeButton = $(this);
		var cartItemKey = $removeButton.data('cart_item_key');
		var $cartItem = $removeButton.closest('.mini-cart-item');

		// Add loading state
		$cartItem.css('opacity', '0.5');

		$.ajax({
			url: wc_add_to_cart_params.ajax_url,
			type: 'POST',
			data: {
				action: 'remove_from_cart',
				cart_item_key: cartItemKey
			},
			success: function(response) {
				if (response.success) {
					// Remove the item from dropdown
					$cartItem.fadeOut(300, function() {
						$(this).remove();

						// Check if cart is empty
						if ($('.mini-cart-item').length === 0) {
							// Hide dropdown and reload page to update header
							$('.mini-cart-dropdown').fadeOut(300, function() {
								location.reload();
							});
						} else {
							// Update cart fragments
							updateMiniCart();
						}
					});
				} else {
					$cartItem.css('opacity', '1');
					alert('Error removing item from cart.');
				}
			},
			error: function() {
				$cartItem.css('opacity', '1');
				alert('Error removing item from cart.');
			}
		});
	});

	/**
	 * Update mini cart on 'added_to_cart' event
	 */
	$(document.body).on('added_to_cart', function() {
		updateMiniCart();
	});

	/**
	 * Update mini cart on 'removed_from_cart' event
	 */
	$(document.body).on('removed_from_cart', function() {
		updateMiniCart();
	});

	/**
	 * Update mini cart on 'wc_fragment_refresh' event
	 */
	$(document.body).on('wc_fragment_refresh', function() {
		updateMiniCart();
	});

})(jQuery);
