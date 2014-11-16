BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//<?php echo $site->url(); ?>//Kirby Calendar Plugin//<?php echo str::upper($site->language()->code()); ?> 
METHOD:PUBLISH
<?php foreach ($calendar->get_all_events() as $event): ?>
BEGIN:VEVENT
DTSTART:<?php echo gmdate('Ymd\THis\Z', $event->get_begin_timestamp()); ?> 
DTEND:<?php echo gmdate('Ymd\THis\Z', $event->get_end_timestamp()); ?> 
SUMMARY:<?php echo $event->get_field('summary') ?> 
DESCRIPTION:<?php echo $event->get_field('description') ?> 
LOCATION:<?php echo $event->get_field('location') ?> 
END:VEVENT
<?php endforeach; ?>
END:VCALENDAR