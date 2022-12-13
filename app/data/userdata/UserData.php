<?php

declare(strict_types=1);

namespace data\userdata;

use App;
use data\failure\CorrectnessProblem;
use data\failure\Failure;
use Exception;
use PDO;

class UserData {
  
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
  public static function getUserById(int $id): ?UserData {
    $sql = "SELECT * FROM users WHERE id = :id";
    try {
      $stmt = App::$pdo->prepare($sql);
      $stmt->execute(["id" => $id]);
      $stmt->setFetchMode(PDO::FETCH_CLASS, UserData::class);
      
      return $stmt->fetch();
    } catch (Exception $e) {
      throw new Failure(
        userDisplayError: "Database error: Ask the admin to check the logs.",
        message:          "Failed to get user by id: " . $sql . " the id was: " . $id,
        previous:         $e
      );
    }
  }
  
  /**
   * @throws Failure
   */
  public static function getUserByUsername(string $username): ?UserData {
    $sql = "SELECT * FROM users WHERE username = :username LIMIT 1";
    try {
      $stmt = App::$pdo->prepare($sql);
      $stmt->execute(["username" => $username]);
      $stmt->setFetchMode(PDO::FETCH_CLASS, UserData::class);
      return $stmt->fetch();
    } catch (Exception $e) {
      throw new Failure(
        userDisplayError: "Database error: Ask the admin to check the logs.",
        message:          "Failed to get user by username: " . $sql . " the username was: " . $username,
        previous:         $e
      );
    }
  }
  
  /**
   * @throws Failure
   */
  public static function getUserByEmail(string $email): ?UserData {
    $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
    try {
      $stmt = App::$pdo->prepare($sql);
      $stmt->execute(["email" => $email]);
      $stmt->setFetchMode(PDO::FETCH_CLASS, UserData::class);
      return $stmt->fetch();
    } catch (Exception $e) {
      throw new Failure(
        userDisplayError: "Database error: Ask the admin to check the logs.",
        message:          "Failed to get user by email: " . $sql . " the email was: " . $email,
        previous:         $e
      );
    }
  }
  
  /**
   * @throws Failure
   */
  public static function getUserByVerificationCode(string $verification_code): ?UserData {
    $sql = "SELECT * FROM users WHERE verification_code = :verification_code LIMIT 1";
    try {
      $stmt = App::$pdo->prepare($sql);
      $stmt->execute(["verification_code" => $verification_code]);
      $stmt->setFetchMode(PDO::FETCH_CLASS, UserData::class);
      return $stmt->fetch();
    } catch (Exception $e) {
      throw new Failure(
        userDisplayError: "Database error: Ask the admin to check the logs.",
        message:          "Failed to get user by verification code: " . $sql . " the verification code was: " . $verification_code,
        previous:         $e
      );
    }
  }
  
  /**
   * @throws Failure
   */
  public static function getUserByUsernameOrEmail(string $usernameOrEmail): ?UserData {
    $user = self::getUserByUsername($usernameOrEmail);
    if ($user === null) {
      $user = self::getUserByEmail($usernameOrEmail);
    }
    return $user;
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
  public function getAllUsers(): array {
    $sql = "SELECT * FROM users";
    try {
      $stmt = App::$pdo->prepare($sql);
      $stmt->execute();
      $stmt->setFetchMode(PDO::FETCH_CLASS, UserData::class);
      return $stmt->fetchAll();
    } catch (Exception $e) {
      throw new Failure(
        userDisplayError: "Database error: Ask the admin to check the logs.",
        message:          "Failed to get all users: " . $sql,
        previous:         $e
      );
    }
  }
  
  /**
   * @throws Failure
   */
  public function update(PDO $pdo): void {
    $errors = $this->checkUserDataCorrectness();
    if (count($errors) > 0) {
      throw new Failure(
        userDisplayError: "Failed to update user because of internal error by bad coding.",
        message:          "Bad Userdata reached update function.<br>
                  This is a coding erorr. <br>
                  Failed to insert user: " . implode(", ", $errors),
      );
    }
    $sql = "UPDATE users SET username = :username, email = :email, password = :password, verified = :verified, verification_code = :verification_code WHERE id = :id";
    try {
      
      $stmt = $pdo->prepare($sql);
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
  public function insert(PDO $pdo): void {
    
    $errors = $this->checkUserDataCorrectness();
    if (count($errors) > 0) {
      throw new Failure(
        userDisplayError: "Failed to insert user because of internal error by bad coding.",
        message:          "Bad Userdata reached insert function.<br>
                  This is a coding erorr. <br>
                  Failed to insert user: " . implode(", ", $errors),
      );
    }
    
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
    try {
      $stmt = $pdo->prepare($sql);
      
      $stmt->execute(
        [
          "username"          => $this->username,
          "email"             => $this->email,
          "password"          => $this->password,
          "verification_code" => $this->verification_code,
          "verified"          => $this->verified,
        ]
      );
    } catch (Exception $e) {
      throw new Failure(
        userDisplayError: "Database error: Ask the admin to check the logs.",
        message:          "Failed to insert user: " . $sql . " the data was: " . json_encode($this),
        previous:         $e
      );
    }
    
    $lastInsertedId = $pdo->lastInsertId();
    
    if ($lastInsertedId !== false) {
      $this->id = (int)$lastInsertedId;
    } else {
      throw new Failure(
        userDisplayError: "Failed to insert user.",
        message:          "Failed to insert user: lastInsertId returned false.",
      );
    }
    
  }
  
  /**
   * @throws Failure
   */
  public function save(PDO $pdo): void {
    if ($this->id === -1) {
      $this->insert($pdo);
    } else {
      $this->update($pdo);
    }
  }
  
  
}