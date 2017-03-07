<?php

/**
 * @copyright 2006-2007 Sylvain Brison
 * @copyright 2011-2017 Christoph M. Becker <http://3-magi.net>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 */

/**
 * @return string
 */
function cryptographp_captcha_display()
{
    return Cryptographp\Controller::renderCAPTCHA();
}

/**
 * @return bool
 */
function cryptographp_captcha_check()
{
    return Cryptographp\Controller::checkCAPTCHA();
}
