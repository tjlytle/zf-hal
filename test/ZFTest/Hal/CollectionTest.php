<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2013 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZFTest\Hal;

use ZF\Hal\Collection;
use ZF\Hal\Link\Link;
use ZF\Hal\Link\LinkCollection;
use PHPUnit_Framework_TestCase as TestCase;
use stdClass;

class CollectionTest extends TestCase
{
    public function invalidCollections()
    {
        return array(
            'null'       => array(null),
            'true'       => array(true),
            'false'      => array(false),
            'zero-int'   => array(0),
            'int'        => array(1),
            'zero-float' => array(0.0),
            'float'      => array(1.1),
            'string'     => array('string'),
            'stdclass'   => array(new stdClass),
        );
    }

    /**
     * @dataProvider invalidCollections
     */
    public function testConstructorRaisesExceptionForNonTraversableCollection($collection)
    {
        $this->setExpectedException('ZF\Hal\Exception\InvalidCollectionException');
        $hal = new Collection($collection, 'collection/route', 'item/route');
    }

    public function testPropertiesAreAccessibleFollowingConstruction()
    {
        $hal = new Collection(array(), 'item/route', array('version' => 1), array('query' => 'format=json'));
        $this->assertEquals(array(), $hal->collection);
        $this->assertEquals('item/route', $hal->resourceRoute);
        $this->assertEquals(array('version' => 1), $hal->resourceRouteParams);
        $this->assertEquals(array('query' => 'format=json'), $hal->resourceRouteOptions);
    }

    public function testDefaultPageIsOne()
    {
        $hal = new Collection(array(), 'item/route');
        $this->assertEquals(1, $hal->page);
    }

    public function testPageIsMutable()
    {
        $hal = new Collection(array(), 'item/route');
        $hal->setPage(5);
        $this->assertEquals(5, $hal->page);
    }

    public function testDefaultPageSizeIsThirty()
    {
        $hal = new Collection(array(), 'item/route');
        $this->assertEquals(30, $hal->pageSize);
    }

    public function testPageSizeIsMutable()
    {
        $hal = new Collection(array(), 'item/route');
        $hal->setPageSize(3);
        $this->assertEquals(3, $hal->pageSize);
    }

    public function testDefaultCollectionNameIsItems()
    {
        $hal = new Collection(array(), 'item/route');
        $this->assertEquals('items', $hal->collectionName);
    }

    public function testCollectionNameIsMutable()
    {
        $hal = new Collection(array(), 'item/route');
        $hal->setCollectionName('records');
        $this->assertEquals('records', $hal->collectionName);
    }

    public function testDefaultAttributesAreEmpty()
    {
        $hal = new Collection(array(), 'item/route');
        $this->assertEquals(array(), $hal->attributes);
    }

    public function testAttributesAreMutable()
    {
        $hal = new Collection(array(), 'item/route');
        $attributes = array(
            'count' => 1376,
            'order' => 'desc',
        );
        $hal->setAttributes($attributes);
        $this->assertEquals($attributes, $hal->attributes);
    }

    public function testComposesLinkCollectionByDefault()
    {
        $hal = new Collection(array(), 'item/route');
        $this->assertInstanceOf('ZF\Hal\Link\LinkCollection', $hal->getLinks());
    }

    public function testLinkCollectionMayBeInjected()
    {
        $hal   = new Collection(array(), 'item/route');
        $links = new LinkCollection();
        $hal->setLinks($links);
        $this->assertSame($links, $hal->getLinks());
    }

    public function testAllowsSettingAdditionalResourceLinks()
    {
        $links = new LinkCollection();
        $links->add(new Link('describedby'));
        $links->add(new Link('orders'));
        $hal   = new Collection(array(), 'item/route');
        $hal->setResourceLinks($links);
        $this->assertSame($links, $hal->getResourceLinks());
        $this->assertSame($links, $hal->resourceLinks);
    }
}
