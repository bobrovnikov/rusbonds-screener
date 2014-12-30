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
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Bonds</title>
</head>
<body>
	<table>
		<thead>
			<tr>
				<th>Наименование</th>
				<th>Ссылка</th>
				<th>ISIN</th>
				<th>Размер купона</th>
				<th>Относительная цена</th>
				<th>Эффективность к погашению</th>
			</tr>
		</thead>
		<tbody>
			<?php while ($bond = mysql_fetch_array($all_bonds)): ?>
			<tr>
				<td><?php echo $bond['name'] ?></td>
				<td><a href="<?php echo $bond['link'] ?>" target="_blank"><?php echo $bond['link'] ?></a></td>
				<td><?php echo $bond['isin_code'] ?></td>
				<td><?php echo $bond['coupon_amount'] ?></td>
				<td><?php echo $bond['price_relative'] ?></td>
				<td><?php echo $bond['effective'] ?></td>
			</tr>
			<?php endwhile ?>
		</tbody>
	</table>
</body>


