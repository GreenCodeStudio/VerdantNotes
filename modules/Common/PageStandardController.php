<?php

namespace Common;

use Authorization\Authorization;

class PageStandardController extends \Core\StandardController
{
    public function getPageTitle(): string
    {
        return 'Verdant Notes';
    }
    public function postAction()
    {
        $userData = Authorization::getUserData();
        $userPreferences = $userData?->preferences ?? [];
        $userPreferences = [...$userPreferences, ...($_GET['_preferences']??[])];
        require __DIR__.'/Views/template.php';
    }
}

