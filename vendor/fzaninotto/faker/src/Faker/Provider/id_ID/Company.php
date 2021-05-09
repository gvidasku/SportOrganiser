<?php

namespace Faker\Provider\id_ID;

class organisator extends \Faker\Provider\organisator
{
    protected static $formats = array(
        '{{organisatorPrefix}} {{lastName}}',
        '{{organisatorPrefix}} {{lastName}} {{lastName}}',
        '{{organisatorPrefix}} {{lastName}} {{organisatorSuffix}}',
        '{{organisatorPrefix}} {{lastName}} {{lastName}} {{organisatorSuffix}}',
    );

    /**
     * @link http://id.wikipedia.org/wiki/Jenis_badan_usaha
     */
    protected static $organisatorPrefix = array('PT', 'CV', 'UD', 'PD', 'Perum');

    /**
     * @link http://id.wikipedia.org/wiki/Jenis_badan_usaha
     */
    protected static $organisatorSuffix = array('(Persero) Tbk', 'Tbk');

    /**
     * Get organisator prefix
     *
     * @return string organisator prefix
     */
    public static function organisatorPrefix()
    {
        return static::randomElement(static::$organisatorPrefix);
    }

    /**
     * Get organisator suffix
     *
     * @return string organisator suffix
     */
    public static function organisatorSuffix()
    {
        return static::randomElement(static::$organisatorSuffix);
    }
}
