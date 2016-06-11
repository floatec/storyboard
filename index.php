<?php
header('Access-Control-Allow-Origin: *');
error_reporting(-1);
ini_set('display_errors', 'On');
require 'vendor/autoload.php';
$app = new \Slim\Slim();
session_start();
require_once("config.php");



$app->get('/', function() {
    $app = \Slim\Slim::getInstance();
    echo "Welcome to Slim 3.0 based API";
});
$app->get('/init/db', function() {
    $app = \Slim\Slim::getInstance();
    echo "DataBase was reseted";
    $app = \Slim\Slim::getInstance();
    $db = getDB();
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, 0);
    foreach(explode(";",file_get_contents("dump.sql")) as $sql){

        if(strlen($sql)>1){
            $sth = $db->prepare($sql);
            $sth->execute();
        }

    }
});
$app->get('/user/', function () {
    $app = \Slim\Slim::getInstance();
    $db = getDB();
    $sth = $db->prepare("SELECT id,username FROM user");
    $sth->execute();
    $results = $sth->fetchAll(PDO::FETCH_ASSOC);

    $app->response->setStatus(200);
    $app->response()->headers->set('Content-Type', 'application/json');
    echo json_encode($results);
});
$app->get('/article/', function () {
    $app = \Slim\Slim::getInstance();
    $db = getDB();
    $sth = $db->prepare("SELECT * FROM article,article_user WHERE article_id=article.id AND :user=user_id");
    $sth->bindValue(':user', ( $_SESSION["user"]));
    $sth->execute();
    $results = $sth->fetchAll(PDO::FETCH_ASSOC);

    $app->response->setStatus(200);
    $app->response()->headers->set('Content-Type', 'application/json');
    echo json_encode($results);
});
$app->get('/post/', function () {
    $app = \Slim\Slim::getInstance();
    $db = getDB();
    $sth = $db->prepare("INSERT INTO `article` (`id`, `title`, `notes`, `deadline`, `created_by`) VALUES (NULL, :title, :note', :deadline, :user);");
    $sth->bindValue(':user', ( $_SESSION["user"]));
    $sth->bindValue(':user', ( $_SESSION["user"]));
    $sth->execute();
    $results = $sth->fetchAll(PDO::FETCH_ASSOC);

    $app->response->setStatus(200);
    $app->response()->headers->set('Content-Type', 'application/json');
    echo json_encode($results);
});
$app->post('/article/user/:id', function ($id) {
    $app = \Slim\Slim::getInstance();
    $db = getDB();
    $sth = $db->prepare("SELECT user_id FROM article_user WHERE article_id=:id");
    $sth->bindValue(':id', $id);
    $sth->execute();
    $results = $sth->fetchAll(PDO::FETCH_ASSOC);

    $app->response->setStatus(200);
    $app->response()->headers->set('Content-Type', 'application/json');
    echo json_encode($results);
});
$app->put('/article/user/:id', function ($id) {
    $app = \Slim\Slim::getInstance();
    $db = getDB();
    $json = $app->request->getBody();
    $data = json_decode($json, true);
    print_r($app->request->getBody());
    $sth = $db->prepare("INSERT INTO article_user VALUES(:id,:userid)");
    $sth->bindValue(':id', $id);
    $sth->bindValue(':userid', $data["user"]);
    $sth->execute();


    $app->response->setStatus(200);
    $app->response()->headers->set('Content-Type', 'application/json');

});
$app->delete('/article/user/:id/:userid', function ($id,$userid) {
    $app = \Slim\Slim::getInstance();
    $db = getDB();
    $json = $app->request->getBody();
    $data = json_decode($json, true);
    print_r($app->request->getBody());
    $sth = $db->prepare("DELETE FROM article_user WHERE article_id=:id AND :userid=user_id");
    $sth->bindValue(':id', $id);
    $sth->bindValue(':userid', $userid);
    $sth->execute();


    $app->response->setStatus(200);
    $app->response()->headers->set('Content-Type', 'application/json');

});



$app->post('/login/', function () {
    $app = \Slim\Slim::getInstance();
    $db = getDB();

    $json = $app->request->getBody();
    $data = json_decode($json, true);

    $sth = $db->prepare("SELECT * FROM user WHERE :username=username AND :password=password");
    $sth->bindValue(':username', ( $data['username']));
    $sth->bindValue(':password', ( $data['password']));
    $sth->execute();
    $results = $sth->fetchAll(PDO::FETCH_ASSOC);

    if($results) {

        $_SESSION["user"]=$results[0]["id"];
        $app->response->setStatus(200);
        $app->response()->headers->set('Content-Type', 'application/json');
        echo json_encode(["status"=>"OK",'msg'=>"loged in"]);
        $db = null;
    } else {
        $app->response->setStatus(200);
        $app->response()->headers->set('Content-Type', 'application/json');
        echo json_encode(["status"=>"FAIL",'msg'=>"no valid credentials"]);
        $db = null;
    }
});
$app->post('/logout/', function () {
    $app = \Slim\Slim::getInstance();
   unset( $_SESSION["user"]);

});

$app->run();
