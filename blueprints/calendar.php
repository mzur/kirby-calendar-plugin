<?php if(!defined('KIRBY')) exit ?>

title: Calendar
pages: false
fields:
	title:
		label: Title
		type:  text
	calendar:
		label: Calendar
		type: structure
		entry: >
			<strong>{{summary}}</strong><br>
			{{description}}<br>
			Beginning: {{_beginDate}} {{_beginTime}}<br>
			End: {{_endDate}} {{_endTime}}
		fields:
			summary:
				label: Summary
				type: text
			description:
				label: Description
				type: textarea
				size: small
			_beginDate:
				label: Beginning date
				type: date
				format: MM/DD/YYYY
			_beginTime:
				label: Beginning time
				type: time
				interval: 15
			_endDate:
				label: Ending date
				type: date
				format: MM/DD/YYYY
			_endTime:
				label: Ending time
				type: time
				interval: 15