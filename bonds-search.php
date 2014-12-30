<?php
ini_set("display_errors", 1);
ini_set("track_errors", 1);
ini_set("html_errors", 1);
set_time_limit(0);
error_reporting(E_ALL);
header('Content-type: text/html; charset=utf-8');

require_once 'simple_html_dom.php';

$dblocation = "localhost";
$dbname = "bonds";
$dbuser = "root";
$dbpasswd = "";
$dbcnx = mysql_connect($dblocation,$dbuser,$dbpasswd);
if (!$dbcnx) exit('DB failure');
if (!@mysql_select_db($dbname,$dbcnx)) exit('DB failure');
mysql_query ("set collation_connection='utf8_general_ci'");

define('BASE_URL', 'http://rusbonds.ru');
$epog = date('d.m.Y', strtotime('today + 1 year'));
$search_page = '/srch_simple.asp?go=1&sec=6&status=T&pvt=2&blof=on&epog='.$epog;

if (!isset($_GET['pages'])) {
    exit('Go to <a target="_blank" href="'.BASE_URL . $search_page.'">search</a> and specify number of result pages via $_GET[pages]');
}
$results = array();
$pages = $_GET['pages'];

mysql_query("TRUNCATE tbl_search_results");

//$pages = 1; // tmp
for ($i = 1; $i <= $pages; $i++) {
    $search = file_get_html(BASE_URL . $search_page . '&p=' . $i);
	$j = 1;
	foreach ($search->find('.tbl_data tbody tr td a') as $result) { // this selector works bad
		if ($j <= 9) {
			$j++;
			continue;
		}
		$name = $result->plaintext;
		$link = BASE_URL . $result->href;
		mysql_query("INSERT INTO tbl_search_results (name, link) VALUES ('$name', '$link')");
	}
}

// assume we always have >9 pages of results
//preg_match_all('/<a href=\/([\S]{1,}) class=bl>\d+\.\.\.\d+<\/a>/', 'test', $pagination_matches);
