<?php

namespace Addin;

use Google_Client;
use Google_Service_PeopleService;
use Google_Service_PeopleService_Person;
use Google_Service_PeopleService_Name;
use Google_Service_PeopleService_EmailAddress;
use Google_Service_PeopleService_PhoneNumber;

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

        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $this->client->setAccessToken($accessToken);
        }

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

    public function listContacts(int $limit = 100): array
    {
        $response = $this->service->people_connections->listPeopleConnections('people/me', [
            'pageSize' => $limit,
            'personFields' => 'names,emailAddresses,phoneNumbers,metadata',
        ]);

        $results = [];

        foreach ($response->getConnections() as $person) {
            $name = '[Tanpa Nama]';
            if (is_array($person->getNames()) && isset($person->getNames()[0])) {
                $name = $person->getNames()[0]->getDisplayName();
            }

            $email = null;
            if (is_array($person->getEmailAddresses()) && isset($person->getEmailAddresses()[0])) {
                $email = $person->getEmailAddresses()[0]->getValue();
            }

            $phone = null;
            if (is_array($person->getPhoneNumbers()) && isset($person->getPhoneNumbers()[0])) {
                $phone = $person->getPhoneNumbers()[0]->getValue();
            }

            $createTime = null;
            $updateTime = null;
            $sources = $person->getMetadata()->getSources();
            if (is_array($sources)) {
                foreach ($sources as $source) {
                    if ($source->getType() === 'CONTACT') {
                         if (method_exists($source, 'getCreateTime')) {
                            $createTime = $source->getCreateTime();
                        }
                        if (method_exists($source, 'getUpdateTime')) {
                            $updateTime = $source->getUpdateTime();
                        }
                        break;
                    }
                }
            }

            $results[] = [
                'resourceName' => $person->getResourceName(),
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'created_at' => $createTime,
                'updated_at' => $updateTime,
            ];
        }

        return $results;
    }

    public function addContact(string $name, string $phone, string $email = null): Google_Service_PeopleService_Person
    {
        $person = new Google_Service_PeopleService_Person();

        $nameObj = new Google_Service_PeopleService_Name();
        $nameObj->setGivenName($name);
        $person->setNames([$nameObj]);

        if ($email) {
            $emailObj = new Google_Service_PeopleService_EmailAddress();
            $emailObj->setValue($email);
            $person->setEmailAddresses([$emailObj]);
        }

        $phoneObj = new Google_Service_PeopleService_PhoneNumber();
        $phoneObj->setValue($phone);
        $person->setPhoneNumbers([$phoneObj]);

        return $this->service->people->createContact($person);
    }

    public function updateContact(string $resourceName, string $newName, string $newPhone, string $newEmail = null): Google_Service_PeopleService_Person
    {
        $person = new Google_Service_PeopleService_Person();

        $nameObj = new Google_Service_PeopleService_Name();
        $nameObj->setGivenName($newName);
        $person->setNames([$nameObj]);

        if ($newEmail) {
            $emailObj = new Google_Service_PeopleService_EmailAddress();
            $emailObj->setValue($newEmail);
            $person->setEmailAddresses([$emailObj]);
        }

        $phoneObj = new Google_Service_PeopleService_PhoneNumber();
        $phoneObj->setValue($newPhone);
        $person->setPhoneNumbers([$phoneObj]);

        return $this->service->people->updateContact($resourceName, $person, [
            'updatePersonFields' => 'names,emailAddresses,phoneNumbers'
        ]);
    }

    public function deleteContact(string $resourceName): void
    {
        $this->service->people->deleteContact($resourceName);
    }

    public function findContactByPhone(string $phone): ?array
    {
        $connections = $this->listContacts(2000);
        foreach ($connections as $person) {
            if ($person['phone'] === $phone) {
                return $person;
            }
        }
        return null;
    }

    public function getService(): Google_Service_PeopleService
    {
        return $this->service;
    }

}
