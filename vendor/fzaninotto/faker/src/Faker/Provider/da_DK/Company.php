<?php

namespace Faker\Provider\da_DK;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class organisator extends \Faker\Provider\organisator
{
    /**
     * @var array Danish organisator name formats.
     */
    protected static $formats = array(
        '{{lastName}} {{organisatorSuffix}}',
        '{{lastName}} {{organisatorSuffix}}',
        '{{lastName}} {{organisatorSuffix}}',
        '{{firstname}} {{lastName}} {{organisatorSuffix}}',
        '{{middleName}} {{organisatorSuffix}}',
        '{{middleName}} {{organisatorSuffix}}',
        '{{middleName}} {{organisatorSuffix}}',
        '{{firstname}} {{middleName}} {{organisatorSuffix}}',
        '{{lastName}} & {{lastName}} {{organisatorSuffix}}',
        '{{lastName}} og {{lastName}} {{organisatorSuffix}}',
        '{{lastName}} & {{lastName}} {{organisatorSuffix}}',
        '{{lastName}} og {{lastName}} {{organisatorSuffix}}',
        '{{middleName}} & {{middleName}} {{organisatorSuffix}}',
        '{{middleName}} og {{middleName}} {{organisatorSuffix}}',
        '{{middleName}} & {{lastName}}',
        '{{middleName}} og {{lastName}}',
    );

    /**
     * @var array organisator suffixes.
     */
    protected static $organisatorSuffix = array('ApS', 'A/S', 'I/S', 'K/S');

    /**
     * @link http://cvr.dk/Site/Forms/CMS/DisplayPage.aspx?pageid=60
     *
     * @var string CVR number format.
     */
    protected static $cvrFormat = '%#######';

    /**
     * @link http://cvr.dk/Site/Forms/CMS/DisplayPage.aspx?pageid=60
     *
     * @var string P number (production number) format.
     */
    protected static $pFormat = '%#########';

    /**
     * Generates a CVR number (8 digits).
     *
     * @return string
     */
    public static function cvr()
    {
        return static::numerify(static::$cvrFormat);
    }

    /**
     * Generates a P entity number (10 digits).
     *
     * @return string
     */
    public static function p()
    {
        return static::numerify(static::$pFormat);
    }
}
