<?php
function calendar($events, $options=array(), $template='table') {
		$calendar = new Calendar($events, $options);
		echo (empty($events)) ? $calendar->noEntry() : $calendar->cal($template);
}
?>
