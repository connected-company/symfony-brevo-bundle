<?php

namespace Connected\BrevoBundle\Service;

use Exception;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BrevoClient implements BrevoClientInterface
{
    /**
     * @var string
     */
    protected $apiUrl;

    /**
     * @var string
     */
    protected $apiKey;

    public function __construct(
        string $apiUrl,
        string $apiKey,
        private HttpClientInterface $client,
    ) {
        $this->apiUrl = $apiUrl;
        $this->apiKey = $apiKey;
        $this->client = $this->client->withOptions([
                'base_uri' => $this->apiUrl,
                'headers' => [
                    'api-key' => $this->apiKey,
                    'content-type ' => 'application/json',
                ],
            ]
        );
    }

    /**
     * Crée un nouveau contact dans la liste des contacts de brevo
     *
     * @param string $contactEmail
     * @param array $attributes
     * @param array $listIds
     *
     * @return array|null
     */
    public function createContact(string $contactEmail, array $attributes, array $listIds): ?array
    {
        $createContact = new \Brevo\Client\Model\CreateContact([
            'email'             => $contactEmail,
            'attributes'        => $attributes,
            'listIds'           => $listIds,
            'emailBlacklisted'  => false,
            'smsBlacklisted'    => false,
            'updateEnabled'     => true
        ]);

        try {
            $response = $this->client->request('POST', $this->apiUrl . 'contacts', ['json' => json_decode($createContact, false, 512, JSON_THROW_ON_ERROR)]);

            if (is_null($response->getContent())) {
                return null;
            }

            return json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $e) {
            throw new BadRequestException('Erreur lors de la création d\'un contact: ' . $e->getMessage());
        }
    }

    /**
     * Récupère le detail d'un contact depuis brevo
     *
     * @param string $identifier
     *
     * @return array|null
     */
    public function getContactInfo(string $identifier): ?array
    {
        try {
            $response = $this->client->request('GET', $this->apiUrl . 'contacts/' . $identifier);

            if (is_null($response->getContent())) {
                return null;
            }

            return json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $e) {
            throw new BadRequestException('Erreur lors de la récupération d\'un contact: ' . $e->getMessage());
        }
    }
}
