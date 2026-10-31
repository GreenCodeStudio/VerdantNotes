<?php
namespace VerdantPage\Exporters;

use ZipArchive;

class DocxExporter
{
    private $contentTypes;
    private $rels;
    private $document;
    public function __construct()
    {
        $this->contentTypes=simplexml_load_string('<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
<Default Extension="xml" ContentType="application/xml"/>
<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>

</Types>');

        $this->rels=simplexml_load_string('<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>
</Relationships>
');
        $this->document=simplexml_load_string('<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
  <w:body>
  </w:body>
</w:document>
');

    }

    public static function export($page)
    {
        $exporter = new DocxExporter();
        $exporter->exportDocument($page);
    }

    private function exportDocument($page)
    {
        $this->document->body->p->r->t = $page->title;

        foreach ($page->nodes as $node) {
            if($node->type === 'text/plain') {
                $this->document->body->p->r->t = $node->content->text;
            }
        }


        $zip = new ZipArchive();
        $tmpFile = tempnam(sys_get_temp_dir(), 'docx');
        $zip->open($tmpFile, ZipArchive::CREATE);
        $zip->addFromString('[Content_Types].xml', $this->contentTypes->asXML());
        $zip->addFromString('_rels/.rels', $this->rels->asXML());
        $zip->addFromString('word/document.xml', $this->document->asXML());
        $zip->close();
        readfile($tmpFile);
        unlink($tmpFile);
    }
}
