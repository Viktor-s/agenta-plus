<?php

namespace AgentPlus\Behat;

use Doctrine\ORM\EntityManagerInterface;

trait DbHelperTrait
{
    /**
     * Update identifier for entity class
     *
     * @param EntityManagerInterface $em
     * @param string                 $entityClass
     * @param mixed                  $oldId
     * @param mixed                  $newId
     */
    protected function updateIdForEntity(EntityManagerInterface $em, $entityClass, $oldId, $newId)
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadata $classMetadata */
        $classMetadata = $em->getClassMetadata($entityClass);
        $tableName = $classMetadata->table['name'];

        $sql = sprintf(
            'UPDATE %s SET id = :new_id WHERE id = :old_id',
            $tableName
        );

        $connection = $em->getConnection();
        $connection->executeQuery($sql, [
            'new_id' => $newId,
            'old_id' => $oldId
        ]);
    }

    /**
     * Get entity reference
     *
     * @param EntityManagerInterface $em
     * @param string                 $entityClass
     * @param string                 $id
     *
     * @return object
     */
    protected function getEntityReference(EntityManagerInterface $em, $entityClass, $id)
    {
        return $em->getReference($entityClass, $id);
    }
}
