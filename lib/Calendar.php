<?php
/*
 ** The MIT License **

 Copyright (c) 2012 Christoph Bach (chbach), Martin Zurowietz (mzur)

 Permission is hereby granted, free of charge, to any person obtaining a copy
 of this software and associated documentation files (the "Software"), to deal
 in the Software without restriction, including without limitation the rights
 to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 copies of the Software, and to permit persons to whom the Software is
 furnished to do so, subject to the following conditions:

 The above copyright notice and this permission notice shall be included in all
 copies or substantial portions of the Software.

 THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 SOFTWARE.


 Home:
 	<https://github.com/mzur/kirby-calendar-plugin>

 Authors: 	
 	the Inspired Ones <http://the-inspired-ones.de/>
 	 - Christoph Bach <http://christoph-bach.net/>
 	 - Martin Zurowietz

 Requirements:
 	- PHP 5.3
*/

class Calendar {

	/** Default hasTime value. See $hasTime below. */
	public static $HAS_TIME = true;
	/** Delimiter that divides start from end time. e.g. 01.01.70->02.01.70. */
	public static $TIME_DELIMITER = '->';

	/** Default formats. See the doc of strftime() for the formatting syntax. */
	const DATE_FORMAT = '%d-%m-%Y';
	const MONTH_FORMAT = '%B';
	/** Default message, if no entry is available. */
	const NO_ENTRY_MSG = 'No entry.';

	/** The array of events. */
	private $events = array();
	/** The keys of all event arrays. e.g. the columns in the table layout. */
	private $columns = array();
	/**
	 * Language for the locale settings. must be RFC 1766 or ISO 639 valid.
	 * e.g. en_US or de_DE.
	 */
	private $lang = false;
	/**
	 * Timezone for decoding the input date-string. See http://php.net/manual/en/timezones.php
	 * for supported timezones. Default is the server's timezone.
	 */
	private $timezone = false;
	/** Format of the date (start and end) displayed for every event. */
	private $dateFormat = false;
	/** Format of the month, which divides the events. */
	private $monthFormat = false;
	/**
	 * Flag for input with only dates and no time. if 'false' the time will be
	 * set +23:59.
	 */
	private $hasTime = false;
	/** Message, if no entry is available. */
	private $noEntryMsg = false;

	/**
	 * Loads the events and sets all options for this calendar.
	 * @param array $cEvents
	 *		The array of events parsed from the YAML output.
	 * @param array $cOptions
	 * 		The options array.
	 */
	function __construct($cEvents, $cOptions=array()) {		
		$this->lang = @$cOptions['lang'];
		$this->timezone = @$cOptions['timezone'];
		$this->dateFormat = @$cOptions['dateForm'];
		$this->monthFormat = @$cOptions['monthForm'];
		$this->hasTime = (array_key_exists('hasTime', $cOptions))
			? $cOptions['hasTime']
			: Calendar::$HAS_TIME;
		$this->noEntryMsg = @$cOptions['noEntryMsg'];

		// Timezone must be set, before the events are parsed! Otherwise the
		// timestamps will be adjusted.
		$this->configure();
		
		if (!$cEvents) return false;

		$this->events = $this->parseEvents($cEvents);

		//Collect columns
		foreach ($this->events as $event) {
			$this->columns = array_merge($this->columns, array_diff($event->getColNames(), $this->columns));
		}
	}

	/** Sets the defined options if available. Sets the defaults otherwise. */
	private function configure() {
		if ($this->lang) {
			setLocale(LC_TIME, $this->lang);
		}

		if ($this->timezone) {
			date_default_timezone_set($this->timezone);
		}

		if (!$this->dateFormat) $this->dateFormat = Calendar::DATE_FORMAT;

		if (!$this->monthFormat) $this->monthFormat = Calendar::MONTH_FORMAT;

		if (!$this->noEntryMsg) $this->noEntryMsg = Calendar::NO_ENTRY_MSG;
	}

	/** Returns an array with Event's for every entry of $events. */
	private function parseEvents($events) {
		$ret = array();

		foreach ($events as $key => $info) {
			$ret[] = new Event($key, $info, $this->hasTime);
        }

        usort($ret, array('Event', 'SORT_EVENTS'));

        return $ret;
	}

	public function noEntry() {
		echo $this->noEntryMsg;
	}

	/**
	 * This function will either, if the file exists, include a tenplate file, or
	 * echo an error message that the given template does not exist.
	 *
	 * @param string $template
	 * 				The name of the template.
	 *
	 */
	public function cal($template) {

		// some useful variables which can be used in the templates
		$eventsArr = $this->events;
		$columnsArr = $this->columns;
		$currentTime = getdate(strtotime('now'));
		$month = false;
		global $site;

		$templatePath = dirname(__FILE__)."/../templates/".$template.".php";
		
		if (file_exists($templatePath)) {
			require_once($templatePath);
		} else {
			echo  'template not supported';
		}
	}
}
?>
