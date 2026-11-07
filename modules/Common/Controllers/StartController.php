<?php

namespace Common\Controllers;

use Common\PageStandardController;
use Core\Exceptions\NotFoundException;
use DateTimeInterface;
use VerdantPage\VerdantPage;

class StartController extends PageStandardController
{
    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            ob_end_clean();
            http_response_code(204);
            header('allow: GET, PROPFIND, OPTIONS');
            header('DAV: 1');
            exit;
        } else if ($_SERVER['REQUEST_METHOD'] === 'PROPFIND') {
            header('DAV: 1');
            $base = 'http://'.$_SERVER['HTTP_HOST'];
            $base = '';
            ob_end_clean();
            http_response_code(207);
            header('Content-Type: application/xml; charset="utf-8"');

            $request = file_get_contents('php://input');

            $multistatus = \simplexml_load_string('<?xml version="1.0" encoding="utf-8" ?><multistatus xmlns="DAV:"/>');
            $r1 = $multistatus->addChild('response', null, 'DAV:');
            $r1->addChild('href', $base.'/');
            $r1->addChild('propstat', null, 'DAV:');
            $r1->propstat->addChild('prop', null, 'DAV:');
            $r1->propstat->prop->addChild('displayname', '/');
            $r1->propstat->prop->addChild('creationdate', date(DateTimeInterface::RFC1123));
            $r1->propstat->prop->addChild('getlastmodified', date(DateTimeInterface::RFC1123));
            $r1->propstat->prop->addChild('resourcetype');
            $r1->propstat->prop->resourcetype->addChild('collection');
            $r1->propstat->prop->addChild('getcontentlength', '0');
            $r1->propstat->prop->addChild('getetag', uniqid());
            $r1->propstat->addChild('status', 'HTTP/1.1 200 OK');

            $r2 = $multistatus->addChild('response', null, 'DAV:');
            $r2->addChild('href', $base.'/WebDAV/');
            $r2->addChild('propstat', null, 'DAV:');
            $r2->propstat->addChild('prop', null, 'DAV:');
            $r2->propstat->prop->addChild('displayname', 'WebDAV');
            $r2->propstat->prop->addChild('creationdate', date(DateTimeInterface::RFC1123));
            $r2->propstat->prop->addChild('getlastmodified', date(DateTimeInterface::RFC1123));
            $r2->propstat->prop->addChild('resourcetype');
            $r2->propstat->prop->resourcetype->addChild('collection');
            $r2->propstat->prop->addChild('getcontentlength', '0');
            $r2->propstat->prop->addChild('getetag', uniqid());
            $r2->propstat->addChild('status', 'HTTP/1.1 200 OK');
            echo str_replace('<D:/', '</D:', str_replace('<D:?', '<?', str_replace('<', '<D:', str_replace('xmlns=', 'xmlns:D=', $multistatus->asXML()))));
            exit;
        } else {
            $page = (new VerdantPage())->getNewEmpty();

            $this->addView('VerdantPage', 'VerdantPage', [
                'page' => $page,
                'showNode' => fn($x) => null,
                'canEdit' => true,
                'isAuthorized' => \Authorization\Authorization::isLogged(),
                'noName'=> fn($x, $default) => $x ?: $default
            ]);

        }
    }

    public function index_data()
    {
        return ['isUserLoggedIn' => \Authorization\Authorization::isLogged()];
    }
}

