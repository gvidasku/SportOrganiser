<?php

namespace Faker\Provider\el_CY;

class organisator extends \Faker\Provider\organisator
{
    protected static $organisatorSuffix = array(
        'ΛΤΔ',
        'Δημόσια εταιρεία',
        '& Υιοι',
        '& ΣΙΑ',
    );

    protected static $formats = array(
        '{{lastName}} {{organisatorSuffix}}',
        '{{lastName}}-{{lastName}}',
    );
}
