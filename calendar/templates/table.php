<?php
/**
 * This is a basic template for a table output of the calendar's data.
 *
 * @author Christoph Bach <info@christoph-bach.net>
 */
?>

<table class="calendar">
	<tr>
		<th>Date</th>
<?php foreach ($columnsArr as $column): ?>
		<th><?php echo $column ?></th>
<?php endforeach; ?>
	</tr>
<tbody>
<?php foreach ($this->events as $event): ?>
<?php
	$begin = $event->getBegin();
	$end = $event->getEnd();

	$tempMonth = strftime($this->monthFormat, $begin[0]);

	$monthIsPast = ($begin[0] < $currentTime[0]
						&& $begin['month'] != $currentTime['month'])
							? true
							: false;
?>
<?php if ($month != $tempMonth):
	$month = $tempMonth;
?>
	<tr class="month<?php echo ($monthIsPast)? ' past' : '';?>">
		<td colspan="<?php echo count($eventsArr)+1; ?>"><?php echo $month ?></td>
	</tr>
<?php endif; ?>
	<tr<?php if (($end && $end[0] < time()) || $begin[0] < time()): ?> class="past" <?php endif; ?>>
		<td>
			<time datetime="<?php echo gmdate("Y-m-d\TH:i:s\Z", $begin[0]);?>">
				<?php echo strftime($this->dateFormat, $begin[0]); ?>
				<?php echo ($end)? ' - '.strftime($this->dateFormat, $end[0]) : ''; ?>
			</time>
		</td>
<?php foreach ($columnsArr as $column): $info = $event->getInfo(); ?>
		<td><?php echo (array_key_exists($column, $info))? $info[$column] : ''; ?></td>
<?php endforeach; ?>
	</tr>
<?php endforeach; ?>
</tbody>
</table>
