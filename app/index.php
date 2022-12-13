<?php
/*  TODO- LISTE
*/

use data\userdata\UserData;

include "lib.php";

echo App::pageTop();

$reg_error = "";
$login_error = "";

$search = false;

if(isset($_GET["action"])){
    ###############################################
    # LOGIN
    ###############################################

    ###############################################
    # LOG OUT
    ###############################################
  if ($_POST["action"] === "logout") {
    $_SESSION = [];
    
    ###############################################
    # CREATE POST
    ###############################################
  } elseif ($_POST["action"] === "create_post") {
    $sql = "INSERT INTO posts (author_user_id, title, content, created_at, image, weblink, category, topic, searchTags) VALUES (:author_user_id, :title, :content, :created_at, :image, :weblink, :category, :topic, :searchTags)";
    
    $stmt = App::$pdo->prepare($sql);
    
    $stmt->execute(
      [
        "author_user_id" => $_SESSION["user_id"],
        "title"          => $_POST["title"],
        "content"        => $_POST["content"],
        "created_at"     => date("Y-m-d H:i:s"),
        "image"          => $_POST["image"],
        "weblink"        => $_POST["weblink"],
        "category"       => $_POST["category"],
        "topic"          => $_POST["topic"],
        "searchTags"     => $_POST["searchTags"]
      ]
    );
    
  } elseif ($_POST["action"] == "search") {
    
    $search_string = $_POST["search_string"]
                     ?? throw new Exception("search_string is missing");
    
    $search_string = trim($search_string);
    
    # escape search string
    $search_string = App::$pdo->quote($search_string);
    
    $search_posts = (bool)($_POST["search_posts"] ?? false);
    $search_users = (bool)($_POST["search_users"] ?? false);
    $search_comments = (bool)($_POST["search_comments"] ?? false);
    
    $user_results = [];
    $post_results = [];
    $comment_results = [];
    
    if ($search_users) {
      $sql = "SELECT * FROM users WHERE username LIKE %$search_string%";
      $stmt = App::$pdo->prepare($sql);
      $stmt->execute();
      $user_results = $stmt->fetchAll();
    }
    
    if ($search_posts) {
      $sql = "SELECT * FROM posts WHERE title LIKE :search_string OR content LIKE %$search_string%";
      $stmt = App::$pdo->prepare($sql);
      $stmt->execute();
      $post_results = $stmt->fetchAll();
    }
    
    if ($search_comments) {
      $sql = "SELECT * FROM comments WHERE content LIKE %$search_string%";
      $stmt = App::$pdo->prepare($sql);
      $stmt->execute();
      $comment_results = $stmt->fetchAll();
    }
    
    $search = true;
    
  } else {
    echo "Unknown action";
  }
}

end_reg_and_login:


if (App::isLoggedIn()) {
  App::logInfo("User is logged in: User ist eingeloggt.");
  ?>

  <h3>Logged in: Willkommen bei Open City!</h3>

  <pre>
    Hier kannst du
      - Missstände beschreiben
      - An die Verwaltung Anfragen stellen
      - Mit lokalen Unternehmen in Kontakt treten
      - Mit anderen Bürgern diskutieren
      - Dich mit anderen Bürgern vernetzen und Organisieren
      - Mit deinen lokalen politischen Vertretern in Kontakt treten
      - Ansatzpunkte für lokale Gemeinschafts-Projekte finden
    
    ---
    Offene Fragen: wie kann man sich sicher sein, dass jemand tatsächlich
    von der Institution ist, die er vorgibt zu sein?
    -> Mail von der Institution mit einem Id-Token
    
    -> Es gibt dann einen Organisations admin, der dann andere Leute seiner organisation
    hinzufügen kann
    
    Jede Stadt hat admins, die die Posts der Institutionen bestätigen können.
    Diese Admins agieren mit vertrauen.
    
    Admins müssen von dem Kern bestätigt werden.
    
  </pre>


  <form method="post">
    <label>
      Ausloggen
      <input type="submit" name="action" value="logout">
    </label>
  </form>


  <form method="post" class="w3-card w3-margin w3-padding">
    <h3>Post erstellen </h3>
    <label>
      Überschrift des Posts
      <input type="text" name="title" value="<?= $_POST["header"] ?? "" ?>">
    </label><br><br>
    <label>
      Beschreibung des Posts
      <textarea name="content"><?= $_POST["content"] ?? "" ?></textarea>
    </label><br><br>
    <label>
      Kategorie
      <select name="category">
        <option value="1">Missstände</option>
        <option value="2">Amtsträger Anfrage</option>
        <option value="3">Lokale Unternehmen</option>
        <option value="4">Diskussion</option>
        <option value="5">Vernetzung</option>
        <option value="6">Partei Anfrage</option>
        <option value="7">Gemeinschaftsprojekte</option>
      </select>
    </label>
    <br><br>
    <label>
      Thema
      <select name="topic">
        <option value="1">Soziales</option>
        <option value="2">Verkehr</option>
        <option value="3">Umwelt</option>
        <option value="4">Kultur</option>
        <option value="5">Wirtschaft</option>
        <option value="6">Bürgerrechte</option>
        <option value="7">Sicherheit</option>
        <option value="8">Lokale News</option>
      </select>
    </label>
    <br><br>
    <label>
      Suchbegriffe. (so können andere Menschen deinen Post durch die Suche finden)
      <textarea name="searchTags"><?= $_POST["searchTags"] ?? "" ?></textarea>
    </label>
    <br><br>
    <label>
      Weblink
      <input type="text" name="weblink" value="<?= $_POST["weblink"] ?? "" ?>">
    </label>
    <br><br>
    <label>
      Bild hochladen. (Nur bilder an denen du die Rechte hast, zum Beispiel Fotos.)
      <input type="file" name="image">
    </label>
    <br><br>
    <input type="hidden" name="action" value="create_post">
    <input type="submit" value="Post erstellen">
    <br><br>
  </form>

  <hr>
  
  <?php
  
  if ($search) {
    
    if ($search_users) {
      ?>
      <h3>Suchergebnisse für Benutzer</h3>
      <table>
        <tr>
          <th>Benutzername</th>
          <th>Profil</th>
        </tr>
        <?php
        foreach ($user_results as $user) {
          ?>
          <tr>
            <td><?= $user["username"] ?></td>
            <td><a href="one.php?class=User&id=<?= $user["id"] ?>">Profil</a></td>
          </tr>
          <?php
        }
        ?>
      </table>
      <?php
    }
    
    if ($search_posts) {
      ?>
      <h3>Suchergebnisse für Posts</h3>
      <table>
        <tr>
          <th>Titel</th>
          <th>Post</th>
        </tr>
        <?php
        foreach ($post_results as $post) {
          ?>
          <tr>
            <td><?= $post["title"] ?></td>
            <td><a href="one.php?class=Post&id=<?= $post["id"] ?>">Post</a></td>
          </tr>
          <?php
        }
        ?>
      </table>
      <?php
    }
    
    if ($search_comments) {
      ?>
      <h3>Suchergebnisse für Kommentare</h3>
      <table>
        <tr>
          <th>Post</th>
          <th>Kommentar</th>
        </tr>
        <?php
        foreach ($comment_results as $comment) {
          ?>
          <tr>
            <td><a href="one.php?class=Comment&id=<?= $comment["post_id"] ?>">Post</a>
            </td>
            <td><?= $comment["content"] ?></td>
          </tr>
          <?php
        }
        ?>
      </table>
      <?php
    }
    
  } else {
    
    $stmt = App::$pdo->prepare("
        SELECT *,
               (SELECT COUNT(*) FROM post_support WHERE post_id = posts.id) as j_support,
                (SELECT COUNT(*) FROM post_support WHERE post_id = posts.id AND user_id = :id)
                  as j_i_support
  
        FROM posts ORDER BY created_at DESC");
    $stmt->execute(["id" => $_SESSION["user"]->id]);
    $posts = $stmt->fetchAll();
    
    foreach ($posts as $post) {
      # escape post html for security
      foreach ($post as $key => $value) {
        if (is_string($value)) {
          $post[$key] = htmlspecialchars($value);
        }
      }
      ?>
      <div class="w3-card w3-margin w3-padding">
        <h3><?= $post["title"] ?></h3>
        <p>Author: <?= $post["author_user_id"] ?></p>
        <pre><?= $post["content"] ?></pre>
        <p><?= $post["created_at"] ?></p>
        <p><?= $post["image"] ?></p>
        <p><?= $post["weblink"] ?></p>
        <p><?= $post["category"] ?></p>
        <p><?= $post["topic"] ?></p>
        <p><?= $post["searchTags"] ?></p>

        <a href="one.php?id=<?= $post["id"] ?>">
          Open this post
        </a>

        <br><br>

        <form method="post">
          <input type="hidden" name="action" value="delete_post">
          <input type="hidden" name="post_id" value="<?= $post["id"] ?>">
          <input type="submit" value="Post löschen"
                 onclick="event.preventDefault();confirm('Post wirklich löschen?') && this.parentNode.submit()">
        </form>

        <form method="post">
          <input type="hidden" name="action" value="edit_post">
          <input type="hidden" name="post_id" value="<?= $post["id"] ?>">
          <input type="submit" value="Post bearbeiten">
        </form>
        
        <?php if ($post["j_i_support"] == 0) { ?>
          <button
            onclick="ajaxPost('ajax.php?action=post_support',{
              postId: <?= $post["id"] ?>,
              },(data)=>{
              console.log(data);
              location.reload()
              alert(data);
              })">
            Diesen Post unterstützen
          </button>
        <?php } else { ?>
          <button
            onclick="ajaxPost('ajax.php?action=post_support',{
              postId: <?= $post["id"] ?>,
              },(data)=>{
              // reload page
              location.reload()
              console.log(data);
              alert(data);
              })">
            Diesen Post nicht mehr unterstützen
          </button>
        <?php } ?>
      </div>
      <br>
      <?php
      
    }
  }
  
  ?>
  
  <?php
} else {
  App::logInfo("User not logged in");
  
  echo UserData::loginFormHtml($login_error);
  echo UserData::registerFormHtml($reg_error);
  
}


?>
</body>
</html>

