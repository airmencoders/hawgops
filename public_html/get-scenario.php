<?php
  require("../req/all/codes.php");
  require("../req/keys/mysql.php");
  require("../req/all/api-v1.php");

  if(isset($_GET["scenario"]) && $_GET["scenario"] != "") {
    echo getScenario($_GET["scenario"]);
  }
?>