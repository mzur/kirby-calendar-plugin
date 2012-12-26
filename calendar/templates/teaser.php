<?php
/**
 * This is a template for a list output of the first 3 future events.
 */
?>

<ul class="teaser">
<?php 
//number of events to show
$max = 3;
$count = 0;

foreach ($this->events as $event):

	if ($count >= $max) break;
	$begin = $event->getBegin();
	$end = $event->getEnd();
	
	//skip all past events
	if (($end && $end[0] >= time()) || $begin[0] >= time()):
		$count++;
?>
	<li<?php echo ($count>=$max) ? ' class="last"' : '';?>>
		<time datetime="<?php echo gmdate("Y-m-d\TH:i:s\Z", $begin[0]);?>"><?php echo strftime($this->dateFormat, $begin[0]); ?><?php echo ($end)? ' - '.strftime($this->dateFormat, $end[0]) : ''; ?></time>
<?php foreach ($columnsArr as $column): $info = $event->getInfo(); ?>
		<?php echo (array_key_exists($column, $info))? $info[$column] : ''; ?>
<?php endforeach; ?>
	
	</li>
<?php endif; ?>
<?php endforeach; ?>
</ul>
