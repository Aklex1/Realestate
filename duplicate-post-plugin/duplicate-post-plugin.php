<?php
/**
 * Plugin Name: Duplicate Post Plugin
 * Description: Плагин для копирования кастомных постов в черновик с добавлением ссылки "Копировать" в списке постов.
 * Version: 1.0
 * Author: @Alex_millller (telegram)
 * License: GPL2
 */

// Функция для копирования поста как черновика
function duplicate_post_as_draft() {
    global $wpdb;

    if ( !isset($_GET['duplicate_post']) || !current_user_can('edit_posts') )
        return;

    $post_id = absint($_GET['duplicate_post']);
    $post = get_post($post_id);

    if (isset($post) && $post != null) {
        $new_post_id = wp_insert_post(array(
            'post_title'    => $post->post_title . ' (Копия)',
            'post_content'  => $post->post_content,
            'post_status'   => 'draft',
            'post_author'   => $post->post_author,
            'post_type'     => $post->post_type,
        ));

        if ($new_post_id) {
            // Копирование метаполей
            $meta_data = get_post_meta($post_id);
            foreach ($meta_data as $key => $values) {
                foreach ($values as $value) {
                    add_post_meta($new_post_id, $key, $value);
                }
            }

            // Перенаправление на редактирование новой записи
            wp_redirect(admin_url('post.php?action=edit&post=' . $new_post_id));
            exit;
        }
    }
}
add_action('admin_init', 'duplicate_post_as_draft');

// Добавление ссылки "Копировать" для кастомного поста
function add_duplicate_post_link($actions, $post) {
    if ($post->post_type == 'real_estate') {
        $actions['duplicate'] = '<a href="' . wp_nonce_url(admin_url('admin.php?action=duplicate_post&duplicate_post=' . $post->ID), 'duplicate_post') . '" title="Копировать эту запись">Копировать</a>';
    }
    return $actions;
}
add_filter('post_row_actions', 'add_duplicate_post_link', 10, 2);
