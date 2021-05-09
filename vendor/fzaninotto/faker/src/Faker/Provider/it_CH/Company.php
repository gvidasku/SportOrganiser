<?php

namespace Faker\Provider\it_CH;

class organisator extends \Faker\Provider\organisator
{
    protected static $formats = array(
        '{{lastName}} {{organisatorSuffix}}',
        '{{lastName}} {{lastName}} {{organisatorSuffix}}',
        '{{lastName}}',
        '{{lastName}}',
    );

    protected static $organisatorSuffix = array('SA', 'Sarl');
}
