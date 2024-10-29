<?php
header('Content-Type:application/json');
 
// - grab wp load, wherever it's hiding -
include '../../../wp-load.php';
 

//list terms in a given taxonomy using wp_list_categories (also useful as a widget if using a PHP Code plugin)
$taxonomy     = 'bemocalendar_eventcategory';
$orderby      = 'name'; 
$show_count   = 0;      // 1 for yes, 0 for no
$pad_counts   = 0;      // 1 for yes, 0 for no
$hierarchical = 1;      // 1 for yes, 0 for no
$title        = '';

$args = array(
  'taxonomy'     => $taxonomy,
  'orderby'      => $orderby,
  'show_count'   => $show_count,
  'pad_counts'   => $pad_counts,
  'hierarchical' => $hierarchical,
  'title_li'     => $title
);

	$colour_mapping = array();

	$categories = get_categories( $args );

	foreach($categories as $key => $val)
	{
		$id = $val->term_id;
		$name = $val->name;
		
		$taxonomy_id = 'bemocalendar-eventcategory_meta_'.$id;
		
		$taxonomy = get_option( $taxonomy_id );

		if($taxonomy)
		{
			for($j=0;$j<count($taxonomy);$j++)
			{
				$colour_mapping[$id]['name'] = $name;
				$colour_mapping[$id]['bgcolor'] = $taxonomy['ba_color_field_id'];
			}
		}
	}

// - fire away -
echo json_encode($colour_mapping);
?>