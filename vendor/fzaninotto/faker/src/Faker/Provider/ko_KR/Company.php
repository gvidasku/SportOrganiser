<?php

namespace Faker\Provider\ko_KR;

class organisator extends \Faker\Provider\organisator
{
    protected static $formats = array(
        '{{organisatorPrefix}}{{firstName}}',
        '{{organisatorPrefix}}{{firstName}}{{organisatorSuffix}}',
        '{{firstName}}{{organisatorSuffix}}',
        '{{firstName}}{{organisatorSuffix}}',
        '{{firstName}}{{organisatorSuffix}}',
        '{{firstName}}{{organisatorSuffix}}',
    );

    protected static $organisatorPrefix = array('(주)', '(주)', '(주)', '(유)');

    protected static $organisatorSuffix = array(
        '전자', '건설', '식품', '인터넷', '그룹', '은행', '보험', '제약', '금융', '네트웍스', '기획', '미디어', '연구소', '모바일', '스튜디오', '캐피탈',
    );

    public static function organisatorPrefix()
    {
        return static::randomElement(static::$organisatorPrefix);
    }

    public static function organisatorSuffix()
    {
        return static::randomElement(static::$organisatorSuffix);
    }
}
