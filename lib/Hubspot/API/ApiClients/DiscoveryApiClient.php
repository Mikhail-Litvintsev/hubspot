<?php

namespace UseDesk\Hubspot\API\ApiClients;

use App\Exceptions\Integrations\Hubspot\HubspotApiException;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use HubSpot\Client\Crm\Associations\Model\BatchInputPublicObjectId;
use HubSpot\Client\Crm\Associations\Model\PublicObjectId;
use HubSpot\Client\Crm\Contacts\ApiException;
use HubSpot\Client\Crm\Contacts\Model\Error;
use HubSpot\Client\Crm\Contacts\Model\Filter;
use HubSpot\Client\Crm\Contacts\Model\FilterGroup;
use HubSpot\Client\Crm\Contacts\Model\PublicObjectSearchRequest;
use HubSpot\Client\Crm\Contacts\Model\SimplePublicObjectInput;
use HubSpot\Client\Crm\Deals\Model\AssociationSpec;
use HubSpot\Client\Crm\Deals\Model\PublicAssociationsForObject;
use HubSpot\Client\Crm\Deals\Model\SimplePublicObjectInputForCreate;
use HubSpot\Delay;
use HubSpot\Discovery\Discovery;
use HubSpot\Factory;
use HubSpot\RetryMiddlewareFactory;
use UseDesk\Hubspot\API\DTO\ContactDTO;
use UseDesk\Hubspot\API\DTO\DTOFactory;
use UseDesk\Hubspot\API\DTO\DealDTO;
use UseDesk\Hubspot\API\DTO\NoteDTO;
use UseDesk\Hubspot\API\DTO\OwnerDTO;
use UseDesk\Hubspot\API\DTO\PipelineDTO;
use UseDesk\Hubspot\Book\Book;
use UseDesk\Hubspot\Book\ObjectTypeIdBook;

class DiscoveryApiClient implements ApiClientInterface
{
    protected Discovery $hubspot;

    protected DTOFactory $DTOFactory;

    /**
     * @param string $token
     */
    public function __construct(protected string $token)
    {
        $this->hubspot = $this->getDiscoveryClient($token);
        $this->DTOFactory = app(DTOFactory::class);
    }

    /** Возвращает Hubspot SDK(Client) */
    private function getDiscoveryClient(string $token): Discovery
    {
        $handlerStack = HandlerStack::create();
        $handlerStack->push(
            RetryMiddlewareFactory::createRateLimitMiddleware(
                Delay::getConstantDelayFunction()
            )
        );

        $handlerStack->push(
            RetryMiddlewareFactory::createInternalErrorsMiddleware(
                Delay::getExponentialDelayFunction(2)
            )
        );

        $client = new Client(['handler' => $handlerStack]);
        return Factory::createWithAccessToken($token, $client);
    }

    /**
     * Ищет контакты по email и phones в Hubspot
     *
     * @return ContactDTO[]
     * @throws HubspotApiException
     */
    public function searchContacts(array $emails, array $phones = []): array
    {
        try {
            $all = [];
            foreach ($emails as $email) {
                $response = $this->searchContactsByEmail($email);
                if (isset($response[0])) {
                    $all = array_merge($all, $response);
                }
            }
            foreach ($phones as $phone) {
                $phone = $this->preparePhone($phone);
                $response = $this->searchContactsByPhone($phone);
                if (isset($response[0])) {
                    $all = array_merge($all, $response);
                }
            }
            $result = [];
            foreach ($all as $item) {
                $result[$item->hs_object_id] = $item;
            }
            return $result;
        } catch (ApiException $apiException) {
            $error = [
                'message' => $apiException->getMessage(),
                'responseBody' => $apiException->getResponseBody()
            ];
        }
        throw new HubspotApiException(json_encode($error));
    }

    /**
     * Ищет контакты по email в Hubspot
     *
     * @return ContactDTO[]
     * @throws HubspotApiException
     */
    public function searchContactsByEmail(string $email): array
    {
        try {
            $filter = new Filter();
            $filter
                ->setOperator('EQ')
                ->setPropertyName('email')
                ->setValue($email);

            return $this->searchRequestContacts([$filter]);
        } catch (ApiException $apiException) {
            $error = [
                'message' => $apiException->getMessage(),
                'responseBody' => $apiException->getResponseBody()
            ];
        }
        throw new HubspotApiException(json_encode($error));
    }

    /**
     * Ищет контакты по phone в Hubspot
     *
     * @return ContactDTO[]
     * @throws HubspotApiException
     */
    public function searchContactsByPhone(string $phone): array
    {
        try {
            $filter = new Filter();
            $filter
                ->setOperator('EQ')
                ->setPropertyName('phone')
                ->setValue($phone);

            return $this->searchRequestContacts([$filter]);
        } catch (ApiException $apiException) {
            $error = [
                'message' => $apiException->getMessage(),
                'responseBody' => $apiException->getResponseBody()
            ];
        }
        throw new HubspotApiException(json_encode($error));
    }

    /**
     * Поиск контактов по фильтрам в Hubspot
     *
     * @return ContactDTO[]
     * @throws HubspotApiException
     */
    protected function searchRequestContacts(array $filters): array
    {
        try {
            $filterGroup = new FilterGroup();
            $filterGroup->setFilters($filters);

            $searchRequest = new PublicObjectSearchRequest();
            $searchRequest->setFilterGroups([$filterGroup]);

            $searchRequest->setProperties(array_keys(get_class_vars(ContactDTO::class)));

            $contacts = $this->hubspot->crm()->contacts()->searchApi()->doSearch($searchRequest)->getResults();
            $result = [];
            foreach ($contacts as $contact) {
                $result[] = $this->DTOFactory->create(ContactDTO::class, $contact->getProperties());
            }
            return $result;
        } catch (ApiException $apiException) {
            $error = [
                'message' => $apiException->getMessage(),
                'responseBody' => $apiException->getResponseBody()
            ];
        } catch (Exception $exception) {
            $error = [
                'message' => $exception->getMessage(),
            ];
        }
        throw new HubspotApiException(json_encode($error));
    }

    /**
     * Поиск контакта по id в Hubspot
     *
     * @param int $hs_contact_id
     * @return ContactDTO
     * @throws HubspotApiException
     */
    public function getContactById(int $hs_contact_id): ContactDTO
    {
        try {
            $apiResponse = $this->hubspot->crm()->contacts()->basicApi()->getById($hs_contact_id, false);
            return $this->DTOFactory->create(ContactDTO::class, $apiResponse->getProperties());
        } catch (ApiException  $apiException) {
            $error = [
                'message' => $apiException->getMessage(),
                'responseBody' => $apiException->getResponseBody()
            ];
        }
        throw new HubspotApiException(json_encode($error));
    }

    /**
     * Исправление номера телефона под формат Hubspot, пример +75556667788
     *
     * @param string $phone
     * @return string
     */
    protected function preparePhone(string $phone): string
    {
        return '+' . preg_replace('/[^0-9]/', '', $phone);
    }

    /**
     * Создание контакта в Hubspot
     *
     * @param ContactDTO $contact
     * @return ContactDTO
     * @throws HubspotApiException
     */
    public function createContact(ContactDTO $contact): ContactDTO
    {
        try {
            $contactInput = new SimplePublicObjectInput();
            $contactInput->setProperties((array)$contact);
            $contact = $this->hubspot->crm()->contacts()->basicApi()->create($contactInput);
            if ($contact instanceof Error) {
                $error = [
                    'message' => $contact->getMessage(),
                    'errors' => $contact->getErrors()
                ];
            } else {
                return $this->DTOFactory->create(ContactDTO::class, $contact->getProperties());
            }
        } catch (ApiException $apiException) {
            $error = [
                'message' => $apiException->getMessage(),
                'responseBody' => $apiException->getResponseBody()
            ];
        } catch (Exception $e) {
            $error = [
                'message' => $e->getMessage(),
            ];
        }

        throw new HubspotApiException(json_encode($error));
    }

    /**
     * Получение всех сделок, связанных с контактом
     *
     * @param int $hs_contact_id
     * @return DealDTO []
     * @throws HubspotApiException
     */
    public function getDealsFromContactId(int $hs_contact_id = 0): array
    {
        $result = [];
        foreach ($this->getDealsIdFromContactId($hs_contact_id) as $hsDeaId) {
            $result[] = $this->getDeal($hsDeaId);
        }
        return $result;
    }

    /**
     * Получение всех id сделок, связанных с контактом
     *
     * @param int $hs_contact_id
     * @return array
     * @throws HubspotApiException
     */
    protected function getDealsIdFromContactId(int $hs_contact_id): array
    {
        try {
            $response = $this->hubspot->crm()->associations()->v4()->basicApi()->getPage(
                Book::CONTACT,
                $hs_contact_id,
                ObjectTypeIdBook::DEALS
            );
            return array_map(function ($item) {
                return $item->getToObjectId();
            }, $response->getResults());
        } catch (\HubSpot\Client\Crm\Associations\V4\ApiException $apiException) {
            $error = [
                'message' => $apiException->getMessage(),
                'responseBody' => $apiException->getResponseBody()
            ];
        }
        throw new HubspotApiException(json_encode($error));
    }

    /**
     * Поиск сделки по id в Hubspot
     *
     * @param int $hs_deal_id
     * @return DealDTO
     * @throws HubspotApiException
     */
    public function getDeal(int $hs_deal_id): DealDTO
    {
        try {
            $filter = new \HubSpot\Client\Crm\Deals\Model\Filter();
            $filter
                ->setOperator('EQ')
                ->setPropertyName('hs_object_id')
                ->setValue($hs_deal_id);
            $filters[] = $filter;
            $filterGroup = new \HubSpot\Client\Crm\Deals\Model\FilterGroup();
            $filterGroup->setFilters($filters);

            $searchRequest = new \HubSpot\Client\Crm\Deals\Model\PublicObjectSearchRequest();
            $searchRequest->setFilterGroups([$filterGroup]);

            $searchRequest->setProperties(array_keys(get_class_vars(DealDTO::class)));
            $results = $this->hubspot->crm()->deals()->searchApi()->doSearch($searchRequest)->getResults();
            if (empty($results)) {
                throw new HubspotApiException("Can not to find deal id $hs_deal_id");
            }
            $properties = $results[0]->getProperties();
            return $this->DTOFactory->create(DealDTO::class, $properties);
        } catch (\HubSpot\Client\Crm\Deals\ApiException $apiException) {
            $error = [
                'message' => $apiException->getMessage(),
                'responseBody' => $apiException->getResponseBody()
            ];
        } catch (Exception $e) {
            $error = [
                'message' => $e->getMessage(),
            ];
        }

        throw new HubspotApiException(json_encode($error));
    }

    /**
     * Получить список всех owners в Hubspot
     *
     * @return OwnerDTO []
     * @throws HubspotApiException
     */
    public function getOwners(): array
    {
        try {
        $owners = $this->hubspot->crm()->owners()->ownersApi()->getPage()->getResults();
        $result = [];

        foreach ($owners as $owner) {
            $result[] = $this->DTOFactory->create(OwnerDTO::class, (array)$owner->jsonSerialize());
        }
        return $result;
        } catch (\HubSpot\Client\Crm\Owners\ApiException $apiException) {
            $error = [
                'message' => $apiException->getMessage(),
                'responseBody' => $apiException->getResponseBody()
            ];
        } catch (Exception $e) {
            $error = [
                'message' => $e->getMessage(),
            ];
        }

        throw new HubspotApiException(json_encode($error));
    }

    /**
     * Получить список всех Deal Pipelines в аккаунте Hubspot
     *
     * @return PipelineDTO []
     * @throws HubspotApiException
     */
    public function getDealPipelines(): array
    {
        try {
            $client = new Client();
            $response = $client->get('https://api.hubapi.com/crm-pipelines/v1/pipelines/deals', [
                'headers' => [
                    'Authorization' => "Bearer " . $this->token
                ]
            ]);
            $pipelines = json_decode($response->getBody()->getContents(), true)['results'];
            $result = [];
            foreach ($pipelines as $pipeline) {
                $result[] = new PipelineDTO(...$pipeline);
            }
            return $result;
        } catch (Exception|GuzzleException $exception) {
            $error = [
                'message' => $exception->getMessage(),
            ];
        }
        throw new HubspotApiException(json_encode($error));
    }

    /**
     * Создание сделки с привязкой к контакту
     *
     * @param DealDTO $dealDTO
     * @param int $hs_contact_id
     * @return DealDTO
     * @throws HubspotApiException
     */
    public function createDeal(DealDTO $dealDTO, int $hs_contact_id): DealDTO
    {
        try {
            $properties = array_filter((array)$dealDTO);
            $relatedContactId = new \HubSpot\Client\Crm\Deals\Model\PublicObjectId([
                'id' => $hs_contact_id
            ]);
            $associationSpec = new AssociationSpec([
                'association_category' => 'HUBSPOT_DEFINED',
                'association_type_id' => 3
            ]);
            $publicAssociationsForObject1 = new PublicAssociationsForObject([
                'to' => $relatedContactId,
                'types' => [$associationSpec]
            ]);
            $simplePublicObjectInputForCreate = new SimplePublicObjectInputForCreate([
                'properties' => $properties,
                'associations' => [$publicAssociationsForObject1],
            ]);
            $deal = $this->hubspot->crm()->deals()->basicApi()->create($simplePublicObjectInputForCreate);
            return $this->DTOFactory->create(DealDTO::class, $deal->getProperties());
        } catch (\HubSpot\Client\Crm\Deals\ApiException|Exception $apiException) {
            $error = [
                'message' => $apiException->getMessage(),
                'responseBody' => $apiException->getResponseBody()
            ];
        }
        throw new HubspotApiException(json_encode($error));
    }

    /**
     * Получить заметки, связанные со сделкой
     *
     * @param int $hs_deal_id
     * @param string|null $sort
     * @return NoteDTO []
     * @throws HubspotApiException
     */
    public function getNotes(int $hs_deal_id, ?string $sort = null): array
    {
        try {
            $ids = $this->getNoteIdsFromDealId($hs_deal_id);
            if (empty($ids)) {
                return [];
            }
            $properties = [
                'hs_note_body',
                'hs_lastmodifieddate',
                'hs_object_id',
                'hs_attachment_ids',
                'hubspot_deal_id',
                'hubspot_owner_id'
            ];

            $filter = new \HubSpot\Client\Crm\Objects\Notes\Model\Filter([
                'values' => $ids,
                'property_name' => 'hs_object_id',
                'operator' => 'IN'
            ]);
            $filterGroup = new \HubSpot\Client\Crm\Objects\Notes\Model\FilterGroup([
                'filters' => [$filter]
            ]);
            $publicObjectSearchRequest = new \HubSpot\Client\Crm\Objects\Notes\Model\PublicObjectSearchRequest([
                'filter_groups' => [$filterGroup],
                'properties' => $properties,
                'limit' => 5,
                'after' => 0,
            ]);
            $apiResponse = $this->hubspot->crm()->objects()->notes()->searchApi()->doSearch(
                $publicObjectSearchRequest
            )->getResults();
            $notes = [];
            foreach ($apiResponse as $result) {
                $notes[] = $this->DTOFactory->create(NoteDTO::class, $result->getProperties());
            }
            return $notes;
        } catch (\HubSpot\Client\Crm\Objects\Notes\ApiException $apiException) {
            $error = [
                'message' => $apiException->getMessage(),
                'responseBody' => $apiException->getResponseBody()
            ];
        } catch (Exception $e) {
            $error = [
                'message' => $e->getMessage(),
            ];
        }
        throw new HubspotApiException(json_encode($error));
    }

    /**
     * Получить список id заметок, связанных со сделкой
     *
     * @param int $hs_deal_id
     * @return array
     * @throws HubspotApiException
     */
    protected function getNoteIdsFromDealId(int $hs_deal_id): array
    {
        try {
            $relatedDealId = new PublicObjectId([
                'id' => $hs_deal_id
            ]);
            $batchInputPublicObjectId = new BatchInputPublicObjectId([
                'inputs' => [$relatedDealId],
            ]);
            $apiResponse = $this->hubspot->crm()->associations()->batchApi()->read(
                ObjectTypeIdBook::DEALS,
                ObjectTypeIdBook::NOTES,
                $batchInputPublicObjectId
            )->getResults();
            if (empty($apiResponse)) {
                return [];
            }
            $associations = $apiResponse[0]->getTo();
            return array_map(function ($assoc) {
                return $assoc->getId();
            }, $associations);
        } catch (\HubSpot\Client\Crm\Associations\ApiException $apiException) {
            $error = [
                'message' => $apiException->getMessage(),
                'responseBody' => $apiException->getResponseBody()
            ];
        }
        throw new HubspotApiException(json_encode($error));
    }
}