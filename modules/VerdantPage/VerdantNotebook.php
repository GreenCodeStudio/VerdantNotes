<?php

namespace VerdantPage;

use Authorization\Authorization;
use Common\Guid;
use VerdantPage\Repository\VerdantNotebookRepository;

class VerdantNotebook
{

    public function getByOwner($getUserId)
    {
        return (new Repository\VerdantNotebookRepository())->getByOwner($getUserId);
    }

    public function insert($data)
    {
        $filtered['id'] = Guid::create();
        $filtered['owner_id'] = Authorization::getUserId();
        $filtered['name'] = $data->name;
        (new Repository\VerdantNotebookRepository())->insert($filtered);
        return $filtered['id'];
    }

    public function getToShow(string $id)
    {
        return (new VerdantNotebookRepository())->getToShow($id);
    }
}
