<?php

require_once("../includes/entry.class.php");

$entry = new Entry();

// action = edit
if(isset($_POST['action']) && $_POST["action"] == "edit" && isset($_POST['id'])) {
    $id = $_POST["id"];

    $result = $entry->get_entry($id);
    $output = array();

    if($result) {
        $row = $result->fetch_assoc();

        $output["name"] = $row["name"];
        $output["url"] = $row["url"];
        $output["text"] = $row["text"];

        echo json_encode($output);
    }
}

// action = add
if(isset($_POST["action"]) && $_POST["action"] == "add") {
    $name = trim($_POST["name"]);
    $url = trim($_POST["url"]);
    $text = trim($_POST["text"]);

    if($entry->add_entry($name, $url, $text)) {
        echo "success";
    } else {
        echo "error";
    }
}

// action = delete
if(isset($_POST["action"]) && $_POST["action"] == "delete" && isset($_POST["id"])) {
    $id = (int) $_POST["id"] ? (int) $_POST["id"] : false;

    if($id) {
        if($entry->delete_entry($id)) {
            echo "success";
        } else {
            echo "error";
        }
    } else {
        echo "error";
    }
}

// action = update
if(isset($_POST["action"]) && $_POST["action"] == "update" && isset($_POST["id"])) {
    $id = (int) $_POST["id"] ? (int) $_POST["id"] : false;

    $name = $_POST["name"];
    $url = $_POST["url"];
    $text = $_POST["text"];

    if($id) {
        if($entry->update_entry($id, $name, $url, $text)) {
            echo "success";
        } else {
            echo "error";
        }
    } else {
        echo "error";
    }
}

// action = search
if(isset($_GET["action"]) && $_GET["action"] == "search") {
    $search = $_GET["text"];

    $entry->search($search);
}

// action = auth
if(isset($_POST["action"]) && $_POST["action"] == "auth") {
    $user_login = $_POST["login"];
    $user_pass = $_POST["password"];

    echo $entry->login($user_login, $user_pass);
}

if(isset($_GET["action"]) && $_GET["action"] == "logout") {
    session_start();
    session_destroy();
}
