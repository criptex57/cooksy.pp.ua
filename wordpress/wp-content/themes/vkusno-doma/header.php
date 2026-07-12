<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
  <head>
    <meta charset="<?php bloginfo('charset'); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <?php wp_head(); ?>
  </head>
  <body <?php body_class(); ?>>
    <?php wp_body_open(); ?>
    <div class="page-shell">
      <header class="site-header">
        <div class="container header-row">
          <a class="brand" href="<?php echo esc_url(home_url('/')); ?>">
            <img class="brand__mark" src="<?php echo esc_url(get_template_directory_uri() . '/assets/logo-mark-raster-dark-cropped.png'); ?>" alt="" aria-hidden="true" />
            <span class="brand__text"><?php bloginfo('name'); ?></span>
          </a>

          <nav class="main-nav" aria-label="<?php esc_attr_e('Основна навігація', 'vkusno-doma'); ?>">
            <?php
            wp_nav_menu([
                'theme_location' => 'primary',
                'container' => false,
                'items_wrap' => '%3$s',
                'fallback_cb' => 'vkusno_doma_menu_fallback',
                'depth' => 1,
            ]);
            ?>
          </nav>

          <div class="header-actions">
            <form class="header-search" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
              <label class="sr-only" for="header-search-field"><?php esc_html_e('Пошук по сайту', 'vkusno-doma'); ?></label>
              <input
                id="header-search-field"
                type="search"
                name="s"
                value="<?php echo esc_attr(get_search_query()); ?>"
                placeholder="<?php esc_attr_e('Шукати рецепти та статті', 'vkusno-doma'); ?>"
              />
              <button class="icon-button icon-button--filled" type="submit" aria-label="<?php esc_attr_e('Знайти', 'vkusno-doma'); ?>">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                  <circle cx="11" cy="11" r="6.5" fill="none" stroke="currentColor" stroke-width="2" />
                  <path d="M16 16l4 4" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="2" />
                </svg>
              </button>
            </form>
            <button class="burger" aria-label="<?php esc_attr_e('Відкрити меню', 'vkusno-doma'); ?>">
              <span></span>
              <span></span>
              <span></span>
            </button>
          </div>
        </div>
      </header>
      <main>
