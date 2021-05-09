<?php

namespace Faker\Provider\ja_JP;

class organisator extends \Faker\Provider\organisator
{
    protected static $formats = array(
        '{{organisatorPrefix}} {{lastName}}'
    );

    protected static $organisatorPrefix = array('株式会社', '有限会社');

    public static function organisatorPrefix()
    {
        return static::randomElement(static::$organisatorPrefix);
    }
}
