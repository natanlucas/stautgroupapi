<?php
ob_start();

require __DIR__ . "/vendor/autoload.php";

use CoffeeCode\Router\Router;

/**
 * API ROUTES
 * index
 */
$route = new Router(url(), ":");
$route->namespace("Source\App\StautApi");

//Users
$route->group("/users");
$route->post("/", "Users:create");
$route->get("/{user_id}", "Users:read");
$route->get("/", "Users:readList");
$route->put("/{user_id}", "Users:update");
$route->delete("/{user_id}", "Users:delete");
$route->post("/{user_id}/drink", "Drinks:drink");

//Login
$route->group("/login");
$route->post("/", "Users:login");

/**
 * ROUTE
 */
$route->dispatch();

/**
 * ERROR REDIRECT
 */
if ($route->error()) {
    header('Content-Type: application/json; charset=UTF-8');
    http_response_code(404);

    echo json_encode([
        "errors" => [
            "type " => "endpoint_not_found",
            "message" => "Não foi possível processar a requisição"
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

ob_end_flush();