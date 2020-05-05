<?php
  $response = array("version" => "1", "errors" => null, "response"=>"authentication passed");
  $response = json_encode($response);
  echo $response;
?>