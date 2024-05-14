<ul class="teaser">
<?php
	if (!isset($languageCode)) {
		$languageCode = 'en';
	}
	foreach ($calendar->getEvents() as $event):
		if (--$items < 0) break;
?>
	<li><strong><?php echo $event->getBeginHtml($languageCode); ?></strong><?php
		foreach ($fields as $key => $value) {
			echo ' '.$event->getField($key);
		}
	?></li>
<?php endforeach; ?>
</ul>
