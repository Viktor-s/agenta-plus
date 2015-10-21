<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\TableNode;
use Solution\JsonRpcApiExtension\Context\JsonRpcClientContext;
use AgentPlus\Component\Kernel\KernelAwareInterface;
use AgentPlus\Component\Kernel\KernelAwareTrait;
use PHPUnit_Framework_Assert as Assertions;

class FeatureContext extends JsonRpcClientContext implements Context, SnippetAcceptingContext, KernelAwareInterface
{
    use KernelAwareTrait;

    /**
     * Clean entity manager before scenario
     *
     * @BeforeStep
     */
    public function cleanEntityManager()
    {
        $this->kernel->getDbEntityManager()->clear();
    }

    /**
     * Clear database
     *
     * @Given /^empty database$/
     */
    public function emptyDatabase()
    {
        $em = $this->kernel->getDbEntityManager();
        $connection = $em->getConnection();
        $platform = $connection->getDatabasePlatform();

        $metadataFactory = $em->getMetadataFactory();
        /** @var \Doctrine\ORM\Mapping\ClassMetadata[] $allMetadata */
        $allMetadata = $metadataFactory->getAllMetadata();

        foreach ($allMetadata as $metadata) {
            $tableName = $metadata->table['name'];
            $sql = $platform->getTruncateTableSQL($tableName, true);
            $connection->executeQuery($sql);
        }

        // Clear EM
        $em->clear();
    }

    /**
     * Check response is pagination
     *
     * @Then /^response is pagination with limit "(\d+)", page "(\d+)", total items "(\d+)", items "(\d+)", data:$/
     *
     * @param int       $limit
     * @param int       $page
     * @param int       $totalItems
     * @param int       $items
     * @param TableNode $table
     */
    public function responseIsPaginationWithLimitPageTotalItemsData($limit, $page, $totalItems, $items, TableNode $table)
    {
        $result = $this->response->getRpcResult();

        Assertions::assertArrayHasKey('limit', $result, 'Not found "limit" field in pagination.');
        Assertions::assertArrayHasKey('page', $result, 'Not found "page" field in pagination.');
        Assertions::assertArrayHasKey('totalItems', $result, 'Not found "totalItems" field in pagination.');
        Assertions::assertArrayHasKey('storage', $result, 'Not found "storage" field in pagination.');

        Assertions::assertEquals($limit, $result['limit']);
        Assertions::assertEquals($page, $result['page']);
        Assertions::assertEquals($totalItems, $result['totalItems']);
        Assertions::assertCount((int) $items, $result['storage'], 'The pagination items not equals.');

        $storage = $result['storage'];

        foreach($table->getHash() as $key => $row) {
            if (isset($row['__key__'])) {
                $key = $row['__key__'];
                unset ($row['__key__']);
            }

            Assertions::assertArrayHasKey($key, $storage, sprintf(
                'Not found data with key "%s" in array (%s).',
                $key,
                json_encode($storage)
            ));

            $this->assertArrayEquals($storage[$key], $row);
        }
    }
}
