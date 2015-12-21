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

		// Get the selected contents as text and place it in the input
		selectedElement = tinyMCEPopup.editor.selection.getNode();
		if (selectedElement != null) var parent = ancestors (selectedElement);
		if (parent != null) 
		{
			getData (parent);
		}
		else
		{
			f.givenName.value = tinyMCEPopup.editor.selection.getContent({format : 'text'});
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


function ancestors (startNode)
{
	if (parents = tinymce.DOM.getParent(startNode.parentNode, 'div'))
	{
		if (tinymce.DOM.hasClass(parents, 'vcard'))
		{
			return parents;
		}
		else
		{
			ancestors (parents);
		}
	}
	return parents;
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
				case "url fn n":
				if (tinymce.DOM.getAttrib(parent.childNodes[node], 'href').indexOf ('aim:goim?screenname=') != -1)
				{
					document.getElementById('aim').value = unescape(tinymce.DOM.getAttrib(parent.childNodes[node], 'href').replace (/aim:goim\?screenname=/i, ''));
				}
				else if (tinymce.DOM.getAttrib(parent.childNodes[node], 'href').indexOf ('ymsgr:sendIM?') != -1)
				{
					document.getElementById('yim').value = unescape(tinymce.DOM.getAttrib(parent.childNodes[node], 'href').replace (/ymsgr:sendIM\?/i, ''));
				}
				else if (tinymce.DOM.getAttrib(parent.childNodes[node], 'href').indexOf ('xmpp:') != -1)
				{
					document.getElementById('jabber').value = unescape (tinymce.DOM.getAttrib(parent.childNodes[node], 'href').replace (/xmpp:/i, ''));
				}
				else if (tinymce.DOM.getAttrib(parent.childNodes[node], 'href').indexOf ('skype:') != -1)
				{
					document.getElementById('skype').value = tinymce.DOM.getAttrib(parent.childNodes[node], 'href').substr(6);
					document.getElementById('skype').value = unescape (document.getElementById('skype').value.substr (0,document.getElementById('skype').value.length - 4));
				}
				else
				{
					document.getElementById('url').value = tinymce.DOM.getAttrib(parent.childNodes[node], 'href');
				}
				break;

				case "given-name":
				document.getElementById('givenName').value = parent.childNodes[node].firstChild.nodeValue;
				break;

				case "additional-name":
				document.getElementById('middleName').value = parent.childNodes[node].firstChild.nodeValue;
				break;

				case "family-name":
				document.getElementById('familyName').value = parent.childNodes[node].firstChild.nodeValue;
				break;

				case "org":
				document.getElementById('organization').value = parent.childNodes[node].firstChild.nodeValue;
				break;

				case "photo":
				document.getElementById('photoURL').value = tinymce.DOM.getAttrib(parent.childNodes[node], 'src');
				break;

				case "note":
				document.getElementById('note').value = parent.childNodes[node].firstChild.nodeValue;
				break;

				case "street-address":
				document.getElementById('street').value = parent.childNodes[node].firstChild.nodeValue;
				break;

				case "locality":
				document.getElementById('city').value = parent.childNodes[node].firstChild.nodeValue;
				break;

				case "region":
				document.getElementById('state').value = parent.childNodes[node].firstChild.nodeValue;
				break;

				case "postal-code":
				document.getElementById('postalCode').value = parent.childNodes[node].firstChild.nodeValue;
				break;

				case "country-name":
				document.getElementById('country').value = parent.childNodes[node].firstChild.nodeValue;
				break;

				case "tel":
				var pref = '';
				var prefType = '';
				for (var telChildren in parent.childNodes[node].childNodes)
				{
					if (tinymce.DOM.getAttrib(parent.childNodes[node].childNodes[telChildren], 'class') == 'type')
					{
						if (tinymce.DOM.getAttrib(parent.childNodes[node].childNodes[telChildren], 'title') == 'pref')
						{
							pref = true;
						}
						else
						{
							prefType = parent.childNodes[node].childNodes[telChildren].firstChild.nodeValue.toLowerCase();
						}
					}
				}
				if (prefType == 'cell') prefType = 'mobile';
				if (prefType == 'work') prefType = 'business';

				if (prefType != '')
				{
					nodeTextArray = scrapeText(parent.childNodes[node], true);
					endDes = nodeTextArray[nodeTextArray.length-1].indexOf (' ');
					phoneNumber = nodeTextArray[nodeTextArray.length-1].substr (endDes+1);
					document.getElementById(prefType+"Phone").value = phoneNumber;
				}
				if (pref)
				{
					for (i=0;i<document.forms[0].phonePref.length;i++)
					{
						if (document.forms[0].phonePref[i].value == prefType)
						{
							document.forms[0].phonePref[i].checked = true;
						}
					}
				}
				break;

				case "email":
				document.getElementById('email').value = tinymce.DOM.getAttrib(parent.childNodes[node], 'href').replace(/mailto:/i, '');
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

function codeit() {

	/* get values of text fields */
	var givenName        = document.getElementById('givenName').value;
	var middleName        = document.getElementById('middleName').value;
	var familyName        = document.getElementById('familyName').value;
	var organization        = document.getElementById('organization').value;
	var photoURL        = document.getElementById('photoURL').value;
	var note        = document.getElementById('note').value;

	var tags        = document.getElementById('tags').value;

	var street        = document.getElementById('street').value;
	var city        = document.getElementById('city').value;
	var state        = document.getElementById('state').value;
	var postalCode        = document.getElementById('postalCode').value;
	var country        = document.getElementById('country').value;

	var homePhone        = document.getElementById('homePhone').value;
	var businessPhone        = document.getElementById('businessPhone').value;
	var mobilePhone        = document.getElementById('mobilePhone').value;

	for (i=0;i<document.forms[0].phonePref.length;i++)
	{
		if (document.forms[0].phonePref[i].checked)
		{
			var phonePref = document.forms[0].phonePref[i].value;
		}
	}

	var email        = document.getElementById('email').value;
	var url        = document.getElementById('url').value;
	var aim        = document.getElementById('aim').value;
	var yim        = document.getElementById('yim').value;
	var jabber        = document.getElementById('jabber').value;
	var skype        = document.getElementById('skype').value;


	/* set results field */
	if (!givenName && !familyName && organization)
	{
		var name = organization;
		var isOrg = true;
	}
	else
	{
		var name = givenName;
		if (middleName) name += " "+middleName;
		if (familyName) name += " "+familyName;
		var isOrg = false;
	}

	var result_div = DIV(
		{'class' : 'vcard', 'id' : (givenName != '' ? 'hcard-' + name.replace(/ /g, '-') : '')}
	);

if (photoURL && photoURL.match(/http:\/\/.*\..{2,4}.*/)) {
	appendChildNodes (
		result_div, IMG({'style' : 'float:left; margin-right:4px','class':'photo', 'src' : photoURL, 'alt' : 'Photo of '+name})
	);
}

if(url && url.match(/http:\/\/.*\..{2,4}.*/)) {
	content_node = A({'class' : 'url fn n', 'href' : url});
	appendChildNodes(result_div, content_node);
} else {
	var content_node = SPAN({'class' : 'fn n'});
	appendChildNodes(result_div, content_node);
}
if (!isOrg)
{
	appendChildNodes(
		content_node, SPAN({'class' : 'given-name'}, givenName)
	);
	appendChildNodes(
		content_node, ' '
	);
	appendChildNodes(
		content_node, SPAN({'class' : 'additional-name'}, middleName)
	);
	appendChildNodes(
		content_node, ' '
	);
	appendChildNodes(
		content_node, SPAN({'class' : 'family-name'}, familyName)
	);

	if (organization) {
		appendChildNodes (
			result_div, DIV({'class' : 'org'}, organization)
		);
	}
}
else
{
	appendChildNodes (
		content_node, DIV({'class' : 'org'}, organization)
	);
}

if (email) {
	emailNode = A({'class' : 'email', 'href' : 'mailto:'+email},email);
	appendChildNodes (result_div, emailNode);
}

if (homePhone) {
	contentNode =  DIV({'class' : 'tel'})
	appendChildNodes (result_div, contentNode);

	phoneNode = SPAN({'class' : 'type'}, 'Home');
	appendChildNodes (contentNode, phoneNode);

	if (phonePref == 'home')
	{
		prefNode = ABBR({'class' : 'type', 'title':'pref'},'(Preferred): ');
		appendChildNodes (contentNode, prefNode);
	}
	else
	{
		appendChildNodes (contentNode, ': ');
	}
	appendChildNodes (contentNode, homePhone);	  
}

if (businessPhone) {
	contentNode =  DIV({'class' : 'tel'})
	appendChildNodes (result_div, contentNode);

	phoneNode = SPAN({'class' : 'type'}, 'Work');
	appendChildNodes (contentNode, phoneNode);

	if (phonePref == 'business')
	{
		prefNode = ABBR({'class' : 'type', 'title':'pref'},'(Preferred): ');
		appendChildNodes (contentNode, prefNode);
	}
	else
	{
		appendChildNodes (contentNode, ': ');
	}
	appendChildNodes (contentNode, businessPhone);  
}

if (mobilePhone) {
	contentNode =  DIV({'class' : 'tel'})
	appendChildNodes (result_div, contentNode);

	phoneNode = SPAN({'class' : 'type'}, 'Cell');
	appendChildNodes (contentNode, phoneNode);

	if (phonePref == 'mobile')
	{
		prefNode = ABBR({'class' : 'type', 'title':'pref'},'(Preferred): ');
		appendChildNodes (contentNode, prefNode);
	}
	else
	{
		appendChildNodes (contentNode, ': ');
	}
	appendChildNodes (contentNode, mobilePhone);  
}

if (street || city || state || postalCode || country) {
	contentNode = DIV({'class' : 'adr'});
	appendChildNodes (result_div, contentNode);

	if (street)
	{
		adrNode = DIV({'class' : 'street-address'}, street);
		appendChildNodes (contentNode, adrNode);
	}
	if (city)
	{
		adrNode = SPAN({'class' : 'locality'}, city);
		appendChildNodes (contentNode, adrNode);
		if ( state || postalCode || country)
		{
			appendChildNodes (contentNode, ', ');
		}
	}
	if (state)
	{
		adrNode = SPAN({'class' : 'region'}, state);
		appendChildNodes (contentNode, adrNode);
		if (postalCode || country)
		{
			appendChildNodes (contentNode, ', ');
		}
	}
	if (country)
	{
		adrNode = SPAN({'class' : 'country-name'}, country);
		appendChildNodes (contentNode, adrNode);
		if (postalCode)
		{
			appendChildNodes (contentNode, ' ');
		}
	}
	if (postalCode)
	{
		adrNode = SPAN({'class' : 'postal-code'}, postalCode);
		appendChildNodes (contentNode, adrNode);
	}
}

if (aim) {
	imNode =  A({'class' : 'url', 'href' : 'aim:goim?screenname='+aim},aim)
	contentNode = DIV({'class' : 'im'},'AIM: ');
	appendChildNodes (result_div, contentNode);
	appendChildNodes (contentNode, imNode);
}
if (yim) {
	imNode =  A({'class' : 'url', 'href' : 'ymsgr:sendIM?'+yim},yim)
	contentNode = DIV({'class' : 'im'},'Yahoo Messenger: ');
	appendChildNodes (result_div, contentNode);
	appendChildNodes (contentNode, imNode);
}
if (jabber) {
	imNode =  A({'class' : 'url', 'href' : 'xmpp:'+jabber},jabber)
	contentNode = DIV({'class' : 'im'},'Jabber: ');
	appendChildNodes (result_div, contentNode);
	appendChildNodes (contentNode, imNode);
}
if (skype) {
	imNode =  A({'class' : 'url', 'href' : 'skype:'+skype+'?add'},skype)
	contentNode = DIV({'class' : 'im'},'Skype: ');
	appendChildNodes (result_div, contentNode);
	appendChildNodes (contentNode, imNode);
}

if (note) {
	contentNode =  P({'class' : 'note'},note)
	appendChildNodes (result_div, contentNode);
}

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

function tabChange (showTab)
{
	switch (showTab)
	{
		case 'basic':
		document.getElementById('basic-tab').className = '';
		document.getElementById('address-tab').className = 'hidden';
		document.getElementById('contact-tab').className = 'hidden';
		break;
		case 'address':
		document.getElementById('basic-tab').className = 'hidden';
		document.getElementById('address-tab').className = '';
		document.getElementById('contact-tab').className = 'hidden';
		break;
		case 'contact':
		document.getElementById('basic-tab').className = 'hidden';
		document.getElementById('address-tab').className = 'hidden';
		document.getElementById('contact-tab').className = '';
		break;
	}
	return false;
}
