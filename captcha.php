<?php

/**
 * CAPTCHA plugin interface of Cryptographp_XH.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Cryptographp
 * @author    Sylvain Brison <cryptographp@alphpa.com>
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2006-2007 Sylvain Brison
 * @copyright 2011-2017 Christoph M. Becker <http://3-magi.net>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Cryptographp_XH
 */

/**
 * Returns the (x)html block element displaying the captcha,
 * the input field for the captcha code and all other elements,
 * that are related directly to the captcha,
 * such as an reload and an audio button.
 *
 * @return string
 */
// @codingStandardsIgnoreStart
function cryptographp_captcha_display()
{
// @codingStandardsIgnoreEnd
    return Cryptographp_Controller::renderCAPTCHA();
}

/**
 * Returns whether the correct captcha code was entered.
 *
 * @return bool
 */
// @codingStandardsIgnoreStart
function cryptographp_captcha_check()
{
// @codingStandardsIgnoreEnd
    return Cryptographp_Controller::checkCAPTCHA();
}

?>
