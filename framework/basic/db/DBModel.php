<?php

namespace pwf\basic\db;

abstract class DBModel extends \pwf\components\activerecord\Model implements \pwf\components\querybuilder\interfaces\SelectBuilder,
    \pwf\components\querybuilder\interfaces\InsertBuilder, \pwf\components\querybuilder\interfaces\UpdateBuilder,
    \pwf\components\querybuilder\interfaces\DeleteBuilder
{

    use \pwf\components\querybuilder\traits\Conditional,
        \pwf\components\querybuilder\traits\Parameterized,
        \pwf\components\querybuilder\traits\SelectBuilder {
        \pwf\components\querybuilder\traits\Parameterized::getParams as parentGetParams;
    }

    /**
     * COnstruct
     * 
     * @param \pwf\components\dbconnection\interfaces\Connection $connection
     * @param array $attributes
     */
    public function __construct($connection, array $attributes = array())
    {
        parent::__construct($connection, $attributes);

        $this->setConditionBuilder(QueryBuilder::getConditionBuilder());
    }

    /**
     * 
     * @return type
     */
    public function count()
    {
        return (int) $this->getConnection()
                ->query(QueryBuilder::select()
                    ->select(['COUNT('.$this->getPK().') AS CNT'])
                    ->table($this->getTable())
                    ->setConditionBuilder($this->getConditionBuilder())
                    ->where($this->getWhere())
                    ->generate(), $this->getParams())
                ->fetchColumn();
    }

    /**
     * @inheritdoc
     */
    public function delete()
    {
        return $this->getConnection()->exec(QueryBuilder::delete()
                    ->table($this->getTable())
                    ->setConditionBuilder($this->getConditionBuilder())
                    ->where($this->getWhere())
                    ->generate(), $this->getParams());
    }

    /**
     * @inheritdoc
     */
    public function getAll()
    {
        return $this->getConnection()->query(QueryBuilder::select()
                    ->table($this->getTable())
                    ->setConditionBuilder($this->getConditionBuilder())
                    ->where($this->getWhere())
                    ->limit($this->getLimit())
                    ->offset($this->getOffset())
                    ->generate(), $this->getParams());
    }

    /**
     * @inheritdoc
     */
    public function getOne()
    {
        return $this->getConnection()->query(
                QueryBuilder::select()
                    ->table($this->getTable())
                    ->setConditionBuilder($this->getConditionBuilder())
                    ->where($this->getWhere())
                    ->limit(1)
                    ->generate(), $this->getParams());
    }

    /**
     * @inheritdoc
     */
    public function save()
    {
        $result = null;
        $id     = $this->getId();
        if (!empty($id)) {
            $result = $this->getConnection()->exec(QueryBuilder::update()
                    ->table($this->getTable())
                    ->setConditionBuilder($this->getConditionBuilder())
                    ->where([
                        $this->getPK() => $this->getId()
                    ])
                    ->setParams($this->getAttributes())
                    ->generate(), $this->getParams());
        } else {
            $result = $this->getConnection()->exec(QueryBuilder::insert()
                    ->table($this->getTable())
                    ->setParams($this->getAttributes())
                    ->generate(), $this->getParams());
        }
        return $result;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function generate()
    {
        throw new \Exception('Not implemented');
    }

    /**
     * @inheritdoc
     */
    public function getParams()
    {
        return array_merge($this->parentGetParams(),
            $this->getConditionBuilder()->getParams());
    }
}