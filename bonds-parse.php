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
$bonds = array();

while ($arr = mysql_fetch_array($all_bonds)) {
    $bond_page = $arr['html'];
	$this_bond = array(
		'isin_code' => '',
		'coupon_date' => '',
		'coupon_frequency' => '',
		'coupon_amount' => '',
		'price_relative' => '',
		'week_volume' => '',
		'effective' => '',
	);
	
	// ISIN код:</td><td>RU000A0JRNJ3</td>
	preg_match('/ISIN код:<\/td><td>(.{12})<\/td>/', $bond_page, $isin_matches);
	if (isset($isin_matches[1])) {
		$this_bond['isin_code'] = $isin_matches[1];
	}
	
	// Дата выплаты купона:</td><td>30.07.2014</td>
	preg_match('/Дата выплаты купона:<\/td><td>((\d+).(\d+).(\d+))<\/td>/', $bond_page, $coupon_matches);
	if (isset($coupon_matches[1])) {
		$this_bond['coupon_date'] = strtotime($coupon_matches[1]);
	}
	
	// Периодичность выплат в год:</td><td>2</td>
	preg_match('/Периодичность выплат в год:<\/td><td>(\d+)<\/td>/', $bond_page, $frequency_match);
	if (isset($frequency_match[1])) {
		$this_bond['coupon_frequency'] = $frequency_match[1];
	}
	
	// Размер купона, % годовых:</td><td>8</td>
	preg_match('/Размер купона, % годовых:<\/td><td>(((\d+),?(\d)+)?)/', $bond_page, $yield_match);
	if (isset($yield_match[1])) {
		$this_bond['coupon_amount'] = str_replace(',', '.', $yield_match[1]);	
	}
	
	// <td>Цена срвзв. чистая, % от номинала:</td><td>99,997 (<span style=color:green>+0,0070</span>)<span style=color:red>*</span></td>
	preg_match('/Цена срвзв. чистая, % от номинала:<\/td><td>([^\s]*)/', $bond_page, $price_match);
	if (isset($price_match[1])) {
	    $this_bond['price_relative'] = str_replace(',', '.', $price_match[1]);
	}
	
	// Объем торгов за неделю:</td><td>106 989 300&nbsp;RUB</td>
	preg_match('/Объем торгов за неделю:<\/td><td>(.*)RUB<\/td>/', $bond_page, $volumes_match);
	if (isset($volumes_match[1])) {
		$volume = $volumes_match[1];
		$volume = preg_replace("/[^0-9]/","",$volume);
		$this_bond['week_volume'] = $volume;
	}
	
	// Доходность к оферте
	preg_match('/эффект\., % годовых(<span style=color:red> \*\* <\/span>)?:<\/td><td>([^\s]*)(.*)<\/td>/', $bond_page, $effective_matches);
	if (isset($effective_matches[2])) {
	    $this_bond['effective'] = str_replace(',', '.', $effective_matches[2]);
	}
	
	mysql_query("UPDATE tbl_search_results SET 
	isin_code = '$this_bond[isin_code]',
	coupon_date = '$this_bond[coupon_date]',
	coupon_frequency = '$this_bond[coupon_frequency]',
	coupon_amount = '$this_bond[coupon_amount]',
	price_relative = '$this_bond[price_relative]',
	week_volume = '$this_bond[week_volume]',
	effective = '$this_bond[effective]'
	WHERE id = '$arr[id]' LIMIT 1");
	
	//$bonds[] = $this_bond;
}

var_dump($bonds);
