# Cryptographp\_XH

Cryptographp\_XH ist ein CAPTCHA Plugin
basierend auf cryptographp von Sylvain Brison.
Es wurde modifiziert um in die CMSimple\_XH Umgebung zu passen,
kann mehrere Male auf der selben Seite aufgerufen werden,
und bietet alternative Audio CAPTCHAs.
Es kann als Utility-Plugin von anderen Plugins,
wie z.B. [Advancedform\_XH](https://github.com/cmb69/advancedform_xh)
verwendet werden,
um Spam-Bots vom erfolgreichen Versenden von Formularen abzuhalten.

- [Voraussetzungen](#voraussetzungen)
- [Download](#download)
- [Installation](#installation)
- [Einstellungen](#einstellungen)
- [Verwendung](#verwendung)
- [Einschränkungen](#einschränkungen)
- [Problembehebung](#problembehebung)
- [Lizenz](#lizenz)
- [Danksagung](#danksagung)

# Voraussetzungen

Cryptographp\_XH ist ein Plugin für [CMSimple\_XH](https://www.cmsimple-xh.org/de/).
Es benötigt CMSimple\_XH ≥ 1.6.3
und PHP ≥ 5.4.0 mit den gd und session Extensions.

## Download

Das [aktuelle Release](https://github.com/cmb69/cryptographp_xh/releases/latest)
kann von Github herunter geladen werden.

## Installation

Die Installation erfolgt wie bei vielen anderen CMSimple\_XH-Plugins auch.

1. Sichern Sie die Daten auf Ihrem Server.
1. Entpacken Sie die ZIP-Datei auf Ihrem Rechner.
1. Laden Sie das ganze Verzeichnis `cryptographp/` auf Ihren Server
   in das `plugins/` Verzeichnis von CMSimple\_XH hoch.
1. Machen Sie die Unterverzeichnisse `config/`, `css/`
   und `languages/` beschreibbar.
1. Navigieren Sie zu `Plugins` → `Cryptographp` im Administrationsbereich,
   und prüfen Sie, ob alle Voraussetzungen erfüllt sind.

## Einstellungen

Die Plugin-Konfiguration erfolgt wie bei vielen anderen
CMSimple\_XH-Plugins auch im Administrationsbereich der Website.
Gehen Sie zu `Plugins` → `Cryptographp`.

Sie können die Voreinstellungen von Cryptographp\_XH
unter `Konfiguration` ändern.
Beim Überfahren der Hilfe-Icons mit der Maus
werden Hinweise zu den Einstellungen angezeigt.

Die Lokalisierung wird unter `Sprache` vorgenommen.
Sie können die Zeichenketten in Ihre eigene Sprache übersetzen,
falls keine entsprechende Sprachdatei zur Verfügung steht,
oder sie entsprechend Ihren Anforderungen anpassen.

Das Aussehen von Cryptographp\_XH kann unter `Stylesheet` angepasst werden.

## Verwendung

Cryptographp\_XH ist ein Utility-Plugin,
das von anderen Plugins verwendet werden kann.
Als solches hat es keine eigene Verwendung.
Wie es von Entwicklern anderer Plugins verwendet werden kann,
wird im [CMSimple\_XH Wiki](https://www.cmsimple-xh.org/wiki/doku.php/captcha_plugins)
erklärt.

## Einschränkungen

Um erfolgreich Formulare, die durch Cryptographp\_XH geschützt werden,
zu versenden, muss der Browser des Besuchers Cookies akzeptieren.

## Problembehebung

Melden Sie Programmfehler und stellen Sie Supportanfragen entweder auf
[Github](https://github.com/cmb69/cryptographp_xh/issues)
oder im [CMSimple\_XH Forum](https://cmsimpleforum.com/).

## Lizenz

Cryptographp\_XH ist freie Software. Sie können es unter den Bedingungen
der GNU General Public License, wie von der Free Software Foundation
veröffentlicht, weitergeben und/oder modifizieren, entweder gemäß
Version 3 der Lizenz oder (nach Ihrer Option) jeder späteren Version.

Die Veröffentlichung von Cryptographp\_XH erfolgt in der Hoffnung, daß es
Ihnen von Nutzen sein wird, aber *ohne irgendeine Garantie*, sogar ohne
die implizite Garantie der *Marktreife* oder der *Verwendbarkeit für einen
bestimmten Zweck*. Details finden Sie in der GNU General Public License.

Sie sollten ein Exemplar der GNU General Public License zusammen mit
Cryptographp\_XH erhalten haben. Falls nicht, siehe
<https://www.gnu.org/licenses/>.

Copyright © 2011-2017 Christoph M. Becker

Slovakische Übersetzung © 2012 Dr. Martin Sereday  
Tschechische Übersetzung © 2012 Josef Němec  
Russische Übersetzung © 2012 Lybomyr Kydray

## Danksagung

Cryptographp\_XH basiert auf cryptographp.
Vielen Dank an Sylvain Brison für die Veröffentlichung dieser feinen
und recht flexiblen CAPTCHA-Lösung unter einer GPL kompatiblen Lizenz.
Das Audio-CAPTCHA wurde von braillecaptcha von *johnjdoe* inspiriert,
und ermöglicht durch Peter Keungs fantastische
[MP3 Dateien](https://www.theblog.ca/mp3-audio-files-alphabet),
die als Freeware veröffentlicht wurden.

Das Pluginlogo wurde von
[Pavel InFeRnODeMoN](https://store.kde.org/u/InFeRnODeMoN) gestaltet.
Vielen Dank für die Veröffentlichung unter GPL.

Die Icons, die im Frontend verwendet werden, wurden von
[Google](https://material.io/icons/) gestaltet.
Vielen Dank für die Veröffentlichung dieser Icons
unter Apache License Version 2.0.

Vielen Dank an die Community im
[CMSimple\_XH Forum](https://www.cmsimpleforum.com/)
für Hinweise, Anregungen und das Testen.
Besonders möchte ich *snafu* danken,
der mich auf cryptographp hingewiesen hat,
und *oldnema*, der den ersten Satz von Sprachdateien
für das Audio-CAPTCHA erstellt hat.

Und zu guter Letzt vielen Dank an
[Peter Harteg](https://www.harteg.dk/), den „Vater“ von CMSimple,
und alle Entwickler von [CMSimple\_XH](https://www.cmsimple-xh.org/de/),
ohne die dieses phantastische CMS nicht existieren würde.
