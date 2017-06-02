<?php
/**
 * User: dongww
 * Date: 14-5-27
 * Time: 上午9:07
 */

namespace PhpGo\Db\Doctrine\Dbal\Tool;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table as SchemaTable;
use PhpGo\Db\Doctrine\Dbal\Structure\Field\FieldAbstract;
use PhpGo\Db\Doctrine\Dbal\Structure\Field\RelationField;
use PhpGo\Db\Doctrine\Dbal\Structure\Structure;

class SchemaChecker
{
    protected $conn;

    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    /**
     * 获得数据库的更改后的 SQL语句 数组
     *
     * @param  string $fileName 数据结构的文件名
     *
     * @return array
     */
    public function getDiffSql($fileName)
    {
        $structure = Structure::createFromYaml($fileName);
        $tables    = $structure->getTables();
        /** @var SchemaTable[] $schemaTables */
        $schemaTables = [];

        $newSchema = new Schema();
        $oldSchema = $this->conn->getSchemaManager()->createSchema();

        foreach ($tables as $table) {
            $schemaTable = $newSchema->createTable($table->getName());
            $schemaTable->addColumn('id', 'integer', ['autoincrement' => true]);
            $schemaTable->setPrimaryKey(['id']);

            $schemaTables[$table->getName()] = $schemaTable;
        }

        //为每个表附加字段
        foreach ($tables as $table) {
            foreach ($table->getFields() as $fieldName => $field) {
                $schemaTable = $schemaTables[$table->getName()];
                $schemaTable->addColumn(
                    $fieldName,
                    $field->getRealType(),
                    $field->getDoctrineOptions()
                );

                if($field instanceof RelationField) {
                    $schemaTable->addForeignKeyConstraint(
                        $schemaTables[$field->getRelationTable()->getName()],
                        [$field->getName()],
                        ["id"],
                        ["onUpdate" => "CASCADE", "onDelete" => "SET NULL"]
                    );

                    continue;
                }

                if($field instanceof FieldAbstract) {
                    if($field->isUnique()) {
                        $schemaTable->addUniqueIndex([$field->getName()]);
                    }

                    if($field->isIndex()) {
                        $schemaTable->addIndex([$field->getName()]);
                    }

                    continue;
                }
            }
        }

        return $sql = $oldSchema->getMigrateToSql(
            $newSchema,
            $this->conn->getDatabasePlatform()
        );
    }
}
