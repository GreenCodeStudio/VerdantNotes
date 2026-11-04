<?php

namespace VerdantPage\Repository;

use Core\Database\DB;
use MKrawczyk\FunQuery\FunQuery;

class VerdantPageRepository
{

    public function getToShow(string $id)
    {
        $item = DB::get("SELECT vp.id, vpd.title, vn.public_permissions_read, vn.public_permissions_edit, vn.name as notebook_name, vn.id as notebook_id
        FROM verdant_page vp
        JOIN verdant_page_details vpd ON vpd.id = vp.details_id
        JOIN verdant_notebook vn ON vn.id = vpd.notebook_id
        WHERE vp.id = ?", [$id])[0] ?? null;
        if (!$item) return null;
        $item->nodes = DB::get("SELECT *
        FROM verdant_page_node vpn
        JOIN verdant_page_node_version vpnv ON vpnv.id = vpn.verdant_page_node_version_id
        WHERE vpnv.page_id = ?", [$id]);
        $item->nodes = FunQuery::from($item->nodes)->map(function ($x) {
            $x->content = json_decode($x->content, false);
            return $x;
        })->toArray();
        $item->notebookPages=DB::get("SELECT vp.id, vpd.title
        FROM verdant_notebook vn
        JOIN verdant_page_details vpd ON vpd.notebook_id = vn.id
        JOIN verdant_page vp ON vp.details_id = vpd.id
        WHERE vn.id = ?", [$item->notebook_id]);
        return $item;
    }

    public function insert(string $pageId)
    {
        DB::insert('verdant_page', ['id' => $pageId]);
    }

    public function insertDetails(array $array)
    {
        DB::insert('verdant_page_details', $array);
        DB::update('verdant_page', ['details_id' => $array['id']], $array['page_id']);
    }
}
