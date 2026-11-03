<?php

namespace VerdantPage\Controllers;
use DateTimeInterface;

class WebDAVController extends \Common\PageStandardController
{
    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            ob_end_clean();
            http_response_code(204);
            header('allow: GET, PROPFIND, OPTIONS');
            header('DAV: 1');
            exit;
        }
        $base='http://'.$_SERVER['HTTP_HOST'];
        $base='';
        ob_end_clean();
        http_response_code(207);
       header('Content-Type: application/xml; charset="utf-8"');
        header('DAV: 1');

        $multistatus=\simplexml_load_string('<?xml version="1.0" encoding="utf-8" ?><multistatus xmlns="DAV:"/>');
//        $r1=$multistatus->addChild('response', null, 'DAV:');
//        $r1->addChild('href', $base.'/WebDAV');
//        $r1->addChild('propstat', null, 'DAV:');
//        $r1->propstat->addChild('prop', null, 'DAV:');
//        $r1->propstat->prop->addChild('displayname', 'Root');
//        $r1->propstat->addChild('status', 'HTTP/1.1 200 OK');

        $r2=$multistatus->addChild('response', null, 'DAV:');
        $r2->addChild('href', $base.'/WebDAV/index/');
        $r2->addChild('propstat', null, 'DAV:');
        $r2->propstat->addChild('prop', null, 'DAV:');
        $r2->propstat->prop->addChild('displayname', 'VerdantPage');
        $r2->propstat->prop->addChild('resourcetype');
        $r2->propstat->prop->resourcetype->addChild('collection');
        $r2->propstat->prop->addChild('getcontentlength', '0');
        $r2->propstat->addChild('status', 'HTTP/1.1 200 OK');

        $r3=$multistatus->addChild('response', null, 'DAV:');
        $r3->addChild('href', $base.'/WebDAV/index/a.html');
        $r3->addChild('propstat', null, 'DAV:');
        $r3->propstat->addChild('prop', null, 'DAV:');
        $r3->propstat->prop->addChild('displayname', 'a.html');
        $r3->propstat->prop->addChild('getcontenttype', 'text/html');
        $r3->propstat->prop->addChild('getcontentlength', '1000000');
        $r3->propstat->prop->addChild('creationdate', date(DateTimeInterface::RFC1123));
        $r3->propstat->prop->addChild('getlastmodified', date(DateTimeInterface::RFC1123));
        $r3->propstat->prop->addChild('resourcetype');
        $r3->propstat->prop->addChild('getetag', uniqid());
        $r3->propstat->addChild('status', 'HTTP/1.1 200 OK');
        echo str_replace('<D:/', '</D:',str_replace('<D:?','<?',str_replace('<', '<D:', str_replace('xmlns=', 'xmlns:D=',$multistatus->asXML()))));
       exit;

    }
}
