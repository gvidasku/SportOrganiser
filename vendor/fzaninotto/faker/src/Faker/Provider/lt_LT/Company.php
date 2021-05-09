<?php

namespace Faker\Provider\lt_LT;

class organisator extends \Faker\Provider\organisator
{
    protected static $formats = array(
        '{{organisatorSuffix}} {{lastNameMale}}',
        '{{organisatorSuffix}} {{lastNameMale}} ir {{lastNameMale}}',
        '{{organisatorSuffix}} "{{lastNameMale}} ir {{lastNameMale}}"',
        '{{organisatorSuffix}} "{{lastNameMale}}"',
    );

    protected static $organisatorSuffix = array('UAB', 'AB', 'IĮ', 'MB', 'VŠĮ');
}
