<ul class="teaser">
<?php
	foreach ($calendar->get_events() as $event):
		if (--$items < 0) break;
?>
	<li>
		<strong><?php echo $event->get_begin_html(); ?></strong><?php
		foreach ($fields as $key => $value) {
			echo ' '.$event->get_field($key);
		}
?>
	</li>
<?php endforeach; ?>
</ul>