<?php

namespace Faker\Provider\sl_SI;

class organisator extends \Faker\Provider\organisator
{
    protected static $formats = array(
        '{{firstName}} {{lastName}} s.p.',
        '{{lastName}} {{organisatorSuffix}}',
        '{{lastName}}, {{lastName}} in {{lastName}} {{organisatorSuffix}}',
    );

    protected static $organisatorSuffix = array('d.o.o.', 'd.d.', 'k.d.', 'k.d.d.','d.n.o.','so.p.');
}
