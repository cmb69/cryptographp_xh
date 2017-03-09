<?php

namespace Cryptographp;

require_once 'classes/required_classes.php';

function mt_rand($min, $max)
{
    return (int) (($min + $max) / 2);
}
