<?php

namespace VerdantPage;

use Common\Guid;
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
        if (!(new VerdantPageNodeRepository())->exists($nodeId)) {
            return true;
        }
        return (new VerdantPageNodeRepository())->canEdit($nodeId);
    }

    public function updateNode(?string $nodeId, ?string $previousVersionId, $type, $content, $pageId)
    {
        $previousVersion = $previousVersionId ? (new VerdantPageNodeRepository())->getVersion($previousVersionId) : null;
        if ($previousVersion) {
            if ($previousVersion->page_id != $pageId) {
                throw new \Exception("Page id mismatch");
            }
            if ($previousVersion->type != $type) {
                throw new \Exception("Node type mismatch");
            }
        } else {
            $node = (new VerdantPageNodeRepository())->get($nodeId);
            if ($node) {
                if ($node->page_id != $pageId) {
                    throw new \Exception("Page id mismatch");
                }
                if ($node->type != $type) {
                    throw new \Exception("Node type mismatch");
                }
            } else {
                (new VerdantPageNodeRepository())->insert($nodeId);
            }
        }
        return (new VerdantPageNodeRepository())->insertVersion($nodeId, $previousVersionId, $type, $content, $pageId);
    }

    public function getNewEmpty()
    {
        return (object)[
            'id' => null,
            'title' => '',
            'public_permissions_read' => true,
            'public_permissions_edit' => true,
            'nodes' => []
        ];
    }

    public function createNewPage(string $pageId)
    {
        $repository = (new VerdantPageRepository());
        $repository->insert($pageId);
        $repository->insertDetails(
            [
                'id' => Guid::create(),
                'page_id' => $pageId,
                'title' => '',
                'public_permissions_read' => false,
                'public_permissions_edit' => false
            ]
        );

    }
}
