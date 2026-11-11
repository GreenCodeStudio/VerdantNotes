<?php

namespace VerdantPage;

use Mkrawczyk\ConfigurableWysiwyg\Config;
use Mkrawczyk\ConfigurableWysiwyg\Elements\Paragraph;

class ConfigurableWysiwygHelper
{
    public static function getConfig(): Config
    {
        $config = new \Mkrawczyk\ConfigurableWysiwyg\Config();
        $config->addAllowedElement(new Paragraph());
        return $config;
    }
}
