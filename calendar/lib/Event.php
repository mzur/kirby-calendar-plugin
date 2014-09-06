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

class Event {
	/** Begin time of the Event. */
	private $begin = false;
	/** End time of the event. */
	private $end = false;
	/** Other infos of the event. */
	private $info = false;

	/** Same as $hasTime of class Calendar. */
	private $hasTime = false;

	/**
	 * Parses the input time key to an unix timestamp and sets the info.
	 * @param String $eventKey
	 *		The String Key from the yaml output containing begin and end time.
	 * @param array $eventInfo
	 * 		The info array from the yaml output which was associated to the eventKey.
	 * @param boolean $hasTime
	 * 		If true, the time will be calculated +23:59.
	 */
	function __construct($eventKey, $eventInfo=array(), $hasTime=false) {
		if (!$eventKey) return false;

		$this->hasTime = $hasTime;

		$time = $this->parseTime($eventKey);
		$this->begin = getdate($time[0]);
		$this->end = ($time[1]) ? getdate($time[1]) : false;

		$this->info = $eventInfo;
	}

	/**
	 * Sort function to perform an usort() on an array of Events.
	 * @param Event $event1
	 * 		First event.
	 * @param Event $event2
	 * 		Second event.
	 */
	public static function SORT_EVENTS($event1, $event2) {
		$begin1 = $event1->getBegin();
		$begin2 = $event2->getBegin();
		return (int) $begin1[0] - $begin2[0];
	}

	public function getBegin() {
		return $this->begin;
	}

	public function getEnd() {
		return $this->end;
	}

	public function getInfo() {
		return $this->info;
	}

	public function getColNames() {
		return array_keys($this->info);
	}

	/**
	 * Returns a string containing the event's data in iCalendar format.
	 * Note that the event must have keys such as summary, description and
	 * location defined to get a usable ical output.
	 *
	 * @return string, event info as ical data
	 */
	public function getICal() {
		$info = $this->info;

		$returnStr  = "BEGIN:VEVENT \n";

		$returnStr .= "DTSTART:".gmdate("Ymd\TH:i:s\Z",$this->begin[0])."\n";
		$returnStr .= "DTEND:".gmdate("Ymd\TH:i:s\Z",
			($this->end)? $this->end[0] : $this->begin[0])."\n";

		if (array_key_exists('summary', $info))
			$returnStr .= "SUMMARY:".$info['summary']."\n";

		if (array_key_exists('description', $info))
			$returnStr .= "DESCRIPTION:".$info['description']."\n";

		if (array_key_exists('location', $info))
			$returnStr .= "LOCATION:".$info['location']."\n";

		$returnStr .= "CLASS:PUBLIC \n";
		$returnStr .= "END:VEVENT \n";

		return $returnStr;
	}

	/**
	 * Parses the String key from the yaml outout to an array of timestamps
	 * of begin and end time.
	 * @param String $timeKey
	 * 		The String of the yaml output containing the event time(s).
	 * @return array
	 * 		Array with begin timestamo at [0] and end timestamp at [1] if available
	 */
	private function parseTime($timeKey) {
		$timeArray = explode(Calendar::$TIME_DELIMITER, $timeKey);
		$timeArray[0] = (int) $this->timestamp($timeArray[0]);
		$timeArray[1] = (@$timeArray[1]) ? (int) $this->timestamp($timeArray[1]) : false;

		return $timeArray;
	}

	//TODO detect time automatically
	/**
	 * Converts a time string (e.g. "10.10.2012") to an unix timestamp.
	 * @param String $timeString
	 * 		The time string.
	 * @return int
	 *		The unix timestamp.
	 */
	private function timestamp($timeString) {
		if ($timeString) {
			return ($this->hasTime)
				? strtotime($timeString)
				: strtotime('+23 hours 59 minutes', strtotime($timeString));
		} else {
			return false;
		}
	}
}
?>
