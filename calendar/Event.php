<?php

class Event {
	private $begin = false;
	private $end = false;
	private $info = false;

	private $hasTime = false;

	function __construct($eventKey, $eventInfo=array(), $hasTime=false) {
		if (!$eventKey) return false;

		$this->hasTime = $hasTime;
		if (!$this->hasTime) $this->hasTime = Calendar::$HAS_TIME;

		$time = $this->parseTime($eventKey);
		$this->begin = getdate($time[0]);
		$this->end = ($time[1]) ? getdate($time[1]) : false;

		$this->info = $eventInfo;
	}

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
			$returnStr .= "SUMMARY:".$info['summary'];

		if (array_key_exists('description', $info))
			$returnStr .= "DESCRIPTION:".$info['description'];

		if (array_key_exists('location', $info))
			$returnStr .= "LOCATION:".$info['location'];

		$returnStr .= "CLASS:PUBLIC \n";
		$returnStr .= "END:VEVENT \n";

		return $returnStr;
	}

	private function parseTime($timeKey) {
		$timeArray = explode(Calendar::$TIME_DELIMITER, $timeKey);
		$timeArray[0] = (int) $this->timestamp($timeArray[0]);
		$timeArray[1] = (@$timeArray[1]) ? (int) $this->timestamp($timeArray[1]) : false;

		return $timeArray;
	}

	//TODO detect time automatically
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
