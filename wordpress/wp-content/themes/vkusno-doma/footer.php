<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
      </main>
      <footer class="site-footer">
        <div class="container footer-grid">
          <section>
            <h2><?php esc_html_e('Про сайт', 'vkusno-doma'); ?></h2>
            <p>
              <?php esc_html_e('«Смачно вдома» — це місце, де люблять готувати та ділитися найкращими рецептами. Приєднуйтеся до нашої кулінарної спільноти.', 'vkusno-doma'); ?>
            </p>
          </section>

          <section>
            <h2><?php esc_html_e('Популярні категорії', 'vkusno-doma'); ?></h2>
            <ul class="footer-links">
              <?php
              $footer_terms = get_terms([
                  'taxonomy' => 'recipe_category',
                  'hide_empty' => false,
                  'number' => 6,
              ]);

              if (!is_wp_error($footer_terms) && !empty($footer_terms)) :
                  foreach ($footer_terms as $footer_term) :
                      ?>
                    <li><a href="<?php echo esc_url(get_term_link($footer_term)); ?>"><?php echo esc_html($footer_term->name); ?></a></li>
                  <?php
                  endforeach;
              endif;
              ?>
            </ul>
          </section>
        </div>

        <div class="container footer-bottom">
          <p>© <?php echo esc_html(wp_date('Y')); ?> <?php esc_html_e('Смачно вдома. Усі права захищено.', 'vkusno-doma'); ?></p>
          <div class="footer-bottom__links">
            <?php
            wp_nav_menu([
                'theme_location' => 'footer',
                'container' => false,
                'items_wrap' => '%3$s',
                'fallback_cb' => '__return_empty_string',
                'depth' => 1,
            ]);
            ?>
          </div>
        </div>
      </footer>
    </div>
    <?php wp_footer(); ?>
  </body>
</html>
