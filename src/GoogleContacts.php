<?php

namespace GoogleContacts;

use Google_Client;
use Google_Service_PeopleService;
use Google_Service_PeopleService_Person;
use Google_Service_PeopleService_Name;
use Google_Service_PeopleService_EmailAddress;

class GoogleContactsManager
{
    protected Google_Client $client;
    protected Google_Service_PeopleService $service;

    public function __construct(string $credentialsPath, string $tokenPath)
    {
        $this->client = new Google_Client();
        $this->client->setApplicationName("Google Contacts Manager");
        $this->client->setScopes(Google_Service_PeopleService::CONTACTS);
        $this->client->setAuthConfig($credentialsPath);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');

        // Load token
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $this->client->setAccessToken($accessToken);
        }

        // Refresh if expired
        if ($this->client->isAccessTokenExpired()) {
            $refreshToken = $this->client->getRefreshToken();
            if (!$refreshToken) {
                throw new \Exception("Refresh token not found. Please reauthorize.");
            }
            $newToken = $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
            if (isset($newToken['error'])) {
                throw new \Exception("Failed to refresh token: " . $newToken['error_description']);
            }
            $newToken['refresh_token'] = $refreshToken;
            file_put_contents($tokenPath, json_encode($newToken));
            $this->client->setAccessToken($newToken);
        }

        $this->service = new Google_Service_PeopleService($this->client);
    }

    public function listContacts(int $limit = 10): array
    {
        $response = $this->service->people_connections->listPeopleConnections('people/me', [
            'pageSize' => $limit,
            'personFields' => 'names,emailAddresses',
        ]);
        return $response->getConnections() ?? [];
    }

    public function addContact(string $name, string $email): Google_Service_PeopleService_Person
    {
        $person = new Google_Service_PeopleService_Person();

        $nameObj = new Google_Service_PeopleService_Name();
        $nameObj->setGivenName($name);
        $person->setNames([$nameObj]);

        $emailObj = new Google_Service_PeopleService_EmailAddress();
        $emailObj->setValue($email);
        $person->setEmailAddresses([$emailObj]);

        return $this->service->people->createContact($person);
    }

    public function updateContact(string $resourceName, string $newName, string $newEmail): Google_Service_PeopleService_Person
    {
        $person = new Google_Service_PeopleService_Person();

        $nameObj = new Google_Service_PeopleService_Name();
        $nameObj->setGivenName($newName);
        $person->setNames([$nameObj]);

        $emailObj = new Google_Service_PeopleService_EmailAddress();
        $emailObj->setValue($newEmail);
        $person->setEmailAddresses([$emailObj]);

        return $this->service->people->updateContact($resourceName, $person, [
            'updatePersonFields' => 'names,emailAddresses'
        ]);
    }

    public function deleteContact(string $resourceName): void
    {
        $this->service->people->deleteContact($resourceName);
    }
}

