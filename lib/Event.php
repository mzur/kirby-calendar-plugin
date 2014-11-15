<?php

class Event {

	/**
	 * The string for the beginning date field key of an event.
	 */
	const begin_date_key = '_begin_date';
	/**
	 * The string for the beginning time field key of an event.
	 */
	const begin_time_key = '_begin_time';
	/**
	 * The string for the end date field key of an event.
	 */
	const end_date_key = '_end_date';
	/**
	 * The string for the end time field key of an event.
	 */
	const end_time_key = '_end_time';

	/**
	 * An array of field keys that are required to create an event.
	 */
	private static $required_keys;

	/**
	 * The timestamp of the beginning of this event.
	 */
	private $begin_timestamp;

	/**
	 * The timestamp of the end of this event. May be false if the event only
	 * lasts a day.
	 */
	private $end_timestamp;

	/**
	 * Was an ending date given?
	 */
	private $has_end;

	/**
	 * Was a beginning time given for this event?
	 */
	private $has_begin_time;

	/**
	 * Was an ending time given for this event?
	 */
	private $has_end_time;

	/**
	 * Formatting of the output string of beginning and end times.
	 */
	private $time_format;

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

		$this->has_end = true;

		$this->has_begin_time = array_key_exists(self::begin_time_key, $event);
		$this->has_end_time = array_key_exists(self::end_time_key, $event);

		$this->begin_timestamp = self::get_timestamp(
			a::get($event, self::begin_date_key),
			a::get($event, self::begin_time_key)
		);

		$this->end_timestamp = self::get_timestamp(
			a::get($event, self::end_date_key),
			a::get($event, self::end_time_key)
		);

		// if there is no end date given, use the same as the beginning date
		if (!$this->end_timestamp) {
			$this->end_timestamp = self::get_timestamp(
				a::get($event, self::begin_date_key),
				a::get($event, self::end_time_key)
			);

			// if there also is no end time given, there is no end at all
			if (!$this->has_end_time) {
				$this->has_end = false;
			}
		}

		// if there is no end time given, the event lasts until end of the day
		if (!array_key_exists(self::end_time_key, $event)) {
			$this->end_timestamp = strtotime('tomorrow', $this->end_timestamp);
		}

		// only use the full format, if there were times given for this event
		$this->time_format = ($this->has_begin_time || $this->has_end_time)
			? l::get('calendar-full-time-format')
			: l::get('calendar-time-format');

		// remove the 'private' fields
		$this->fields = self::filter_fields($event);
	}

	/**
	 * Static initializer.
	 */
	public static function __init() {
		self::$required_keys = array(
			self::begin_date_key
		);
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
		return $e1->begin_timestamp - $e2->begin_timestamp;
	}

	/**
	 * @param Event $e
	 * @return <code>false</code> if the event is past, <code>true</code>
	 * otherwise.
	 */
	public static function filter_past($e) {
		return !$e->is_past();
	}

	/**
	 * @return The timestamp in seconds for the beginning of this event.
	 */
	public function get_begin_timestamp() {
		return $this->begin_timestamp;
	}

	/**
	 * @return The date array of the beginning of this event.
	 */
	public function get_begin_date() {
		return getdate($this->begin_timestamp);
	}

	/**
	 * @return The formatted string of the beginning of this event. Formatting
	 * is done according to the language configuration of Kirby.
	 */
	public function get_begin_str() {
		return strftime($this->time_format, $this->begin_timestamp);
	}

	/**
	 * @return The timestamp in seconds for the ending of this event.
	 */
	public function get_end_timestamp() {
		return $this->end_timestamp;
	}

	/**
	 * @return The date array of the ending of this event.
	 */
	public function get_end_date() {
		return getdate($this->end_timestamp);
	}

	/**
	 * @return The formatted string of the ending of this event. Formatting
	 * is done according to the language configuration of Kirby.
	 */
	public function get_end_str() {
		/*
		 * The convention for an event lasting all day is from midnight of the
		 * day to midnight of the following day. But if we have an event lasting
		 * from the 14th to 15th it would be printed as 14th (12 am) to 16th 
		 * (12 am).
		 * So if there is no custom ending time given, we go one second back, so
		 * it prints as 14th (12 am) to 15th (11:59:59 pm).
		 */
		$timestamp = ($this->has_end_time)
			? $this->end_timestamp
			: $this->end_timestamp - 1;
		return strftime($this->time_format, $timestamp);
	}

	/**
	 * @return All non-'private' field keys of this event.
	 */
	public function get_field_keys() {
		return array_keys($this->fields);
	}

	/**
	 * @param string $key The key of the field to get.
	 * @return The content of the field or an empty string if it doesn't exist
	 * in this event.
	 */
	public function get_field($key) {
		return a::get($this->fields, $key, '');
	}

	/**
	 * @return <code>true</code> if the event is past at the current time,
	 * <code>false</code> otherwise
	 */
	public function is_past() {
		return $this->end_timestamp < time();
	}

	/**
	 * @return <code>true</code> if the event has an ending date/time
	 * <code>false</code> otherwise
	 */
	public function has_end() {
		return $this->has_end;
	}

	/**
	 * Checks if all required keys are in the 'raw' event array. Throws an
	 * exception if one is missing.
	 *
	 * @param array $event a 'raw' event array containing all fields
	 */
	private static function validate($event) {
		$missing_keys = a::missing($event, self::$required_keys);
		if (!empty($missing_keys)) {
			$message = "Event creation failed because of the following missing " .
				"required fields:\n" . a::show($missing_keys, false);
			throw new Exception($message, 1);
		}
	}

	/**
	 * @param string $date the date, e.g. '01.01.1970'
	 * @param string $time optional time, e.g. '10:00:00'
	 * @return The date as a UNIX timestamp or <code>false</code> if there
	 * was no $date given.
	 */
	private static function get_timestamp($date, $time = '') {
		return ($date) ? strtotime($date . ' ' . $time) : false;
	}

	/**
	 * @param array $event the 'raw' event array of fields.
	 * @return the array of fields without the 'private' fields with keys
	 */
	private static function filter_fields($event) {
		foreach (array_keys($event) as $key) {
			if (str::startsWith($key, '_')) {
				unset($event[$key]);
			}
		}

		return $event;
	}
}

// initialize the static variables
event::__init();