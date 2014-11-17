BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//<?php echo $site->url(); ?>//Kirby Calendar Plugin//<?php echo str::upper($site->language()->code()); ?> 
METHOD:PUBLISH
<?php foreach ($calendar->getAllEvents() as $event): ?>
BEGIN:VEVENT
DTSTART:<?php echo gmdate('Ymd\THis\Z', $event->getBeginTimestamp()); ?> 
DTEND:<?php echo gmdate('Ymd\THis\Z', $event->getEndTimestamp()); ?> 
SUMMARY:<?php echo $event->getField('summary') ?> 
DESCRIPTION:<?php echo $event->getField('description') ?> 
LOCATION:<?php echo $event->getField('location') ?> 
END:VEVENT
<?php endforeach; ?>
END:VCALENDAR