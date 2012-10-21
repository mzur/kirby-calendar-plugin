kirby-calendar-plugin
=====================

A plugin for the [Kirby CMS](http://getkirby.com) to easily implement an event calendar.

## Installation

All you have to do is to put the `calendar` directory in `/site/plugins`.

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

There are two default templates for the calendar output at the moment, `div` and
`table`. You can change them in the `templates/` directory to your needs. You
can also easily implement your own template *mytemplate* and just load it with
*'mytemplate'* as the last paramater of the calendar function.

#### table

This output is from 21st October 2012.

```html
<table class="calendar">
	<tr>
		<th>Date</th>
		<th>Location</th>
		<th>Price</th>
		<th>Description</th>
	</tr>
	<tbody>
		<tr class="month past">
			<td colspan="4">January</td>
		</tr>
		<tr class="past" >
			<td>
				<time datetime="2012-01-01T10:00:00Z">
					01-01-2012 - 02-01-2012
				</time>
			</td>
			<td>The Pub</td>
			<td>free</td>
			<td></td>
		</tr>
		<tr class="past" >
			<td>
				<time datetime="2012-01-03T00:00:00Z">
					03-01-2012- 04-01-2012
				</time>
			</td>
			<td>Concert hall</td>
			<td>a beer</td>
			<td></td>
		</tr>
		<tr class="past" >
			<td>
				<time datetime="2012-01-05T00:00:00Z">
					05-01-2012
				</time>
			</td>
			<td>couch</td>
			<td>priceless</td>
			<td>chillin'</td>
		</tr>
	</tbody>
</table>
```

#### div

This output is from 21st October 2012.

```html
<section class="calendar">
	<div class="row header">
		<div class="item">Date</div>
		<div class="item">Location</div>
		<div class="item">Price</div>
		<div class="item">Description</div>
	</div>
	<div class="row month past">
		<div class="item">January</div>
	</div>
	<div class="row past">
		<time datetime="2012-01-01T10:00:00Z">
				01-01-2012 - 02-01-2012
		</time>
		<div class="item">The Pub</div>
		<div class="item">free</div>
		<div class="item"></div>
	</div>
	<div class="row past">
		<time datetime="2012-01-03T00:00:00Z">
				03-01-2012 - 04-01-2012
		</time>
		<div class="item">Concert hall</div>
		<div class="item">a beer</div>
		<div class="item"></div>
	</div>
	<div class="row past">
		<time datetime="2012-01-05T00:00:00Z">
				05-01-2012
		</time>
		<div class="item">couch</div>
		<div class="item">priceless</div>
		<div class="item">chillin'</div>
	</div>
</section>
```

### iCal output

There's also an iCal template predefined. Note that you have to make sure that
given iCal constants such as `summary`, `description` and `location` must
be specified in your YAML input to get a proper and usable iCal output.

To get an additional iCal output, just create a subpage with an `ical.txt` in
it. Then, you are able to create a custom template `ical.php` in your
`templates/` folder. Just insert the following lines of code there:

```php
<?php
$data = $page->parent()->events();
calendar(yaml($data), array(), 'ical');
?>
```
Now you're able to set a link to this subpage which will return an iCal version
of your calendar.

## Compatibility

This plugin should run on PHP 5.3 and above. If you experience any problems,
have a look at the [Forum Thread](http://getkirby.com/forum/code-snippets/topic:138) or open an issue on GitHub.
