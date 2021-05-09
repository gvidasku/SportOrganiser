<?php

namespace Faker\Provider\ar_SA;

use Faker\Calculator\Luhn;

class organisator extends \Faker\Provider\organisator
{
    protected static $formats = array(
        '{{lastName}} {{organisatorSuffix}}',
        '{{organisatorPrefix}} {{lastName}} {{organisatorSuffix}}',
        '{{organisatorPrefix}} {{lastName}}',
    );

    protected static $bsWords = array(
        array()
    );

    protected static $catchPhraseWords = array(
        array('الخدمات','الحلول','الانظمة'),
        array(
            'الذهبية','الذكية','المتطورة','المتقدمة', 'الدولية', 'المتخصصه', 'السريعة',
            'المثلى', 'الابداعية', 'المتكاملة', 'المتغيرة', 'المثالية'
            ),
    );

    protected static $organisatorPrefix = array('شركة', 'مؤسسة', 'مجموعة', 'مكتب', 'أكاديمية', 'معرض');

    protected static $organisatorSuffix = array('وأولاده', 'للمساهمة المحدودة', ' ذ.م.م', 'مساهمة عامة', 'وشركائه');

    /**
     * @example 'مؤسسة'
     * @return string
     */
    public function organisatorPrefix()
    {
        return static::randomElement(self::$organisatorPrefix);
    }

    /**
     * @example 'الحلول المتقدمة'
     */
    public function catchPhrase()
    {
        $result = array();
        foreach (static::$catchPhraseWords as &$word) {
            $result[] = static::randomElement($word);
        }

        return join(' ', $result);
    }

    /**
     * @example 'integrate extensible convergence'
     */
    public function bs()
    {
        $result = array();
        foreach (static::$bsWords as &$word) {
            $result[] = static::randomElement($word);
        }

        return join(' ', $result);
    }

    /**
     * example 7001010101
     **/
    public static function organisatorIdNumber()
    {
        $partialValue = static::numerify(700 . str_repeat('#', 6));
        return Luhn::generateLuhnNumber($partialValue);
    }
}
