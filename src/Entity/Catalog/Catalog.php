<?php

namespace AgentPlus\Entity\Catalog;

use AgentPlus\Entity\Factory\Factory;
use AgentPlus\Entity\User\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use FiveLab\Component\Reflection\Reflection;
use FiveLab\Component\ModelTransformer\Annotation as ModelTransform;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="catalog"
 * )
 *
 * @ModelTransform\Object(transformedClass="AgentPlus\Model\Catalog\Catalog")
 */
class Catalog
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @ModelTransform\Property()
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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AgentPlus\Entity\User\User")
     * @ORM\JoinColumn(name="creator_id", referencedColumnName="id", nullable=false, onDelete="RESTRICT")
     *
     * @ModelTransform\Property(shouldTransform=true)
     */
    private $creator;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     *
     * @ModelTransform\Property()
     */
    private $name;

    /**
     * @var \Doctrine\Common\Collections\Collection|Factory[]
     *
     * @ORM\ManyToMany(targetEntity="AgentPlus\Entity\Factory\Factory")
     * @ORM\JoinTable(
     *      name="catalog_factories",
     *      joinColumns={
     *          @ORM\JoinColumn(name="catalog_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="factory_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     *      }
     * )
     *
     * @ModelTransform\Property(shouldTransform=true)
     */
    private $factories;

    /**
     * @var \Doctrine\Common\Collections\Collection|Image[]
     *
     * @ORM\OneToMany(targetEntity="AgentPlus\Entity\Catalog\Image", cascade={"persist"}, mappedBy="catalog")
     *
     * @ModelTransform\Property(shouldTransform=true)
     */
    private $images;

    /**
     * Construct
     *
     * @param User   $creator
     * @param string $name
     */
    public function __construct(User $creator, $name)
    {
        $this->creator = $creator;
        $this->name = $name;

        $this->createdAt = new \DateTime();
        $this->factories = new ArrayCollection();
        $this->images = new ArrayCollection();
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
     * Get creator
     *
     * @return User
     */
    public function getCreator()
    {
        return $this->creator;
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
     * Set name
     *
     * @param string $name
     *
     * @return Catalog
     */
    public function setName($name)
    {
        $this->name = $name;

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
     * Has factory?
     *
     * @param Factory $factory
     *
     * @return bool
     */
    public function hasFactory(Factory $factory)
    {
        return $this->factories->exists(function ($key, Factory $item) use ($factory) {
            return $item->getId() == $factory->getId();
        });
    }

    /**
     * Add factory
     *
     * @param Factory $factory
     *
     * @return Catalog
     */
    public function addFactory(Factory $factory)
    {
        if (!$this->hasFactory($factory)) {
            $this->factories->add($factory);
        }

        return $this;
    }

    /**
     * Remove factory
     *
     * @param Factory $factory
     *
     * @return Catalog
     */
    public function removeFactory(Factory $factory)
    {
        $removalIndex = null;
        $this->factories->forAll(function ($key, Factory $item) use ($factory, &$removalIndex) {
            if ($factory->getId() == $item->getId()) {
                $removalIndex = $key;

                return false;
            }

            return true;
        });

        if (null !== $removalIndex) {
            $this->factories->remove($removalIndex);
        }

        return $this;
    }

    /**
     * Replace factories
     *
     * @param Collection|Factory[] $factories
     *
     * @return Catalog
     */
    public function replaceFactories(Collection $factories)
    {
        // First step: add factories
        foreach ($factories as $factory) {
            $this->addFactory($factory);
        }

        // Second step: remove factories
        foreach ($this->factories as $factory) {
            $exist = $factories->exists(function ($key, Factory $item) use ($factory) {
                return $item->getId() == $factory->getId();
            });

            if (!$exist) {
                $this->factories->remove($factory);
            }
        }

        return $this;
    }

    /**
     * Has image?
     *
     * @param Image $image
     *
     * @return bool
     */
    public function hasImage(Image $image)
    {
        return $this->images->exists(function ($key, Image $item) use ($image) {
            return $image->getId() == $item->getId();
        });
    }

    /**
     * Add image
     *
     * @param Image $image
     *
     * @return Catalog
     */
    public function addImage(Image $image)
    {
        if ($image->getId()) {
            /** @var Catalog $catalog */
            $catalog = Reflection::getPropertyValue($image, 'catalog');

            if ($catalog && $catalog->getId() && $catalog->getId() != $this->getId()) {
                throw new \InvalidArgumentException(sprintf(
                    'The image "%s" already added to another catalog "%s [%d]".',
                    $image->getName(),
                    $catalog->getName(),
                    $catalog->getId()
                ));
            }
        }

        if (!$this->hasImage($image)) {
            Reflection::setPropertyValue($image, 'catalog', $this);
            $this->images->add($image);
        }

        return $this;
    }

    /**
     * Remove image
     *
     * @param Image $image
     *
     * @return Catalog
     */
    public function removeImage(Image $image)
    {
        $removalKey = null;

        $this->images->forAll(function ($key, Image $item) use ($image, &$removalKey) {
            if ($image->getId() == $item->getId()) {
                $removalKey = $key;

                return false;
            }

            return true;
        });

        if (null !== $removalKey) {
            $this->images->remove($removalKey);
        }

        return $this;
    }
}
