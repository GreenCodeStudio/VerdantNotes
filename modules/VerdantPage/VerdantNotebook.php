<?php

namespace VerdantPage;

class VerdantNotebook
{

    public function getByOwner($getUserId)
    {
        return (new Repository\VerdantNotebookRepository())->getByOwner($getUserId);
    }
}
