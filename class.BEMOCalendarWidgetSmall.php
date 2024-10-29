<?php
/* Add big calendar widget */
// Creating the widget 
class bemocalendar_widget_small extends WP_Widget {

function __construct() {
parent::__construct(
// Base ID of your widget
'bemocalendar_widget_small', 

// Widget name will appear in UI
__('BeMoore Calendar Widget Small', 'bemocalendar_widget_small_domain'), 

// Widget description
array( 'description' => __( 'BeMoore Small Event Calendar Widget', 'bemocalendar_widget_small_domain' ), ) 
);
}

// Creating widget front-end
// This is where the action happens
public function widget( $args, $instance ) {
   /* This is the small calendar */		
	$title = apply_filters( 'widget_title', $instance['title'] );
	// before and after widget arguments are defined by themes
	echo $args['before_widget'];
	if ( ! empty( $title ) )
	echo $args['before_title'] . $title . $args['after_title'];
	// This is where you run the code and display the output	$plugin_dir_path = dirname(__FILE__);
	require_once 'class.CalendarDisplay.php';	$display = new CalendarDisplay();	echo $display->getSmallCalendar();	echo $args['after_widget'];}
// Widget Backend 
public function form( $instance ) {
if ( isset( $instance[ 'title' ] ) ) {
$title = $instance[ 'title' ];
}
else {
$title = __( 'New title', 'bemocalendar_widget_small_domain' );
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