<?php
class CalendarDisplay
{
protected $id = '';
protected $type = '';

function __construct($id = '',$type='bemocalendar')
{
	$this->id = $id;
	$this->type = $type;
	add_action('init', array(&$this, 'init'));
}

public function init(){
	
	if($this->type == 'bemocalendar')
		add_action('init', 'bemocalendar_register_script');
}

//Display the main calendar
public function getMainCalendar()
{
	wp_enqueue_script('fullcalendar', plugins_url('fullcalendar.js', __FILE__), array('jquery'));
	wp_enqueue_script('bemocalendar', plugins_url('bemocalendar.js', __FILE__), array('jquery'));
	wp_register_style( 'bemocalendar', plugins_url('fullcalendar.css', __FILE__));

	// - set path to json feed -
	$jsonevents = plugins_url('/json.php',__FILE__);
	$jsoneventcategories = plugins_url('/json_categories.php',__FILE__);
	$active_widgets = get_option('sidebars_widgets');

	// - tell JS to use this variable instead of a static value -
	wp_localize_script( 'fullcalendar', 'bemocalendar', 
		array(
			'events' => $jsonevents,
			'eventcategories' => $jsoneventcategories,
			'cssid' => '#'.$this->id
			)
	);

   wp_enqueue_style( 'bemocalendar' );

	/* This is the big calendar */		
	// This is where you run the code and display the output
	$retval = '<div id=\''.$this->id.'\'></div>';
	$retval .= '<div id=\''.$this->id.'_color\'></div>';
   
   return $retval;
}

//Display the small calendar
public function getSmallCalendar()
{
	return '<img src="' . plugins_url( 'images/pro-version-small-widget.png' , __FILE__ ) . '" > ';
}

public function getCalendarEventsList()
{
	return '<img src="' . plugins_url( 'images/pro-version-list-widget.png' , __FILE__ ) . '" > ';
}

}
?>