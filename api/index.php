<?php
require_once "../vendor/autoload.php";
require_once "src/Main/Autoloader.php";

\Main\Autoloader::register();

$app = new \Slim\Slim();
//$app->add(new \Slim\Middleware\ContentTypes());

$app->response->headers->set('Content-Type', 'application/json');

$app->get('/posts', function () use($app) {
    $list = \Main\DAO\ListDAO::gets("posts", [
        "where"=> [
            "ORDER"=> "id DESC"
        ]
    ]);
    echo json_encode($list);
});

$app->post('/posts', function () use($app) {
    $data = $app->request->params();
    if(preg_match ( '/^application\/json/' , $app->request->getContentType())){
        $json = $app->request->getBody();
        $data = json_decode($json, true);
    }

    $db = Main\DB\Medoo\MedooFactory::getInstance();
    $id = $db->insert("posts", $data);
    $item = $db->get("posts", "*", ["id"=> $id]);
    echo json_encode($item);
});

$app->get('/posts/i:id', function ($id) use($app) {
    $db = Main\DB\Medoo\MedooFactory::getInstance();
    $item = $db->get("posts", "*", ["id"=> $id]);
    echo json_encode($item);
});

$app->put('/posts/i:id', function ($id) use($app) {
    $db = Main\DB\Medoo\MedooFactory::getInstance();
    $item = $db->update("posts", $app->request->put(), ["id"=> $id]);
    echo json_encode($item);
});

$app->delete('/posts/:id', function ($id) use($app) {
    $db = Main\DB\Medoo\MedooFactory::getInstance();
    $db->delete("posts", ["id"=> $id]);
    $success = $db->get("posts", "*", ["id"=> $id]);
    echo json_encode(["success"=> !$success]);
});

$app->run();