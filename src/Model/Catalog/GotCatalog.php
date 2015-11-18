<?php

namespace AgentPlus\Model\Catalog;

use FiveLab\Component\ModelNormalizer\Annotation as ModelNormalize;

class GotCatalog
{
    /**
     * @var \DateTime
     *
     * @ModelNormalize\Property(shouldNormalize=true)
     */
    private $createdAt;

    /**
     * @var Catalog
     *
     * @ModelNormalize\Property(shouldNormalize=true)
     */
    private $catalog;

    /**
     * @var \AgentPlus\Model\Diary\Diary
     *
     * @ModelNormalize\Property(shouldNormalize=true)
     */
    private $diary;

    /**
     * @var \AgentPlus\Model\Client\Client
     *
     * @ModelNormalize\Property(shouldNormalize=true)
     */
    private $client;
}
