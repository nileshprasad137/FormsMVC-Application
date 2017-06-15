<?php
require_once("interface/globals.php");
require_once("library/classes/Controller.class.php");

$controller = new Controller();
//print_r($_GET);
//echo "<hr>";
echo $controller->act($_GET);

?>
