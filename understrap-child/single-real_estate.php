<?php
/**
 * Template Name: Single Real Estate
 * Template Post Type: real_estate
 */

get_header(); ?>


<div class="container mt-5">

    <?php if ( have_posts() ) :
        while ( have_posts() ) : the_post(); ?>

            <div class="real-estate-header mb-4">
                <h1><?php the_title(); ?></h1>
            </div>

            <div class="real-estate-thumbnail mb-4">
                <?php if ( has_post_thumbnail() ) {
                    the_post_thumbnail('full', ['class' => 'img-fluid']);
                } ?>
            </div>

            <div class="real-estate-content mb-4">
                <?php the_content(); ?>
            </div>

            <div class="real-estate-meta mb-4">
                <ul class="list-unstyled">
                    <li><strong>Площадь:</strong> <?php the_field('площадь'); ?> кв.м</li>
                    <li><strong>Стоимость:</strong> <?php the_field('стоимость'); ?> руб.</li>
                    <li><strong>Адрес:</strong> <?php the_field('адрес'); ?></li>
                    <li><strong>Жилая площадь:</strong> <?php the_field('жилая_площадь'); ?> кв.м</li>
                    <li><strong>Этаж:</strong> <?php the_field('этаж'); ?></li>
                </ul>
            </div>

            <?php
            // Получение ID города
            $city_id = get_post_meta(get_the_ID(), '_city_id', true);
            if ($city_id) {
                $city = get_post($city_id);
                if ($city) : ?>
                    <div class="real-estate-city mb-4">
                        <h2>Город: <?php echo esc_html($city->post_title); ?></h2>
                        <?php if (has_post_thumbnail($city_id)) : ?>
                            <div class="city-thumbnail mb-3">
                                <?php echo get_the_post_thumbnail($city_id, 'thumbnail', ['class' => 'img-fluid']); ?>
                            </div>
                        <?php endif; ?>
                        <div class="city-description">
                            <?php echo wpautop($city->post_content); ?>
                        </div>
                    </div>
                <?php endif;
            }
            ?>

        <?php endwhile;
    endif; ?>

</div>

<?php get_footer(); ?>
