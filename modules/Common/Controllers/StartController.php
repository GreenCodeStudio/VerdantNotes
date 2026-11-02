<?php

namespace Common\Controllers;

use Common\PageStandardController;

class StartController extends PageStandardController
{
    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'PROPFIND') {
            $base='http://'.$_SERVER['HTTP_HOST'];
            ob_end_clean();
            http_response_code(207);
            header('Content-Type: application/xml; charset="utf-8"');

            $request=file_get_contents('php://input');

            $multistatus=\simplexml_load_string('<?xml version="1.0" encoding="utf-8" ?><multistatus xmlns="DAV:"/>');
            $r1=$multistatus->addChild('response', null, 'DAV:');
            $r1->addChild('href', $base.'/WebDAV');
            $r1->addChild('propstat', null, 'DAV:');
            $r1->propstat->addChild('prop', null, 'DAV:');
            $r1->propstat->prop->addChild('displayname', 'Root');
            $r1->propstat->prop->addChild('resourcetype');
            $r1->propstat->prop->resourcetype->addChild('collection');
            $r1->propstat->addChild('status', 'HTTP/1.1 200 OK');
            echo $multistatus->asXML();
            exit;
        }
    }
}

