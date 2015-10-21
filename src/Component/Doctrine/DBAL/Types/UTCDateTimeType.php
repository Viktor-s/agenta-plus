<?php

namespace AgentPlus\Component\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeType;

class UTCDateTimeType extends DateTimeType
{
    /**
     * @var \DateTimeZone
     */
    static private $utc = null;

    /**
     * {@inheritDoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        /** @var \DateTime $value */
        if ($value === null) {
            return null;
        }

        if (!self::$utc) {
            self::$utc = new \DateTimeZone('UTC');
        }

        if ($value->getTimezone()->getName() != 'UTC') {
            $cloned = clone ($value);
            $cloned->setTimezone(self::$utc);

            $format = $cloned->format($platform->getDateTimeFormatString());
        } else {
            $format = $value->format($platform->getDateTimeFormatString());
        }

        return $format;
    }

    /**
     * {@inheritDoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        if (!self::$utc) {
            self::$utc = new \DateTimeZone('UTC');
        }

        $val = \DateTime::createFromFormat(
            $platform->getDateTimeFormatString(),
            $value,
            self::$utc
        );

        if ('UTC' != date_default_timezone_get()) {
            $defaultTimezone = new \DateTimeZone(date_default_timezone_get());
            $val->setTimezone($defaultTimezone);
        }

        if (!$val) {
            throw ConversionException::conversionFailed($value, $this->getName());
        }

        return $val;
    }
}
