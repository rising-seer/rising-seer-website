<?php
echo "Rewrite is working!\n";
echo "Query string: " . $_SERVER['QUERY_STRING'] . "\n";
echo "File parameter: " . (isset($_GET['file']) ? $_GET['file'] : 'not set') . "\n";
?>