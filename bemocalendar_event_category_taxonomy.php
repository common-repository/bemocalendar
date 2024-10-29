<?php

//include the main class file
require_once("class.TaxMetaClass.php");
if (is_admin()){
  /* 
   * prefix of meta keys, optional
   */
  $prefix = 'ba_';
  /* 
   * configure your meta box
   */
  $config = array(
    'id' => 'demo_meta_box',          // meta box id, unique per meta box
    'title' => 'Demo Meta Box',          // meta box title
    'pages' => array('bemocalendar_eventcategory'),        // taxonomy name, accept categories, post_tag and custom taxonomies
    'context' => 'normal',            // where the meta box appear: normal (default), advanced, side; optional
    'fields' => array(),            // list of meta fields (can be added by field arrays)
    'local_images' => false,          // Use local or hosted images (meta box images for add/remove)
    'use_with_theme' => false          //change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
  );
  
  
  /*
   * Initiate your meta box
   */
  $my_meta =  new Tax_Meta_Class($config,'bemocalendar-eventcategory');
  
  $my_meta->setSelfPath($SelfPath);
  
  /*
   * Add fields to your meta box
   */
  $my_meta->addColor($prefix.'color_field_id',array('name'=> __('Event Color ','bemocalendar-eventcategory-meta')));
  $my_meta->addColor($prefix.'font_color_field_id',array('name'=> __('Event Font Color ','bemocalendar-eventcategory-meta')));

  //Finish Meta Box Decleration
  $my_meta->Finish();
}