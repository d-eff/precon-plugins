<?php
/**
 * Plugin Name: Policy Recon Img Widget
 * Plugin URI: http:www.policyrecon.com
 * Description: Policy Recon Img Widget
 * Version: 1.0.0
 * Text Domain: pr-img-widget
 */
// Creating the widget 
    class precon_widget_image extends WP_Widget {
        function precon_widget_image() {
            $widget_ops = array('classname' => 'precon_widget_image', 'description' => __( 'Display an image with (optional) text over it.', 'cc_language' ) );
            $this->WP_Widget('precon_widget_image', 'Policy Recon Img Widget', $widget_ops);
        }

        function form($instance) {
            $defaults = array(
                'title' => '',
                'image' => '',
                'copy' => '',
                'checkbox' => '',
                'link' => ''
            );
            $instance = wp_parse_args( (array) $instance, $defaults );
            $title = $instance['title'];
            $image = $instance['image'];
            $copy = $instance['copy'];
            $checkbox = $instance['checkbox'];
            $link = $instance['link'];
            ?>
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Wiget Title', 'cc_language'); ?>:
                <input id="<?php echo $this->get_field_id('title'); ?>" class="widefat" type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" /></label>
                <span>Please include a title even if you intend to hide it.</span>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('image'); ?>"><?php _e('Current Image', 'cc_language'); ?>:
                <input id="<?php echo $this->get_field_id('image'); ?>" class="widefat" type="text" name="<?php echo $this->get_field_name('image'); ?>" value="<?php echo $image; ?>" /></label>
            </p>
            <p>
                <label for="cc-image-upload-file"><?php _e('Image', 'cc_language'); ?>:</label><br>
                <label for="cc-image-upload-file">
                    <input type="text" id="cc-image-upload-file" class="widefat custom_media_image custom_media_url" name="<?php echo $this->get_field_name('image'); ?>" value="<?php echo $image; ?>" />
                    <input type="button" id="cc-image-upload-file-button" class="button custom_media_upload" value="Upload file" />
                    <label for="cc-image-upload-file"><span class="description">Enter URL or upload file</span></label>
                </label>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('copy'); ?>"><?php _e('Copy', 'cc_language'); ?>:
                <textarea id="<?php echo $this->get_field_id('copy'); ?>" class="widefat" type="text" rows="5" name="<?php echo $this->get_field_name('copy'); ?>"><?php echo $copy; ?></textarea>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('link'); ?>"><?php _e('Link', 'cc_language'); ?> (please include 'http://'):
                <input id="<?php echo $this->get_field_id('link'); ?>" class="widefat" type="text" name="<?php echo $this->get_field_name('link'); ?>" value="<?php echo $link; ?>" /></label>
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
                $instance['link'] = $new_instance['link'];
            return $instance;
        }

        function widget($args, $instance) {
            extract($args, EXTR_SKIP);

            $title = apply_filters('widget_title', empty($instance['title']) ? __('Image') : $instance['title'], $instance, $this->id_base);
            $copy = $instance['copy'];
            $link = $instance['link'];

            echo $before_widget;

            // display the widget title 
                if ( $instance['checkbox'] == 'true' ) {
                } else {
                    if ( $title )
                    echo $before_title . $title . $after_title;
                }

            // display the widget content 
                echo '<div class="precon-imgWidgetContainer">' .
                        '<a href="' . $link .'"><img src="' . $instance['image'] . '" class="precon-imgWidgetImage">' .
                        '<div class="precon-imgWidgetTextWrap"><span class="precon-imgWidgetText">' . $copy . '</span></div></a>' .
                      '</div>';
        echo $after_widget;
        }
    }
    function precon_imgwidget_enqueue_scripts(){
        wp_enqueue_script('precon-mepload', plugins_url() . '/precon-plugins/precon-mepload.js');
        wp_enqueue_media();
    }
    function precon_imgwidget_enqueue_style() {
        wp_enqueue_style('precon-image-style', plugins_url() . '/precon-plugins/precon-imgstyle.css');
    }
    add_action( 'widgets_init', create_function('', 'return register_widget("precon_widget_image");') );

    add_action('admin_enqueue_scripts', 'precon_imgwidget_enqueue_scripts');
    add_action('wp_enqueue_scripts', 'precon_imgwidget_enqueue_style');
