/*
Copyright 2008  Alex Willemsma  (email : webmaster@undergroundwebdesigns.com)

	This file is part of Micro Anywhere

    Micro Anywhere is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Mico Anywhere is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Micro Anywhere.  If not, see <http://www.gnu.org/licenses/>.
*/
tinyMCEPopup.requireLangPack();

var ExampleDialog = {
	init : function() {
		var f = document.forms[0];

		// Set the date boxes to the current date, and fill the year box with appropriate years.
		setupDates ();
		
		
		// Get the selected contents as text and place it in the input
		selectedElement = tinyMCEPopup.editor.selection.getNode();
		if (selectedElement != null) var parent = ancestors (selectedElement);
		if (parent != null) 
		{
			getData (parent);
		}
		else
		{
			f.summary.value = tinyMCEPopup.editor.selection.getContent({format : 'text'});
			currTime = new Date();
		}
	},

	insert : function() {
		
		selectedElement = tinyMCEPopup.editor.selection.getNode();
		if (selectedElement != null) var parent = ancestors (selectedElement);
		if (parent != null)
		{
			tinyMCEPopup.editor.dom.setOuterHTML (parent, codeit());
		}
		else
		{
			tinyMCEPopup.editor.execCommand('mceInsertContent', false, codeit());
		}
		tinyMCEPopup.close();
	}
};

tinyMCEPopup.onInit.add(ExampleDialog.init, ExampleDialog);

function setupDates ()
{
	currDate = new Date ();
	
	document.getElementById('startMonth').selectedIndex = currDate.getMonth();
	setDaysOfMonth (document.getElementById('startYear'), document.getElementById('startMonth'), document.getElementById('startDay'));
	document.getElementById('endMonth').selectedIndex = currDate.getMonth();
	setDaysOfMonth (document.getElementById('endYear'), document.getElementById('endMonth'), document.getElementById('endDay'));
	document.getElementById('startDay').selectedIndex = currDate.getDate() - 1;
	document.getElementById('endDay').selectedIndex = currDate.getDate() - 1;
	
	currentYear = currDate.getFullYear();
	var years = "";
	for (var i = currentYear-5; i <= currentYear + 5; i ++)
	{
		if (i == currentYear)
		{
			years += '<option selected="selected">'+i+'</option>';
		}
		else
		{
			years += "<option>"+i+"</option>";
		}
	}
	document.getElementById('startYear').innerHTML = years;
	document.getElementById('endYear').innerHTML = years;
}

function setDaysOfMonth (yearBox, monthBox, daysBox)
{
	var leapYear = checkleapyear (yearBox.value);
	var output = "";
	var currentDay = daysBox.value;
	month = monthBox.selectedIndex
	switch (month)
	{
		case 8:
		case 3:
		case 5:
		case 10:
			for (dayCount = 1; dayCount <= 30; dayCount++)
			{
				if (dayCount == currentDay)
				{
					output += '<option selected="selected">'+dayCount+'</option>';
				}
				else
				{
					output += "<option>"+dayCount+"</option>";
				}
			}
			break;
		case 1:
			if (leapYear)
			{
				for (dayCount = 1; dayCount <= 29; dayCount++)
				{
					if (dayCount == currentDay)
					{
						output += '<option selected="selected">'+dayCount+'</option>';
					}
					else
					{
						output += "<option>"+dayCount+"</option>";
					}
				}
			}
			else
			{
				for (dayCount = 1; dayCount <= 28; dayCount++)
				{
					if (dayCount == currentDay)
					{
						output += '<option selected="selected">'+dayCount+'</option>';
					}
					else
					{
						output += "<option>"+dayCount+"</option>";
					}
				}
			}
			break;
		default:
			for (dayCount = 1; dayCount <= 31; dayCount++)
			{
				if (dayCount == currentDay)
				{
					output += '<option selected="selected">'+dayCount+'</option>';
				}
				else
				{
					output += "<option>"+dayCount+"</option>";
				}
			}
			break;
	}
	daysBox.innerHTML = output;
}

<!-- Script by hscripts.com -->
function checkleapyear(datea)
{
	datea = parseInt(datea);

	if(datea%4 == 0)
	{
		if(datea%100 != 0)
		{
			return true;
		}
		else
		{
			if(datea%400 == 0)
				return true;
			else
				return false;
		}
	}
return false;
}
<!-- Script by hscripts.com -->
	

function ancestors (startNode)
{
	if (parents = tinymce.DOM.getParent(startNode.parentNode, 'div'))
	{
		if (tinymce.DOM.hasClass(parents, 'vevent'))
		{
			return parents;
		}
		else
		{
			ancestors (parents);
		}
	}
	return null;
}

function getData (parent)
{	
	for (var node in parent.childNodes)
	{
		if (parent.childNodes[node].nodeType == 1)
		{
			var type = tinymce.DOM.getAttrib(parent.childNodes[node], 'class');
			switch (type) {
				case "url":
				document.getElementById('url').value = tinymce.DOM.getAttrib(parent.childNodes[node], 'href');
				break;
				case "dtstart":
				var year = tinymce.DOM.getAttrib(parent.childNodes[node], 'title').substr(0, 4);
				var month = tinymce.DOM.getAttrib(parent.childNodes[node], 'title').substr(5, 2);
				month = parseInt (month,10);
				var day = tinymce.DOM.getAttrib(parent.childNodes[node], 'title').substr(8, 2);
				var hour = tinymce.DOM.getAttrib(parent.childNodes[node], 'title').substr(11, 2);
				var minute = tinymce.DOM.getAttrib(parent.childNodes[node], 'title').substr(14, 2);
				var timezone = tinymce.DOM.getAttrib(parent.childNodes[node], 'title').substr(16, 6);
				set_startdate (year, month, day, hour, minute);
				for (var count = 0; count < document.getElementById('timezone').options.length; count ++)
				{
					if (document.getElementById('timezone').options[count].value == timezone)
					{
						document.getElementById('timezone').options[count].selected = true;
						break;
					}
				}
				break;
				case "dtend":
				var year = tinymce.DOM.getAttrib(parent.childNodes[node], 'title').substr(0, 4);
				var month = tinymce.DOM.getAttrib(parent.childNodes[node], 'title').substr(5, 2);
				month = parseInt (month,10);
				var day = tinymce.DOM.getAttrib(parent.childNodes[node], 'title').substr(8, 2);
				day = parseInt (day, 10);
				var hour = tinymce.DOM.getAttrib(parent.childNodes[node], 'title').substr(11, 2);
				var minute = tinymce.DOM.getAttrib(parent.childNodes[node], 'title').substr(14, 2);
				var timezone = tinymce.DOM.getAttrib(parent.childNodes[node], 'title').substr(16, 6);
				
				set_enddate (year, month, day, hour, minute);
				
				break;
				case "summary":
				document.getElementById('summary').value = parent.childNodes[node].firstChild.nodeValue;
				break;
				case "location":
				document.getElementById('location').value = parent.childNodes[node].firstChild.nodeValue;
				break;
				case "description":
				document.getElementById('description').value = parent.childNodes[node].firstChild.nodeValue;
				break;
				case "tags":
				var tagsList = parent.childNodes[node].childNodes;
				for (var tag in tagsList)
				{
					if (tagsList[tag].nodeType == "1")
					{
						if (document.getElementById('tags').value != "") document.getElementById('tags').value += ", ";
						document.getElementById('tags').value += tagsList[tag].firstChild.nodeValue;
					}
				}
				break;
			}
			if (parent.childNodes[node].childNodes.length > 0)
			{
				getData (parent.childNodes[node]);
			}
		}
	}
}

function saneify_inputs() {
  if(parseInt(document.getElementById('startHour').value > 23))   document.getElementById('startHour').value = 23;
  if(parseInt(document.getElementById('startHour').value < 0))   document.getElementById('startHour').value = 0;
  if(parseInt(document.getElementById('endHour').value > 23))     document.getElementById('endHour').value = 23;
  if(parseInt(document.getElementById('endHour').value < 0))    document.getElementById('endHour').value = 0;
  if(parseInt(document.getElementById('startMinute').value > 59)) document.getElementById('startMinute').value = 59;
  if(parseInt(document.getElementById('startMinute').value < 0))    document.getElementById('startMinute').value = 0;
  if(parseInt(document.getElementById('endMinute').value > 59))  document.getElementById('endMinute').value = 59;
  if(parseInt(document.getElementById('endMinute').value < 0))   document.getElementById('endMinute').value = 0;
}

function codeit() {

  saneify_inputs();
  /* get values of text fields */
  var summary        = document.getElementById('summary').value;
  var url            = document.getElementById('url').value;
  
  var startdate      = get_startdate();
  var enddate        = get_enddate();

  var late = late_night()
  
  var startMonthText = document.getElementById('startMonth').options[document.getElementById('startMonth').selectedIndex].text;
  var startDayText   = document.getElementById('startDay').options[document.getElementById('startDay').selectedIndex].text;
  var endDayText     = document.getElementById('endDay').options[document.getElementById('endDay').selectedIndex].text;
  var endMonthText   = document.getElementById('endMonth').options[document.getElementById('endMonth').selectedIndex].text;
  
  var timezone       = document.getElementById('timezone').value;
  var description    = document.getElementById('description').value;
  var tags           = document.getElementById('tags').value;
  
  if(!timezone) {
	timezone = '';
  } else if(timezone > 0) {
	timezone = timezone;
  }
  
  var dtstart = startdate.year.toString() + '-' + pad(startdate.month) + '-' + pad(startdate.day);
  
  if(!isNaN(startdate.hour)) {
	//if there are no minutes given, assume that its the top of the hour
	if(!startdate.minute) startdate.minute = '00';
	dtstart += 'T' + pad(startdate.hour) + ':' + pad(startdate.minute.toString()) + timezone + "00";
  }
  
  if(document.getElementById('startHour').value.length == 0 && (startdate != enddate)){
	//bump to the next day
	var bump = true;
	enddate.day = enddate.day + 1;
  } else {
	var bump = false;
  }
  
  var dtend = pad(enddate.year.toString()) + '-' + pad(enddate.month.toString()) + '-' + pad(enddate.day.toString());

  if(!isNaN(enddate.hour)) {
	//if there are no minutes given, assume that its the top of the hour
	if(!enddate.minute) enddate.minute = '00';
	dtend += 'T' + pad(enddate.hour) + ':' + pad(enddate.minute.toString()) + timezone + "00";
  }
  
  var startOut = startMonthText + ' ' + startDayText;
  if ((startdate.day != enddate.day) || (startdate.month != enddate.month)) {
	switch (startdate.day){
	  case 1:
	  case 21:
		startOut += 'st';
		break;
	  case 2:
	  case 22:
		startOut += 'nd';
		break;
	  case 3:
	  case 23:
		startOut += 'rd';
		break;
	  default:
		startOut += 'th';
		break;
	}
  }
  
  if(startdate.year != enddate.year) {
	startOut += ', ' + startdate.year;
  }
  
  var endOut = '';
  if(!late) {
	if(startdate.month != enddate.month || startdate.year != enddate.year) {
	  endOut += endMonthText + ' ';
	}

	if(!(startdate.month == enddate.month && startdate.year == enddate.year && startdate.day == enddate.day)) {
	  if ((startdate.day != enddate.day) && (startdate.month != enddate.month)){
		endOut += enddate.day;
		switch (enddate.day){
		  case 1:
		  case 21:
			endOut += 'st';
			break;
		  case 2:
		  case 22:
			endOut += 'nd';
			break;
		  case 3:
		  case 23:
			endOut += 'rd';
			break;
		  default:
			endOut += 'th';
			break;
		}
	  } else if(!(startdate.day == (parseInt(enddate.day) - 1))) {
		if (!bump)
		{
			endOut += parseInt(enddate.day);
		}
		else
		{
			endOut += parseInt(enddate.day-1);
		}
		if ((startdate.day != enddate.day) || (startdate.month != enddate.month)){
		  switch (enddate.day){
			case 1:
			case 21:
			  endOut += 'st';
			  break;
			case 2:
			case 22:
			  endOut += 'nd';
			  break;
			case 3:
			case 23:
			  endOut += 'rd';
			  break;
			default:
			  endOut += 'th';
			  break;
		  }
		}
	  }
	} else {
	  startOut += ', ' + startdate.year;
	}
  }

  if(startdate.hour && startdate.minute) {
	startOut += ' ';
	if (startdate.hour > 12){
	  startOut += ' ' + startdate.hour - 12;
	  var meridiem = 'pm';
	} else if (startdate.hour == 12){
	  startOut += ' ' + startdate.hour;
	  var meridiem = 'pm';
	} else {
	  startOut += ' ' + startdate.hour;
	  var meridiem = 'am';
	}
	if (!(startdate.minute == '00')){
	  startOut += ':' + pad(startdate.minute);
	}

	if(endOut) {
	  var collapse = true;
	}

	if (!enddate.hour || startdate.day != enddate.day || startdate.month != enddate.month){
	  startOut += meridiem;
	}
  }

  if(enddate.hour && enddate.minute) {
	if((!(startdate.day == enddate.day)) || (!(startdate.month == enddate.month))) {
	  endOut += ' '
	}

	if(enddate.hour > 12){
	  endOut += enddate.hour - 12;
	  var meridiem = 'pm';
	} else if (enddate.hour == 12){
	  endOut += enddate.hour;
	  var meridiem = 'pm';
	} else {
	  endOut += enddate.hour;
	  var meridiem = 'am';
	}
  
	if(!(enddate.minute == '00')){
	  endOut += ':' + pad(enddate.minute);
	}
	endOut += meridiem;
  }

  if(!(startdate.month == enddate.month && startdate.year == enddate.year && startdate.day == enddate.day)) {
	if(!(startdate.day == (parseInt(enddate.day) - 1)) || (!(startdate.month == enddate.month))){
	  endOut += ', ' + enddate.year;
	} else {
	  endOut += ' ' + enddate.year; 
	}
  }

  var location = document.getElementById('location').value;

  /* set results field */
  var result_div = DIV(
	{'class' : 'vevent', 'id' : (summary != '' ? 'hcalendar-' + summary.replace(/ /g, '-') : '')}
  );

  if(url && url.match(/http:\/\/.*\..{2,4}.*/)) {
	content_node = A({'class' : 'url', 'href' : url});
	appendChildNodes(result_div, content_node);
  } else {
	var content_node = result_div;
  }

  appendChildNodes(
	content_node, ABBR({'class' : 'dtstart', 'title' : dtstart}, startOut)
  );

  if(startdate.day != enddate.day ||
	((startdate.day == enddate.day) && !(startdate.hour + startdate.minute == enddate.hour + enddate.minute)) ) {
	//TODO: wtf?
	if((startdate.day != (parseInt(enddate.day) - 1)) || (startdate.month != enddate.month)){
	  if((startdate.day == enddate.day) && (startdate.month == enddate.month)) {
		appendChildNodes(content_node, ' - ');
	  } else {
		appendChildNodes(content_node, ' : ');
	  }
	} else {
	  appendChildNodes(content_node, ', ');
	}
  }

  if(!((startdate.year + startdate.month + startdate.day == enddate.year + enddate.month + enddate.day) && !enddate.hour)) {
	appendChildNodes(
	  content_node, ABBR({'class' : 'dtend', 'title' : dtend}, endOut)
	);
  }

  if(enddate.hour && enddate.minute) appendChildNodes(content_node, ' : ');
  
  appendChildNodes(
	content_node, ' ', ''
  );
  
  appendChildNodes(
	content_node, SPAN({'class' : 'summary'}, summary)
  );
  
  if(location) appendChildNodes(
	content_node, ' at ', SPAN({'class' : 'location'}, location)
  );

  if(description) appendChildNodes(
	result_div, DIV({'class' : 'description'}, description)
  );

  if(tags) {
	  var tag_list = tags.split(",");
	  var tags_div = DIV({'class' : 'tags'}, 'Tags: ');
	  for (var i in tag_list) {
		appendChildNodes(
		  tags_div,
		  A({'rel': 'tag', 'href' : 'http://eventful.com/events/tags/' + encodeURI(tag_list[i])}, tag_list[i])
		)
	  }
	  appendChildNodes(result_div, tags_div);
  }

  var wrap = DIV({}, result_div)
  result = wrap.innerHTML;
  return result;
}

function get_startdate() {
  var startdate    = new Object;
  startdate.year   = parseInt(document.getElementById('startYear').value, 10);
  startdate.month  = parseInt(document.getElementById('startMonth').selectedIndex, 10);
  startdate.month++;
  startdate.day    = parseInt(document.getElementById('startDay').value, 10);
  startdate.hour   = parseInt(document.getElementById('startHour').value, 10);
  startdate.minute = parseInt(document.getElementById('startMinute').value, 10);

  if(startdate.hour && !startdate.minute) {
	startdate.minute = '00';
  }
  return startdate;
}

function get_enddate(){
  var enddate = new Object;
  enddate.year =    parseInt(document.getElementById('endYear').value, 10);
  enddate.month =   parseInt(document.getElementById('endMonth').selectedIndex, 10);
  enddate.month++;
  enddate.day =     parseInt(document.getElementById('endDay').value, 10);
  enddate.hour =    parseInt(document.getElementById('endHour').value, 10);
  enddate.minute =  parseInt(document.getElementById('endMinute').value, 10);

  return enddate;
}

function update_endtime() {
  var enddate = get_enddate();
  var startdate = get_startdate();

  if(enddate.hour && startdate.hour &&
	startdate.year == enddate.year && 
	startdate.month == enddate.month &&
	startdate.day == enddate.day) {

	var startTime = startdate.hour + startdate.minute;
	var endTime = enddate.hour + enddate.minute;

	//just use pad()?
	if(startTime.length == 3) startTime = '0' + startTime;
	if(endTime.length == 3)   endTime   = '0' + endTime;

	if(endTime < startTime){
	  increment_end_date();
	}
  }
}

function increment_end_date() {

  var enddate = get_enddate();

  var d = new Date(enddate.year, parseInt(enddate.month) - 1, parseInt(enddate.day));

  d.setDate(++enddate.day);

  set_enddate(d.getFullYear(), d.getMonth() + 1, d.getDate());
}

function late_night() {
  //a tantek feature: if the enddate is in the late night / early morning, roll over to the next day

  //convert to date objects
  var enddate = get_enddate();
  if(parseInt(enddate.hour) < 6) {
	var endDate = new Date(enddate.year, document.getElementById('endMonth').selectedIndex, parseInt(enddate.day));

	var startDate = new Date(parseInt(document.getElementById('startYear').value), parseInt(document.getElementById('startMonth').selectedIndex), parseInt(document.getElementById('startDay').value));
	//increment and test

	startDate.setDate(startDate.getDate() + 1);

	if(startDate.getYear() == endDate.getYear() && startDate.getMonth() == endDate.getMonth() && startDate.getDay() == endDate.getDay()) {
	  return true;
	}
  }
  return false;
}

function escape_output(input){
  //this is not the most robust solution, but it should cover most cases
  var amp = /\s&\s/gi;
  var lt = /\s\<\s/gi;
  var gt = /\s>\s/gi;

  var temp = input.replace(amp,' &amp; ');
  temp = temp.replace(lt,' &lt; ');
  var output = temp.replace(gt,' &gt; ');
  return output;
}

function set_startdate(year, month, day, hour, minute) {
  if(year)   document.getElementById('startYear').value = year;
  if(month)  document.getElementById('startMonth').selectedIndex = pad(month -1);
  if(day)    document.getElementById('startDay').selectedIndex = pad(day -1);
  if(hour)   document.getElementById('startHour').value = pad(hour);
  if(minute) document.getElementById('startMinute').value = pad(minute);
}

function set_enddate(year, month, day, hour, minute) {
  if (year)   document.getElementById('endYear').value = year;
  if (month)  document.getElementById('endMonth').selectedIndex = pad(month - 1);
  if (day)    document.getElementById('endDay').selectedIndex = pad(day - 1);
  if (hour)   document.getElementById('endHour').value = pad(hour);
  if (minute) document.getElementById('endMinute').value = pad(minute);
}

function doreset() {
  var d = new Date();

  var month = d.getMonth();
  var day = d.getDate();
  set_startdate(d.getFullYear(), month, day);
  set_enddate(d.getFullYear(), month, day);

  var timezone = d.getTimezoneOffset();
  timezone = -timezone / 60;
  timezone = timezone + ":00";
  if(timezone.length == 5)
	timezone = timezone.charAt(0) + "0" + timezone.substring(1);

  if(parseInt(timezone) > 0) {
	timezone = "+" + timezone;
  }

  document.getElementById('timezone').value = timezone;
  codeit();
}

function pad(input) {
  if(input.toString().length < 2) {
	input = '0' + input.toString();
  }
  return input;
}

function activate_time(){
  if(document.getElementById('startHour').value.length > 0){
	//enable endHour and endMinute
	document.getElementById('endHour').disabled = '';
	document.getElementById('endMinute').disabled = '';
  } else {
	//disable
	document.getElementById('endHour').disabled = 'disabled';
	document.getElementById('endMinute').disabled = 'disabled';
  }
}