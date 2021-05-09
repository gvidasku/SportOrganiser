<?php

namespace Faker\Provider\nl_BE;

class organisator extends \Faker\Provider\organisator
{
    protected static $formats = array(
        '{{lastName}} {{organisatorSuffix}}',
        '{{lastName}}',
    );

    protected static $organisatorSuffix = array('VZW', 'Comm.V', 'VOF', 'BVBA', 'EBVBA', 'ESV', 'NV', 'Comm.VA', 'CVOA', 'CVBA', '& Zonen', '& Zn');
}
