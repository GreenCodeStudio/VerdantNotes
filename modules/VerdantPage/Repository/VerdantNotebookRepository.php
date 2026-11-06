<?php

namespace VerdantPage\Repository;

use Core\Database\DB;
use MKrawczyk\FunQuery\FunQuery;

class VerdantNotebookRepository
{
    public function insert($data)
    {
        DB::insert('verdant_notebook', $data);
    }

    public function getByOwner($getUserId)
    {
        return DB::get("SELECT verdant_notebook.*
        FROM verdant_notebook
        WHERE owner_id = ?", [$getUserId]);
    }

    public function getToShow(string $id)
    {
        $item= DB::get("SELECT verdant_notebook.*
        FROM verdant_notebook
        WHERE id = ?", [$id])[0]??null;
        if($item){
            $item->pages = DB::get("SELECT verdant_page.id, verdant_page_details.title
            FROM verdant_page
            JOIN verdant_page_details ON verdant_page.details_id = verdant_page_details.id
            WHERE notebook_id = ?", [$id]);
        }
        return $item;
    }
}
