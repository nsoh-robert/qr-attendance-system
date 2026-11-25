<?php
session_start();

$_SESSION["test"] = "Session is working";

echo "PHP SESSION IS WORKING";
echo "<br>";
echo $_SESSION["test"];
?>