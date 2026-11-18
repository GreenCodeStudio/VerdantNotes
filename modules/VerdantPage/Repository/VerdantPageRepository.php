<?php

namespace VerdantPage\Repository;

use Core\Database\DB;
use MKrawczyk\FunQuery\FunQuery;

class VerdantPageRepository
{

    public function getToShow(string $id)
    {
        $item = DB::get("SELECT vp.id, vpd.title, vn.public_permissions_read, vn.public_permissions_edit, vn.name as notebook_name, vn.id as notebook_id, vn.owner_id as notebook_owner_id
        FROM verdant_page vp
        JOIN verdant_page_details vpd ON vpd.id = vp.details_id
        JOIN verdant_notebook vn ON vn.id = vpd.notebook_id
        WHERE vp.id = ?", [$id])[0] ?? null;
        if (!$item) return null;
        $item->nodes = DB::get("SELECT *
        FROM verdant_page_node vpn
        JOIN verdant_page_node_version vpnv ON vpnv.id = vpn.verdant_page_node_version_id
        WHERE vpnv.page_id = ? AND vpnv.isDeleted = 0
        ORDER BY vpnv.sortOrder", [$id]);
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
    public function getToShowHistory(string $id, \DateTime $historyStamp)
    {
        $item = DB::get("SELECT vp.id, vpd.title, vn.public_permissions_read, vn.public_permissions_edit, vn.name as notebook_name, vn.id as notebook_id, vn.owner_id as notebook_owner_id
        FROM verdant_page vp
        JOIN verdant_page_details vpd ON vpd.page_id = vp.id
        JOIN verdant_notebook vn ON vn.id = vpd.notebook_id
        WHERE vp.id = ? AND vpd.stamp <= ?
        ORDER BY vpd.stamp DESC LIMIT 1
        ", [$id, $historyStamp])[0] ?? null;
        if (!$item) return null;
        $item->nodes = DB::get("
        SELECT *
        FROM (
            SELECT vpnv.node_id, MAX(vpnv.stamp) as stamp
            FROM verdant_page_node_version vpnv
            WHERE vpnv.page_id = ? AND vpnv.stamp<= ?
            GROUP BY vpnv.node_id
        ) sub
        JOIN verdant_page_node_version vpnv 
            ON vpnv.node_id = sub.node_id AND vpnv.stamp = sub.stamp
        JOIN verdant_page_node vpn 
            ON vpn.verdant_page_node_version_id = vpnv.id
        WHERE vpnv.isDeleted = 0
        ORDER BY vpnv.sortOrder
        ", [$id, $historyStamp]);
        $item->nodes = FunQuery::from($item->nodes)->map(function ($x) {
            $x->content = json_decode($x->content, false);
            return $x;
        })->toArray();
        return $item;
    }

    public function insert(string $pageId)
    {
        DB::insert('verdant_page', ['id' => $pageId]);
    }

    public function insertDetails(array $array)
    {
        $array['stamp'] = date('Y-m-d H:i:s');
        DB::insert('verdant_page_details', $array);
        DB::update('verdant_page', ['details_id' => $array['id']], $array['page_id']);
    }

    public function getCurrentDetails(string $pageId)
    {
        return DB::get("SELECT verdant_page_details.*
        FROM verdant_page_details
        JOIN verdant_page ON verdant_page.details_id = verdant_page_details.id
        WHERE verdant_page.id = ?
        LIMIT 1", [$pageId])[0] ?? null;
    }

    public function getHistory(string $id)
    {
        return DB::get("
            SELECT distinct stamp
            FROM(
                SELECT vpnv.stamp
                FROM verdant_page vp
                JOIN verdant_page_node_version vpnv ON vpnv.page_id = vp.id
                WHERE vp.id = :pageId AND vpnv.stamp IS NOT NULL
                
                UNION
                SELECT vpd.stamp
                FROM verdant_page vp
                JOIN verdant_page_details vpd ON vpd.page_id = vp.id
                WHERE vp.id = :pageId AND vpd.stamp IS NOT NULL
            ) sub
            ORDER BY stamp ASC   
        ", ['pageId' => $id]);
    }
}
