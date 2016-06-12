<?php
header('Access-Control-Allow-Origin: http://flobanana:8000');
header("Access-Control-Allow-Credentials: true");

$STATUS_FINISHED=3;
error_reporting(-1);
ini_set('display_errors', 'On');
require 'vendor/autoload.php';
$app = new \Slim\Slim();
session_start();
require_once("config.php");

//$_SESSION["user"]=1;

$app->get('/', function() {
    $app = \Slim\Slim::getInstance();
    $app->redirect("/public_html/scrumj-frontend/app/index.html#!/landingpage");
    echo "Welcome to Slim 3.0 based API";
});
$app->get('/init/db', function() {
    $app = \Slim\Slim::getInstance();

    $app = \Slim\Slim::getInstance();
    $db = getDB();
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, 0);
    foreach(explode(";",file_get_contents("dump.sql")) as $sql){

        if(strlen($sql)>1){
            $sth = $db->prepare($sql);
            $sth->execute();
        }

    }
    echo "DataBase was reseted";
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
$app->get('/article/:id', function ($id) {
    getArticle($id);
});
$app->get('/article/lite/:id', function ($id) {
    getArticle($id,true);
});


function getArticle($id,$lite=false){
    $app = \Slim\Slim::getInstance();
    $db = getDB();
    $sth = $db->prepare("SELECT * FROM article WHERE id=:id");

    $sth->bindValue(':id', $id);
    $sth->execute();
    $article = $sth->fetch(PDO::FETCH_ASSOC);
    $sth = $db->prepare("SELECT * FROM pakage WHERE article_id=:id");
    $sth->bindValue(':id', $id);
    $sth->execute();
    $article["pakage"]= $sth->fetchAll(PDO::FETCH_ASSOC);
    $lanes=[];
    $lanes['todo']=[];
    $lanes['inprogress']=[];
    $lanes['review']=[];
    $lanes['done']=[];
    if(!$lite){
        for($i=0;$i<sizeof($article["pakage"]);$i++){
            $pid=$article["pakage"][$i]['id'];
            $sth = $db->prepare("SELECT * FROM task WHERE pakage_id=:id");
            $sth->bindValue(':id', $pid);
            $sth->execute();
            $article['pakage'][$i]['task']=$sth->fetchAll(PDO::FETCH_ASSOC);
            $sth = $db->prepare("SELECT * FROM task WHERE pakage_id=:id AND state=0");
            $sth->bindValue(':id', $pid);
            $sth->execute();
            $lanes['todo']=array_merge($lanes['todo'],$sth->fetchAll(PDO::FETCH_ASSOC));
            $sth = $db->prepare("SELECT * FROM task WHERE pakage_id=:id AND state=1");
            $sth->bindValue(':id', $pid);
            $sth->execute();
            $lanes['inprogress']=array_merge($lanes['inprogress'],$sth->fetchAll(PDO::FETCH_ASSOC));
            $sth = $db->prepare("SELECT * FROM task WHERE pakage_id=:id AND state=2");
            $sth->bindValue(':id', $pid);
            $sth->execute();
            $lanes['review']=array_merge($lanes['review'],$sth->fetchAll(PDO::FETCH_ASSOC));
            $sth = $db->prepare("SELECT * FROM task WHERE pakage_id=:id AND state=3");
            $sth->bindValue(':id', $pid);
            $sth->execute();
            $lanes['done']=array_merge($lanes['done'],$sth->fetchAll(PDO::FETCH_ASSOC));
        }
        $article["lanes"]=$lanes;
    }

  /*  $sth = $db->prepare("SELECT Count(task.id) FROM article,pakage,task WHERE article.id=:id AND article.id=pakage.article_id AND article.id=pakage.article_id ");

    $sth->bindValue(':id', $id);
    $sth->execute();
    $article = $sth->fetch(PDO::FETCH_ASSOC);*/

    $app->response->setStatus(200);
    $app->response()->headers->set('Content-Type', 'application/json');
    echo json_encode($article);
}

$app->post('/article/', function () {
    $app = \Slim\Slim::getInstance();
    $db = getDB();
    $json = $app->request->getBody();
    $data = json_decode($json, true);
    $sth = $db->prepare("INSERT INTO `article` (`id`, `title`, `notes`, `deadline`, `created_by`) VALUES (NULL, :title, :note, :deadline, :userid)");
    $sth->bindValue(':userid', ( $_SESSION["user"]));
    $sth->bindValue(':title', $data['title']);
    $sth->bindValue(':note', $data['note']);
    $sth->bindValue(':deadline', $data['deadline']);
    $sth->execute();

    $id=$db->lastInsertId();
    $sth = $db->prepare("INSERT INTO article_user VALUES(:id,:userid)");
    $sth->bindValue(':id', $id);
    $sth->bindValue(':userid', $_SESSION["user"]);
    $sth->execute();

    $sth = $db->prepare("INSERT INTO `pakage` (`id`, `article_id`, `name`) VALUES (NULL, :id, :name);");
    $sth->bindValue(':name', "DEFAULT");
    $sth->bindValue(':id', $id);
    $sth->execute();
    $pid=$db->lastInsertId();

    $app->response->setStatus(200);
    $app->response()->headers->set('Content-Type', 'application/json');
    echo json_encode(["id"=>$id,"pakage_id"=>$pid]);
});
$app->post('/article/pakage/:id', function ($id) {
    $app = \Slim\Slim::getInstance();
    $db = getDB();
    $json = $app->request->getBody();
    $data = json_decode($json, true);
    $sth = $db->prepare("INSERT INTO `pakage` (`id`, `article_id`, `name`) VALUES (NULL, :id, :name);");
    $sth->bindValue(':name', $data['name']);
    $sth->bindValue(':id', $id);
    $sth->execute();


    $app->response->setStatus(200);
    $app->response()->headers->set('Content-Type', 'application/json');
    echo json_encode(["id"=>$db->lastInsertId()]);
});
$app->get('/article/pakage/:id', function ($id) {
    $app = \Slim\Slim::getInstance();
    $db = getDB();
    $sth = $db->prepare("SELECT * FROM pakage WHERE article_id=article.id AND article_id=:id");

    $sth->bindValue(':id', $id);
    $sth->execute();
    $results = $sth->fetchAll(PDO::FETCH_ASSOC);

    $app->response->setStatus(200);
    $app->response()->headers->set('Content-Type', 'application/json');
    echo json_encode($results);
});
$app->post('/article/pakage/task/:id', function ($id) {
    $app = \Slim\Slim::getInstance();
    $db = getDB();
    $json = $app->request->getBody();
    $data = json_decode($json, true);
    $sth = $db->prepare("INSERT INTO `task` (`id`, `pakage_id`, `user_id`, `name`, `deadline`, `description`, `review`) VALUES (NULL, :id, :userid, :name, :deadline, :description, :review)");
    $sth->bindValue(':name', $data['name']);
    $sth->bindValue(':userid', $data['userid']);
    $sth->bindValue(':deadline', $data['deadline']);
    $sth->bindValue(':description', $data['description']);
    $sth->bindValue(':review', $data['review']);
    $sth->bindValue(':id', $id);
    $sth->execute();


    $app->response->setStatus(200);
    $app->response()->headers->set('Content-Type', 'application/json');
    echo json_encode(["id"=>$db->lastInsertId()]);
});

$app->post('/article/pakage/task/status/:id', function ($id) {
    $app = \Slim\Slim::getInstance();
    $db = getDB();
    $json = $app->request->getBody();
    $data = json_decode($json, true);
    $sth = $db->prepare("UPDATE task SET status=:status WHERE id=:id");
    $sth->bindValue(':status', $data['status']);
    $sth->bindValue(':id', $id);
    $sth->execute();
    $app->response->setStatus(200);
    $app->response()->headers->set('Content-Type', 'application/json');

});
$app->post('/article/pakage/task/status/:id', function ($id) {
    $app = \Slim\Slim::getInstance();
    $db = getDB();
    $json = $app->request->getBody();
    $data = json_decode($json, true);
    $sth = $db->prepare("UPDATE task SET status=:status WHERE id=:id");
    $sth->bindValue(':status', $data['status']);
    $sth->bindValue(':id', $id);
    $sth->execute();
    $app->response->setStatus(200);
    $app->response()->headers->set('Content-Type', 'application/json');

});
$app->post('/article/pakage/task/data/:id', function ($id) {
    $app = \Slim\Slim::getInstance();
    $db = getDB();
    $json = $app->request->getBody();
    $data = json_decode($json, true);
    $sth = $db->prepare("UPDATE task SET data=:data WHERE id=:id");
    $sth->bindValue(':data', $data['data']);
    $sth->bindValue(':id', $id);
    $sth->execute();
    $app->response->setStatus(200);
    $app->response()->headers->set('Content-Type', 'application/json');

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
    $app->response->setStatus(200);
    $app->response()->headers->set('Content-Type', 'application/json');
});
$app->post('/logout/', function () {
    $app = \Slim\Slim::getInstance();
   unset( $_SESSION["user"]);
    $app->response->setStatus(200);
    $app->response()->headers->set('Content-Type', 'application/json');

});




$app->run();
