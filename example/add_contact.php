<?php
require __DIR__ . '/init.php';

$name = "John Doe";
$phone = "+6281234567890";
$email = "hello@addin.web.id";

$contact = $manager->addContact($name, $phone, $email);
echo "Contact added: " . $contact->getResourceName();
