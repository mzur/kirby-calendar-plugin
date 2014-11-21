<?php
	$tmpDate = getdate(0);
	$currentDate = getdate();
?>

<?php
	if (empty($calendar->getAllEvents())): 
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
<?php foreach ($calendar->getAllEvents() as $event):
		$date = $event->getBeginDate();
?>
<?php 	if ($tmpDate['mon'] < $date['mon'] || $tmpDate['year'] < $date['year']): ?>
		<tr class="month<?php e($date['mon'] < $currentDate['mon'] or $date['year'] < $currentDate['year'], ' past'); ?>">
			<td colspan="<?php echo count($fields)+1; ?>"><?php echo strftime(l::get('calendar-month-format'), $date[0]); ?></td>
		</tr>
<?php 	endif; ?>
		<tr class="event<?php e($event->isPast(), ' past'); ?>">
			<td><?php
				echo $event->getBeginHtml();
				if ($event->hasEnd()) {
					echo ' '.l::get('to').' '.$event->getEndHtml();
				}
			?></td>
<?php 	foreach ($fields as $key => $value): ?>
			<td><?php echo $event->getField($key); ?></td>
<?php 	endforeach; ?>
		</tr>
<?php $tmpDate = $date; ?>
<?php endforeach; ?>
	</tbody>
</table>

<?php endif; ?>