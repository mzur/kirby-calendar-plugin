<?php

if (!class_exists('Calendar'))  require_once('lib/Calendar.php');
if (!class_exists('Event')) require_once('lib/Event.php');

function calendar($events = array()) {
	try {
		return new Calendar($events);
	} catch (Exception $e) {
		print "<strong>The calendar plugin threw an error</strong><br>" .
			$e->getMessage();
	}
}