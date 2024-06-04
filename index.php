<?php
session_start();
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8" />
    <title>お問い合わせフォーム</title>
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
    <h1>お問い合わせフォーム</h1>
    <form action="send.php" method="post">
      <input
        type="hidden"
        name="csrf_token"
        value="<?= $_SESSION['csrf_token'] ?>"
      />
      <label for="name">名前:</label>
      <input type="text" id="name" name="name" required />
      <br />
      <label for="email">メールアドレス:</label>
      <input type="email" id="email" name="email" required />
      <br />
      <label for="message">本文:</label>
      <textarea id="message" name="message" required></textarea>
      <br />
      <input type="submit" value="送信" />
    </form>
  </body>
</html>
