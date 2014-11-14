<?php
if(!class_exists('Calendar'))  require_once('lib/Calendar.php');
if(!class_exists('Event')) require_once('lib/Event.php');

function calendar($events, $options=array(), $template='table') {
		$calendar = new Calendar($events, $options);
		echo (empty($events)) ? $calendar->noEntry() : $calendar->cal($template);
}
?>
