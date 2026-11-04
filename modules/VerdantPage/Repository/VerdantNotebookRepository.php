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
}
