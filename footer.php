<?php
/**
 * The template for displaying the footer
 *
 * @package kacosmetics
 */

// Get theme mods for contact info
$phone = get_theme_mod('kacosmetics_contact_phone', '+421 123 456 789');
$email = get_theme_mod('kacosmetics_contact_email', 'info@kosmo.sk');
$address = get_theme_mod('kacosmetics_contact_address', '');
$working_hours = get_theme_mod('kacosmetics_working_hours', '');

// Company info
$company_name = get_theme_mod('kacosmetics_company_name', 'K&A Cosmetics s.r.o.');
$company_ico = get_theme_mod('kacosmetics_company_ico', '');
$company_dic = get_theme_mod('kacosmetics_company_dic', '');
$company_icdph = get_theme_mod('kacosmetics_company_icdph', '');

// Social media
$facebook = get_theme_mod('kacosmetics_facebook_url', '');
$instagram = get_theme_mod('kacosmetics_instagram_url', '');
$tiktok = get_theme_mod('kacosmetics_tiktok_url', '');
$youtube = get_theme_mod('kacosmetics_youtube_url', '');

// Get product categories
$footer_categories = get_terms(array(
    'taxonomy' => 'product_cat',
    'hide_empty' => true,
    'exclude' => get_option('default_product_cat'),
    'number' => 6,
));
?>

<footer id="colophon" class="site-footer">

    <!-- Main Footer -->
    <div class="footer-main">
        <div class="footer-container">

            <!-- Column 1: About / Logo -->
            <div class="footer-column footer-about">
                <div class="footer-logo">
                    <span class="footer-site-title"><?php bloginfo('name'); ?></span>
                </div>
                <p class="footer-description">
                    <?php echo esc_html(get_theme_mod('kacosmetics_about_text', __('Premium cosmetics and skincare products for your beauty routine.', 'kacosmetics'))); ?>
                </p>

                <!-- Social Media -->
                <?php if ($facebook || $instagram || $tiktok || $youtube) : ?>
                <div class="footer-social">
                    <?php if ($facebook) : ?>
                        <a href="<?php echo esc_url($facebook); ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                    <?php endif; ?>
                    <?php if ($instagram) : ?>
                        <a href="<?php echo esc_url($instagram); ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                    <?php endif; ?>
                    <?php if ($tiktok) : ?>
                        <a href="<?php echo esc_url($tiktok); ?>" target="_blank" rel="noopener noreferrer" aria-label="TikTok">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>
                        </a>
                    <?php endif; ?>
                    <?php if ($youtube) : ?>
                        <a href="<?php echo esc_url($youtube); ?>" target="_blank" rel="noopener noreferrer" aria-label="YouTube">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                        </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Column 2: Categories -->
            <div class="footer-column">
                <h4 class="footer-title"><?php esc_html_e('Categories', 'kacosmetics'); ?></h4>
                <ul class="footer-links">
                    <?php if ($footer_categories && !is_wp_error($footer_categories)) : ?>
                        <?php foreach ($footer_categories as $cat) : ?>
                            <li><a href="<?php echo esc_url(get_term_link($cat)); ?>"><?php echo esc_html($cat->name); ?></a></li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <li><a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"><?php esc_html_e('All Products', 'kacosmetics'); ?></a></li>
                </ul>
            </div>

            <!-- Column 3: Customer Service -->
            <div class="footer-column">
                <h4 class="footer-title"><?php esc_html_e('Customer Service', 'kacosmetics'); ?></h4>
                <ul class="footer-links">
                    <li><a href="<?php echo esc_url(kac_get_contact_page_url()); ?>"><?php esc_html_e('Contact Us', 'kacosmetics'); ?></a></li>
                    <li><a href="<?php echo esc_url(get_privacy_policy_url()); ?>"><?php esc_html_e('Privacy Policy', 'kacosmetics'); ?></a></li>
                    <li><a href="<?php echo esc_url(wc_get_page_permalink('terms')); ?>"><?php esc_html_e('Terms & Conditions', 'kacosmetics'); ?></a></li>
                    <li><a href="<?php echo esc_url(kac_url('shipping-delivery/')); ?>"><?php esc_html_e('Shipping & Delivery', 'kacosmetics'); ?></a></li>
                    <li><a href="<?php echo esc_url(kac_url('returns-refunds/')); ?>"><?php esc_html_e('Returns & Refunds', 'kacosmetics'); ?></a></li>
                    <li><a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>"><?php esc_html_e('My Account', 'kacosmetics'); ?></a></li>
                </ul>
            </div>

            <!-- Column 4: Contact Info -->
            <div class="footer-column footer-contact">
                <h4 class="footer-title"><?php esc_html_e('Contact', 'kacosmetics'); ?></h4>
                <ul class="footer-contact-list">
                    <?php if ($address) : ?>
                        <li>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            <span><?php echo nl2br(esc_html($address)); ?></span>
                        </li>
                    <?php endif; ?>
                    <?php if ($phone) : ?>
                        <li>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                            <a href="tel:<?php echo esc_attr(preg_replace('/\s+/', '', $phone)); ?>"><?php echo esc_html($phone); ?></a>
                        </li>
                    <?php endif; ?>
                    <?php if ($email) : ?>
                        <li>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                            <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>
                        </li>
                    <?php endif; ?>
                    <?php if ($working_hours) : ?>
                        <li>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            <span><?php echo nl2br(esc_html($working_hours)); ?></span>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>

        </div>
    </div>

    <!-- Payment Methods -->
    <div class="footer-payments">
        <div class="footer-container">
            <span class="payments-label"><?php esc_html_e('Secure Payment:', 'kacosmetics'); ?></span>
            <div class="payment-icons">
                <span class="payment-icon payment-visa">VISA</span>
                <span class="payment-icon payment-mastercard">
                    <span class="mc-circles"></span>
                    mastercard
                </span>
                <span class="payment-icon payment-applepay">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/></svg>
                    Pay
                </span>
                <span class="payment-icon payment-googlepay">
                    <svg viewBox="0 0 24 24" width="14" height="14"><path fill="#4285F4" d="M12.24 10.285V14.4h6.806c-.275 1.765-2.056 5.174-6.806 5.174-4.095 0-7.439-3.389-7.439-7.574s3.345-7.574 7.439-7.574c2.33 0 3.891.989 4.785 1.849l3.254-3.138C18.189 1.186 15.479 0 12.24 0c-6.635 0-12 5.365-12 12s5.365 12 12 12c6.926 0 11.52-4.869 11.52-11.726 0-.788-.085-1.39-.189-1.989H12.24z"/></svg>
                    Pay
                </span>
            </div>
        </div>
    </div>

    <!-- Bottom Footer -->
    <div class="footer-bottom">
        <div class="footer-container">
            <div class="footer-copyright">
                <p>&copy; <?php echo date('Y'); ?> <?php echo esc_html($company_name); ?>. <?php esc_html_e('All rights reserved.', 'kacosmetics'); ?></p>
                <?php if ($company_ico || $company_dic || $company_icdph) : ?>
                    <p class="company-info">
                        <?php if ($company_ico) : ?>
                            <span><?php esc_html_e('ID:', 'kacosmetics'); ?> <?php echo esc_html($company_ico); ?></span>
                        <?php endif; ?>
                        <?php if ($company_dic) : ?>
                            <span><?php esc_html_e('Tax ID:', 'kacosmetics'); ?> <?php echo esc_html($company_dic); ?></span>
                        <?php endif; ?>
                        <?php if ($company_icdph) : ?>
                            <span><?php esc_html_e('VAT:', 'kacosmetics'); ?> <?php echo esc_html($company_icdph); ?></span>
                        <?php endif; ?>
                    </p>
                <?php endif; ?>
            </div>
            <div class="footer-bottom-links">
                <a href="<?php echo esc_url(get_privacy_policy_url()); ?>"><?php esc_html_e('Privacy', 'kacosmetics'); ?></a>
                <a href="<?php echo esc_url(wc_get_page_permalink('terms')); ?>"><?php esc_html_e('Terms', 'kacosmetics'); ?></a>
                <a href="<?php echo esc_url(kac_url('cookies/')); ?>"><?php esc_html_e('Cookies', 'kacosmetics'); ?></a>
            </div>
            <div class="footer-developed-by">
                <?php esc_html_e('Developed by', 'kacosmetics'); ?> <a href="https://omnicode.sk/sk" target="_blank" rel="noopener noreferrer">Omnicode</a>
            </div>
        </div>
    </div>

</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
