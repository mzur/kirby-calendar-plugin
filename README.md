kirby-calendar-plugin
=====================

A plugin for the [Kirby CMS](http://getkirby.com) to easily implement an event calendar.

## Installation

All you have to do is to put the `calendar.php` in the `/site/plugins` directory.

## Usage

### YAML input

The events shown by the Calendar Plugin will be read out of a field of the page, structured like this in its source `.txt`:

```yaml
Events:
01.01.2012 10:00 -> 02.01.2012 10:00:
	Location: The Pub
	Price: free
	
03.01.2012 -> 04.01.2012:
	Location: Concert hall
	Price: a beer

05.01.2012:
	Description: chillin'
	Location: couch
	Price: priceless
```

More general each event looks like this (`[]` is optional):

```yaml
beginDate [beginTime] [-> endDate [endTime]]:
	[Category: [Value]]
	[...as much categorys as you like]
```

You can use different formatting for the date and time (e.g. `01-31-2012 11pm`). If your preferred formatting is not supported, check the `'timezone'` option (see **Options** below).

See [Structured Field Content](http://getkirby.com/blog/structured-field-content) for more Information about YAML and Kirby.

### The page template

To include the calendar into your website you have to put the following code in the content section of your template:

```php
<?php calendar(yaml($page->events()), $options, 'table'); ?>
```

`$page->events()` refers to the field of the page containing your events. If you have called it `Foo:`, you have to use `$page->foo()`.

The second and third parameters of `calendar()` are both optional. `$options` is the array of options (see **Options** below) and `'table'` is the name of the calendar template (see **The calendar tempalte** below).

### Options

The options are set in an array. The available options are:

#### lang

`lang` sets the locale for the time formatting (e.g. the names of the months). It must be a valid **RFC 1766** or **ISO 639** code. For example `de_DE`.

Default is `en_US`.

#### timezone

`timezone` is important for the date formats the calendar is able to read from the input. By default it should be able to read most of the common formats but if you encounter an error check this option. All valid timezones are listed [here](http://php.net/manual/en/timezones.php).

Default is the timezone of your server.

#### dateFormat

`dateFormat` sets the format of the date and time displayed for each event. For example `%d.%m.` will result in `31.12.`. All formatting characters are listed [here](http://php.net/manual/en/function.strftime.php).

Default is `%d-%m-%Y`.

#### monthFormat

`monthFormat` sets the format of the date which divides the calendar whenever a new month begins. For example `%B %Y` will result in `December 2012`. The allowed formatting characters are the same as at `dateFormat`.

Default is `%B`.

#### hasTime

If you set the `hasTime` flag to `false` the calendar will assume that you never specify a time for the events. By default at a date without time, the time will be set to `0:00`(`0am`). With this option set `false` all times are calculated `+23:59` so that the past events are marked properly.

Default is `true`.

In a future version this will be done automatically for each event.

#### noEntryMsg

This option is for multi language support. Here you can set the message that will be shown if no event is available.

Default is `No entry.`.

#### Example

```php
<?php $options = array(
	'lang' 		=> (c::get('lang.current') === 'de') ? 'de_DE' : 'fr_FR',
	'timezone' 	=> 'Europe/Berlin',
	'dateForm'	=> '%d.',
	'monthForm'	=> '%B %Y',
	'hasTime'	=> false
);?>
```

### The calendar template

In a future version you will be able to specify the layout of the calendar in a separate template file. The only layouts currently available are `table` and `div`.

You are able to style the calendar via several CSS classes. See the example HTML outputs below:

#### table

This output is from 15th October 2012.

```html
<table class="calendar">
	<tr>
		<th></th>
		<th>Location</th>
		<th>Title</th>
		<th>Host</th>
	</tr>
	<tbody>
		<tr class="month past">
			<td colspan="4">January 2012</td>
		</tr>
		<tr class="past">
			<td><time datetime="1325458740">01.01.</time></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr class="month">
			<td colspan="4">October 2012</td>
		</tr>
		<tr>
			<td><time datetime="1349474340">05.10. - 30.10.</time></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr class="past">
			<td><time datetime="1349560740">06.10.</time></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr class="month">
			<td colspan="4">January 2013</td>
		</tr>
		<tr>
			<td><time datetime="1357081140">01.01.</time></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
	</tbody>
</table>
```

#### div

This output is from 15th October 2012.

```html
<div class="calendar">
	<div class="head">
		<div>Location</div>
		<div>Title</div>
		<div>Host</div>
	</div>
	<div class="month past">January 2012</div>
	<div class="event past">
		<time datetime="1325458740">01.01.</time>
		<div class="entry"></div>
		<div class="entry"></div>
		<div class="entry"></div>
	</div>
	<div class="month">October 2012</div>
	<div class="event">
		<time datetime="1349474340">05.10. - 30.10.</time>
		<div class="entry"></div>
		<div class="entry"></div>
		<div class="entry"></div>
	</div>
	<div class="event past">
		<time datetime="1349560740">06.10.</time>
		<div class="entry"></div>
		<div class="entry"></div>
		<div class="entry"></div>
	</div>
	<div class="month">January 2013</div>
	<div class="event">
		<time datetime="1357081140">01.01.</time>
		<div class="entry"></div>
		<div class="entry"></div>
		<div class="entry"></div>
	</div>
</div>
```
