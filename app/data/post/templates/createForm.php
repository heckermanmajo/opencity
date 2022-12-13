<?php

# todo: handle creation

?>

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

