<?php
/**
 * This is a basic template for a tableless output of
 * the calendar's data.
 *
 * @author Christoph Bach <info@christoph-bach.net>
 */
?>

<section class="calendar">
	<div class="row header">
		<div class="item">Date</div>
<?php foreach ($columnsArr as $column): ?>
		<div class="item"><?php echo $column ?></div>
<?php endforeach; ?>
	</div>
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
	<div class="row month<?php echo ($monthIsPast)? ' past' : '';?>">
		<div class="item"><?php echo $month ?></div>
	</div>
<?php endif; ?>
	<div class="row<?php if (($end && $end[0] < time()) || $begin[0] < time()): ?> past<?php endif; ?>">
		<time datetime="<?php echo gmdate("Y-m-d\TH:i:s\Z", $begin[0]);?>">
				<?php echo strftime($this->dateFormat, $begin[0]); ?>
				<?php echo ($end)? ' - '.strftime($this->dateFormat, $end[0]) : ''; ?>
		</time>
<?php foreach ($columnsArr as $column): $info = $event->getInfo(); ?>
		<div class="item"><?php echo (array_key_exists($column, $info))? $info[$column] : ''; ?></div>
<?php endforeach; ?>
	</div>
<?php endforeach; ?>
</section>
