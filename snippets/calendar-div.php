<?php
	$tmpDate = getdate(0);
	$currentDate = getdate();
?>
<section class="calendar">

<?php
	if (!$calendar->getAllEvents()):
		echo l::get('calendar-no-entry');
	else:
?>
	<div class="row head">
		<div class="item"><?php echo l::get('date'); ?></div>
<?php foreach ($fields as $field): ?>
		<div class="item"><?php echo $field; ?></div>
<?php endforeach; ?>
	</div>
<?php foreach ($calendar->getAllEvents() as $event):
		$date = $event->getBeginDate();
?>
<?php 	if ($tmpDate['mon'] < $date['mon'] || $tmpDate['year'] < $date['year']): ?>
	<div class="row month<?php e($date['mon'] < $currentDate['mon'] or $date['year'] < $currentDate['year'], ' past'); ?>">
		<div class="item"><?php echo strftime(l::get('calendar-month-format'), $date[0]); ?></div>
	</div>
<?php 	endif; ?>
	<div class="row event<?php e($event->isPast(), ' past'); ?>">
		<div class="item date"><?php
				echo $event->getBeginHtml();
				if ($event->hasEnd()) {
					echo ' '.l::get('to').' '.$event->getEndHtml();
				}
		?></div>
<?php 	foreach ($fields as $key => $value): ?>
		<div class="item"><?php echo $event->getField($key); ?></div>
<?php 	endforeach; ?>
	</div>
<?php $tmpDate = $date; ?>
<?php endforeach; ?>

<?php endif; ?>
</section>