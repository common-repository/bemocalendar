<?php
//Validator for saving the events thing.
class BEMOEventsValidator 
{
	var $errors = false;
	var $start_unix = 0;	
	var $end_unix = 0;

	function isValid() 
	{    
		if(is_array($this->errors))
			return false;	
			
		return true;
	}
	
	function areDatesInWrongOrder($start,$end){
	
		$this->update_option(1);
		/*
		$this->start_unix = strtotime($start);
		$this->end_unix = strtotime($end);
		
		if($this->start_unix < $this->end_unix)
		{
			$this->errors[] = "Start Date (".$this->start_unix.") must be before End Date (".$this->end_unix.")";
			
		}*/
	}

	//You can change the error message here. This for your your admin_notices hook
	function start_date_bigger_than_end_date()
	{
		echo '<div class="error">';
		echo '<p>The start date cannot be greater than the end date.</p>';
		echo '</div>';
	}

	//update option when admin_notices is needed or not
	function update_option($val)
	{
		update_option('display_my_admin_message',$val);
	}

	//function to use for your admin notice
	function add_plugin_notice() 
	{
		if (get_option('display_my_admin_message') == 1) 
		{ // check whether to display the message
			add_action('admin_notices' , array(&$this,'start_date_bigger_than_end_date') );
			update_option('display_my_admin_message', 0); // turn off the message
		}
	}
}