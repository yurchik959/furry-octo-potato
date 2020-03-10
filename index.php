<?php
header('Content-Type: text/html; charset=utf-8');

require_once 'StringParser.php';
require_once 'DBConnector.php';


$string = '{Пожалуйста,|Просто|Если сможете,} сделайте так, чтобы это {удивительное|крутое|простое|важное|бесполезное} тестовое предложение {изменялось {быстро|мгновенно|оперативно|правильно} случайным образом|менялось каждый раз}';

$array = StringParser::to_array($string);

$mysqli = new DBConnector();
$mysqli->insert_array($array);

?>
