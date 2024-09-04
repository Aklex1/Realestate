<?php


function understrap_child_enqueue_styles() {
    // Подключение стилей родительской темы
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    
    // Подключение стилей дочерней темы
    wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style'));
    
     
    
   
}
add_action('wp_enqueue_scripts', 'understrap_child_enqueue_styles');


//Подключение Navwalker 
//require_once get_template_directory() . '/inc/class-wp-bootstrap-navwalker.php';

//Кастомная запись недвижимость
function create_real_estate_post_type() {
    $labels = array(
        'name' => __('Недвижимость'),
        'singular_name' => __('Недвижимость'),
        'add_new' => __('Добавить новую'),
        'add_new_item' => __('Добавить новую недвижимость'),
        'edit_item' => __('Редактировать недвижимость'),
        'new_item' => __('Новая недвижимость'),
        'view_item' => __('Просмотр недвижимости'),
        'search_items' => __('Поиск недвижимости'),
        'not_found' => __('Недвижимость не найдена'),
        'not_found_in_trash' => __('В корзине недвижимости не найдено'),
        'all_items' => __('Вся недвижимость'),
        'menu_name' => __('Недвижимость'),
        'name_admin_bar' => __('Недвижимость')
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-building',
        'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'rewrite' => array('slug' => 'real-estate'),
    );

    register_post_type('real_estate', $args);
}
add_action('init', 'create_real_estate_post_type');


//Тип недвижимости 
function create_real_estate_taxonomy() {
    $labels = array(
        'name' => __('Тип недвижимости'),
        'singular_name' => __('Тип недвижимости'),
        'search_items' => __('Поиск типов недвижимости'),
        'all_items' => __('Все типы недвижимости'),
        'parent_item' => __('Родительский тип'),
        'parent_item_colon' => __('Родительский тип:'),
        'edit_item' => __('Редактировать тип'),
        'update_item' => __('Обновить тип'),
        'add_new_item' => __('Добавить новый тип'),
        'new_item_name' => __('Новое название типа'),
        'menu_name' => __('Тип недвижимости'),
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'property-type'),
    );

    register_taxonomy('property_type', array('real_estate'), $args);
}
add_action('init', 'create_real_estate_taxonomy');




function create_city_post_type() {
    $labels = array(
        'name' => __('Города'),
        'singular_name' => __('Город'),
        'add_new' => __('Добавить новый'),
        'add_new_item' => __('Добавить новый город'),
        'edit_item' => __('Редактировать город'),
        'new_item' => __('Новый город'),
        'view_item' => __('Просмотреть город'),
        'search_items' => __('Искать города'),
        'not_found' => __('Города не найдены'),
        'not_found_in_trash' => __('В корзине города не найдены'),
        'all_items' => __('Все города'),
        'menu_name' => __('Города'),
        'name_admin_bar' => __('Город')
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-admin-site',
        'supports' => array('title', 'editor', 'thumbnail'),
        'rewrite' => array('slug' => 'city'),
    );

    register_post_type('city', $args);
}
add_action('init', 'create_city_post_type');



function add_city_meta_box() {
    add_meta_box(
        'city_meta_box',
        'Город',
        'render_city_meta_box',
        'real_estate',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'add_city_meta_box');

function render_city_meta_box($post) {
    // Получаем текущие значения метаполя
    $selected_city = get_post_meta($post->ID, '_city_id', true);

    // Получаем все посты типа "Город"
    $cities = get_posts(array(
        'post_type' => 'city',
        'posts_per_page' => -1
    ));
    ?>
    <select name="city_id" id="city_id" class="postbox">
        <option value="">Выберите город</option>
        <?php foreach ($cities as $city) : ?>
            <option value="<?php echo esc_attr($city->ID); ?>" <?php selected($selected_city, $city->ID); ?>>
                <?php echo esc_html($city->post_title); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php
}

function save_city_meta_box($post_id) {
    if (array_key_exists('city_id', $_POST)) {
        update_post_meta(
            $post_id,
            '_city_id',
            $_POST['city_id']
        );
    }
}
add_action('save_post', 'save_city_meta_box');

function handle_add_real_estate() {
    // Проверка nonce
    check_ajax_referer('add_real_estate_nonce', 'nonce');

    // Проверка и валидация полей
    if (empty($_POST['title']) || empty($_POST['area']) || empty($_POST['price']) || empty($_POST['address']) || empty($_POST['living_area']) || empty($_POST['floor'])) {
        wp_send_json_error('Все поля обязательны для заполнения.');
    }

    // Создание нового объекта недвижимости
    $post_id = wp_insert_post(array(
        'post_title'  => sanitize_text_field($_POST['title']),
        'post_type'   => 'real_estate',
        'post_status' => 'publish',
    ));

    if (is_wp_error($post_id)) {
        wp_send_json_error('Ошибка создания записи.');
    }

    // Сохранение данных в ACF полях
    update_field('field_area', sanitize_text_field($_POST['area']), $post_id);
    update_field('field_price', sanitize_text_field($_POST['price']), $post_id);
    update_field('field_address', sanitize_text_field($_POST['address']), $post_id);
    update_field('field_living_area', sanitize_text_field($_POST['living_area']), $post_id);
    update_field('field_floor', sanitize_text_field($_POST['floor']), $post_id);

    // Обработка загрузки изображения
    if (!empty($_FILES['image']['name'])) {
        $uploaded_image = media_handle_upload('image', $post_id);

        if (is_wp_error($uploaded_image)) {
            wp_send_json_error('Ошибка загрузки изображения.');
        } else {
            set_post_thumbnail($post_id, $uploaded_image);
        }
    }

    wp_send_json_success('Объект недвижимости успешно добавлен.');
}

add_action('wp_ajax_add_real_estate', 'handle_add_real_estate');
add_action('wp_ajax_nopriv_add_real_estate', 'handle_add_real_estate');




//Хлебные крошки
function custom_breadcrumbs() {
    // Выход из функции, если не включена отображение хлебных крошек
    if (!is_front_page()) {
        echo '<nav class="breadcrumbs" aria-label="Breadcrumbs">';
        echo '<a href="' . home_url() . '">Главная</a> &gt; ';
        if (is_category() || is_single()) {
            echo '<span>' . get_the_category_list(', ') . '</span>';
            if (is_single()) {
                echo ' &gt; <span>' . get_the_title() . '</span>';
            }
        } elseif (is_page()) {
            echo '<span>' . get_the_title() . '</span>';
        }
        echo '</nav>';
    }
}


// Регистрация меню
function register_my_menu() {
    register_nav_menus(array(
        'main-menu' => __('Main Menu')
    ));
}
add_action('init', 'register_my_menu');



