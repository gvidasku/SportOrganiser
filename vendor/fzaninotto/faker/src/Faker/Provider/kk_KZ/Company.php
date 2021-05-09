<?php

namespace Faker\Provider\kk_KZ;

class organisator extends \Faker\Provider\organisator
{
    protected static $organisatorNameFormats = array(
        '{{organisatorPrefix}} {{organisatorNameElement}}',
        '{{organisatorPrefix}} {{organisatorNameElement}}{{organisatorNameElement}}',
        '{{organisatorPrefix}} {{organisatorNameElement}}{{organisatorNameElement}}{{organisatorNameElement}}',
        '{{organisatorPrefix}} {{organisatorNameElement}}{{organisatorNameElement}}{{organisatorNameElement}}{{organisatorNameSuffix}}',
    );

    protected static $organisatorPrefixes = array(
        'АҚ', 'ЖШС', 'ЖАҚ'
    );

    protected static $organisatorNameSuffixes = array(
        'Құрылыс', 'Машина', 'Бұзу', '-М', 'Лизинг', 'Страх', 'Ком', 'Телеком'
    );

    protected static $organisatorElements = array(
        'Қазақ', 'Кітап', 'Цемент', 'Лифт', 'Креп', 'Авто', 'Теле', 'Транс', 'Алмаз', 'Метиз',
        'Мотор', 'Қаз', 'Тех', 'Сантех', 'Алматы', 'Астана', 'Электро',
    );

    /**
     * @example 'ЖШС АлматыТелеком'
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

    /**
     * National Business Identification Numbers
     *
     * @link   http://egov.kz/wps/portal/Content?contentPath=%2Fegovcontent%2Fbus_business%2Ffor_businessmen%2Farticle%2Fbusiness_identification_number&lang=en
     * @param  \DateTime $registrationDate
     * @return string 12 digits, like 150140000019
     */
    public static function businessIdentificationNumber(\DateTime $registrationDate = null)
    {
        if (!$registrationDate) {
            $registrationDate = \Faker\Provider\DateTime::dateTimeThisYear();
        }

        $dateAsString              = $registrationDate->format('ym');
        $legalEntityType           = (string) static::numberBetween(4, 6);
        $legalEntityAdditionalType = (string) static::numberBetween(0, 3);
        $randomDigits              = (string) static::numerify('######');

        return $dateAsString . $legalEntityType . $legalEntityAdditionalType . $randomDigits;
    }
}
