<?php

namespace AgentPlus\Entity\Catalog;

use Doctrine\ORM\Mapping as ORM;
use FiveLab\Component\ModelTransformer\Annotation as ModelTransform;

/**
 * @ORM\Entity
 * @ORM\Table(
 *      name="catalog_image"
 * )
 *
 * @ModelTransform\Object(transformedClass="AgentPlus\Model\Attachment")
 */
class Image
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
     * @var Catalog
     *
     * @ORM\ManyToOne(targetEntity="AgentPlus\Entity\Catalog\Catalog", inversedBy="images")
     * @ORM\JoinColumn(name="catalog_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $catalog;

    /**
     * The path for file (from "/web")
     *
     * @var string
     *
     * @ORM\Column(name="path", type="string")
     *
     * @ModelTransform\Property()
     */
    private $path;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     *
     * @ModelTransform\Property()
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="size", type="bigint")
     *
     * @ModelTransform\Property()
     */
    private $size;

    /**
     * @var string
     *
     * @ORM\Column(name="mime_type")
     *
     * @ModelTransform\Property()
     */
    private $mimeType;

    /**
     * Construct
     *
     * @param string $path
     * @param string $name
     * @param string $size
     * @param string $mimeType
     */
    public function __construct($path, $name, $size, $mimeType)
    {
        $this->path = $path;
        $this->name = $name;
        $this->size = $size;
        $this->mimeType = $mimeType;
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
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get size
     *
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Get mime type
     *
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }
}
