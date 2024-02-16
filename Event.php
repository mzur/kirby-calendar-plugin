<?php

namespace Mzur\KirbyCalendar;

use Exception;
use Kirby\Toolkit\A;
use Kirby\Toolkit\Str;

class Event {

	/**
	 * The string for the beginning date field key of an event.
	 */
	const BEGIN_DATE_KEY = '_begin_date';

	/**
	 * The string for the beginning time field key of an event.
	 */
	const BEGIN_TIME_KEY = '_begin_time';

	/**
	 * The string for the end date field key of an event.
	 */
	const END_DATE_KEY = '_end_date';

	/**
	 * The string for the end time field key of an event.
	 */
	const END_TIME_KEY = '_end_time';

	/**
	 * An array of field keys that are required to create an event.
	 */
	private static $requiredKeys = [
		self::BEGIN_DATE_KEY,
	];

	/**
	 * The timestamp of the beginning of this event.
	 */
	private $beginTimestamp;

	/**
	 * The timestamp of the end of this event. May be false if the event only
	 * lasts a day.
	 */
	private $endTimestamp;

	/**
	 * Was an ending date given?
	 */
	private $hasEnd;

	/**
	 * Was a beginning time given for this event?
	 */
	private $hasBeginTime;

	/**
	 * Was an ending time given for this event?
	 */
	private $hasEndTime;

	/**
	 * Formatting of the output string of beginning and end times.
	 */
	private $timeFormat;

	/**
	 * Array of fields without the 'private' fields starting with a
	 * <code>_</code>.
	 */
	private $fields;

	/**
	 * @param array $event The fields of this event including the 'private'
	 * fields which start with a <code>_</code> (e.g. <code>_begin_date</code>).
	 */
	function __construct($event) {
		self::validate($event);

		$this->hasEnd = true;

		$this->hasBeginTime = (bool) A::get($event, self::BEGIN_TIME_KEY);
		$this->hasEndTime = (bool) A::get($event, self::END_TIME_KEY);

		$this->beginTimestamp = self::getTimestamp(
			A::get($event, self::BEGIN_DATE_KEY),
			A::get($event, self::BEGIN_TIME_KEY)
		);

		$this->endTimestamp = self::getTimestamp(
			A::get($event, self::END_DATE_KEY),
			A::get($event, self::END_TIME_KEY)
		);

		// if there is no end date given, use the same as the beginning date
		if (!$this->endTimestamp) {
			$this->endTimestamp = self::getTimestamp(
				A::get($event, self::BEGIN_DATE_KEY),
				A::get($event, self::END_TIME_KEY)
			);

			// if there also is no end time given, there is no end at all
			if (!$this->hasEndTime) {
				$this->hasEnd = false;
			}
		}

		// if there is no end time given, the event lasts until end of the day
		if (!$this->hasEndTime) {
			$this->endTimestamp = strtotime('tomorrow', $this->endTimestamp);
		}

		// only use the full format, if there were times given for this event
		$this->timeFormat = ($this->hasBeginTime || $this->hasEndTime)
			? t('calendar-full-time-format')
			: t('calendar-time-format');

		// remove the 'private' fields
		$this->fields = self::filterFields($event);
	}

	/**
	 * @param array $event A 'raw' event array.
	 * @return A new Event instance.
	 */
	public static function instantiate($event) {
		return new Event($event);
	}

	/**
	 * @param Event $e1
	 * @param Event $e2
	 * @return Integer < 0 if $e1 is older than $e2, 0 if they happen at the
	 * same time and > 0 if $e2 is older than $e1
	 */
	public static function compare($e1, $e2) {
		return $e1->beginTimestamp - $e2->beginTimestamp;
	}

	/**
	 * @param Event $e
	 * @return <code>false</code> if the event is past, <code>true</code>
	 * otherwise.
	 */
	public static function filterPast($e) {
		return !$e->isPast();
	}

	/**
	 * Checks if all required keys are in the 'raw' event array. Throws an
	 * exception if one is missing.
	 *
	 * @param array $event a 'raw' event array containing all fields
	 */
	private static function validate($event) {
		$missingKeys = A::missing($event, self::$requiredKeys);
		if ($missingKeys) {
			$message = "Event creation failed because of the following missing " .
				"required fields:\n" . A::show($missingKeys, false);
			throw new Exception($message, 1);
		}
	}

	/**
	 * @param string $date the date, e.g. '01.01.1970'
	 * @param string $time optional time, e.g. '10:00:00'
	 * @return The date as a UNIX timestamp or <code>false</code> if there
	 * was no $date given.
	 */
	private static function getTimestamp($date, $time = '') {
		return ($date) ? strtotime($date . ' ' . $time) : false;
	}

	/**
	 * @param array $event the 'raw' event array of fields.
	 * @return the array of fields without the 'private' fields with keys
	 */
	private static function filterFields($event) {
		foreach (array_keys($event) as $key) {
			if (Str::startsWith($key, '_')) {
				unset($event[$key]);
			}
		}

		return $event;
	}

	/**
	 * @return The timestamp in seconds for the beginning of this event.
	 */
	public function getBeginTimestamp() {
		return $this->beginTimestamp;
	}

	/**
	 * @return The date array of the beginning of this event.
	 */
	public function getBeginDate() {
		return getdate($this->beginTimestamp);
	}

	/**
	 * @return The formatted string of the beginning of this event. Formatting
	 * is done according to the language configuration of Kirby.
	 */
	public function getBeginStr($languageCode) {
    return \IntlDateFormatter::formatObject(
      \DateTime::createFromFormat('U', $this->beginTimestamp), $this->timeFormat, $languageCode);
	}

	/**
	 *	@return The formatted string of the beginning of this event wrapped in
	 * a <code>time</code> element with <code>datetime</code> attribute.
	 */
	public function getBeginHtml($languageCode = 'en') {
		return '<time datetime="' .
			gmdate('Y-m-d\TH:i:s\Z', $this->beginTimestamp) . '">' .
			$this->getBeginStr($languageCode) . '</time>';
	}

	/**
	 * @return The timestamp in seconds for the ending of this event.
	 */
	public function getEndTimestamp() {
		return $this->endTimestamp;
	}

	/**
	 * @return The date array of the ending of this event.
	 */
	public function getEndDate() {
		return getdate($this->endTimestamp);
	}

	/**
	 * @return The formatted string of the ending of this event. Formatting
	 * is done according to the language configuration of Kirby.
	 */
	public function getEndStr($languageCode) {
		/*
		 * The convention for an event lasting all day is from midnight of the
		 * day to midnight of the following day. But if we have an event lasting
		 * from the 14th to 15th it would be printed as 14th (12 am) to 16th 
		 * (12 am).
		 * So if there is no custom ending time given, we go one second back, so
		 * it prints as 14th (12 am) to 15th (11:59:59 pm).
		 */
		$timestamp = ($this->hasEndTime)
			? $this->endTimestamp
			: $this->endTimestamp - 1;
    return \IntlDateFormatter::formatObject(
      \DateTime::createFromFormat('U', $timestamp), $this->timeFormat, $languageCode);
	}

	/**
	 *	@return The formatted string of the ending of this event wrapped in
	 * a <code>time</code> element with <code>datetime</code> attribute.
	 */
	public function getEndHtml($languageCode = 'en') {
		return '<time datetime="' .
			gmdate('Y-m-d\TH:i:s\Z', $this->endTimestamp) . '">' .
			$this->getEndStr($languageCode) . '</time>';
	}

	/**
	 * @return All non-'private' field keys of this event.
	 */
	public function getFieldKeys() {
		return array_keys($this->fields);
	}

	/**
	 * @param string $key The key of the field to get.
	 * @return The content of the field or an empty string if it doesn't exist
	 * in this event.
	 */
	public function getField($key) {
		return A::get($this->fields, $key, '');
	}

	/**
	 * @return <code>true</code> if the event is past at the current time,
	 * <code>false</code> otherwise
	 */
	public function isPast() {
		return $this->endTimestamp < time();
	}

	/**
	 * @return <code>true</code> if the event has an ending date/time
	 * <code>false</code> otherwise
	 */
	public function hasEnd() {
		return $this->hasEnd;
	}
}
