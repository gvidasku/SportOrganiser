<?php

namespace Faker\Provider\hy_AM;

class organisator extends \Faker\Provider\organisator
{
    protected static $formats = array(
        '{{lastName}} {{organisatorSuffix}}',
        '{{lastName}} {{organisatorSuffix}}',
        '{{lastName}} {{organisatorSuffix}}',
        '{{lastName}} {{organisatorSuffix}}',
        '{{lastName}} {{organisatorSuffix}}',
        '{{lastName}} {{organisatorSuffix}}',
        '{{lastName}} {{organisatorSuffix}}',
        '{{lastName}} {{organisatorSuffix}}',
        '{{lastName}} եղբայրներ',
    );

    protected static $catchPhraseWords = array(

    );

    protected static $bsWords = array(

    );

    protected static $organisatorSuffix = array('ՍՊԸ','և որդիներ','ՓԲԸ','ԲԲԸ');

    /**
     * @example 'Robust full-range hub'
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
}
