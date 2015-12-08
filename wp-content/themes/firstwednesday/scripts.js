// Modernizr 2.0.6 (Custom Build) | MIT & BSD; Contains: fontface | borderradius | iepp | cssclasses | teststyles | testprop | testallprops | domprefixes
;window.Modernizr=function(a,b,c){function B(a,b){var c=a.charAt(0).toUpperCase()+a.substr(1),d=(a+" "+n.join(c+" ")+c).split(" ");return A(d,b)}function A(a,b){for(var d in a)if(k[a[d]]!==c)return b=="pfx"?a[d]:!0;return!1}function z(a,b){return!!~(""+a).indexOf(b)}function y(a,b){return typeof a===b}function x(a,b){return w(prefixes.join(a+";")+(b||""))}function w(a){k.cssText=a}var d="2.0.6",e={},f=!0,g=b.documentElement,h=b.head||b.getElementsByTagName("head")[0],i="modernizr",j=b.createElement(i),k=j.style,l,m=Object.prototype.toString,n="Webkit Moz O ms Khtml".split(" "),o={},p={},q={},r=[],s=function(a,c,d,e){var f,h,j,k=b.createElement("div");if(parseInt(d,10))while(d--)j=b.createElement("div"),j.id=e?e[d]:i+(d+1),k.appendChild(j);f=["&shy;","<style>",a,"</style>"].join(""),k.id=i,k.innerHTML+=f,g.appendChild(k),h=c(k,a),k.parentNode.removeChild(k);return!!h},t,u={}.hasOwnProperty,v;!y(u,c)&&!y(u.call,c)?v=function(a,b){return u.call(a,b)}:v=function(a,b){return b in a&&y(a.constructor.prototype[b],c)};var C=function(a,c){var d=a.join(""),f=c.length;s(d,function(a,c){var d=b.styleSheets[b.styleSheets.length-1],g=d.cssRules&&d.cssRules[0]?d.cssRules[0].cssText:d.cssText||"",h=a.childNodes,i={};while(f--)i[h[f].id]=h[f];e.fontface=/src/i.test(g)&&g.indexOf(c.split(" ")[0])===0},f,c)}(['@font-face {font-family:"font";src:url("https://")}'],["fontface"]);o.borderradius=function(){return B("borderRadius")},o.fontface=function(){return e.fontface};for(var D in o)v(o,D)&&(t=D.toLowerCase(),e[t]=o[D](),r.push((e[t]?"":"no-")+t));w(""),j=l=null,a.attachEvent&&function(){var a=b.createElement("div");a.innerHTML="<elem></elem>";return a.childNodes.length!==1}()&&function(a,b){function s(a){var b=-1;while(++b<g)a.createElement(f[b])}a.iepp=a.iepp||{};var d=a.iepp,e=d.html5elements||"abbr|article|aside|audio|canvas|datalist|details|figcaption|figure|footer|header|hgroup|mark|meter|nav|output|progress|section|summary|time|video",f=e.split("|"),g=f.length,h=new RegExp("(^|\\s)("+e+")","gi"),i=new RegExp("<(/*)("+e+")","gi"),j=/^\s*[\{\}]\s*$/,k=new RegExp("(^|[^\\n]*?\\s)("+e+")([^\\n]*)({[\\n\\w\\W]*?})","gi"),l=b.createDocumentFragment(),m=b.documentElement,n=m.firstChild,o=b.createElement("body"),p=b.createElement("style"),q=/print|all/,r;d.getCSS=function(a,b){if(a+""===c)return"";var e=-1,f=a.length,g,h=[];while(++e<f){g=a[e];if(g.disabled)continue;b=g.media||b,q.test(b)&&h.push(d.getCSS(g.imports,b),g.cssText),b="all"}return h.join("")},d.parseCSS=function(a){var b=[],c;while((c=k.exec(a))!=null)b.push(((j.exec(c[1])?"\n":c[1])+c[2]+c[3]).replace(h,"$1.iepp_$2")+c[4]);return b.join("\n")},d.writeHTML=function(){var a=-1;r=r||b.body;while(++a<g){var c=b.getElementsByTagName(f[a]),d=c.length,e=-1;while(++e<d)c[e].className.indexOf("iepp_")<0&&(c[e].className+=" iepp_"+f[a])}l.appendChild(r),m.appendChild(o),o.className=r.className,o.id=r.id,o.innerHTML=r.innerHTML.replace(i,"<$1font")},d._beforePrint=function(){p.styleSheet.cssText=d.parseCSS(d.getCSS(b.styleSheets,"all")),d.writeHTML()},d.restoreHTML=function(){o.innerHTML="",m.removeChild(o),m.appendChild(r)},d._afterPrint=function(){d.restoreHTML(),p.styleSheet.cssText=""},s(b),s(l);d.disablePP||(n.insertBefore(p,n.firstChild),p.media="print",p.className="iepp-printshim",a.attachEvent("onbeforeprint",d._beforePrint),a.attachEvent("onafterprint",d._afterPrint))}(a,b),e._version=d,e._domPrefixes=n,e.testProp=function(a){return A([a])},e.testAllProps=B,e.testStyles=s,g.className=g.className.replace(/\bno-js\b/,"")+(f?" js "+r.join(" "):"");return e}(this,this.document);
// debugging bugger
var echo = (typeof(console)=='undefined') ? alert : console.log;
// start ATG object
var ATG = {
	init : function() { // starter functions
		ATG.GMaps.init();
	},
	removehtml : function(str){ //	removes HTML from string
		if (str === null || str === '') {return;}
		str = str.replace(/&(lt|gt);/g, function(strMatch, p1){return (p1 == 'lt') ? '<' : '>';});
		str = str.replace(/<\/?[^>]+(>|$)/g,'');
		return str;
	},
	GMaps : {
		init : function() { // modify to look for Google Map links in Post, convert to IFRAME
			var A = document.getElementsByTagName('article')[0].getElementsByTagName('div')[0].getElementsByTagName('a'),
				a = '', h = '', i, l, len = A.length, p;
			for (i = -1; ++i < len;) {
				a = A[i];
				// http://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=151+W+46th+St,+New+York,+NY+10036&amp;sll=40.760957,-73.985032&amp;sspn=0.221299,0.317574&amp;gl=us&amp;ie=UTF8&amp;hq=&amp;hnear=151+W+46th+St,+New+York,+10036&amp;ll=40.75831,-73.984079&amp;spn=0.006916,0.009924&amp;t=h&amp;z=17
				// http://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=151+W+46th+St,+New+York,+NY+10036&amp;sll=40.760957,-73.985032&amp;sspn=0.221299,0.317574&amp;gl=us&amp;ie=UTF8&amp;hq=&amp;hnear=151+W+46th+St,+New+York,+10036&amp;ll=40.75831,-73.984079&amp;spn=0.006916,0.009924&amp;t=h&amp;z=14&amp;output=embed
				// http://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=151+W+46th+St,+New+York,+NY+10036&amp;sll=40.760957,-73.985032&amp;sspn=0.221299,0.317574&amp;gl=us&amp;ie=UTF8&amp;hq=&amp;hnear=151+W+46th+St,+New+York,+10036&amp;ll=40.75831,-73.984079&amp;spn=0.006916,0.009924&amp;t=h&amp;z=14&amp;output=embed
				if (a.href.indexOf('maps.google.com') > 0) {
					l = a.href.split('z=')[0]+'z=14&amp;output=embed&amp;iwloc=near';
					p = a.parentNode;
					p.removeChild(a);
					break;
				}
			}
			h += '<div id="google-map">';
			h += '<iframe width="250" height="250" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="'+l+'"></iframe><br />';
			h += '<small><a href="'+l+'" target="_blank">View Larger Map</a></small>';
			h += '</div>';
			p.innerHTML = h+p.innerHTML;
		}
	}/*,
	GMaps : {
		init : function() { // modify to look for Google Map links in Post, convert to IFRAME
			if (!document.getElementById('event')) {return false;}
			var p = document.getElementById('event'),
				s = p.getElementsByTagName('address')[0].getElementsByTagName('span'),
				h = '',
				a = '', i, len = s.length;
			for (i = -1; ++i < len;) {
				if (s[i].className !== 'note') {
					a += ATG.removehtml(s[i].innerHTML)+' ';
				}
			}
			a = a.trim().split(' ').join('+');
			h += '<div id="google-map">',
			h += '<iframe width="250" height="250" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"';
			h += 'src="http://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;&amp;output=embed&amp;iwloc=near&addr&amp;';
			h += 'q='+a+'"></iframe>';
			h += '<br /><small><a href="http://maps.google.com/maps?f=q&amp;source=embed&amp;hl=en&amp;ie=UTF8&amp;z=14&amp;';
			h += 'q='+a+'">View Larger Map</a></small>';
			h += '</div>';
			p.innerHTML = h+p.innerHTML;
		}
	}*/
};
//	let's get this party started
ATG.init();
