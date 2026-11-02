<?php

namespace VerdantPage\Ajax;

use Authorization\Exceptions\UnauthorizedException;
use VerdantPage\VerdantPage;

class VerdantPageAjax extends \Core\AjaxController
{
    public function updateNode(string $nodeId, string $previousVersionId, $content)
    {
        $canEdit = (new VerdantPage())->canEditNode($nodeId);
        if (!$canEdit) {
            throw new UnauthorizedException();
        }
        (new VerdantPage())->updateNode($nodeId, $previousVersionId, $content);
    }
}
