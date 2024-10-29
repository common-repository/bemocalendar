jQuery.noConflict();

jQuery(document).ready(function() {

	jQuery(bemocalendar_small.cssid).eventCalendar({
	  eventsjson: bemocalendar_small.events,
	  openEventInNewWindow: true,
      showDescription: true // also it can be false	  
	});
});	