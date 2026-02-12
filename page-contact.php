<?php
/**
 * Template Name: Contact Page
 *
 * The template for displaying contact page
 *
 * @package kacosmetics
 */

get_header();

// Get contact information from theme options
$contact_phone = get_theme_mod('kacosmetics_contact_phone', '+421 123 456 789');
$contact_email = get_theme_mod('kacosmetics_contact_email', 'info@kosmo.sk');
$contact_address = kacosmetics_get_translated_mod('kacosmetics_contact_address', 'Adresa ulica 123, 811 01 Bratislava, Slovensko');

// Company data (required for Slovakia)
$company_name = get_theme_mod('kacosmetics_company_name', 'K&A Cosmetics s.r.o.');
$company_ico = get_theme_mod('kacosmetics_company_ico', '12345678');
$company_dic = get_theme_mod('kacosmetics_company_dic', '1234567890');
$company_icdph = get_theme_mod('kacosmetics_company_icdph', 'SK1234567890');
$company_register = kacosmetics_get_translated_mod('kacosmetics_company_register', 'Obchodný register Okresného súdu Bratislava I, oddiel: Sro, vložka č. 12345/B');

// About Us & Social Media (with Polylang translation support)
$about_text = kacosmetics_get_translated_mod('kacosmetics_about_text', 'K&A Cosmetics je slovenská kozmetická spoločnosť zameraná na kvalitné produkty pre starostlivosť o pleť a telo. Naším poslaním je prinášať vám tie najlepšie kozmetické produkty za dostupné ceny.');
$facebook_url = get_theme_mod('kacosmetics_facebook_url', '');
$instagram_url = get_theme_mod('kacosmetics_instagram_url', '');
$tiktok_url = get_theme_mod('kacosmetics_tiktok_url', '');
$youtube_url = get_theme_mod('kacosmetics_youtube_url', '');

// Working hours (with Polylang translation support)
$working_hours = kacosmetics_get_translated_mod('kacosmetics_working_hours', "Pondelok - Piatok: 9:00 - 18:00\nSobota: 10:00 - 14:00\nNedeľa: Zatvorené");

// Map coordinates
$map_latitude = get_theme_mod('kacosmetics_map_latitude', '48.1486');
$map_longitude = get_theme_mod('kacosmetics_map_longitude', '17.1077');
?>

<div id="primary" class="content-area contact-page">
    <main id="main" class="site-main">

        <!-- Page Header -->
        <div class="contact-page-header">
            <h1 class="contact-page-title"><?php esc_html_e('Contact Us', 'kacosmetics'); ?></h1>
            <p class="contact-page-subtitle"><?php esc_html_e('We\'d love to hear from you. Get in touch with us!', 'kacosmetics'); ?></p>
        </div>

        <div class="contact-container">

            <!-- Contact Information -->
            <div class="contact-info-section">
                <div class="contact-info-grid">

                    <!-- Contact Details -->
                    <div class="contact-info-card">
                        <div class="contact-info-icon">
                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
                                <path d="M26 22.4V26.4C26 26.9304 25.7893 27.4391 25.4142 27.8142C25.0391 28.1893 24.5304 28.4 24 28.4H8C7.46957 28.4 6.96086 28.1893 6.58579 27.8142C6.21071 27.4391 6 26.9304 6 26.4V22.4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M22 10.4L16 4.4L10 10.4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M16 4.4V19.4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <h3><?php esc_html_e('Address', 'kacosmetics'); ?></h3>
                        <p><?php echo nl2br(esc_html($contact_address)); ?></p>
                    </div>

                    <!-- Phone -->
                    <div class="contact-info-card">
                        <div class="contact-info-icon">
                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
                                <path d="M28.4 22.56V26.56C28.4013 26.9313 28.3226 27.2989 28.1695 27.6382C28.0163 27.9776 27.7918 28.2809 27.5102 28.5291C27.2286 28.7773 26.8962 28.9648 26.5352 29.0792C26.1743 29.1937 25.793 29.2323 25.416 29.192C21.3304 28.7479 17.4063 27.3547 13.9679 25.1281C10.7693 23.0881 8.11168 20.4305 6.07196 17.232C3.83998 13.7808 2.44631 9.84071 2.00796 5.744C1.96771 5.36812 2.00597 4.98793 2.11965 4.62783C2.23333 4.26773 2.41968 3.93587 2.66669 3.65412C2.9137 3.37237 3.21537 3.14712 3.5532 2.99269C3.89103 2.83827 4.25729 2.75821 4.62796 2.76H8.62796C9.26263 2.75396 9.87862 2.98373 10.3574 3.40577C10.8363 3.82782 11.1438 4.41028 11.224 5.04C11.3746 6.29953 11.6635 7.54005 12.0859 8.74C12.2699 9.24467 12.3106 9.79126 12.2033 10.3181C12.096 10.8449 11.8452 11.3311 11.4799 11.72L9.79196 13.408C11.6348 16.7354 14.4246 19.5252 17.752 21.368L19.44 19.68C19.8289 19.3147 20.3151 19.0639 20.8419 18.9566C21.3687 18.8493 21.9153 18.89 22.42 19.074C23.6199 19.4964 24.8605 19.7853 26.12 19.936C26.7553 20.0169 27.3424 20.3298 27.7663 20.8165C28.1902 21.3032 28.4182 21.9296 28.4 22.56Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <h3><?php esc_html_e('Phone', 'kacosmetics'); ?></h3>
                        <p><a href="tel:<?php echo esc_attr(str_replace(' ', '', $contact_phone)); ?>"><?php echo esc_html($contact_phone); ?></a></p>
                    </div>

                    <!-- Email -->
                    <div class="contact-info-card">
                        <div class="contact-info-icon">
                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
                                <path d="M5.6 6.4H26.4C27.84 6.4 29 7.56 29 9V23C29 24.44 27.84 25.6 26.4 25.6H5.6C4.16 25.6 3 24.44 3 23V9C3 7.56 4.16 6.4 5.6 6.4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M29 9L16 17.2L3 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <h3><?php esc_html_e('Email', 'kacosmetics'); ?></h3>
                        <p><a href="mailto:<?php echo esc_attr($contact_email); ?>"><?php echo esc_html($contact_email); ?></a></p>
                    </div>

                    <!-- Working Hours -->
                    <div class="contact-info-card">
                        <div class="contact-info-icon">
                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
                                <circle cx="16" cy="16" r="12" stroke="currentColor" stroke-width="2"/>
                                <path d="M16 8V16L21 19" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <h3><?php esc_html_e('Working Hours', 'kacosmetics'); ?></h3>
                        <p><?php echo nl2br(esc_html($working_hours)); ?></p>
                    </div>

                </div>
            </div>

            <!-- About Us Section -->
            <div class="about-us-section">
                <h2><?php esc_html_e('About Us', 'kacosmetics'); ?></h2>
                <div class="about-us-content">
                    <p><?php echo esc_html($about_text); ?></p>

                    <?php if ($facebook_url || $instagram_url || $tiktok_url || $youtube_url) : ?>
                    <div class="social-links">
                        <span class="social-links-label"><?php esc_html_e('Follow us:', 'kacosmetics'); ?></span>
                        <div class="social-icons">
                            <?php if ($facebook_url) : ?>
                            <a href="<?php echo esc_url($facebook_url); ?>" target="_blank" rel="noopener noreferrer" class="social-icon social-facebook" aria-label="Facebook">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </a>
                            <?php endif; ?>

                            <?php if ($instagram_url) : ?>
                            <a href="<?php echo esc_url($instagram_url); ?>" target="_blank" rel="noopener noreferrer" class="social-icon social-instagram" aria-label="Instagram">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                </svg>
                            </a>
                            <?php endif; ?>

                            <?php if ($tiktok_url) : ?>
                            <a href="<?php echo esc_url($tiktok_url); ?>" target="_blank" rel="noopener noreferrer" class="social-icon social-tiktok" aria-label="TikTok">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/>
                                </svg>
                            </a>
                            <?php endif; ?>

                            <?php if ($youtube_url) : ?>
                            <a href="<?php echo esc_url($youtube_url); ?>" target="_blank" rel="noopener noreferrer" class="social-icon social-youtube" aria-label="YouTube">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                </svg>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Company Information -->
            <div class="company-info-section">
                <h2><?php esc_html_e('Company Information', 'kacosmetics'); ?></h2>
                <div class="company-info-grid">
                    <div class="company-info-item">
                        <strong><?php esc_html_e('Company Name:', 'kacosmetics'); ?></strong>
                        <span><?php echo esc_html($company_name); ?></span>
                    </div>
                    <div class="company-info-item">
                        <strong><?php esc_html_e('IČO:', 'kacosmetics'); ?></strong>
                        <span><?php echo esc_html($company_ico); ?></span>
                    </div>
                    <div class="company-info-item">
                        <strong><?php esc_html_e('DIČ:', 'kacosmetics'); ?></strong>
                        <span><?php echo esc_html($company_dic); ?></span>
                    </div>
                    <div class="company-info-item">
                        <strong><?php esc_html_e('IČ DPH:', 'kacosmetics'); ?></strong>
                        <span><?php echo esc_html($company_icdph); ?></span>
                    </div>
                    <div class="company-info-item company-info-full">
                        <strong><?php esc_html_e('Register:', 'kacosmetics'); ?></strong>
                        <span><?php echo esc_html($company_register); ?></span>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="contact-form-section">
                <h2><?php esc_html_e('Send us a Message', 'kacosmetics'); ?></h2>

                <?php
                // Check if WPForms or Contact Form 7 is active
                if (shortcode_exists('wpforms')) {
                    // Use WPForms shortcode (you'll need to create a form and replace ID)
                    echo do_shortcode('[wpforms id="1"]');
                } elseif (shortcode_exists('contact-form-7')) {
                    // Use Contact Form 7 shortcode (you'll need to create a form and replace ID)
                    echo do_shortcode('[contact-form-7 id="1"]');
                } else {
                    // Fallback simple HTML form
                    ?>
                    <form class="contact-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <input type="hidden" name="action" value="kacosmetics_contact_form">
                        <?php wp_nonce_field('kacosmetics_contact_form', 'contact_nonce'); ?>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="contact_name"><?php esc_html_e('Name', 'kacosmetics'); ?> *</label>
                                <input type="text" id="contact_name" name="contact_name" required>
                            </div>

                            <div class="form-group">
                                <label for="contact_email"><?php esc_html_e('Email', 'kacosmetics'); ?> *</label>
                                <input type="email" id="contact_email" name="contact_email" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="contact_phone"><?php esc_html_e('Phone', 'kacosmetics'); ?></label>
                            <input type="tel" id="contact_phone" name="contact_phone">
                        </div>

                        <div class="form-group">
                            <label for="contact_subject"><?php esc_html_e('Subject', 'kacosmetics'); ?> *</label>
                            <input type="text" id="contact_subject" name="contact_subject" required>
                        </div>

                        <div class="form-group">
                            <label for="contact_message"><?php esc_html_e('Message', 'kacosmetics'); ?> *</label>
                            <textarea id="contact_message" name="contact_message" rows="6" required></textarea>
                        </div>

                        <button type="submit" class="contact-submit-btn">
                            <?php esc_html_e('Send Message', 'kacosmetics'); ?>
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M18 2L9 11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M18 2L12 18L9 11L2 8L18 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </form>
                    <?php
                }
                ?>
            </div>

            <!-- Map Section -->
            <?php if ($map_latitude && $map_longitude) : ?>
            <div class="contact-map-section">
                <div id="contact-map" class="contact-map"
                     data-lat="<?php echo esc_attr($map_latitude); ?>"
                     data-lng="<?php echo esc_attr($map_longitude); ?>">
                    <!-- Map will be loaded here via JavaScript or iframe -->
                    <iframe
                        width="100%"
                        height="450"
                        style="border:0"
                        loading="lazy"
                        allowfullscreen
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2662.5!2d<?php echo esc_attr($map_longitude); ?>!3d<?php echo esc_attr($map_latitude); ?>!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDjCsDA4JzU1LjAiTiAxN8KwMDYnMjcuNyJF!5e0!3m2!1sen!2ssk!4v1234567890">
                    </iframe>
                </div>
            </div>
            <?php endif; ?>
        </div>

    </main>
</div>

<?php
get_footer();
