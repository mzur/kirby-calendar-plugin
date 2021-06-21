<?php

load([
   'Mzur\\KirbyCalendar\\Calendar' => 'Calendar.php',
   'Mzur\\KirbyCalendar\\Event' => 'Event.php',
], __DIR__);

Kirby::plugin('mzur/kirby-calendar', [
   'snippets' => [
      'calendar-div' => __DIR__.'/snippets/calendar-div.php',
      'calendar-ical' => __DIR__.'/snippets/calendar-ical.php',
      'calendar-table' => __DIR__.'/snippets/calendar-table.php',
      'calendar-teaser' => __DIR__.'/snippets/calendar-teaser.php',
   ],
   'translations' => [
      'de' => [
         'calendar-full-time-format' => '%d %X',
         'calendar-month-format' => '%B %Y',
         'calendar-no-entry' => 'Zur Zeit gibt es keine Events.',
         'calendar-time-format' => '%d',
         'date' => 'Datum',
         'description' => 'Beschreibung',
         'title' => 'Titel',
         'to' => 'bis',
      ],
      'en' => [
         'calendar-full-time-format' => '%d %X',
         'calendar-month-format' => '%B %Y',
         'calendar-no-entry' => 'There currently are no events.',
         'calendar-time-format' => '%d',
         'date' => 'Date',
         'description' => 'Description',
         'title' => 'Title',
         'to' => 'to',
      ],
   ],
]);
