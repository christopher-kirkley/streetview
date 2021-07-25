<?php

require 'encode.php';
require 'config.php';

$apiKey = $api['apiKey'];
$privateKey = $api['privateKey'];

$baseURL = 'https://maps.googleapis.com/maps/api/streetview/metadata?';
  
function getGeoCoord() {
  $ATTEMPTS = 5;
  $url = "https://api.3geonames.org/?randomland=US&json=1";
  for ($i; $i < $ATTEMPTS; $i++) {
    $data = callApi($url);
    sleep(1);
    if ($data['nearest']) {
      $lat = $data['nearest']['latt'];
      $long = $data['nearest']['longt'];
      return ($lat . "," . $long);
      break;
    }
  }
};

function callApi($url) {
  $ch = curl_init();

  // set url
  curl_setopt($ch, CURLOPT_URL, $url);

  //return the transfer as a string
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

  // $output contains the output string
  $output = curl_exec($ch);

  // close curl resource to free up system resources
  curl_close($ch);
  return json_decode($output, true);
};


function getImage($panoID, $apiKey) {
  $base = "https://maps.googleapis.com/maps/api/streetview?";
  $request_params = [
    pano => $panoID,
    radius => 100,
    size => "600x400",
    source => "outdoor",
    key => $apiKey,
  ];
  $url = $base . http_build_query($request_params);
  $ch = curl_init();

  // set url
  curl_setopt($ch, CURLOPT_URL, $url);

  //return the transfer as a string
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

  // $output contains the output string
  $output = curl_exec($ch);

  // close curl resource to free up system resources
  curl_close($ch);
  return $output;
}


if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $coord = getGeoCoord();

    $request_params = [
      location => $coord,
      radius => 100000,
      key => $apiKey,
    ];

    $url = $baseURL . http_build_query($request_params);

    $signedURL = signUrl($url, $privateKey);

    $resp = callApi($signedURL);

    if ($resp["status"] == 'OK') {
      $panoID = $resp['pano_id'];
      $raw = getImage($panoID, $apiKey);
      $data = base64_encode($raw);
      $obj1 = [
        status=>'ok',
        img=> $data,
      ];
      echo json_encode($obj1);

    } else {
      $myObj = [
        status=>'error',
        coord=>$coord
      ];
      echo json_encode($myObj);
    }



}

?>
