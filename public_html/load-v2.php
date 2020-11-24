<?php
  require("../req/all/codes.php");
  require("../req/keys/mysql.php");
  require("../req/keys/recaptcha.php");
  require("../req/all/api-v1.php");

  createLog("info", $HTTP_OK, $_SERVER["REQUEST_URI"], "-", "Navigation", "-");

  if(!isLoggedIn()) {
    createLog("warning", $ERROR_UNAUTHORIZED, "my-scenarios", "-", "User not logged in", "-");
    //logErrorMsg("User is not logged in. ($ERROR_UNAUTHORIZED)");
    header("Location: /?s=$ERROR_UNAUTHORIZED");
    closeLogs();
  }

  $v2_scenario = null;
  $v2_scenario_name = "";

  if(isset($_GET["scenario"]) && $_GET["scenario"] != "") {
    $v2_scenario = getScenario($_GET["scenario"]);
    $v2_scenario_name = getScenarioName($_GET["scenario"]);
  } else {
    header("Location: /?s=$API_GET_SCENARIO_SCENARIO_ID_NOT_RECEIVED");
  }

  if ($v2_scenario != null) {
    $v2_decoded_json = json_decode($v2_scenario, true);


    $v2_decoded_json["name"] = $v2_scenario_name;
    $v2_encoded_json = json_encode($v2_decoded_json);

    $v2_encoded_json = str_replace('\\', '', $v2_encoded_json);
    /*header("Content-type: text/plain");
    header("Content-Disposition: attachment; filename=Hawg View Scenario ".$v2_scenario_name.".txt");
    echo $v2_scenario;*/
  }
?>
<DOCTYPE! html>
<html>
  <head>
    <script type="module">
      import { handleUpdateScenario } from "./js/handleUpdateScenario.js";
      var scenario = handleUpdateScenario(<?php echo $v2_encoded_json; ?>);
      const element = document.createElement('a');
      const file = new Blob([JSON.stringify(scenario)], { type: 'text/plain' })
      element.href = URL.createObjectURL(file);
      element.download = `Hawg View Scenario <?php echo $v2_scenario_name; ?>.txt`;
      document.body.appendChild(element)
      element.click()
      setTimeout(() => {
        window.close()
      }, 1)
    </script>
  </head>
  <body>

  </body>
</head>