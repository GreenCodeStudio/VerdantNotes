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

    public function insertVersion(string $nodeId, $previousVersion, $content)
    {
        $versionGuid = Guid::create();
        DB::insert('verdant_page_node_version', [
            'id' => $versionGuid,
            'node_id' => $nodeId,
            'content' => json_encode($content),
            'previous_version_id' => $previousVersion->id,
            'type' => $previousVersion->type,
            'page_id' => $previousVersion->page_id
        ]);
        DB::update('verdant_page_node', ['verdant_page_node_version_id' => $versionGuid], $nodeId);
    }

    public function getVersion(string $previousVersionId)
    {
        return DB::get("SELECT *
        FROM verdant_page_node_version
        WHERE id = ?", [$previousVersionId])[0] ?? null;
    }
}
