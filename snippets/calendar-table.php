<?php
	$tmp_date = getdate(0);
	$current_date = getdate();
?>

<?php
	if (empty($calendar->get_all_events())): 
		echo l::get('calendar-no-entry');
	else:
?>

<table class="calendar">
	<thead>
		<tr>
			<th><?php echo l::get('date'); ?></th>
<?php foreach ($fields as $field): ?>
			<th><?php echo $field; ?></th>
<?php endforeach; ?>
		</tr>
	</thead>
	<tbody>
<?php foreach ($calendar->get_all_events() as $event):
		$date = $event->get_begin_date();
?>
<?php 	if ($tmp_date['mon'] < $date['mon'] || $tmp_date['year'] < $date['year']): ?>
		<tr class="month<?php e($date['mon'] < $current_date['mon'] or $date['year'] < $current_date['year'], ' past'); ?>">
			<td colspan="<?php echo count($fields)+1; ?>">
				<?php echo strftime(l::get('calendar-month-format'), $date[0]); ?>
			</td>
		</tr>
<?php 	endif; ?>
		<tr class="event<?php e($event->is_past(), ' past'); ?>">
			<td>
				<time datetime="<?php echo gmdate('Y-m-d\TH:i:s\Z', $event->get_begin_timestamp()); ?>"><?php echo $event->get_begin_str(); ?></time>
<?php 	if ($event->has_end()): ?>
				<br><?php echo l::get('to') ?><br><time datetime="<?php echo gmdate('Y-m-d\TH:i:s\Z', $event->get_end_timestamp()); ?>"><?php echo $event->get_end_str(); ?></time>
<?php 	endif; ?>
			</td>
<?php 	foreach ($fields as $key => $value): ?>
			<td><?php echo $event->get_field($key); ?></td>
<?php 	endforeach; ?>
		</tr>
<?php $tmp_date = $date; ?>
<?php endforeach; ?>
	</tbody>
</table>

<?php endif; ?>