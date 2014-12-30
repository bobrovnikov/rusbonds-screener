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

$all_bonds = mysql_query("SELECT * FROM tbl_search_results");

while ($arr = mysql_fetch_array($all_bonds)) {
    $html = mysql_real_escape_string(iconv('windows-1251', 'utf-8', file_get_contents($arr['link'])));
	mysql_query("UPDATE tbl_search_results SET html = '$html' WHERE id = $arr[id] LIMIT 1");
}
