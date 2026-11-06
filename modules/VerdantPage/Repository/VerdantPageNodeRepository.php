<?php

namespace VerdantPage\Repository;

use Common\Guid;
use Core\Database\DB;
use MKrawczyk\FunQuery\FunQuery;

class VerdantPageNodeRepository
{
    public function canEdit(string $nodeId)
    {
        return DB::get("SELECT vpd.public_permissions_edit
        FROM verdant_page_node vpn
        JOIN verdant_page_node_version vpnv ON vpnv.id = vpn.verdant_page_node_version_id
        JOIN verdant_page vp ON vp.id = vpnv.page_id
        JOIN verdant_page_details vpd ON vpd.id = vp.details_id
        WHERE vpn.id = ?", [$nodeId])[0]->public_permissions_edit ?? false;
    }

    public function insertVersion(string $nodeId, $previousVersion, $type, $content, $pageId, $sortOrder)
    {
        $versionGuid = Guid::create();
        DB::insert('verdant_page_node_version', [
            'id' => $versionGuid,
            'node_id' => $nodeId,
            'content' => json_encode($content),
            'previous_version_id' => $previousVersion?->id,
            'type' => $type,
            'page_id' => $pageId,
            'sortOrder' => $sortOrder
        ]);
        DB::update('verdant_page_node', ['verdant_page_node_version_id' => $versionGuid], $nodeId);
        return $versionGuid;
    }

    public function getVersion(string $previousVersionId)
    {
        return DB::get("SELECT *
        FROM verdant_page_node_version
        WHERE id = ?", [$previousVersionId])[0] ?? null;
    }

    public function get(string $nodeId)
    {
        return DB::get("SELECT verdant_page_node_version.*
        FROM verdant_page_node
        JOIN verdant_page_node_version ON verdant_page_node_version.id = verdant_page_node.verdant_page_node_version_id
        WHERE verdant_page_node.id = ?", [$nodeId])[0] ?? null;
    }

    public function insert(string $nodeId)
    {
        DB::insert('verdant_page_node', [
            'id' => $nodeId,
            'verdant_page_node_version_id' => null
        ]);
    }

    public function exists(string $nodeId)
    {
        return DB::get("SELECT 1
        FROM verdant_page_node
        WHERE id = ?", [$nodeId])[0] ?? false;
    }
}
