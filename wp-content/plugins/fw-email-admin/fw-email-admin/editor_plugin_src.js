/**
 * $Id: editor_plugin_src.js 201 2007-02-12 15:56:56Z spocke $
 *
 * @author Alex Willemsma, UndergroundWebDesigns
 * @copyright Copyright © 2008, Underground Web Designs
 
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

(function() {
	// Load plugin specific language pack
	tinymce.PluginManager.requireLangPack('hcard');

	tinymce.create('tinymce.plugins.hCardPlugin', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {
			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');
			ed.addCommand('hcardDialog', function() {
				ed.windowManager.open({
					file : url + '/dialog.htm',
					width : 435 + parseInt(ed.getLang('hcard.delta_width', 0)),
					height : 420 + parseInt(ed.getLang('hcard.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : url, // Plugin absolute URL
				});
			});

			// Register example button
			ed.addButton('hcard', {
				title : 'hcard.desc',
				cmd : 'hcardDialog',
				image : url + '/img/hcard_button.gif'
			});

			// Add a node change handler, selects the button in the UI when a image is selected
			ed.onNodeChange.add(function(ed, cm, n) { 
				cm.setActive('hcard', (tinymce.DOM.hasClass(n.parentNode.parentNode,'vcard')||tinymce.DOM.hasClass(n.parentNode,'vcard')||tinymce.DOM.hasClass(n,'vcard')));
			});
		},

		/**
		 * Creates control instances based in the incomming name. This method is normally not
		 * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
		 * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
		 * method can be used to create those.
		 *
		 * @param {String} n Name of the control to create.
		 * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
		 * @return {tinymce.ui.Control} New control instance or null if no control was created.
		 */
		createControl : function(n, cm) {
			return null;
		},

		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
				longname : 'hCard plugin',
				author : 'Alex Willemsma',
				authorurl : 'http://www.undergroundwebdesigns.com',
				infourl : 'http://www.undergroundwebdesigns.com',
				version : "0.1"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('hcard', tinymce.plugins.hCardPlugin);
})();