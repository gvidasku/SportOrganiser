<?php

namespace Faker\Provider\hr_HR;

class organisator extends \Faker\Provider\organisator
{
    protected static $formats = array(
        '{{lastName}} {{organisatorSuffix}}',
        '{{organisatorPrefix}} {{lastName}}',
        '{{organisatorPrefix}} {{firstName}}',
    );

    protected static $organisatorSuffix = array(
        'd.o.o.', 'j.d.o.o.', 'Security'
    );

    protected static $organisatorPrefix = array(
        'Autoškola', 'Cvjećarnica', 'Informatički obrt', 'Kamenorezački obrt', 'Kladionice', 'Market', 'Mesnica', 'Prijevoznički obrt', 'Suvenirnica', 'Turistička agencija', 'Voćarna'
    );

    public static function organisatorPrefix()
    {
        return static::randomElement(static::$organisatorPrefix);
    }
}
