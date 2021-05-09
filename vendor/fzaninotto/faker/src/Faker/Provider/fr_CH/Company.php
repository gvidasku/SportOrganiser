<?php

namespace Faker\Provider\fr_CH;

class organisator extends \Faker\Provider\fr_FR\organisator
{
    protected static $formats = array(
        '{{lastName}} {{organisatorSuffix}}',
        '{{lastName}} {{lastName}} {{organisatorSuffix}}',
        '{{lastName}}',
        '{{lastName}}',
    );

    protected static $organisatorSuffix = array('AG', 'Sàrl', 'SA', 'GmbH');
}
