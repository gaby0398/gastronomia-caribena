<?php
namespace App\controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use PDO;
//require __DIR__ . '/../controllers/cliente.php';

class categories{
    protected $container;
    public function __construct(ContainerInterface $c){
        $this->container = $c;

    }

    function create  (Request $request, Response $response, $args){

        $body = json_decode($request->getBody(), 1);
        
        $sql = "INSERT INTO categories (";
        $values = "VALUES (";
        foreach ($body as $key => $value){
            $sql .= $key . ', ';
            $values .= ":$key, ";
        }
       // $sql = rtrim($sql)
        $sql = rtrim($sql, ', ') . ') ' .rtrim($values, ', ')  . ')';

        $data = [];
        foreach ($body as $key => $value){
            $data[$key] = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
        }

        $con = $this->container->get('bd');
        $con->beginTransaction();

        try {
            $query = $con->prepare($sql);
            $query->execute($data);
            $con->commit();
            $status = 201;

        } catch (\PDOException $e){
            $con->rollback();
            echo ("Error en la consulta: " . $e->getMessage());
            $status = $e->getCode() == 23000 ? 409 : 500; //500: error de servidor
        }
 
        $query = null;
        $con = null;
        return $response->withStatus($status);

    } 

    function read  (Request $request, Response $response, $args){
        $sql = "SELECT * FROM categories";
        if(isset($args['id'])){
            $sql .= " WHERE id = :id";
        }

        $con = $this->container->get('bd');
        $query = $con->prepare($sql);
        if (isset($args['id'])){
            $query->execute(['id' => $args['id']]);
        } else {
            $query->execute();
        }

        $res = $query->fetchAll();

        $status = $query->rowCount() > 0 ? 200 : 204;
        $query = null;
        $con = null;

        $response->getBody()->write(json_encode($res));

        return $response
            ->withHeader('Content-type','Application/json')
            ->withStatus($status);
        
    } 

    function update  (Request $request, Response $response, $args){
       
        $body = json_decode($request->getBody());

        if(isset($body->idCliente)){
            unset($body->idCliente);
        }

        $sql = "UPDATE categories SET ";
        foreach ($body as $key => $value){
            $sql .= "$key = :$key, ";   
        }
        $sql = substr($sql, 0, -2);
        $sql .= " WHERE id = :id;";

        $con = $this->container->get('bd');
        $query = $con->prepare($sql);

        foreach ($body as $key => $value){
            $query->bindValue(":$key", $value, PDO::PARAM_STR);  
        }
        $query->bindValue(':id', $args['id'], PDO::PARAM_INT); 

        $query->execute();

        $status = $query->rowCount() > 0 ? 200 : 204;  
        $query = null;
        $con = null;

        return $response->withStatus($status);
        
    } 

    function delete  (Request $request, Response $response, $args){

        $sql= "DELETE FROM categories WHERE id = :id";

        $con = $this->container->get('bd');

        $query = $con->prepare($sql);
        $query->bindValue(':id', $args['id'], PDO::PARAM_INT); 
        $query->execute();

        $status = $query->rowCount() > 0 ? 200 : 204; #204 no hubo ningun error
        $query = null;
        $con = null;

        return $response->withStatus($status);
        
    } 

    function filtrar  (Request $request, Response $response, $args){
        $datos = $request->getQueryParams();
        
        $sql = "SELECT * FROM categories WHERE ";
        foreach($datos as $key => $value){
            $sql .= "$key LIKE :$key AND ";
        }

        $sql = rtrim($sql, 'AND ') . ';';

        $con = $this->container->get('bd');
        $query = $con->prepare($sql);

        foreach($datos as $key => $value){
            $query->bindValue(":$key", "%$value%");
        }

        $query->execute();

        $res = $query->fetchAll();
        $status = $query->rowCount() > 0 ? 200 : 204;
        $query = null;
        $con = null;

        $response->getBody()->write(json_encode($res));

        return $response
            ->withHeader('Content-type','Application/json')
            ->withStatus($status);
    }
}