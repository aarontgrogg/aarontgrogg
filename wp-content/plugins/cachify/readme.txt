=== Cachify ===
Contributors: sergej.mueller
Tags: apc, cache, caching, performance
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=5RDDW9FEHGLG6
Requires at least: 3.4
Tested up to: 3.5
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html



Turbo für WordPress. Smarte, aber effiziente Cache-Lösung für WordPress. Mit der Konzentration aufs Wesentliche.



== Description ==

= Unkompliziert und ausbaufähig =
*Cachify* optimiert Ladezeit der Blogseiten, indem Seiteninhalte in statischer Form wahlweise in der Datenbank, auf der Festplatte oder dank APC (Alternative PHP Cache) im Speicher des Webservers abgelegt und beim Seitenaufruf ohne Umwege ausgegeben werden. Die Anzahl der DB-Anfragen und PHP-Anweisungen reduziert sich je nach gewählter Methode gegen Null.


= Pluspunkte =
* Zusammenarbeit mit Custom Post Types
* Speicherungsmethoden: DB, HDD und APC
* "Cache leeren" als Schaltfläche in der Admin Bar
* Online-Handbuch
* Einsatzbereit für WordPress-Multisite
* Optionale Komprimierung der HTML-Ausgabe
* Ausnahmelisten für Beiträge und User Agents
* Manueller und automatischer Cache-Reset
* Deutschsprachige Oberfläche zum Sofortstart
* Automatisches Management des Cache-Bestandes
* Anzeige der Cache-Belegung auf dem Dashboard
* Konfigurationseinstellungen für Apache- und Nginx-Server


> #### Cachify eBook für Kindle
> Als Begleithandbuch und Installationshilfe für *Cachify* wurde ein Kindle eBook mit dem Titel „[WordPress Performance](http://www.amazon.de/dp/B0091LDUVA "WordPress Performance"): Beschleunigung der Blogseiten durch Caching“ konzipiert und veröffentlicht. Das digitale Buch beleuchtet verfügbare Caching-Methoden, empfiehlt Einstellungen und liefert wertvolle Tipps & Tricks zur Geschwindigkeitsoptimierung unter WordPress.


= Support =
Freundlich formulierte Fragen rund um das Plugin werden per E-Mail beantwortet.


= Systemvoraussetzungen =
* WordPress ab 3.4
* PHP ab 5.2.4
* APC ab 3.1.4 (optional, falls installiert)


= Unterstützung =
* Per [Flattr](https://flattr.com/donation/give/to/sergej.mueller)
* Per [PayPal](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=5RDDW9FEHGLG6)


= Handbuch =
* [Cachify: Caching für WordPress](http://playground.ebiene.de/cachify-wordpress-cache/)


= Website =
* [cachify.de](http://cachify.de "Cachify WordPress Cache")


= Autor =
* [Google+](https://plus.google.com/110569673423509816572 "Google+")
* [Plugins](http://wpcoder.de "Plugins")




== Changelog ==

= 2.0.6 =
* Cache-Neuaufbau einer Blogseite nur bei Kommentaren, die freigegeben sind

= 2.0.5 =
* Cache-Leerung nach einem WordPress-Upgrade
* Keine Cache-Ausgabe für Jetpack Mobile Theme
* Abfrage auf eingeloggte Nutzer bei APC als Caching-Methode
* Änderung der Systemvoraussetzungen
* Cache-Reset nach WordPress-Update

= 2.0.4 =
* Bessere Trennung der Cache-Gesamtgröße im Dashboard-Widget "Auf einen Blick"

= 2.0.3 =
* Cache-Leerung beim Veröffentlichen verfügbarer Custom Post Types
* Noindex in der von WordPress generierten `robots.txt` für den Ordner mit HDD-Cache
* Hook `cachify_flush_cache` zum Leeren des Cache aus Drittanwendungen

= 2.0.2 =
* Unterstützung für WordPress 3.4
* Hochauflösende Icons für iPad & Co.
* Anpassungen für ältere PHP5-Versionen
* Entfernung des Plugin-Icons aus der Sidebar

= 2.0.1 =
* Verbesserter Autoload-Prozess
* Diverse Umbenennungen der Optionen
* Cache-Neuaufbau bei geplanten Beiträgen (Cachify DB)

= 2.0 =
* Überarbeitung der GUI
* Source Code-Modularisierung
* Cache-Größe auf dem Dashboard
* Festplatte als Ablageort für Cache
* Produktseite online: http://cachify.de
* Cache-Neuaufbau bei Kommentarstatusänderungen
* APC-Anforderungen: APC 3.0.0, empfohlen 3.1.4
* Optional: Kein Cache für kommentierende Nutzer
* Schnellübersicht der Optionen als Inline-Hilfe
* Mindestanforderungen: WordPress 3.1 & PHP 5.1.2

= 1.5.1 =
* `zlib.output_compression = Off` für Apache Webserver

= 1.5 =
* Überarbeitung des Regexp für HTML-Minify
* Reduzierung des Toolbar-Buttons auf das Icon
* Formatierung und Kommentierung des Quelltextes

= 1.4 =
* Xmas Edition

= 1.3 =
* Unterstützung für APC (Alternative PHP Cache)
* Umpositionierung des Admin Bar Buttons

= 1.2.1 =
* Icon für die "Cache leeren" Schaltfläche in der Admin Bar

= 1.2 =
* Schaltfläche "Cache leeren" in der Adminbar (ab WordPress 3.1)
* `flush_cache` auf public gesetzt, um von [wpSEO](http://wpseo.de "WordPress SEO Plugin") ansprechen zu können
* Ausführliche Tests unter WordPress 3.3

= 1.1 =
* Interne Prüfung auf fehlerhafte Cache-Generierung
* Anpassungen an der Code-Struktur
* Entfernung der Inline-Hilfe
* Verknüpfung der Online-Hilfe mit Optionen

= 1.0 =
* Leerung des Cache beim Aktualisieren von statischen Seiten
* Seite mit Plugin-Einstellungen
* Inline-Dokumentation in der Optionsseite
* Ausschluss von Passwort-geschützten Seiten
* WordPress 3.2 Support
* Unterstützung der WordPress Multisite Blogs
* Umstellung auf den template_redirect-Hook (Plugin-Kompatibilität)
* Interne Code-Bereinigung

= 0.9.2 =
* HTML-Kompression
* Flattr-Link

= 0.9.1 =
* Cache-Reset bei geplanten Beiträgen
* Unterstützung für das Carrington-Mobile Theme

= 0.9 =
* Workaround für Redirects

= 0.8 =
* Blacklist für PostIDs
* Blacklist für UserAgents
* Ausnahme für WP Touch
* Ausgabe des Zeitpunktes der Generierung
* Umbenennung der Konstanten

= 0.7 =
* Ausgabe des Speicherverbrauchs

= 0.6 =
* Live auf wordpress.org




== Screenshots ==

1. Cachify Optionen

2. Cachegröße auf dem Dashboard