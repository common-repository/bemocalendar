jQuery.noConflict(); 
jQuery(document).ready(function($) {
    
	// page is now ready, initialize the calendar...
    $(bemocalendar.cssid).fullCalendar({
        events: bemocalendar.events,
		header: {
		  left: 'prev,next today',
		  center: 'title',
		  right: 'month,agendaWeek,agendaDay'
		}
    })
	
	//Grab the json categories
	$.getJSON(bemocalendar.eventcategories,function(data){

		var color_widget_id = bemocalendar.cssid + '_color';
		for (var prop in data) {
			if (data.hasOwnProperty(prop)) { 
				$(color_widget_id).append('<div class="fc-legend" style="background-color:' + data[prop].bgcolor + '"></div><div class="fc-legend-label">' + data[prop].name + '</div>');
			}
		}
    });	
	
	
});