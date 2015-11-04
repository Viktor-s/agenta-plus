<?php

namespace AgentPlus\Entity\Diary;

use Doctrine\ORM\Mapping as ORM;
use FiveLab\Component\ModelTransformer\Annotation as ModelTransform;

/**
 * Diary attachment
 *
 * @ORM\Entity
 * @ORM\Table(
 *      name="diary_attachment",
 *      indexes={
 *          @ORM\Index(name="diary_attachment_diary_idx", columns={"diary_id"})
 *      }
 * )
 *
 * @ModelTransform\Object(transformedClass="AgentPlus\Model\Attachment")
 */
class Attachment
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
     * @var Diary
     *
     * @ORM\ManyToOne(targetEntity="AgentPlus\Entity\Diary\Diary", inversedBy="attachments")
     * @ORM\JoinColumn(name="diary_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $diary;

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
