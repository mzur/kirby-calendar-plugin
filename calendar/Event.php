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
	
	//TODO
	public function getICAL() {
		return false;
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
