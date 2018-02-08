<?php
/*
	Name: anomalyReader
	Version: 1.0
	Author: Mert S. Kaplan
	Web site: https://lab.mertskaplan.com/anomaly-reader
	Twitter: @anomalyReader
	Contact: mail@mertskaplan.com
	License: GNU General Public License v3
*/

// anomaly read settings
$city = 'Ankara';
$geocode = '39.925912,32.852308';
$word = 'patlama';

// app settings
$consumerKey = '';
$consumerSecret = '';
$accessToken = '';
$accessTokenSecret = '';

// functions
function anomaly($array,$count) {
	return ($count > array_sum($array)/count($array) * 1.4 ? true : false);
}

// defaults
header('Content-Type: application/json; charset=utf-8');

// codes

require_once 'class/twitteroauth/autoload.php';
use Abraham\TwitterOAuth\TwitterOAuth;

$connection = new TwitterOAuth($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);
$limits = $connection->get('application/rate_limit_status', array('resources' => 'search'));
if ($connection->getLastHttpCode() == 200 && !isset($limits->errors[0]->code)) { // level 1
	if ($limits->resources->search->{'/search/tweets'}->remaining >= 1) { // level 2

		$exportErrors['errors'] = $arrayWrite = $timeArray = $countArray = $sinceArray = '';
		
		$countFile = file("counts.txt", FILE_IGNORE_NEW_LINES);
		foreach ($countFile as $lines) {
			$line = explode(',', $lines);
			$timeArray[] = $line[0];
			$countArray[] = $line[1];
			$sinceArray[] = $line[2];

			$array[] = $line[0] .','. $line[1] .','. $line[2] ."\n";
		}
		array_shift($array);

		$search = $connection->get('search/tweets', array('q' => '-filter:retweets AND '. $word .' geocode:'. $geocode .',65mi OR '. $city .' '. $word, 'result_type' => 'recent', 'count' => '100', 'since_id' => end($sinceArray), 'include_entities' => false));
		$avarageTweet = floor(count($search->statuses) / (floor(time() / 60) - floor(end($timeArray) / 60)));

		if (anomaly($countArray,$avarageTweet)) {
			// tweet at
		} else {
				print_r(json_encode([
					'tweetCount' => count($search->statuses),
					'passedTime' => floor(time() / 60) - floor(end($timeArray) / 60),
					'avarageTweet' => $avarageTweet,
					'allAvarageTweet' => array_sum($countArray)/count($countArray),
					'anomalyBorder' => array_sum($countArray)/count($countArray) * 1.4
				]));
		}

		foreach ($array as $a) {
			$arrayWrite .= $a;
		}

		if (isset($search->statuses[0]->id)) {
			$sinceId = $search->statuses[0]->id;
		} else {
			$sinceId = end($sinceArray);
		}
		$open = fopen('counts.txt','r+');
		$write = $arrayWrite;
		$write .= time() .','. $avarageTweet .','. $sinceId ."\n";
		fwrite($open, $write);
		fclose($open);
	} else {
		array_push($exportErrors['errors'], [
			'errorCode' => '777',
			'errorMessage' => 'Rate limit exceeded',
			'errorLevel' => 2
		]);
	}
} else {
	$errors = $limits;
	$level = '1';
	require 'errors.php';
}

if ($exportErrors['errors'] != '') {
	print_r(json_encode($exportErrors['errors']));
}
?>
