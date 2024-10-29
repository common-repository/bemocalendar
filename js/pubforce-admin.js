jQuery(document).ready(function($)  
{	

//Start Date
$("#bemocalendar_events_startdate").datepicker({
    dateFormat: 'D, M d, yy',
    showOn: 'button',
    buttonImage: SiteParameters.datepicker_url + '/icon-datepicker.png',
    buttonImageOnly: true,
     onSelect: function() {
    	$(this).change();
  },
    numberOfMonths: 3,
	showButtonPanel: true
    });
    
$('#bemocalendar_events_startdate').change(function() { 
	$("#bemocalendar_events_enddate").val($(this).val());    
});
    
    
//End Date    
$("#bemocalendar_events_enddate").datepicker({
    dateFormat: 'D, M d, yy',
    showOn: 'button',
    buttonImage: SiteParameters.datepicker_url + '/icon-datepicker.png',
    buttonImageOnly: true,
    numberOfMonths: 3
    });    
});