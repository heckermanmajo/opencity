<?php

include "lib.php";
App::init();

$class = $_GET["class"] ?? throw new Exception("No class given");

if (!in_array($class, ["User", "Post", "Comment", "Profile"])) {
  die("Invalid class");
}

$id = $_GET["id"] ?? throw new Exception("No id given");


###############################################
#                     USER
###############################################
if ($class === "User") {
  $stmt = App::$pdo->prepare("SELECT * FROM users WHERE id = :id");
  // check that user is the owner
  
  $stmt->execute(
    [
      ":id" => (int)$id
    ]
  );
  
  $user = $stmt->fetch();
  
  if (!$user) {
    die("User not found");
  }
  
  if ((int)$_SESSION["user_id"] !== (int)$user["id"]) {
    die("You are not the owner of this user.");
  }
 
  ####################################
  #               POST
  ####################################
} elseif ($class === "Post") {
  $stmt = App::$pdo->prepare("SELECT * FROM posts WHERE id = :id");
  
  $stmt->execute(
    [
      ":id" => (int)$id
    ]
  );
  
  $post = $stmt->fetch();
  
  if (!$post) {
    die("Post not found");
  }
  
  if ($_SESSION["user_id"] !== $post["author_user_id"]) {
    die("You are not the owner of this post.");
  }
  

  ####################################
  #               COMMENT
  ####################################
} elseif ($class === "Comment") {
  
  $stmt = App::$pdo->prepare("SELECT * FROM comments WHERE id = :id");
  
  $stmt->execute(
    [
      ":id" => (int)$id
    ]
  );
  
  $comment = $stmt->fetch();
  
  if (!$comment) {
    die("Comment not found");
  }
  
  if ($_SESSION["user_id"] !== $comment["author_user_id"]) {
    die("You are not the owner of this comment.");
  }
}

// POST

if (isset($_POST["action"])) {
  
  ###############################################
  # EDIT USER
  ###############################################
  if ($class == "User") {
    // edit user data
    
    // check that given email is not already in use
    
    $stmt = App::$pdo->prepare("SELECT * FROM users WHERE email = :email");
    
    $stmt->execute(
      [
        ":email" => $_POST["email"]
      ]
    );
    
    $user = $stmt->fetch();
    
    if ($user && $user["id"] !== $_SESSION["user_id"]) {
      die("Email already in use.");
      # todo: dont die, but show error message
    }
    
    $stmt = App::$pdo->prepare("UPDATE users SET email = :email WHERE id = :id");
    
    $stmt->execute(
      [
        ":email" => $_POST["email"],
        ":id"    => $id
      ]
    );
    
    $stmt = App::$pdo->prepare("SELECT * FROM users WHERE id = :id");
    
    $stmt->execute(
      [
        ":id"   => $id
      ]
    );
    
    $_SESSION["user"] = $stmt->fetch();
    
    // stay on edit page
    
    ###############################################
    # EDIT POST
    ###############################################
  } elseif ($class == "Post") {
    // edit post data
    
    /*
     
    App::addColumn("posts", "author_user_id", "INTEGER");
    App::addColumn("posts", "title", "TEXT");
    App::addColumn("posts", "content", "TEXT");
    App::addColumn("posts", "created_at", "TEXT");
    App::addColumn("posts", "image", "TEXT");
    App::addColumn("posts", "weblink", "TEXT");
    App::addColumn("posts", "category", "TEXT");
    App::addColumn("posts", "topic", "TEXT");
    App::addColumn("posts", "searchTags", "TEXT");
     
     */
    
    if($_POST["image"] !== null) {
      // upload the new image
      $stmt = App::$pdo->prepare(
        "
      UPDATE posts
        SET
          title = :title,
          content = :content,
          image = :image,
          weblink = :weblink,
          category = :category,
          topic = :topic,
          searchTags = :searchTags
        WHERE
          id = :id
          ");
  
      $stmt->execute(
        [
          ":title"   => $_POST["title"],
          ":content" => $_POST["content"],
          ":id"      => $id,
          ":image"   => $_POST["image"],
          ":weblink" => $_POST["weblink"],
          ":category" => $_POST["category"],
          ":topic" => $_POST["topic"],
          ":searchTags" => $_POST["searchTags"]
        ]
      );
    }else{
      $stmt = App::$pdo->prepare(
        "
      UPDATE posts
        SET
          title = :title,
          content = :content,
          weblink = :weblink,
          category = :category,
          topic = :topic,
          searchTags = :searchTags
        WHERE
          id = :id
          ");
  
      $stmt->execute(
        [
          ":title"   => $_POST["title"],
          ":content" => $_POST["content"],
          ":id"      => $id,
          ":weblink" => $_POST["weblink"],
          ":category" => $_POST["category"],
          ":topic" => $_POST["topic"],
          ":searchTags" => $_POST["searchTags"]
        ]
      );
    }
    
    $stmt = App::$pdo->prepare("SELECT * FROM posts WHERE id = :id");
    
    $stmt->execute(
      [
        ":id" => (int)$id
      ]
    );
    
    $post = $stmt->fetch();
    
    // stay on edit page
    
    ###############################################
    # EDIT COMMENT
    ###############################################
  } elseif ($class == "Comment") {
    
    $stmt = App::$pdo->prepare("UPDATE comments SET content = :content WHERE id = :id");
    
    $stmt->execute(
      [
        ":content" => $_POST["content"],
        ":id"      => $id
      ]
    );
    
    $stmt = App::$pdo->prepare("SELECT * FROM comments WHERE id = :id");
    
    $stmt->execute(
      [
        ":id" => (int)$id
      ]
    );
    
    $comment = $stmt->fetch();
    
    // stay on edit page
  }
  
}

// Display The edit page html

if ($class == "User"){
  /** @var array $user */
?>
  <pre class="todo">
    # todo: show error message if something went wrong
    # todo: add labels
  </pre>
  <form method="post">
    <input type="hidden" name="action" value="edit">
    <label>
      <span>Email</span>
      <br>
      <input type="email" name="email" value="<?php echo $user["email"] ?>">
    </label>
    <br>
    <input type="submit" value="Save">
  </form>
  <?php
}

if ($class == "Post"){
  /** @var array $post */
?>
  <pre class="todo">
    # todo: show error message if something went wrong
    # todo: add labels
  </pre>
  <form method="post">
    <input type="hidden" name="action" value="edit">
    <input type="text" name="title" value="<?php echo $post["title"] ?>">
    <textarea name="content"><?php echo $post["content"] ?></textarea>
    <input type="text" name="image" value="<?php echo $post["image"] ?>">
    <input type="text" name="weblink" value="<?php echo $post["weblink"] ?>">
    <input type="text" name="category" value="<?php echo $post["category"] ?>">
    <input type="text" name="topic" value="<?php echo $post["topic"] ?>">
    <input type="text" name="searchTags" value="<?php echo $post["searchTags"] ?>">
    <input type="submit" value="Save">
  </form>
  <?php
}

if ($class == "Comment"){
  /** @var array $comment */
  ?>
  <pre class="todo">
    # todo: show error message if something went wrong
    # todo: add labels
  </pre>
  <form method="post">
    <input type="hidden" name="action" value="edit">
    <textarea name="content"><?php echo $comment["content"] ?></textarea>
    <input type="submit" value="Save">
  </form>
  <?php
}