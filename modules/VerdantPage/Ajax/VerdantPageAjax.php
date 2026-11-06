<?php

namespace VerdantPage\Ajax;

use Authorization\Exceptions\UnauthorizedException;
use VerdantPage\VerdantPage;

class VerdantPageAjax extends \Core\AjaxController
{
    public function updateNode(string $nodeId, ?string $previousVersionId, string $type, $content, string $pageId, ?int $sortOrder=null)
    {
        $canEdit = (new VerdantPage())->canEditNode($nodeId);
        if (!$canEdit) {
            throw new UnauthorizedException();
        }
        (new VerdantPage())->updateNode($nodeId, $previousVersionId, $type, $content, $pageId, $sortOrder);
    }
    public function createNewPage(string $pageId, ?string $notebookId = null)
    {
        (new VerdantPage())->createNewPage($pageId, $notebookId);
    }
    public function updateTitle(string $pageId, string $title)
    {
        (new VerdantPage())->updateTitle($pageId, $title);
    }
}
