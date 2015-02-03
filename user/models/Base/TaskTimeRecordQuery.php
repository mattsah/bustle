<?php

namespace Base;

use \TaskTimeRecord as ChildTaskTimeRecord;
use \TaskTimeRecordQuery as ChildTaskTimeRecordQuery;
use \Exception;
use \PDO;
use Map\TaskTimeRecordTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'task_time_records' table.
 *
 *
 *
 * @method     ChildTaskTimeRecordQuery orderByTaskId($order = Criteria::ASC) Order by the task column
 * @method     ChildTaskTimeRecordQuery orderByAssignee($order = Criteria::ASC) Order by the assignee column
 * @method     ChildTaskTimeRecordQuery orderByTimeEntered($order = Criteria::ASC) Order by the time_entered column
 * @method     ChildTaskTimeRecordQuery orderByRequiredTime($order = Criteria::ASC) Order by the required_time column
 *
 * @method     ChildTaskTimeRecordQuery groupByTaskId() Group by the task column
 * @method     ChildTaskTimeRecordQuery groupByAssignee() Group by the assignee column
 * @method     ChildTaskTimeRecordQuery groupByTimeEntered() Group by the time_entered column
 * @method     ChildTaskTimeRecordQuery groupByRequiredTime() Group by the required_time column
 *
 * @method     ChildTaskTimeRecordQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildTaskTimeRecordQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildTaskTimeRecordQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildTaskTimeRecordQuery leftJoinUser($relationAlias = null) Adds a LEFT JOIN clause to the query using the User relation
 * @method     ChildTaskTimeRecordQuery rightJoinUser($relationAlias = null) Adds a RIGHT JOIN clause to the query using the User relation
 * @method     ChildTaskTimeRecordQuery innerJoinUser($relationAlias = null) Adds a INNER JOIN clause to the query using the User relation
 *
 * @method     ChildTaskTimeRecordQuery leftJoinTask($relationAlias = null) Adds a LEFT JOIN clause to the query using the Task relation
 * @method     ChildTaskTimeRecordQuery rightJoinTask($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Task relation
 * @method     ChildTaskTimeRecordQuery innerJoinTask($relationAlias = null) Adds a INNER JOIN clause to the query using the Task relation
 *
 * @method     \UserQuery|\TaskQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildTaskTimeRecord findOne(ConnectionInterface $con = null) Return the first ChildTaskTimeRecord matching the query
 * @method     ChildTaskTimeRecord findOneOrCreate(ConnectionInterface $con = null) Return the first ChildTaskTimeRecord matching the query, or a new ChildTaskTimeRecord object populated from the query conditions when no match is found
 *
 * @method     ChildTaskTimeRecord findOneByTaskId(int $task) Return the first ChildTaskTimeRecord filtered by the task column
 * @method     ChildTaskTimeRecord findOneByAssignee(int $assignee) Return the first ChildTaskTimeRecord filtered by the assignee column
 * @method     ChildTaskTimeRecord findOneByTimeEntered(string $time_entered) Return the first ChildTaskTimeRecord filtered by the time_entered column
 * @method     ChildTaskTimeRecord findOneByRequiredTime(double $required_time) Return the first ChildTaskTimeRecord filtered by the required_time column *

 * @method     ChildTaskTimeRecord requirePk($key, ConnectionInterface $con = null) Return the ChildTaskTimeRecord by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildTaskTimeRecord requireOne(ConnectionInterface $con = null) Return the first ChildTaskTimeRecord matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildTaskTimeRecord requireOneByTaskId(int $task) Return the first ChildTaskTimeRecord filtered by the task column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildTaskTimeRecord requireOneByAssignee(int $assignee) Return the first ChildTaskTimeRecord filtered by the assignee column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildTaskTimeRecord requireOneByTimeEntered(string $time_entered) Return the first ChildTaskTimeRecord filtered by the time_entered column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildTaskTimeRecord requireOneByRequiredTime(double $required_time) Return the first ChildTaskTimeRecord filtered by the required_time column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildTaskTimeRecord[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildTaskTimeRecord objects based on current ModelCriteria
 * @method     ChildTaskTimeRecord[]|ObjectCollection findByTaskId(int $task) Return ChildTaskTimeRecord objects filtered by the task column
 * @method     ChildTaskTimeRecord[]|ObjectCollection findByAssignee(int $assignee) Return ChildTaskTimeRecord objects filtered by the assignee column
 * @method     ChildTaskTimeRecord[]|ObjectCollection findByTimeEntered(string $time_entered) Return ChildTaskTimeRecord objects filtered by the time_entered column
 * @method     ChildTaskTimeRecord[]|ObjectCollection findByRequiredTime(double $required_time) Return ChildTaskTimeRecord objects filtered by the required_time column
 * @method     ChildTaskTimeRecord[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class TaskTimeRecordQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \Base\TaskTimeRecordQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\TaskTimeRecord', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildTaskTimeRecordQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildTaskTimeRecordQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildTaskTimeRecordQuery) {
            return $criteria;
        }
        $query = new ChildTaskTimeRecordQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj = $c->findPk(array(12, 34), $con);
     * </code>
     *
     * @param array[$task, $assignee] $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildTaskTimeRecord|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = TaskTimeRecordTableMap::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(TaskTimeRecordTableMap::DATABASE_NAME);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildTaskTimeRecord A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT task, assignee, time_entered, required_time FROM task_time_records WHERE task = :p0 AND assignee = :p1';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key[0], PDO::PARAM_INT);
            $stmt->bindValue(':p1', $key[1], PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            /** @var ChildTaskTimeRecord $obj */
            $obj = new ChildTaskTimeRecord();
            $obj->hydrate($row);
            TaskTimeRecordTableMap::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return ChildTaskTimeRecord|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, ConnectionInterface $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(array(12, 56), array(832, 123), array(123, 456)), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return $this|ChildTaskTimeRecordQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(TaskTimeRecordTableMap::COL_TASK, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(TaskTimeRecordTableMap::COL_ASSIGNEE, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildTaskTimeRecordQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(TaskTimeRecordTableMap::COL_TASK, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(TaskTimeRecordTableMap::COL_ASSIGNEE, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
    }

    /**
     * Filter the query on the task column
     *
     * Example usage:
     * <code>
     * $query->filterByTaskId(1234); // WHERE task = 1234
     * $query->filterByTaskId(array(12, 34)); // WHERE task IN (12, 34)
     * $query->filterByTaskId(array('min' => 12)); // WHERE task > 12
     * </code>
     *
     * @see       filterByTask()
     *
     * @param     mixed $taskId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildTaskTimeRecordQuery The current query, for fluid interface
     */
    public function filterByTaskId($taskId = null, $comparison = null)
    {
        if (is_array($taskId)) {
            $useMinMax = false;
            if (isset($taskId['min'])) {
                $this->addUsingAlias(TaskTimeRecordTableMap::COL_TASK, $taskId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($taskId['max'])) {
                $this->addUsingAlias(TaskTimeRecordTableMap::COL_TASK, $taskId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TaskTimeRecordTableMap::COL_TASK, $taskId, $comparison);
    }

    /**
     * Filter the query on the assignee column
     *
     * Example usage:
     * <code>
     * $query->filterByAssignee(1234); // WHERE assignee = 1234
     * $query->filterByAssignee(array(12, 34)); // WHERE assignee IN (12, 34)
     * $query->filterByAssignee(array('min' => 12)); // WHERE assignee > 12
     * </code>
     *
     * @see       filterByUser()
     *
     * @param     mixed $assignee The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildTaskTimeRecordQuery The current query, for fluid interface
     */
    public function filterByAssignee($assignee = null, $comparison = null)
    {
        if (is_array($assignee)) {
            $useMinMax = false;
            if (isset($assignee['min'])) {
                $this->addUsingAlias(TaskTimeRecordTableMap::COL_ASSIGNEE, $assignee['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($assignee['max'])) {
                $this->addUsingAlias(TaskTimeRecordTableMap::COL_ASSIGNEE, $assignee['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TaskTimeRecordTableMap::COL_ASSIGNEE, $assignee, $comparison);
    }

    /**
     * Filter the query on the time_entered column
     *
     * Example usage:
     * <code>
     * $query->filterByTimeEntered('2011-03-14'); // WHERE time_entered = '2011-03-14'
     * $query->filterByTimeEntered('now'); // WHERE time_entered = '2011-03-14'
     * $query->filterByTimeEntered(array('max' => 'yesterday')); // WHERE time_entered > '2011-03-13'
     * </code>
     *
     * @param     mixed $timeEntered The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildTaskTimeRecordQuery The current query, for fluid interface
     */
    public function filterByTimeEntered($timeEntered = null, $comparison = null)
    {
        if (is_array($timeEntered)) {
            $useMinMax = false;
            if (isset($timeEntered['min'])) {
                $this->addUsingAlias(TaskTimeRecordTableMap::COL_TIME_ENTERED, $timeEntered['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($timeEntered['max'])) {
                $this->addUsingAlias(TaskTimeRecordTableMap::COL_TIME_ENTERED, $timeEntered['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TaskTimeRecordTableMap::COL_TIME_ENTERED, $timeEntered, $comparison);
    }

    /**
     * Filter the query on the required_time column
     *
     * Example usage:
     * <code>
     * $query->filterByRequiredTime(1234); // WHERE required_time = 1234
     * $query->filterByRequiredTime(array(12, 34)); // WHERE required_time IN (12, 34)
     * $query->filterByRequiredTime(array('min' => 12)); // WHERE required_time > 12
     * </code>
     *
     * @param     mixed $requiredTime The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildTaskTimeRecordQuery The current query, for fluid interface
     */
    public function filterByRequiredTime($requiredTime = null, $comparison = null)
    {
        if (is_array($requiredTime)) {
            $useMinMax = false;
            if (isset($requiredTime['min'])) {
                $this->addUsingAlias(TaskTimeRecordTableMap::COL_REQUIRED_TIME, $requiredTime['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($requiredTime['max'])) {
                $this->addUsingAlias(TaskTimeRecordTableMap::COL_REQUIRED_TIME, $requiredTime['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TaskTimeRecordTableMap::COL_REQUIRED_TIME, $requiredTime, $comparison);
    }

    /**
     * Filter the query by a related \User object
     *
     * @param \User|ObjectCollection $user The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildTaskTimeRecordQuery The current query, for fluid interface
     */
    public function filterByUser($user, $comparison = null)
    {
        if ($user instanceof \User) {
            return $this
                ->addUsingAlias(TaskTimeRecordTableMap::COL_ASSIGNEE, $user->getPersonId(), $comparison);
        } elseif ($user instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(TaskTimeRecordTableMap::COL_ASSIGNEE, $user->toKeyValue('PrimaryKey', 'PersonId'), $comparison);
        } else {
            throw new PropelException('filterByUser() only accepts arguments of type \User or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the User relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildTaskTimeRecordQuery The current query, for fluid interface
     */
    public function joinUser($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('User');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'User');
        }

        return $this;
    }

    /**
     * Use the User relation User object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \UserQuery A secondary query class using the current class as primary query
     */
    public function useUserQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinUser($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'User', '\UserQuery');
    }

    /**
     * Filter the query by a related \Task object
     *
     * @param \Task|ObjectCollection $task The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildTaskTimeRecordQuery The current query, for fluid interface
     */
    public function filterByTask($task, $comparison = null)
    {
        if ($task instanceof \Task) {
            return $this
                ->addUsingAlias(TaskTimeRecordTableMap::COL_TASK, $task->getId(), $comparison);
        } elseif ($task instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(TaskTimeRecordTableMap::COL_TASK, $task->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByTask() only accepts arguments of type \Task or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Task relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildTaskTimeRecordQuery The current query, for fluid interface
     */
    public function joinTask($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Task');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Task');
        }

        return $this;
    }

    /**
     * Use the Task relation Task object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \TaskQuery A secondary query class using the current class as primary query
     */
    public function useTaskQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinTask($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Task', '\TaskQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildTaskTimeRecord $taskTimeRecord Object to remove from the list of results
     *
     * @return $this|ChildTaskTimeRecordQuery The current query, for fluid interface
     */
    public function prune($taskTimeRecord = null)
    {
        if ($taskTimeRecord) {
            $this->addCond('pruneCond0', $this->getAliasedColName(TaskTimeRecordTableMap::COL_TASK), $taskTimeRecord->getTaskId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(TaskTimeRecordTableMap::COL_ASSIGNEE), $taskTimeRecord->getAssignee(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

    /**
     * Deletes all rows from the task_time_records table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(TaskTimeRecordTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            TaskTimeRecordTableMap::clearInstancePool();
            TaskTimeRecordTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    /**
     * Performs a DELETE on the database based on the current ModelCriteria
     *
     * @param ConnectionInterface $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public function delete(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(TaskTimeRecordTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(TaskTimeRecordTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            TaskTimeRecordTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            TaskTimeRecordTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // TaskTimeRecordQuery
