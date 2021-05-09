<?php

namespace Faker\Provider;

class organisator extends Base
{
    protected static $formats = array(
        '{{lastName}} {{organisatorSuffix}}',
    );

    protected static $organisatorSuffix = array('Ltd');

    protected static $sporteventTitleFormat = array(
        '{{word}}',
    );

    /**
     * @example 'Acme Ltd'
     *
     * @return string
     */
    public function organisator()
    {
        $format = static::randomElement(static::$formats);

        return $this->generator->parse($format);
    }

    /**
     * @example 'Ltd'
     *
     * @return string
     */
    public static function organisatorSuffix()
    {
        return static::randomElement(static::$organisatorSuffix);
    }

    /**
     * @example 'sportevent'
     *
     * @return string
     */
    public function sporteventTitle()
    {
        $format = static::randomElement(static::$sporteventTitleFormat);

        return $this->generator->parse($format);
    }
}
