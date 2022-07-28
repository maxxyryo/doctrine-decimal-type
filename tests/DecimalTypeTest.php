<?php

namespace Kenny1911\DoctrineDecimalType\Tests;

use Kenny1911\DoctrineDecimalType\DecimalType;
use Decimal\Decimal;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Kenny1911 <o-muzyka@mail.ru>
 */
final class DecimalTypeTest extends TestCase
{
    /** @var DecimalType */
    private $type;

    /** @var AbstractPlatform|MockObject  */
    private $platform;

    protected function setUp(): void
    {
        $this->type = new DecimalType();
        $this->platform = $this->getMockBuilder(AbstractPlatform::class)->getMockForAbstractClass();
    }

    /**
     * @throws ConversionException
     *
     * @dataProvider dataConvertToDatabaseValue
     */
    public function testConvertToDatabaseValue(?string $expected, ?Decimal $value): void
    {
        $this->assertSame($expected, $this->type->convertToDatabaseValue($value, $this->platform));
    }

    public function dataConvertToDatabaseValue(): array
    {
        return [
            'decimal' => ['123.45', new Decimal('123.45')],
            'null' => [null, null],
        ];
    }

    public function testConvertToDatabaseValueInvalidType(): void
    {
        $this->expectException(ConversionException::class);

        $this->type->convertToDatabaseValue(123, $this->platform);
    }

    /**
     * @throws ConversionException
     *
     * @dataProvider dataConvertToPHPValue
     */
    public function testConvertToPHPValue(?Decimal $expected, ?string $value): void
    {
        $actual = $this->type->convertToPHPValue($value, $this->platform);

        if ($expected instanceof Decimal) {
            $this->assertTrue($expected->equals($actual));
        } else {
            $this->assertNull($actual);
        }
    }

    public function dataConvertToPHPValue(): array
    {
        return [
            'decimal' => [new Decimal('123.45'), '123.45'],
            'null' => [null, null],
        ];
    }

    public function testConvertToPHPValueFailedFormat(): void
    {
        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage(sprintf('Could not convert database value "failed" to Doctrine Type %s', DecimalType::NAME));

        $this->type->convertToPHPValue('failed', $this->platform);
    }
}
