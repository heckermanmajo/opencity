<?php

// this file handles all ajax requests

// include the app
include "lib.php";
App::init();

// get the action
$action = $_GET["action"] ?? throw new Exception("No action given");
// check if the action is valid

switch ($action) {
  case "post_support":
    
    $postId = $_POST["postId"] ?? throw new Exception("No postId given");
    
    $stmt = App::$pdo->prepare("SELECT * FROM post_support WHERE post_id = :post_id AND user_id = :user_id");
    
    $stmt->execute(
      [
        ":post_id" => $postId,
        ":user_id" => $_SESSION["user_id"]
      ]
    );
    
    $support = $stmt->fetch();
    
    if ($support) {
      // delete support
      $stmt = App::$pdo->prepare("DELETE FROM post_support WHERE post_id = :post_id AND user_id = :user_id");
      
      $stmt->execute(
        [
          ":post_id" => $postId,
          ":user_id" => $_SESSION["user_id"]
        ]
      );
      
      echo json_encode(
        [
          "success" => true,
          "message" => "Support removed",
          "support_change" => - 1
        ]
      );
      
    } else {
      // add support
      $stmt = App::$pdo->prepare("INSERT INTO post_support (post_id, user_id) VALUES (:post_id, :user_id)");
      
      $stmt->execute(
        [
          ":post_id" => $postId,
          ":user_id" => $_SESSION["user_id"]
        ]
      );
      
      echo json_encode(
        [
          "success" => true,
          "message" => "Support added",
          "support_change" => 1
        ]
      );
    }
    
    break;
  
  default:
    throw new Exception("Invalid action");
}
