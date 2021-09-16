<?php
	
	// function to get querystring variables
	function getquerystring ($key, $default = false) {
		return (isset($_GET[$key]) && $_GET[$key] !== '') ? $_GET[$key] : $default;
	}
	
	// process into calendar variables
	$start = getquerystring('start');
	$end = getquerystring('end');
	$title = getquerystring('title');
	$desc = str_replace("|", "\\n", urldecode(getquerystring('desc')));
	$location = getquerystring('location');
	$url = getquerystring('url');
	
	// build vevent
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
	echo "DESCRIPTION:".$desc."\n";
	echo "LOCATION:".$location."\n";
	echo "URL:".$url."\n";
	echo "END:VEVENT\n";
	echo "END:VCALENDAR\n";
?>