<?php

$plugin_tx['cryptographp']['utf-8-marker']="äöüß";

$plugin_tx['cryptographp']['message_reload']="Reload CAPTCHA";
$plugin_tx['cryptographp']['message_enter_code']="Enter the anti spam code!<br>(Cookies must be enabled)";

$plugin_tx['cryptographp']['error_use_max']="Too many reloads!";
$plugin_tx['cryptographp']['error_user_time']="Too quickly reloaded!";
$plugin_tx['cryptographp']['error_phpversion']="Cryptographp_XH requires at least PHP version %s!";
$plugin_tx['cryptographp']['error_extension']="Cryptographp_XH requires PHP's %s extension!";
$plugin_tx['cryptographp']['error_encoding']="Cryptographp_XH requires UTF-8 encoding!";

$plugin_tx['cryptographp']['cf_crypt_width']="Width of cryptogram in pixels";
$plugin_tx['cryptographp']['cf_crypt_height']="Height of cryptogram in pixels";
$plugin_tx['cryptographp']['cf_bg_rgb_red']="RGB background color: intensity of red";
$plugin_tx['cryptographp']['cf_bg_rgb_green']="RGB background color: intensity of green";
$plugin_tx['cryptographp']['cf_bg_rgb_blue']="RGB background color: intensity of blue";
$plugin_tx['cryptographp']['cf_bg_clear']="Wether background is transparent (PNG format only): <em>yes</em> or <em>no</em>";
$plugin_tx['cryptographp']['cf_bg_image']="File name of the background image. The image will be resized if necessary to fit to the cryptogram. If a folder name is given, the image will be choosen randomly from the folder.";
$plugin_tx['cryptographp']['cf_bg_frame']="Wether a frame is added around the cryptogram: <em>yes</em> or <em>no</em>";
$plugin_tx['cryptographp']['cf_char_rgb_red']="RGB color of the characters: intensity of red";
$plugin_tx['cryptographp']['cf_char_rgb_green']="RGB color of the characters: intensity of green";
$plugin_tx['cryptographp']['cf_char_rgb_blue']="RGB color of the characters: intensity of blue";
$plugin_tx['cryptographp']['cf_char_color_random']="Wether the color is choosen randomly. <em>yes</em> or <em>no</em>";
$plugin_tx['cryptographp']['cf_char_color_random_level']="The brightness of random colors: <em>0</em> (no restriction), <em>1</em> (very light colors only), <em>2</em> (light colors only), <em>3</em> (dark colors only) or <em>4</em> (very dark colors only)";
$plugin_tx['cryptographp']['cf_char_clear']="Transparency of the characters: between <em>0</em> (opaque) and <em>127</em> (totally transparent). Requires PHP >= 4.3.2";
$plugin_tx['cryptographp']['cf_fonts']="Semicolon delimited list of fonts, that are available in the crypt/fonts/ folder";
$plugin_tx['cryptographp']['cf_char_allowed']="Allowed characters";
$plugin_tx['cryptographp']['cf_crypt_easy']="Wether the cryptograms are made up alternatively of consonants and vowels: <em>yes</em> or <em>no</em>";
$plugin_tx['cryptographp']['cf_char_allowed_consonants']="Allowed consonants";
$plugin_tx['cryptographp']['cf_char_allowed_vowels']="Allowed Vowels";
$plugin_tx['cryptographp']['cf_char_case_sensitive']="Wether lower and upper case letters are distinguished: <em>yes</em> or <em>no</em>";
$plugin_tx['cryptographp']['cf_char_count_min']="Minimum number of characters in the cryptograms";
$plugin_tx['cryptographp']['cf_char_count_max']="Maximum number of characters in the cryptograms";
$plugin_tx['cryptographp']['cf_char_space']="Space between the characters in pixels";
$plugin_tx['cryptographp']['cf_char_size_min']="Minimum font size of the characters";
$plugin_tx['cryptographp']['cf_char_size_max']="Maximum font size of the characters";
$plugin_tx['cryptographp']['cf_char_angle_max']="Maximum angle of character rotation";
$plugin_tx['cryptographp']['cf_char_displace']="Wether the characters are randomly displaced in vertical direction: <em>yes</em> or <em>no</em>";
$plugin_tx['cryptographp']['cf_crypt_gaussian_blur']="Wether the image is blurred using the Gaussian method: <em>yes</em> or <em>no</em>. Requires PHP > 5.0.0";
$plugin_tx['cryptographp']['cf_crypt_gray_scale']="Wether the image is converted to grayscale: <em>yes</em> or <em>no</em>. Requires PHP > 5.0.0";
$plugin_tx['cryptographp']['cf_noise_pixel_min']="Minimum number of random pixels";
$plugin_tx['cryptographp']['cf_noise_pixel_max']="Maximum number of random pixels";
$plugin_tx['cryptographp']['cf_noise_line_min']="Minimum number of random lines";
$plugin_tx['cryptographp']['cf_noise_line_max']="Maximum number of random lines";
$plugin_tx['cryptographp']['cf_noise_circle_min']="Minimum number of random circles";
$plugin_tx['cryptographp']['cf_noise_circle_max']="Maximum number of random circles";
$plugin_tx['cryptographp']['cf_noise_color_char']="The color of the noise: <em>1</em> (character color), <em>2</em> (background color) or <em>3</em> (random color)";
$plugin_tx['cryptographp']['cf_noise_brush_size']="The thickness of the lines and circles";
$plugin_tx['cryptographp']['cf_noise_above']="Wether the noise is above the characters: <em>yes</em> or <em>no</em>";
$plugin_tx['cryptographp']['cf_crypt_format']="Image file format: <em>gif</em>, <em>png</em> or <em>jpeg</em>";
$plugin_tx['cryptographp']['cf_crypt_secure']="Type of encryption: <em>md5</em>, <em>sha1</em> or leave blank for none.";
$plugin_tx['cryptographp']['cf_crypt_use_timer']="Delay in seconds before reloading a new cryptogram is possible";
$plugin_tx['cryptographp']['cf_crypt_use_timer_error']="Strategy if cryptograms are reloaded too fast: <em>1</em> (return no image), <em>2</em> (return error message) or <em>3</em> (return after the delay is over)";
$plugin_tx['cryptographp']['cf_crypt_use_max']="How many times the cryptogram may be reloaded";
$plugin_tx['cryptographp']['cf_crypt_one_use']="Wether the cryptogram should be discarded after successful confirmation: <em>yes</em> or <em>no</em>";
$plugin_tx['cryptographp']['cf_utf-8-marker']="Internal use. <strong>Do not change!</strong>";

?>
