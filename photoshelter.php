<?php

/**
 * Plugin Name: PhotoShelter Gallery Widget
 * Plugin URI: http://graphpaperpress.com/plugins/photoshelter-gallery-widget/
 * Description: A widget for showing your recent PhotoShelter galleries in your sidebar.
 * Version: 1.6.0
 * Author: Thad Allender
 * Author URI: http://graphpaperpress.com
*/

// Load the widget
add_action( 'widgets_init', 'photoshelter_gallery_load_widget' );

// Register the widget
function photoshelter_gallery_load_widget() {
	register_widget( 'WP_Widget_PhotoShelter_Gallery' );
}

// Widget class that creates the settings, input form, update and display
class WP_Widget_PhotoShelter_Gallery extends WP_Widget {

	// Setup the widget
	function WP_Widget_PhotoShelter_Gallery() {
		$widget_ops = array( 'classname' => 'photoshelter-gallery-widget', 'description' => __('Display your latest PhotoShelter galleries' ) );
		$control_ops = array( 'width' => 400, 'height' => 350 );
		$this->WP_Widget( 'photoshelter-gallery-widget', __('PhotoShelter Galleries', 'photoshelter-gallery-widget'), $widget_ops, $control_ops );
	}

	// Display settings for printing widget to screen
	function widget( $args, $instance ) {
		extract($args);

		$title = apply_filters('widget_title', $instance['title'] );
		$id = apply_filters( 'widget_text', $instance['id'] );
		$collections = apply_filters( 'widget_text', $instance['collections'] );
		$galleries = apply_filters( 'widget_text', $instance['galleries'] );
		$show = apply_filters( 'widget_text', $instance['show'] );
		$img = apply_filters( 'widget_text', $instance['img'] );
		$img_size = apply_filters( 'widget_text', $instance['img_size'] );
		$desc = apply_filters( 'widget_text', $instance['desc'] );
		$count = apply_filters( 'widget_text', $instance['count'] );


		// Before widget (defined by themes)
		echo "\n" . $before_widget . "\n";
		// Display the widget title if one was input (before and after defined by themes)
		if ( $title )
			echo $before_title . $title . $after_title. "\n";
		echo '<ul class="photoshelter-gallery-widget">' . "\n";
		// check if user supplied an id
		if ( !$id ) {
			echo '<p class="error">Please add your PhotoShelter label to the <a href="' . site_url('wp-admin/widgets.php') . '">PhotoShelter Gallery Widget</a>.</p>';
			return;
		} else {
			// set url
			$xmlurl = 'http://' . $id . '.photoshelter.com/gallery-list?feed=xml';
			// check if xml file exists
			if ( photoshelter_gallery_widget_get_http_response_code( $xmlurl ) == "404" ) {
				echo '<p class="error">' . __( 'Cannot load the xml file from PhotoShelter. Please check the ID you supplied in the PhotoShelter Widget.', 'photoshelter-gallery-widget' ) . '</p>';
				return;
			} else {
				// check if curl is enabled
				if ( !function_exists( 'curl_init' ) ) {
					echo '<p class="error">' . __( 'Your server doesn\'t have the necessary capabilities to use this plugin. Contact your web host and ask them to enable CURL and file_get_contents.', 'photoshelter-gallery-widget' ) . '</p>';
					return;
				}
				// sanitize the xml
				$xml = esc_html( $xmlurl );
				// curl the xml
				$xml = photoshelter_gallery_widget_file_get_contents_curl( $xml, false );
				// if $xml exists, then it is valid xml, so lets continue
				if ( $xml ) {
					$xml = new SimpleXMLElement( $xml );
					if ( $galleries == 1 ) :
						$i = 0;
						// for galleries
						foreach ( $xml->galleries->gallery as $gallery ) {
							// how many to show?
							if( $i == $show ) break;
							// lets do this. show me the galleries.
							echo '<li>';
							echo '<h6><a href="http://' . $id . '.photoshelter.com/gallery/' . $gallery->name . '/' . $gallery->id . '">' . $gallery->name . '</a></h6>';
							if ( $img == 1 ) :
								echo '<a href="http://' . $id . '.photoshelter.com/gallery/' . $gallery->name . '/' . $gallery->id . '">
									  	<img width="' . $img_size . '" src="http://c.photoshelter.com/img-get/' . $gallery->key_image . '" border="0" />
									  </a>';
							endif;
							if ( $desc == 1 )
								echo '<p>' . $gallery->description . '</p>', "\n";
							if ( $count == 1 )
								echo '<div class="imagecount">' . __( 'Photos', 'photoshelter-gallery-widget') . ': ' . $gallery->image_count . ' </div>', "\n";
							echo '</li>';
							$i++;
						} // end foreach
					endif; // end galleries optional listing
					// for collections
					if ( $collections == 1 ) :
						$j = 0;
						foreach ( $xml->gallery_collections->gallery_collection as $collections ) {
							// how many to show?
							if( $j == $show ) break;
							// lets do this. show me the galleries.
							echo '<li>';
							echo '<h6><a href="http://' . $id . '.photoshelter.com/gallery-collection/' . $collections->name . '/' . $collections->id . '">' . $collections->name . '</a></h6>';
							if ( $img == 1 ) :
								echo '<a href="http://' . $id . '.photoshelter.com/gallery-collection/' . $collections->name . '/' . $collections->id . '">
									  	<img width="' . $img_size . '" src="http://c.photoshelter.com/img-get/' . $collections->key_image . '" border="0" />
									  </a>';
							endif;
							if ( $desc == 1 )
								echo '<p>' . $collections->description . '</p>', "\n";
							if ( $count == 1 )
								echo '<div class="imagecount">' . __( 'Galleries', 'photoshelter-gallery-widget') . ': ' . $collections->gallery_count . ' </div>', "\n";
							echo '</li>';
							$j++;
						} // end foreach
					endif; // end collectons optional listing
				} else { // $xml did not exist, so it must be invalid xml
					echo '<p class="error">Your <a href="' . $xmlurl . '">PhotoShelter xml feed</a> is broken. Remove all non-alphanumeric characters in your image or gallery descriptions in PhotoShelter. Remove all quotes, brackets, dashes, basically anything that isn\'t a number or letter. If that doesn\'t solve your problem, contact PhotoShelter and tell them your gallery xml contains errors.</p>';
				}
			} // xml exists
		} // end check if there is no $id set
		echo "</ul>" . "\n";
		echo $after_widget . "\n";
	}

	// Update the widget settings when saved
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['id'] = strip_tags($new_instance['id']);
		$instance['collections'] = isset($new_instance['collections']);
		$instance['galleries'] = isset($new_instance['galleries']);
		$instance['show'] = $new_instance['show'];
		$instance['img'] = isset($new_instance['img']);
		$instance['img_size'] = strip_tags($new_instance['img_size']);
		$instance['desc'] = isset($new_instance['desc']);
		$instance['count'] = isset($new_instance['count']);

		return $instance;
	}


	// Displays the widget settings controls on the widget panel.
	// Make use of the get_field_id() and get_field_name() function
	// when creating your form elements. This handles the confusing stuff.
	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'PhotoShelter label' => '', 'show' => '', 'collections' => '', 'galleries' => true, 'img' => '', 'img_size' => '310', 'desc' => '', 'count' => '' ) );
		$title = strip_tags($instance['title']);

		if ( ! empty( $instance['id'] ) )
		    $id = strip_tags( $instance['id'] );
		else
		    $id = null;

		$img_size = strip_tags($instance['img_size']);
		$show = $instance['show'];
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('<strong>Title of this Widget:</strong>'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('id'); ?>"><?php _e('<strong>Enter your PhotoShelter label here:</strong>'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('id'); ?>" name="<?php echo $this->get_field_name('id'); ?>" type="text" value="<?php echo esc_attr($id); ?>" /></p>

		<p><input id="<?php echo $this->get_field_id('collections'); ?>" name="<?php echo $this->get_field_name('collections'); ?>" type="checkbox" <?php checked($instance['collections']); ?> />&nbsp;<label for="<?php echo $this->get_field_id('collections'); ?>"><?php _e('Display gallery collections? If checked, the number of gallery collections listed will be determined by the display count option below.'); ?></label></p>
		<p><input id="<?php echo $this->get_field_id('galleries'); ?>" name="<?php echo $this->get_field_name('galleries'); ?>" type="checkbox" <?php checked($instance['galleries']); ?> />&nbsp;<label for="<?php echo $this->get_field_id('galleries'); ?>"><?php _e('Display galleries not in collections? If checked, the number of galleries listed will be determined by the display count option below.'); ?></label></p>

		<p><?php _e('How many items would you like to display?'); ?>
		<select class="widefat" name="<?php echo $this->get_field_name('show'); ?>" id="<?php echo $this->get_field_id('show'); ?>" style="display:inline;width:auto">
		<?php for($i=1;$i<=15;$i++) { ?>
		<option value="<?php echo $i;?>"<?php echo ($i == esc_attr($show) ) ? 'selected="selected"' : ''; ?> ><?php echo $i;?></option>
		<?php } ?>
		</select>
		</p>

		<p><input id="<?php echo $this->get_field_id('img'); ?>" name="<?php echo $this->get_field_name('img'); ?>" type="checkbox" <?php checked($instance['img']); ?> />&nbsp;<label for="<?php echo $this->get_field_id('img'); ?>"><?php _e('Display thumbnails?'); ?></label></p>
		<p><label for="<?php echo $this->get_field_id('img_size'); ?>"><?php _e('<strong>Maximum thumbnail width in pixels:</strong>'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('img_size'); ?>" name="<?php echo $this->get_field_name('img_size'); ?>" type="text" value="<?php echo esc_attr($img_size); ?>" /></p>

		<p><input id="<?php echo $this->get_field_id('desc'); ?>" name="<?php echo $this->get_field_name('desc'); ?>" type="checkbox" <?php checked($instance['desc']); ?> />&nbsp;<label for="<?php echo $this->get_field_id('desc'); ?>"><?php _e('Display descriptions?'); ?></label></p>
		<p><input id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="checkbox" <?php checked($instance['count']); ?> />&nbsp;<label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Display image count?'); ?></label></p>

<?php
	}
}

// Add some CSS
function photoshelter_gallery_widget_css() {

	echo "
	<style type='text/css'>
		#sidebar ul.photoshelter-gallery-widget, .photoshelter-gallery-widget {list-style:none;list-style-position:inside;margin:0 0 1em 0;padding:0;border:none}
		#sidebar ul.photoshelter-gallery-widget li, .photoshelter-gallery-widget li {display:block;margin:0;padding:0;background:none;border:none}
		#sidebar ul.photoshelter-gallery-widget li a img, .photoshelter-gallery-widget li a img {margin:0;padding:0;max-width:100%;height:auto}
		#sidebar ul.photoshelter-gallery-widget li a, .photoshelter-gallery-widget li a {background:none;border:none;padding:0}
		#sidebar ul.photoshelter-gallery-widget li a:hover, .photoshelter-gallery-widget li a:hover {background:none;}
		#sidebar ul.photoshelter-gallery-widget h6, .photoshelter-gallery-widget h6 {margin:1em 0;}
		#footer ul.photoshelter-gallery-widget h6 a {color:#999}
		#footer ul.photoshelter-gallery-widget h6 a:hover {color:#ccc}
		#sidebar ul.photoshelter-gallery-widget .imagecount, .photoshelter-gallery-widget .imagecount { text-align:right; font-style:italic; font-size:.9em; color:#ccc}
		.error {background: #FFF6BF; padding: 1em;}
		.error a {color:red}
	</style>
	";
}

// Adds CSS to head
add_action('wp_head', 'photoshelter_gallery_widget_css');

// checks the header response status. Used for making sure xml exists.
function photoshelter_gallery_widget_get_http_response_code($url) {
    $headers = get_headers($url);
    return substr($headers[0], 9, 3);
}

// curl for getting xml
function photoshelter_gallery_widget_file_get_contents_curl($url) {
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
	curl_setopt($ch, CURLOPT_URL, $url);

	$data = curl_exec($ch);
	curl_close($ch);

	if (@simplexml_load_string($data)) {
		return $data;
	}

}

?>