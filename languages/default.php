<?php

$plugin_tx['cryptographp']['alt_image']="Visual challenge";
$plugin_tx['cryptographp']['alt_reload']="Get new challenge";
$plugin_tx['cryptographp']['alt_audio']="Get audio challenge";

$plugin_tx['cryptographp']['message_enter_code']="Enter the anti spam code:";

$plugin_tx['cryptographp']['syscheck_title']="System check";
$plugin_tx['cryptographp']['syscheck_phpversion']="PHP version &ge; %s";
$plugin_tx['cryptographp']['syscheck_extension']="Extension '%s' loaded";
$plugin_tx['cryptographp']['syscheck_magic_quotes']="Magic quotes runtime off";
$plugin_tx['cryptographp']['syscheck_freetype_support']="TrueType font support available";
$plugin_tx['cryptographp']['syscheck_gif_support']="GIF support available";
$plugin_tx['cryptographp']['syscheck_jpeg_support']="JPEG support available";
$plugin_tx['cryptographp']['syscheck_png_support']="PNG support available";
$plugin_tx['cryptographp']['syscheck_encoding']="Encoding 'UTF-8' configured";
$plugin_tx['cryptographp']['syscheck_writable']="Folder '%s' writable";

$plugin_tx['cryptographp']['error_cookies']="Cookies must be enabled!";
$plugin_tx['cryptographp']['error_user_time']="Reloaded too fast!";
$plugin_tx['cryptographp']['error_audio']="The audio CAPTCHA couldn't be generated! Please get a new challenge and try again.";

$plugin_tx['cryptographp']['cf_crypt_width']="Width of cryptogram in pixels";
$plugin_tx['cryptographp']['cf_crypt_height']="Height of cryptogram in pixels";
$plugin_tx['cryptographp']['cf_bg_rgb_red']="RGB background color: intensity of red";
$plugin_tx['cryptographp']['cf_bg_rgb_green']="RGB background color: intensity of green";
$plugin_tx['cryptographp']['cf_bg_rgb_blue']="RGB background color: intensity of blue";
$plugin_tx['cryptographp']['cf_bg_clear']="Whether background is transparent (PNG format only): <em>yes</em> or <em>no</em>";
$plugin_tx['cryptographp']['cf_bg_image']="File name of the background image. The image will be resized if necessary to fit to the cryptogram. If a folder name is given, the image will be choosen randomly from the folder. Specify the path relative to the plugin's folder.";
$plugin_tx['cryptographp']['cf_bg_frame']="Whether a frame is added around the cryptogram: <em>yes</em> or <em>no</em>";
$plugin_tx['cryptographp']['cf_char_rgb_red']="RGB color of the characters: intensity of red";
$plugin_tx['cryptographp']['cf_char_rgb_green']="RGB color of the characters: intensity of green";
$plugin_tx['cryptographp']['cf_char_rgb_blue']="RGB color of the characters: intensity of blue";
$plugin_tx['cryptographp']['cf_char_color_random']="Whether the color is choosen randomly. <em>yes</em> or <em>no</em>";
$plugin_tx['cryptographp']['cf_char_color_random_level']="The brightness of random colors: <em>0</em> (no restriction), <em>1</em> (very light colors only), <em>2</em> (light colors only), <em>3</em> (dark colors only) or <em>4</em> (very dark colors only)";
$plugin_tx['cryptographp']['cf_char_clear']="Transparency of the characters: between <em>0</em> (opaque) and <em>127</em> (totally transparent). Requires PHP >= 4.3.2";
$plugin_tx['cryptographp']['cf_fonts']="Semicolon delimited list of TrueType fonts, that are available in the fonts/ folder";
$plugin_tx['cryptographp']['cf_char_allowed']="Allowed characters, if \"crypt easy\" is \"no\" (only A-Z and 0-9 are supported by the audio CAPTCHA).";
$plugin_tx['cryptographp']['cf_crypt_easy']="Whether the cryptograms are made up alternatively of consonants and vowels: <em>yes</em> or <em>no</em>";
$plugin_tx['cryptographp']['cf_char_allowed_consonants']="Allowed consonants, if \"crypt easy\" is \"yes\" (only A-Z and 0-9 are supported by the audio CAPTCHA).";
$plugin_tx['cryptographp']['cf_char_allowed_vowels']="Allowed Vowels, if \"crypt easy\" is \"yes\" (only A-Z and 0-9 are supported by the audio CAPTCHA).";
$plugin_tx['cryptographp']['cf_char_count_min']="Minimum number of characters in the cryptograms";
$plugin_tx['cryptographp']['cf_char_count_max']="Maximum number of characters in the cryptograms";
$plugin_tx['cryptographp']['cf_char_space']="Space between the characters in pixels";
$plugin_tx['cryptographp']['cf_char_size_min']="Minimum font size of the characters";
$plugin_tx['cryptographp']['cf_char_size_max']="Maximum font size of the characters";
$plugin_tx['cryptographp']['cf_char_angle_max']="Maximum angle of character rotation";
$plugin_tx['cryptographp']['cf_char_displace']="Whether the characters are randomly displaced in vertical direction: <em>yes</em> or <em>no</em>";
$plugin_tx['cryptographp']['cf_crypt_gaussian_blur']="Whether the image is blurred using the Gaussian method: <em>yes</em> or <em>no</em>. Requires PHP > 5.0.0";
$plugin_tx['cryptographp']['cf_crypt_gray_scale']="Whether the image is converted to grayscale: <em>yes</em> or <em>no</em>. Requires PHP > 5.0.0";
$plugin_tx['cryptographp']['cf_noise_pixel_min']="Minimum number of random pixels";
$plugin_tx['cryptographp']['cf_noise_pixel_max']="Maximum number of random pixels";
$plugin_tx['cryptographp']['cf_noise_line_min']="Minimum number of random lines";
$plugin_tx['cryptographp']['cf_noise_line_max']="Maximum number of random lines";
$plugin_tx['cryptographp']['cf_noise_circle_min']="Minimum number of random circles";
$plugin_tx['cryptographp']['cf_noise_circle_max']="Maximum number of random circles";
$plugin_tx['cryptographp']['cf_noise_color_char']="The color of the noise: <em>1</em> (character color), <em>2</em> (background color) or <em>3</em> (random color)";
$plugin_tx['cryptographp']['cf_noise_brush_size']="The thickness of the lines and circles";
$plugin_tx['cryptographp']['cf_noise_above']="Whether the noise is above the characters: <em>yes</em> or <em>no</em>";
$plugin_tx['cryptographp']['cf_crypt_format']="Image file format: <em>gif</em>, <em>png</em> or <em>jpeg</em>";
$plugin_tx['cryptographp']['cf_crypt_use_timer']="Delay (in seconds) before reloading a new cryptogram is possible";
$plugin_tx['cryptographp']['cf_crypt_use_timer_error']="Whether to report an error, if the cryptogram is reloaded too fast (\"yes\") or to wait for some time (\"no\")";
$plugin_tx['cryptographp']['cf_crypt_expiration']="How long each cryptogram can be used (in seconds)";

?>
