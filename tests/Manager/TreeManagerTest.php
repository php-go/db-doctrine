<?php
/**
 * User: dongww
 * Date: 2014-10-13
 * Time: 14:47
 */

namespace PhpGo\Db\Doctrine\Dbal\Test\Manager;


use PhpGo\Db\Doctrine\Dbal\Extension\Manager\TreeAbleManager;
use PHPUnit_Extensions_Database_DataSet_IDataSet;
use PHPUnit_Extensions_Database_DB_IDatabaseConnection;

class TreeManagerTest extends AbstractTest
{
    /** @var  TreeAbleManager */
    protected $cm;

    public function setup()
    {
        parent::setup();
        $this->cm = $this->mf->getManager('category');
    }

    public function testInit()
    {
        $cm = $this->mf->getManager('category');
        $this->assertInstanceOf(
            'PhpGo\Db\Doctrine\Dbal\Extension\Manager\TreeAbleManager',
            $cm
        );

        $bean = $cm->get(1);

        $this->assertInstanceOf('PhpGo\Db\Doctrine\Dbal\Manager\Bean', $bean);
        $this->assertEquals('水果', $bean->name);
    }

    public function testAddChild()
    {
        $bean = $this->cm->get(1);

        $insertBean       = $this->cm->createEntity();
        $insertBean->name = '苹果';

        $count = $this->cm->addChildNode($insertBean, $bean);

        $this->assertEquals(1, $count);

        $addedBean = $this->cm->get(2);

        $this->assertEquals(2, $addedBean->id);
        $this->assertEquals(1, $addedBean->parent_id);
        $this->assertEquals('苹果', $addedBean->name);
        $this->assertEquals('/1/', $addedBean->path);
        $this->assertEquals('1', $addedBean->sort);
    }
}
 