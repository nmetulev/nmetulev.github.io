<?php
$result = '';
foreach ($_GET as $key => $value){
	$result .= $key . '=' . $value . '&';
}
$result = rtrim($result, '&');
echo $result;
?>