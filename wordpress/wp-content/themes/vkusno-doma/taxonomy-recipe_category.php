<?php
if (!defined('ABSPATH')) {
    exit;
}

$term = get_queried_object();
$term_image = $term instanceof WP_Term ? vkusno_doma_get_term_image_url($term->term_id) : '';

get_header();
?>
<section class="section page-section">
  <div class="container">
    <div class="page-hero page-hero--split">
      <div>
        <p class="eyebrow"><?php esc_html_e('Категорія рецептів', 'vkusno-doma'); ?></p>
        <h1><?php single_term_title(); ?></h1>
        <?php if ($term instanceof WP_Term && $term->description) : ?>
          <p class="page-hero__description"><?php echo esc_html($term->description); ?></p>
        <?php endif; ?>
      </div>
      <?php if ($term_image) : ?>
        <div class="page-hero__thumb">
          <img src="<?php echo esc_url($term_image); ?>" alt="<?php echo esc_attr($term->name); ?>" />
        </div>
      <?php endif; ?>
    </div>

    <div class="recipes-grid">
      <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>
          <?php $meta = vkusno_doma_get_recipe_meta(get_the_ID()); ?>
          <article class="recipe-card">
            <a class="recipe-card__image" href="<?php the_permalink(); ?>">
              <?php the_post_thumbnail('large'); ?>
            </a>
            <div class="recipe-card__body">
              <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
              <div class="recipe-meta">
                <span>★ <?php echo esc_html($meta['rating']); ?></span>
                <span>◷ <?php echo esc_html($meta['time']); ?></span>
              </div>
              <p class="recipe-author"><?php echo esc_html($meta['author']); ?></p>
            </div>
          </article>
        <?php endwhile; ?>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php get_footer(); ?>
