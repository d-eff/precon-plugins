<?php
/**
 * Plugin Name: Img Widget Test
 * Plugin URI: http:policyrecon.com
 * Description: Img Widget test
 * Version: 1.0.0
 * Text Domain: imgwidgettest
 */
// Creating the widget 
    class cc_widget_image extends WP_Widget {
        function cc_widget_image() {
            $widget_ops = array('classname' => 'cc_widget_image', 'description' => __( 'Select and show an image.', 'cc_language' ) );
            $this->WP_Widget('cc_widget_image', 'CC - ' . __( 'Image', 'cc_language' ), $widget_ops);
        }

        function form($instance) {
            $instance = wp_parse_args( (array) $instance, $defaults );
            $title = $instance['title'];
            $image = $instance['image'];
            $copy = $instance['copy'];
            $checkbox = $instance['checkbox'];
            ?>
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Wiget Title', 'cc_language'); ?>:
                <input id="<?php echo $this->get_field_id('title'); ?>" class="widefat" type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" /></label>
                <span>Please include a title even if you intend to hide it.</span>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('image'); ?>"><?php _e('Current Image', 'cc_language'); ?>:
                <input id="<?php echo $this->get_field_id('image'); ?>" class="widefat" type="text" name="<?php echo $this->get_field_name('image'); ?>" value="<?php echo $instance['image']; ?>" /></label>
            </p>
            <p>
                <label for="cc-image-upload-file"><?php _e('Image', 'cc_language'); ?>:</label><br>
                <label for="cc-image-upload-file">
                    <input type="text" id="cc-image-upload-file" class="widefat custom_media_image custom_media_url" name="<?php echo $this->get_field_name('image'); ?>" value="<?php echo $instance['image']; ?>" />
                    <input type="button" id="cc-image-upload-file-button" class="button custom_media_upload" value="Upload file" />
                    <label for="cc-image-upload-file"><span class="description">Enter URL or upload file</span></label>
                </label>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('copy'); ?>"><?php _e('Copy', 'cc_language'); ?>:
                <textarea id="<?php echo $this->get_field_id('copy'); ?>" class="widefat" type="text" name="<?php echo $this->get_field_name('copy'); ?>" /><?php echo $instance['copy']; ?></textarea></label>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('checkbox'); ?>"><?php _e('Do not show title', 'cc_language'); ?></label>
                <input id="<?php echo $this->get_field_id('checkbox'); ?>" type="checkbox" name="<?php echo $this->get_field_name('checkbox'); ?>" value="true" <?php checked( 'true', $checkbox ); ?> />
            </p>
            <?php
        }

        function update($new_instance, $old_instance) {
            $instance = $old_instance;          
                $instance['title'] = strip_tags($new_instance['title']); 
                $instance['image'] = $new_instance['image']; 
                $instance['checkbox'] = strip_tags($new_instance['checkbox']); 
                $instance['copy'] = $new_instance['copy'];
            return $instance;
        }

        function widget($args, $instance) {
            extract($args, EXTR_SKIP);

            $title = apply_filters('widget_title', empty($instance['title']) ? __('Image') : $instance['title'], $instance, $this->id_base);
            $copy = $instance['copy'];

            echo $before_widget;

            // display the widget title 
                if ( $instance['checkbox'] == 'true' ) {
                } else {
                    if ( $title )
                    echo $before_title . $title . $after_title;
                }

            // display the widget content 
                //echo the_post_thumbnail(array(220,200));
                echo '<div class="precon-imgWidgetContainer">' .
                        '<img src="' . $instance['image'] . '" class="precon-imgWidgetImage">' .
                        '<div class="precon-imgWidgetTextWrap"><span class="precon-imgWidgetText">' . $copy . '</span></div>' .
                      '</div>';
        echo $after_widget;
        }
    }
    function imgwidget_enqueue_scripts(){
        wp_enqueue_script('mepload', plugins_url() . '/precon/mepload.js');
        wp_enqueue_media();
    }
    function imgwidget_enqueue_style() {
        wp_enqueue_style('precon-image-style', plugins_url() . '/precon/imagestyle.css');
    }
    add_action( 'widgets_init', create_function('', 'return register_widget("cc_widget_image");') );

    add_action('admin_enqueue_scripts', 'imgwidget_enqueue_scripts');
    add_action('wp_enqueue_scripts', 'imgwidget_enqueue_style');