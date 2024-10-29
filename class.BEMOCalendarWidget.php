<?php
/* Add big calendar widget */
// Creating the widget 
class bemocalendar_widget extends WP_Widget {

function __construct() {
parent::__construct(
// Base ID of your widget
'bemocalendar_widget', 

// Widget name will appear in UI
__('BeMoore Calendar Widget', 'bemocalendar_widget_domain'), 

// Widget description
array( 'description' => __( 'BeMoore Event Calendar Widget', 'bemocalendar_widget_domain' ), ) 
);
/*
	if ( is_active_widget(false, false, $this->id_base) )
		add_action( 'wp_head', array(&$this, 'bemocalendarwidget_init') );
*/
}

// Creating widget front-end
// This is where the action happens
	add_action('init', 'bemocalendar_register_script');
	// This is the big calendar 
	// - set path to json feed -
	// - tell JS to use this variable instead of a static value -
			'events' => $jsonevents,
   wp_enqueue_style( 'bemocalendar' );

	$title = apply_filters( 'widget_title', $instance['title'] );
	// before and after widget arguments are defined by themes
	echo $args['before_widget'];
	if ( ! empty( $title ) )
	echo $args['before_title'] . $title . $args['after_title'];

	// This is where you run the code and display the output
	echo '<div id=\''.$this->id.'\'></div>';
	echo '<div id=\''.$this->id.'_color\'></div>';
	echo $args['after_widget'];
}
		
// Widget Backend 
public function form( $instance ) {
if ( isset( $instance[ 'title' ] ) ) {
$title = $instance[ 'title' ];
}
else {
$title = __( 'New title', 'bemocalendar_widget_domain' );
}
// Widget admin form
?>
<p>
<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>
<?php 
}
	
// Updating widget replacing old instances with new
public function update( $new_instance, $old_instance ) {
$instance = array();
$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
return $instance;
}
} // Class bemocalendar_widget ends here
?>