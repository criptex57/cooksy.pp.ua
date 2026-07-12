<?php

if (!defined('ABSPATH')) {
    exit;
}

const VKUSNO_DOMA_SEED_VERSION = '1.0.6';

function vkusno_doma_setup(): void
{
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);

    register_nav_menus([
        'primary' => __('Primary Menu', 'vkusno-doma'),
        'footer' => __('Footer Menu', 'vkusno-doma'),
    ]);
}
add_action('after_setup_theme', 'vkusno_doma_setup');

function vkusno_doma_assets(): void
{
    wp_enqueue_style(
        'vkusno-doma-style',
        get_stylesheet_uri(),
        [],
        filemtime(get_stylesheet_directory() . '/style.css')
    );

    wp_enqueue_style(
        'vkusno-doma-theme',
        get_template_directory_uri() . '/assets/css/theme.css',
        ['vkusno-doma-style'],
        filemtime(get_stylesheet_directory() . '/assets/css/theme.css')
    );

    wp_enqueue_script(
        'vkusno-doma-theme',
        get_template_directory_uri() . '/assets/js/theme.js',
        [],
        filemtime(get_stylesheet_directory() . '/assets/js/theme.js'),
        true
    );

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'vkusno_doma_assets');

function vkusno_doma_output_favicons(): void
{
    $theme_uri = get_template_directory_uri();
    echo '<link rel="icon" href="' . esc_url($theme_uri . '/assets/logo-mark-raster-dark-cropped.png') . '" type="image/png" />';
    echo '<link rel="shortcut icon" href="' . esc_url($theme_uri . '/assets/logo-mark-raster-dark-cropped.png') . '" type="image/png" />';
}
add_action('wp_head', 'vkusno_doma_output_favicons', 1);

function vkusno_doma_register_content_types(): void
{
    register_post_type('recipe', [
        'labels' => [
            'name' => __('Рецепти', 'vkusno-doma'),
            'singular_name' => __('Рецепт', 'vkusno-doma'),
            'add_new_item' => __('Додати рецепт', 'vkusno-doma'),
            'edit_item' => __('Редагувати рецепт', 'vkusno-doma'),
            'new_item' => __('Новий рецепт', 'vkusno-doma'),
            'view_item' => __('Переглянути рецепт', 'vkusno-doma'),
            'search_items' => __('Шукати рецепти', 'vkusno-doma'),
        ],
        'public' => true,
        'has_archive' => 'recipes',
        'rewrite' => ['slug' => 'recipes'],
        'menu_icon' => 'dashicons-carrot',
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'comments'],
        'show_in_rest' => true,
    ]);

    register_taxonomy('recipe_category', ['recipe'], [
        'labels' => [
            'name' => __('Категорії рецептів', 'vkusno-doma'),
            'singular_name' => __('Категорія рецептів', 'vkusno-doma'),
        ],
        'public' => true,
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite' => ['slug' => 'recipe-category'],
    ]);

    register_taxonomy('recipe_collection', ['recipe'], [
        'labels' => [
            'name' => __('Добірки рецептів', 'vkusno-doma'),
            'singular_name' => __('Добірка рецептів', 'vkusno-doma'),
            'search_items' => __('Шукати добірки', 'vkusno-doma'),
            'all_items' => __('Усі добірки', 'vkusno-doma'),
            'edit_item' => __('Редагувати добірку', 'vkusno-doma'),
            'update_item' => __('Оновити добірку', 'vkusno-doma'),
            'add_new_item' => __('Додати добірку', 'vkusno-doma'),
            'new_item_name' => __('Назва нової добірки', 'vkusno-doma'),
        ],
        'public' => false,
        'show_ui' => true,
        'show_admin_column' => true,
        'hierarchical' => true,
        'show_in_rest' => true,
        'meta_box_cb' => 'post_categories_meta_box',
        'rewrite' => false,
    ]);
}
add_action('init', 'vkusno_doma_register_content_types');

function vkusno_doma_maybe_flush_rewrite_rules(): void
{
    if (get_option('vkusno_doma_flush_rewrite') !== '1') {
        return;
    }

    flush_rewrite_rules();
    update_option('vkusno_doma_flush_rewrite', '0');
}
add_action('init', 'vkusno_doma_maybe_flush_rewrite_rules', 99);

function vkusno_doma_on_theme_switch(): void
{
    update_option('vkusno_doma_flush_rewrite', '1');
}
add_action('after_switch_theme', 'vkusno_doma_on_theme_switch');

function vkusno_doma_include_searchable_post_types(WP_Query $query): void
{
    if (is_admin() || !$query->is_main_query() || !$query->is_search()) {
        return;
    }

    $query->set('post_type', ['recipe', 'page']);
}
add_action('pre_get_posts', 'vkusno_doma_include_searchable_post_types');

function vkusno_doma_menu_fallback(): void
{
    ?>
    <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Головна', 'vkusno-doma'); ?></a>
    <a href="<?php echo esc_url(get_post_type_archive_link('recipe')); ?>"><?php esc_html_e('Рецепти', 'vkusno-doma'); ?></a>
    <a href="<?php echo esc_url(home_url('/#categories')); ?>"><?php esc_html_e('Категорії', 'vkusno-doma'); ?></a>
    <a href="<?php echo esc_url(home_url('/#collections')); ?>"><?php esc_html_e('Добірки', 'vkusno-doma'); ?></a>
    <?php
}

function vkusno_doma_get_add_recipe_url(): string
{
    $target = admin_url('post-new.php?post_type=recipe');

    if (is_user_logged_in()) {
        return $target;
    }

    return wp_login_url($target);
}

function vkusno_doma_get_page_url(string $slug): string
{
    $page = get_page_by_path($slug);

    if (!$page instanceof WP_Post) {
        return home_url('/');
    }

    return get_permalink($page);
}

function vkusno_doma_get_term_image_id(int $term_id): int
{
    return (int) get_term_meta($term_id, '_vkusno_doma_image_id', true);
}

function vkusno_doma_get_term_image_url(int $term_id): string
{
    $image_id = vkusno_doma_get_term_image_id($term_id);

    if (!$image_id) {
        return '';
    }

    return (string) wp_get_attachment_image_url($image_id, 'large');
}

function vkusno_doma_get_recipe_meta(int $post_id): array
{
    return [
        'time' => (string) get_post_meta($post_id, '_vkusno_doma_time', true),
        'rating' => (string) get_post_meta($post_id, '_vkusno_doma_rating', true),
        'author' => (string) get_post_meta($post_id, '_vkusno_doma_author', true),
    ];
}

function vkusno_doma_get_seed_data(): array
{
    return [
        'categories' => [
            [
                'name' => 'Завтраки',
                'slug' => 'breakfast',
                'description' => 'Быстрые и уютные идеи для идеального начала дня.',
                'image' => 'category-breakfast.png',
            ],
            [
                'name' => 'Супы',
                'slug' => 'soups',
                'description' => 'Насыщенные и домашние супы на каждый день.',
                'image' => 'category-soup.png',
            ],
            [
                'name' => 'Салаты',
                'slug' => 'salads',
                'description' => 'Свежие сочетания овощей, зелени и заправок.',
                'image' => 'category-salad.png',
            ],
            [
                'name' => 'Сири',
                'slug' => 'snacks',
                'description' => 'Витримані, тягучі та домашні сири для тих, хто любить працювати з молоком і часом.',
                'image' => 'category-cheese.png',
            ],
            [
                'name' => 'Горячие блюда',
                'slug' => 'main-dishes',
                'description' => 'Главные блюда для семейного ужина и праздников.',
                'image' => 'category-hot-dishes.png',
            ],
            [
                'name' => 'Выпечка',
                'slug' => 'baking',
                'description' => 'Сладкая и несладкая выпечка с ароматом домашней кухни.',
                'image' => 'category-bakery.png',
            ],
            [
                'name' => 'Десерты',
                'slug' => 'desserts',
                'description' => 'Легкие и эффектные десерты для особых моментов.',
                'image' => 'category-dessert.png',
            ],
            [
                'name' => 'Напитки',
                'slug' => 'drinks',
                'description' => 'Горячие и освежающие напитки для любого сезона.',
                'image' => 'category-drinks.png',
            ],
            [
                'name' => 'Соусы и заправки',
                'slug' => 'sauces',
                'description' => 'Соусы, которые делают знакомые блюда ярче.',
                'image' => 'category-sauces.png',
            ],
        ],
        'recipes' => [
            [
                'title' => 'Паста карбонара',
                'slug' => 'pasta-carbonara',
                'excerpt' => 'Классическая кремовая паста с беконом, пармезаном и черным перцем.',
                'content' => '<h2>Ингредиенты</h2><ul><li>300 г спагетти</li><li>150 г бекона</li><li>2 яйца</li><li>60 г пармезана</li><li>соль и свежемолотый перец</li></ul><h2>Приготовление</h2><ol><li>Отварите пасту до состояния al dente.</li><li>Обжарьте бекон до золотистой корочки.</li><li>Смешайте яйца с тертым пармезаном и перцем.</li><li>Соедините горячую пасту с беконом и снимите с огня.</li><li>Быстро вмешайте яично-сырную смесь, регулируя густоту небольшим количеством воды от пасты.</li></ol>',
                'image' => 'recipe-pasta-carbonara.png',
                'category' => 'main-dishes',
                'rating' => '4.8 (124)',
                'time' => '30 мин.',
                'author' => 'Елена Иванова',
            ],
            [
                'title' => 'Крем-суп из тыквы',
                'slug' => 'pumpkin-cream-soup',
                'excerpt' => 'Нежный суп с ярким вкусом запеченной тыквы, сливок и ароматных трав.',
                'content' => '<h2>Ингредиенты</h2><ul><li>700 г тыквы</li><li>1 луковица</li><li>2 зубчика чеснока</li><li>500 мл овощного бульона</li><li>100 мл сливок</li></ul><h2>Приготовление</h2><ol><li>Запеките тыкву до мягкости.</li><li>Обжарьте лук и чеснок на небольшом количестве масла.</li><li>Добавьте тыкву и бульон, доведите до кипения.</li><li>Пробейте суп блендером до гладкости.</li><li>Влейте сливки, прогрейте и подавайте с зеленью.</li></ol>',
                'image' => 'recipe-pumpkin-soup.png',
                'category' => 'soups',
                'rating' => '4.9 (98)',
                'time' => '40 мин.',
                'author' => 'Алексей Смирнов',
            ],
            [
                'title' => 'Греческий салат',
                'slug' => 'greek-salad',
                'excerpt' => 'Свежий салат с сочными томатами, огурцами, фетой и оливками.',
                'content' => '<h2>Ингредиенты</h2><ul><li>2 томата</li><li>1 огурец</li><li>1 сладкий перец</li><li>120 г феты</li><li>оливки, оливковое масло, орегано</li></ul><h2>Приготовление</h2><ol><li>Крупно нарежьте овощи.</li><li>Добавьте фету и оливки.</li><li>Заправьте салат маслом, щепоткой соли и орегано.</li><li>Перемешайте аккуратно, чтобы сохранить текстуру ингредиентов.</li></ol>',
                'image' => 'recipe-greek-salad.png',
                'category' => 'salads',
                'rating' => '4.7 (76)',
                'time' => '15 мин.',
                'author' => 'Мария Кузнецова',
            ],
            [
                'title' => 'Курица с розмарином',
                'slug' => 'rosemary-chicken',
                'excerpt' => 'Запеченная курица с хрустящей корочкой, чесноком и свежим розмарином.',
                'content' => '<h2>Ингредиенты</h2><ul><li>4 куриных бедра</li><li>3 веточки розмарина</li><li>3 зубчика чеснока</li><li>2 ст. л. оливкового масла</li><li>соль и перец</li></ul><h2>Приготовление</h2><ol><li>Натрите курицу солью, перцем и маслом.</li><li>Добавьте рубленый чеснок и розмарин.</li><li>Запекайте при 200°C около 45 минут до румяной корочки.</li><li>Подавайте с овощами или картофелем.</li></ol>',
                'image' => 'recipe-rosemary-chicken.png',
                'category' => 'main-dishes',
                'rating' => '4.9 (112)',
                'time' => '1 ч. 10 мин.',
                'author' => 'Ирина Петрова',
            ],
            [
                'title' => 'Чизкейк Нью-Йорк',
                'slug' => 'new-york-cheesecake',
                'excerpt' => 'Классический десерт с шелковистой текстурой и нежным сливочным вкусом.',
                'content' => '<h2>Ингредиенты</h2><ul><li>200 г печенья</li><li>90 г сливочного масла</li><li>600 г сливочного сыра</li><li>150 г сахара</li><li>3 яйца</li><li>150 мл сливок</li></ul><h2>Приготовление</h2><ol><li>Сделайте основу из печенья и масла.</li><li>Смешайте сыр, сахар, яйца и сливки до однородности.</li><li>Вылейте массу на основу и выпекайте на низкой температуре.</li><li>Охладите минимум 6 часов перед подачей.</li></ol>',
                'image' => 'recipe-cheesecake.png',
                'category' => 'desserts',
                'rating' => '4.8 (89)',
                'time' => '1 ч.',
                'author' => 'Дмитрий Волков',
            ],
            [
                'title' => 'Авторские сырники с ванилью',
                'slug' => 'avtorskie-syrniki-s-vanilyu',
                'excerpt' => 'Нежные сырники с ванилью и золотистой корочкой для уютного завтрака.',
                'content' => '<h2>Ингредиенты</h2><ul><li>400 г творога</li><li>1 яйцо</li><li>2 ст. л. сахара</li><li>1 ч. л. ванильного сахара</li><li>3 ст. л. муки</li></ul><h2>Приготовление</h2><ol><li>Смешайте творог, яйцо, сахар и ваниль.</li><li>Добавьте муку и сформируйте небольшие сырники.</li><li>Обжарьте на среднем огне до румяной корочки с двух сторон.</li><li>Подавайте со сметаной, ягодами или медом.</li></ol>',
                'image' => 'category-breakfast.png',
                'category' => 'breakfast',
                'rating' => '4.8 (54)',
                'time' => '25 мин.',
                'author' => 'Автор Вкусно дома',
            ],
            [
                'title' => 'Булочки с корицей',
                'slug' => 'cinnamon-rolls',
                'excerpt' => 'Мягкие домашние булочки с корицей и ароматной сахарной начинкой.',
                'content' => '<h2>Ингредиенты</h2><ul><li>450 г муки</li><li>220 мл молока</li><li>50 г сливочного масла</li><li>2 ст. л. сахара</li><li>корица и коричневый сахар для начинки</li></ul><h2>Приготовление</h2><ol><li>Замесите мягкое дрожжевое тесто и дайте ему подняться.</li><li>Раскатайте пласт, смажьте маслом и посыпьте сахаром с корицей.</li><li>Сверните рулетом, нарежьте и выложите в форму.</li><li>Выпекайте до золотистого цвета и подавайте теплыми.</li></ol>',
                'image' => 'category-bakery.png',
                'category' => 'baking',
                'rating' => '4.9 (41)',
                'time' => '1 ч. 20 мин.',
                'author' => 'Елена Иванова',
            ],
            [
                'title' => 'Домашний цитрусовый лимонад',
                'slug' => 'homemade-citrus-lemonade',
                'excerpt' => 'Освежающий напиток с лимоном, апельсином и мятой для жарких дней.',
                'content' => '<h2>Ингредиенты</h2><ul><li>2 лимона</li><li>1 апельсин</li><li>3 ст. л. меда или сахара</li><li>1 л холодной воды</li><li>лед и мята</li></ul><h2>Приготовление</h2><ol><li>Выжмите сок из цитрусовых.</li><li>Смешайте сок с водой и медом.</li><li>Добавьте лед, дольки лимона и мяту.</li><li>Охладите 10 минут перед подачей.</li></ol>',
                'image' => 'category-drinks.png',
                'category' => 'drinks',
                'rating' => '4.7 (33)',
                'time' => '10 мин.',
                'author' => 'Мария Кузнецова',
            ],
            [
                'title' => 'Йогуртовый соус с зеленью',
                'slug' => 'yogurt-herb-sauce',
                'excerpt' => 'Легкий соус из йогурта, чеснока и свежей зелени для салатов и закусок.',
                'content' => '<h2>Ингредиенты</h2><ul><li>200 г густого йогурта</li><li>1 зубчик чеснока</li><li>укроп и петрушка</li><li>1 ч. л. лимонного сока</li><li>соль и перец</li></ul><h2>Приготовление</h2><ol><li>Мелко порубите зелень и чеснок.</li><li>Смешайте с йогуртом, лимонным соком, солью и перцем.</li><li>Охладите 15 минут, чтобы вкус стал насыщеннее.</li><li>Подавайте к овощам, мясу или картофелю.</li></ol>',
                'image' => 'category-sauces.png',
                'category' => 'sauces',
                'rating' => '4.8 (29)',
                'time' => '15 мин.',
                'author' => 'Алексей Смирнов',
            ],
            [
                'title' => 'Домашній пармезан',
                'slug' => 'domashnii-parmezan',
                'excerpt' => 'Витриманий твердий сир із щільним зерном, насиченим ароматом і довгим дозріванням.',
                'content' => '<p>Домашній пармезан потребує терпіння, але віддячує глибоким смаком, крихкою текстурою та виразними сирними кристалами. Головне тут не перевантажувати рецепт дрібницями, а чітко тримати температуру, пресування і час визрівання.</p><h2>Інгредієнти</h2><ul><li>20 л молока: приблизно 13,5 л вчорашнього знятого молока та 6,5 л свіжого ранкового</li><li>2 дози термофільної закваски для пармезану</li><li>сичужний фермент</li><li>0,5 склянки кип’яченої охолодженої води</li><li>для розсолу: 2 л води та 500 г солі</li></ul><h2>Короткий інвентар</h2><ul><li>велика каструля або водяна баня</li><li>термометр</li><li>вінчик або ніж для різання згустку</li><li>марля, друшляк, форма для сиру</li><li>вантажі для пресування 5 і 10 кг</li></ul><h2>Приготування</h2><ol><li><strong>Підготуйте молоко.</strong> Змішайте вчорашнє зняте молоко зі свіжим, нагрійте до 33°C, внесіть закваску і витримайте 1 годину при 32–33°C.</li><li><strong>Внесіть фермент.</strong> Розчиніть його у воді, добре перемішайте з молоком і дочекайтеся щільного згустку. Наріжте згусток і активно вимішуйте, поки зерно не стане дрібним, приблизно 2–3 мм.</li><li><strong>Проведіть другий нагрів.</strong> Поступово підніміть температуру з 33°C до 58°C приблизно за 20 хвилин, постійно помішуючи. Потім опустіть до 55°C і вимішуйте ще 5–10 хвилин, поки зерно не буде добре злипатися в руці.</li><li><strong>Формуйте та пресуйте.</strong> Зберіть зерно в марлю, поверніть у сироватку на 60 хвилин при 55–57°C, перевертаючи кожні 15 хвилин. Далі пресуйте у формі: 20 хвилин під 5 кг, ще 20 хвилин після перевороту, потім по 40 хвилин з кожного боку під 10 кг і залиште під пресом на ніч.</li><li><strong>Посоліть і визрівайте.</strong> Після 30–48 годин ферментації занурте головку в 20% розсіл. Орієнтир для соління: 6 годин на кожні 500 г сиру. Далі обсушіть 1–2 дні та визрівайте при 10–11°C і вологості близько 85% від 6 до 12 місяців.</li></ol><h2>Порада</h2><p>Якщо під час дозрівання з’являється пліснява, її можна акуратно зняти тканиною, змоченою в міцному розсолі. Коли скоринка добре підсохне, тонкий шар оливкової олії допоможе захистити сир від пересихання.</p>',
                'image' => 'recipe-parmesan.png',
                'category' => 'snacks',
                'rating' => '5.0 (1)',
                'time' => '6–12 міс.',
                'author' => 'Автор сайту',
            ],
        ],
        'collections' => [
            [
                'title' => 'Рецепты для пикника',
                'slug' => 'recipes-for-picnic',
                'excerpt' => 'Идеи для вкусного отдыха на природе.',
                'content' => '<p>Подборка блюд, которые удобно брать с собой: закуски, сэндвичи, легкие салаты и выпечка.</p>',
                'image' => 'collection-picnic.png',
                'recipes' => ['greek-salad', 'homemade-citrus-lemonade'],
            ],
            [
                'title' => 'Быстрые ужины',
                'slug' => 'quick-dinners',
                'excerpt' => 'Вкусные блюда за 30 минут.',
                'content' => '<p>Собрали быстрые рецепты для будних вечеров, когда хочется приготовить что-то домашнее без лишней суеты.</p>',
                'image' => 'collection-quick-dinner.png',
                'recipes' => ['pasta-carbonara', 'rosemary-chicken'],
            ],
            [
                'title' => 'От автора',
                'slug' => 'baking-for-tea',
                'excerpt' => 'Личная подборка рецептов, которые вы добавляете и рекомендуете сами.',
                'content' => '<p>Эта подборка создана для авторских рецептов: здесь можно собирать любимые блюда, фирменные идеи и все, чем хочется делиться от своего имени.</p>',
                'image' => 'collection-baking.png',
                'recipes' => [],
            ],
        ],
        'blog_posts' => [],
        'pages' => [
            ['title' => 'Главная', 'slug' => 'home', 'content' => ''],
            ['title' => 'О нас', 'slug' => 'about-us', 'content' => '<p>«Вкусно дома» — кулинарный проект о красивой домашней еде, понятных рецептах и любви к процессу готовки.</p>'],
            ['title' => 'Правила', 'slug' => 'rules', 'content' => '<p>Правила использования сайта и публикации пользовательского контента.</p>'],
            ['title' => 'Конфиденциальность', 'slug' => 'privacy-policy', 'content' => '<p>Мы бережно относимся к персональным данным и используем их только для работы сайта.</p>'],
            ['title' => 'Контакты', 'slug' => 'contacts', 'content' => '<p>Для связи напишите нам на hello@vkusnodoma.local.</p>'],
        ],
    ];
}

function vkusno_doma_import_local_image(string $filename, int $parent_post_id = 0): int
{
    $existing = get_posts([
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'meta_key' => '_vkusno_doma_source_asset',
        'meta_value' => $filename,
        'fields' => 'ids',
        'numberposts' => 1,
    ]);

    if (!empty($existing)) {
        return (int) $existing[0];
    }

    $source = get_template_directory() . '/assets/images/' . $filename;

    if (!file_exists($source)) {
        return 0;
    }

    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';

    $uploads = wp_upload_dir();
    wp_mkdir_p($uploads['basedir']);
    wp_mkdir_p($uploads['path']);

    $unique_name = wp_unique_filename($uploads['path'], basename($source));
    $destination = trailingslashit($uploads['path']) . $unique_name;

    if (!copy($source, $destination)) {
        return 0;
    }

    $attachment_id = wp_insert_attachment([
        'guid' => trailingslashit($uploads['url']) . $unique_name,
        'post_mime_type' => wp_check_filetype($unique_name)['type'] ?? 'image/png',
        'post_title' => sanitize_file_name(pathinfo($unique_name, PATHINFO_FILENAME)),
        'post_content' => '',
        'post_status' => 'inherit',
    ], $destination, $parent_post_id);

    if (is_wp_error($attachment_id) || !$attachment_id) {
        return 0;
    }

    $metadata = wp_generate_attachment_metadata($attachment_id, $destination);

    if (!is_wp_error($metadata) && !empty($metadata)) {
        wp_update_attachment_metadata($attachment_id, $metadata);
    }

    update_post_meta($attachment_id, '_vkusno_doma_source_asset', $filename);

    return (int) $attachment_id;
}

function vkusno_doma_upsert_page(array $data): int
{
    $existing = get_page_by_path($data['slug']);

    $page_args = [
        'post_title' => $data['title'],
        'post_name' => $data['slug'],
        'post_type' => 'page',
        'post_status' => 'publish',
        'post_content' => $data['content'],
    ];

    if ($existing instanceof WP_Post) {
        $page_args['ID'] = $existing->ID;
        return (int) wp_update_post($page_args);
    }

    return (int) wp_insert_post($page_args);
}

function vkusno_doma_seed_site(): void
{
    $seed_data = vkusno_doma_get_seed_data();

    foreach ($seed_data['pages'] as $page_data) {
        vkusno_doma_upsert_page($page_data);
    }

    $front_page = get_page_by_path('home');
    if ($front_page instanceof WP_Post) {
        update_option('show_on_front', 'page');
        update_option('page_on_front', $front_page->ID);
    }

    update_option('page_for_posts', 0);

    foreach ($seed_data['categories'] as $category_data) {
        $term = get_term_by('slug', $category_data['slug'], 'recipe_category');

        if (!$term) {
            $term = wp_insert_term($category_data['name'], 'recipe_category', [
                'slug' => $category_data['slug'],
                'description' => $category_data['description'],
            ]);
        } else {
            wp_update_term($term->term_id, 'recipe_category', [
                'name' => $category_data['name'],
                'description' => $category_data['description'],
            ]);
        }

        $term_id = is_array($term) ? (int) $term['term_id'] : (int) $term->term_id;
        $image_id = vkusno_doma_import_local_image($category_data['image']);

        if ($image_id) {
            update_term_meta($term_id, '_vkusno_doma_image_id', $image_id);
        }
    }

    foreach ($seed_data['collections'] as $collection_data) {
        $existing_collection_term = get_term_by('slug', $collection_data['slug'], 'recipe_collection');

        if (!$existing_collection_term) {
            wp_insert_term($collection_data['title'], 'recipe_collection', [
                'slug' => $collection_data['slug'],
                'description' => $collection_data['excerpt'],
            ]);
        } else {
            wp_update_term($existing_collection_term->term_id, 'recipe_collection', [
                'name' => $collection_data['title'],
                'description' => $collection_data['excerpt'],
            ]);
        }
    }

    foreach ($seed_data['recipes'] as $recipe_data) {
        $existing = get_page_by_path($recipe_data['slug'], OBJECT, 'recipe');

        $post_args = [
            'post_title' => $recipe_data['title'],
            'post_name' => $recipe_data['slug'],
            'post_type' => 'recipe',
            'post_status' => 'publish',
            'comment_status' => 'open',
            'ping_status' => 'closed',
            'post_excerpt' => $recipe_data['excerpt'],
            'post_content' => $recipe_data['content'],
        ];

        if ($existing instanceof WP_Post) {
            $post_args['ID'] = $existing->ID;
            $post_id = (int) wp_update_post($post_args);
        } else {
            $post_id = (int) wp_insert_post($post_args);
        }

        if (!$post_id) {
            continue;
        }

        wp_set_object_terms($post_id, [$recipe_data['category']], 'recipe_category');
        update_post_meta($post_id, '_vkusno_doma_rating', $recipe_data['rating']);
        update_post_meta($post_id, '_vkusno_doma_time', $recipe_data['time']);
        update_post_meta($post_id, '_vkusno_doma_author', $recipe_data['author']);

        $attachment_id = vkusno_doma_import_local_image($recipe_data['image'], $post_id);

        if ($attachment_id) {
            set_post_thumbnail($post_id, $attachment_id);
        }
    }

    $collection_ids = [];

    foreach ($seed_data['collections'] as $collection_data) {
        $existing = get_page_by_path($collection_data['slug']);
        $page_args = [
            'post_title' => $collection_data['title'],
            'post_name' => $collection_data['slug'],
            'post_type' => 'page',
            'post_status' => 'publish',
            'post_excerpt' => $collection_data['excerpt'],
            'post_content' => $collection_data['content'],
        ];

        if ($existing instanceof WP_Post) {
            $page_args['ID'] = $existing->ID;
            $page_id = (int) wp_update_post($page_args);
        } else {
            $page_id = (int) wp_insert_post($page_args);
        }

        if (!$page_id) {
            continue;
        }

        update_post_meta($page_id, '_vkusno_doma_is_collection', '1');
        $attachment_id = vkusno_doma_import_local_image($collection_data['image'], $page_id);

        if ($attachment_id) {
            set_post_thumbnail($page_id, $attachment_id);
        }

        $collection_ids[] = $page_id;
    }

    update_option('vkusno_doma_collection_page_ids', $collection_ids);

    foreach ($seed_data['collections'] as $collection_data) {
        if (empty($collection_data['recipes'])) {
            continue;
        }

        foreach ($collection_data['recipes'] as $recipe_slug) {
            $recipe = get_page_by_path($recipe_slug, OBJECT, 'recipe');

            if (!$recipe instanceof WP_Post) {
                continue;
            }

            wp_set_object_terms((int) $recipe->ID, [$collection_data['slug']], 'recipe_collection', true);
        }
    }

    vkusno_doma_seed_menus();

    update_option('vkusno_doma_seed_version', VKUSNO_DOMA_SEED_VERSION);
    update_option('vkusno_doma_flush_rewrite', '1');
}

function vkusno_doma_enable_recipe_comments(): void
{
    if (get_option('vkusno_doma_recipe_comments_enabled') === VKUSNO_DOMA_SEED_VERSION) {
        return;
    }

    $recipe_ids = get_posts([
        'post_type' => 'recipe',
        'post_status' => 'any',
        'fields' => 'ids',
        'posts_per_page' => -1,
    ]);

    foreach ($recipe_ids as $recipe_id) {
        wp_update_post([
            'ID' => (int) $recipe_id,
            'comment_status' => 'open',
            'ping_status' => 'closed',
        ]);
    }

    update_option('default_comment_status', 'open');
    update_option('comment_registration', '0');
    update_option('vkusno_doma_recipe_comments_enabled', VKUSNO_DOMA_SEED_VERSION);
}
add_action('init', 'vkusno_doma_enable_recipe_comments', 30);

function vkusno_doma_migrate_author_collection(): void
{
    if (get_option('vkusno_doma_author_collection_migrated') === VKUSNO_DOMA_SEED_VERSION) {
        return;
    }

    $collection = get_page_by_path('baking-for-tea');

    if ($collection instanceof WP_Post) {
        wp_update_post([
            'ID' => $collection->ID,
            'post_title' => 'Від автора',
            'post_excerpt' => 'Особиста добірка рецептів, які ви додаєте та радите самі.',
            'post_content' => '<p>Ця добірка створена для авторських рецептів: тут можна зберігати улюблені страви, фірмові ідеї та все, чим хочеться ділитися від свого імені.</p>',
        ]);
    }

    update_option('vkusno_doma_author_collection_migrated', VKUSNO_DOMA_SEED_VERSION);
}
add_action('init', 'vkusno_doma_migrate_author_collection', 31);

function vkusno_doma_remove_menu_item(int $menu_id, string $title): void
{
    $items = wp_get_nav_menu_items($menu_id) ?: [];

    foreach ($items as $item) {
        if ($item->title === $title) {
            wp_delete_post((int) $item->ID, true);
        }
    }
}

function vkusno_doma_remove_blog_content(): void
{
    if (get_option('vkusno_doma_blog_removed') === VKUSNO_DOMA_SEED_VERSION) {
        return;
    }

    $blog_posts = get_posts([
        'post_type' => 'post',
        'post_status' => 'any',
        'numberposts' => -1,
        'fields' => 'ids',
    ]);

    foreach ($blog_posts as $blog_post_id) {
        wp_delete_post((int) $blog_post_id, true);
    }

    $blog_page = get_page_by_path('blog');

    if ($blog_page instanceof WP_Post) {
        wp_delete_post($blog_page->ID, true);
    }

    update_option('page_for_posts', 0);

    $primary_menu = wp_get_nav_menu_object('Головне меню') ?: wp_get_nav_menu_object('Основное меню');

    if ($primary_menu) {
        vkusno_doma_remove_menu_item((int) $primary_menu->term_id, 'Блог');
        vkusno_doma_remove_menu_item((int) $primary_menu->term_id, 'Blog');
    }

    update_option('vkusno_doma_blog_removed', VKUSNO_DOMA_SEED_VERSION);
}
add_action('init', 'vkusno_doma_remove_blog_content', 32);

function vkusno_doma_migrate_collection_taxonomy(): void
{
    if (get_option('vkusno_doma_collection_taxonomy_migrated') === VKUSNO_DOMA_SEED_VERSION) {
        return;
    }

    $seed_data = vkusno_doma_get_seed_data();

    foreach ($seed_data['collections'] as $collection_data) {
        $term = get_term_by('slug', $collection_data['slug'], 'recipe_collection');

        if (!$term) {
            $term = wp_insert_term($collection_data['title'], 'recipe_collection', [
                'slug' => $collection_data['slug'],
                'description' => $collection_data['excerpt'],
            ]);
        } else {
            wp_update_term($term->term_id, 'recipe_collection', [
                'name' => $collection_data['title'],
                'description' => $collection_data['excerpt'],
            ]);
        }

        if (empty($collection_data['recipes'])) {
            continue;
        }

        foreach ($collection_data['recipes'] as $recipe_slug) {
            $recipe = get_page_by_path($recipe_slug, OBJECT, 'recipe');

            if ($recipe instanceof WP_Post) {
                wp_set_object_terms((int) $recipe->ID, [$collection_data['slug']], 'recipe_collection', true);
            }
        }
    }

    update_option('vkusno_doma_collection_taxonomy_migrated', VKUSNO_DOMA_SEED_VERSION);
}
add_action('init', 'vkusno_doma_migrate_collection_taxonomy', 33);

function vkusno_doma_migrate_parmesan_recipe(): void
{
    if (get_option('vkusno_doma_parmesan_recipe_migrated') === VKUSNO_DOMA_SEED_VERSION) {
        return;
    }

    $seed_data = vkusno_doma_get_seed_data();
    $cheese_category = null;
    $parmesan_recipe = null;

    foreach ($seed_data['categories'] as $category_data) {
        if ($category_data['slug'] === 'snacks') {
            $cheese_category = $category_data;
            break;
        }
    }

    foreach ($seed_data['recipes'] as $recipe_data) {
        if ($recipe_data['slug'] === 'domashnii-parmezan') {
            $parmesan_recipe = $recipe_data;
            break;
        }
    }

    if (!$cheese_category || !$parmesan_recipe) {
        return;
    }

    $term = get_term_by('slug', 'snacks', 'recipe_category');

    if (!$term) {
        $term = wp_insert_term($cheese_category['name'], 'recipe_category', [
            'slug' => $cheese_category['slug'],
            'description' => $cheese_category['description'],
        ]);
    } else {
        wp_update_term($term->term_id, 'recipe_category', [
            'name' => $cheese_category['name'],
            'description' => $cheese_category['description'],
        ]);
    }

    $term_id = is_array($term) ? (int) $term['term_id'] : (int) $term->term_id;
    $term_image_id = vkusno_doma_import_local_image($cheese_category['image']);

    if ($term_image_id) {
        update_term_meta($term_id, '_vkusno_doma_image_id', $term_image_id);
    }

    $existing_recipe = get_page_by_path($parmesan_recipe['slug'], OBJECT, 'recipe');
    $post_args = [
        'post_title' => $parmesan_recipe['title'],
        'post_name' => $parmesan_recipe['slug'],
        'post_type' => 'recipe',
        'post_status' => 'publish',
        'comment_status' => 'open',
        'ping_status' => 'closed',
        'post_excerpt' => $parmesan_recipe['excerpt'],
        'post_content' => $parmesan_recipe['content'],
    ];

    if ($existing_recipe instanceof WP_Post) {
        $post_args['ID'] = $existing_recipe->ID;
        $post_id = (int) wp_update_post($post_args);
    } else {
        $post_id = (int) wp_insert_post($post_args);
    }

    if ($post_id) {
        wp_set_object_terms($post_id, ['snacks'], 'recipe_category');
        update_post_meta($post_id, '_vkusno_doma_rating', $parmesan_recipe['rating']);
        update_post_meta($post_id, '_vkusno_doma_time', $parmesan_recipe['time']);
        update_post_meta($post_id, '_vkusno_doma_author', $parmesan_recipe['author']);

        $attachment_id = vkusno_doma_import_local_image($parmesan_recipe['image'], $post_id);

        if ($attachment_id) {
            set_post_thumbnail($post_id, $attachment_id);
        }
    }

    update_option('vkusno_doma_parmesan_recipe_migrated', VKUSNO_DOMA_SEED_VERSION);
}
add_action('init', 'vkusno_doma_migrate_parmesan_recipe', 34);

function vkusno_doma_seed_menus(): void
{
    $primary_menu_name = 'Головне меню';
    $footer_menu_name = 'Нижнє меню';
    $primary_menu = wp_get_nav_menu_object($primary_menu_name);
    $footer_menu = wp_get_nav_menu_object($footer_menu_name);

    $primary_menu_id = $primary_menu ? (int) $primary_menu->term_id : wp_create_nav_menu($primary_menu_name);
    $footer_menu_id = $footer_menu ? (int) $footer_menu->term_id : wp_create_nav_menu($footer_menu_name);

    if ($primary_menu_id) {
        vkusno_doma_create_menu_item($primary_menu_id, 'Головна', home_url('/'));
        vkusno_doma_create_menu_item($primary_menu_id, 'Рецепти', get_post_type_archive_link('recipe'));
        vkusno_doma_create_menu_item($primary_menu_id, 'Категорії', home_url('/#categories'));
        vkusno_doma_create_menu_item($primary_menu_id, 'Добірки', home_url('/#collections'));
        vkusno_doma_remove_menu_item($primary_menu_id, 'Блог');
    }

    if ($footer_menu_id) {
        vkusno_doma_create_menu_item($footer_menu_id, 'Про нас', vkusno_doma_get_page_url('about-us'));
        vkusno_doma_create_menu_item($footer_menu_id, 'Правила', vkusno_doma_get_page_url('rules'));
        vkusno_doma_create_menu_item($footer_menu_id, 'Конфіденційність', vkusno_doma_get_page_url('privacy-policy'));
        vkusno_doma_create_menu_item($footer_menu_id, 'Контакти', vkusno_doma_get_page_url('contacts'));
    }

    $locations = get_theme_mod('nav_menu_locations', []);
    $locations['primary'] = $primary_menu_id;
    $locations['footer'] = $footer_menu_id;
    set_theme_mod('nav_menu_locations', $locations);
}

function vkusno_doma_create_menu_item(int $menu_id, string $title, string $url): void
{
    $items = wp_get_nav_menu_items($menu_id) ?: [];

    foreach ($items as $item) {
        if ($item->title === $title) {
            return;
        }
    }

    wp_update_nav_menu_item($menu_id, 0, [
        'menu-item-title' => $title,
        'menu-item-url' => $url,
        'menu-item-status' => 'publish',
    ]);
}
