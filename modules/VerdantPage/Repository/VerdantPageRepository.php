<?php

namespace VerdantPage\Repository;

use Core\Database\DB;
use MKrawczyk\FunQuery\FunQuery;

class VerdantPageRepository
{

    public function getToShow(string $id)
    {
        $item = DB::get("SELECT *
        FROM verdant_page vp
        JOIN verdant_page_details vpd ON vpd.id = vp.details_id
        WHERE vp.id = ?", [$id])[0] ?? null;
        if (!$item) return null;
        $item->nodes = DB::get("SELECT *
        FROM verdant_page_node vpn
        JOIN verdant_page_node_version vpnv ON vpnv.id = vpn.verdant_page_node_version_id
        WHERE vpnv.page_id = ?", [$id]);
        $item->nodes=FunQuery::from($item->nodes)->map(function($x) {
            $x->content=json_decode($x->content, false);
            return $x;
        })->toArray();
        return $item;
    }
}
