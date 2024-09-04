<?php get_header(); ?>

<div class="container">
    <!-- Секция объектов недвижимости -->
    <section class="recent-real-estate">
        <h2 class="text-center"></h2>
        <?php
        $recent_real_estate = new WP_Query(array(
            'post_type' => 'real_estate',
            'posts_per_page' => 8, // Количество объектов для отображения
            'order' => 'DESC',
            'orderby' => 'date',
        ));

        if ($recent_real_estate->have_posts()) :
            echo '<div class="row">';
            while ($recent_real_estate->have_posts()) : $recent_real_estate->the_post(); ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="real-estate-item card h-100">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="real-estate-thumbnail card-img-top">
                                <?php the_post_thumbnail('medium', array('class' => 'img-fluid')); ?>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h3 class="card-title"><?php the_title(); ?></h3>
                            <p>Площадь: <?php echo esc_html(get_field('field_area')); ?> кв.м</p>
                            <p>Стоимость: <?php echo esc_html(get_field('field_price')); ?> руб.</p>
                            <p>Адрес: <?php echo esc_html(get_field('field_address')); ?></p>
                            <p>Жилая площадь: <?php echo esc_html(get_field('field_living_area')); ?> кв.м</p>
                            <p>Этаж: <?php echo esc_html(get_field('field_floor')); ?></p>
                            <a href="<?php the_permalink(); ?>" class="btn btn-primary">Подробнее</a>
                        </div>
                    </div>
                </div>
            <?php endwhile;
            echo '</div>';
            wp_reset_postdata();
        else :
            echo '<p>Нет объектов недвижимости для отображения.</p>';
        endif;
        ?>
    </section>

    <!-- Секция городов -->
    <section class="recent-cities mt-5">
        <h2 class="text-center">Выбрать город</h2>
        <?php
        $recent_cities = new WP_Query(array(
            'post_type' => 'city',
            'posts_per_page' => 4, // Количество городов для отображения
            'order' => 'DESC',
            'orderby' => 'date',
        ));

        if ($recent_cities->have_posts()) :
            echo '<div class="row">';
            while ($recent_cities->have_posts()) : $recent_cities->the_post(); ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="city-item card h-100 text-center">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="city-thumbnail rounded-circle mx-auto" style="width: 150px; height: 150px;">
                                <?php the_post_thumbnail('thumbnail', array('class' => 'img-fluid rounded-circle')); ?>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h3 class="card-title"><?php the_title(); ?></h3>
                            <!-- <p><?php the_excerpt(); ?></p> -->
                            <a href="<?php the_permalink(); ?>" class="btn btn-primary">Подробнее</a>
                        </div>
                    </div>
                </div>
            <?php endwhile;
            echo '</div>';
            wp_reset_postdata();
        else :
            echo '<p>Нет городов для отображения.</p>';
        endif;
        ?>
    </section>
</div>

<!-- Секция баннера и формы -->
<section class="container mt-5">
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="banner">
                <img src="https://creditbanker.ru/wp-content/uploads/2024/09/real-estate-2-design-template-9765a6e146f1fee8513fe84d982358e9_screen.jpg" class="img-fluid rounded" alt="Banner">
            </div>
        </div>
        <div class="col-lg-6">
            <div class="add-real-estate">
                <h2>Добавить объект недвижимости</h2>
                <form id="real-estate-form" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post" enctype="multipart/form-data">
                    <?php wp_nonce_field('add_real_estate_nonce', 'nonce'); ?>
                    <input type="hidden" name="action" value="add_real_estate">
                    <div class="form-group">
                        <label for="title">Название:</label>
                        <input type="text" id="title" name="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="area">Площадь (кв.м):</label>
                        <input type="number" id="area" name="area" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="price">Стоимость (руб.):</label>
                        <input type="number" id="price" name="price" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Адрес:</label>
                        <input type="text" id="address" name="address" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="living_area">Жилая площадь (кв.м):</label>
                        <input type="number" id="living_area" name="living_area" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="floor">Этаж:</label>
                        <input type="number" id="floor" name="floor" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="image">Изображение:</label>
                        <input type="file" id="image" name="image" class="form-control-file" accept="image/*" required>
                    </div>
                    <div class="form-group">
                        <input type="submit" value="Добавить объект" class="btn btn-primary">
                    </div>
                    <div id="response"></div>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
jQuery(document).ready(function($) {
    $('#real-estate-form').on('submit', function(e) {
        e.preventDefault();

        var formData = new FormData(this);

        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.success) {
                    $('#response').html('<p>Объект недвижимости добавлен успешно!</p>');
                    $('#real-estate-form')[0].reset();
                } else {
                    $('#response').html('<p>' + response.data + '</p>');
                }
            },
            error: function(response) {
                $('#response').html('<p>Произошла ошибка. Попробуйте еще раз.</p>');
            }
        });
    });
});
</script>

<?php get_footer(); ?>
