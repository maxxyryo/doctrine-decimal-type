<?php

namespace Kenny1911\DoctrineDecimalType;

use Decimal\Decimal;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Exception;

/**
 * @author Kenny1911 <o-muzyka@mail.ru>
 */
class DecimalType extends \Doctrine\DBAL\Types\DecimalType
{
    const NAME = 'decimal_number';

    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }

        if ($value instanceof Decimal) {
            return $value->toString();
        }

        throw ConversionException::conversionFailedInvalidType($value, $this->getName(), [Decimal::class, 'null']);
    }

    /**
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?Decimal
    {
        if (null === $value) {
            return null;
        }

        try {
            return new Decimal($value);
        } catch (Exception $e) {
            throw ConversionException::conversionFailed($value, $this->getName());
        }
    }
}