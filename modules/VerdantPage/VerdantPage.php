<?php
namespace VerdantPage;

use VerdantPage\Repository\VerdantPageRepository;

class VerdantPage
{

    public function getToShow(string $id)
    {
        return (new VerdantPageRepository())->getToShow($id);
    }
}
