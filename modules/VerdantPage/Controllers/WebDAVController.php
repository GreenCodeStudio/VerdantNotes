<?php

namespace VerdantPage\Controllers;
class WebDAVController extends \Common\PageStandardController
{
    public function index()
    {
        $base='http://'.$_SERVER['HTTP_HOST'];
        ob_end_clean();
        http_response_code(207);
       header('Content-Type: application/xml; charset="utf-8"');

        $multistatus=\simplexml_load_string('<?xml version="1.0" encoding="utf-8" ?><multistatus xmlns="DAV:"/>');
//        $r1=$multistatus->addChild('response', null, 'DAV:');
//        $r1->addChild('href', $base.'/WebDAV');
//        $r1->addChild('propstat', null, 'DAV:');
//        $r1->propstat->addChild('prop', null, 'DAV:');
//        $r1->propstat->prop->addChild('displayname', 'Root');
//        $r1->propstat->addChild('status', 'HTTP/1.1 200 OK');

        $r2=$multistatus->addChild('response', null, 'DAV:');
        $r2->addChild('href', $base.'/WebDAV/index');
        $r2->addChild('propstat', null, 'DAV:');
        $r2->propstat->addChild('prop', null, 'DAV:');
        $r2->propstat->prop->addChild('displayname', 'VerdantPage');
        $r2->propstat->prop->addChild('resourcetype');
        $r2->propstat->prop->resourcetype->addChild('collection');
        $r2->propstat->addChild('status', 'HTTP/1.1 200 OK');

        $r3=$multistatus->addChild('response', null, 'DAV:');
        $r3->addChild('href', $base.'/WebDAV/index/a.html');
        $r3->addChild('propstat', null, 'DAV:');
        $r3->propstat->addChild('prop', null, 'DAV:');
        $r3->propstat->prop->addChild('displayname', 'VerdantPag3e');
        $r3->propstat->prop->addChild('getcontenttype', 'text/html');
        $r3->propstat->prop->addChild('getcontentlength', '1000000');
        $r3->propstat->addChild('status', 'HTTP/1.1 200 OK');
        echo $multistatus->asXML();
       exit;

    }
}
