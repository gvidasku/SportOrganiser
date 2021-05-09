<?php

namespace Faker\Provider\he_IL;

class organisator extends \Faker\Provider\organisator
{
    protected static $formats = array(
        '{{lastName}} {{organisatorSuffix}}',
        '{{lastName}} את {{lastName}} {{organisatorSuffix}}',
        '{{lastName}} ו{{lastName}}'
    );

    protected static $organisatorSuffix = array('בע"מ', 'ובניו', 'סוכנויות', 'משווקים');
}
