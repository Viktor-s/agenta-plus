<?php

namespace  AgentPlus\Component\Validator;

use FiveLab\Component\VarTagValidator\VarTagValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Validator implements VarTagValidatorInterface, ValidatorInterface
{
    /**
     * Real validator
     *
     * @var ValidatorInterface
     */
    private $sfValidator;

    /**
     * @var VarTagValidatorInterface
     */
    private $varTagValidator;

    /**
     * Construct
     *
     * @param ValidatorInterface       $validator
     * @param VarTagValidatorInterface $varTagValidator
     */
    public function __construct(ValidatorInterface $validator, VarTagValidatorInterface $varTagValidator)
    {
        $this->sfValidator = $validator;
        $this->varTagValidator = $varTagValidator;
    }

    /**
     * {@inheritDoc}
     */
    public function getMetadataFor($value)
    {
        return $this->sfValidator->getMetadataFor($value);
    }

    /**
     * {@inheritDoc}
     */
    public function hasMetadataFor($value)
    {
        return $this->sfValidator->hasMetadataFor($value);
    }

    /**
     * {@inheritDoc}
     */
    public function validate($value, $constraints = null, $groups = null)
    {
        return $this->sfValidator->validate($value, $constraints, $groups);
    }

    /**
     * {@inheritDoc}
     */
    public function validateProperty($object, $propertyName, $groups = null)
    {
        return $this->sfValidator->validateProperty($object, $propertyName, $groups);
    }

    /**
     * {@inheritDoc}
     */
    public function validatePropertyValue($objectOrClass, $propertyName, $value, $groups = null)
    {
        return $this->sfValidator->validatePropertyValue($objectOrClass, $propertyName, $value, $groups);
    }

    /**
     * {@inheritDoc}
     */
    public function startContext()
    {
        return $this->sfValidator->startContext();
    }

    /**
     * {@inheritDoc}
     */
    public function inContext(ExecutionContextInterface $context)
    {
        return $this->sfValidator->inContext($context);
    }

    /**
     * {@inheritDoc}
     */
    public function validateObjectByVarTags($object)
    {
        return $this->varTagValidator->validateObjectByVarTags($object);
    }
}
