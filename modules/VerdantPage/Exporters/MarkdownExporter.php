<?php

namespace VerdantPage\Exporters;

use VerdantPage\ConfigurableWysiwygHelper;

class MarkdownExporter
{
    public static function export($page)
    {
        echo '# '.$page->title."\n";
        foreach ($page->nodes as $node) {
            if ($node->type === 'text/plain') {
                echo $node->content->text."\n";
            } else if ($node->type === 'text/xml+configurable-wysiwyg') {
                $document = \Mkrawczyk\ConfigurableWysiwyg\Document::loadXmlString($node->content?->xml);
                $document->config = ConfigurableWysiwygHelper::getConfig();
                $plaintextConverter = new \Mkrawczyk\ConfigurableWysiwyg\Converters\ToPlaintextConverter();
                echo $plaintextConverter->convert($document)."\n\n";
            }

        }
    }
}
