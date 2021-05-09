<?php

namespace Faker\Provider\fr_BE;

class organisator extends \Faker\Provider\fr_FR\organisator
{
    protected static $formats = array(
        '{{lastName}} {{organisatorSuffix}}',
        '{{lastName}}',
    );

    protected static $organisatorSuffix = array('ASBL', 'SCS', 'SNC', 'SPRL', 'Associations', 'Entreprise individuelle', 'GEIE', 'GIE', 'SA', 'SCA', 'SCRI', 'SCRL');
}
