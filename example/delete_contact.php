<?php
require __DIR__ . '/init.php';

$resourceName = "people/xxx"; // ganti dengan yang valid
$manager->deleteContact($resourceName);
echo "Contact deleted: $resourceName";
