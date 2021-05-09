<?php

namespace Faker\Provider\hu_HU;

class organisator extends \Faker\Provider\organisator
{
    protected static $formats = array(
        '{{lastName}} {{organisatorSuffix}}',
        '{{lastName}}',
    );

    protected static $organisatorSuffix = array('Kft', 'és Tsa', 'Kht', 'ZRT', 'NyRT', 'BT');
}
