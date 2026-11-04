<?php

namespace Common\Controllers;


use Authorization\Exceptions\BadAuthorizationException;
use Authorization\Exceptions\ExpiredTokenException;
use Common\PageStandardController;

class Authorization extends PageStandardController
{

    public function index()
    {
        $this->addView('Common', 'login', ['username'=>$_GET['username']??'']);
    }

    public function hasPermission(string $methodName)
    {
        return true;
    }
}
