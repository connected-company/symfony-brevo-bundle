<?php

namespace Connected\BrevoBundle\Service;

interface BrevoClientInterface
{
    public function createContact(string $contactEmail, array $attributes, array $listIds): ?array;
    public function getContactInfo(string $identifier): ?array;
}