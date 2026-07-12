<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();

while (have_posts()) :
    the_post();
    $is_collection = get_post_meta(get_the_ID(), '_vkusno_doma_is_collection', true) === '1';
    $collection_recipes = [];
    $collection_slug = get_post_field('post_name', get_the_ID());

    if ($is_collection) {
        $collection_recipes = new WP_Query([
            'post_type' => 'recipe',
            'post_status' => 'publish',
            'posts_per_page' => 6,
            'tax_query' => [[
                'taxonomy' => 'recipe_collection',
                'field' => 'slug',
                'terms' => $collection_slug,
            ]],
        ]);
    }
    ?>
  <section class="section page-section">
    <div class="container">
      <article class="single-post">
        <div class="single-post__content">
          <p class="eyebrow"><?php echo $is_collection ? esc_html__('Добірка', 'vkusno-doma') : esc_html__('Сторінка', 'vkusno-doma'); ?></p>
          <h1><?php the_title(); ?></h1>
          <div class="rich-content">
            <?php the_content(); ?>
          </div>
        </div>
      </article>

      <?php if ($is_collection && $collection_recipes instanceof WP_Query && $collection_recipes->have_posts()) : ?>
        <div class="related-block">
          <div class="section-head">
            <h2><?php esc_html_e('Рецепти в добірці', 'vkusno-doma'); ?></h2>
          </div>
          <div class="recipes-grid">
            <?php while ($collection_recipes->have_posts()) : $collection_recipes->the_post(); ?>
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
            <?php wp_reset_postdata(); ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </section>
<?php endwhile; ?>
<?php get_footer(); ?>
