# kirby-calendar-plugin

A plugin for the [Kirby CMS](http://getkirby.com) to easily implement an event calendar.


## Installation

1. [Download](https://github.com/mzur/kirby-calendar-plugin/archive/refs/heads/master.zip) this repository and extract the directory to `site/plugins/`.

2. Make sure you have the language support of Kirby activated (even, if you only want to support one language).

For more information on the multi language support of Kirby, see [the docs](https://getkirby.com/docs/guide/languages/introduction).

## Usage

Making this plugin work involves three steps: providing the calendar data, setting up the calendar object and displaying the calendar as a snippet.

### Calendar data

You provide the calendar data as you provide any content in Kirby; as a data field of the page. For example take the page `content/calendar/calendar.en.txt`:

```txt
Title: Calendar

----

Calendar: 
-
  summary: My Event
  _begin_date: 12/14/2014
  _begin_time: 10:00
  _end_date: 12/14/2014
  _end_time: 15:00
- 
  summary: My supercool event for the whole day
  description: This wil be a nice event!
  _begin_date: 10/01/2014
```

The `Calendar` field contains all events as a [structure](https://getkirby.com/docs/reference/panel/fields/structure). Each event has several own fields, too, but of them only  `_begin_date` is mandatory (see [event-fields](#event-fields)).  You can define as many other fields as you like.

### Calendar object

Now let's get to the `calendar` template. Setting up the calendar object is really simple; you only have to do this:

```php
<?php $calendar = new Mzur\KirbyCalendar\Calendar($page->calendar()->yaml()); ?>
```
### Calendar snippet

To produce any output, you have to use a calendar snippet. This enables you to print even multiple different versions of the same calendar in the same template! You can use one of the [snippets provided by this plugin](#snippets) or make your own.

Typical usage of a snippet looks like this (here we use the [`table`](#table) snippet provided by the plugin):

```php
<?php
   snippet('calendar-table', [
      'calendar' => $calendar,
      'fields' => [
         'summary' => t('title'),
         'description' => t('description')
      ]
   ]);
?>
```

There are two variables we are passing along to the snippet here: `$calendar` is the calendar object we created in the previous step and `$fields` is an associative array of all the event fields that should be displayed in this snippet. The keys of this array are the keys of the event fields specified in the calendar data; the values specify how these fields should be displayed (in this case it depends on the language of the site).

So this is basically it. If you followed the instructions correctly, you should see the calendar printed on the page.

### Panel

The calendar data is formatted to work perfectly with a [structure field](https://getkirby.com/docs/reference/panel/fields/structure). Here is an example blueprint:

```yaml
title: Events
preset: page
fields:
   title:
      label: Title
      type:  text
      readonly: true
   events:
      label: Events
      type: structure
      sortBy: _begin_date asc
      fields:
         _begin_date:
            label: Start date
            type: date
            display: DD.MM.YYYY
            required: true
         _begin_time:
            label: Start time
            type: time
            interval: 15
            required: true
         _end_date:
            label: End date
            type: date
            display: DD.MM.YYYY
            required: true
         _end_time:
            label: End time
            type: time
            interval: 15
            required: true
         summary:
            label: Title
            type: text
            required: true
```

## Localisation

The plugin is designed to fully support localisation, thus requiring Kirby's language support to be enabled.

### Language variables

The provided language files contain some language variables used by the plugin internally, by the default snippets or in this example.

- `calendar-time-format`: The formatting string of the date of an event (without a time).
- `calendar-full-time-format`: The formatting string of the date and time of an event.
- `calendar-month-format`: The formatting string of a month.
- `calendar-no-entry`: The message printed when there are no events to display.

See [here](http://php.net/manual/en/function.strftime.php) for more information on time formatting strings.

### Timezone

By default the plugin uses the uses the timezone of the server it is running on. The correct timezone is essential for correct `datetime` attributes or iCal output, for example. To set the timezone manually you can use [`data_default_timezone_set`](http://php.net/manual/en/function.date-default-timezone-set.php) in the header snippet of your site like this:

```php
<?php date_default_timezone_set('Europe/Berlin'); ?>
```

### Locale

The locale is set by Kirby's language configuration. So if you've set up everything correctly, it should work without problems.

## Event fields

Events can have arbitrary fields. However there are the special fields for this plugin marked by a `_`-prefix:

- `_begin_date`: The only **mandatory** field specifying the date, the event begins.
- `_begin_time`: The time of the day the event begins.
- `_end_date`: The date the event ends.
- `_end_time`: The time of the day the event ends.

The behavior with different combinations of these fields is the following:

- only `_begin_date` given: The event lasts the whole day.

- `_begin_date` and `_begin_time` given: The event lasts from the given time until midnight (12 am) of the following day.

- `_begin_date` and `_end_time` given: The event lasts from midnight until the given time of the day.

- `_begin_date` and `_end_date` given: The event lasts from mindnight of the beginning day until midnight of the day after the ending day.

## Functions

There are two classes you can work with, `Mzur\KirbyCalendar\Calendar` and `Mzur\KirbyCalendar\Event`.

### Calendar class

The `Mzur\KirbyCalendar\Calendar` class has the following functions:

#### getAllEvents()

Returns an array of all the events of this calendar, including the past events.

#### getEvents()

Returns an array of all future events of this calendar.

#### getEventFields()

Returns an array of all the event fields occurring in the events of this calendar.

### Event class

The `Mzur\KirbyCalendar\Event` class has the following functions:

#### getBeginTimestamp()

Returns the UNIX timestamp in seconds for the beginning of this event.

#### getBeginDate()

Returns the [PHP date array](http://php.net/manual/en/function.getdate.php) of the beginning of this event.

#### getBeginStr()

Returns the formatted string of the beginning of this event. Formatting is done according to the language configuration of Kirby. If the event was given a time, the `calendar-full-time-format` is used, `calendar-time-format` otherwise.

#### getBeginHtml()

Returns the formatted string of the beginning of this event as a `time` element with `datetime` attribute.

#### getEndTimestamp()

Returns the UNIX timestamp in seconds for the ending of this event.

#### getEndDate()

Returns the [PHP date array](http://php.net/manual/en/function.getdate.php) of the ending of this event.

#### getEndStr()

Returns the formatted string of the ending of this event. Formatting is done according to the language configuration of Kirby. If the event was given a time, the `calendar-full-time-format` is used, `calendar-time-format` otherwise.

#### getEndHtml()

Returns the formatted string of the ending of this event as a `time` element with `datetime` attribute.

#### getFieldKeys()

Returns all field keys of this event that have no `_`-prefix.

#### getField($key)

Returns the content of the field or an empty string if it doesn't exist in this event.

`$key`: The event field key whose value should be returned.

#### isPast()

Returns `true` if this event is past at the current time, `false` otherwise.

#### hasEnd()

Returns `true` if the event was given an ending date or time, `false` otherwise.

## Snippets

There are a few default calendar snippets provided by this plugin. You can find them in the `snippets` directory of this repo. Of course you can customize them to your needs or write ones on your own!

### table

Displays all events (including the past ones) as a table. Only the event fields given in `$fields` are displayed. `$fields` specify the column labels as well. Example output:

```html

<table class="calendar">
   <thead>
      <tr>
         <th>Date</th>
         <th>Title</th>
         <th>Description</th>
      </tr>
   </thead>
   <tbody>
      <tr class="month past">
         <td colspan="3">October 2014</td>
      </tr>
      <tr class="event past">
         <td><time datetime="2014-10-01T00:00:00Z">01</time></td>
         <td>My supercool event for the whole day</td>
         <td>This wil be a nice event!</td>
      </tr>
      <tr class="month">
         <td colspan="3">December 2014</td>
      </tr>
      <tr class="event">
         <td><time datetime="2014-12-14T10:00:00Z">14 10:00:00 AM</time> to <time datetime="2014-12-15T23:45:00Z">15 11:45:00 PM</time></td>
         <td>Event</td>
         <td></td>
      </tr>
   </tbody>
</table>
```

### div

Displays all events (including the past ones) as a div layout. Only the event fields given in `$fields` are displayed. `$fields` specify the column labels as well. Example output:

```html
<section class="calendar">
   <div class="row head">
      <div class="item">Date</div>
      <div class="item">Title</div>
      <div class="item">Description</div>
   </div>
   <div class="row month past">
      <div class="item">October 2014</div>
   </div>
   <div class="row event past">
      <div class="item date"><time datetime="2014-10-01T00:00:00Z">01</time></div>
      <div class="item">My supercool event for the whole day</div>
      <div class="item">This wil be a nice event!</div>
   </div>
   <div class="row month">
      <div class="item">December 2014</div>
   </div>
   <div class="row event">
      <div class="item date"><time datetime="2014-12-14T10:00:00Z">14 10:00:00 AM</time> to <time datetime="2014-12-15T23:45:00Z">15 11:45:00 PM</time></div>
      <div class="item">Event</div>
      <div class="item"></div>
   </div>
</section>
```

### ical

Displays all events (including the past ones) in iCal format. This is very useful to provide iCal export of your calendar. It only includes the `summary`, `description` and `location` event fields. Be sure to set the header of your iCal-export template like this:

```php
<?php header('Content-type: text/calendar'); ?>
```

Example output:

```ical
BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//http://example.com//Kirby Calendar Plugin//EN
METHOD:PUBLISH
BEGIN:VEVENT
DTSTART:20141001T000000Z
DTEND:20141002T000000Z
SUMMARY:My supercool event for the whole day
DESCRIPTION:This wil be a nice event!
LOCATION:
END:VEVENT
BEGIN:VEVENT
DTSTART:20141214T100000Z
DTEND:20141215T234500Z
SUMMARY:Event
DESCRIPTION:
LOCATION:
END:VEVENT
END:VCALENDAR
```

### teaser

Displays a set number of future events as a list. The number of events to display is specified in `$items`. The event fields to display are specified in `$fields`. Example output:

```html
<ul class="teaser">
   <li><strong><time datetime="2014-12-14T10:00:00Z">14 10:00:00 AM</time></strong> Event</li>
</ul>
```
