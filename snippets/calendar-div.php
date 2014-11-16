<?php
	$tmp_date = getdate(0);
	$current_date = getdate();
?>

<?php
	if (empty($calendar->get_all_events())): 
		echo l::get('calendar-no-entry');
	else:
?>

<section class="calendar">
	<div class="row head">
		<div class="item"><?php echo l::get('date'); ?></div>
<?php foreach ($fields as $field): ?>
		<div class="item"><?php echo $field; ?></div>
<?php endforeach; ?>
	</div>
<?php foreach ($calendar->get_all_events() as $event):
		$date = $event->get_begin_date();
?>
<?php 	if ($tmp_date['mon'] < $date['mon'] || $tmp_date['year'] < $date['year']): ?>
	<div class="row month<?php e($date['mon'] < $current_date['mon'] or $date['year'] < $current_date['year'], ' past'); ?>">
		<div class="item"><?php echo strftime(l::get('calendar-month-format'), $date[0]); ?></div>
	</div>
<?php 	endif; ?>
	<div class="row event<?php e($event->is_past(), ' past'); ?>">
		<div class="item date"><?php
				echo $event->get_begin_html();
				if ($event->has_end()) {
					echo ' '.l::get('to').' '.$event->get_end_html();
				}
		?></div>
<?php 	foreach ($fields as $key => $value): ?>
		<div class="item"><?php echo $event->get_field($key); ?></div>
<?php 	endforeach; ?>
	</div>
<?php $tmp_date = $date; ?>
<?php endforeach; ?>
</section>

<?php endif; ?>