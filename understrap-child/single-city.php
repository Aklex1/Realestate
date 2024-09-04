<?php get_header(); ?>

<div class="container mt-5">
    <h1 class="mb-4"><?php the_title(); ?></h1>

    <?php if (has_post_thumbnail()) : ?>
        <div class="city-thumbnail mb-4">
            <?php the_post_thumbnail('large', ['class' => 'img-fluid']); ?>
        </div>
    <?php endif; ?>

    <div class="city-description mb-5">
        <?php the_content(); ?>
    </div>

    <h2 class="mb-4">Последние объекты недвижимости в этом городе</h2>
    
    <?php
    // Получение ID текущего города
    $city_id = get_the_ID();

    // Запрос для получения объектов недвижимости, связанных с этим городом
    $args = array(
        'post_type' => 'real_estate',
        'meta_query' => array(
            array(
                'key' => '_city_id',
                'value' => $city_id,
                'compare' => '='
            )
        ),
        'posts_per_page' => 10,
        'order' => 'DESC',
        'orderby' => 'date',
    );
    $city_real_estate = new WP_Query($args);

    if ($city_real_estate->have_posts()) :
        echo '<div class="row">';
        while ($city_real_estate->have_posts()) : $city_real_estate->the_post(); ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <?php if (has_post_thumbnail()) : ?>
                        <img src="<?php the_post_thumbnail_url('medium'); ?>" class="card-img-top" alt="<?php the_title(); ?>">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?php the_title(); ?></h5>
                        <p class="card-text">Площадь: <?php echo esc_html(get_field('field_area')); ?> кв.м</p>
                        <p class="card-text">Стоимость: <?php echo esc_html(get_field('field_price')); ?> руб.</p>
                        <p class="card-text">Адрес: <?php echo esc_html(get_field('field_address')); ?></p>
                        <p class="card-text">Жилая площадь: <?php echo esc_html(get_field('field_living_area')); ?> кв.м</p>
                        <p class="card-text">Этаж: <?php echo esc_html(get_field('field_floor')); ?></p>
                        <a href="<?php the_permalink(); ?>" class="btn btn-primary">Подробнее</a>
                    </div>
                </div>
            </div>
        <?php endwhile;
        echo '</div>';
        wp_reset_postdata();
    else :
        echo '<p>Нет объектов недвижимости в этом городе.</p>';
    endif;
    ?>
</div>

<?php get_footer(); ?>
