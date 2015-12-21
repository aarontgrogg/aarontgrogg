/*
 * Copyright (c) 2013 Netbiscuits GmbH
 * All rights reserved. Unless required by applicable law
 * or agreed to in writing, this software/data
 * USAGE IS STRICTLY PROHIBITED
 *
 * Date: 2013-2-19
 */
(function(){var JSON;var deviceDetect;var dcsInitialization;dcsInitialization=(function(func){return func;}(function DCS_initialization(response,deviceDetectCallbacks){var CONSTANT={LOG:"log",SILENT:"silent",THROW:"throw",ROOT:window};var DCS={};DCS.dcsVersion="1.0.8-SNAPSHOT";function raiseException(){var i;for(i=0;i<response.errors.length;i+=1){DCS._error(response.errors[i].text+". "+response.errors[i].reason);}}function exportPlugin(){var settingsNames=DCS.settings.name,namesLength,i;if(typeof settingsNames==="string"){settingsNames=[settingsNames];}namesLength=settingsNames.length;function processNames(nsName){var namespaceName=nsName,objectTree,papaObject,pluginName;objectTree=getTree(namespaceName);papaObject=getPreLast(objectTree);pluginName=namespaceName.split(".");pluginName=getLast(pluginName);if(papaObject){papaObject[pluginName]=DCS;}}for(i=0;i<namesLength;i+=1){processNames(settingsNames[i]);}}function mergeClientDetection(){if(isObject(deviceDetectCallbacks)){DCS._data=merge(deviceDetectCallbacks,DCS._data);}}function initPlugin(){mergeClientDetection();exportPlugin();raiseException();return DCS;}var create=Object.create?function(){return Object.create.apply(Object,arguments);}:function(obj){var F=function(){};F.prototype=obj;return new F();};var trim=String.prototype.trim?function(string){return string.trim();}:function(string){return string.replace(/^\s+|\s+$/g,"");};function isObject(obj){return Object.prototype.toString.call(obj)==="[object Object]"||typeof obj==="object";}function merge(from,to){var propertyName;for(propertyName in from){if(isObject(from[propertyName])){if(!to.hasOwnProperty(propertyName)){to[propertyName]={};}merge(from[propertyName],to[propertyName]);}else{to[propertyName.toLowerCase()]=from[propertyName];}}return to;}function getLast(arr){return arr[arr.length-1];}function setLast(arr,val){arr[arr.length-1]=val;}function getPreLast(arr){return arr[arr.length-2];}function getTree(string,context){var path;var i;var res;var current_level;string=trim(string);path=string.split(".");current_level=context||CONSTANT.ROOT;res=[current_level];for(i=0;i<path.length;i+=1){if(current_level&&isObject(current_level)&&path[i] in current_level){current_level=current_level[path[i]];}else{current_level=undefined;}res.push(current_level);}return res;}function updateTree(string,context,value){var path;var i;var current_level;string=trim(string);path=string.split(".");current_level=context||CONSTANT.ROOT;for(i=0;i<path.length;i+=1){if(i===path.length-1){current_level[path[i]]=value;}else{if(current_level&&isObject(current_level)&&path[i] in current_level){current_level=current_level[path[i]];}else{current_level[path[i]]={};current_level=current_level[path[i]];}}}}DCS.settings=response.settings;DCS._data=response.data;DCS._log=function(){if(window.console&&typeof window.console.log==="function"){window.console.log.apply(window.console,arguments);}};DCS.Error=function(message){if(this.constructor!==DCS.Error){return new DCS.Error(message);}else{message=DCS.settings.name+": "+message;this.message=message;}};DCS.Error.prototype=create(Error.prototype);DCS.Error.prototype.constructor=DCS.Error;DCS._error=function(error){var loglevel;if(typeof error==="string"){this._error(this.Error(error));}else{loglevel=this.settings.loglevel;switch(loglevel){case CONSTANT.SILENT:break;case CONSTANT.THROW:throw error;case CONSTANT.LOG:this._log(error.message);break;default:this._log(error.message);this._log(DCS.settings.name+': Invalid "loglevel" setting: "'+loglevel+'"');break;}}};DCS.get=function(propertyName){var propertyValue;var path;if(propertyName&&propertyName.toString){if(propertyName.toString().toLowerCase()==="device.model"){propertyName="device.modelname";}path=getTree(trim(propertyName.toString().toLowerCase()),this._data);propertyValue=getLast(path);if(typeof propertyValue==="undefined"){this._error('Property "'+propertyName+'" is not provided');}}if(typeof propertyValue==="function"){try{propertyValue=propertyValue();}catch(err){DCS._error('Error, while evaluating "'+propertyName+'" ');}}return propertyValue;};DCS.set=function(propertyName,value){if(propertyName&&propertyName.toString){updateTree(trim(propertyName.toString().toLowerCase()),this._data,value);}};DCS.isProvided=function(propertyName){var foundProperty;var path;if(propertyName&&propertyName.toString){propertyName=trim(propertyName.toString().toLowerCase());path=getTree(propertyName,DCS._data);foundProperty=getLast(path);return foundProperty!==null&&foundProperty!==undefined;}};return initPlugin();}))||function(){};JSON={};JSON.data=(function(obj){return obj;}(
{"html5form" : {"caninputdatetimelocal" : true, "caninputautocorrect" : false, "caninputplaceholdercss" : true, "caninputnumbercontrols" : true, "caninputrequired" : true, "caninputautocapitalize" : false, "caninputplaceholder" : true, "caninputplaceholderfallback" : "1"}, "os" : {"releasedate" : "2009-10-22"}, "hardware" : {"bearer" : {"canevdo" : false, "cannfc" : false, "canlte" : false, "cancdma" : false, "canhsdpa" : false, "canumts" : false, "canedge" : false, "cancsd" : false, "canconsumerir" : false, "cangprs" : false}, "cancamera3d" : false, "performance" : {"js" : 867.43}, "cancamera" : false}, "clusters" : {"cluster_bandwidth" : [], "cluster_nglcluster" : ["exception parsing cluster: java.lang.RuntimeException: Unable to resolve symbol: non in this context, compiling:(null:1)"], "cluster_office" : [], "cluster_recursion_test" : ["Works"], "cluster_newcluster_new_12" : [], "cluster_oldnames" : [], "cluster_hiber2" : ["blubber","tschuu, tschuu"], "cluster_performance" : ["fast-performance"], "cluster_isfast" : ["not-fast"], "cluster_ismedium" : ["not-medium"], "cluster_device_routing" : ["desktop"], "cluster_globalcluster" : ["big"], "cluster_time_personas" : [], "cluster_isweekend" : [], "cluster_ismedium2" : ["not-medium"], "cluster_size" : ["big"]}, "operatingsystemagelatest" : 64, "operatingsystemage" : 2170, "html5media" : {"canvideomultisrc" : true, "canaudiomultisrc" : true, "canvideoonerror" : true, "canaudiosinglesrc" : true, "canvideoonerrorsinglesrc" : true, "canaudioonerror" : true, "canaudioasvideo" : true, "canaudioonerrorsinglesrc" : true, "canvideosinglesrc" : true}, "device" : {"modelname" : "Windows PC", "operatingsystemmodel" : "Windows 7", "uaid" : "999-023-001", "vendor" : "Misc", "displayresolutiondetectionmethod" : "document client width", "isbot" : false, "operatingsystemuaid" : "201-026-001", "operatingsystem" : "Windows", "copyright" : "Copyright (c) 2013 Netbiscuits GmbH. All rights reserved.\\nUnless required by applicable law or agreed to in writing, this\\nsoftware/data USAGE IS STRICTLY PROHIBITED.", "type" : "Computer", "revision" : "619129343773", "operatingsystemversion" : "7", "operatingsystemvendor" : "Microsoft", "modelseries" : "Windows PC", "carrier" : "International", "canswitchdisplayorientation" : false}, "browser" : {"candisableinput" : true, "markup" : {"canhtmltransitional" : true, "canhtml5" : true, "canhtmlstrict" : true, "canchtml" : true, "canxhtml1.0" : true, "canxhtml1.1" : true, "canxhtml1.2" : true}, "model" : "Chrome 45", "canserverredirect" : true, "events" : {"canonorientationchange" : false}, "releasedate" : "2015-09-01", "canjavascript" : true, "type" : "Desktop-Browser", "agelatest" : 28, "canpointerapi" : false, "cancookie" : true, "age" : 30, "html5" : {"files" : {"canfilereaderapi" : true, "canfilesystemapi" : true}, "other" : {"cangetselection" : true, "canpagevisiblity" : true, "canscrollintoview" : true}, "animation" : {"canrequestanimationframe" : true, "canrequestfullscreen" : true}, "audio" : {"canaudio" : true, "canaac" : true, "canmp3" : true, "canvorbis" : true, "canpcm" : true, "canwebm" : true}, "location" : {"canorientation" : true, "cangeolocation" : true}, "communication" : {"canxmlhttprequest2upload" : true, "canxmlhttprequest2blobresponse" : true, "caneventsource" : true, "canpostmessage" : true, "canxmlhttprequest2textresponse" : true, "canxmlhttprequest2documentresponse" : true, "canwebsocket" : true, "canxmlhttprequest2arrayresponse" : true}, "canhistory" : true, "canmicrodata" : false, "canvas" : {"cancontext" : true, "cantext" : true, "cancanvas" : true}, "canwebaudioapi" : true, "interaction" : {"editing" : {"elementscaniscontenteditable" : true, "elementscancontenteditable" : true, "documentscandesignmode" : true}, "apis" : {"canquerycommandvalue" : true, "canexeccommand" : true, "canquerycommandindeterm" : true, "canquerycommandsupported" : true, "canquerycommandstate" : true, "canquerycommandenabled" : true}, "events" : {"canondrop" : true, "canondragstart" : true, "canondragover" : true, "canondrag" : true, "canondragleave" : true, "canondragenter" : true, "canondragend" : true}, "canspellcheck" : true, "attributes" : {"candropzone" : true, "candraggable" : true}}, "security" : {"cansrcdoc" : true, "canseamless" : false, "cansandbox" : true}, "workers" : {"cansharedworker" : true, "canworker" : true}, "canhistoryhashchange" : true, "offline" : {"canaddsearchprovider" : true, "canregistercontenthandler" : false, "canregisterprotocolhandler" : true, "canapplicationcache" : true}, "webgl" : {"cancontext" : true, "datatypes" : {"canint16array" : true, "canuint32array" : true, "canarraybuffer" : true, "canint32array" : true, "canfloat64array" : true, "candataview" : true, "canuint8array" : true, "canint8array" : true, "canuint16array" : true, "canfloat32array" : true}}, "cangetusermedia" : true, "cannotifications" : false, "parsing" : {"candoctype" : true, "cantree" : true, "canmathml" : true, "cansvg" : true, "cantokenizer" : true}, "various" : {"canbase64" : true, "canscoped" : false, "canonerror" : true, "canasync" : true}, "sdtsize" : "792537443249", "elements" : {"caninteractivemenutoolbar" : false, "cangroupingfigure" : true, "caninteractivemenucontext" : false, "cangroupingol" : true, "cansemanticwbr" : true, "cansectionaside" : true, "canhidden" : true, "caninteractivecommand" : false, "cansectionsection" : true, "candataset" : true, "candynamicouterhtml" : true, "cansectionfooter" : true, "cansectionarticle" : true, "caninteractivedetails" : true, "cansectionhgroup" : true, "cansectionheader" : true, "cangroupingfigcaption" : true, "cansectionnav" : true, "cansemanticmark" : true, "cansemantictime" : false, "caninteractivemenu" : true, "candynamicinsertadjacenthtml" : true, "caninteractivesummary" : true, "cansemanticruby" : true}, "video" : {"cansubtitle" : true, "cantheora" : true, "canh264" : true, "canposter" : true, "canwebm" : true, "canvideo" : true, "canmpeg4" : false}, "forms" : {"canselect" : true, "candatalist" : true, "other" : {"canplaceholder" : true, "candirname" : true, "canautocomplete" : true, "canmultiple" : true, "canautofocus" : true}, "candatalistlist" : true, "validation" : {"canrequired" : true, "cannovalidate" : true, "cancheckvalidity" : true, "canpattern" : true}, "cantextareawrap" : true, "association" : {"canformmethod" : true, "canformtarget" : true, "canlabels" : true, "canformnovalidate" : true, "canform" : true, "cancontrol" : true, "canformenctype" : true, "canformaction" : true}, "events" : {"canoninvalid" : true, "canoninput" : true, "canonchange" : true}, "cantextareamaxlength" : true, "canoutput" : true, "cankeygenkeytype" : true, "canmeter" : true, "canfieldsetdisabled" : true, "inputtype" : {"candatestepdown" : true, "canmonthmax" : true, "cancolorsanitization" : true, "cannumbermax" : true, "candatetimelocal" : true, "canmonthmin" : true, "cantimesanitization" : true, "cantimemin" : true, "canrangestepdown" : true, "canurl" : true, "cantimemax" : true, "cantimestepup" : true, "candatestepup" : true, "canimageheight" : true, "canmonthstep" : true, "cannumberstepdown" : true, "cannumbersanitization" : true, "canmonthstepdown" : true, "canweekui" : true, "canmonthsanitization" : true, "canemailvalidation" : true, "cantimeui" : true, "cantimestep" : true, "candatemin" : true, "canweekmax" : true, "candatemax" : true, "canimagewidth" : true, "candatetimelocalstepdown" : true, "cancolor" : true, "canmonthstepup" : true, "candatetimestepdown" : false, "canfile" : true, "candatetimelocalstep" : true, "candatetimelocalstepup" : true, "candateui" : true, "canweekstepdown" : true, "cannumbervalidation" : true, "cantime" : true, "candatetimestepup" : false, "canweekstepup" : true, "candate" : true, "canmonth" : true, "cantel" : true, "canweeksanitization" : true, "candatetimesanitization" : false, "canfilefiles" : true, "candatetimelocalmin" : true, "canrangestepup" : true, "cancolorvalidation" : false, "candatetimeui" : false, "canurlvalidation" : true, "canrangesanitization" : true, "cancheckbox" : true, "cannumbermin" : true, "candatetime" : false, "canrangeui" : true, "candatetimemin" : false, "candatesanitization" : true, "cannumberui" : true, "cannumber" : true, "cantext" : true, "candatetimemax" : false, "candatestep" : true, "candatetimelocalmax" : true, "canweekmin" : true, "canrange" : true, "candatetimelocalui" : true, "canweekstep" : true, "canrangemax" : true, "cannumberstepup" : true, "canrangemin" : true, "canemail" : true, "cannumberstep" : true, "cantimestepdown" : true, "candatetimestep" : false, "cantextselection" : true, "candatetimelocalsanitization" : true, "canrangestep" : true, "cancheckboxindeterminate" : true, "canimage" : true, "canmonthui" : true, "canweek" : true, "cansearch" : true, "cancolorui" : true}, "cankeygenchallenge" : true, "selectors" : {"canrequired" : true, "canoptional" : true, "canvalid" : true, "canoutofrange" : true, "canreadwrite" : true, "canreadonly" : true, "caninvalid" : true, "caninrange" : true}, "canfieldset" : true, "canfieldsetelements" : true, "canprogress" : true, "canselectrequired" : true, "cankeygen" : true, "cantextarea" : true}, "storage" : {"canindexeddb" : true, "canlocalstorage" : true, "cansessionstorage" : true, "cansqldatabase" : true}}, "api" : {"supportswebnotification" : true}, "canjavascriptredirect" : true, "cantable" : true, "javascript" : {"canmatchmediaapi" : false}, "uaid" : "119-272-001", "vendor" : "Google", "cantouch" : true, "canssl" : true, "canajax" : true, "cantelmakecall" : false, "canmailtotag" : true, "tables" : {"canverticalalignment" : true, "canth" : true, "cancellbackgroundcolor" : true, "canverticalalignmenttop" : true, "canborder" : true, "canhorizontalaligment" : true, "canbackgroundcolor" : true, "cancellspacing" : true, "cancellpadding" : true}, "islatestrelease" : false, "cantouchapi" : true, "newerreleasecount" : 5, "canmetaredirect" : true, "modelseries" : "Chrome", "css" : {"3image" : {"canlineargradient" : false, "canrepeatingradialgradient" : false, "canobjectfit" : true, "canrepeatinglineargradient" : false, "canradialgradient" : false, "canorientation" : false, "canimage" : false, "canobjectposition" : true, "canresolution" : false}, "3animations" : {"cananimationiterationcount" : true, "cananimationfillmode" : true, "cananimation" : true, "cananimationplaystate" : true, "cananimationdirection" : true, "cananimationtimingfunction" : true, "cananimationduration" : true, "cananimationname" : true, "cananimationdelay" : true}, "cansvg" : true, "canfonthelvetica" : false, "3flexbox" : {"canflexgrow" : true, "canorder" : true, "canminheight" : true, "canalignitems" : true, "canflexflow" : true, "canjustifycontent" : true, "canflexshrink" : true, "canaligncontent" : true, "candisplay" : true, "canflexdirection" : true, "canminwidth" : true, "canflexwrap" : true, "canflex" : true, "canflexbasis" : true, "canalignself" : true}, "3color" : {"canrgba" : true, "canhsla" : true, "cantransparent" : true, "canopacity" : true, "canhsl" : true, "cancurrentcolor" : true}, "3multicolumns" : {"cancolumncount" : true, "cancolumnrulewidth" : true, "canbreakinside" : false, "cancolumns" : true, "cancolumnrulecolor" : true, "cancolumnrule" : true, "cancolumnwidth" : true, "cancolumngap" : true, "cancolumnfill" : false, "cancolumnrulestyle" : true, "canbreakbefore" : false, "cancolumnspan" : true, "canbreakafter" : false}, "3mediaqueries" : {"canheight" : true, "canaspectratio" : true, "canmonochrome" : true, "cangrid" : true, "candeviceheight" : true, "canorientation" : true, "cancolor" : true, "candeviceaspectratio" : true, "canwidth" : true, "cannegation" : true, "canscan" : true, "cancolorindex" : true, "candevicewidth" : true, "canresolution" : true}, "3transitions" : {"cantransitionproperty" : true, "cantransitiondelay" : true, "cantransitiontimingfunction" : true, "cantransitionduration" : true, "cantransition" : true}, "fontsizesuitable" : 16, "cansvgclippath" : true, "canfontsansserif" : false, "3border" : {"canimagerepeat" : true, "canimagesource" : true, "canimageoutset" : true, "canradius" : true, "canimage" : true}, "3selectors" : {"canroot" : true, "cannamespaces" : true, "cannthoftype" : true, "cansiblingcombinator" : true, "cannthlastchild" : true, "cansuffix" : true, "candisabled" : true, "cannthchild" : true, "canonlyoftype" : true, "canenabled" : true, "cannthlastoftype" : true, "canchecked" : true, "canlastoftype" : true, "canfirstletter" : true, "canafter" : true, "canfirstoftype" : true, "canfirstline" : true, "canbefore" : true, "cancontains" : true, "cannot" : true, "cantarget" : true, "canlastchild" : true, "canempty" : true, "canonlychild" : true, "canindeterminate" : true, "canprefix" : true}, "3text" : {"canlinebreak" : true, "canletterspacing" : false, "cantextalign" : true, "cantextemphasisstyle" : true, "cantextdecorationstyle" : false, "cantextunderlineposition" : false, "cantextindent" : false, "canwordspacing" : false, "cantexttransform" : false, "cantextalignlast" : false, "cantextemphasis" : true, "cantabsize" : true, "cantextdecorationcolor" : false, "cantextemphasiscolor" : true, "cantextdecoration" : false, "cantextjustify" : false, "canoverflowwrap" : true, "canhyphens" : false, "cantextdecorationskip" : false, "canwordbreak" : true, "cantextshadow" : true, "cantextemphasisposition" : false, "cantextdecorationline" : false, "canhangingpunctuation" : false}, "cansvginline" : true, "canformmargin" : true, "vendorprefix" : "-webkit-", "cangeneratedcontent" : true, "3transforms" : {"canperspectiveorigin" : true, "canperspective" : true, "cantransformorigin" : true, "canbackfacevisibility" : true, "cantransform" : true, "cantransformstyle" : true, "cantransform3d" : true}, "canelementbackgroundcolor" : true, "canfontsize" : true, "fontsizeminimum" : 14, "3fonts" : {"canfontvariantalternates" : false, "canfontsizeadjust" : false, "canfontvariantposition" : false, "canfontvariant" : false, "canfontkerning" : true, "canfontvariantnumeric" : false, "canfontvarianteastasian" : false, "canfontstretch" : true, "canfontvariantcaps" : false, "canfontsynthesis" : false, "canfontface" : true, "canfontvariantligatures" : true}, "3valuesunits" : {"canvmin" : true, "canch" : true, "cancalc" : true, "cantoggle" : false, "canrem" : true, "canvh" : true, "canattr" : false, "canvw" : true}, "canfontarial" : false, "3background" : {"canrepeat" : true, "canclip" : true, "canmultiple" : true, "canattachment" : true, "cansize" : true, "canposition" : true}, "3ui" : {"canboxsizing" : false, "cannavup" : false, "cannavleft" : false, "cannavindex" : false, "cannavdown" : false, "canoutlineoffset" : true, "cancursor" : true, "cantextoverflow" : true, "cannavright" : false, "canresize" : true, "cancontent" : false, "canimemode" : false, "canicon" : false}, "cansvgsmil" : true, "canpositionfixed" : true, "3box" : {"candecorationbreak" : true, "canshadow" : true}, "3writingmodes" : {"cantextcombinehorizontal" : false, "cantextcombinemode" : false, "canwritingmode" : false, "canunicodebidi" : true, "cantextorientation" : true, "cancaptionside" : false}, "canformsubmitgraphical" : false, "3speech" : {"canvoicerate" : false, "canvoicebalance" : false, "cancueafter" : false, "canspeakas" : false, "canspeak" : false, "cancuebefore" : false, "canpauseafter" : false, "canvoicestress" : false, "canrestafter" : false, "canvoicevolume" : false, "canvoicerange" : false, "canvoicepitch" : false, "cancue" : false, "canpause" : false, "canvoiceduration" : false, "canvoicefamily" : false, "canrestbefore" : false, "canpausebefore" : false, "canrest" : false}, "canfontverdana" : false, "canreflections" : true}}, "image" : {"maxwidth" : 1920, "maxheight" : 1080}, "operatingsystemislatestrelease" : false, "operatingsystemnewerreleasecount" : 5, "internal" : {"carriername" : "n/a", "cachekey" : "999-023-001,201-026-001,119-272-001", "devicemodelnamegeneralized" : "Windows PC"}, "video" : {"suggestedvideoformat" : {"videovideoformatframesize" : "qcif", "videovideoformatmaxvideobitrate" : 104, "videovideoformatvideocodec" : "H264", "videovideoformatframespersecond" : "15", "videovideoformatvideocodecprofile" : "Baseline Profile, Level 1", "videovideoformatcanstreaming" : true, "videovideoformataudiochannels" : 2, "videovideoformatmaxaudiobitrate" : 24, "videovideoformatcontainertype" : "mp4", "videovideoformatsupportsprogressivedownloads" : true, "videovideoformataudiosamplingrate" : "22050", "videovideoformataudiocodec" : "AAC"}, "canh264_480" : true}}
))||{};JSON.errors=(function(arr){return arr;}(

))||[];deviceDetect=(function(obj){return obj;}(
{"internal" : {"cookiesupporttested" : function() {  var support, cvalue, readUrl;  support = false;  cvalue = 0;  try {   if (document.cookie) {    var cookies = document.cookie.split("; ");    var output = "";    for ( var i = 0; i < cookies.length; i++) {     var cookiesplit = cookies[i].split("=");     if (cookiesplit[0] === "emvcc") {      cvalue = cookiesplit[1];     }    }   }   if (cvalue === "true" || cvalue === "1") {    support = true;   }  } catch (ex) { }  return support; }, "connectiontype" : function() {  var ct, t;  ct = "unknown";  try {   for (t in navigator.connection) {    if (t != "type" && navigator.connection.type === navigator.connection[t]) {     ct = t;    }   }  } catch (ex) { }  return ct; }, "browserpixelratio" : function() {  var pr = 1;  try {   pr = window.devicePixelRatio;   if (pr) {    pr = parseFloat(pr, 10);   } else {    pr = 1;   }  } catch (ex) { }  return pr; }}}
))||{};JSON.settings={loglevel:"log",name:["jQuery.dcs","jQuery.dci","window.dcs"]};dcsInitialization(JSON,deviceDetect);}());var dcs = dcs || {set: function(){}};
dcs.dynamic = (function () {

	// Private functions and members

	// Request session ID
	var requestSessionId,

		// Analytics session ID
		sessionId="bce58f652b798a22c20dd517f302b86a",

		// User Id
		visitorId="su-bce58f652b798a22c20dd517f302b86a",

		// Stabilization state of visitorId
		visitorIdStable = visitorId && visitorId.indexOf('su-') === 0,

		// List of visitorAliases
		visitorAliases=["su-bce58f652b798a22c20dd517f302b86a"],

		// Device id
		deviceId="sd-bce58f652b798a22c20dd517f302b86a",

		// Stabilization state of deviceId
		deviceIdStable = deviceId && deviceId.indexOf('sd-') === 0,

		// Current hostname or user-defined hostname override
		hostname = (typeof window !== 'undefined' && typeof window.location !== 'undefined') ? window.location.hostname : undefined,

		// Current path
		pathname = (typeof window !== 'undefined' && typeof window.location !== 'undefined') ? window.location.pathname : undefined,

		// Referrer to page that linked to current page
		origReferrer = (typeof document !== 'undefined') ? document.referrer : undefined,

		// Variable to be initialized with analytics script if present
		analytics = undefined,

		// Variable to be initialized with fingerprint script if present
		fingerprint = undefined,

		// Callback to be used when fingerprint result has been sent
		fpCallback = undefined,

		// Callback to be used when conversion was logged
		conversionCallback = undefined,

		// insert customs clusters if provided, DEFAULT HAS TO BE UNDEFINED
		customClusters = undefined,

		// Array to store conversions temporary if requestSessionId not yet available
		conversionsToLog = [],

		// Container object for all automatic conversion sensors
		autoConversion = {},

		/**
		 * Cookie reader/writer framework
		 * https://developer.mozilla.org/en-US/docs/Web/API/document/cookie
		 * @type {{getItem: Function, setItem: Function, removeItem: Function, hasItem: Function}}
		 */
		cookies = {
			/**
			 * Get the value of a cookie
			 * @param {string} sKey Name of the cookie
			 * @return {string|null} Value of the cookie or null if cookie is not present
			 */
			getItem: function (sKey) {
				if (!sKey) { return null; }
				return decodeURIComponent(document.cookie.replace(new RegExp("(?:(?:^|.*;)\\s*" + encodeURIComponent(sKey).replace(/[\-\.\+\*]/g, "\\$&") + "\\s*\\=\\s*([^;]*).*$)|^.*$"), "$1")) || null;
			},
			/**
			 * Create a new cookie
			 * @param {string} sKey Name of the cookie
			 * @param {*} sValue Value of the cookie
			 * @param {Number|String|Date} [vEnd] Expiry
			 * @param {string} [sDomain] Domain
			 * @param {string} [sPath] Path
			 * @param {boolean} [bSecure] HTTPS cookie
			 * @return {boolean} True, if cookie was set, false in case of incompatible cookie name
			 */
			setItem: function (sKey, sValue, vEnd, sDomain, sPath, bSecure) {
				if (!sKey || /^(?:expires|max\-age|path|domain|secure)$/i.test(sKey)) { return false; }
				var sExpires = "";
				if (vEnd) {
					switch (vEnd.constructor) {
						case Number:
							sExpires = vEnd === Infinity ? "; expires=Fri, 31 Dec 9999 23:59:59 GMT" : "; max-age=" + vEnd;
							break;
						case String:
							sExpires = "; expires=" + vEnd;
							break;
						case Date:
							sExpires = "; expires=" + vEnd.toUTCString();
							break;
					}
				}
				document.cookie = encodeURIComponent(sKey) + "=" + encodeURIComponent(sValue) + sExpires + (sDomain ? "; domain=" + sDomain : "") + (sPath ? "; path=" + sPath : "") + (bSecure ? "; secure" : "");
				return true;
			},
			/**
			 * Delete a cookie
			 * @param {string} sKey Cookie name
			 * @param {string} [sDomain] Domain
			 * @param {string} [sPath] Path
			 * @return {boolean} True, if cookie was delete, false if cookie was not present
			 */
			removeItem: function (sKey, sDomain, sPath) {
				if (!this.hasItem(sKey)) { return false; }
				document.cookie = encodeURIComponent(sKey) + "=; expires=Thu, 01 Jan 1970 00:00:00 GMT" + (sDomain ? "; domain=" + sDomain : "") + (sPath ? "; path=" + sPath : "");
				return true;
			},
			/**
			 * Check existance of a cookie
			 * @param {string} sKey Name of cookie
			 * @return {boolean} True, if cookie exists, false else
			 */
			hasItem: function (sKey) {
				if (!sKey) { return false; }
				return (new RegExp("(?:^|;\\s*)" + encodeURIComponent(sKey).replace(/[\-\.\+\*]/g, "\\$&") + "\\s*\\=")).test(document.cookie);
			}
		},

		firstScriptElement = document.getElementsByTagName('script')[0],

		/**
		 * Function to insert script tags into the DOM
		 * @param {HTMLElement} script Script tag
		 */
		insertScriptTag = function(script) { firstScriptElement.parentNode.insertBefore(script, firstScriptElement); };

	/**
	 * Run all sensors and call the callback.
	 * @param {object} paras Parameter object
	 * @param {Array.<String>} [paras.sensors] List of sensors to use
	 * @param {String} [paras.image=//dcs.netbiscuits.net/static/cqdimg.gif] Image to be used for bandwidth detection
	 * @param {boolean} [paras.benchmark=false] Wether or not to enable hardware benchmark
	 * @param {boolean} [paras.profilediff=false] Whether or not to exchange profiles after benchmark has run
	 * @param {boolean} [paras.disableAnalyticsSession=false] Whether or not to disable analytics session
	 * @param {string} [paras.path] Custom path for DCS callback
	 * @param {string} [paras.hostname] Custom hostname to be sent back to analytics
	 * @param {Function} [paras.callback] Callback called after dynamic detection is finished
	 * @param {Function} [paras.fpCallback] Callback called after fingerprint has finished
	 * @param {Function} [paras.conversionCallback] Callback called after conversion was logged
	 * @param {Function} [paras.cbNbSid] Callback to return session Id (for testing only)
	 */
	function run(paras) {
		"_putHereExternalStuff:nomunge, requestSessionId:nomunge, pathClusters:nomunge, pathAnalytics:nomunge, customClusters:nomunge, tools:nomunge, paraHwBench:nomunge, results:nomunge, nbParams:nomunge, checkToSend:nomunge, paraProfileDiff: nomunge, ";

		var paraSensors = paras.sensors,
			paraImage = paras.image || "//dcs.netbiscuits.net/static/cqdimg.gif",
			paraHwBench = paras.benchmark || false,
			paraProfileDiff = paras.profilediff || false,
			disableAnalyticsSessionFlag = paras.disableAnalyticsSession || false,

			toolsToExecuteCount = 0,

			// Container object for all sensors
			tools = {},

			// Container object for sensor results
			results = {},

			// String containing the parameters for a analytics/cluster-evaluation request
			nbParams = '',
			allToolsCount = 0,
			path = paras.path,
			callback = "dcs.dynamic.processCluster",
			finishedSensorsCount = 0,

			// insert path to clusters
			pathClusters="//dci.dev.netbiscuits.com:80/ds/detect/cluster/dcsdemo/0b0524ec8c1c26c1dfc2529de12697920f27a156ab2ef19d0429a45b7d6ac02fc5c62ebb1f2983efea151571dfc17337f29bf1e969cde93c748dc975b6145ff3?cb=",

			// insert path to analytics
			pathAnalytics="//dci.dev.netbiscuits.com:80/ds/analytics/log/dcsdemo/0b0524ec8c1c26c1dfc2529de12697920f27a156ab2ef19d0429a45b7d6ac02fc5c62ebb1f2983efea151571dfc17337f29bf1e969cde93c748dc975b6145ff3?cb=";

		// Return sessionId if callback was provided
		typeof paras.cbNbSid === 'function' && paras.cbNbSid(sessionId);

		// Overwrite hostname with customer setting if available
		hostname = paras.hostname || hostname;

		// Overwrite fingerprint callback or set default empty function
		fpCallback = paras.fpCallback || function() {};

		// Overwrite fingerprint callback or set default empty function
		conversionCallback = paras.conversionCallback || function() {};

		nbParams += (hostname) ? '&h=' + hostname : '';
		if (pathname) {
			nbParams += '&p=' + encodeURIComponent(pathname);

			var googlePaid = /(utm_|GCLID)/i;
			// Find utm_* or GCLID parameters and add them
			if (googlePaid.test(window.location.search)) {
				var sParams = window.location.search.slice(1).split('&'),
					queryString = '';
				while (sParams.length) {
					var p = sParams.pop();
					if (googlePaid.test(p)) {
						queryString += (queryString ? '%26' : '?') + p;
					}
				}
				nbParams += queryString;
			}
		}
		nbParams += (origReferrer) ? '&or=' + encodeURIComponent(origReferrer) : '';

		// needed for accessing client callback after receiving clusters
		dcs.dynamic.clientCallback = paras.callback;

		if (!path) {
			if (!customClusters) {
				path = pathAnalytics;
			} else {
				nbParams += "&cl=" + encodeURIComponent(customClusters.join(","));
				path = pathClusters;
			}
		}

		// protected
/*jslint browser: true, evil: true, white: false, devel: true, onevar: false */
/*global window */
/*global tools */
/*global paraHwBench */
/*global paraProfileDiff */
/*global results */
/*global nbParams */
/*global dcs */
/*global checkToSend */

tools.hardware = function () {
	var pr = "1.0",
		ar = "",
		ident,
		t = -1,
		settings,
		p = "",
		ch = window.navigator.userAgent.indexOf("CriOS") >= 0,
		osv,
		ack,
		i,
		cv,
		getCookie,
		measureTime,
		cube,
		measureCubeTime,
		getPixelRatio,
		getOsVersion,
		getAspectRatio,
		getPlatform,
		getSettings,
		deciders = [];

	/**
	 * Get iOS versions.
	 * Returns object with major, minor and build numbers.
	 */
	getOsVersion = function () {
		var ua, osv, v, versions;

		versions = {};
		ua = window.navigator.userAgent;

		if (ua.indexOf("OS 9") >= 0) {
			osv = 9;
		} else if (ua.indexOf("OS 8") >= 0) {
			osv = 8;
		} else if (ua.indexOf("OS 7") >= 0) {
			osv = 7;
		} else if (ua.indexOf("OS 6") >= 0) {
			osv = 6;
		} else if (ua.indexOf("OS 5") >= 0) {
			osv = 5;
		} else if (ua.indexOf("OS 4") >= 0) {
			osv = 4;
		} else if (ua.indexOf("OS 3") >= 0) {
			osv = 3;
		} else if (ua.indexOf("OS 2") >= 0) {
			osv = 2;
		} else if (ua.indexOf("OS 1") >= 0) {
			osv = 1;
		} else {
			osv = 0;
		}

		if (osv === 1) {
			versions.major = 1;
			versions.minor = 0;
			versions.build = 0;
		} else {
			try {
				v = (navigator.appVersion).match(/OS (\d+)_(\d+)_?(\d+)?/);
				versions.major = parseInt(v[1], 10);
				versions.minor = parseInt(v[2], 10);
				versions.build = parseInt(v[3], 10);
			} catch (e) {
				versions.major = osv;
				versions.minor = 0;
				versions.build = 0;
			}
		}

		return versions;
	};

	/**
	 * Get pixel ratio as string.
	 */
	getPixelRatio = function () {
		var pr = "1.0";
		try {
			pr = window.devicePixelRatio;
			if (pr) {
				pr = "" + parseFloat(pr);
			} else {
				pr = "1.0";
			}
		} catch (e2) {
		}
		return pr;
	};

	/**
	 * Calculate ackermann function.
	 * mm setting for ackermann
	 * nn setting for ackermann
	 */
	ack = function (mm, nn) {
		return mm === 0 ? nn + 1 : ack(mm - 1, nn === 0 ? 1 : ack(mm, nn - 1));
	};

	/**
	 * Measure time for benchmark.
	 * mm setting for ackermann
	 * nn setting for ackermann
	 * Returns time taken in ms.
	 */
	measureTime = function (mm, nn) {
		var startS, endS, t;
		t = -1;
		try {
			startS = new Date().getTime();
			ack(mm, nn);
			endS = new Date().getTime();
			t = endS - startS;
		} catch (ex) {
			t = -1;
		}
		return t;
	};

	/**
	 * Cube function, from JetStream Benchmarking
	 * http://browserbench.org/JetStream/
	 */
	cube = function(){
		// 3D Cube Rotation
		// http://www.speich.net/computer/moztesting/3d.htm
		// Created by Simon Speich

		var Q = new Array();
		var MTrans = new Array();  // transformation matrix
		var MQube = new Array();  // position information of qube
		var I = new Array();      // entity matrix
		var Origin = new Object();
		var Testing = new Object();
		var LoopTimer;

		var validation = {
		 20: 2889.0000000000045,
		 40: 2889.0000000000055,
		 80: 2889.000000000005,
		 160: 2889.0000000000055
		};

		var DisplArea = new Object();
		DisplArea.Width = 300;
		DisplArea.Height = 300;

		function DrawLine(From, To) {
		  var x1 = From.V[0];
		  var x2 = To.V[0];
		  var y1 = From.V[1];
		  var y2 = To.V[1];
		  var dx = Math.abs(x2 - x1);
		  var dy = Math.abs(y2 - y1);
		  var x = x1;
		  var y = y1;
		  var IncX1, IncY1;
		  var IncX2, IncY2;
		  var Den;
		  var Num;
		  var NumAdd;
		  var NumPix;

		  if (x2 >= x1) {  IncX1 = 1; IncX2 = 1;  }
		  else { IncX1 = -1; IncX2 = -1; }
		  if (y2 >= y1)  {  IncY1 = 1; IncY2 = 1; }
		  else { IncY1 = -1; IncY2 = -1; }
		  if (dx >= dy) {
		    IncX1 = 0;
		    IncY2 = 0;
		    Den = dx;
		    Num = dx / 2;
		    NumAdd = dy;
		    NumPix = dx;
		  }
		  else {
		    IncX2 = 0;
		    IncY1 = 0;
		    Den = dy;
		    Num = dy / 2;
		    NumAdd = dx;
		    NumPix = dy;
		  }

		  NumPix = Math.round(Q.LastPx + NumPix);

		  var i = Q.LastPx;
		  for (; i < NumPix; i++) {
		    Num += NumAdd;
		    if (Num >= Den) {
		      Num -= Den;
		      x += IncX1;
		      y += IncY1;
		    }
		    x += IncX2;
		    y += IncY2;
		  }
		  Q.LastPx = NumPix;
		}

		function CalcCross(V0, V1) {
		  var Cross = new Array();
		  Cross[0] = V0[1]*V1[2] - V0[2]*V1[1];
		  Cross[1] = V0[2]*V1[0] - V0[0]*V1[2];
		  Cross[2] = V0[0]*V1[1] - V0[1]*V1[0];
		  return Cross;
		}

		function CalcNormal(V0, V1, V2) {
		  var A = new Array();   var B = new Array();
		  for (var i = 0; i < 3; i++) {
		    A[i] = V0[i] - V1[i];
		    B[i] = V2[i] - V1[i];
		  }
		  A = CalcCross(A, B);
		  var Length = Math.sqrt(A[0]*A[0] + A[1]*A[1] + A[2]*A[2]);
		  for (var i = 0; i < 3; i++) A[i] = A[i] / Length;
		  A[3] = 1;
		  return A;
		}

		function CreateP(X,Y,Z) {
		  this.V = [X,Y,Z,1];
		}

		// multiplies two matrices
		function MMulti(M1, M2) {
		  var M = [[],[],[],[]];
		  var i = 0;
		  var j = 0;
		  for (; i < 4; i++) {
		    j = 0;
		    for (; j < 4; j++) M[i][j] = M1[i][0] * M2[0][j] + M1[i][1] * M2[1][j] + M1[i][2] * M2[2][j] + M1[i][3] * M2[3][j];
		  }
		  return M;
		}

		//multiplies matrix with vector
		function VMulti(M, V) {
		  var Vect = new Array();
		  var i = 0;
		  for (;i < 4; i++) Vect[i] = M[i][0] * V[0] + M[i][1] * V[1] + M[i][2] * V[2] + M[i][3] * V[3];
		  return Vect;
		}

		function VMulti2(M, V) {
		  var Vect = new Array();
		  var i = 0;
		  for (;i < 3; i++) Vect[i] = M[i][0] * V[0] + M[i][1] * V[1] + M[i][2] * V[2];
		  return Vect;
		}

		// add to matrices
		function MAdd(M1, M2) {
		  var M = [[],[],[],[]];
		  var i = 0;
		  var j = 0;
		  for (; i < 4; i++) {
		    j = 0;
		    for (; j < 4; j++) M[i][j] = M1[i][j] + M2[i][j];
		  }
		  return M;
		}

		function Translate(M, Dx, Dy, Dz) {
		  var T = [
		  [1,0,0,Dx],
		  [0,1,0,Dy],
		  [0,0,1,Dz],
		  [0,0,0,1]
		  ];
		  return MMulti(T, M);
		}

		function RotateX(M, Phi) {
		  var a = Phi;
		  a *= Math.PI / 180;
		  var Cos = Math.cos(a);
		  var Sin = Math.sin(a);
		  var R = [
		  [1,0,0,0],
		  [0,Cos,-Sin,0],
		  [0,Sin,Cos,0],
		  [0,0,0,1]
		  ];
		  return MMulti(R, M);
		}

		function RotateY(M, Phi) {
		  var a = Phi;
		  a *= Math.PI / 180;
		  var Cos = Math.cos(a);
		  var Sin = Math.sin(a);
		  var R = [
		  [Cos,0,Sin,0],
		  [0,1,0,0],
		  [-Sin,0,Cos,0],
		  [0,0,0,1]
		  ];
		  return MMulti(R, M);
		}

		function RotateZ(M, Phi) {
		  var a = Phi;
		  a *= Math.PI / 180;
		  var Cos = Math.cos(a);
		  var Sin = Math.sin(a);
		  var R = [
		  [Cos,-Sin,0,0],
		  [Sin,Cos,0,0],
		  [0,0,1,0],
		  [0,0,0,1]
		  ];
		  return MMulti(R, M);
		}

		function DrawQube() {
		  // calc current normals
		  var CurN = new Array();
		  var i = 5;
		  Q.LastPx = 0;
		  for (; i > -1; i--) CurN[i] = VMulti2(MQube, Q.Normal[i]);
		  if (CurN[0][2] < 0) {
		    if (!Q.Line[0]) { DrawLine(Q[0], Q[1]); Q.Line[0] = true; };
		    if (!Q.Line[1]) { DrawLine(Q[1], Q[2]); Q.Line[1] = true; };
		    if (!Q.Line[2]) { DrawLine(Q[2], Q[3]); Q.Line[2] = true; };
		    if (!Q.Line[3]) { DrawLine(Q[3], Q[0]); Q.Line[3] = true; };
		  }
		  if (CurN[1][2] < 0) {
		    if (!Q.Line[2]) { DrawLine(Q[3], Q[2]); Q.Line[2] = true; };
		    if (!Q.Line[9]) { DrawLine(Q[2], Q[6]); Q.Line[9] = true; };
		    if (!Q.Line[6]) { DrawLine(Q[6], Q[7]); Q.Line[6] = true; };
		    if (!Q.Line[10]) { DrawLine(Q[7], Q[3]); Q.Line[10] = true; };
		  }
		  if (CurN[2][2] < 0) {
		    if (!Q.Line[4]) { DrawLine(Q[4], Q[5]); Q.Line[4] = true; };
		    if (!Q.Line[5]) { DrawLine(Q[5], Q[6]); Q.Line[5] = true; };
		    if (!Q.Line[6]) { DrawLine(Q[6], Q[7]); Q.Line[6] = true; };
		    if (!Q.Line[7]) { DrawLine(Q[7], Q[4]); Q.Line[7] = true; };
		  }
		  if (CurN[3][2] < 0) {
		    if (!Q.Line[4]) { DrawLine(Q[4], Q[5]); Q.Line[4] = true; };
		    if (!Q.Line[8]) { DrawLine(Q[5], Q[1]); Q.Line[8] = true; };
		    if (!Q.Line[0]) { DrawLine(Q[1], Q[0]); Q.Line[0] = true; };
		    if (!Q.Line[11]) { DrawLine(Q[0], Q[4]); Q.Line[11] = true; };
		  }
		  if (CurN[4][2] < 0) {
		    if (!Q.Line[11]) { DrawLine(Q[4], Q[0]); Q.Line[11] = true; };
		    if (!Q.Line[3]) { DrawLine(Q[0], Q[3]); Q.Line[3] = true; };
		    if (!Q.Line[10]) { DrawLine(Q[3], Q[7]); Q.Line[10] = true; };
		    if (!Q.Line[7]) { DrawLine(Q[7], Q[4]); Q.Line[7] = true; };
		  }
		  if (CurN[5][2] < 0) {
		    if (!Q.Line[8]) { DrawLine(Q[1], Q[5]); Q.Line[8] = true; };
		    if (!Q.Line[5]) { DrawLine(Q[5], Q[6]); Q.Line[5] = true; };
		    if (!Q.Line[9]) { DrawLine(Q[6], Q[2]); Q.Line[9] = true; };
		    if (!Q.Line[1]) { DrawLine(Q[2], Q[1]); Q.Line[1] = true; };
		  }
		  Q.Line = [false,false,false,false,false,false,false,false,false,false,false,false];
		  Q.LastPx = 0;
		}

		function Loop() {
		  if (Testing.LoopCount > Testing.LoopMax) return;
		  var TestingStr = String(Testing.LoopCount);
		  while (TestingStr.length < 3) TestingStr = "0" + TestingStr;
		  MTrans = Translate(I, -Q[8].V[0], -Q[8].V[1], -Q[8].V[2]);
		  MTrans = RotateX(MTrans, 1);
		  MTrans = RotateY(MTrans, 3);
		  MTrans = RotateZ(MTrans, 5);
		  MTrans = Translate(MTrans, Q[8].V[0], Q[8].V[1], Q[8].V[2]);
		  MQube = MMulti(MTrans, MQube);
		  var i = 8;
		  for (; i > -1; i--) {
		    Q[i].V = VMulti(MTrans, Q[i].V);
		  }
		  DrawQube();
		  Testing.LoopCount++;
		  Loop();
		}

		function Init(CubeSize) {
		  // init/reset vars
		  Origin.V = [150,150,20,1];
		  Testing.LoopCount = 0;
		  Testing.LoopMax = 50;
		  Testing.TimeMax = 0;
		  Testing.TimeAvg = 0;
		  Testing.TimeMin = 0;
		  Testing.TimeTemp = 0;
		  Testing.TimeTotal = 0;
		  Testing.Init = false;

		  // transformation matrix
		  MTrans = [
		  [1,0,0,0],
		  [0,1,0,0],
		  [0,0,1,0],
		  [0,0,0,1]
		  ];

		  // position information of qube
		  MQube = [
		  [1,0,0,0],
		  [0,1,0,0],
		  [0,0,1,0],
		  [0,0,0,1]
		  ];

		  // entity matrix
		  I = [
		  [1,0,0,0],
		  [0,1,0,0],
		  [0,0,1,0],
		  [0,0,0,1]
		  ];

		  // create qube
		  Q[0] = new CreateP(-CubeSize,-CubeSize, CubeSize);
		  Q[1] = new CreateP(-CubeSize, CubeSize, CubeSize);
		  Q[2] = new CreateP( CubeSize, CubeSize, CubeSize);
		  Q[3] = new CreateP( CubeSize,-CubeSize, CubeSize);
		  Q[4] = new CreateP(-CubeSize,-CubeSize,-CubeSize);
		  Q[5] = new CreateP(-CubeSize, CubeSize,-CubeSize);
		  Q[6] = new CreateP( CubeSize, CubeSize,-CubeSize);
		  Q[7] = new CreateP( CubeSize,-CubeSize,-CubeSize);

		  // center of gravity
		  Q[8] = new CreateP(0, 0, 0);

		  // anti-clockwise edge check
		  Q.Edge = [[0,1,2],[3,2,6],[7,6,5],[4,5,1],[4,0,3],[1,5,6]];

		  // calculate squad normals
		  Q.Normal = new Array();
		  for (var i = 0; i < Q.Edge.length; i++) Q.Normal[i] = CalcNormal(Q[Q.Edge[i][0]].V, Q[Q.Edge[i][1]].V, Q[Q.Edge[i][2]].V);

		  // line drawn ?
		  Q.Line = [false,false,false,false,false,false,false,false,false,false,false,false];

		  // create line pixels
		  Q.NumPx = 9 * 2 * CubeSize;
		  for (var i = 0; i < Q.NumPx; i++) CreateP(0,0,0);

		  MTrans = Translate(MTrans, Origin.V[0], Origin.V[1], Origin.V[2]);
		  MQube = MMulti(MTrans, MQube);

		  var i = 0;
		  for (; i < 9; i++) {
		    Q[i].V = VMulti(MTrans, Q[i].V);
		  }
		  DrawQube();
		  Testing.Init = true;
		  Loop();

		  // Perform a simple sum-based verification.
		  var sum = 0;
		  for (var i = 0; i < Q.length; ++i) {
		    var vector = Q[i].V;
		    for (var j = 0; j < vector.length; ++j)
		      sum += vector[j];
		  }
		  if (sum != validation[CubeSize])
		    throw "Error: bad vector sum for CubeSize = " + CubeSize + "; expected " + validation[CubeSize] + " but got " + sum;
		}

		for ( var i = 20; i <= 160; i *= 2 ) {
		  Init(i);
		}

		Q = null;
		MTrans = null;
		MQube = null;
		I = null;
		Origin = null;
		Testing = null;
		LoopTime = null;
		DisplArea = null;
	};

	/**
	 * Iterate through the cube function, returning the sum of all test results.
	 * Returns total time taken in ms.
	 */
	measureCubeTime = function () {
		var i = -1,
			iter = 10,
			total = 0;
		for (; ++i < iter;) {
			var startS, endS;
			startS = new Date().getTime();
			cube();
			endS = new Date().getTime();
			total += (endS - startS);
		}
		return total;
	};

	/**
	 * Get platform with override for simulators.
	 */
	getPlatform = function () {
		var p = "";
		try {
			p = window.navigator.platform;
			if (p === "iPhone Simulator") {
				p = "iPhone";
			}
		} catch (e1) {
		}
		return p;
	};

	/**
	 * Test for known aspect ratios.
	 * Returns the aspect ratio as a string if a known one matches.
	 */
	getAspectRatio = function () {
		var mq, ar;
		try {
			mq = window.matchMedia("(device-aspect-ratio: 2/3)");
			if (mq.matches) {
				ar = "2/3";
			}
		} catch (e3) {
		}

		try {
			mq = window.matchMedia("(device-aspect-ratio: 40/71)");
			if (mq.matches) {
				ar = "40/71";
			}
		} catch (e4) {
		}

		try {
			mq = window.matchMedia("(device-aspect-ratio: 3/4)");
			if (mq.matches) {
				ar = "3/4";
			}
		} catch (e5) {
		}
		return ar;
	};

	/**
	 * Get a cookie value;
	 * name: The name of the cookie.
	 */
	getCookie = function (name) {
		var cv, cvs, ctvs;
		cv = undefined;
		try {
			cvs = document.cookie.split(";");
			for (i = 0; i < cvs.length; i = i + 1) {
				ctvs = cvs[i].split("=");
				if (ctvs[0] === name) {
					cv = ctvs[1];
				}
			}
		} catch (e0) {
		}
		return cv;
	};

	/**
	 * Decide which settings to use for further detection.
	 */
	getSettings = function (platform, pixelRatio, aspectRatio, osVersion) {
		var m, n, cube, result;
		m = 2;
		n = 0;
		cube = false;

		result = {};

		if (platform === "iPhone" && (pixelRatio === "2.0" || pixelRatio === "2" || pixelRatio === "3.0" || pixelRatio === "3")) {
			// iPhone 4/4S
			if (aspectRatio === "2/3") {
				if (osVersion.major <= 4) {
					n = 300;
				} else {
					m = 3;
					n = 7;
				}

			// iPhone 5/5C/5S
			} else if (aspectRatio === "40/71") {
				if (osVersion.major <= 7) {
					n = 600;
				} else if (osVersion.major === 8) {
					n = 500;
				} else {
					n = 600;
				}

			// iPhone 4/4S
			} else if (aspectRatio === "") {
				if (osVersion.major <= 4) {
					n = 300;
				}
			}

			// iPhone 6 Plus/6s Plus
			if (pixelRatio === "3.0" || pixelRatio === "3") {
				/* These devices use a modified test */
				n = 0;
				cube = true;

			// iPhone 6/6s
			} else if (pixelRatio === "2.0" || pixelRatio === "2") {
				/* These devices use a modified test */
				if ((screen.availWidth === 375 && screen.availHeight === 647) ||
					//(screen.availWidth === 320 && screen.availHeight === 548) ||
					(screen.availWidth === 667 && screen.availHeight === 375)) {
					n = 0;
					cube = true;
				}
			}
		}
		if (platform === "iPad") {

			// iPad Pro
			if (screen.availWidth === 1024 && screen.availHeight === 1346) {
				/* These devices use a modified test directly inside of their decider function, so 0 is used here */
				n = 0;

			// iPad 3/4/Air/Air 2/Mini 2/Mini 3/Mini 4
			} else if (pixelRatio === "2.0" || pixelRatio === "2") {
				if (osVersion.major <= 7) {
					n = 1000;
				} else if (osVersion.major === 8) {
					n = 600;
				} else {
					m = 3;
					n = 8;
				}
				/*n = 0;
				cube = true;*/

			// iPad 1/2/Mini
			} else {
				if (osVersion.major <= 4) {
					m = 3;
					n = 6;
				} else {
					m = 3;
					n = 7;
				}
			}
		}
		result.m = m;
		result.n = n;
		result.cube = cube;
		return result;
	};

	// iPhone 6 Plus/6s Plus
	deciders.push(function (platform, pixelRatio, aspectRatio, osVersion, time, bench) {

		var result = undefined;

		if (platform === "iPhone" && (pr === "3.0" || pr === "3") && osVersion.major >= 8) {

			//  normal  ||  power-save mode
			if ((time >= 0 && time < 135) || (time > 170 && time < 220)) {
				if (osVersion >= 9) {
					// iPhone 6s Plus
					result = "61ba";
				} else {
					// iPhone 6 Plus
					result = "2c66";
				}
			} else if ((time >= 135 && time <= 170) || time >= 220){
				// iPhone 6 Plus
				result = "2c66";
			}
		}

		return result;
	});

	// iPhone 6/6s
	deciders.push(function (platform, pixelRatio, aspectRatio, osVersion, time, bench) {

		var result = undefined;

		if (platform === "iPhone" && (pixelRatio === "2.0" || pixelRatio === "2")) {
			if ((screen.availWidth === 375 && screen.availHeight === 647) ||
				(screen.availWidth === 667 && screen.availHeight === 375)) {

				//  normal  ||  power-save mode
				if ((time >= 0 && time < 135) || (time > 170 && time < 220)) {
					if (osVersion >= 9) {
						// iPhone 6s
						result = "5dde";
					} else {
						result = "a9b4";
					}
				} else if ((time >= 135 && time <= 170) || time >= 220) {
					// iPhone 6
					result = "a9b4";
				}
			}
		}

		return result;
	});

	// iPhone 6 Plus, 6S Plus, 6, 6S
	deciders.push(function (platform, pixelRatio, aspectRatio, osVersion, time, bench) {

		var result = undefined;

		if (platform === "iPhone" && (pr === "3.0" || pr === "3") && osVersion.major >= 8) {
			// iPhone 6 Plus/6s Plus
			result = "cdee";
		} else if (platform === "iPhone" && (pixelRatio === "2.0" || pixelRatio === "2")) {
			if ((screen.availWidth === 375 && screen.availHeight === 647) ||
				(screen.availWidth === 667 && screen.availHeight === 375)) {
				// iPhone 6/6s
				result = "33e4";
			}
		}

		return result;
	});

	// iPhone 5S
	deciders.push(function (platform, pixelRatio, aspectRatio, osVersion, time, bench) {

		var result = undefined;

		if (platform === "iPhone" && (pixelRatio === "2.0" || pixelRatio === "2")) {
			if (aspectRatio === "40/71") {
				if (osVersion.major <= 7) {
					if (time >= 0 && time <= 35) {
						result = "1bc3";
					}
				} else if (osVersion.major === 8) {
					if (time >= 0 && time <= 16) {
						result = "1bc3";
					}
				} else {
					if (time >= 0 && time <= 28) {
						result = "1bc3";
					}
				}
			}
		}

		return result;
	});

	// iPhone 5/5C
	deciders.push(function (platform, pixelRatio, aspectRatio, osVersion, time, bench) {

		var result = undefined;

		if (platform === "iPhone" && (pixelRatio === "2.0" || pixelRatio === "2")) {
			if (aspectRatio === "40/71") {
				if (osVersion.major === 6) {
					if (bench) {
						result = "e55d";
					} else {
						result = "9f1b";
					}
				} else if (osVersion.major === 7) {
					if (time > 35) {
						result = "9afe";
					}
				} else if (osVersion.major === 8) {
					if (time > 16) {
						result = "9afe";
					}
				} else {
					if (time > 28) {
						result = "9afe";
					}
				}
			}
		}

		return result;
	});

	// iPhone 5/5C/5S
	deciders.push(function (platform, pixelRatio, aspectRatio, osVersion, time, bench) {

		var result = undefined;

		if (platform === "iPhone" && (pixelRatio === "2.0" || pixelRatio === "2")) {
			if (aspectRatio === "40/71") {
				if (osVersion.major === 7) {
					if (time === -1) {
						result = "ffe7";
					}
				} else {
					if (!(screen.availWidth === 375 && screen.availHeight === 647) &&
						//!(screen.availWidth === 320 && screen.availHeight === 548) &&
						!(screen.availWidth === 667 && screen.availHeight === 375)) {
						result = "ffe7";
					}
				}
			}
		}

		return result;
	});

	// iPhone 3GS and older
	deciders.push(function (platform, pixelRatio, aspectRatio, osVersion, time, bench) {

		var result = undefined;

		if (platform === "iPhone") {
			if (osVersion.major <= 3) {
				result = "50d7";
			} else if (osVersion.major === 4) {
				if (pixelRatio === "1.0" || pixelRatio === "1") {
					result = "6e7b";
				}
			} else {
				if (pixelRatio === "1.0" || pixelRatio === "1") {
					result = "27ac";
				}
			}
		}

		return result;
	});

	// iPhone 4S
	deciders.push(function (platform, pixelRatio, aspectRatio, osVersion, time, bench) {

		var result = undefined;

		if (platform === "iPhone" && (pixelRatio === "2.0" || pixelRatio === "2")) {
			if (aspectRatio === "2/3") {
				if (osVersion.major === 5) {
					if (time >= 0 && time <= 155) {
						result = "cb24";
					}
				} else if (osVersion.major === 6 || osVersion.major === 7) {
					if (time >= 0 && time <= 120) {
						result = "cb24";
					}
				} else {
					if (bench) {
						result = "cb24";
					} else {
						result = "d7d5";
					}
				}
			}
		}

		return result;
	});

	// iPhone 4
	deciders.push(function (platform, pixelRatio, aspectRatio, osVersion, time, bench) {

		var result = undefined;

		if (platform === "iPhone" && (pixelRatio === "2.0" || pixelRatio === "2")) {
			if (osVersion.major <= 4) {
				if (bench) {
					result = "6b71";
				} else {
					result = "154e";
				}
			} else if (osVersion.major === 5) {
				if (time > 155) {
					result = "6b71";
				}
			} else if (osVersion.major > 5) {
				if (time > 120) {
					result = "6b71";
				}
			}
		}

		return result;
	});

	// iPhone 4/4S
	deciders.push(function (platform, pixelRatio, aspectRatio, osVersion, time, bench) {

		var result = undefined;

		if (platform === "iPhone" && (pixelRatio === "2.0" || pixelRatio === "2")) {
			if (aspectRatio === "2/3") {
				if (time === -1 || !bench) {
					result = "a11c";
				}
			}
		}

		return result; 
	});

	// iPad Pro
	deciders.push(function (platform, pixelRatio, aspectRatio, osVersion, time, bench) {

		var result = undefined;

		if (screen.availWidth === 1024 && screen.availHeight === 1346) {
			result = "229b";
		}

		return result;
	});

	// iPad 2/Mini
	deciders.push(function (platform, pixelRatio, aspectRatio, osVersion, time, bench) {

		var result = undefined;

		if (platform === "iPad" && (pixelRatio === "1.0" || pixelRatio === "1")) {
			if (osVersion.major <= 4) {
				if (time >= 0 && time <= 50) {
					result = "7a3d";
				}
			} else if (osVersion.major === 5) {
				if (time >= 0 && time <= 125) {
					result = "7a3d";
				}
			} else {
				result = "bb57";
			}
		}

		return result;
	});

	// iPad 1
	deciders.push(function (platform, pixelRatio, aspectRatio, osVersion, time, bench) {

		var result = undefined;

		if (platform === "iPad") {
			if (osVersion.major === 3) {
				if (bench) {
					result = "5766";
				} else {
					result = "e199";
				}
			} else if (osVersion.major === 4 && osVersion.minor < 3) {
				if (bench) {
					result = "5766";
				} else {
					result = "e199";
				}
			} else if (osVersion.major === 4 && osVersion.minor === 3) {
				if (time > 50) {
					result = "5766";
				}
			} else if (osVersion.major === 5) {
				if (pixelRatio === "1.0" || pixelRatio === "1") {
					if (time > 125) {
						result = "5766";
					}
				}
			}
		}

		return result;
	});

	// iPad 1/2
	deciders.push(function (platform, pixelRatio, aspectRatio, osVersion, time, bench) {

		var result = undefined;

		if (platform === "iPad" && (pixelRatio === "1.0" || pixelRatio === "1")) {
			if (osVersion.major === 4 && osVersion.minor === 3) {
				if (!bench) {
					result = "1e79";
				}
			} else if (osVersion.major === 5) {
				if (!bench) {
					result = "1e79";
				}
			}
		}

		return result;
	});

	// iPad Air 2/Mini 4
	deciders.push(function (platform, pixelRatio, aspectRatio, osVersion, time, bench) {

		var result = undefined;

		if (platform === "iPad" && (pixelRatio === "2.0" || pixelRatio === "2")) {

			if (osVersion.major === 8) {
				if (time >= 0 && time < 10) {
					result = "a40c"; // iPad Air 2
				}
			} else {				
				if (time >= 0 && time < 50) {
					result = "q8d4"; // iPad Air 2/Mini 4
				}
			}
		}

		return result;
	});

	// iPad Air/Mini 2/Mini 3
	deciders.push(function (platform, pixelRatio, aspectRatio, osVersion, time, bench) {

		var result = undefined;

		if (platform === "iPad" && (pixelRatio === "2.0" || pixelRatio === "2")) {
			if (osVersion.major <= 7) {
				if (time >= 0 && time <= 65) {
					result = "88a1";
				}
			} else if (osVersion.major === 8) {
				if (time >= 10 && time <= 27) {
					result = "88a1";
				}
			} else {
				if (time >= 50 && time <= 75) {
					result = "88a1";
				}
			}
		}

		return result;
	});

	// iPad 4
	deciders.push(function (platform, pixelRatio, aspectRatio, osVersion, time, bench) {

		var result = undefined;

		if (platform === "iPad" && (pixelRatio === "2.0" || pixelRatio === "2")) {
			if (osVersion.major <= 7) {
				if (time > 65 && time <= 130) {
					result = "58be";
				}
			} else if (osVersion.major === 8) {
				if (time > 27 && time <= 40) {
					result = "58be";
				}
			} else {
				if (time > 75 && time <= 150) {
					result = "58be";
				}
			}
		}

		return result;
	});

	// iPad 3
	deciders.push(function (platform, pixelRatio, aspectRatio, osVersion, time, bench) {

		var result = undefined;

		if (platform === "iPad" && (pixelRatio === "2.0" || pixelRatio === "2")) {
			if (osVersion.major === 5 && osVersion.minor === 1) {
				if (bench) {
					result = "4c04";
				} else {
					result = "162a";
				}
			} else if (osVersion.major === 6) {
				if (time > 110) {
					result = "4c04";
				}
			} else if (osVersion.major === 7) {
				if (time > 130) {
					result = "4c04";
				}
			} else if (osVersion.major === 8) {
				if (time > 40) {
					result = "4c04";
				}
			} else {
				if (time > 150) {
					result = "4c04";
				}
			}
		}

		return result;
	});

	// iPad 3/4/Air/Mini 2/Mini 3/Mini 4
	deciders.push(function (platform, pixelRatio, aspectRatio, osVersion, time, bench) {

		var result = undefined;

		if (platform === "iPad" && (pixelRatio === "2.0" || pixelRatio === "2")) {
			if (time === -1) {
				if (osVersion.major === 6) {
					result = "6630";
				} else if (osVersion.major === 7 && osVersion.minor === 0 && osVersion.build < 3) {
					result = "6630";
				} else {
					result = "2bf5";
				}
			}
		}

		return result;
	});

	cv = getCookie("nbhwident");
	if (typeof cv === "undefined") {
		osv = getOsVersion();
		pr = getPixelRatio();
		p = getPlatform();
		ar = getAspectRatio();

		settings = getSettings(p, pr, ar, osv);
		if (paraHwBench && settings.n !== 0 && (p === "iPhone" || p === "iPad") && !ch) {
			t = measureTime(settings.m, settings.n);
		} else if (paraHwBench && settings.n === 0 && settings.cube && (p === "iPhone" || p === "iPad") && !ch) {
			t = measureCubeTime();
		}

		ident = undefined;
		for (i = 0; i < deciders.length; i = i + 1) {
			var f = deciders[i];
			ident = f(p, pr, ar, osv, t, paraHwBench);
			if (typeof ident !== "undefined") {
				break;
			}
		}
		if (typeof ident === "undefined") {
			ident = "";
		}

		document.cookie = "nbhwident=" + ident;
	} else {
		pr = getPixelRatio();
		ar = getAspectRatio();
		t = -1;
		ident = cv;
	}

	if (typeof results.hardware === "undefined") {
		results.hardware = {};
	}
	results.hardware.os = osv;
	results.hardware.pr = pr;
	results.hardware.ar = ar;
	results.hardware.t = t;
	results.hardware.ident = ident;
	if (ident !== "") {
		nbParams += "&hwIdent=" + results.hardware.ident;
	}

	if (paraProfileDiff && ident !== "") {
		var diffs = dcs.get('profile.diffs');
		try {
			if (typeof diffs !== 'undefined') {
				for (i = 0; i < diffs.length; i = i + 1) {
					var cond = diffs[i].condition;
					var diff = diffs[i].diff;
					if ((typeof cond !== 'undefined' && typeof diff !== 'undefined') &&
						(cond.param.toLowerCase() === 'hwident') &&
						(cond.comp === '=' || cond.comp === '==') &&
						(cond.value === ident)) {
						for (var j = 0; j < diff.length; j = j + 1) {
							dcs.set(diff[j].name, diff[j].value);
						}
					}
				}
			}
		} catch (e) {
		}
	}
	if (nbParams.indexOf("nbpr") === -1) {
		nbParams += "&nbpr=" + pr;
	}

	// add dynamic parameters to static data
	if (typeof dcs !== "undefined" && typeof dcs.set === "function") {
		dcs.set("browser.pixelratio", pr);
		dcs.set("device.aspectratio", ar);
	}

	checkToSend();
};
/* global cookies */
/* global sessionId */
/* global requestSessionId */
/* global insertScriptTag */
analytics = (function () {

	// Script version for better debugging
	var odsVersion = '1.42.0-SNAPSHOT';

	// helpers for accessing url parameters
	/**
	 * Get the value for the given variable from the query string
	 * @param {string} variable Name of the variable the value should be extracted
	 * @returns {string} Value of the variable if it is available
	 */
	var getQueryVariable = function (variable) {
		var query = window.location.search.substring(1);
		if (query && query.indexOf(variable) > -1) {
			var vars = query.split('&');
			for (var i = 0; i < vars.length; i++) {
				var pair = vars[i].split('=');
				if (pair[0] === variable) {
					return pair[1];
				}
			}
		}
	};

	/**
	 * Add the value for parameter with <code>name</code> to the query string. Value comes from cookie or query string.
	 * @param {string} nbParams Existing query string
	 * @param {string} name Name of the parameter to be added
	 * @returns {string} Extended query string
	 */
	var updateOneParam = function (nbParams, name) {
		var tmp;
		tmp = cookies.getItem(name);
		if (!tmp) {
			tmp = getQueryVariable(name);
		}
		if (tmp) {
			nbParams = nbParams + '&' + name + '=' + tmp;
		}
		return nbParams;
	};

	/**
	 * Add or update the parameters usually present as cookie or in query string to existing query string
	 * @param {string} nbParams Existing query string to be updated or extended
	 * @returns {string} Extended/updated query string
	 */
	var updateParams = function (nbParams) {
		var params = nbParams;

		// The following will be added
		params = updateOneParam(params, 'emvcc');
		params = updateOneParam(params, 'emvAD');
		params = updateOneParam(params, 'nbcol');

		// This will only be added if not already present
		if (params.indexOf('nbpr') === -1) {
			params = updateOneParam(params, 'nbpr');
		}

		return params;
	};

	/**
	 * Additional default information to be transferred back to the server
	 * @return {string} Containing version info, JS Profile name and EndOfQuery symbol
	 */
	var getExtraRequestParams = function() {
		return  (sessionId ? '&nbasl=' + sessionId : '')
				+ (odsVersion ? '&ods=' + encodeURIComponent(odsVersion) : '')
				+ (dcs.dcsVersion ? '&dcs=' + encodeURIComponent(dcs.dcsVersion) : '')
				+ '&eoq';
	};

	// Public API
	return {

		/**
		 * send data back to analytics
		 * @param {string} nbParams Parameters to send
		 * @param {string} path URL to send the values to
		 * @param {string} callback Name of the callback function to call when server responds
		 */
		sendValues: function (nbParams, path, callback) {
			var script = document.createElement('script');
			script.src = path
						+ callback
						+ updateParams(nbParams)
						+ getExtraRequestParams();
			insertScriptTag(script);
		},

		/**
		 * Disable analytics session cookie
		 */
		disableAnalyticsSession: function () {
			var script = document.createElement('script');
			script.type = 'text/javascript';
			script.src = '//dci.dev.netbiscuits.com:80/ds/analytics/disableSession';
			insertScriptTag(script);
		},

		/**
		 * Send data on conversion(s) back to analytics
		 * @param {Object} conversionData customer defined conversion information
		 */
		trackConversion: function (conversionData) {

			// Only log objects
			if (typeof conversionData !== 'object') {
				return;
			}

			var script = document.createElement('script');
			script.type = 'text/javascript';

			script.src = '//dci.dev.netbiscuits.com:80/ds/analytics/log/conversion//dcsdemo/0b0524ec8c1c26c1dfc2529de12697920f27a156ab2ef19d0429a45b7d6ac02fc5c62ebb1f2983efea151571dfc17337f29bf1e969cde93c748dc975b6145ff3?conversion='
				+ encodeURIComponent(JSON.stringify(conversionData))
				+ '&rsid=' + requestSessionId
				+ nbParams
				+ getExtraRequestParams();

			insertScriptTag(script);
		}
	}
})();

		// here goes all the external stuff

		// ---------------------- HARDCODED SENSORS START ----------------------//

		// Only add tools.bwd if we have the required script snippet inserted above
		if (typeof quality !== 'undefined') {

			/**
			 * Wrapper function for bandwidth detection. It will provide a callback to bandwidth detection
			 * which then again calls the checkToSend function.
			 */
			tools.bwd = function () {

				var params = {
					url: paraImage
				};

				/**
				 * Callback called upon bandwidth detection being finished.
				 * @param {object} result Result of the detection
				 */
				function callBack(result) {
					results.bwd = {};
					results.bwd.bandwidthScore = result.score;
					results.bwd.bandwidthNetwork = encodeURIComponent(result.network.name);
					// add dynamic parameters to static data
					dcs.set('bandwidth.estimatednetwork', results.bwd.bandwidthNetwork);
					dcs.set('bandwidth.score', results.bwd.bandwidthScore);
					nbParams += '&bandwidthScore=' + results.bwd.bandwidthScore + '&bandwidthNetwork=' + results.bwd.bandwidthNetwork;
					checkToSend();
				}

				quality.speed(params).done(callBack);
			};
		}

		// Only add tools.geo if we have the required script snippet inserted above
		if (typeof nbgeo !== 'undefined') {

			/**
			 * Wrapper function for geolocation. It provides various callbacks to the geolocation itself as well as
			 * handling the case when geolocation could not be initialized.
			 */
			tools.geo = function () {

				var timedOut = false,
					timeoutId;

				/**
				 * Success callback
				 * @param {object} result
				 */
				function success_callback(result) {
					if (timedOut) {
						return;
					}
					clearTimeout(timeoutId);

					if (result.coords && result.coords.latitude && result.coords.longitude) {
						results.geo = {};
						results.geo.latitude = result.coords.latitude.toFixed(5);
						results.geo.longitude = result.coords.longitude.toFixed(5);

						// add dynamic parameters to static data
						dcs.set('location.latitude', results.geo.latitude);
						dcs.set('internal.latitude', results.geo.latitude);
						dcs.set('location.longitude', results.geo.longitude);
						dcs.set('internal.longitude', results.geo.longitude);

						nbParams += '&latitude=' + results.geo.latitude + '&longitude=' + results.geo.longitude;
					}

					if (!timedOut) {
						checkToSend();
					}
				}

				/**
				 * Error callback. This will only call back to checkToSend function
				 */
				function error_callback() {
					clearTimeout(timeoutId);
					if (!timedOut) {
						checkToSend();
					}
				}

				// Try to initialize the geolocation
				if (nbgeo.init()) {

					// Wait no longer than 5 seconds for a user decision
					timeoutId = setTimeout(function() {
						timedOut = true;
						checkToSend();
					}, 5000);

					nbgeo.getCurrentPosition(success_callback, error_callback, {enableHighAccuracy: true});

				// If initialization failed call back to checkToSend though
				} else {
					checkToSend();
				}
			};
		}

		/**
		 * Always on sensor for evaluating client's local time.
		 */
		tools.localtime = function () {
			var date = new Date();

			// Timezone offset is effectively added here (as it is negative for GMT+x and positive for GMT-x)
			// This is because date.getTime() delivers a UTC Unix timestamp and it will be decoded on the server
			// as UTC. This would result in the wrong localtime (e.g. GMT+2 would result in two hours earlier).
			// Therefore adding the offset will modify the UTC timestamp to be equivalent to local time.
			// Example:
			// Localtime GMT+2 10:42:00 -> 08:42:00 GMT/UTC -> add 120 Minutes -> 10:42:00 GMT/UTC -> server decodes that
			// localtime on device was 10:42:00 ignoring any timezone.
			nbParams += '&localtime=' + (date.getTime() - (date.getTimezoneOffset() * 60000));
			checkToSend();
		};

		/**
		 * Always on sensor for evaluating clients current screensize
		 */
		tools.screensize = function () {
			var checkSizeVariable = function(v) {
				return v && typeof(v) === 'number' && v > 0;
			};

			var sensor = function (results) {
				var w, h;
				if (checkSizeVariable(window.innerWidth) && checkSizeVariable(window.innerHeight)) {
					w = window.innerWidth;
					h = window.innerHeight;
				} else if (document.documentElement && checkSizeVariable(document.documentElement.clientWidth) && checkSizeVariable(document.documentElement.clientHeight)) {
					w = document.documentElement.clientWidth;
					h = document.documentElement.clientHeight;
				} else if (document.body && checkSizeVariable(document.body.clientWidth) && checkSizeVariable(document.body.clientHeight)) {
					w = document.body.clientWidth;
					h = document.body.clientHeight;
				} else if (checkSizeVariable(window.screen.availWidth) && checkSizeVariable(window.screen.availHeight)) {
					w = window.screen.availWidth;
					h = window.screen.availHeight;
				}
				results.browserusablewidth = w;
				results.browserusableheight = h;
				results.screenorientation = undefined;
				if (results.browserusablewidth <= results.browserusableheight) {
					results.screenorientation = 'portrait';
				} else {
					results.screenorientation = 'landscape';
				}

				// add dynamic parameters to static data
				dcs.set('browser.usablewidth', results.browserusablewidth);
				dcs.set('browser.usableheight', results.browserusableheight);
				if (results.screenorientation !== undefined) {
					dcs.set('device.screen.orientation', results.screenorientation);
				}
			};

			sensor(results);

			try {
				if ("onresize" in window) {
					addEventListener("resize", function () {
						sensor(results);
					}, true);
				}
				else if ("onorientationchange" in window) {
					addEventListener("orientationchange", function () {
						sensor(results);
					}, true);
				}
			}
			catch (e) {
			}

			nbParams += '&buw=' + results.browserusablewidth;
			nbParams += '&buh=' + results.browserusableheight;

			if (results.browserusablewidth > 10000) {
				var d = "";
				try {
					d += "&wiw=";
					d += window.innerWidth;
					d += "&wih=";
					d += window.innerHeight;
				} catch (ignore) {}
				try {
					d += "&dcw=";
					d += document.documentElement.clientWidth;
					d += "&dch=";
					d += document.documentElement.clientHeight;
				} catch (ignore) {}
				try {
					d += "&dbw=";
					d += document.body.clientWidth;
					d += "&dbh=";
					d += document.body.clientHeight;
				} catch (ignore) {}
				try {
					d += "&saw=";
					d += window.screen.availWidth;
					d += "&sah=";
					d += window.screen.availHeight;
				} catch (ignore) {}
				nbParams += d;
			}

			checkToSend();
		};
		// ---------------------- HARDCODED SENSORS END ----------------------//

		// count sensors
		for (var tool in tools) {
			if (tools.hasOwnProperty(tool)) {
				allToolsCount += 1;
			}
		}

		// Disable analytics session if the user wants it that way
		if (disableAnalyticsSessionFlag) {
			dcs.dynamic.disableAnalyticsSession();
		}

		/**
		 * Callback for all sensors. It will check whether all sensors have called back
		 * before performing the analytics callback or evaluation custom clusters.
		 */
		function checkToSend() {
			finishedSensorsCount += 1;
			if (toolsToExecuteCount === finishedSensorsCount) {
				// Reference the result output object
				dcs.dynamic.result = results;
				sendAndCallback();
			}
		}

		/**
		 * Add a sensor to the array of sensors and increase the total count of sensors
		 * @param {String} sensorName Name of the sensor to add
		 */
		function addSensor(sensorName) {
			toolsToExecuteCount += 1;
			paraSensors.push(sensorName);
		}

		// No sensors requested from outside -> use all available
		if (!paraSensors) {

			// Initialize paraSensors as new array
			paraSensors = [];

			// always-on sensors
			addSensor('localtime');
			addSensor('screensize');

			if (tools.hardware) {
				addSensor('hardware');
			}
			if (tools.battery) {
				addSensor('battery');
			}
			if (tools.pr) {
				addSensor('pr');
			}
			if (tools.cs) {
				addSensor('cs');
			}

			// Sensors for bandwidth and geolocation need to be last
			if (tools.bwd) {
				addSensor('bwd');
			}
			if (tools.geo) {
				addSensor('geo');
			}
			if (tools.benchmark) {
				addSensor('benchmark');
			}

			// User requested specific sensors
		} else {

			// Remove provided sensors that are not present
			var filteredSensors = [];
			for (var i = 0; i < paraSensors.length; i++) {
				if (tools.hasOwnProperty(paraSensors[i])) {
					filteredSensors.push(paraSensors[i]);
				}
			}
			paraSensors = filteredSensors;
			toolsToExecuteCount = paraSensors.length;

			var containsLocaltime = false;
			var containsScreensize = false;
			var containsbenchmark = false;
			for (var j = 0; j < paraSensors.length; j++) {
				if (paraSensors[j] === 'localtime') {
					containsLocaltime = true;
				} else if (paraSensors[j] === 'screensize') {
					containsScreensize = true;
				} else if (paraSensors[j] === 'benchmark') {
					containsbenchmark = true;
				}
			}

			// always add localtime sensor
			if (!containsLocaltime) {
				addSensor('localtime');
			}

			// always add screensize sensor
			if (!containsScreensize) {
				addSensor('screensize');
			}

			// always add benchmark sensor if present
			if (tools.benchmark && !containsbenchmark) {
				addSensor('benchmark');
			}
		}

		// check if there is any parameter and if there are not more parameters than is possible
		if (toolsToExecuteCount > 0 && toolsToExecuteCount <= allToolsCount) {

			// iterate through all arguments
			for (var k = 0; k < toolsToExecuteCount; k++) {

				// check if we have appropriate method and execute
				if (tools[paraSensors[k]]) {
					tools[paraSensors[k]]();
				}
			}

		// No sensors found
		} else if (toolsToExecuteCount === 0) {
			sendAndCallback();
		}

		/**
		 * Send data back to analytics if possible.
		 * Call custom callback if no custom clusters are requested.
		 * Start fingerprinting with a short delay to not interfer with custom callback.
		 */
		function sendAndCallback() {

			// Send to analytics if we have the measures to do so
			if (analytics && typeof analytics.sendValues === 'function') {
				analytics.sendValues(nbParams, path, callback);
			}

			// Call the client callback if it is present and we do not have to wait for custom cluster results
			if (dcs.dynamic.clientCallback && !customClusters) {
				dcs.dynamic.clientCallback();
			}

			// Start fingerprinting with a short delay
			if (typeof fingerprint !== 'undefined') {
				setTimeout(function () {
					fingerprint.generate(fpCallback);
				}, 100);
			}
		}
	}

	// Public functions and members
	return {

		/**
		 * Initialize and run the sensors
		 * @param {object} args Options for client-side detection
		 */
		init: function (args) {
			run(args);
		},

		/**
		 * Save data on custom clusters in result object and callback to server to evaluate custom clusters
		 * @param {
		 *  {
	     *      result: string,
	     *      cluster_ : Array.<string>,
		 *      requestSessionId: Array.<string>,
	     *      asid: Array.<string>,
	     *      autoConversionConfig: Array.<{type: string, name: string, value: number, time: number}>,
	     *      nbvid: string,
	     *      nbdid: string
	     *  }
	     * } data Custom cluster data
		 */
		processCluster: function (data) {
			if (data) {

				// Create a new object for filtering the returned values
				var result = {};

				for (var property in data) {

					if (data.hasOwnProperty(property)) {

						// Use only properties that are cluster lables (i.e. start with 'cluster_')
						if (property.indexOf('cluster_') === 0) {
							result[property] = data[property];

						// Or the requestSessionId
						} else if (property === 'requestSessionId') {
							requestSessionId = data.requestSessionId[0];

						// Autoconversion configuration
						} else if (property === 'autoConversionConfig') {

							// Initialize asynchronously
							setTimeout(function() {
								for (var i = 0; i < data.autoConversionConfig.length; ++i) {
									var setting = data.autoConversionConfig[i];
									if (autoConversion[setting.type]) {
										autoConversion[setting.type].init(setting);
									}
								}
							}, 0);
						}
					}
				}
				dcs.dynamic.cluster = result;
			}

			// Log conversions that could not be logged earlier - even if there still is no requestSessionId
			var conversion = conversionsToLog.shift();
			while (conversion) {

				// Use the analytics version of the function to avoid an endless loop
				analytics.trackConversion(conversion);
				conversion = conversionsToLog.shift();
			}

			// call client callback function after all data has been gathered and we have cluster eval results
			if (dcs.dynamic.clientCallback && customClusters) {
				dcs.dynamic.clientCallback();
			}
		},

		/**
		 * Disable analytics session cookie
		 */
		disableAnalyticsSession: function () {
			if (analytics && typeof analytics.disableAnalyticsSession === 'function') {
				analytics.disableAnalyticsSession();
			}
		},

		/**
		 * Send data on conversion(s) back to analytics
		 * @param {Object} conversionData customer defined conversion information
		 */
		trackConversion: function (conversionData) {

			// Check if analytics is enabled (otherwise return silently)
			if (analytics && typeof analytics.trackConversion === 'function') {

				// Log imediately if we have a requestSessionId, else store it for later
				if (requestSessionId) {
					analytics.trackConversion(conversionData);
				} else {
					conversionsToLog.push(conversionData);
				}
			}
		},

		/**
		 * Call an optional callback that provides information on the just logged conversion event
		 * @param {Object} conversionData Conversion event that has been logged on the server
		 */
		conversionTracked: function(conversionData) {
			conversionCallback(conversionData);
		},

		/**
		 * Get the NB visitor Id consisting of visitorId, deviceId and a list of user aliases
		 * @return {
		 *  {
		 *      visitorId: (string|undefined),
		 *      visitorIdStable: boolean,
		 *      deviceId: (string|undefined),
		 *      deviceIdStable: boolean,
		 *      visitorAliases: Array.<string>
		 *   }
		 * }
		 */
		getVisitorId: function() {
			return {
				visitorId: visitorId,
				visitorIdStable: visitorIdStable,
				deviceId: deviceId,
				deviceIdStable: deviceIdStable,
				visitorAliases: visitorAliases
			};
		}
	};
})();