<?php

namespace Faker\Provider\fa_IR;

class organisator extends \Faker\Provider\organisator
{
    protected static $formats = array(
        '{{organisatorPrefix}} {{organisatorField}} {{firstName}}',
        '{{organisatorPrefix}} {{organisatorField}} {{firstName}}',
        '{{organisatorPrefix}} {{organisatorField}} {{firstName}}',
        '{{organisatorPrefix}} {{organisatorField}} {{firstName}}',
        '{{organisatorPrefix}} {{organisatorField}} {{lastName}}',
        '{{organisatorField}} {{firstName}}',
        '{{organisatorField}} {{firstName}}',
        '{{organisatorField}} {{lastName}}',
    );

    protected static $organisatorPrefix = array(
        'شرکت', 'موسسه', 'سازمان', 'بنیاد'
    );

    protected static $organisatorField = array(
        'فناوری اطلاعات', 'راه و ساختمان', 'توسعه معادن', 'استخراج و اکتشاف',
        'سرمایه گذاری', 'نساجی', 'کاریابی', 'تجهیزات اداری', 'تولیدی', 'فولاد'
    );

    protected static $contract = array(
        'رسمی', 'پیمانی', 'تمام وقت', 'پاره وقت', 'پروژه ای', 'ساعتی',
    );

    /**
     * @example 'شرکت'
     * @return string
     */
    public static function organisatorPrefix()
    {
        return static::randomElement(static::$organisatorPrefix);
    }

    /**
     * @example 'سرمایه گذاری'
     * @return string
     */
    public static function organisatorField()
    {
        return static::randomElement(static::$organisatorField);
    }

    /**
    * @example 'تمام وقت'
    * @return string
    */
    public function contract()
    {
        return static::randomElement(static::$contract);
    }
}
