<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>
<section class="section page-section">
  <div class="container">
    <div class="page-hero">
      <p class="eyebrow"><?php esc_html_e('Кулінарний блог', 'vkusno-doma'); ?></p>
      <h1><?php single_post_title(); ?></h1>
      <p class="page-hero__description"><?php esc_html_e('Статті про продукти, сервірування, кулінарні звички та натхнення для домашньої кухні.', 'vkusno-doma'); ?></p>
    </div>

    <div class="blog-grid">
      <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>
          <article class="blog-card">
            <a class="blog-card__image" href="<?php the_permalink(); ?>">
              <?php the_post_thumbnail('large'); ?>
            </a>
            <div class="blog-card__body">
              <p class="blog-card__meta"><?php echo esc_html(get_the_date()); ?></p>
              <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
              <p><?php echo esc_html(get_the_excerpt()); ?></p>
            </div>
          </article>
        <?php endwhile; ?>
      <?php endif; ?>
    </div>

    <div class="pagination-wrap">
      <?php the_posts_pagination(); ?>
    </div>
  </div>
</section>
<?php get_footer(); ?>
