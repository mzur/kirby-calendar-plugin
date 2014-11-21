<?php

/**
 * A simple calendar object that mainly consists of a list of Event objects.
 */
class Calendar {

	/**
	 * Array of all events of this calendar.
	 */
	private $events;

	/**
	 * Array of the aggregation of all present fields of the events. Not every 
	 * event may contain every field.
	 */
	private $eventFields;

	/**
	 * @param array $events An array of 'raw' events. A raw event is an array
	 * of field => value pairs.
	 */
	function __construct($events = array()) {
		// intantiate all the given events to Event objects
		$this->events = array_map('event::instantiate', $events);
		// sort the events from old to new
		usort($this->events, 'event::compare');

		$this->eventFields = self::findEventFields($this->events);
	}

	/**
	 * @return all events, even the ones that are already past.
	 */
	public function getAllEvents() {
		return $this->events;
	}

	/**
	 * @return all future events.
	 */
	public function getEvents() {
		return array_filter($this->events, 'event::filterPast');
	}

	/**
	 * @return all present fields of the events of this calendar.
	 */
	public function getEventFields() {
		return $this->eventFields;
	}

	/**
	 * Aggregates all the fields present in the given event object.
	 * @param array $events An array of Event objects.
	 */
	private static function findEventFields($events) {
		$fields = array();

		foreach ($events as $event) {
			$fields = a::merge($fields, $event->getFieldKeys());
		}

		// make an associative array with the same keys as values
		return array_combine($fields, $fields);
	}
}