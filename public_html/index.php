<?php
require("../req/all/codes.php");
require("../req/keys/mysql.php");
require("../req/keys/recaptcha.php");
require("../req/all/api-v1.php");

createLog("info", $HTTP_OK, $_SERVER["REQUEST_URI"], "-", "Navigation", "-");
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php require("../req/head/head.php"); ?>
  </head>

  <body id="bg">
    <?php require("../req/structure/navbar.php"); ?>

    <noscript><?php require("../req/structure/js-alert.php"); ?></noscript>

    <div id="body-container" class="container">
      <div id="alert-container"><?php require("../req/structure/alert-container.php"); ?></div>

      <div class="jumbotron my-5">
        <h1 class="display-4">Premier Close Air Support Mission Planning</h1>
        <p class="lead">Integration is essential to CAS. With Hawg Ops CAS Planner, mission planning takes less time, allows you to share scenarios &amp; GRGs with participating units, and use your scenario as a visual aid for flight briefs &amp; debriefs.</p>
        <hr class="my-4">
        <h4>Version 2</h4>
        <p>I am currently fielding Hawg View Version 2 Beta. If you have not yet used Hawg View, please start with version 2 as that will be where I am shifting the site to. If you are a returning user, please try out version 2 and work to move your scenarios to version 2.</p>
        <hr class="my-4">
        <p>I'm always looking for more ideas on how to enhance the mission planning process, if you have any ideas to make this product better, let me know!</p>
      </div>
    </div>
  </body>
</html>
<?php closeLogs(); ?>