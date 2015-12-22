<?php

function sha256Encode($str, $key) {
	return hash_hmac('sha256', $str, $key);
}

function base64Encode($str) {
	return base64_encode($str);
}

function base64Decode($str) {
	return base64_decode($str);
}

function debug($msg) {
	echo "<pre>";
	print_r($msg);
	echo "</pre>";
}

?>