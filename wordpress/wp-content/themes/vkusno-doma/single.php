<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();

while (have_posts()) :
    the_post();
    ?>
  <section class="section page-section">
    <div class="container">
      <article class="single-post">
        <?php if (has_post_thumbnail()) : ?>
          <div class="single-post__media">
            <?php the_post_thumbnail('large'); ?>
          </div>
        <?php endif; ?>
        <div class="single-post__content">
          <p class="eyebrow"><?php esc_html_e('Блог', 'vkusno-doma'); ?></p>
          <h1><?php the_title(); ?></h1>
          <p class="blog-card__meta"><?php echo esc_html(get_the_date()); ?></p>
          <div class="rich-content">
            <?php the_content(); ?>
          </div>
        </div>
      </article>
    </div>
  </section>
<?php endwhile; ?>
<?php get_footer(); ?>
