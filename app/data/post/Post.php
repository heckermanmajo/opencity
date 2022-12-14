<?php

namespace data\post;

use App;

class Post {
  
  public static function createAndUpdateTable(): void {
    App::createTable("posts");
    App::addColumn("posts", "author_user_id", "INTEGER DEFAULT 0");
    App::addColumn("posts", "title", "TEXT DEFAULT ''");
    App::addColumn("posts", "content", "TEXT DEFAULT ''");
    App::addColumn("posts", "created_at", "TEXT DEFAULT ''");
    App::addColumn("posts", "image", "TEXT DEFAULT ''");
    App::addColumn("posts", "weblink", "TEXT DEFAULT ''");
    App::addColumn("posts", "category", "TEXT DEFAULT ''");
    App::addColumn("posts", "topic", "TEXT DEFAULT ''");
    App::addColumn("posts", "searchTags", "TEXT DEFAULT ''");
  }
  
  public int $id = -1;
  public int $author_user_id = -1;
  public string $title = "";
  public string $content = "";
  public string $created_at = "";
  public string $image = "";
  public string $weblink = "";
  public string $category = "";
  public string $topic = "";
  public string $searchTags = "";
  
  public static function getCreateFormHTMLAndHandlePossibleCreation(): string {
    ob_start();
    include "templates/createForm.php";
    return ob_get_clean();
  }
  
  public function getEditFormHTMLAndHandlePossibleEditing(): string {
    ob_start();
    include "templates/editForm.php";
    return ob_get_clean();
  }
  
  public function getPostById(): Post {}
  
}