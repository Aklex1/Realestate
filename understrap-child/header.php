<?php
/**
 * The header for our theme
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<header id="header" class="bg-dark text-white py-3">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <!-- Логотип -->
            <div class="logo">
                <a href="<?php echo home_url(); ?>">
                    <img src="https://creditbanker.ru/wp-content/uploads/2024/09/images.png" alt="<?php bloginfo('name'); ?>" />
                </a>
            </div>

            <!-- Кнопка для мобильного меню -->
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Главное меню -->
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'main-menu',
                    'container'      => 'div',
                    'container_id'   => 'navbarNav',
                    'container_class'=> 'collapse navbar-collapse',
                    'menu_class'     => 'navbar-nav ml-auto',
                    'walker'         => new WP_Bootstrap_Navwalker(),
                    'depth'          => 2,
                ));
                ?>
            </nav>
            </div><?php custom_breadcrumbs(); ?>
        </div>
    
     
</header>

<div id="content" class="site-content">
