<?php

if ($_SERVER["REQUEST_METHOD"] == "GET") {
  $url = ($_GET['url']);

  $request_params = $_GET;

  if (is_array($request_params)) {
    unset($request_params['url']);
    $query = $url . '&' . http_build_query($request_params);
  }

  $ch = curl_init();

  // set url
  curl_setopt($ch, CURLOPT_URL, $query);

  //return the transfer as a string
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

  // $output contains the output string
  $output = curl_exec($ch);

  // close curl resource to free up system resources
  curl_close($ch);
  echo ($output);
  /* echo json_encode($output); */
}
?>
