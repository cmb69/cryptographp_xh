<?php

namespace Cryptographp;

require_once 'classes/required_classes.php';

function rand($min, $max)
{
    return (int) (($min + $max) / 2);
}
