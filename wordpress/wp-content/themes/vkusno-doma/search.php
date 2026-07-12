<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>
<section class="section page-section">
  <div class="container">
    <div class="page-hero">
      <p class="eyebrow"><?php esc_html_e('Пошук', 'vkusno-doma'); ?></p>
      <h1><?php printf(esc_html__('Результати за запитом: %s', 'vkusno-doma'), get_search_query()); ?></h1>
    </div>

    <div class="content-list">
      <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>
          <article class="content-list__item">
            <p class="blog-card__meta"><?php echo esc_html(get_post_type_object(get_post_type())->labels->singular_name ?? ''); ?></p>
            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
            <p><?php echo esc_html(get_the_excerpt()); ?></p>
          </article>
        <?php endwhile; ?>
      <?php else : ?>
        <p class="empty-state"><?php esc_html_e('Нічого не знайдено. Спробуйте інший запит.', 'vkusno-doma'); ?></p>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php get_footer(); ?>
