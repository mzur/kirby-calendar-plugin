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

		$this->beginTimestamp = self::getDate(
			A::get($event, self::BEGIN_DATE_KEY),
			A::get($event, self::BEGIN_TIME_KEY)
		);

		$this->endTimestamp = self::getDate(
			A::get($event, self::END_DATE_KEY),
			A::get($event, self::END_TIME_KEY)
		);

		// if there is no end date given, use the same as the beginning date
		if (!$this->endTimestamp) {
			$this->endTimestamp = self::getDate(
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
			$this->endTimestamp->setTime(23, 59, 59);
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
		return $e1->beginTimestamp <=> $e2->beginTimestamp;
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
	 * @return The date as a DateTimeImmutable object or <code>false</code> if there
	 * was no $date given.
	 */
	private static function getDate($date, $time = '') {
		if ($date) {
			return new \DateTimeImmutable($date . ' ' . $time);
		} else {
			return false;
		}
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
		return $this->beginTimestamp->getTimestamp();
	}

	/**
	 * @return The date array of the beginning of this event.
	 */
	public function getBeginDate() {
		return getdate($this->beginTimestamp->getTimestamp());
	}

	/**
	 * @param string $languageCode the language used to create the date string, e.g. 'de'
	 * @return The formatted string of the beginning of this event. Formatting
	 * is done according to the language code given as argument.
	 */
	public function getBeginStr($languageCode) {
		return \IntlDateFormatter::formatObject(
			$this->beginTimestamp,
			$this->timeFormat,
			$languageCode);
	}

	/**
	 *	@return The formatted string of the beginning of this event wrapped in
	 * a <code>time</code> element with <code>datetime</code> attribute.
	 */
	public function getBeginHtml($languageCode = 'en') {
		return '<time datetime="' .
			gmdate('Y-m-d\TH:i:s\Z', $this->beginTimestamp->getTimestamp()) . '">' .
			$this->getBeginStr($languageCode) . '</time>';
	}

	/**
	 * @return The timestamp in seconds for the ending of this event.
	 */
	public function getEndTimestamp() {
		return $this->endTimestamp->getTimestamp();
	}

	/**
	 * @return The date array of the ending of this event.
	 */
	public function getEndDate() {
		return getdate($this->endTimestamp->getTimestamp());
	}

	/**
	 * @param string $languageCode the language used to create the date string, e.g. 'de'
	 * @return The formatted string of the ending of this event. Formatting
	 * is done according to the language code given as argument.
	 */
	public function getEndStr($languageCode) {
		return \IntlDateFormatter::formatObject(
			$this->endTimestamp,
			$this->timeFormat,
			$languageCode);
	}

	/**
	 *	@return The formatted string of the ending of this event wrapped in
	 * a <code>time</code> element with <code>datetime</code> attribute.
	 */
	public function getEndHtml($languageCode = 'en') {
		return '<time datetime="' .
			gmdate('Y-m-d\TH:i:s\Z', $this->endTimestamp->getTimestamp()) . '">' .
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
		return $this->endTimestamp < new \DateTime("now");
	}

	/**
	 * @return <code>true</code> if the event has an ending date/time
	 * <code>false</code> otherwise
	 */
	public function hasEnd() {
		return $this->hasEnd;
	}
}
