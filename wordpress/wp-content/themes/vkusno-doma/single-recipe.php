<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();

while (have_posts()) :
    the_post();
    $meta = vkusno_doma_get_recipe_meta(get_the_ID());
    $recipe_terms = get_the_terms(get_the_ID(), 'recipe_category');
    $related = new WP_Query([
        'post_type' => 'recipe',
        'posts_per_page' => 3,
        'post__not_in' => [get_the_ID()],
        'tax_query' => !empty($recipe_terms) && !is_wp_error($recipe_terms) ? [[
            'taxonomy' => 'recipe_category',
            'field' => 'term_id',
            'terms' => wp_list_pluck($recipe_terms, 'term_id'),
        ]] : [],
    ]);
    ?>
  <section class="section page-section">
    <div class="container">
      <article class="single-layout">
        <div class="single-layout__media">
          <?php the_post_thumbnail('large'); ?>
        </div>
        <div class="single-layout__content">
          <p class="eyebrow"><?php esc_html_e('Рецепт', 'vkusno-doma'); ?></p>
          <h1><?php the_title(); ?></h1>
          <div class="meta-badges">
            <span>★ <?php echo esc_html($meta['rating']); ?></span>
            <span>◷ <?php echo esc_html($meta['time']); ?></span>
            <span><?php echo esc_html($meta['author']); ?></span>
          </div>

          <?php if (!empty($recipe_terms) && !is_wp_error($recipe_terms)) : ?>
            <div class="taxonomy-pills">
              <?php foreach ($recipe_terms as $recipe_term) : ?>
                <a class="taxonomy-pill" href="<?php echo esc_url(get_term_link($recipe_term)); ?>"><?php echo esc_html($recipe_term->name); ?></a>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <?php if (has_excerpt()) : ?>
            <p class="single-layout__excerpt"><?php echo esc_html(get_the_excerpt()); ?></p>
          <?php endif; ?>

          <div class="rich-content">
            <?php the_content(); ?>
          </div>
        </div>
      </article>

      <?php if ($related->have_posts()) : ?>
        <div class="related-block">
          <div class="section-head">
            <h2><?php esc_html_e('Схожі рецепти', 'vkusno-doma'); ?></h2>
          </div>
          <div class="recipes-grid">
            <?php while ($related->have_posts()) : $related->the_post(); ?>
              <?php $related_meta = vkusno_doma_get_recipe_meta(get_the_ID()); ?>
              <article class="recipe-card">
                <a class="recipe-card__image" href="<?php the_permalink(); ?>">
                  <?php the_post_thumbnail('large'); ?>
                </a>
                <div class="recipe-card__body">
                  <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                  <div class="recipe-meta">
                    <span>★ <?php echo esc_html($related_meta['rating']); ?></span>
                    <span>◷ <?php echo esc_html($related_meta['time']); ?></span>
                  </div>
                  <p class="recipe-author"><?php echo esc_html($related_meta['author']); ?></p>
                </div>
              </article>
            <?php endwhile; ?>
            <?php wp_reset_postdata(); ?>
          </div>
        </div>
      <?php endif; ?>

      <?php if (comments_open() || get_comments_number()) : ?>
        <?php comments_template(); ?>
      <?php endif; ?>
    </div>
  </section>
<?php endwhile; ?>
<?php get_footer(); ?>
