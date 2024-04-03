<?php

/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

$req_url = '/';

if (isset($_SERVER['REQUEST_URI'])) {
    $req_url = $_SERVER['REQUEST_URI'];
};

if (isset($_REQUEST['url'])) {
    $req_url = '/' . $_REQUEST['url'];
};

$req_url_split_qs = explode('?', $req_url);
$req_url_split = explode('/', $req_url_split_qs[0]);
$req_url_query = $req_url_split_qs[1] ?? '';
unset($req_url_split_qs);

if (!isset($req_url_split[1]) || $req_url_split[1] == '') {
	include 'page.html';
	die();
};



function base64url_decode($data, $strict = false)
{
  // Convert Base64URL to Base64 by replacing “-” with “+” and “_” with “/”
  $b64 = strtr($data, '-_', '+/');

  // Decode Base64 string and return the original data
  return base64_decode($b64, $strict);
}
/*
function obf($input) {
	$prev = 0;
	$out = '';
	$len = strlen($input);
	for ($i = 0; $i < $len; $i++) {
		$val = ord(substr($input, $i, 1));
		$val = $val > $prev ? $val - $prev + 256 : $val - $prev;
		$prev += $val;
    	$prev %= 256;
		$out .= chr($val);
	};
	return $out;
};
*/
function deobf($input) {
	$prev = 0;
	$out = '';
	$len = strlen($input);
	for ($i = 0; $i < $len; $i++) {
		$val = ord(substr($input, $i, 1)) + $prev;
		$prev = $val % 256;
		$out .= chr($val);
	};
	return $out;
};

function l($url) {
	$https = !str_starts_with($url, '.');
	$www = str_ends_with($url, '.');
	$real_url = ($https ? $url : substr($url, 1, strlen($url) - 1));
	$real_url = ($www ? substr($real_url, 0, strlen($real_url) - 1) : $real_url);
	return ($https ? 'https://' : 'http://') . ($www ? 'www.' : '') . str_replace(['\n', '\r', ';', '\\', ':'], 'leo', deobf(base64url_decode($real_url)));
};

array_shift($req_url_split);

$random = !!(count($req_url_split) - 1);
if ($random) $loc = l($req_url_split[array_rand($req_url_split)]);
else $loc = l($req_url_split[0]);

header('Referrer-Policy: unsafe-url');
header('Location: ' . $loc, true, $random ? 307 : 301);
die();
