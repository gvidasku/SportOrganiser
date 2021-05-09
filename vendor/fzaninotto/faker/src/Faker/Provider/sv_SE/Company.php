<?php

namespace Faker\Provider\sv_SE;

class organisator extends \Faker\Provider\organisator
{
    protected static $formats = array(
        '{{lastName}} {{organisatorSuffix}}',
        '{{lastName}} {{organisatorSuffix}}',
        '{{lastName}} {{organisatorSuffix}}',
        '{{firstName}} {{lastName}} {{organisatorSuffix}}',
        '{{lastName}} & {{lastName}} {{organisatorSuffix}}',
        '{{lastName}} & {{lastName}}',
        '{{lastName}} och {{lastName}}',
        '{{lastName}} och {{lastName}} {{organisatorSuffix}}'
    );

    protected static $organisatorSuffix = array('AB', 'HB');
    
    protected static $sporteventTitles = array('Automationsingenjör', 'Bagare', 'Digital Designer', 'Ekonom', 'Ekonomichef', 'Elektronikingenjör', 'Försäljare', 'Försäljningschef', 'Innovationsdirektör', 'Investeringsdirektör', 'Journalist', 'Kock', 'Kulturstrateg', 'Läkare', 'Lokförare', 'Mäklare', 'Programmerare', 'Projektledare', 'Sjuksköterska', 'Utvecklare', 'UX Designer', 'Webbutvecklare');
    
    public function sporteventTitle()
    {
        return static::randomElement(static::$sporteventTitles);
    }
}
