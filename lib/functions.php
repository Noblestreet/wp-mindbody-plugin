<?php
/**
 * Summary.
 *
 * Gets a YYYY-mm-dd date and returns an array of four dates:
 *      start of requested week
 *      end of requested week 
 *      following week start date
 *      previous week start date.
 *
 * @since 1.0
 * @source (initially adapted) 
 * http://stackoverflow.com/questions/186431/calculating-days-of-week-given-a-week-number
 * Used by mz_show_schedule(), mz_show_events(), mz_mindbody_debug_text()
 * also used by mZ_mbo_pages_pages() in Mz MBO Pages plugin
 *
 * @param var $date Start date for date range.
 * @param var $duration Optional. Description. Default.
 * @return array Start Date, End Date and Previous Range Start Date.
 */
function mz_getDateRange($date, $duration=7) {
    /*Gets a YYYY-mm-dd date and returns an array of four dates:
        start of requested week
        end of requested week 
        following week start date
        previous week start date
    adapted from http://stackoverflow.com/questions/186431/calculating-days-of-week-given-a-week-number
    */
    $return = array();

    list($year, $month, $day) = explode("-", $date);

    // Get the weekday of the given date
    $wkday = date_i18n('l',mktime('0','0','0', $month, $day, $year));

    switch($wkday) {
        case __('Monday', 'mz-mindbody-api'): $numDaysSinceMon = 0; break;
        case __('Tuesday', 'mz-mindbody-api'): $numDaysSinceMon = 1; break;
        case __('Wednesday', 'mz-mindbody-api'): $numDaysSinceMon = 2; break;
        case __('Thursday', 'mz-mindbody-api'): $numDaysSinceMon = 3; break;
        case __('Friday', 'mz-mindbody-api'): $numDaysSinceMon = 4; break;
        case __('Saturday', 'mz-mindbody-api'): $numDaysSinceMon = 5; break;
        case __('Sunday', 'mz-mindbody-api'): $numDaysSinceMon = 6; break;   
    }

    // Timestamp of the monday for that week
    $seconds_in_a_day = 86400;
    
    $monday = mktime('0','0','0', $month, $day-$numDaysSinceMon, $year);
    $today = mktime('0','0','0', $month, $day, $year);

    if ($duration != 7){
        $rangeEnd = $today+($seconds_in_a_day*$duration);
    }else{
        $rangeEnd = $today+($seconds_in_a_day*($duration - $numDaysSinceMon));
    }
    $previousRangeStart = $monday+($seconds_in_a_day*($numDaysSinceMon - ($numDaysSinceMon+$duration)));
    $return[0] = array('StartDateTime'=>date('Y-m-d',$today), 'EndDateTime'=>date('Y-m-d',$rangeEnd-1));
    
    //Subesquent range start
    $return[1] = date('Y-m-d',$rangeEnd+1); 
    $return[2] = date('Y-m-d',$previousRangeStart);
    return $return;
}

class Global_Strings {
 // property declaration

	// method declaration
	public function translate_them() {
		return array( 'username' => __( 'Username', 'mz-mindbody-api' ),
					  'password' => __( 'Password', 'mz-mindbody-api' ),
					  'login' => __( 'Login', 'mz-mindbody-api' ),
					  'logout' => __( 'Log Out', 'mz-mindbody-api' ),
					  'sign_up' => __( 'Sign Up', 'mz-mindbody-api' ),
					  'login_to_sign_up' => __( 'Log in to Sign up', 'mz-mindbody-api' ),
					  'manage_on_mbo' => __('Manage on MindBody Site', 'mz-mindbody-api' ),
					  'login_url' => __( 'login', 'mz-mindbody-api' ),
					  'logout_url' => __( 'logout', 'mz-mindbody-api' ),
					  'signup_url' => __( 'signup', 'mz-mindbody-api' ),
					  'create_account_url' => __('create-account', 'mz-mindbody-api' ),
					  'or' => __( 'or', 'mz-mindbody-api' ),
					  'with' => __( 'with', 'mz-mindbody-api' ),
					  'day' => __( 'day', 'mz-mindbody-api' ),
					  'shortcode' => __( 'shortcode', 'mz-mindbody-api' ),
						);
	}
}
	
function mz_mbo_schedule_nav($date, $period, $duration=7)
{
	$sched_nav = '<div class="mz_schedule_nav_holder">';
	if (!isset($period)){
		$period = __('Week',' mz-mindbody-api');
		}
	$mz_schedule_page = get_permalink();
	//Navigate through the weeks
	$mz_start_end_date = mz_getDateRange($date, $duration);
	$mz_nav_weeks_text_prev = sprintf(__('Previous %1$s','mz-mindbody-api'), $period);
	$mz_nav_weeks_text_current = sprintf(__('Current %1$s','mz-mindbody-api'), $period);
	$mz_nav_weeks_text_following = sprintf(__('Following %1$s','mz-mindbody-api'), $period);
	$sched_nav .= ' <a href='.add_query_arg(array('mz_date' => ($mz_start_end_date[2]))).'>'.$mz_nav_weeks_text_prev.'</a> - ';
	if (isset($_GET['mz_date']))
	    $sched_nav .= ' <a href='.add_query_arg(array('mz_date' => (date_i18n('Y-m-d',current_time('timestamp'))))).'>'.$mz_nav_weeks_text_current.'</a>  - ';
	$sched_nav .= '<a href='.add_query_arg(array('mz_date' => ($mz_start_end_date[1]))).'>'.$mz_nav_weeks_text_following.'</a>';
	$sched_nav .= '</div>';

	return $sched_nav;
}


function mz_validate_date( $string ) {
	if (preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/',$string))
	{
		return $string;
	}
	else
	{
		return "mz_validate_weeknum error";
	}
}

/* create an html element, like in js 
 * Source: https://davidwalsh.name/create-html-elements-php-htmlelement-class
 */
class html_element
{
	/* vars */
	var $type;
	var $attributes;
	var $self_closers;
	
	/* constructor */
	function html_element($type,$self_closers = array('input','img','hr','br','meta','link'))
	{
		$this->type = strtolower($type);
		$this->self_closers = $self_closers;
	}
	
	/* get */
	function get($attribute)
	{
		return $this->attributes[$attribute];
	}
	
	/* set -- array or key,value */
	function set($attribute,$value = '')
	{
		if(!is_array($attribute))
		{
			$this->attributes[$attribute] = $value;
		}
		else
		{
			$this->attributes = array_merge($this->attributes,$attribute);
		}
	}
	
	/* remove an attribute */
	function remove($att)
	{
		if(isset($this->attributes[$att]))
		{
			unset($this->attributes[$att]);
		}
	}
	
	/* clear */
	function clear()
	{
		$this->attributes = array();
	}
	
	/* inject */
	function inject($object)
	{
		if(@get_class($object) == __class__)
		{
			$this->attributes['text'].= $object->build();
		}
	}
	
	/* build */
	function build()
	{
		//start
		$build = '<'.$this->type;
		
		//add attributes
		if(count($this->attributes))
		{
			foreach($this->attributes as $key=>$value)
			{
				if($key != 'text') { $build.= ' '.$key.'="'.$value.'"'; }
			}
		}
		
		//closing
		if(!in_array($this->type,$this->self_closers))
		{
			$build.= '>'.$this->attributes['text'].'</'.$this->type.'>';
		}
		else
		{
			$build.= ' />';
		}
		
		//return it
		return $build;
	}
	
	/* spit it out */
	function output()
	{
		echo $this->build();
	}
}
// EOF create an html element


// Following two functions used for usort in older php versions.

function mz_sort_horizontal_times($a, $b) {
				if(strtotime($a->startDateTime) == strtotime($b->startDateTime)) {
 					return 0;
 				}
 				return $a->startDateTime < $b->startDateTime ? -1 : 1;
			}
			
function mz_sort_grid_times ($a, $b) {
			if(date_i18n("N", strtotime($a->startDateTime)) == date_i18n("N", strtotime($b->startDateTime))) {
				return 0;
			}
			return $a->startDateTime < $b->startDateTime ? -1 : 1;
		}
			
function sortClassesByDate($mz_classes = array(), $time_format = "g:i a", 
																	$locations=1, $hide_cancelled=0, $hide=array(), 
																	$advanced=0, $show_registrants=0, $registrants_count=0, 
																	$calendar_format='horizontal', $class_type='Enrollment') {
	$mz_classesByDate = array();
	
	if(!is_array($locations)):
		$locations = array($locations);
	endif;
	
	foreach($mz_classes as $class)
	{
		
		if ($hide_cancelled == 1):
			if ($class['IsCanceled'] == 1):
				continue;
			endif;
		endif;
		
		/* Create a new array with a key for each date YYYY-MM-DD
		and corresponsing value an array of class details */ 
		$classDate = date("Y-m-d", strtotime($class['StartDateTime']));

		$single_event = new Single_event($class, $daynum="", $hide=array(), $locations, $hide_cancelled=0, 
																			$advanced, $show_registrants, $registrants_count, $calendar_format,
																			$calendar_format);
									
		if(!empty($mz_classesByDate[$classDate])) {
			if (
				(!in_array($class['Location']['ID'], $locations)) || 
				($class['ClassDescription']['Program']['ScheduleType'] == $class_type)
				) {
					continue;
				}
			//$mz_classesByDate[$classDate] = array_merge($mz_classesByDate[$classDate], array($class));
			array_push($mz_classesByDate[$classDate]['classes'], $single_event);
		} else {
			if (
				(!in_array($class['Location']['ID'], $locations)) || 
				($class['ClassDescription']['Program']['ScheduleType'] == $class_type)
				) {
					continue;
				}
			//$mz_classesByDate[$classDate]['classes'] = $single_event;
			$mz_classesByDate[$classDate] = array('classes' => array($single_event));
		}
	}
	/* They are not ordered by date so order them by date */
	ksort($mz_classesByDate);
	foreach($mz_classesByDate as $classDate => &$mz_classes)
	{	
		/*
		$mz_classes is an array of all classes for given date
		Take each of the class arrays and order it by time
		*/
		if (phpversion() >= 5.3) {
			usort($mz_classes['classes'], function($a, $b) {
				if(strtotime($a->startDateTime) == strtotime($b->startDateTime)) {
					return 0;
					}
				return $a->startDateTime < $b->startDateTime ? -1 : 1;
				}); 
			} else {
			usort($mz_classes['classes'], 'mz_sort_horizontal_times');
		}
	}
		
	return $mz_classesByDate;
}

function sortClassesByTimeThenDay($mz_classes = array(), $time_format = "g:i a", 
																	$locations=1, $hide_cancelled=0, $hide, 
																	$advanced, $show_registrants, $registrants_count, 
																	$calendar_format) {
																		
	$mz_classesByTime = array();

	if(!is_array($locations)):
		$locations = array($locations);
	endif;
										
	foreach($mz_classes as $class)
	{
		if ($hide_cancelled == 1):
			if ($class['IsCanceled'] == 1):
				continue;
			endif;
		endif;
		
		/* Create a new array with a key for each time
		and corresponsing value an array of class details 
		for classes at that time. */ 
		$classTime = date_i18n("G.i", strtotime($class['StartDateTime'])); // for numerical sorting
		// $class['day_num'] = '';
		$class['day_num'] = date_i18n("N", strtotime($class['StartDateTime'])); // Weekday num 1-7

		$single_event = new Single_event($class, $class['day_num'], $hide, $locations, $hide_cancelled, 
																			$advanced, $show_registrants, $registrants_count, $calendar_format);
																			
		if(!empty($mz_classesByTime[$classTime])) {
			if (
				(!in_array($class['Location']['ID'], $locations)) || 
				($class['ClassDescription']['Program']['ScheduleType'] == 'Enrollment')
				) {
					continue;
				}
			array_push($mz_classesByTime[$classTime]['classes'], $single_event);
		} else {
			// Assign the first element ( of this time slot ?).
			if (
				(!in_array($class['Location']['ID'], $locations)) || 
				($class['ClassDescription']['Program']['ScheduleType'] == 'Enrollment')
				) {
					continue;
				}
			$display_time = (date_i18n($time_format, strtotime($class['StartDateTime']))); 
			$mz_classesByTime[$classTime] = array('display_time' => $display_time, 
													'classes' => array($single_event));
			
		}
	}

	/* Timeslot keys in new array are not time-sequenced so do so*/
	ksort($mz_classesByTime);
	foreach($mz_classesByTime as $scheduleTime => &$mz_classes)
	{
		/*
		$mz_classes is an array of all class_event objects for given time
		Take each of the class arrays and order it by days 1-7
		*/
		
		if (phpversion() >= 5.3) {
			usort($mz_classes['classes'], function($a, $b) {
				if(date_i18n("N", strtotime($a->startDateTime)) == date_i18n("N", strtotime($b->startDateTime))) {
					return 0;
					}
					return $a->startDateTime < $b->startDateTime ? -1 : 1;
				}); 
			} else {
				usort($mz_classes['classes'], 'mz_sort_grid_times'); 
			}
		$mz_classes['classes'] = week_of_timeslot($mz_classes['classes'], 'day_num');
	}
	return $mz_classesByTime;
}

function week_of_timeslot($array, $indicator){
	/*
	Make a clean array with seven corresponding slots and populate 
	based on indicator (day) for each class. There may be more than
	one even for each day and empty arrays will represent empty time slots.
	*/
	$seven_days = array_combine(range(1, 7), array(array(), array(), array(),
											array(), array(), array(), array()));
		foreach($seven_days as $key => $value){
			foreach ($array as $class) {
					if ($class->$indicator == $key){
						array_push($seven_days[$key], $class);
					}
				}
			}
	return $seven_days;
	}

?>