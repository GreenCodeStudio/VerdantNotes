<?php

namespace VerdantPage\Exporters;

use VerdantPage\ConfigurableWysiwygHelper;

class RtfExporter
{
    public static function export($page)
    {
        echo '{\rtf ';
        echo $page->title;
        foreach ($page->nodes as $node) {
            if ($node->type === 'text/plain') {
                echo '\par ';
                echo $node->content->text;
            } else if ($node->type === 'text/xml+configurable-wysiwyg') {
                $document = \Mkrawczyk\ConfigurableWysiwyg\Document::loadXmlString($node->content?->xml);
                $document->config = ConfigurableWysiwygHelper::getConfig();
                $plaintextConverter = new \Mkrawczyk\ConfigurableWysiwyg\Converters\ToRTFConverter();
                echo $plaintextConverter->convert($document)."\n\n";
            }

        }
        echo '}';
    }
}
