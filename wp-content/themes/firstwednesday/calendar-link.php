<?php
	// function to get querystring variables
	function getquerystring ($key, $default = false) {
		return (isset($_GET[$key]) && $_GET[$key] !== '') ? $_GET[$key] : $default;
	}
	function createcalendarlink ($id, $type) {
		// fetch post info
		
		// process into calendar variables
		$title = "First Wednesday - May 4, 2011";
		$desc = "Lucy's Cantina Royale; 1 Penn Plaza; Between 7th & 8th Avenues; New York, NY 10119; (212) 643-1270; http://www.lucyscantinaroyale.com";
		$location = "Lucy's Cantina Royale; 1 Penn Plaza; New York, NY 10119";
		$start = "20110504T220000Z";
		$end = "20110505T020000Z";
		$url = "http://aarontgrogg.com/firstwednesday/may-4-2011/";
		/// build vevent
		switch ($type) {
			case 'google':
				/*
				 	https://calendar.google.com/googlecalendar/images/favicon_v2010_2.ico (_2 represents day of month, could change to reflect date for FW...)
				*/
				$link = "http://www.google.com/calendar/event?action=TEMPLATE&trp=false"
					. "&text=".urlencode($title)
					. "&dates=".$start."/".$end
					. "&details=".urlencode(str_replace("; ", "\n", $desc."\n\n".$url))
					. "&location=".urlencode(str_replace("; ", ", ", $location))
					. "&sprop=".urlencode($url)
					. "&sprop=name:First%20Wednesday";
				//echo $link;
				header("Location: " . $link);
				exit();
			case 'yahoo':
				/*
					http://calendar.yahoo.com/favicon.ico
				*/
				$link = "http://calendar.yahoo.com/"
					. "?TITLE=".urlencode($title)
					. "&DESC=".urlencode(str_replace("; ", "\n", $desc."\n\n".$url))
					. "&in_loc=".urlencode(str_replace("; ", ", ", $location))
					. "&ST=".$start."%2B0000"
					. "&in_st=".urlencode($street)
					. "&DUR=360"
					. "&TYPE=20&VIEW=d&v=60";
				header("Location: " . $link);
				exit();
			default: // outlook, iCal, etc.
				header("Content-Type: text/Calendar");
				header("Content-Disposition: inline;");
				echo "BEGIN:VCALENDAR\n";
				echo "VERSION:2.0\n";
				echo "METHOD:PUBLISH\n";
				echo "BEGIN:VEVENT\n";
				echo "UID:first-wednesday\n";
				echo "ORGANIZER;CN=First Wednesday:MAILTO:firstwednesday@aarontgrogg.com\n";
				echo "DTSTAMP:20110101T000000Z\n";
				echo "DTSTART:".$start."\n";
				echo "DTEND:".$end."\n";
				echo "SUMMARY;LANGUAGE=en-us:".$title."\n";
				echo "DESCRIPTION:".str_replace("; ", "\\\n", $desc)."\n";
				echo "LOCATION:".str_replace("; ", ", ", $location)."\n";
				echo "URL:".$url."\n";
				echo "END:VEVENT\n";
				echo "END:VCALENDAR\n";
				break;
			/*
			
			*/
		}
	}
	// get querystring params
	$id = getquerystring('id');
	$type = getquerystring('type');
	createcalendarlink($id, $type);
?>



