<?php
require __DIR__ . '/init.php';

$resourceName = "people/xxx"; // ganti dengan yang valid
$newName = "John Updated";
$newPhone = "+6281230000000";
$newEmail = "john.updated@example.com";

$contact = $manager->updateContact($resourceName, $newName, $newPhone, $newEmail);
echo "Contact updated: " . $contact->getResourceName();
