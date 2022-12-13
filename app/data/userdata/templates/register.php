<?php

/**
 * @var string $reg_error
 */


###############################################
# REGISTER
###############################################
use data\failure\Failure;
use data\userdata\UserData;

if (isset($_POST["action"]) && $_POST["action"] === "reg") {
  App::logInfo("Registering new user");
  // check if such user already exists
  try {
    $user = UserData::getUserByUsername($_POST["username"]);
  } catch (Failure $e) {
    $e->getUserErrorBox();
    goto end_reg_and_login;
  }
  
  if ($user) {
    $reg_error .= "User already exists: Username ist schon vergeben. ";
    App::logInfo("User already exists: Username ist schon vergeben.");
    goto end_reg_and_login;
  }
  
  // check if such email already exists
  try {
    $user = UserData::getUserByEmail($_POST["email"]);
  } catch (Failure $e) {
    $e->getUserErrorBox();
    goto end_reg_and_login;
  }
  
  if ($user) {
    $reg_error .= "Email already exists: Email ist schon vergeben. ";
    App::logInfo("Email already exists: Email ist schon vergeben.");
    goto end_reg_and_login;
  }
  
  // check if password and password2 are the same
  
  if ($_POST["password"] !== $_POST["password_repeat"]) {
    $reg_error .= "Passwords do not match: Passwörter stimmen nicht überein. ";
    App::logInfo("Passwords do not match: Passwörter stimmen nicht überein.");
    goto end_reg_and_login;
  }
  # todo: use check correctness problems for these checks ...
  // check if email is valid
  
  if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    $reg_error .= "Email is not valid: Email ist nicht gültig. ";
    App::logInfo("Email is not valid: Email ist nicht gültig.");
    goto end_reg_and_login;
  }
  
  // check if username is valid
  
  if (!preg_match("/^[a-zA-Z0-9_]{3,20}$/", $_POST["username"])) {
    $reg_error .= "Username is not valid: Username ist nicht gültig. ";
    App::logInfo("Username is not valid: Username ist nicht gültig.");
    goto end_reg_and_login;
  }
  
  // check if password is valid
  
  if (!preg_match("/^[a-zA-Z0-9_]{3,20}$/", $_POST["password"])) {
    $reg_error .= "Password is not valid: Passwort ist nicht gültig. ";
    App::logInfo("Password is not valid: Passwort ist nicht gültig.");
    goto end_reg_and_login;
  }
  
  $password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);
  
  $new_user = new UserData();
  
  $new_user->username = $_POST["username"];
  $new_user->email = $_POST["email"];
  $new_user->password = $password_hash;
  
  try {
    $new_user->verification_code = $new_user->generateVerificationCode();
  } catch (Failure $e) {
    $e->getUserErrorBox();
    goto end_reg_and_login;
  }
  
  try {
    $new_user->save(App::$pdo);
    App::logInfo("New user saved with id: $new_user->id and name $new_user->username");
  } catch (Failure $e) {
    $e->getUserErrorBox();
    goto end_reg_and_login;
  }
  
  // send verification email
  // todo: ....
  
  end_reg_and_login:
  
}



?>
<!-- registration form -->
<div>
  <?= $reg_error ?>
</div>
<form method="post">
  <label>
    <span class="oc-input-label"> Benutzername: </span>
    <input type="text" name="username" value="<?= $_POST["username"] ?? "" ?>">
  </label>
  <br>
  <label>
    <span class="oc-input-label"> Email: </span>
    <input type="email" name="email" value="<?= $_POST["email"] ?? "" ?>">
  </label>
  <br>
  <label>
    <span class="oc-input-label"> Passwort: </span>
    <input type="password" name="password">
  </label>
  <br>
  <label>
    <span class="oc-input-label"> Passwort wiederholen: </span>
    <input type="password" name="password_repeat">
  </label>
  <br>
  <!-- Datenschutz gelesen und akzeptiert -->
  <label>
    <input type="checkbox" name="privacy"
           value="<?= ($_POST["privacy"] ?? null) ?? "checked" ?>">
    <span class="oc-input-label"> Ich habe die <a href="#">Datenschutzerklärung</a> gelesen und akzeptiere diese. </span>
  </label>
  <input type="hidden" name="action" value="reg">
  <br>
  <button> Registrieren</button>
</form>
