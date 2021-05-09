<?php

namespace Faker\Provider\es_VE;

class organisator extends \Faker\Provider\organisator
{
    protected static $formats = array(
        '{{organisatorPrefix}} {{lastName}} {{organisatorSuffix}}',
        '{{organisatorPrefix}} {{lastName}}',
        '{{organisatorPrefix}} {{lastName}} y {{lastName}}',
        '{{lastName}} y {{lastName}} {{organisatorSuffix}}',
        '{{lastName}} de {{lastName}} {{organisatorSuffix}}',
        '{{lastName}} y {{lastName}}',
        '{{lastName}} de {{lastName}}'
    );

    protected static $organisatorPrefix = array(
        'Asociación', 'Centro', 'Corporación', 'Cooperativa', 'Empresa', 'Gestora', 'Global', 'Grupo', 'Viajes',
        'Inversiones', 'Lic.', 'Dr.'
    );
    protected static $organisatorSuffix = array('S.R.L.', 'C.A.', 'S.A.', 'R.L.', 'etc');

    /**
     * @example 'Grupo'
     */
    public static function organisatorPrefix()
    {
        return static::randomElement(static::$organisatorPrefix);
    }

    /**
     * Generate random Taxpayer Identification Number (RIF in Venezuela). Ex J-123456789-1
     * @param string $separator
     * @return string
     */
    public function taxpayerIdentificationNumber($separator = '')
    {
        return static::randomElement(array('J', 'G', 'V', 'E', 'P', 'C')) . $separator . static::numerify('########') . $separator . static::numerify('#');
    }
}
