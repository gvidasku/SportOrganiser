<?php

namespace Faker\Provider\bn_BD;

class organisator extends \Faker\Provider\organisator
{
    protected static $formats = array(
        '{{organisatorName}} {{organisatorType}}'
    );

    protected static $names = array(
        'রহিম', 'করিম', 'বাবলু'
    );

    protected static $types = array(
        'সিমেন্ট', 'সার', 'ঢেউটিন'
    );

    public static function organisatorType()
    {
        return static::randomElement(static::$types);
    }

    public static function organisatorName()
    {
        return static::randomElement(static::$names);
    }
}
