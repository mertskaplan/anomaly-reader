<?php
if ($connection->getLastHttpCode() == 200 && isset($errors->errors[0]->code)) { // x.1
	if (in_array($errors->errors[0]->code, array(32,89,99,135,215,231))) { // Authenticate error.
		array_push($export['errors'], [
			'errorCode' => $errors->errors[0]->code,
			'errorMessage' => $errors->errors[0]->message,
			'errorLevel' => $level.'.1.1'
		]);
	} elseif (in_array($errors->errors[0]->code, array(63,64))) { // User has been suspended.
		array_push($export['errors'], [
			'errorCode' => $errors->errors[0]->code,
			'errorMessage' => 'User has been suspended',
			'errorLevel' => $level.'.1.2'
		]);
	} elseif ($errors->errors[0]->code == 88) { // Rate limit exceeded.
		array_push($export['errors'], [
			'errorCode' => $errors->errors[0]->code,
			'errorMessage' => 'Rate limit exceeded',
			'errorLevel' => $level.'.1.3'
		]);
	} elseif (in_array($errors->errors[0]->code, array(130,131))) { // There is noting to do.
		array_push($export['errors'], [
			'errorCode' => $errors->errors[0]->code,
			'errorMessage' => 'Twitter is temporarily over capacity or an unknown internal error occurred',
			'errorLevel' => $level.'.1.4'
		]);
	} elseif ($errors->errors[0]->code == 226) { // This request looks like it might be automated.
		array_push($export['errors'], [
			'errorCode' => $errors->errors[0]->code,
			'errorMessage' => 'This request looks like it might be automated',
			'errorLevel' => $level.'.1.5'
		]);
	} elseif ($errors->errors[0]->code == 326) { // To protect our users from spam and other malicious activity, this account is temporarily locked.
		array_push($export['errors'], [
			'errorCode' => $errors->errors[0]->code,
			'errorMessage' => 'To protect our users from spam and other malicious activity, this account is temporarily locked. Please log in to https://twitter.com to unlock your account',
			'errorLevel' => $level.'.1.6'
		]);
	} elseif ($errors->errors[0]->code == 161) { // You are unable to follow more people at this time.
		array_push($export['errors'], [
			'errorCode' => $errors->errors[0]->code,
			'errorMessage' => 'You are unable to follow more people at this time',
			'errorLevel' => $level.'.1.7'
		]);
	} elseif ($errors->errors[0]->code == 261) { // Application cannot perform write actions. !!!!!!!!!! Kullaniciyi uyarmayi unutma
		array_push($export['errors'], [
			'errorCode' => $errors->errors[0]->code,
			'errorMessage' => $errors->errors[0]->message,
			'errorLevel' => $level.'.1.8'
		]);
	} elseif (in_array($errors->errors[0]->code, array(50,108,160))) { // The user is not found! / Cannot find specified user. / You've already requested to follow XXX
		array_push($export['errors'], [
			'errorCode' => $errors->errors[0]->code,
			'errorMessage' => $errors->errors[0]->message,
			'errorLevel' => $level.'.1.9'
		]);
	} else {
		array_push($export['errors'], [
			'errorCode' => $errors->errors[0]->code,
			'errorMessage' => $errors->errors[0]->message,
			'errorLevel' => $level.'.1.z'
		]);
	}
} elseif ($connection->getLastHttpCode() != 200) { // x.2
	if ($connection->getLastHttpCode() == 400) { // Bad Request.
		array_push($export['errors'], [
			'errorCode' => $connection->getLastHttpCode(),
			'errorMessage' => 'HTTP Error: Bad Request',
			'errorLevel' => $level.'.2.1'
		]);
	} elseif ($connection->getLastHttpCode() == 401) { // Unauthorized.
		array_push($export['errors'], [
			'errorCode' => $connection->getLastHttpCode(),
			'errorMessage' => 'HTTP Error: Unauthorized',
			'errorLevel' => $level.'.2.2'
		]);
	} elseif ($connection->getLastHttpCode() == 403) { // Forbidden.
		array_push($export['errors'], [
			'errorCode' => $connection->getLastHttpCode(),
			'errorMessage' => 'HTTP Error: Forbidden',
			'errorLevel' => $level.'.2.3'
		]);
	} elseif ($connection->getLastHttpCode() == 404) { // Not Found.
		array_push($export['errors'], [
			'errorCode' => $connection->getLastHttpCode(),
			'errorMessage' => 'HTTP Error: Not Found',
			'errorLevel' => $level.'.2.4'
		]);
	} elseif ($connection->getLastHttpCode() == 500) { // Internal Server Error. Something is broken. Please post to the developer forums with additional details of your request, in case others are having similar issues.
		array_push($export['errors'], [
			'errorCode' => $connection->getLastHttpCode(),
			'errorMessage' => 'HTTP Error: The Twitter servers are up, but overloaded with requests. Try again later.',
			'errorLevel' => $level.'.2.5'
		]);
	} elseif ($connection->getLastHttpCode() == 503) { // The Twitter servers are up, but overloaded with requests. Try again later.
		array_push($export['errors'], [
			'errorCode' => $connection->getLastHttpCode(),
			'errorMessage' => 'HTTP Error: The Twitter servers are up, but overloaded with requests. Try again later.',
			'errorLevel' => $level.'.2.6'
		]);
	} else {
		array_push($export['errors'], [
			'errorCode' => $connection->getLastHttpCode(),
			'errorMessage' => 'HTTP Error: Other',
			'errorLevel' => $level.'.2.z'
		]);
	}
} else { // 6.3
	array_push($export['errors'], [
		'errorCode' => '888',
		'errorMessage' => 'Impossible error',
		'errorLevel' => $level.'.z'
	]);
}
