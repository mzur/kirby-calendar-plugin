<?php

function calendar($events, $options=array(), $template='table') {
		$calendar = new Calendar($events, $options);
		echo (empty($events)) ? $calendar->noEntry() : $calendar->cal($template);
}

class Calendar {
	
	/** default formats. see the doc of strftime() for the formatting syntax. */
	const DATE_FORMAT = '%d-%m-%Y';
	const MONTH_FORMAT = '%B';
	
	/** delimiter that divides start from end time. e.g. 01.01.70->02.01.70. */
	const TIME_DELIMITER = '->';
	/** 
	 * delimiter that marks the number of the event, if there are multiple
	 * events at the same time.
	 */
	const EVENT_NO_DELIMITER = '#';
	/** default message, when no entry is available. */
	const NO_ENTRY_MSG = 'No entry.';

    /** the array of events. */
	var $events = array();
	/** the keys of all event arrays. e.g. the columns in the table layout. */
	var $columns = array();
	var $status = array();
	/** 
	 * language for the locale settings. must be RFC 1766 or ISO 639 valid.
	 * e.g. en_US or de_DE.
	 */
	var $lang = false;
	/**
	 * timezone for decoding the input date-string. see http://php.net/manual/en/timezones.php
	 * for supported timezones. default is the server's timezone.
	 */
	var $timezone = false;
	/** format of the date (start and end) displayed for every event. */
	var $dateFormat = false;
	/** format of the month, which divides the events. */
	var $monthFormat = false;
	/**
	 * flag for input with only dates and no time. if 'false' the time will be
	 * set +23:59
	 */	
	var $hasTime = true;
	/** message, when no entry is available. */
	var $noEntryMsg = false;
	
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
		foreach ($this->events as $timeKey => $event) {
			$this->columns = array_merge($this->columns, array_diff(array_keys($event), $this->columns));
		}
	}
	
	//TODO errormessages
	function configure() {
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
	
	/** converts the input date to a timestamp an sorts from low to high. */
	function parseEvents($events) {		
		foreach ($events as $time => $event) {
			unset($events[$time]);
			do {
				$timeKey = $this->getTimeKey($time);
			} while (array_key_exists($timeKey, $events));
			
        	$events[$timeKey] = $event;
        }
        ksort($events);
        
        return $events;
	}
	
	//TODO multiple events at the same time. NOT POSSIBLE BECAUSE OF YAML INPUT.
	function getTimeKey($time) {
		$timesArray = explode(Calendar::TIME_DELIMITER, $time);
		$timeKey = $this->timestamp($timesArray[0]).
			Calendar::TIME_DELIMITER.$this->timestamp(@$timesArray[1]);
			
		return $timeKey.Calendar::EVENT_NO_DELIMITER.$this->getEventNumber($timeKey);
	}
	
	function getEventNumber($timeKey) {
		$tmp = explode(Calendar::EVENT_NO_DELIMITER, $timeKey);
		return ((int) @$tmp[1])+1;
	}
	
	function getTimeArray($timeKey) {
		$timeArray = explode(Calendar::TIME_DELIMITER, $timeKey);
		$begin = (int) $timeArray[0];
		$endArray = explode(Calendar::EVENT_NO_DELIMITER, @$timeArray[1]);
		$end = @$endArray[0];
		
		return array(
			'begin' => $begin,
			'end'	=> $end
			);
	}
	
	//TODO detect time automatically
	function timestamp($time) {
		if ($time) {
			return ($this->hasTime) 
				? strtotime($time)
				: strtotime('+23 hours 59 minutes', strtotime($time));
		} else {
			return '';
		}
	}
	
	function noEntry() {
		echo $this->noEntryMsg;
	}
	
	//TODO allow custom templates
	function cal($template) {
		if ($template === 'table') {
			return $this->table();
		} else if ($template === 'div') {
			return $this->div();
		} else {
			return 'template not supported';
		}
	}
	
	function table() {
		$output = false;
		$month = false;
		
		$output .= "<table class=\"calendar\">\n";
		
		$output .= "\t<tr>\n\t\t<th></th>\n";
		foreach ($this->columns as $column) {
		    $output .= "\t\t<th>".$column."</th>\n";
		}
		$output .= "\t</tr>\n\t<tbody>\n";
		
		$currentTime = getdate(strtotime('now'));
		
		foreach ($this->events as $timeKey => $event) {
			$date = $this->getTimeArray($timeKey);
			
			$begin = getdate($date['begin']);
			$end = ($date['end']) ? getdate($date['end']) : false;
			
			$tempMonth = strftime($this->monthFormat, $begin[0]);
			
			$monthIsPast = ($begin[0] < $currentTime[0] 
							&& $begin['month'] != $currentTime['month'])
								? true
								: false;

			if ($month != $tempMonth) {
				$month = $tempMonth;
				//columns+1 colspan for the date column
				$output .= "\t\t<tr class=\"month";
				$output .= ($monthIsPast) ? " past" : "";
				$output .= "\">\n\t\t\t<td colspan=\"".
					(count($this->columns)+1)."\">".$month."</td>\n\t\t</tr>\n";
			}
			
			$output .= "\t\t<tr";
			$output .= 
				(($end) ? time() > $end[0] : time() > $begin[0])
					? " class=\"past\">\n"
					: ">\n";
			
			$output .= "\t\t\t<td class=\"date\">".strftime($this->dateFormat, $begin[0]);
			$output .= ($end) ? ' - '.strftime($this->dateFormat, $end[0]) : '';
			$output .= "</td>\n";
			
			foreach ($this->columns as $column) {
				$entry = (array_key_exists($column, $event)) ? $event[$column] : '';
				$output .= "\t\t\t<td>".$entry."</td>\n";
			}
			
			$output .= "\t\t</tr>\n";
		}
		
		$output .= "\t</tbody>\n</table>";
		
		return $output;
	}
	
	function div() {
		$output = false;
		$month = false;
		
		$output .= "<div class=\"calendar\">\n";
		
		$output .= "\t<div class=\"head\">\n";
		foreach ($this->columns as $column) {
		    $output .= "\t\t<div>".$column."</div>\n";
		}
		$output .= "\t</div>\n";
		
		$currentTime = getdate(strtotime('now'));
		
		foreach ($this->events as $timeKey => $event) {
			$date = $this->getTimeArray($timeKey);
			
			$begin = getdate($date['begin']);
			$end = ($date['end']) ? getdate($date['end']) : false;
			
			$tempMonth = strftime($this->monthFormat, $begin[0]);
			
			$monthIsPast = ($begin[0] < $currentTime[0] 
							&& $begin['month'] != $currentTime['month'])
								? true
								: false;

			if ($month != $tempMonth) {
				$month = $tempMonth;
				//columns+1 colspan for the date column
				$output .= "\t<div class=\"month";
				$output .= ($monthIsPast) ? " past" : "";
				$output .= "\">".$month."</div>\n";
			}
			
			$output .= "\t<div";
			$output .= 
				(($end) ? time() > $end[0] : time() > $begin[0])
					? " class=\"past_event\">\n"
					: " class=\"event\">\n";
			
			$output .= "\t\t<time>".strftime($this->dateFormat, $begin[0]);
			$output .= ($end) ? ' - '.strftime($this->dateFormat, $end[0]) : '';
			$output .= "</time>\n";
			
			foreach ($this->columns as $column) {
				$entry = (array_key_exists($column, $event)) ? $event[$column] : '';
				$output .= "\t\t<div class=\"entry\">".$entry."</div>\n";
			}
			
			$output .= "\t</div>\n";
		}
		
		$output .= "</div>";
		
		return $output;
	}
}

?>
