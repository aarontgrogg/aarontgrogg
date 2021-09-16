function setupDates(){currDate=new Date,document.getElementById("startMonth").selectedIndex=currDate.getMonth(),setDaysOfMonth(document.getElementById("startYear"),document.getElementById("startMonth"),document.getElementById("startDay")),document.getElementById("endMonth").selectedIndex=currDate.getMonth(),setDaysOfMonth(document.getElementById("endYear"),document.getElementById("endMonth"),document.getElementById("endDay")),document.getElementById("startDay").selectedIndex=currDate.getDate()-1,document.getElementById("endDay").selectedIndex=currDate.getDate()-1,currentYear=currDate.getFullYear();for(var e="",t=currentYear-5;t<=currentYear+5;t++)e+=t==currentYear?'<option selected="selected">'+t+"</option>":"<option>"+t+"</option>";document.getElementById("startYear").innerHTML=e,document.getElementById("endYear").innerHTML=e}function setDaysOfMonth(e,t,n){var d=checkleapyear(e.value),a="",o=n.value;switch(month=t.selectedIndex,month){case 8:case 3:case 5:case 10:for(dayCount=1;dayCount<=30;dayCount++)a+=dayCount==o?'<option selected="selected">'+dayCount+"</option>":"<option>"+dayCount+"</option>";break;case 1:if(d)for(dayCount=1;dayCount<=29;dayCount++)a+=dayCount==o?'<option selected="selected">'+dayCount+"</option>":"<option>"+dayCount+"</option>";else for(dayCount=1;dayCount<=28;dayCount++)a+=dayCount==o?'<option selected="selected">'+dayCount+"</option>":"<option>"+dayCount+"</option>";break;default:for(dayCount=1;dayCount<=31;dayCount++)a+=dayCount==o?'<option selected="selected">'+dayCount+"</option>":"<option>"+dayCount+"</option>"}n.innerHTML=a}function checkleapyear(e){return e=parseInt(e),e%4==0?e%100!=0?!0:e%400==0?!0:!1:!1}function ancestors(e){if(parents=tinymce.DOM.getParent(e.parentNode,"div")){if(tinymce.DOM.hasClass(parents,"vevent"))return parents;ancestors(parents)}return null}function getData(e){for(var t in e.childNodes)if(1==e.childNodes[t].nodeType){var n=tinymce.DOM.getAttrib(e.childNodes[t],"class");switch(n){case"url":document.getElementById("url").value=tinymce.DOM.getAttrib(e.childNodes[t],"href");break;case"dtstart":var d=tinymce.DOM.getAttrib(e.childNodes[t],"title").substr(0,4),a=tinymce.DOM.getAttrib(e.childNodes[t],"title").substr(5,2);a=parseInt(a,10);var o=tinymce.DOM.getAttrib(e.childNodes[t],"title").substr(8,2),r=tinymce.DOM.getAttrib(e.childNodes[t],"title").substr(11,2),u=tinymce.DOM.getAttrib(e.childNodes[t],"title").substr(14,2),s=tinymce.DOM.getAttrib(e.childNodes[t],"title").substr(16,6);set_startdate(d,a,o,r,u);for(var l=0;l<document.getElementById("timezone").options.length;l++)if(document.getElementById("timezone").options[l].value==s){document.getElementById("timezone").options[l].selected=!0;break}break;case"dtend":var d=tinymce.DOM.getAttrib(e.childNodes[t],"title").substr(0,4),a=tinymce.DOM.getAttrib(e.childNodes[t],"title").substr(5,2);a=parseInt(a,10);var o=tinymce.DOM.getAttrib(e.childNodes[t],"title").substr(8,2);o=parseInt(o,10);var r=tinymce.DOM.getAttrib(e.childNodes[t],"title").substr(11,2),u=tinymce.DOM.getAttrib(e.childNodes[t],"title").substr(14,2),s=tinymce.DOM.getAttrib(e.childNodes[t],"title").substr(16,6);set_enddate(d,a,o,r,u);break;case"summary":document.getElementById("summary").value=e.childNodes[t].firstChild.nodeValue;break;case"location":document.getElementById("location").value=e.childNodes[t].firstChild.nodeValue;break;case"description":document.getElementById("description").value=e.childNodes[t].firstChild.nodeValue;break;case"tags":var i=e.childNodes[t].childNodes;for(var m in i)"1"==i[m].nodeType&&(""!=document.getElementById("tags").value&&(document.getElementById("tags").value+=", "),document.getElementById("tags").value+=i[m].firstChild.nodeValue)}e.childNodes[t].childNodes.length>0&&getData(e.childNodes[t])}}function saneify_inputs(){parseInt(document.getElementById("startHour").value>23)&&(document.getElementById("startHour").value=23),parseInt(document.getElementById("startHour").value<0)&&(document.getElementById("startHour").value=0),parseInt(document.getElementById("endHour").value>23)&&(document.getElementById("endHour").value=23),parseInt(document.getElementById("endHour").value<0)&&(document.getElementById("endHour").value=0),parseInt(document.getElementById("startMinute").value>59)&&(document.getElementById("startMinute").value=59),parseInt(document.getElementById("startMinute").value<0)&&(document.getElementById("startMinute").value=0),parseInt(document.getElementById("endMinute").value>59)&&(document.getElementById("endMinute").value=59),parseInt(document.getElementById("endMinute").value<0)&&(document.getElementById("endMinute").value=0)}function codeit(){saneify_inputs();var e=document.getElementById("summary").value,t=document.getElementById("url").value,n=get_startdate(),d=get_enddate(),a=late_night(),o=document.getElementById("startMonth").options[document.getElementById("startMonth").selectedIndex].text,r=document.getElementById("startDay").options[document.getElementById("startDay").selectedIndex].text,u=(document.getElementById("endDay").options[document.getElementById("endDay").selectedIndex].text,document.getElementById("endMonth").options[document.getElementById("endMonth").selectedIndex].text),s=document.getElementById("timezone").value,l=document.getElementById("description").value,i=document.getElementById("tags").value;s?s>0&&(s=s):s="";var m=n.year.toString()+"-"+pad(n.month)+"-"+pad(n.day);if(isNaN(n.hour)||(n.minute||(n.minute="00"),m+="T"+pad(n.hour)+":"+pad(n.minute.toString())+s+"00"),0==document.getElementById("startHour").value.length&&n!=d){var c=!0;d.day=d.day+1}else var c=!1;var y=pad(d.year.toString())+"-"+pad(d.month.toString())+"-"+pad(d.day.toString());isNaN(d.hour)||(d.minute||(d.minute="00"),y+="T"+pad(d.hour)+":"+pad(d.minute.toString())+s+"00");var g=o+" "+r;if(n.day!=d.day||n.month!=d.month)switch(n.day){case 1:case 21:g+="st";break;case 2:case 22:g+="nd";break;case 3:case 23:g+="rd";break;default:g+="th"}n.year!=d.year&&(g+=", "+n.year);var p="";if(!a)if((n.month!=d.month||n.year!=d.year)&&(p+=u+" "),n.month!=d.month||n.year!=d.year||n.day!=d.day){if(n.day!=d.day&&n.month!=d.month)switch(p+=d.day,d.day){case 1:case 21:p+="st";break;case 2:case 22:p+="nd";break;case 3:case 23:p+="rd";break;default:p+="th"}else if(n.day!=parseInt(d.day)-1&&(p+=c?parseInt(d.day-1):parseInt(d.day),n.day!=d.day||n.month!=d.month))switch(d.day){case 1:case 21:p+="st";break;case 2:case 22:p+="nd";break;case 3:case 23:p+="rd";break;default:p+="th"}}else g+=", "+n.year;if(n.hour&&n.minute){if(g+=" ",n.hour>12){g+=" "+n.hour-12;var h="pm"}else if(12==n.hour){g+=" "+n.hour;var h="pm"}else{g+=" "+n.hour;var h="am"}"00"!=n.minute&&(g+=":"+pad(n.minute)),d.hour&&n.day==d.day&&n.month==d.month||(g+=h)}if(d.hour&&d.minute){if((n.day!=d.day||n.month!=d.month)&&(p+=" "),d.hour>12){p+=d.hour-12;var h="pm"}else if(12==d.hour){p+=d.hour;var h="pm"}else{p+=d.hour;var h="am"}"00"!=d.minute&&(p+=":"+pad(d.minute)),p+=h}(n.month!=d.month||n.year!=d.year||n.day!=d.day)&&(p+=n.day!=parseInt(d.day)-1||n.month!=d.month?", "+d.year:" "+d.year);var I=document.getElementById("location").value,v=DIV({"class":"vevent",id:""!=e?"hcalendar-"+e.replace(/ /g,"-"):""});if(t&&t.match(/http:\/\/.*\..{2,4}.*/))E=A({"class":"url",href:t}),appendChildNodes(v,E);else var E=v;if(appendChildNodes(E,ABBR({"class":"dtstart",title:m},g)),(n.day!=d.day||n.day==d.day&&n.hour+n.minute!=d.hour+d.minute)&&(n.day!=parseInt(d.day)-1||n.month!=d.month?n.day==d.day&&n.month==d.month?appendChildNodes(E," - "):appendChildNodes(E," : "):appendChildNodes(E,", ")),(n.year+n.month+n.day!=d.year+d.month+d.day||d.hour)&&appendChildNodes(E,ABBR({"class":"dtend",title:y},p)),d.hour&&d.minute&&appendChildNodes(E," : "),appendChildNodes(E," ",""),appendChildNodes(E,SPAN({"class":"summary"},e)),I&&appendChildNodes(E," at ",SPAN({"class":"location"},I)),l&&appendChildNodes(v,DIV({"class":"description"},l)),i){var B=i.split(","),f=DIV({"class":"tags"},"Tags: ");for(var M in B)appendChildNodes(f,A({rel:"tag",href:"http://eventful.com/events/tags/"+encodeURI(B[M])},B[M]));appendChildNodes(v,f)}var D=DIV({},v);return result=D.innerHTML,result}function get_startdate(){var e=new Object;return e.year=parseInt(document.getElementById("startYear").value,10),e.month=parseInt(document.getElementById("startMonth").selectedIndex,10),e.month++,e.day=parseInt(document.getElementById("startDay").value,10),e.hour=parseInt(document.getElementById("startHour").value,10),e.minute=parseInt(document.getElementById("startMinute").value,10),e.hour&&!e.minute&&(e.minute="00"),e}function get_enddate(){var e=new Object;return e.year=parseInt(document.getElementById("endYear").value,10),e.month=parseInt(document.getElementById("endMonth").selectedIndex,10),e.month++,e.day=parseInt(document.getElementById("endDay").value,10),e.hour=parseInt(document.getElementById("endHour").value,10),e.minute=parseInt(document.getElementById("endMinute").value,10),e}function update_endtime(){var e=get_enddate(),t=get_startdate();if(e.hour&&t.hour&&t.year==e.year&&t.month==e.month&&t.day==e.day){var n=t.hour+t.minute,d=e.hour+e.minute;3==n.length&&(n="0"+n),3==d.length&&(d="0"+d),n>d&&increment_end_date()}}function increment_end_date(){var e=get_enddate(),t=new Date(e.year,parseInt(e.month)-1,parseInt(e.day));t.setDate(++e.day),set_enddate(t.getFullYear(),t.getMonth()+1,t.getDate())}function late_night(){var e=get_enddate();if(parseInt(e.hour)<6){var t=new Date(e.year,document.getElementById("endMonth").selectedIndex,parseInt(e.day)),n=new Date(parseInt(document.getElementById("startYear").value),parseInt(document.getElementById("startMonth").selectedIndex),parseInt(document.getElementById("startDay").value));if(n.setDate(n.getDate()+1),n.getYear()==t.getYear()&&n.getMonth()==t.getMonth()&&n.getDay()==t.getDay())return!0}return!1}function escape_output(e){var t=/\s&\s/gi,n=/\s\<\s/gi,d=/\s>\s/gi,a=e.replace(t," &amp; ");a=a.replace(n," &lt; ");var o=a.replace(d," &gt; ");return o}function set_startdate(e,t,n,d,a){e&&(document.getElementById("startYear").value=e),t&&(document.getElementById("startMonth").selectedIndex=pad(t-1)),n&&(document.getElementById("startDay").selectedIndex=pad(n-1)),d&&(document.getElementById("startHour").value=pad(d)),a&&(document.getElementById("startMinute").value=pad(a))}function set_enddate(e,t,n,d,a){e&&(document.getElementById("endYear").value=e),t&&(document.getElementById("endMonth").selectedIndex=pad(t-1)),n&&(document.getElementById("endDay").selectedIndex=pad(n-1)),d&&(document.getElementById("endHour").value=pad(d)),a&&(document.getElementById("endMinute").value=pad(a))}function doreset(){var e=new Date,t=e.getMonth(),n=e.getDate();set_startdate(e.getFullYear(),t,n),set_enddate(e.getFullYear(),t,n);var d=e.getTimezoneOffset();d=-d/60,d+=":00",5==d.length&&(d=d.charAt(0)+"0"+d.substring(1)),parseInt(d)>0&&(d="+"+d),document.getElementById("timezone").value=d,codeit()}function pad(e){return e.toString().length<2&&(e="0"+e.toString()),e}function activate_time(){document.getElementById("startHour").value.length>0?(document.getElementById("endHour").disabled="",document.getElementById("endMinute").disabled=""):(document.getElementById("endHour").disabled="disabled",document.getElementById("endMinute").disabled="disabled")}tinyMCEPopup.requireLangPack();var ExampleDialog={init:function(){var e=document.forms[0];if(setupDates(),selectedElement=tinyMCEPopup.editor.selection.getNode(),null!=selectedElement)var t=ancestors(selectedElement);null!=t?getData(t):(e.summary.value=tinyMCEPopup.editor.selection.getContent({format:"text"}),currTime=new Date)},insert:function(){if(selectedElement=tinyMCEPopup.editor.selection.getNode(),null!=selectedElement)var e=ancestors(selectedElement);null!=e?tinyMCEPopup.editor.dom.setOuterHTML(e,codeit()):tinyMCEPopup.editor.execCommand("mceInsertContent",!1,codeit()),tinyMCEPopup.close()}};tinyMCEPopup.onInit.add(ExampleDialog.init,ExampleDialog);