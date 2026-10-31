<?php
namespace VerdantPage\Exporters;

class RtfExporter
{
    public static function export($page)
    {
        echo '{\rtf ';
        echo $page->title;
        foreach ($page->nodes as $node) {
            if($node->type === 'text/plain') {
                echo '\par ';
                echo $node->content->text;
            }

        }
        echo '}';
    }
}
