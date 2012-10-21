<?php

class Calendar {

	public static $HAS_TIME = true;
	/** delimiter that divides start from end time. e.g. 01.01.70->02.01.70. */
	public static $TIME_DELIMITER = '->';

	/** default formats. see the doc of strftime() for the formatting syntax. */
	const DATE_FORMAT = '%d-%m-%Y';
	const MONTH_FORMAT = '%B';
	/** default message, when no entry is available. */
	const NO_ENTRY_MSG = 'No entry.';

	/** the array of events. */
	private $events = array();
	/** the keys of all event arrays. e.g. the columns in the table layout. */
	private $columns = array();
	/**
	 * language for the locale settings. must be RFC 1766 or ISO 639 valid.
	 * e.g. en_US or de_DE.
	 */
	private $lang = false;
	/**
	 * timezone for decoding the input date-string. see http://php.net/manual/en/timezones.php
	 * for supported timezones. default is the server's timezone.
	 */
	private $timezone = false;
	/** format of the date (start and end) displayed for every event. */
	private $dateFormat = false;
	/** format of the month, which divides the events. */
	private $monthFormat = false;
	/**
	 * flag for input with only dates and no time. if 'false' the time will be
	 * set +23:59
	 */
	private $hasTime = false;
	/** message, when no entry is available. */
	private $noEntryMsg = false;

	function __construct($cEvents, $cOptions=array()) {
		if (!$cEvents) return false;

		$this->lang = @$cOptions['lang'];
		$this->timezone = @$cOptions['timezone'];
		$this->dateFormat = @$cOptions['dateForm'];
		$this->monthFormat = @$cOptions['monthForm'];
		$this->hasTime = @$cOptions['hasTime'];
		$this->noEntryMsg = @$cOptions['noEntryMsg'];

		// timezone must be set, before the events are parsed! otherwise the
		// timestamps will be adjusted.
		$this->configure();

		$this->events = $this->parseEvents($cEvents);

		//collect columns
		foreach ($this->events as $event) {
			$this->columns = array_merge($this->columns, array_diff($event->getColNames(), $this->columns));
		}
	}

	private function configure() {
		if (!$this->hasTime) $this->hasTime = Calendar::$HAS_TIME;

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

	/** returns an array with Event's for every entry of $events. */
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

		if (file_exists(__DIR__."/templates/".$template.".php")) {
			require_once(__DIR__."/templates/".$template.".php");
		} else {
			echo  'template not supported';
		}
	}
?>
