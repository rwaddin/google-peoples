<?php
require __DIR__ . '/../vendor/autoload.php';

use Addin\GoogleContactsManager;

$credentialsPath = __DIR__ . '/../credentials.json';
$tokenPath = __DIR__ . '/../token.json';

$manager = new GoogleContactsManager($credentialsPath, $tokenPath);

$groups = $manager->getService()->contactGroups->listContactGroups();

foreach ($groups->getContactGroups() as $group) {
    echo $group->getName() . ' - ' . $group->getResourceName() . PHP_EOL;
}
