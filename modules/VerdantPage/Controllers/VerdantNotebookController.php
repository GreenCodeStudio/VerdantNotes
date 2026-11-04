<?php

namespace VerdantPage\Controllers;

use Authorization\Authorization;
use Common\PageStandardController;
use VerdantPage\VerdantNotebook;

class VerdantNotebookController extends PageStandardController
{
    public function my()
    {
        $notebooks = (new VerdantNotebook())->getByOwner(Authorization::getUserId());
        $this->addView('VerdantPage', 'MyNotebooks', ['notebooks' => $notebooks]);
    }

    public function hasPermission(string $methodName)
    {
        return Authorization::isLogged();
    }
}
