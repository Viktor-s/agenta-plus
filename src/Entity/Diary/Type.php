<?php

namespace AgentPlus\Entity\Diary;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *      name="diary_type"
 * )
 */
class Type
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
     * @var Type
     *
     * @ORM\ManyToOne(targetEntity="AgentPlus\Entity\Diary\Type", inversedBy="child")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    private $parent;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position = 0;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="AgentPlus\Entity\Diary\Type", mappedBy="parent")
     * @ORM\OrderBy({"position": "ASC"})
     */
    private $child;

    /**
     * Construct
     *
     * @param string $name
     * @param Type   $parent
     */
    public function __construct($name, Type $parent = null)
    {
        $this->name = $name;
        $this->parent = $parent;
        $this->child = new ArrayCollection();
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
     * Get parent
     *
     * @return Type
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set parent
     *
     * @param Type $parent
     *
     * @return Type
     */
    public function setParent(Type $parent = null)
    {
        $this->parent = $parent;

        return $this;
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
     * Set name
     *
     * @param string $name
     *
     * @return Type
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set position
     *
     * @param int $position
     *
     * @return Type
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get child types
     *
     * @return \Doctrine\Common\Collections\Collection|Type[]
     */
    public function getChild()
    {
        return $this->child;
    }

    /**
     * Get root level
     *
     * @return integer
     */
    public function getRootLevel()
    {
        return count($this->getParents());
    }

    /**
     * Get all parents
     *
     * @return \Doctrine\Common\Collections\Collection|Type[]
     */
    public function getParents()
    {
        $parents = new ArrayCollection();

        if (!$this->parent) {
            return $parents;
        }

        $parent = $this->parent;

        $parents->add($parent);

        while ($parent = $parent->getParent()) {
            $parents->add($parent);
        }

        return $parents;
    }

    /**
     * Get full name
     *
     * @return string
     */
    public function getFullName()
    {
        $parentNames = array_map(function (Type $parent) {
            return $parent->getName();
        }, $this->getParents()->toArray());

        $parentNames = array_reverse($parentNames);

        $parentNames[] = $this->getName();

        return implode(' :: ', $parentNames);
    }
}
