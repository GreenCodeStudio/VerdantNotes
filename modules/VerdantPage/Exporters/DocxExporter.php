<?php
namespace VerdantPage\Exporters;

use VerdantPage\ConfigurableWysiwygHelper;
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
 <Override ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.styles+xml" PartName="/word/styles.xml"/>
</Types>');

        $this->rels=simplexml_load_string('<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>
</Relationships>
');     $this->documentRels=simplexml_load_string('<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
<Relationship Id="rId5" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>
');
        $this->document=simplexml_load_string('<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
  <w:body>
  </w:body>
</w:document>
',
            'SimpleXMLElement',
            0, 'http://schemas.openxmlformats.org/wordprocessingml/2006/main', false);

        $this->styles=simplexml_load_string('<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:styles
          xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
    <w:docDefaults>
        <w:rPrDefault>
            <w:rPr>
                <w:rFonts w:ascii="Calibri" w:eastAsia="Calibri" w:hAnsi="Calibri"
                          w:cs="Calibri"/>
                <w:lang w:val="pl-PL" w:eastAsia="pl-PL" w:bidi="ar-SA"/>

                <w:color w:val="000000"/>
            <w:sz w:val="22"/>
            <w:szCs w:val="22"/>
            </w:rPr>
        </w:rPrDefault>
        <w:pPrDefault/>
    </w:docDefaults>
        <w:style w:type="paragraph" w:styleId="Title">
        <w:name w:val="Title"/>
        <w:pPr>
            <w:keepNext w:val="1"/>
            <w:keepLines w:val="1"/>
            <w:pageBreakBefore w:val="0"/>
            <w:spacing w:after="60" w:before="0" w:lineRule="auto"/>
        </w:pPr>
        <w:rPr>
            <w:sz w:val="52"/>
            <w:szCs w:val="52"/>
                <w:color w:val="FF0000"/>
        </w:rPr>
    </w:style>
    <w:style w:type="paragraph" w:customStyle="1" w:styleId="S2">
        <w:name w:val="S2"/>
        <w:uiPriority w:val="10"/>
        <w:qFormat/>
        <w:pPr>
            <w:keepNext/>
            <w:keepLines/>
            <w:spacing w:after="60"/>
        </w:pPr>
        <w:rPr>
            <w:color w:val="00FF00"/>
            <w:sz w:val="52"/>
            <w:szCs w:val="52"/>
        </w:rPr>
    </w:style>
</w:styles>
');

    }

    public static function export($page)
    {
        $exporter = new DocxExporter();
        $exporter->exportDocument($page);
    }

    private function exportDocument($page)
    {
        $titleParagraph=$this->document->body->addChild('p');
        $titleParagraph->r->t = $page->title;
        $titleParagraph->addChild('pPr')->addChild('pStyle')->addAttribute('w:val', 'Title', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

        foreach ($page->nodes as $node) {
            if($node->type === 'text/plain') {
                $this->document->body->p->r->t = $node->content->text;
            }else if ($node->type === 'text/xml+configurable-wysiwyg') {
                $document = \Mkrawczyk\ConfigurableWysiwyg\Document::loadXmlString($node->content?->xml);
                $document->config = ConfigurableWysiwygHelper::getConfig();
                $plaintextConverter = new \Mkrawczyk\ConfigurableWysiwyg\Converters\ToPlaintextConverter();
$paragraph=$this->document->body->addChild('p');
                $paragraph->r->t = $plaintextConverter->convert($document);
                $paragraph->addChild('pPr')->addChild('pStyle')->addAttribute('w:val', 'S2', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
            }
        }


        $zip = new ZipArchive();
        $tmpFile = tempnam(sys_get_temp_dir(), 'docx');
        $zip->open($tmpFile, ZipArchive::CREATE);
        $zip->addFromString('[Content_Types].xml', $this->contentTypes->asXML());
        $zip->addFromString('_rels/.rels', $this->rels->asXML());
        $zip->addFromString('word/document.xml', $this->document->asXML());
        $zip->addFromString('word/_rels/document.xml.rels', $this->documentRels->asXML());
        $zip->addFromString('word/styles.xml', $this->styles->asXML());
        $zip->close();
        readfile($tmpFile);
        unlink($tmpFile);
    }
}
