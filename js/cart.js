/**
 * Cart Page JavaScript
 */

(function ($) {
  "use strict";

  $(document).ready(function () {
    // Add plus/minus buttons to quantity inputs
    $(".cart-item-quantity .quantity").each(function () {
      const $quantity = $(this);
      const $input = $quantity.find("input.qty");

      // Add minus button
      $quantity.prepend(
        '<button type="button" class="quantity-button minus" aria-label="Decrease quantity">−</button>',
      );

      // Add plus button
      $quantity.append(
        '<button type="button" class="quantity-button plus" aria-label="Increase quantity">+</button>',
      );
    });

    // Handle plus button click
    $(document).on("click", ".quantity-button.plus", function () {
      const $input = $(this).siblings("input.qty");
      const max = parseInt($input.attr("max")) || 999;
      let value = parseInt($input.val()) || 0;

      if (value < max) {
        $input.val(value + 1).trigger("change");
      }
    });

    // Handle minus button click
    $(document).on("click", ".quantity-button.minus", function () {
      const $input = $(this).siblings("input.qty");
      const min = parseInt($input.attr("min")) || 0;
      let value = parseInt($input.val()) || 0;

      if (value > min) {
        $input.val(value - 1).trigger("change");
      }
    });

    // Auto-update cart when quantity changes
    let updateTimer;
    $(".cart-item-quantity input.qty").on("change", function () {
      clearTimeout(updateTimer);
      const $button = $('button[name="update_cart"]');

      // Show visual feedback
      $button.addClass("updating").text("Updating...");

      updateTimer = setTimeout(function () {
        $button.trigger("click");
      }, 800);
    });

    // Smooth remove animation
    $(".cart-item-remove .remove-item").on("click", function (e) {
      e.preventDefault();
      const $item = $(this).closest(".cart-item");
      const url = $(this).attr("href");

      // Animate removal
      $item.css({
        opacity: "0",
        transform: "translateX(20px)",
        transition: "all 0.3s ease",
      });

      setTimeout(function () {
        window.location.href = url;
      }, 300);
    });

    // Add loading state to checkout button
    $(".checkout-button").on("click", function () {
      const $button = $(this);
      $button
        .text("Processing...")
        .css("opacity", "0.7")
        .prop("disabled", true);
    });

    // Quantity input validation
    $(".cart-item-quantity input.qty").on("input", function () {
      const min = parseInt($(this).attr("min")) || 0;
      const max = parseInt($(this).attr("max")) || 999;
      let value = parseInt($(this).val()) || min;

      if (value < min) {
        $(this).val(min);
      } else if (value > max) {
        $(this).val(max);
      }
    });

    // Prevent form submission on Enter key in quantity input
    $(".cart-item-quantity input.qty").on("keypress", function (e) {
      if (e.which === 13) {
        e.preventDefault();
        $(this).blur();
        $('button[name="update_cart"]').trigger("click");
      }
    });

    // Smooth scroll to messages
    if (
      $(".woocommerce-message, .woocommerce-error, .woocommerce-info").length
    ) {
      $("html, body").animate(
        {
          scrollTop:
            $(".woocommerce-message, .woocommerce-error, .woocommerce-info")
              .first()
              .offset().top - 100,
        },
        500,
      );
    }
  });
})(jQuery);
