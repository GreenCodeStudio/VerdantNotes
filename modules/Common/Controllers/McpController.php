<?php

namespace Common\Controllers;

class McpController extends \Common\PageStandardController
{
    public function index()
    {
        ob_end_clean();
//        http_response_code(401);
        header('WWW-Authenticate: Bearer resource_metadata="'.(($_SERVER['SERVER_PORT']==443)?'https':'http').'://'.$_SERVER['HTTP_HOST'].'/.well-known/oauth-protected-resource"');
        $post = file_get_contents("php://input");
        $request = json_decode($post);
        if ($request->jsonrpc == "2.0") {
            header('Content-Type: application/json');
            if ($request->method == 'server/discover') {
                echo json_encode([
                    'jsonrpc' => '2.0',
                    'id' => $request->id,
                    'result' => [
                        "resultType" => "complete",
                        "supportedVersions" => ["2026-07-28"],
                        "capabilities" => [
                            "tools" => (object)[]
                        ],
                        "cacheScope" => "private",
                        "ttlMs" => 0,
                        "_meta" => [
                            "io.modelcontextprotocol/serverInfo" => [
                                "name" => "verdant-notes-demo",
                                "version" => "1.0.0"
                            ]
                        ],
                    ]
                ]);
            } else if ($request->method == 'tools/list') {
                echo json_encode([
                    'jsonrpc' => '2.0',
                    'id' => $request->id,
                    'result' => [
                        "resultType" => "complete",
                        'ttlMs' => 600000,
                        'cacheScope' => 'private',
                        "tools" => [
                            [
                                "name" => "listMyNotebooks",
                                "title" => "List my notebooks",
                                "description" => "Lists Verdant Notebooks of the current user",
                                "inputSchema" => [
                                    'type' => 'object',
                                    'properties' => (object)[
                                        ]
                                ],
                                "annotations" => [
                                    "readOnlyHint" => true,
                                    "destructiveHint" => false,
                                    "idempotentHint" => true,
                                    "openWorldHint" => false
                                ],
                            ]
                        ]
                    ]
                ]);
            } else if ($request->method == 'tools/call') {
                if ($request->params->name == 'listMyNotebooks') {
                    echo json_encode([
                        'jsonrpc' => '2.0',
                        'id' => $request->id,
                        'result' => [
                            "resultType" => "complete",
                            "content"=>[],
                            "structuredContent" => [
                                "notebooks" => [
                                    [
                                        "name" => "notebook1",
                                        "title" => "Notebook 1",
                                        "description" => "This is notebook 1"
                                    ],
                                    [
                                        "name" => "notebook2",
                                        "title" => "Notebook 2",
                                        "description" => "This is notebook 2"
                                    ]
                                ]
                            ]
                        ]
                    ]);
                } else {
                    echo json_encode([
                        'jsonrpc' => '2.0',
                        'id' => $request->id,
                        'error' => [
                            "code" => -32601,
                            "message" => "Method not found"
                        ]
                    ]);
                }
            } else if ($request->method == 'initialize') {
                echo json_encode(
                    [
                        "jsonrpc" => "2.0",
                        "id" => $request->id,
                        "error" => [
                            "code" => -32022,
                            "message" => "Unsupported protocol version",
                            "data" => [
                                "supported" => ["2026-07-28"],
                                "requested" => $request->params->protocolVersion
                            ]
                        ]
                    ]
                );
            } else {
                dump($request);
                echo json_encode([
                    'jsonrpc' => '2.0',
                    'id' => $request->id,
                    'error' => [
                        "code" => -32601,
                        "message" => "Method not found"
                    ]
                ]);
            }
        }
        ob_flush();
    }

    public function postAction()
    {
        //do nothing, this is a JSON-RPC endpoint
    }
}
