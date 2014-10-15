<?php
/**
 * User: dongww
 * Date: 2014-10-7
 * Time: 14:38
 */

namespace PhpGo\Db\Doctrine\Dbal\Extension;

use PhpGo\Db\Doctrine\Dbal\Extension\Manager\TreeAbleManager;
use PhpGo\Db\Doctrine\Dbal\Manager\ManagerFactory;
use PhpGo\Db\Doctrine\Dbal\Structure\Field\IntegerField;
use PhpGo\Db\Doctrine\Dbal\Structure\Field\StringField;
use PhpGo\Db\Doctrine\Dbal\Structure\Table;

class TreeAbleExtension implements ExtensionInterface
{
    const NAME = 'tree_able';

    public function getName()
    {
        return static::NAME;
    }

    public function extendTable(Table $table)
    {
        $table
            ->addField(new StringField('name', $table))
            ->addField(new IntegerField('sort', $table))
            ->addField(new StringField('path', $table))
            ->addField(new IntegerField('level', $table))
            ->addBelongTo($table, 'parent_id');
    }

    public function registerListener(ManagerFactory $mf)
    {

    }

    public function registerManager(ManagerFactory $mf)
    {
        $structure = $mf->getStructure();

        foreach ($structure->getConfig()['tables'] as $name => $table) {
            if (in_array(static::NAME, $table['extensions'])) {
                $mf->registerManager($name, new TreeAbleManager(
                        $mf,
                        $structure->getTable($name)
                    )
                );
            }
        }
    }
}
