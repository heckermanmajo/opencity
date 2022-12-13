<?php
include "lib.php";
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <title>Debug</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>
<?php

if (isset($_POST["action"])) {
  
  ###############################################
  # DELETE POST
  ###############################################
  if ($_POST["action"] === "delete_post") {
    
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
    
    $sql = "DELETE FROM posts WHERE id = :id";
    
    $stmt = App::$pdo->prepare($sql);
    
    $stmt->execute(
      [
        "id" => $_POST["post_id"]
      ]
    );
    
    ###############################################
    # CREATE COMMENT
    ###############################################
  } elseif ($_POST["action"] === "create_comment") {
    
    $sql = "INSERT INTO comments (author_user_id, post_id, content, created_at) VALUES (:author_user_id, :post_id, :content, :created_at)";
    
    $stmt = App::$pdo->prepare($sql);
    
    $stmt->execute(
      [
        "author_user_id" => $_SESSION["user_id"],
        "post_id"        => $_POST["post_id"],
        "content"        => $_POST["content"],
        "created_at"     => date("Y-m-d H:i:s")
      ]
    );
    
    ###############################################
    # DELETE COMMENT
    ###############################################
  } elseif ($_POST["action"] === "delete_comment") {
    
    // check that I am the owner
    $sql = "SELECT * FROM comments WHERE id = :id";
    
    $stmt = App::$pdo->prepare($sql);
    
    $stmt->execute(
      [
        "id" => $_POST["comment_id"]
      ]
    );
    
    $comment = $stmt->fetch();
    
    if ($comment["author_user_id"] !== $_SESSION["user_id"]) {
      die("You are not the owner of this comment.");
    }
    
    $sql = "DELETE FROM comments WHERE id = :id";
    
    $stmt = App::$pdo->prepare($sql);
    
    $stmt->execute(
      [
        "id" => $_POST["comment_id"]
      ]
    );
    
  }
}

if (!isset($_GET["id"])) {
  echo "No id given";
  exit;
}

$id = $_GET['id'];

$stmt = App::$pdo->prepare("SELECT * FROM posts WHERE id = :id");

$stmt->execute(
  [
    ":id" => $id
  ]
);

$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
  echo "Post not found";
  exit;
}

$stmt = App::$pdo->prepare("SELECT * FROM comments WHERE post_id = :post_id");

$stmt->execute(
  [
    ":post_id" => $id
  ]
);

$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);


# print post
echo "<div class='w3-container w3-card w3-white w3-round w3-margin'><br>";
echo "<img src='https://www.w3schools.com/w3images/avatar2.png' alt='Avatar' class='w3-left w3-circle w3-margin-right' style='width:60px'>";
echo "<span class='w3-right w3-opacity'>" . $post["created_at"] . "</span>";
echo "<h4>" . $post["title"] . "</h4><br>";
echo "<hr class='w3-clear'>";
echo "<p>" . $post["content"] . "</p>";
echo "<div class='w3-row-padding' style='margin:0 -16px'>";
echo "</div>";

if ($post["author_user_id"] === $_SESSION["user_id"]) {

}

echo "</div>";

# create comment

?>

<?php if (isset($_SESSION["user_id"])): ?>
  <form method="post">
    <input type="hidden" name="action" value="create_comment">
    <input type="hidden" name="post_id" value="<?php echo $post["id"] ?>">
    <label>
      Comment:
      <textarea name="content"></textarea>
    </label>
    <input type="submit" value="Create Comment">
  </form>
<?php endif ?>

<?php

# print comments

foreach ( $comments as $comment ) {
  echo "<div class='w3-container w3-card w3-white w3-round w3-margin w3-padding'><br>";
  echo "<img src='https://www.w3schools.com/w3images/avatar2.png' alt='Avatar' class='w3-left w3-circle w3-margin-right' style='width:60px'>";
  echo "<span class='w3-right w3-opacity'>" . $comment["created_at"] . "</span>";
  echo "<h4>" . $comment["content"] . "</h4><br>";
  if($comment["author_user_id"] === $_SESSION["user_id"]) {
    echo "<form method='post'>";
    echo "<input type='hidden' name='action' value='delete_comment'>";
    echo "<input type='hidden' name='comment_id' value='" . $comment["id"] . "'>";
    echo "<input type='submit' value='Delete Comment'>";
    echo "</form>";
    echo "<a href='edit.php?class=Comment&id=" . $comment["id"] . "' class='w3-button w3-blue'> Diesen Kommentar Bearbeiten </a>";
  }
  echo "</div>";
}

?>
</body>
</html>
  