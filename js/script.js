jQuery(document).ready(function ($) {
    $('#add-slide').on('click', function (e) {
        e.preventDefault();
        var mediaUploader = wp.media({
            title: 'Оберіть зображення для слайду',
            button: {
                text: 'Додати зображення'
            },
            multiple: false
        });

        mediaUploader.on('select', function () {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#slider-images').append(`
                <div class="slider-image" data-id="${attachment.id}">
                    <img src="${attachment.url}" style="max-width: 150px;"/>
                    <button class="button remove-slide">Видалити</button>
                </div>
            `);
        });

        mediaUploader.open();
    });

    // Видалення зображення
    $('#slider-images').on('click', '.remove-slide', function () {
        $(this).closest('.slider-image').remove();
    });

    // Робимо зображення сортуємими
    $('#slider-images').sortable();

    // Збереження ID зображень через AJAX
    $('#slider-animation-form').on('submit', function (e) {
        e.preventDefault();

        let imageIds = [];
        $('#slider-images .slider-image').each(function () {
            imageIds.push($(this).data('id'));
        });

        let animationTime = $('#slider-animation-time').val();

        let animationType = $('#slider-animation-type').val();

        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'save_slider_images',
                image_ids: imageIds.join(','),
                slider_animation_time: animationTime,
                slider_animation_type: animationType
            },
            success: function (response) {
            if (response.success) {
                alert('Успішно слайд збережено');
            } else {
                alert('Помилка при збереженні: ' + response.data);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert('Сталася помилка при обробці запиту.');
        }
});
    });
});
