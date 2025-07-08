<?php
require __DIR__ . '/init.php';

$contacts = $manager->listContacts();
echo json_encode($contacts, JSON_PRETTY_PRINT);
