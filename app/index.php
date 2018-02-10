<?php
/*
	Name: anomalyReader
	Version: 1.0
	Author: Mert S. Kaplan
	Contact: mail@mertskaplan.com
	GitHub: https://github.com/mertskaplan/anomaly-reader
	Twitter: @anomalyReader
	License: GNU General Public License v3
*/

// anomaly read settings
$word = 'patlama';
$cities = [
	['plate' => 01, 'name' => 'Adana', 'key' => 'a', 'lat' => '36.991419', 'long' => '35.330829'],
	['plate' => 06, 'name' => 'Ankara', 'key' => 'a', 'lat' => '39.920951', 'long' => '32.854003'],
	['plate' => 16, 'name' => 'Bursa', 'key' => 'a', 'lat' => '40.188528', 'long' => '29.060964'],
	['plate' => 21, 'name' => 'Diyarbakır', 'key' => 'a', 'lat' => '37.924973', 'long' => '40.210983'],
	['plate' => 25, 'name' => 'Erzurum', 'key' => 'a', 'lat' => '39.905499', 'long' => '41.265824'],
	['plate' => 26, 'name' => 'Eskişehir', 'key' => 'e', 'lat' => '39.766706', 'long' => '30.525631'],
	['plate' => 34, 'name' => 'İstanbul', 'key' => 'a', 'lat' => '41.008238', 'long' => '28.978359'],
	['plate' => 35, 'name' => 'İzmir', 'key' => 'e', 'lat' => '38.423734', 'long' => '27.142826'],
	['plate' => 55, 'name' => 'Samsun', 'key' => 'a', 'lat' => '41.279703', 'long' => '36.336067'],
	['plate' => 61, 'name' => 'Trabzon', 'key' => 'a', 'lat' => '41.005915', 'long' => '39.718494'],
	['plate' => 63, 'name' => 'Şanlıurfa', 'key' => 'a', 'lat' => '37.167512', 'long' => '38.795578'],
	['plate' => 65, 'name' => 'Van', 'key' => 'a', 'lat' => '38.501215', 'long' => '43.372908']
];
$radius = '20km';

// app settings
$consumerKey = '';
$consumerSecret = '';
$accessToken = '';
$accessTokenSecret = '';

// defaults
// ini_set('display_errors', 'On'); // http://php.net/manual/en/ini.list.php
// error_reporting(E_ALL | E_STRICT);
$export['errors'] = $export['result'] = [];

// functions
function anomaly($array,$count) {
	return ($count > array_sum($array)/count($array) * 1.4 ? true : false);
}

// codes
header('Content-Type: application/json; charset=utf-8');
require_once 'class/twitteroauth/autoload.php';
use Abraham\TwitterOAuth\TwitterOAuth;

$connection = new TwitterOAuth($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);
$limits = $connection->get('application/rate_limit_status', array('resources' => 'search'));
if ($connection->getLastHttpCode() == 200 && !isset($limits->errors[0]->code)) { // level 1
	if ($limits->resources->search->{'/search/tweets'}->remaining >= 1) { // level 2

		foreach ($cities as $city) {
			$array = $arrayWrite = $timeArray = $countArray = $sinceArray = $write = '';

			$countFile = file('cities/'. $city['plate'] .'.txt', FILE_IGNORE_NEW_LINES);
			foreach ($countFile as $lines) {
				$line = explode(',', $lines);
				if (isset($line[0])) {
					$timeArray[] = $line[0];
					$countArray[] = $line[1];
					$sinceArray[] = $line[2];
					$array[] = $line[0] .','. $line[1] .','. $line[2] ."\n";
				}
			}
			array_shift($array);

			$q = $city['name'] .' '. $word .' OR '. $city['name'] .'d'. $city['key'] .' '. $word .' OR '. $city['name'] .'d'. $city['key'] .'n '. $word .' OR '. $city['name'] .'d'. $city['key'] .'ki '. $word .' OR "'. $word .' geocode:'. $city['lat'] .','. $city['long'] .' within:'. $radius .'" exclude:nativeretweets exclude:retweets';
			$search = $connection->get('search/tweets', array('q' => $q, 'result_type' => 'recent', 'count' => '100', 'since_id' => end($sinceArray), 'include_entities' => false));

			$count = count($search->statuses);
			$timeDifference = floor(time() / 60) - floor(end($timeArray) / 60);
			if ($timeDifference <= 0) {
				$timeDifference = 1;
			}
			if ($count > 0) {
				$avarageTweet = $count / $timeDifference;
			} else {
				$avarageTweet = 0;
			}
			$searchLink = 'https://twitter.com/search?f=tweets&q='. $city['name'] .'%20'. $word .'%20OR%20'. $city['name'] .'d'. $city['key'] .'%20'. $word .'%20OR%20'. $city['name'] .'d'. $city['key'] .'n%20'. $word .'%20OR%20'. $city['name'] .'d'. $city['key'] .'ki%20'. $word .'%20OR%20%22'. $word .'%20near%3A'. $city['lat'] .'%2C'. $city['long'] .'%20within%3A'. $radius .'%22%20exclude%3Anativeretweets%20exclude%3Aretweets%20&src=typd';

			if (anomaly($countArray,$avarageTweet)) {
				// tweet
				$tweetText = '#'. $city['name'] .' civarından gönderilen veya '. $city['name'] .' ile ilgili olarak "'. $word .'" kelimesini içeren tweetlerin sayısında anomali tespit edildi. Bkz: '. $searchLink;
				$geo = $connection->get('geo/reverse_geocode', array('lat' => $city['lat'], 'long' => $city['long']));
				$tweet = $connection->post('statuses/update', array('status' => $tweetText, 'lat' => $city['lat'], 'long' => $city['long'], 'place_id' => $geo->result->places[0]->id, 'display_coordinates ' => true));
				if ($connection->getLastHttpCode() == 200 && !isset($tweet->errors[0]->code)) {
					array_push($export['result'], [
						'anomaly' => true,
						'tweeted' => true,
						'city' => $city['name'],
						'tweetCount' => count($search->statuses),
						'passedTime' => floor(time() / 60) - floor(end($timeArray) / 60),
						'avarageTweet' => $avarageTweet,
						'allAvarageTweet' => array_sum($countArray)/count($countArray),
						'anomalyBorder' => array_sum($countArray)/count($countArray) * 1.4,
						'searchLink' => $searchLink
					]);
				} else {
					array_push($export['result'], [
						'anomaly' => true,
						'tweeted' => false,
						'city' => $city['name'],
						'tweetCount' => count($search->statuses),
						'passedTime' => floor(time() / 60) - floor(end($timeArray) / 60),
						'avarageTweet' => $avarageTweet,
						'allAvarageTweet' => array_sum($countArray)/count($countArray),
						'anomalyBorder' => array_sum($countArray)/count($countArray) * 1.4,
						'searchLink' => $searchLink
					]);
				}
			} else {
				array_push($export['result'], [
					'anomaly' => false,
					'tweeted' => null,
					'city' => $city['name'],
					'tweetCount' => count($search->statuses),
					'passedTime' => floor(time() / 60) - floor(end($timeArray) / 60),
					'avarageTweet' => $avarageTweet,
					'allAvarageTweet' => array_sum($countArray)/count($countArray),
					'anomalyBorder' => array_sum($countArray)/count($countArray) * 1.4,
					'searchLink' => $searchLink
				]);
			}

			foreach ($array as $a) {
				$arrayWrite .= $a;
			}

			if (isset($search->statuses[0]->id)) {
				$sinceId = $search->statuses[0]->id;
			} else {
				$sinceId = end($sinceArray);
			}
			$open = fopen('cities/'. $city['plate'] .'.txt', 'w');
			$write = $arrayWrite;
			$write .= time() .','. $avarageTweet .','. $sinceId ."\n";
			fwrite($open, $write);
			fclose($open);
		}
	} else {
		array_push($export['errors'], [
			'errorCode' => '777',
			'errorMessage' => 'Rate limit exceeded',
			'errorLevel' => 2
		]);
	}
} else {
	$errors = $limits;
	$level = '1';
	require_once 'errors.php';
}

print_r(json_encode($export));
?>
