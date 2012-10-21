<?php
/**
 * This template will produce an iCal output of the calendar
 * data. Make sure that there's no additional output before
 * and after this template.
 *
 * @author Christoph Bach <info@christoph-bach.net>
 */

header('Content-type: text/calendar');
?>
BEGIN:VCALENDAR
VERSION:2.0
PRODID:<?php echo $site->url()."\n" ?>
METHOD:PUBLISH
<?php
	foreach ($eventsArr as $event) {
		echo $event->getICal();
	}
?>
END:VCALENDAR
