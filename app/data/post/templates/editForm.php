<?php


if (isset($_POST["action"]) && $_POST["action"] === "update_post") {
  
  // check that I am the owner
  $sql = "SELECT * FROM posts WHERE id = :id";
  
  $stmt = App::$pdo->prepare($sql);
  
  $stmt->execute(
    [
      "id" => $_POST["post_id"]
    ]
  );
  
  $post = $stmt->fetch();
  
  if ($post["author_user_id"] !== $_SESSION["user_id"]) {
    die("You are not the owner of this post.");
  }
  
  $sql = "UPDATE posts SET title = :title, content = :content, image = :image, weblink = :weblink, category = :category, topic = :topic, searchTags = :searchTags WHERE id = :id";
  
  $stmt = App::$pdo->prepare($sql);
  
  $stmt->execute(
    [
      "id"         => $_POST["post_id"],
      "title"      => $_POST["title"],
      "content"    => $_POST["content"],
      "image"      => $_POST["image"],
      "weblink"    => $_POST["weblink"],
      "category"   => $_POST["category"],
      "topic"      => $_POST["topic"],
      "searchTags" => $_POST["searchTags"]
    ]
  );
}

?>