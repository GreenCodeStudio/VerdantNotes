<?php

namespace VerdantPage;

use VerdantPage\Repository\VerdantPageNodeRepository;
use VerdantPage\Repository\VerdantPageRepository;

class VerdantPage
{

    public function getToShow(string $id)
    {
        return (new VerdantPageRepository())->getToShow($id);
    }

    public function canEditNode(string $nodeId)
    {
        return (new VerdantPageNodeRepository())->canEdit($nodeId);
    }

    public function updateNode(string $nodeId, string $previousVersionId, $content)
    {
        $previousVersion=(new VerdantPageNodeRepository())->getVersion($previousVersionId);
        return (new VerdantPageNodeRepository())->insertVersion($nodeId,$previousVersion,  $content);
    }
}
