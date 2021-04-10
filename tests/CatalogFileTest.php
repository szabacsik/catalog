<?php

namespace Szabacsik\Catalog\Test;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Szabacsik\Catalog\CatalogFile;
use Szabacsik\Catalog\CatalogInterface;

class CatalogFileTest extends TestCase
{
    private const ITEMS = [
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
        'Maecenas suscipit eget nibh eget hendrerit.',
        'Aliquam ante mi, aliquam vitae imperdiet sed, porttitor nec tortor.',
        'Sed non mauris at nibh semper suscipit vitae et massa.',
        'Duis ac pulvinar mi, faucibus accumsan ante.',
        'Donec at mollis lorem non suscipit.',
        'Donec lobortis nibh odio, non rutrum dui efficitur ut.',
        'Etiam porta quis tortor non tempor.',
        'Vivamus consequat nulla ut elit venenatis, sit amet interdum nisi vehicula.',
        'Duis ac tellus sem.',
        'Pellentesque velit magna, varius id tortor at, feugiat elementum sapien.',
        'Duis condimentum sit amet lorem et lobortis.',
    ];

    private const REGULAR_EXPRESSION_PATTERN = '/^(?=.*Lorem|.*Suscipit).*$/i';

    public function testCreateInstance(): CatalogInterface
    {
        $catalog = new CatalogFile('/tmp');
        $this->assertInstanceOf(CatalogFile::class, $catalog);
        return $catalog;
    }

    /**
     * @depends testCreateInstance
     * @param \Szabacsik\Catalog\CatalogInterface $catalog
     * @return void
     */
    public function testAdd(CatalogInterface $catalog): void
    {
        foreach (self::ITEMS as $item) {
            $this->assertTrue($catalog->add($item));
        }
    }

    /**
     * @depends testCreateInstance
     * @param \Szabacsik\Catalog\CatalogInterface $catalog
     * @return void
     */
    public function testFindFirst(CatalogInterface $catalog): void
    {
        $item = $catalog->findFirst(self::REGULAR_EXPRESSION_PATTERN);
        $this->assertEquals(self::ITEMS[0], $item);
    }

    /**
     * @depends testCreateInstance
     * @param \Szabacsik\Catalog\CatalogInterface $catalog
     * @return void
     */
    public function testFindAll(CatalogInterface $catalog): void
    {
        $items = $catalog->findAll(self::REGULAR_EXPRESSION_PATTERN);
        $this->assertCount(5, $items);
    }

    /**
     * @depends testCreateInstance
     * @param \Szabacsik\Catalog\CatalogInterface $catalog
     * @return void
     * @Ignore
     */
    public function testTruncate(CatalogInterface $catalog): void
    {
        //$this->markTestSkipped('testTruncate skipped');
        $this->assertTrue($catalog->truncate());
    }

    /**
     * @depends testCreateInstance
     * @param \Szabacsik\Catalog\CatalogInterface $catalog
     * @return void
     */
    public function testSearchWithInvalidRegularExpression(CatalogInterface $catalog): void
    {
        $this->expectException(InvalidArgumentException::class);
        $catalog->findFirst('lorem');
    }
}
