jQuery(document).ready(function(){
	var big = jQuery('.time-panel .big');
	var cost_big = 0;
	// =========================================================
	// TIME PANEL
	// =========================================================
	activateTimer();	
	// =========================================================
	// SWITHC PANELS CLICK
	// =========================================================
	jQuery('#switch-panels').click(function(e){
		var money = jQuery('#time-panel-money');
		var time  = jQuery('#time-panel-time');
		if(money.hasClass('hide'))
		{
			money.removeClass('hide');
			time.addClass('hide');
			jQuery('#switch-panels i').removeClass('fa-dollar');
			jQuery('#switch-panels i').addClass('fa-clock-o');
		}
		else
		{
			money.addClass('hide');
			time.removeClass('hide');
			jQuery('#switch-panels i').removeClass('fa-clock-o');
			jQuery('#switch-panels i').addClass('fa-dollar');			
		}
	});	
	// =========================================================
	// BOXER
	// =========================================================
	jQuery('.boxer').boxer({
		fixed: true,
		retina: true,				
		labels : { close : "×"}
	});	
	jQuery(window).on('open.boxer', function(){
		// =========================================================
		// FOCUS DEFAULT MODAL DIALOG CONTROL
		// =========================================================
		switch(jQuery.boxer('getData').$target.attr('href'))
		{
			case '#modal-join':
				jQuery.boxer('getData').$content.find('input[name="cost"]').focus();
				break;			
			default:
				jQuery.boxer('getData').$content.find('button[type="submit"]').focus();
				break;
		}
	});
});

/**
 * Remove project action
 * @param  object event --- event
 */
function removeProject(event)
{
	var url = jQuery.boxer('getData').$target.data('href');
	document.location.href = url;
	event.preventDefault();
}

/**
 * Remove worker from the project
 * @param  object event --- event
 */
function removeWorker(event)
{
	var url = jQuery.boxer('getData').$target.data('href');
	document.location.href = url;
	event.preventDefault();
}

/**
 * Allow user to work on project
 * @param  object event --- event object
 */
function allowJoin(event)
{
	var url = jQuery.boxer('getData').$target.data('href');
	document.location.href = url;
	event.preventDefault();
}

/**
 * Join to project
 * @param  object event --- event object
 */
function join(event)
{		
	var val = parseInt(jQuery.boxer('getData').$content.find('input[name="cost"]').val());
	var url = jQuery.boxer('getData').$target.data('href');
	val     = isNaN(val) ? 0 : val;	
	document.location.href = url + '&cost=' + val;
	
	event.preventDefault();
}

/**
 * Close Modal dialog [Boxer]
 * @param  object event --- event object
 */
function closeBoxer(event)
{
	jQuery.boxer("close");	
	event.preventDefault();
}

/**
 * Format integer to two digit
 * From 1 to 01
 * @param  integer val --- integer to format
 * @return string      --- formatted string
 */
function formatTwoDigits(val)
{
	return val < 10 ? "0" + val : val;
}

/**
 * Activate session timer
 * @return boolean
 */
function activateTimer()
{
	if(!window.session_active) return false;
	
	var now      = 0;
	var start    = 0; 
	var diff     = 0;
	var days     = 0;
	var cost     = 0; 
	var cost_big = 0;
	var small    = jQuery('.time-panel .small');
	var big      = jQuery('.time-panel .big');

	setInterval(function(){
		now       = new moment().utc();
		start     = new moment.utc(timer_start); 
		diff      = moment.utc(now.diff(start));
		days      = moment.utc(now.diff(start, 'days'));
		start_big = new moment.utc(timer_start_big); 
		diff_big  = moment.utc(now.diff(start_big));
		days_big  = moment.utc(now.diff(start_big, 'days'));
		cost      = parseFloat(diff.unix()/3600*user_cost).toFixed(2);		
		cost_big  = parseFloat(parseFloat(money_offset) + parseFloat(cost)).toFixed(2);


		small.find('.money .digits').text(cost);
		small.find('.hours .time').text(formatTwoDigits(diff.hours()));
		small.find('.minutes .time').text(formatTwoDigits(diff.minutes()));
		small.find('.seconds .time').text(formatTwoDigits(diff.seconds()));

		big.find('.money .digits').text(cost_big);
		big.find('.days .time').text(formatTwoDigits(days_big));
		big.find('.hours .time').text(formatTwoDigits(diff_big.hours()));
		big.find('.minutes .time').text(formatTwoDigits(diff_big.minutes()));
	}, 500);
}