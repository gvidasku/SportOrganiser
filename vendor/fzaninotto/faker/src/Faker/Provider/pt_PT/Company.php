<?php

namespace Faker\Provider\pt_PT;

class organisator extends \Faker\Provider\organisator
{
    protected static $formats = array(
        '{{lastName}} {{organisatorSuffix}}',
        '{{lastName}} {{lastName}}',
        '{{lastName}} e {{lastName}}',
        '{{lastName}} {{lastName}} {{organisatorSuffix}}',
        'Grupo {{lastName}} {{organisatorSuffix}}'
    );

    protected static $organisatorSuffix = array('e Filhos', 'e Associados', 'Lda.', 'S.A.');
}
