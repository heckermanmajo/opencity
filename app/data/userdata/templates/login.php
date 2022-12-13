<?php

/**
 * @var string $login_error
 */

use data\failure\Failure;
use data\userdata\UserData;

if (isset($_POST["action"]) && $_POST["action"] === "login") {
  App::logInfo("Logging in user");
  $username_or_mail = $_POST["username_or_mail"];
  $password = $_POST["password"];
  try {
    $user = UserData::getUserByUsernameOrEmail($username_or_mail);
    if ($user === null) {
      $login_error = "Benutzername oder Email nicht gefunden";
    } else {
      if (password_verify($password, $user->password)) {
        $_SESSION["user"] = $user;
        $_SESSION["user_id"] = $user->id;
        header("Location: /");
      } else {
        $login_error = "Passwort falsch";
      }
    }
  } catch (Failure $e) {
    $e->getUserErrorBox();
  }
}

$login_error_card = "";
if ($login_error !== "") {
  $login_error_card = <<<HTML
    <div class="w3-panel w3-red">
      <h3>Oops!</h3>
      <p>$login_error</p>
    </div>
HTML;
}

?>

<div>
  <?= $login_error_card ?>
</div>
<form method="post">
  <label>
    <span class="oc-input-label"> Benutzername/Email: </span>
    <input type="text" name="username_or_mail"
           value="<?= $_POST["username_or_mail"] ?? "" ?>">
  </label>
  <br>
  <label>
    <span class="oc-input-label"> Passwort: </span>
    <input type="password" name="password">
  </label>
  <input type="hidden" name="action" value="login">
  <br>
  <button> Einloggen</button>
</form>




