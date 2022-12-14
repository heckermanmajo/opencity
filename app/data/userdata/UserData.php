<?php

declare(strict_types=1);

namespace data\userdata;

use App;
use data\failure\CorrectnessProblem;
use data\failure\Failure;
use Exception;
use PDO;

# todo: format sql strings
# todo: creates tests and test data
class UserData {
  
  public static array $test_user_data;
  
  public int $id = -1;
  public string $username = "";
  public string $email = "";
  public string $password = "";
  public int $verified = 0;
  public string $verification_code = "";
  
  public static function loginFormHtml(string $login_error): string {
    ob_start();
    include "templates/login.php";
    return ob_get_clean();
  }
  
  public static function registerFormHtml(string $reg_error): string {
    ob_start();
    include "templates/register.php";
    return ob_get_clean();
  }
  
  public static function createTable(): void {
    App::createTable("users");
    App::addColumn("users", "username", "TEXT DEFAULT ''");
    App::addColumn("users", "email", "TEXT DEFAULT ''");
    App::addColumn("users", "password", "TEXT DEFAULT ''");
    App::addColumn("users", "verified", "INTEGER DEFAULT 0");
    App::addColumn("users", "verification_code", "TEXT DEFAULT ''");
  }
  
  /**
   * @throws Failure
   */
  public function checkCorrectnessWhereCorrectnessIsExpected(
    string $function_name
  ): void {
    $errors = $this->checkUserDataCorrectness();
    if (count($errors) > 0) {
      throw new Failure(
        userDisplayError: "Failed to update user because of internal error by bad coding.",
        message:          "Bad Userdata reached $function_name .<br>
                  This is a coding erorr. <br>
                  Failed to insert user: " . implode(", ", $errors),
      );
    }
  }
  
  /**
   * @throws Failure
   */
  public static function getUserById(int $id): ?UserData {
    $val = App::queryForOne(
      sql:                    "SELECT * FROM users WHERE id = :id",
      classNameWithNamespace: UserData::class,
      params:                 ["id" => $id]
    );
    assert($val instanceof UserData || $val === null);
    return $val;
  }
  
  /**
   * @throws Failure
   */
  public static function getUserByUsername(string $username): ?UserData {
    $val = App::queryForOne(
      sql:                    "SELECT * FROM users WHERE username = :username LIMIT 1",
      classNameWithNamespace: UserData::class,
      params:                 ["username" => $username]
    );
    assert($val instanceof UserData || $val === null);
    return $val;
  }
  
  /**
   * @throws Failure
   */
  public static function getUserByEmail(string $email): ?UserData {
    $val = App::queryForOne(
      sql:                    "SELECT * FROM users WHERE email = :email LIMIT 1",
      classNameWithNamespace: UserData::class,
      params:                 ["email" => $email]
    );
    assert($val instanceof UserData || $val === null);
    return $val;
  }
  
  /**
   * @throws Failure
   */
  public static function getUserByVerificationCode(string $verification_code): ?UserData {
    $val = App::queryForOne(
      sql:                    "SELECT * FROM users WHERE verification_code = :verification_code LIMIT 1",
      classNameWithNamespace: UserData::class,
      params:                 ["verification_code" => $verification_code]
    );
    assert($val instanceof UserData || $val === null);
    return $val;
  }
  
  /**
   * @throws Failure
   */
  public static function getUserByUsernameOrEmail(string $usernameOrEmail): ?UserData {
    $val = App::queryForOne(
      sql:                    "SELECT * FROM users WHERE username = :usernameOrEmail OR email = :usernameOrEmail LIMIT 1",
      classNameWithNamespace: UserData::class,
      params:                 ["usernameOrEmail" => $usernameOrEmail]
    );
    assert($val instanceof UserData || $val === null);
    return $val;
  }
  
  
  /**
   * @return array<string, array<CorrectnessProblem>> Field-names mapped on their correctness problems.
   *         The key "__data" is used for problems with the whole data entry.
   */
  public function checkUserDataCorrectness(): array {
    
    # todo:
    
    return [];
  }
  
  /**
   * @throws Failure
   */
  public function generateVerificationCode(): string {
    try {
      return bin2hex(random_bytes(16));
    } catch (Exception $e) {
      throw new Failure(
        userDisplayError: "Failed to generate verification code.",
        message:          "Failed to generate verification code: random_bytes failed.",
        previous:         $e
      );
    }
  }
  
  /**
   * todo: add options for pagination
   * @return array<UserData>
   * @throws Failure
   */
  public static function getAllUsers(): array {
    return App::queryForList(
      sql:                    "SELECT * FROM users",
      classNameWithNamespace: UserData::class
    );
  }
  
  /**
   * @throws Failure
   */
  public function update(): void {
    $this->checkCorrectnessWhereCorrectnessIsExpected(__FUNCTION__);
    $sql = "UPDATE users SET username = :username, email = :email, password = :password, verified = :verified, verification_code = :verification_code WHERE id = :id";
    try {
      
      $stmt = App::$pdo->prepare($sql);
      $stmt->execute(
        [
          "id"                => $this->id,
          "username"          => $this->username,
          "email"             => $this->email,
          "password"          => $this->password,
          "verified"          => $this->verified,
          "verification_code" => $this->verification_code,
        ]
      );
    } catch (Exception $e) {
      throw new Failure(
        userDisplayError: "Database error: Ask the admin to check the logs.",
        message:          "Failed to update user: " . $sql . " the id was: " . $this->id,
        previous:         $e
      );
    }
    
  }
  
  /**
   * @throws Failure
   */
  public function delete(PDO $pdo): void {
    $sql = "DELETE FROM users WHERE id = :id";
    
    try {
      
      $stmt = $pdo->prepare($sql);
      
      $stmt->execute(
        [
          "id" => $this->id,
        ]
      );
    } catch (Exception $e) {
      throw new Failure(
        userDisplayError: "Database error: Ask the admin to check the logs.",
        message:          "Failed to delete user: " . $sql . " the id was: " . $this->id,
        previous:         $e
      );
    }
  }
  
  /**
   * @throws Failure
   */
  public function insert(): void {
    $this->checkCorrectnessWhereCorrectnessIsExpected(__FUNCTION__);
    $sql = "
        INSERT INTO users (
            username,
            email,
            verified,
            password,
            verification_code
        ) VALUES (
            :username,
            :email,
            :verified,
            :password,
            :verification_code
        )
    ";
    $id = App::insertEntry(
      sql: $sql,
      params: ([
        "username"          => $this->username,
        "email"             => $this->email,
        "verified"          => $this->verified,
        "password"          => $this->password,
        "verification_code" => $this->verification_code,
      ])
    );
    $this->id = $id;
  }
  
  /**
   * @throws Failure
   */
  public function save(): void {
    if ($this->id === -1) {
      $this->insert();
    } else {
      $this->update();
    }
  }
  
  public static function createTestData(): void {
    UserData::$test_user_data = [];
    
    # this entry is first so it gets the id 1
    $u1 = new UserData();
    $u1->username = "majo";
    $u1->email = "hackermanmajo@gmail.com";
    $u1->password = password_hash("123", PASSWORD_DEFAULT);
    $u1->verified = 1;
    $u1->verification_code = "123";
    
    UserData::$test_user_data[] = $u1;
    
    # todo: add more ...
  }
  
}

