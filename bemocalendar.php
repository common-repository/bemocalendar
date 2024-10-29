<?php
/**
 * Plugin Name: BEMOCalendar
 * Plugin URI: http://www.bemoore.com/bemocalendar
 * Description: A jQuery Calendar plugin because I couldn't find anything suitable
 * Version: 0.0.3
 * Author: Bob Moore, BeMoore Software
 * Author URI: http://www.bemoore.com
 * License: GPL2
*/
/*  
Copyright 2013  Bob Moore  (email : bob.moore@bemoore.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
 
//Widget loading
include('class.BEMOCalendarWidget.php');
include('class.BEMOCalendarWidgetSmall.php');
include('class.BEMOCalendarWidgetEventsList.php');

//Add the small thumbnail size
add_image_size( 'small-calendar-thumbnail-size', 50, 50 );

//Init the errors handler

// Register and load the calendar widgets
function bemocalendar_load_widgets() {
	register_widget( 'bemocalendar_widget' );
	register_widget( 'bemocalendar_widget_small' );
	register_widget( 'bemocalendar_widget_eventslist' );
}
add_action( 'widgets_init', 'bemocalendar_load_widgets' );

// 0. Base

add_action('admin_init', 'bemocalendar_functions_css');

function bemocalendar_functions_css() {
	wp_enqueue_style('bemocalendar-functions-css', plugins_url('css/bemocalendar-functions.css',__FILE__));
}

//0. Add a nice icon
function add_menu_icons_styles(){ 
?>
<style>
#adminmenu .menu-icon-bemocalendar_events div.wp-menu-image:before {
		content: "\f145";
}
</style>
<?php
}
add_action( 'admin_head', 'add_menu_icons_styles' );

// 1. Custom Post Type Registration (Events)

add_action( 'init', 'create_event_postype' );

function create_event_postype() {
$labels = array(
    'name' => _x('Events', 'post type general name'),
    'singular_name' => _x('Event', 'post type singular name'),
    'add_new' => _x('Add New', 'events'),
    'add_new_item' => __('Add New Event'),
    'edit_item' => __('Edit Event'),
    'new_item' => __('New Event'),
    'view_item' => __('View Event'),
    'search_items' => __('Search Events'),
    'not_found' =>  __('No events found'),
    'not_found_in_trash' => __('No events found in Trash'),
    'parent_item_colon' => '',
);

$args = array(
    'label' => __('Events'),
    'labels' => $labels,
    'public' => true,
	'menu_icon' => 'menu-icon-events',
    'can_export' => true,
    'show_ui' => true,
    '_builtin' => false,
    '_edit_link' => 'post.php?post=%d', // ?
    'capability_type' => 'post',
    'menu_icon' => '\f145',
    'hierarchical' => false,
    'rewrite' => array( "slug" => "events" ),
    'supports'=> array('title', 'excerpt', 'editor' , 'thumbnail') ,
    'show_in_nav_menus' => true,
	'has_archive' => true,
    'taxonomies' => array( 'bemocalendar_eventcategory')
);

register_post_type( 'bemocalendar_events', $args);

}

// 2. Custom Taxonomy Registration (Event Types)

function create_eventcategory_taxonomy() {

    $labels = array(
        'name' => _x( 'Categories', 'categories' ),
        'singular_name' => _x( 'Category', 'category' ),
        'search_items' =>  __( 'Search Categories' ),
        'popular_items' => __( 'Popular Categories' ),
        'all_items' => __( 'All Categories' ),
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => __( 'Edit Category' ),
        'update_item' => __( 'Update Category' ),
        'add_new_item' => __( 'Add New Category' ),
        'new_item_name' => __( 'New Category Name' ),
        'separate_items_with_commas' => __( 'Separate categories with commas' ),
        'add_or_remove_items' => __( 'Add or remove categories' ),
        'choose_from_most_used' => __( 'Choose from the most used categories' ),
    );

    register_taxonomy('bemocalendar_eventcategory','bemocalendar_events', array(
        'label' => __('Event Category'),
        'labels' => $labels,
        'hierarchical' => true,
        'show_ui' => true,
        'query_var' => true,
        'rewrite' => array( 'slug' => 'event-category' ),
    ));

}

add_action( 'init', 'create_eventcategory_taxonomy', 0 );


//2.5 Add field(s) to custom taxonomy
$SelfPath = plugins_url( '' , __FILE__ );
include('bemocalendar_event_category_taxonomy.php');

// 3. Show Columns

add_filter ("manage_edit-bemocalendar_events_columns", "bemocalendar_events_edit_columns");
add_action ("manage_posts_custom_column", "bemocalendar_events_custom_columns");


function bemocalendar_events_edit_columns($columns) {

    $columns = array(
        "cb" => "<input type=\"checkbox\" />",
        "title" => "Event",
        "bemocalendar_col_ev_cat" => "Category",
        "bemocalendar_col_ev_date" => "Dates",
        "bemocalendar_col_ev_times" => "Times",
		"bemocalendar_col_ev_allday" => "All Day Event?",
        "bemocalendar_col_ev_thumb" => "Thumbnail",
        "bemocalendar_col_ev_desc" => "Description",
        );

    return $columns;

}

function bemocalendar_events_custom_columns($column) {

    global $post;
    $custom = get_post_custom();
    switch ($column)
        {
		
            case "bemocalendar_col_ev_cat":
                // - show taxonomy terms -
                $eventcats = get_the_terms($post->ID, "bemocalendar_eventcategory");
                $eventcats_html = array();
                if ($eventcats) {
                    foreach ($eventcats as $eventcat)
                    array_push($eventcats_html, $eventcat->name);
                    echo implode($eventcats_html, ", ");
                } else {
                _e('None', 'bemocalendar');;
                }
            break;
            case "bemocalendar_col_ev_date":
                // - show dates -
                $startd = $custom["bemocalendar_events_startdate"][0];
                $endd = $custom["bemocalendar_events_enddate"][0];
                $startdate = date("F j, Y", $startd);
                $enddate = date("F j, Y", $endd);
                echo $startdate . '<br /><em>' . $enddate . '</em>';
            break;
            case "bemocalendar_col_ev_times":
                // - show times -
                $startt = $custom["bemocalendar_events_startdate"][0];
                $endt = $custom["bemocalendar_events_enddate"][0];
                $time_format = get_option('time_format');
                $starttime = date($time_format, $startt);
                $endtime = date($time_format, $endt);
                echo $starttime . ' - ' .$endtime;
            break;
            case "bemocalendar_col_ev_allday":
                // - show allday -
                $allday = $custom["bemocalendar_events_allday"][0];
				
				if((int)$allday == 1)
					echo "True";
				else
					echo "False";
            break;
            case "bemocalendar_col_ev_thumb":
                // - show thumb -
                $post_image_id = get_post_thumbnail_id(get_the_ID());
                if ($post_image_id) {
                    $thumbnail = wp_get_attachment_image_src( $post_image_id, 'post-thumbnail', false);
                    
					echo '<img src="'.$thumbnail[0].'" ></img>';
                }
            break;
            case "bemocalendar_col_ev_desc";
                the_excerpt();
            break;

        }
}

// 4. Show Meta-Box

add_action( 'admin_init', 'bemocalendar_events_create' );

function bemocalendar_events_create() {
    add_meta_box('bemocalendar_events_meta', 'Events', 'bemocalendar_events_meta', 'bemocalendar_events');
}

function bemocalendar_events_meta () {

    // - grab data -

    global $post;
	
//	if( is_wp_error($bemocalendar_errors) )
//		echo $bemocalendar_errors->get_error_message();
	
    $custom = get_post_custom($post->ID);
	
	//echo $error->get_error_message();

    $meta_sd = $custom["bemocalendar_events_startdate"][0];
    $meta_ed = $custom["bemocalendar_events_enddate"][0];
	$meta_ad = (int)$custom["bemocalendar_events_allday"][0];
	
    $meta_st = $meta_sd;
    $meta_et = $meta_ed;

    // - grab wp time format -

    $date_format = get_option('date_format'); // Not required in my code
    $time_format = get_option('time_format');

    // - populate today if empty, 00:00 for time -

    if ($meta_sd == null) { $meta_sd = time(); $meta_ed = $meta_sd; $meta_st = 0; $meta_et = 0;}

    // - convert to pretty formats -

    $clean_sd = date("D, M d, Y", $meta_sd);
    $clean_ed = date("D, M d, Y", $meta_ed);
    $clean_st = date($time_format, $meta_st);
    $clean_et = date($time_format, $meta_et);

    // - security -

    echo '<input type="hidden" name="bemocalendar-events-nonce" id="bemocalendar-events-nonce" value="' .
    wp_create_nonce( 'bemocalendar-events-nonce' ) . '" />';

    // - output -
	
	$checked = '';
	
	if($meta_ad == 1)
		$checked = 'checked';
    ?>
    <div class="bemocalendar-meta">
		<ul>
			<li><label>All Day Event?</label><input name="bemocalendar_events_allday" value="1" type="checkbox" <?php echo $checked; ?> /></li>
		</ul>
        <ul>
            <li><label>Start Date</label><input id="bemocalendar_events_startdate" name="bemocalendar_events_startdate" class="bemocalendardate" value="<?php echo $clean_sd; ?>" /></li>
			
			<?php 
			if($meta_ad == 0){
			echo '<li><label>Start Time</label><input name="bemocalendar_events_starttime" value="'. $clean_st.'" /><em>Use 24h format (7pm = 19:00)</em></li>';
			}
			?>
			
            <li><label>End Date</label><input id="bemocalendar_events_enddate" name="bemocalendar_events_enddate" class="bemocalendardate" value="<?php echo $clean_ed; ?>" /></li>

			<?php if($meta_ad == 0){
            echo '<li><label>End Time</label><input name="bemocalendar_events_endtime" value="'.$clean_et .'" /><em>Use 24h format (7pm = 19:00)</em></li>'; 
			}
			?>
        </ul>
    </div>
    <?php
}

// 5. Save Data

add_action ('save_post', 'save_bemocalendar_events');


function save_bemocalendar_events(){

    global $post;
	
    if ( !wp_verify_nonce( $_POST['bemocalendar-events-nonce'], 'bemocalendar-events-nonce' )) 
        return $post->ID;

    if ( !current_user_can( 'edit_post', $post->ID ))
        return $post->ID;
		
	if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	
    // - convert back to unix & update post
	if(isset($_POST["bemocalendar_events_allday"]))
		update_post_meta($post->ID, "bemocalendar_events_allday", '1' );
	else
		update_post_meta($post->ID, "bemocalendar_events_allday", '0' );	
		
     $updatestartd = strtotime ( $_POST["bemocalendar_events_startdate"] . $_POST["bemocalendar_events_starttime"] );
     update_post_meta($post->ID, "bemocalendar_events_startdate", $updatestartd );

    if(!isset($_POST["bemocalendar_events_enddate"]))
        return $post;

    $updateendd = strtotime ( $_POST["bemocalendar_events_enddate"] . $_POST["bemocalendar_events_endtime"]);
    update_post_meta($post->ID, "bemocalendar_events_enddate", $updateendd );
}

//Needed to stop reporting that it worked
add_filter('redirect_post_location','my_redirect_location',10,2);
function my_redirect_location($location,$post_id){
    //If post was published...
    if (isset($_POST['publish'])){
        //obtain current post status
        $status = get_post_status( $post_id );

        //The post was 'published', but if it is still a draft, display draft message (10).
        if($status=='draft')
            $location = add_query_arg('message', 10, $location);
    }

    return $location;
}

// 6. Customize Update Messages

add_filter('post_updated_messages', 'events_updated_messages');

function events_updated_messages( $messages ) {

  global $post, $post_ID;

  $messages['bemocalendar_events'] = array(
    0 => '', // Unused. Messages start at index 1.
    1 => sprintf( __('Event updated. <a href="%s">View item</a>'), esc_url( get_permalink($post_ID) ) ),
    2 => __('Custom field updated.'),
    3 => __('Custom field deleted.'),
    4 => __('Event updated.'),
    // translators: %s: date and time of the revision 
    5 => isset($_GET['revision']) ? sprintf( __('Event restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
    6 => sprintf( __('Event published. <a href="%s">View event</a>'), esc_url( get_permalink($post_ID) ) ),
    7 => __('Event saved.'),
    8 => sprintf( __('Event submitted. <a target="_blank" href="%s">Preview event</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    9 => sprintf( __('Event scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview event</a>'),
      // translators: Publish box date format, see http://php.net/date
      date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
    10 => sprintf( __('Event draft updated. <a target="_blank" href="%s">Preview event</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
  );

  return $messages;
}

// 7. JS Datepicker UI

function events_styles() {
    global $post_type;
	
    if( 'bemocalendar_events' != $post_type )
        return;
		
    wp_enqueue_style('jquery-ui-datepicker-css',plugins_url('css/jquery-ui-1.10.4.custom.css',__FILE__));
}

function events_scripts() {
    global $post_type;

	//echo 'post type : '.$post_type;
    if( 'bemocalendar_events' != $post_type )
		return;

	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-validate', plugins_url('js/jquery.validate.min.js',__FILE__), array('jquery'));
	wp_enqueue_script('jquery-validate-additional', plugins_url('js/additional-methods.min.js',__FILE__), array('jquery'));
	wp_enqueue_script('jquery-validate-validation', plugins_url('js/validation.js',__FILE__), array('jquery'));
	wp_enqueue_script('jquery-ui-datepicker');
	
	// Make site url available to JS scripts 
	$site_parameters = array('datepicker_url' => plugins_url( 'images' , __FILE__ )	);
	
	wp_enqueue_script('custom_script', plugins_url('js/pubforce-admin.js',__FILE__), array('jquery'));
	wp_localize_script( 'custom_script', 'SiteParameters', $site_parameters );
}

	add_action( 'admin_print_styles-post.php', 'events_styles', 1000 );
	add_action( 'admin_print_styles-post-new.php', 'events_styles', 1000 );

	add_action( 'admin_print_scripts-post.php', 'events_scripts', 1000 );
	add_action( 'admin_print_scripts-post-new.php', 'events_scripts', 1000 );
?>
