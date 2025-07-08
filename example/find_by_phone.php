<?php
require __DIR__ . '/init.php';

$phone = "+6281234567890";
$contact = $manager->findContactByPhone($phone);

echo $contact ? json_encode($contact, JSON_PRETTY_PRINT) : "Contact not found.";
