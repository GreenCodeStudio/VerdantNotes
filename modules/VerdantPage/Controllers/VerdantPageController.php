<?php

namespace VerdantPage\Controllers;

use Common\PageStandardController;
use Core\Exceptions\NotFoundException;
use VerdantPage\VerdantPage;

class VerdantPageController extends PageStandardController
{
    public function show(string $id)
    {
        $page = (new VerdantPage())->getToShow($id);
        if (!$page) throw new NotFoundException();

        dump($page);
        $this->addView('VerdantPage', 'VerdantPage', ['page' => $page, 'showNode' => fn($x) => $this->showNode($x)]);
    }

    private function showNode($node)
    {
        dump($node);
        if ($node->type === 'text/plain') {
            return '<section>'.htmlspecialchars($node->content->text).'</section>';
        }
    }
}
