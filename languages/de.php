<?php

$plugin_tx['cryptographp']['alt_image']="Visuelle Aufgabe";
$plugin_tx['cryptographp']['alt_reload']="Neue Aufgabe stellen";
$plugin_tx['cryptographp']['alt_audio']="Höraufgabe stellen";
$plugin_tx['cryptographp']['alt_logo']="Verbotsschild";

$plugin_tx['cryptographp']['message_enter_code']="Geben Sie den Anti-Spam-Code ein:";

$plugin_tx['cryptographp']['syscheck_title']="System-Prüfung";
$plugin_tx['cryptographp']['syscheck_alt_ok']="OK";
$plugin_tx['cryptographp']['syscheck_alt_warn']="Warnung";
$plugin_tx['cryptographp']['syscheck_alt_fail']="Fehler";
$plugin_tx['cryptographp']['syscheck_phpversion']="PHP Version &ge; %s";
$plugin_tx['cryptographp']['syscheck_xhversion']="CMSimple_XH Version &ge; %s";
$plugin_tx['cryptographp']['syscheck_extension']="Erweiterung \"%s\" geladen";
$plugin_tx['cryptographp']['syscheck_freetype_support']="TrueType Schriftarten werden unterstützt";
$plugin_tx['cryptographp']['syscheck_gif_support']="GIF wird unterstützt";
$plugin_tx['cryptographp']['syscheck_jpeg_support']="JPEG wird unterstützt";
$plugin_tx['cryptographp']['syscheck_png_support']="PNG wird unterstützt";
$plugin_tx['cryptographp']['syscheck_magic_quotes']="Magic quotes runtime off";
$plugin_tx['cryptographp']['syscheck_writable']="Ordner \"%s\" beschreibbar";

$plugin_tx['cryptographp']['error_cookies']="Cookies müssen aktiviert sein!";
$plugin_tx['cryptographp']['error_user_time']="Zu schnell nachgeladen!";
$plugin_tx['cryptographp']['error_audio']="Das Audio-CAPTCHA konnte nicht erzeugt werden! Bitte versuchen Sie es mit einer neuen Aufgabe noch einmal.";

$plugin_tx['cryptographp']['cf_crypt_width']="Breite des Bildes in Pixel";
$plugin_tx['cryptographp']['cf_crypt_height']="Höhe des Bildes in Pixel";
$plugin_tx['cryptographp']['cf_crypt_easy']="Abwechselnd Konsonanten und Vokale für die CAPTCHAs verwenden";
$plugin_tx['cryptographp']['cf_crypt_gaussian_blur']="Bild mit der Gaußschen Methode verwischen";
$plugin_tx['cryptographp']['cf_crypt_gray_scale']="Schwarz-Weiß-Bild erzeugen";
$plugin_tx['cryptographp']['cf_crypt_format']="Das Dateiformat der Bilder";
$plugin_tx['cryptographp']['cf_crypt_use_timer']="Verzögerung (in Sekunden) bevor ein neues CAPTCHA nachgeladen werden kann";
$plugin_tx['cryptographp']['cf_crypt_use_timer_error']="Fehler melden, wenn das CAPTCHA zu schnell nachgeladen wurde (alternativ für eine Weile warten)";
$plugin_tx['cryptographp']['cf_crypt_expiration']="Wie lange jedes CAPTCHA verwendet werden kann (in Sekunden)";
$plugin_tx['cryptographp']['cf_bg_rgb_red']="RGB Hintergrundfarbe: Rot-Intensität (0-255)";
$plugin_tx['cryptographp']['cf_bg_rgb_green']="RGB Hintergrundfarbe: Grün-Intensität (0-255)";
$plugin_tx['cryptographp']['cf_bg_rgb_blue']="RGB Hintergrundfarbe: Blau-Intensität (0-255)";
$plugin_tx['cryptographp']['cf_bg_clear']="Transparenten Hintergrund verwenden (nur für PNG Format)";
$plugin_tx['cryptographp']['cf_bg_image']="Dateiname des Hintergrundbildes. Das Bild wird bei Bedarf skaliert um ins CAPTCHA zu passen. Wenn ein Ordnername angegeben wird, dann wird daraus ein zufälliges Bild ausgewählt. Geben Sie den Pfad relativ zum Bilderordner (normalerweise userfiles/images/) an.";
$plugin_tx['cryptographp']['cf_bg_frame']="CAPTCHA einrahmen";
$plugin_tx['cryptographp']['cf_char_rgb_red']="RGB Farbe der Zeichen: Rot-Intensität (0-255)";
$plugin_tx['cryptographp']['cf_char_rgb_green']="RGB Farbe der Zeichen: Grün-Intensität (0-255)";
$plugin_tx['cryptographp']['cf_char_rgb_blue']="RGB Farbe der Zeichen: Blau-Intensität (0-255)";
$plugin_tx['cryptographp']['cf_char_color_random']="Zufällige Farben verwenden";
$plugin_tx['cryptographp']['cf_char_color_random_level']="Die Helligkeit der zufälligen Farben: <em>0</em> (beliebige Farben), <em>1</em> (nur sehr helle Farben), <em>2</em> (nur helle Farben), <em>3</em> (nur dunkle Farben) oder <em>4</em> (nur sehr dunkle Farben)";
$plugin_tx['cryptographp']['cf_char_clear']="Transparenz der Zeichen: zwischen <em>0</em> (undurchsichtig) und <em>127</em> (völlig transparent)";
$plugin_tx['cryptographp']['cf_char_fonts']="Durch Strichpunkte getrennte Liste von TrueType Schriftarten, die im fonts/ Ordner vorhanden sind";
$plugin_tx['cryptographp']['cf_char_allowed']="Erlaubte Zeichen, wenn \"crypt easy\" deaktiviert ist (vom Audio-CAPTCHA werden nur A-Z und 0-9 unterstützt).";
$plugin_tx['cryptographp']['cf_char_allowed_consonants']="Erlaubte Konsonanten, wenn \"crypt easy\" aktiviert ist (vom Audio-CAPTCHA werden nur A-Z und 0-9 unterstützt).";
$plugin_tx['cryptographp']['cf_char_allowed_vowels']="Erlaubte Vokale, wenn \"crypt easy\" aktiviert ist (vom Audio-CAPTCHA werden nur A-Z und 0-9 unterstützt).";
$plugin_tx['cryptographp']['cf_char_count_min']="Mindestanzahl der Zeichen im CAPTCHA";
$plugin_tx['cryptographp']['cf_char_count_max']="Höchstanzahl der Zeichen im CAPTCHA";
$plugin_tx['cryptographp']['cf_char_space']="Abstand zwischen den Zeichen in Pixeln";
$plugin_tx['cryptographp']['cf_char_size_min']="Mindestgröße der Schriftart";
$plugin_tx['cryptographp']['cf_char_size_max']="Maximalgröße der Schriftart";
$plugin_tx['cryptographp']['cf_char_angle_max']="Maximaler Grad der Zeichenrotation";
$plugin_tx['cryptographp']['cf_char_displace']="Zufälliges Versetzen der Zeichen in vertikaler Richtung";
$plugin_tx['cryptographp']['cf_noise_pixel_min']="Mindestanzahl von zufälligen Pixeln";
$plugin_tx['cryptographp']['cf_noise_pixel_max']="Höchstanzahl von zufälligen Pixeln";
$plugin_tx['cryptographp']['cf_noise_line_min']="Mindestanzahl von zufälligen Linien";
$plugin_tx['cryptographp']['cf_noise_line_max']="Höchstanzahl von zufälligen Linien";
$plugin_tx['cryptographp']['cf_noise_circle_min']="Mindestanzahl von zufälligen Kreisen";
$plugin_tx['cryptographp']['cf_noise_circle_max']="Höchstanzahl von zufälligen Kreisen";
$plugin_tx['cryptographp']['cf_noise_color']="Die Farbe des Rauschens:: <em>1</em> (Zeichenfarbe), <em>2</em> (Hintergrundfarbe) or <em>3</em> (Zufallsfarbe)";
$plugin_tx['cryptographp']['cf_noise_brush_size']="Die Dicke der Linien und Kreise";
$plugin_tx['cryptographp']['cf_noise_above']="Rauschen <em>über</em> die Zeichen legen";

?>
