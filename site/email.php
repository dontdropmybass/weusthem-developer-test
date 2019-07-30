<?php
require 'db.php';

$method = $_SERVER['REQUEST_METHOD'];
$request = explode("/", substr(@$_SERVER['PATH_INFO'], 1));

switch ($method) {
    case 'PUT':
        if (strpos(file_get_contents("php://input"), "id") !== false) {
            $matches = [];
            preg_match("/(\\r\\n|id=)(\d+)(\\r\\n)?/", file_get_contents("php://input"), $matches);
            archive_email($matches[2]);
        }
        else {
            http_response_code(400);
            echo json_encode([
                "success" => 0,
                "message" => "ID must be sent to set the archive field on an email"
            ]);
            return;
        }
        break;
    case 'POST':
        new_email($_POST);
        header("Location: /inbox.php?email=".$_POST['from']);
        die();
        break;
    case 'GET':
        get_emails($_GET);
        break;
    case 'DELETE':
        if (strpos(file_get_contents("php://input"), "id") !== false) {
            preg_match("/(\\r\\n|id=)(\d+)(\\r\\n)?/", file_get_contents("php://input"), $matches);
            delete_email($matches[2]);
        }
        else {
            http_response_code(400);
            echo json_encode([
                "success" => 0,
                "message" => "ID must be sent to delete an email"
            ]);
            return;
        }
        break;
    default:
        // handle_error($request);
        break;
}

function archive_email($id) {
    // if (!isset($id)) {
    //     http_response_code(400);
    //     echo json_encode([
    //         "success" => 0,
    //         "message" => "ID is missing or invalid"
    //     ]);
    //     return;
    // }

    global $conn;
    $sql = "SELECT * FROM emails WHERE `id` = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute(["id" => $id]);
    $email = $stmt->fetch();
    // echo $email['archived'];
    $sql = "UPDATE emails SET `archived` = :archived WHERE `id` = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        "archived" => $email['archived'] == 0 ? 1 : 0,
        "id" => $id
    ]);
    http_response_code(200);
    echo json_encode([
        "success" => "1",
        "archived" => $email['archived'] == 0 ? "1" : "0"
    ]);
    return;
}

function new_email($request) {
    // echo json_encode($request); die;
    if (empty($request['to']) || empty($request['from']) || empty($request['subject']) || empty($request['body'])) {
        http_response_code(400);
        echo json_encode([
            "success" => 0,
            "message" => "All fields are required to be filled",
            "fields" => [
                'to' => empty($request['to']),
                'from' => empty($request['from']),
                'subject' => empty($request['subject']),
                'body' => empty($request['body'])
            ]
        ]);
        return;
    }

    global $conn;
    $sql = "INSERT INTO emails (`to`, `from`, `subject`, `body`, `created_at`) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        $request['to'],
        $request['from'],
        $request['subject'],
        $request['body'],
        date("Y-m-d H:i:s")
    ]);
    // $conn->commit();
    $id = $conn->lastInsertId();
    $sql = "SELECT * FROM emails WHERE `id` = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute(["id" => $id]);
    $email = $stmt->fetch();
    http_response_code(200);
    echo json_encode([
        "success" => 1,
        "email" => $email
    ]);
}

function get_emails($request) {
    global $conn;
    $sql = "SELECT * FROM emails WHERE `archived` = :archived";
    $variables = ["archived" => !empty($request['archived'])];
    if (isset($request['id'])) {
        $sql .= " AND `id` = :id";
        $variables["id"] = $request['id'];
    }
    if (isset($request['from'])) {
        $sql .= " AND `from` = :from";
        $variables["from"] = $request['from'];
    }
    if (isset($request['to'])) {
        $sql .= " AND `to` = :to";
        $variables["to"] = $request['to'];
    }
    if (isset($request['subject'])) {
        $sql .= " AND `subject` = :subject";
        $variables["subject"] = $request['subject'];
    }
    $stmt = $conn->prepare($sql);
    // echo json_encode([$sql, $variables]);
    $stmt->execute($variables);
    $emails = $stmt->fetchAll();
    echo json_encode([
        "success" => 1,
        "emails" => $emails
    ]);
}

function delete_email($id) {
    global $conn;
    $sql = "DELETE FROM emails WHERE `id` = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute(["id" => $id]);
    $deleted = $stmt->rowCount();
    http_response_code(200);
    echo json_encode([
        "success" => 1,
        "deleted" => $deleted > 0
    ]);
}

function handle_error($request) {

}

/*
CREATE TABLE `emails` (
	`id` INT unsigned NOT NULL AUTO_INCREMENT,
	`subject` VARCHAR(255) DEFAULT '',
	`body` TEXT,
	`created_at` DATE NOT NULL,
	`archived` BOOLEAN NOT NULL DEFAULT '0',
	`from` VARCHAR(320) NOT NULL,
	`to` VARCHAR(320) NOT NULL,
	PRIMARY KEY (`id`)
);
*/
