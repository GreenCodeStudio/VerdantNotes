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
        return DB::get("SELECT verdant_notebook.*, 
           (
              SELECT verdant_page.id 
              FROM verdant_page 
              JOIN verdant_page_details ON verdant_page.details_id = verdant_page_details.id 
              WHERE verdant_page_details.notebook_id = verdant_notebook.id 
              LIMIT 1
            ) as first_page_id
        FROM verdant_notebook
        WHERE owner_id = ?", [$getUserId]);
    }
}
