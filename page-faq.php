<?php
/**
 * Template Name: FAQ Page
 *
 * @package kacosmetics
 */

get_header();

$faq_groups = kacosmetics_get_faq_by_categories();

// Get page data
$page_title = get_the_title();
$page_excerpt = has_excerpt() ? get_the_excerpt() : '';
?>

<div id="primary" class="content-area faq-page">
    <main id="main" class="site-main">

        <header class="page-header">
            <h1 class="page-title"><?php echo esc_html($page_title); ?></h1>
            <?php if ($page_excerpt) : ?>
                <div class="page-description"><?php echo wp_kses_post($page_excerpt); ?></div>
            <?php endif; ?>
        </header>

        <div class="faq-container">
            <?php if (!empty($faq_groups)) : ?>
                <?php foreach ($faq_groups as $group) : ?>
                    <?php if ($group['category']) : ?>
                        <h2 class="faq-category-title"><?php echo esc_html($group['category']->name); ?></h2>
                    <?php endif; ?>

                    <div class="faq-accordion">
                        <?php foreach ($group['faqs'] as $faq) : ?>
                            <div class="faq-item">
                                <button class="faq-question" aria-expanded="false">
                                    <span class="faq-question-text"><?php echo esc_html($faq->post_title); ?></span>
                                    <span class="faq-icon">
                                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <polyline points="6 9 12 15 18 9"></polyline>
                                        </svg>
                                    </span>
                                </button>
                                <div class="faq-answer">
                                    <div class="faq-answer-content">
                                        <?php echo apply_filters('the_content', $faq->post_content); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <p class="no-faq"><?php esc_html_e('No FAQ items found.', 'kacosmetics'); ?></p>
            <?php endif; ?>
        </div>

    </main>
</div>

<script>
jQuery(function($) {
    $('.faq-question').on('click', function(e) {
        e.preventDefault();
        var $item = $(this).closest('.faq-item');
        var $answer = $item.find('.faq-answer');
        var isOpen = $item.hasClass('active');

        if (isOpen) {
            $item.removeClass('active');
            $(this).attr('aria-expanded', 'false');
            $answer.slideUp(300);
        } else {
            $item.addClass('active');
            $(this).attr('aria-expanded', 'true');
            $answer.slideDown(300);
        }
    });
});
</script>

<?php
get_footer();
