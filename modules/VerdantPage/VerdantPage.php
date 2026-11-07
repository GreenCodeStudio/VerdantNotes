<?php

namespace VerdantPage;

use Authorization\Authorization;
use Common\Guid;
use VerdantPage\Repository\VerdantNotebookRepository;
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
        return (new VerdantPageNodeRepository())->canEdit($nodeId, Authorization::getUserId());
    }

    public function updateNode(?string $nodeId, ?string $previousVersionId, $type, $content, $pageId, $sortOrder)
    {
        $previousVersion = $previousVersionId ? (new VerdantPageNodeRepository())->getVersion($previousVersionId) : null;
        if ($previousVersion) {
            if ($previousVersion->page_id != $pageId) {
                throw new \Exception("Page id mismatch");
            }
            if ($previousVersion->type != $type) {
                throw new \Exception("Node type mismatch");
            }
            if (!$sortOrder)
                $sortOrder = $previousVersion->sortOrder || time() << 16;
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
        return (new VerdantPageNodeRepository())->insertVersion($nodeId, $previousVersionId, $type, $content, $pageId, $sortOrder);
    }
    public function deleteNode(string $nodeId)
    {
        $node = (new VerdantPageNodeRepository())->get($nodeId);
        return (new VerdantPageNodeRepository())->insertVersion($nodeId, $node->previousVersionId, $node->type, null, $node->page_id, $node->sortOrder, true);

    }

    public function getNewEmpty()
    {
        return (object)[
            'id' => null,
            'title' => '',
            'public_permissions_read' => true,
            'public_permissions_edit' => true,
            'nodes' => [],
            'notebookPages' => [],
            'notebook_id' => null,
            'notebook_name' => '',
            'sortOrder' => time() << 16
        ];
    }

    public function createNewPage(string $pageId, ?string $notebookId = null)
    {
        if ($notebookId === null) {
            $notebookId = Guid::create();
            (new VerdantNotebookRepository())->insert(['id' => $notebookId, 'owner_id' => Authorization::getUserId()]);
        }
        $repository = (new VerdantPageRepository());
        $repository->insert($pageId);
        $repository->insertDetails(
            [
                'id' => Guid::create(),
                'page_id' => $pageId,
                'notebook_id' => $notebookId,
                'title' => '',
                'sortOrder' => time() << 16
            ]
        );

    }

    public function updateTitle(string $pageId, string $title)
    {
        $details = (new VerdantPageRepository())->getCurrentDetails($pageId);
        $repository = (new VerdantPageRepository());
        $details->previous_details_id = $details->id;
        $details->id = Guid::create();
        $details->title = $title;
        $repository->insertDetails((array)$details);
    }
}
