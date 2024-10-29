<?php
header('Content-Type:application/json');
 
// - grab wp load, wherever it's hiding -
include '../../../wp-load.php';
 
 
if(isset($_REQUEST['today'])) 
	$cutoff = strtotime('today 6:00') + ( get_option( 'gmt_offset' ) * 3600 );
else		//1 year ago
	$cutoff = strtotime("-1 year", time());

$querystr = "
    SELECT *
    FROM $wpdb->posts wposts, $wpdb->postmeta metastart, $wpdb->postmeta metaend
    WHERE (wposts.ID = metastart.post_id AND wposts.ID = metaend.post_id)
    AND (metaend.meta_key = 'bemocalendar_events_enddate'  )
    AND ( metastart.meta_key = 'bemocalendar_events_startdate' AND metastart.meta_value > '$cutoff ')
    AND wposts.post_type = 'bemocalendar_events'
    AND wposts.post_status = 'publish'
    ORDER BY metastart.meta_value ASC LIMIT 500
 ";

// - query -
global $wpdb;

$events = $wpdb->get_results($querystr, OBJECT);
$jsonevents = array();
 
// - loop -
if ($events):
global $post;

foreach ($events as $post):
setup_postdata($post);
 
// - custom post type variables -
$custom = get_post_custom(get_the_ID());
$sd = $custom["bemocalendar_events_startdate"][0];
$ed = $custom["bemocalendar_events_enddate"][0];
$allday = (int)$custom["bemocalendar_events_allday"][0];
$backgroundColor = '';
$fontColor = '';


//Get at the custom fields
$terms = get_the_terms( get_the_ID() ,'bemocalendar_eventcategory');

	if($terms)
	{
		for($i=0;$i<count($terms);$i++)
		{
			$taxonomy_id = "bemocalendar-eventcategory_meta_".$terms[$i]->term_id;
			$taxonomy = get_option( $taxonomy_id );
			
			if(is_array($taxonomy))
			{
				$backgroundColor = $taxonomy["ba_color_field_id"];
				$fontColor = $taxonomy["ba_font_color_field_id"];
			}
		}
	}

 
// - set to ISO 8601 date format -
$stime = date('c', $sd);
$etime = date('c', $ed);

	// - json items -
	$jsonevents[]= array(
	'title' => $post->post_title,
	'allDay' => $allday, // <- true by default with FullCalendar
	'start' => $stime,
	'end' => $etime,
	'backgroundColor' => $backgroundColor,
	'fontColor' => $fontColor,
	'url' => get_permalink($post->ID)
	);

endforeach;
else :
endif;
 
// - fire away -
echo json_encode($jsonevents);
?>