<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Psr\Log\LoggerInterface;

return function (App $app) {

    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('API v0.0.1');
        return $response;
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });

    $app->post('/api/v1/validate-employee', function (Request $request, Response $response, array $args) {
        $data = $request->getParsedBody();
        $employeeId = intval($data["employeeId"]);
        $lastName = strtoupper(trim($data["lastName"]));
        $firstName = strtoupper(trim($data["firstName"]));

        $name = $lastName . " " . $firstName;

        $logger = $this->get(LoggerInterface::class);

        $sql = "SELECT * 
        FROM employee 
        WHERE employee_id = :employee_id and name LIKE CONCAT('%', :name, '%')";
        $db = $this->get(PDO::class);
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':employee_id', $employeeId, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        try {
            $stmt->execute();
            $employees = $stmt->fetchAll();
            $result = new stdClass();
            $result->data = $employees;
            $logger->notice($stmt->debugDumpParams());
            $db = null;
            $payload = json_encode($result);
            $logger->notice($payload);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        } catch (PDOException $e) {
            $logger->warning($stmt->debugDumpParams());
            $error = array(
                "message" => $e->getMessage(),
                "debug" => $stmt->debugDumpParams(),
                "trace" => $e->getTraceAsString()
            );

            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(500);
        }
    });

    $app->get('/db-test', function (Request $request, Response $response) {
        try {
            $db = $this->get(PDO::class);
            $sth = $db->prepare("SELECT * FROM employee ORDER BY id");
            $sth->execute();
            $data = $sth->fetchAll(PDO::FETCH_ASSOC);
            $payload = json_encode($data);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        } catch (PDOException $e) {
            $error = array(
                "message" => $e->getMessage(),
                "trace" => $e->getTraceAsString()
            );

            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(500);
        }
    });
};
