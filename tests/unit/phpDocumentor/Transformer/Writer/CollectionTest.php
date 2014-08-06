<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Writer;

use Mockery\MockInterface;
use Mockery as m;

/**
 * Test class for phpDocumentor\Transformer\Writer\Collection
 */
class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /** @var MockInterface|Queue */
    protected $routers;

    /** @var MockInterface|Xsl */
    protected $writer;
    
    /**
     * Initializes the fixture and dependencies for this testcase.
     */
    public function setUp()
    {
        $this->routers = m::mock('phpDocumentor\Transformer\Router\Queue');
        $this->writer = m::mock('phpDocumentor\Plugin\Core\Transformer\Writer\Xsl');
        $this->fixture = new Collection($this->routers);
    }

    /**
     * @covers phpDocumentor\Transformer\Writer\Collection::__construct
     */
    public function testIfDependenciesAreCorrectlyRegisteredOnInitialization()
    {
        $this->assertAttributeSame($this->routers, 'routers', $this->fixture);
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @covers phpDocumentor\Transformer\Writer\Collection::offsetSet
     */
    public function testOffsetSetWithWriterNotDescendingFromWriterAbstract()
    {
        $this->fixture->offsetSet('index', new \stdClass());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @covers phpDocumentor\Transformer\Writer\Collection::offsetSet
     */
    public function testOffsetSetWithInvalidIndexName()
    {
        $this->fixture->offsetSet('i', $this->writer);
    }
    
    /**
     * @covers phpDocumentor\Transformer\Writer\Collection::offsetSet
     */
    public function testOffsetSetWriterReceivesRouterQueue()
    {
        $this->writer->shouldReceive('setRouters')->once()->with($this->routers);
        $this->fixture->offsetSet('index', $this->writer);
        
        $this->assertTrue(true);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @covers phpDocumentor\Transformer\Writer\Collection::offsetGet
     */
    public function testOffsetGetWithNonExistingIndex()
    {
        $this->fixture->offsetGet('nonExistingIndex');
    }
    
    /**
     * @covers phpDocumentor\Transformer\Writer\Collection::offsetGet
     */
    public function testOffsetGetWithExistingIndex()
    {
        //first we need a registered writer: the index is 'index'
        $this->testOffsetSetWriterReceivesRouterQueue();
        
        $this->assertSame($this->writer, $this->fixture->offsetGet('index'));
    }
    
   /**
    * @covers phpDocumentor\Transformer\Writer\Collection::checkRequirements
    */
    public function testCheckRequirements()
    {
        //in order to test if the requirements are checked, we need a registered writer
        $this->testOffsetSetWriterReceivesRouterQueue();

        $this->writer->shouldReceive('checkRequirements')->once();
        $this->fixture->checkRequirements();

        $this->assertTrue(true);
    }
}
