!function(){tinymce.PluginManager.requireLangPack("hcalendar"),tinymce.create("tinymce.plugins.hCalendarPlugin",{init:function(n,e){n.addCommand("eventDialog",function(){n.windowManager.open({file:e+"/dialog.html",width:415+parseInt(n.getLang("hcalendar.delta_width",0)),height:370+parseInt(n.getLang("hcalendar.delta_height",0)),inline:1},{plugin_url:e})}),n.addButton("hcalendar",{title:"hcalendar.desc",cmd:"eventDialog",image:e+"/img/calendar_button.gif"}),n.onNodeChange.add(function(n,e,a){e.setActive("hcalendar",tinymce.DOM.hasClass(a.parentNode.parentNode,"vevent")||tinymce.DOM.hasClass(a.parentNode,"vevent")||tinymce.DOM.hasClass(a,"vevent"))})},createControl:function(n,e){return null},getInfo:function(){return{longname:"hCalendar plugin",author:"Alex Willemsma",authorurl:"http://www.undergroundwebdesigns.com",infourl:"http://www.undergroundwebdesigns.com/tinyMCE-hcalendar-plugin.html",version:"0.2"}}}),tinymce.PluginManager.add("hcalendar",tinymce.plugins.hCalendarPlugin)}();