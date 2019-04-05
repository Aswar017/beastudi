<?php
/**
 * Services widget type A
 *
 * @package Perth
 */

class Perth_Services_Type_A extends WP_Widget {

    function perth_services_type_a() {
		$widget_ops = array('classname' => 'perth_services_widget', 'description' => __( 'Show what services you are able to provide.', 'perth') );
        parent::__construct(false, $name = __('Perth FP: Services Type A', 'perth'), $widget_ops);
		$this->alt_option_name = 'perth_services_widget';
			
    }
	
	function form($instance) {
		$title     		= isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$number    		= isset( $instance['number'] ) ? intval( $instance['number'] ) : -1;
		$category   	= isset( $instance['category'] ) ? esc_attr( $instance['category'] ) : '';
		$see_all   		= isset( $instance['see_all'] ) ? esc_url_raw( $instance['see_all'] ) : '';		
		$see_all_text  	= isset( $instance['see_all_text'] ) ? esc_html( $instance['see_all_text'] ) : '';
		$two_cols 		= isset( $instance['two_cols'] ) ? (bool) $instance['two_cols'] : false;
	?>

	<p><?php _e('In order to display this widget, you must first add some services from your admin area.', 'perth'); ?></p>
	<p>
	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'perth'); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
	</p>
	<p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of services to show (-1 shows all of them):', 'perth' ); ?></label>
	<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>
    <p><label for="<?php echo $this->get_field_id('see_all'); ?>"><?php _e('The URL for your button [In case you want a button below your services block]', 'perth'); ?></label>
	<input class="widefat custom_media_url" id="<?php echo $this->get_field_id( 'see_all' ); ?>" name="<?php echo $this->get_field_name( 'see_all' ); ?>" type="text" value="<?php echo $see_all; ?>" size="3" /></p>	
    <p><label for="<?php echo $this->get_field_id('see_all_text'); ?>"><?php _e('The text for the button [Defaults to <em>See all our services</em> if left empty]', 'perth'); ?></label>
	<input class="widefat custom_media_url" id="<?php echo $this->get_field_id( 'see_all_text' ); ?>" name="<?php echo $this->get_field_name( 'see_all_text' ); ?>" type="text" value="<?php echo $see_all_text; ?>" size="3" /></p>
	<p><label for="<?php echo $this->get_field_id( 'category' ); ?>"><?php _e( 'Enter the slug for your category or leave empty to show all services.', 'perth' ); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id( 'category' ); ?>" name="<?php echo $this->get_field_name( 'category' ); ?>" type="text" value="<?php echo $category; ?>" size="3" /></p>
	<p><input class="checkbox" type="checkbox" <?php checked( $two_cols ); ?> id="<?php echo $this->get_field_id( 'two_cols' ); ?>" name="<?php echo $this->get_field_name( 'two_cols' ); ?>" />
	<label for="<?php echo $this->get_field_id( 'two_cols' ); ?>"><?php _e( 'Display services in two columns instead of three?', 'perth' ); ?></label></p>

	<?php
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] 			= strip_tags($new_instance['title']);
		$instance['number'] 		= strip_tags($new_instance['number']);
		$instance['see_all'] 		= esc_url_raw( $new_instance['see_all'] );	
		$instance['see_all_text'] 	= strip_tags($new_instance['see_all_text']);		
		$instance['category'] 		= strip_tags($new_instance['category']);
		$instance['two_cols'] 		= isset( $new_instance['two_cols'] ) ? (bool) $new_instance['two_cols'] : false;		
		    			
		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['perth_services']) )
			delete_option('perth_services');		  
		  
		return $instance;
	}
	
	function widget($args, $instance) {
		$cache = array();
		if ( ! $this->is_preview() ) {
			$cache = wp_cache_get( 'perth_services', 'widget' );
		}

		if ( ! is_array( $cache ) ) {
			$cache = array();
		}

		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}

		ob_start();
		extract($args);

		$title 			= ( ! empty( $instance['title'] ) ) ? $instance['title'] : '';
		$title 			= apply_filters( 'widget_title', $title, $instance, $this->id_base );
		$see_all 		= isset( $instance['see_all'] ) ? esc_url($instance['see_all']) : '';
		$see_all_text 	= isset( $instance['see_all_text'] ) ? esc_html($instance['see_all_text']) : '';		
		$number 		= ( ! empty( $instance['number'] ) ) ? intval( $instance['number'] ) : -1;
		if ( ! $number )
			$number 	= -1;				
		$category 		= isset( $instance['category'] ) ? esc_attr($instance['category']) : '';
		$two_cols 		= isset( $instance['two_cols'] ) ? $instance['two_cols'] : false;

		$services = new WP_Query( array(
			'no_found_rows'       => true,
			'post_status'         => 'publish',
			'post_type' 		  => 'services',
			'posts_per_page'	  => $number,
			'category_name'		  => $category			
		) );

		echo $args['before_widget'];

		if ($services->have_posts()) :
?>
			<?php if ( $title ) echo $before_title . $title . $after_title; ?>
				<div class="service-area">
					<?php while ( $services->have_posts() ) : $services->the_post(); ?>
						<?php $icon = get_post_meta( get_the_ID(), 'wpcf-service-icon', true ); ?>
						<?php $link = get_post_meta( get_the_ID(), 'wpcf-service-link', true ); ?>
						<?php if ( !$two_cols ) : ?>
						<div class="service columns3">
						<?php else : ?>
						<div class="service columns2">
						<?php endif; ?>
							<?php if ( has_post_thumbnail() ) : ?>
							<div class="service-thumb">
								<?php the_post_thumbnail('perth-client-thumb'); ?>
							</div>
							<?php elseif ($icon) : ?>	
							<div class="svg-container service-icon-svg">
								<?php perth_svg_1(); ?>
								<span class="service-icon">
									<?php echo '<i class="fa ' . esc_html($icon) . '"></i>'; ?>
								</span>								
							</div>										
							<?php endif; ?>							
							<div class="content">
								<h4 class="service-title">
								<?php if ($link) : ?>
									<a href="<?php echo esc_url($link); ?>"><?php the_title(); ?></a>
								<?php else : ?>
									<?php the_title(); ?>
								<?php endif; ?>
								</h4>
								<?php the_content(); ?>
							</div>	
						</div>
					<?php endwhile; ?>
				</div>	

				<?php if ($see_all != '') : ?>
					<a href="<?php echo esc_url($see_all); ?>" class="button more-button">
						<?php if ($see_all_text) : ?>
							<?php echo $see_all_text; ?>
						<?php else : ?>
							<?php echo __('See all our services', 'perth'); ?>
						<?php endif; ?>
					</a>
				<?php endif; ?>				
	<?php
		wp_reset_postdata();
		endif;
		echo $args['after_widget'];

		if ( ! $this->is_preview() ) {
			$cache[ $args['widget_id'] ] = ob_get_flush();
			wp_cache_set( 'perth_services', $cache, 'widget' );
		} else {
			ob_end_flush();
		}
	}
	
}