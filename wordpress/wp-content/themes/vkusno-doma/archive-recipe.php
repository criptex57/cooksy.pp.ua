<?php
if (!defined('ABSPATH')) {
    exit;
}

$terms = get_terms([
    'taxonomy' => 'recipe_category',
    'hide_empty' => false,
]);

get_header();
?>
<section class="section page-section">
  <div class="container">
    <div class="page-hero">
      <p class="eyebrow"><?php esc_html_e('Каталог рецептів', 'vkusno-doma'); ?></p>
      <h1><?php post_type_archive_title(); ?></h1>
      <p class="page-hero__description"><?php esc_html_e('Переглядайте рецепти за категоріями, часом приготування та настроєм кухні.', 'vkusno-doma'); ?></p>
    </div>

    <div class="taxonomy-pills">
      <a class="taxonomy-pill taxonomy-pill--active" href="<?php echo esc_url(get_post_type_archive_link('recipe')); ?>"><?php esc_html_e('Усі', 'vkusno-doma'); ?></a>
      <?php if (!is_wp_error($terms)) : ?>
        <?php foreach ($terms as $term) : ?>
          <a class="taxonomy-pill" href="<?php echo esc_url(get_term_link($term)); ?>"><?php echo esc_html($term->name); ?></a>
        <?php endforeach; ?>
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
      <?php else : ?>
        <p class="empty-state"><?php esc_html_e('Рецепти ще не опубліковані.', 'vkusno-doma'); ?></p>
      <?php endif; ?>
    </div>

    <div class="pagination-wrap">
      <?php the_posts_pagination(); ?>
    </div>
  </div>
</section>
<?php get_footer(); ?>
