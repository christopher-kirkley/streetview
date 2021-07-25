<?php

require 'encode.php';
require 'config.php';

$apiKey = $api['apiKey'];
$privateKey = $api['privateKey'];



class GeoCoord
{

  const ATTEMPTS = 5;

  const url = "https://api.3geonames.org/?randomland=US&json=1";

  public function getCoord()
  {
	  for ($i = 0; $i < self::ATTEMPTS; $i++) {
		$raw = callApi(self::url);
		$data = json_decode($raw, true);
		sleep(1 * $i);
		if ($data['nearest']) {
		  $lat = $data['nearest']['latt'];
		  $long = $data['nearest']['longt'];
		  return ($lat . "," . $long);
		  break;
		}
  }

  }

}

class StreetViewMeta
{

	const baseURL = 'https://maps.googleapis.com/maps/api/streetview/metadata?';


	function __construct($apiKey, $privateKey, $coord) {
		$this->coord = $coord;
		$this->request_params = [
			'location' => $coord,
			'radius' => 10000,
			'key' => $apiKey,
		];
		$this->privateKey = $privateKey;
		$this->apiKey = $apiKey;
		$this->url = self::baseURL . http_build_query($this->request_params);
		$this->signedURL = signUrl($this->url, $privateKey);
	}

	public function getMeta()
	{
		$raw = callApi($this->signedURL);
		return json_decode($raw, true);
	}

}


class StreetViewImage
{

	const baseURL = "https://maps.googleapis.com/maps/api/streetview?";

	function __construct($apiKey, $privateKey, $panoID) {
		$this->request_params = [
			'pano' => $panoID,
			'radius' => 100,
			'size' => "600x400",
			'source' => "outdoor",
			'key' => $apiKey,
		];
		$this->privateKey = $privateKey;
		$this->apiKey = $apiKey;
		$this->url = self::baseURL . http_build_query($this->request_params);
	}


	public function getImage()
	{
		$raw = callApi($this->url);
		return $raw;
	}

}

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
  return $output;
};




if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $geoCoord = new GeoCoord;

	$coord = $geoCoord -> getCoord();

	$streetMeta = new StreetViewMeta($apiKey, $privateKey, $coord);

	$resp = $streetMeta -> getMeta();

    if ($resp["status"] == 'OK') {
      $panoID = $resp['pano_id'];
	  $streetImageAPI = new StreetViewImage($apiKey, $privateKey, $panoID);
	  $raw = $streetImageAPI -> getImage();
      $data = base64_encode($raw);
      $obj1 = [
        'status'=>'ok',
        'img'=> $data,
      ];
      echo json_encode($obj1);

    } else {
      $myObj = [
        'status'=>'error',
        'coord'=>$coord
      ];
      echo json_encode($myObj);
    }



}

?>
