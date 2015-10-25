<?php

namespace AgentPlus\Component\ModelTransformer;

use AgentPlus\Entity\Client\Client;
use AgentPlus\Model\Client\Client as ClientModel;
use FiveLab\Component\ModelTransformer\ContextInterface;
use FiveLab\Component\ModelTransformer\Exception\TransformationFailedException;
use FiveLab\Component\ModelTransformer\ModelTransformerInterface;
use FiveLab\Component\ModelTransformer\ModelTransformerManagerAwareInterface;
use FiveLab\Component\ModelTransformer\ModelTransformerManagerInterface;
use FiveLab\Component\Reflection\Reflection;
use Symfony\Component\Intl\Intl;

class ClientModelTransformer implements ModelTransformerInterface, ModelTransformerManagerAwareInterface
{
    /**
     * @var ModelTransformerManagerInterface
     */
    private $modelTransformer;

    /**
     * {@inheritDoc}
     */
    public function setModelTransformerManager(ModelTransformerManagerInterface $manager)
    {
        $this->modelTransformer = $manager;
    }

    /**
     * {@inheritDoc}
     */
    public function transform($object, ContextInterface $context)
    {
        if (!$object instanceof Client) {
            throw TransformationFailedException::unexpected($object, Client::class);
        }

        $regionBundle = Intl::getRegionBundle();
        if ($object->getCountry()) {
            $countryName = $regionBundle->getCountryName($object->getCountry());
        } else {
            $countryName = null;
        }

        $client = new ClientModel();

        Reflection::setPropertiesValue($client, [
            'id' => $object->getId(),
            'createdAt' => $object->getCreatedAt(),
            'updatedAt' => $object->getUpdatedAt(),
            'name' => $object->getName(),
            'countryCode' => $object->getCountry(),
            'countryName' => $countryName,
            'city' => $object->getCity(),
            'address' => $object->getAddress(),
            'notes' => $object->getNotes(),
            'phones' => $object->getPhones(),
            'emails' => $object->getEmails(),
            'invoices' => $this->modelTransformer->transform($object->getInvoices())
        ]);

        return $client;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return is_a($class, Client::class, true);
    }
}
