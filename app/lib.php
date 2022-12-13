<?php

declare(strict_types=1);
const __debug__ = true;

// display errors
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

# throw all errors
error_reporting(E_ALL);
ini_set('display_errors', '1');
# set timezone
date_default_timezone_set('Europe/Berlin');
# throw error on warning
set_error_handler(function (
  $errno,
  $errstr,
  $errfile,
  $errline
) {
  if (App::$logFile !== null){
    App::logInfo("Error: $errstr in $errfile on line $errline");
  }else{
    echo "GOT ERROR, BUT CANT LOG; LOGFILE IS NULL";
  }
  throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});


// autoloader by namespace
spl_autoload_register(function ($class) {
  $class = str_replace("\\", "/", $class);
  $file = __DIR__ . "/$class.php";
  if (file_exists($file)) {
    include $file;
  } else {
    throw new Exception("File $file not found");
  }
});


session_start();

class App {
  
  /**
   * @var Resource
   */
  static        $logFile;
  static string $logFilePath;
  
  static function logAction(string $actionName) {
    if (__debug__) {
      fwrite(self::$logFile, $actionName . PHP_EOL);
    }
  }
  
  static function logInfo(string $info) {
    if (__debug__) {
      fwrite(self::$logFile, $info . PHP_EOL);
    }
  }
  
  static PDO   $pdo;
  static array $user_data = [];
  
  static function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
  }
  
  static function addColumn(
    $table,
    $field,
    $type
  ): void {
    $sql = "ALTER TABLE `$table` ADD COLUMN `$field` $type";
    try {
      $stmt = App::$pdo->prepare(
        $sql
      );
      $stmt->execute();
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
      echo $sql;
      echo $e->getMessage();
      echo "Column username already exists";
    }
  }
  
  static function createTable($tableName): void {
    $stmt = App::$pdo->prepare(
      "CREATE TABLE IF NOT EXISTS `$tableName` (
        id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL
      );
    ");
    $stmt->execute();
  }
  
  static function createUserDataBase(): void {
    \data\userdata\UserData::createTable();
  }
  
  static function createPostDataBase(): void {
    App::createTable("posts");
    App::addColumn("posts", "author_user_id", "INTEGER");
    App::addColumn("posts", "title", "TEXT");
    App::addColumn("posts", "content", "TEXT");
    App::addColumn("posts", "created_at", "TEXT");
    App::addColumn("posts", "image", "TEXT");
    App::addColumn("posts", "weblink", "TEXT");
    App::addColumn("posts", "category", "TEXT");
    App::addColumn("posts", "topic", "TEXT");
    App::addColumn("posts", "searchTags", "TEXT");
  }
  
  static function createCommentDataBase(): void {
    App::createTable("comments");
    App::addColumn("comments", "author_user_id", "INTEGER");
    App::addColumn("comments", "post_id", "INTEGER");
    App::addColumn("comments", "content", "TEXT");
    App::addColumn("comments", "created_at", "TEXT");
  }
  
  static function createSupportPostDataBase(): void {
    App::createTable("post_support");
    App::addColumn("post_support", "user_id", "INTEGER");
    App::addColumn("post_support", "post_id", "INTEGER");
  }
  
  /**
   * @param $tableName
   * @return void
   */
  static function truncateTable($tableName): void {
    $stmt = App::$pdo->prepare(
      "DELETE FROM `$tableName`"
    );
    $stmt->execute();
  }
  
  /**
   * This function is called is a user is created.
   * This function is also used for testing.
   * @param string $username
   * @param string $email
   * @param string $password
   * @return void
   * @throws Exception
   */
  static function insertUser(
    string $username,
    string $email,
    string $password
  ): void {
    $sql = "INSERT INTO users (username, email, password, verification_code) VALUES (:username, :email, :password, :verification_code)";
    
    $stmt = App::$pdo->prepare($sql);
    
    $stmt->execute(
      [
        "username"          => $username,
        "email"             => $email,
        "password"          => password_hash($password, PASSWORD_DEFAULT),
        "verification_code" => bin2hex(random_bytes(16))
      ]
    );
  }
  
  /**
   * @throws Exception
   */
  static function pageTop(): string {
    $links = "";
    
    if (App::isLoggedIn()) {
      $userid = $_SESSION['user_id'] ?? throw new Exception("User is not logged in");
      $links = "<a href='edit.php?class=User&id=$userid'>Dein Profil</a>";
    }
    
    # replace " with '
    $head = <<<____EOT
      <!DOCTYPE html>
      <html lang="en">
      <head>
        <meta charset="UTF-8">
        <title>Debug</title>
        <meta charset="utf - 8">
        <meta name="viewport" content="width = device - width, initial - scale = 1">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js" integrity="sha512-aVKKRRi/Q/YV+4mjoKBsE4x3H+BkegoM/em46NNlCqNTmUYADjBbeNefNxYV7giUp0VxICtqdrbqU7iVaeZNXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script>
          function ajaxPost(url, data, callback) {
            $.ajax({
              url: url,
              type: "POST",
              data: data,
              success: callback
            });
          }
        </script>
      </head>
      <body>
      <header>
        <nav>
          <a href="index.php">Home</a>
           $links
          <a href="debug.php">Debug</a>
        </nav>
      </header>

____EOT;
    
    return $head;
  }
  
  static function pageBottom() {
    $foot = <<<____EOT
      </body>
      </html>
____EOT;
  }
  
}


if (__debug__) {
  echo "DEBUG MODE<br>";
  App::$logFilePath = __DIR__ . "/../" . basename(__FILE__, '.php') . "_log.txt";
  #echo "Logfile: " . App::$logFilePath . PHP_EOL;
  
  if (file_exists(App::$logFilePath)) {
    unlink(App::$logFilePath);
  }
  
  #echo App::$logFilePath;
  App::$logFile = fopen(App::$logFilePath, "a+");
  if (!App::$logFile) {
    echo "Error: Could not open log file: ". App::$logFilePath;
    exit();
  }
}

# connect to sqlite db
try {
  App::$pdo = new PDO('sqlite:../db.sqlite');
  App::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  App::logInfo("Connected to database");
} catch (PDOException $e) {
  echo "Error connecting to database: " . $e->getMessage();
  exit;
}



