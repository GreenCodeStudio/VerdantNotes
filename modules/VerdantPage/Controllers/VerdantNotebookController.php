<?php

namespace VerdantPage\Controllers;

use Authorization\Authorization;
use Common\PageStandardController;
use Core\Exceptions\NotFoundException;
use VerdantPage\VerdantNotebook;
use VerdantPage\VerdantPage;

class VerdantNotebookController extends PageStandardController
{
    public function my()
    {
        $notebooks = (new VerdantNotebook())->getByOwner(Authorization::getUserId());
        $this->addView('VerdantPage', 'MyNotebooks', ['notebooks' => $notebooks,
            'noName'=> fn($x, $default) => $x ?: $default]);
    }

    public function hasPermission(string $methodName)
    {
        return Authorization::isLogged();
    }
    public function show(string $id)
    {
        $notebook = (new VerdantNotebook())->getToShow($id);
        if (!$notebook) throw new NotFoundException();
        $this->addView('VerdantPage', 'VerdantNotebook', [
            'notebook' => $notebook,
            'isAuthorized' => \Authorization\Authorization::isLogged(),
                'noName'=> fn($x, $default) => $x ?: $default
        ]);
    }
}
