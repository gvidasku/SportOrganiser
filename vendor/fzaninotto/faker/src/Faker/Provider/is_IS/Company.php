<?php

namespace Faker\Provider\is_IS;

/**
 * @author Birkir Gudjonsson <birkir.gudjonsson@gmail.com>
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
    protected static $organisatorSuffix = array('ehf.', 'hf.', 'sf.');

    /**
     * @link http://www.rsk.is/atvinnurekstur/virdisaukaskattur/
     *
     * @var string VSK number format.
     */
    protected static $vskFormat = '%####';

    /**
     * Generates a VSK number (5 digits).
     *
     * @return string
     */
    public static function vsk()
    {
        return static::numerify(static::$vskFormat);
    }
}
