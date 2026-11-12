<?php

namespace Common\Controllers;

class McpController extends \Common\PageStandardController
{
    public function index()
    {
        $post = file_get_contents("php://input");
        $request = json_decode($post);
        if ($request->jsonrpc == "2.0") {
            ob_end_clean();
            header('Content-Type: application/json');
            if ($request->method == 'server/discover') {
                echo json_encode([
                    'jsonrpc' => '2.0',
                    'id' => $request->id,
                    'result' => [
                        "resultType" => "complete",
                        "supportedVersions" => ["2026-07-28"],
                        "capabilities" => [
                            "tools" => []
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
                        "tools" => [
                            [
                                "name" => "listMyNotebooks",
                                "title" => "List my notebooks",
                                "description" => "Lists Verdant Notebooks of the current user",
                                "inputSchema" => [
                                    "type" => "object",
                                    "properties" => [

                                    ],
                                    "required" => []
                                ]
                            ]
                        ]
                    ]
                ]);
            }else if ($request->method == 'tools/call') {
                if ($request->params->toolName == 'listMyNotebooks') {
                    echo json_encode([
                        'jsonrpc' => '2.0',
                        'id' => $request->id,
                        'result' => [
                            "resultType" => "complete",
                            "output" => [
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

            exit;
        }
        dump($post);
    }
}
