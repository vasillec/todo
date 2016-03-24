<!DOCTYPE html>

<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="view/css/style.css" />
    <script src="view/js/jquery-1.12.0.min.js"></script>
    <script src="view/js/jquery-ui.min.js"></script>
</head>
<body>
<?php if(isset($_SESSION["logged_in"])) : ?>
  <section class="main-wrap">
      <div class="log-out-block">
          <a id="logOut" class="blue-btn" href="logout.php">Log Out</a>
          <span class="main-title">User: <?php echo $_SESSION['login']; ?></span>
      </div>
      <header>
          <h1 class="main-title projects-title">TODO LISTS</h1>
      </header>
<?php endif; ?>