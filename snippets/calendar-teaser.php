<ul class="teaser">
<?php
	foreach ($calendar->getEvents() as $event):
		if (--$items < 0) break;
?>
	<li><strong><?php echo $event->getBeginHtml(); ?></strong><?php
		foreach ($fields as $key => $value) {
			echo ' '.$event->getField($key);
		}
	?></li>
<?php endforeach; ?>
</ul>