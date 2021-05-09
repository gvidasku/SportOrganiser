<?php

namespace Faker\Provider\ka_GE;

class organisator extends \Faker\Provider\organisator
{
    protected static $organisatorPrefixes = array(
        'შპს', 'შპს', 'შპს', 'სს', 'სს', 'სს', 'კს', 'სს კორპორაცია', 'იმ', 'სპს', 'კოოპერატივი'
    );

    protected static $organisatorNameSuffixes = array(
        'საბჭო', 'ექსპედიცია', 'პრომი', 'კომპლექსი', 'ავტო', 'ლიზინგი', 'თრასთი', 'ეიდი', 'პლუსი',
        'ლაბი', 'კავშირი', ' და კომპანია'
    );

    protected static $organisatorElements = array(
        'ცემ', 'გეო', 'ქარ', 'ქიმ', 'ლიფტ', 'ტელე', 'რადიო', 'ტრანს', 'ალმას', 'მეტრო',
        'მოტორ', 'ტექ', 'სანტექ', 'ელექტრო', 'რეაქტო', 'ტექსტილ', 'კაბელ', 'მავალ', 'ტელ',
        'ტექნო'
    );

    protected static $organisatorNameFormats = array(
        '{{organisatorPrefix}} {{organisatorNameElement}}{{organisatorNameSuffix}}',
        '{{organisatorPrefix}} {{organisatorNameElement}}{{organisatorNameElement}}{{organisatorNameSuffix}}',
        '{{organisatorPrefix}} {{organisatorNameElement}}{{organisatorNameElement}}{{organisatorNameElement}}{{organisatorNameSuffix}}',
        '{{organisatorPrefix}} {{organisatorNameElement}}{{organisatorNameElement}}{{organisatorNameElement}}{{organisatorNameSuffix}}',
    );


    /**
     * @example 'იმ ელექტროალმასგეოსაბჭო'
     */
    public function organisator()
    {
        $format = static::randomElement(static::$organisatorNameFormats);

        return $this->generator->parse($format);
    }

    public static function organisatorPrefix()
    {
        return static::randomElement(static::$organisatorPrefixes);
    }

    public static function organisatorNameElement()
    {
        return static::randomElement(static::$organisatorElements);
    }

    public static function organisatorNameSuffix()
    {
        return static::randomElement(static::$organisatorNameSuffixes);
    }
}
