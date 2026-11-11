<?php

namespace VerdantPage\Controllers;

use Common\PageStandardController;
use Core\Exceptions\NotFoundException;
use Mkrawczyk\ConfigurableWysiwyg\Converters\ToHtmlConverter;
use Mkrawczyk\ConfigurableWysiwyg\Converters\ToPlaintextConverter;
use Mkrawczyk\ConfigurableWysiwyg\Elements\Paragraph;
use VerdantPage\ConfigurableWysiwygHelper;
use VerdantPage\Exporters\DocxExporter;
use VerdantPage\Exporters\MarkdownExporter;
use VerdantPage\Exporters\RtfExporter;
use VerdantPage\VerdantPage;

class VerdantPageController extends PageStandardController
{
    public function show(string $id)
    {
        $page = (new VerdantPage())->getToShow($id);
        if (!$page) throw new NotFoundException();

        $canEdit = $page->public_permissions_edit || (\Authorization\Authorization::isLogged() && $page->notebook_owner_id === \Authorization\Authorization::getUserId());
        $this->addView('VerdantPage', 'VerdantPage', [
            'page' => $page,
            'showNode' => fn($x) => $this->showNode($x, $canEdit),
            'canEdit' => $canEdit,
            'isAuthorized' => \Authorization\Authorization::isLogged(),
            'noName' => fn($x, $default) => $x ?: $default
        ]);
        $this->headLinks[] = ['rel' => 'alternate', 'type' => 'application/pdf', 'title' => 'PDF', 'href' => '/VerdantPage/pdf/'.$page->id];
        $this->headLinks[] = ['rel' => 'alternate', 'type' => 'application/rtf', 'title' => 'RTF', 'href' => '/VerdantPage/rtf/'.$page->id];
        $this->headLinks[] = ['rel' => 'alternate', 'type' => 'text/markdown', 'title' => 'Markdown', 'href' => '/VerdantPage/md/'.$page->id];
        $this->headLinks[] = ['rel' => 'alternate', 'type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'title' => 'DOCX', 'href' => '/VerdantPage/docx/'.$page->id];
    }

    public function pdf(string $id)
    {
        $page = (new VerdantPage())->getToShow($id);
        if (!$page) throw new NotFoundException();
        ob_end_clean();
        header('Content-Type: application/pdf');
        echo "%PDF-1.4
1 0 obj
<< /Type /Catalog /Pages 2 0 R >>
endobj
2 0 obj
<< /Type /Pages /Kids [3 0 R] /Count 1 >>
endobj
3 0 obj
<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>
endobj
4 0 obj
<<  >>
stream
BT
/F1 24 Tf
100 700 Td
(".$page->title.") Tj
ET
endstream
endobj
5 0 obj
<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>
endobj
xref
0 6
0000000000 65535 f 
000000000. 00000 n 
000000009 00000 n 
000000058 00000 n 
000000173 00000 n 
000000267 00000 n 
trailer
<< /Size 6 /Root 1 0 R >>
startxref
349
%%EOF";
        ob_end_flush();
        exit;
    }

    public function rtf(string $id)
    {
        $page = (new VerdantPage())->getToShow($id);
        if (!$page) throw new NotFoundException();
        ob_end_clean();
        header('Content-Type: application/rtf');
        RtfExporter::export($page);
        ob_end_flush();
        exit;
    }

    public function md(string $id)
    {
        $page = (new VerdantPage())->getToShow($id);
        if (!$page) throw new NotFoundException();
        ob_end_clean();
        header('Content-Type: text/markdown');
        MarkdownExporter::export($page);
        ob_end_flush();
        exit;
    }

    public function docx(string $id)
    {
        $page = (new VerdantPage())->getToShow($id);
        if (!$page) throw new NotFoundException();
        ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        DocxExporter::export($page);
        ob_end_flush();
        exit;
    }

    private function showNode($node, bool $canEdit)
    {
        if ($node->type === 'text/plain') {
            if ($canEdit) {
                return '<section data-node="'.htmlspecialchars(json_encode($node)).'">'.htmlspecialchars($node->content->text).'</section>';
            }
            return '<section>'.htmlspecialchars($node->content->text).'</section>';
        } else if ($node->type === 'text/xml+configurable-wysiwyg') {
            $document = \Mkrawczyk\ConfigurableWysiwyg\Document::loadXmlString($node->content?->xml);
            $document->config = ConfigurableWysiwygHelper::getConfig();
            if ($canEdit) {
                return '<section data-node="'.htmlspecialchars(json_encode($node)).'">'.(new ToHtmlConverter())->convert($document).'</section>';
            }
            return '<section>'.(new ToHtmlConverter())->convert($document).'</section>';
        }
    }

    public function show_data()
    {
        return ['isUserLoggedIn' => \Authorization\Authorization::isLogged()];
    }
}
