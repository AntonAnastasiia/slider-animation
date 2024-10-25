<?php
/*
Plugin Name: Slider Animation
Description: Простий плагін для додавання анімованого слайдера за допомогою шорткоду.
Version: 2.0
Author: Anton Anastasiia
*/

if (!defined('ABSPATH')) {
    exit; 
}

class SliderAnimation {

    public function __construct() {
        add_shortcode('slider_animation', [$this, 'render_slider']);
        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('admin_menu', [$this, 'register_admin_menu']);
        add_action('wp_ajax_save_slider_images', [$this, 'save_slider_images']);

    }

    public function admin_enqueue_scripts() {
        wp_enqueue_style('style', plugin_dir_url(__FILE__) . 'css/style.css');
        wp_enqueue_script('script', plugin_dir_url(__FILE__) . 'js/script.js', ['jquery'], '1.0', true);
    	if (is_admin()) {
        	wp_enqueue_media(); 
    	}
    }

    public function enqueue_scripts() {
        wp_enqueue_style('slider-animation-style', plugin_dir_url(__FILE__) . 'css/slider.css');
        wp_enqueue_script('slider-animation-script', plugin_dir_url(__FILE__) . 'js/slider.js', ['jquery'], '1.0', true);
    	if (is_admin()) {
        	wp_enqueue_media(); 
    	}
    }

	public function render_slider($atts) {
    	$image_ids = get_option('slider_animation_images', '');
    	$image_ids = !empty($image_ids) ? explode(',', $image_ids) : [];
        $image_urls = array_map('wp_get_attachment_url', $image_ids);
    	$animation_type = get_option('slider_animation_type', 'default'); 
    	$animation_time = get_option('slider_animation_time', '5'); 

    	ob_start();
    	?>
    	<div id="slider-animation" class="slider-animation <?php echo esc_attr($animation_type); ?>" data-animation-time="<?php echo esc_attr($animation_time); ?>">
        <?php   if (!empty($image_ids)){
                    foreach ($image_urls as $url) {
                        if($animation_type == 'fade') {
                            echo '<div class="slide first" data-url = ' . esc_url($url) . ' style="background-image: url(' . esc_url($url) . ');"></div>';  
                        } else {
                            echo '<div class="slide" data-url = ' . esc_url($url) . ' style="background-image: url(' . esc_url($url) . ');"></div>';                              
                        }
                    }
                } else {?>
                    <p>Немає зображень для відображення.</p>
                <?php } ?>
    	</div>
    	<?php
    	return ob_get_clean();
	}

    public function register_admin_menu() {
        add_menu_page(
            'Slider Animation Settings',
            'Slider Animation',
            'manage_options',
            'slider-animation',
            [$this, 'render_admin_page'],
            'dashicons-images-alt2'
        );
    }

    public function render_admin_page() {
        $image_ids = get_option('slider_animation_images', '');
        $image_ids = !empty($image_ids) ? explode(',', $image_ids) : [];
        $animation_type = get_option('slider_animation_type', 'default'); 
        $animation_time = get_option('slider_animation_time', '5');
        ?>
        <div class="wrap">
            <h1>Налаштування Slider Animation</h1>
            <form id="slider-animation-form">
                <div id="slider-images">
                    <?php foreach ($image_ids as $id): 
                        $image_url = wp_get_attachment_image_url($id, 'thumbnail'); ?>
                        <div class="slider-image" data-id="<?php echo esc_attr($id); ?>">
                            <img src="<?php echo esc_url($image_url); ?>" style="max-width: 150px;"/>
                            <button class="button remove-slide">Видалити</button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="animation-settings">
                <h2>Інтервал анімації, сукенди</h2>
            	<select name="slider_animation_time" id="slider-animation-time">
                	<option value="3" <?php selected($animation_time, '3'); ?>>3</option>
                	<option value="4" <?php selected($animation_time, '4'); ?>>4</option>
                	<option value="5" <?php selected($animation_time, '5'); ?>>5</option>
                	<option value="6" <?php selected($animation_time, '6'); ?>>6</option>
                	<option value="7" <?php selected($animation_time, '7'); ?>>7</option>
                	<option value="8" <?php selected($animation_time, '8'); ?>>8</option>
            	</select>
            	</div>
                <div class="animation-settings">
                <h2>Тип анімації</h2>
            	<select name="slider_animation_type" id="slider-animation-type">
                	<option value="slide-left" <?php selected($animation_type, 'slide-left'); ?>>Slide left</option>
                	<option value="rotate" <?php selected($animation_type, 'rotate'); ?>>Rotate</option>
                	<option value="fade" <?php selected($animation_type, 'fade'); ?>>Fade</option>
                	<option value="zoom" <?php selected($animation_type, 'zoom'); ?>>Zoom</option>
               	 	<option value="flip" <?php selected($animation_type, 'flip'); ?>>Flip</option>
               	 	<option value="slide-rigth" <?php selected($animation_type, 'slide-rigth'); ?>>Slide rigth</option>
            	</select>
            	</div>
            	<button type="button" class="button button-primary" id="add-slide">Додати зображення</button>
                <input type="submit" class="button button-primary" value="Зберегти зміни">
            </form>
        </div>
        <?php
    }

    public function save_slider_images() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Недостатньо прав для збереження.');
        }

        if (isset($_POST['image_ids']) || isset($_POST['slider_animation_time']) || isset($_POST['slider_animation_type'])) {
            update_option('slider_animation_images', sanitize_text_field($_POST['image_ids']));
            update_option('slider_animation_time', sanitize_text_field($_POST['slider_animation_time']));
            update_option('slider_animation_type', sanitize_text_field($_POST['slider_animation_type']));
            wp_send_json_success('збережено.');
        }  else {
            wp_send_json_error('Немає даних для збереження.');
        }
    }
}

new SliderAnimation();

