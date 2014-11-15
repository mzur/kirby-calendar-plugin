<?php if(!defined('KIRBY')) exit ?>

title: Home
pages: false
fields:
	title:
		label: Title
		type:  text
	calendar:
		label: Calendar
		type: structure
		entry: >
			<strong>{{title}}</strong><br>
			{{description}}<br>
			Beginning: {{_begin_date}} {{_begin_time}}<br>
			End: {{_end_date}} {{_end_time}}
		fields:
			title:
				label: Title
				type: text
			description:
				label: Description
				type: textarea
				size: small
			_begin_date:
				label: Beginning date
				type: date
				format: YYYY-MM-DD
			_begin_time:
				label: Beginning time
				type: time
				interval: 15
			_end_date:
				label: Ending date
				type: date
				format: YYYY-MM-DD
			_end_time:
				label: Ending time
				type: time
				interval: 15
