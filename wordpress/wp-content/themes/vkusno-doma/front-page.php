<?php
if (!defined('ABSPATH')) {
    exit;
}

$categories = get_terms([
    'taxonomy' => 'recipe_category',
    'hide_empty' => false,
    'number' => 12,
]);

$recipes = new WP_Query([
    'post_type' => 'recipe',
    'posts_per_page' => 5,
]);

$collections = get_posts([
    'post_type' => 'page',
    'posts_per_page' => 3,
    'meta_key' => '_vkusno_doma_is_collection',
    'meta_value' => '1',
    'orderby' => 'menu_order date',
    'order' => 'ASC',
]);

get_header();
?>
<section class="hero">
  <div class="container">
    <div class="hero-card">
      <div class="hero__content">
        <p class="eyebrow"><?php esc_html_e('Домашня кухня без зайвого', 'vkusno-doma'); ?></p>
        <h1><?php esc_html_e('Готуйте з любов’ю, діліться натхненням', 'vkusno-doma'); ?></h1>
        <p class="hero__description">
          <?php esc_html_e('Зберігайте улюблені рецепти, відкривайте нові смаки й повертайтеся до домашньої кухні, яка справді надихає.', 'vkusno-doma'); ?>
        </p>
        <div class="hero-actions">
          <a class="button button--primary" href="<?php echo esc_url(get_post_type_archive_link('recipe')); ?>"><?php esc_html_e('Дивитися рецепти', 'vkusno-doma'); ?></a>
        </div>
      </div>

      <div class="hero__visual">
        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/hero-pasta.png'); ?>" alt="" />
      </div>
    </div>
  </div>
</section>

<section class="section" id="categories">
  <div class="container">
    <div class="section-head">
      <h2><?php esc_html_e('Категорії', 'vkusno-doma'); ?></h2>
      <a class="section-link" href="<?php echo esc_url(get_post_type_archive_link('recipe')); ?>"><?php esc_html_e('Усі рецепти', 'vkusno-doma'); ?></a>
    </div>

    <div class="categories-carousel" data-carousel>
      <button class="carousel-arrow carousel-arrow--prev" type="button" aria-label="<?php esc_attr_e('Попередні категорії', 'vkusno-doma'); ?>" data-carousel-prev>
        <span aria-hidden="true">‹</span>
      </button>
      <div class="categories-track" data-carousel-track>
        <?php if (!is_wp_error($categories) && !empty($categories)) : ?>
          <?php foreach ($categories as $category) : ?>
            <article class="category-card">
              <a href="<?php echo esc_url(get_term_link($category)); ?>">
                <div class="category-card__art">
                  <?php $term_image_url = vkusno_doma_get_term_image_url($category->term_id); ?>
                  <?php if ($term_image_url) : ?>
                    <img src="<?php echo esc_url($term_image_url); ?>" alt="<?php echo esc_attr($category->name); ?>" />
                  <?php endif; ?>
                </div>
                <h3><?php echo esc_html($category->name); ?></h3>
              </a>
            </article>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
      <button class="carousel-arrow carousel-arrow--next" type="button" aria-label="<?php esc_attr_e('Наступні категорії', 'vkusno-doma'); ?>" data-carousel-next>
        <span aria-hidden="true">›</span>
      </button>
    </div>
  </div>
</section>

<section class="section" id="recipes">
  <div class="container">
    <div class="section-head">
      <h2><?php esc_html_e('Популярні рецепти', 'vkusno-doma'); ?></h2>
      <a class="section-link" href="<?php echo esc_url(get_post_type_archive_link('recipe')); ?>"><?php esc_html_e('Дивитися всі', 'vkusno-doma'); ?></a>
    </div>

    <div class="recipes-grid">
      <?php if ($recipes->have_posts()) : ?>
        <?php while ($recipes->have_posts()) : $recipes->the_post(); ?>
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
      <?php endif; ?>
    </div>
  </div>
</section>

<section class="section" id="collections">
  <div class="container">
    <h2><?php esc_html_e('Добірки рецептів', 'vkusno-doma'); ?></h2>
    <div class="collections-grid">
      <?php foreach ($collections as $collection) : ?>
        <article class="collection-card">
          <a href="<?php echo esc_url(get_permalink($collection)); ?>">
            <?php echo get_the_post_thumbnail($collection->ID, 'large'); ?>
            <div class="collection-card__overlay">
              <h3><?php echo esc_html(get_the_title($collection)); ?></h3>
              <p><?php echo esc_html(get_the_excerpt($collection)); ?></p>
              <span class="button button--ghost"><?php esc_html_e('Дивитися добірку', 'vkusno-doma'); ?></span>
            </div>
          </a>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section--features" id="community">
  <div class="container">
    <div class="section-head section-head--stacked">
      <div>
        <p class="eyebrow"><?php esc_html_e('Спільнота та структура', 'vkusno-doma'); ?></p>
        <h2><?php esc_html_e('Готуйте разом із нами', 'vkusno-doma'); ?></h2>
      </div>
      <p class="section-lead"><?php esc_html_e('Зручний кулінарний простір без візуального шуму: живі категорії, авторські добірки й рецепти, до яких хочеться повертатися.', 'vkusno-doma'); ?></p>
    </div>
    <div class="features-grid">
      <article class="feature">
        <div class="feature__icon" aria-hidden="true">01</div>
        <div>
          <p class="feature__label"><?php esc_html_e('Основа колекції', 'vkusno-doma'); ?></p>
          <h3><?php esc_html_e('Рецепти без хаосу', 'vkusno-doma'); ?></h3>
          <p><?php esc_html_e('Улюблені страви зібрані в одному місці: зрозуміло, чисто й без зайвих відволікань.', 'vkusno-doma'); ?></p>
        </div>
      </article>
      <article class="feature">
        <div class="feature__icon" aria-hidden="true">02</div>
        <div>
          <p class="feature__label"><?php esc_html_e('Зрозумілий процес', 'vkusno-doma'); ?></p>
          <h3><?php esc_html_e('Покрокова подача', 'vkusno-doma'); ?></h3>
          <p><?php esc_html_e('Інгредієнти, етапи та важливі дрібниці подані так, щоб за рецептом було легко готувати.', 'vkusno-doma'); ?></p>
        </div>
      </article>
      <article class="feature">
        <div class="feature__icon" aria-hidden="true">03</div>
        <div>
          <p class="feature__label"><?php esc_html_e('Жива навігація', 'vkusno-doma'); ?></p>
          <h3><?php esc_html_e('Категорії зі змістом', 'vkusno-doma'); ?></h3>
          <p><?php esc_html_e('Сайт будується навколо реальних страв і тем, тому знаходити потрібне швидко й природно.', 'vkusno-doma'); ?></p>
        </div>
      </article>
      <article class="feature">
        <div class="feature__icon" aria-hidden="true">04</div>
        <div>
          <p class="feature__label"><?php esc_html_e('Особистий акцент', 'vkusno-doma'); ?></p>
          <h3><?php esc_html_e('Авторські добірки', 'vkusno-doma'); ?></h3>
          <p><?php esc_html_e('Окремий простір для ваших фірмових рецептів, сезонних ідей і домашніх знахідок.', 'vkusno-doma'); ?></p>
        </div>
      </article>
    </div>
  </div>
</section>
<?php get_footer(); ?>
