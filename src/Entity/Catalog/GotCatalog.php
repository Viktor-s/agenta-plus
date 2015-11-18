<?php

namespace AgentPlus\Entity\Catalog;

use AgentPlus\Entity\Client\Client;
use AgentPlus\Entity\Diary\Diary;
use Doctrine\ORM\Mapping as ORM;
use FiveLab\Component\ModelTransformer\Annotation as ModelTransform;

/**
 * @ORM\Entity()
 * @ORM\Table(
 *      name="got_catalog",
 *
 *      indexes={
 *          @ORM\Index(name="got_catalog_diary_idx", columns={"diary_id"}),
 *          @ORM\Index(name="got_catalog_catalog_idx", columns={"catalog_id"}),
 *          @ORM\Index(name="got_catalog_client_idx", columns={"client_id"})
 *      }
 * )
 *
 * @ModelTransform\Object(transformedClass="AgentPlus\Model\Catalog\GotCatalog")
 */
class GotCatalog
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     *
     * @ModelTransform\Property()
     */
    private $createdAt;

    /**
     * @var Catalog
     *
     * @ORM\ManyToOne(targetEntity="AgentPlus\Entity\Catalog\Catalog")
     * @ORM\JoinColumn(name="catalog_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     *
     * @ModelTransform\Property(shouldTransform=true)
     */
    private $catalog;

    /**
     * @var Diary
     *
     * @ORM\ManyToOne(targetEntity="AgentPlus\Entity\Diary\Diary")
     * @ORM\JoinColumn(name="diary_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     *
     * @ModelTransform\Property(shouldTransform=true)
     */
    private $diary;

    /**
     * @var Client
     *
     * @ORM\ManyToOne(targetEntity="AgentPlus\Entity\Client\Client")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     *
     * @ModelTransform\Property(shouldTransform=true)
     */
    private $client;

    /**
     * Construct
     *
     * @param Catalog $catalog
     * @param Diary   $diary
     * @param Client  $client
     */
    public function __construct(Catalog $catalog, Diary $diary, Client $client = null)
    {
        if (!$client) {
            $client = $diary->getClient();
        }

        if ($client && $diary->getClient() && $client->getId() != $diary->getClient()->getId()) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid client. The client "%s [%d]" not equals for diary client "%s [%d]".',
                $client->getName(),
                $client->getId(),
                $diary->getClient()->getName(),
                $diary->getClient()->getId()
            ));
        }

        $this->createdAt = new \DateTime();
        $this->catalog = $catalog;
        $this->diary = $diary;
        $this->client = $client;
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get created at
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get catalog
     *
     * @return Catalog
     */
    public function getCatalog()
    {
        return $this->catalog;
    }

    /**
     * Get diary
     *
     * @return Diary
     */
    public function getDiary()
    {
        return $this->diary;
    }

    /**
     * Get client
     *
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }
}
