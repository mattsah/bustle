<?php

namespace Base;

use \TaskComment as ChildTaskComment;
use \TaskCommentQuery as ChildTaskCommentQuery;
use \Exception;
use \PDO;
use Map\TaskCommentTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'task_comments' table.
 *
 *
 *
 * @method     ChildTaskCommentQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildTaskCommentQuery orderByTaskId($order = Criteria::ASC) Order by the task column
 * @method     ChildTaskCommentQuery orderByOwner($order = Criteria::ASC) Order by the owner column
 * @method     ChildTaskCommentQuery orderByTimeCreated($order = Criteria::ASC) Order by the time_created column
 * @method     ChildTaskCommentQuery orderByContent($order = Criteria::ASC) Order by the content column
 *
 * @method     ChildTaskCommentQuery groupById() Group by the id column
 * @method     ChildTaskCommentQuery groupByTaskId() Group by the task column
 * @method     ChildTaskCommentQuery groupByOwner() Group by the owner column
 * @method     ChildTaskCommentQuery groupByTimeCreated() Group by the time_created column
 * @method     ChildTaskCommentQuery groupByContent() Group by the content column
 *
 * @method     ChildTaskCommentQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildTaskCommentQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildTaskCommentQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildTaskCommentQuery leftJoinUser($relationAlias = null) Adds a LEFT JOIN clause to the query using the User relation
 * @method     ChildTaskCommentQuery rightJoinUser($relationAlias = null) Adds a RIGHT JOIN clause to the query using the User relation
 * @method     ChildTaskCommentQuery innerJoinUser($relationAlias = null) Adds a INNER JOIN clause to the query using the User relation
 *
 * @method     ChildTaskCommentQuery leftJoinTask($relationAlias = null) Adds a LEFT JOIN clause to the query using the Task relation
 * @method     ChildTaskCommentQuery rightJoinTask($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Task relation
 * @method     ChildTaskCommentQuery innerJoinTask($relationAlias = null) Adds a INNER JOIN clause to the query using the Task relation
 *
 * @method     \UserQuery|\TaskQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildTaskComment findOne(ConnectionInterface $con = null) Return the first ChildTaskComment matching the query
 * @method     ChildTaskComment findOneOrCreate(ConnectionInterface $con = null) Return the first ChildTaskComment matching the query, or a new ChildTaskComment object populated from the query conditions when no match is found
 *
 * @method     ChildTaskComment findOneById(int $id) Return the first ChildTaskComment filtered by the id column
 * @method     ChildTaskComment findOneByTaskId(int $task) Return the first ChildTaskComment filtered by the task column
 * @method     ChildTaskComment findOneByOwner(int $owner) Return the first ChildTaskComment filtered by the owner column
 * @method     ChildTaskComment findOneByTimeCreated(string $time_created) Return the first ChildTaskComment filtered by the time_created column
 * @method     ChildTaskComment findOneByContent(string $content) Return the first ChildTaskComment filtered by the content column *

 * @method     ChildTaskComment requirePk($key, ConnectionInterface $con = null) Return the ChildTaskComment by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildTaskComment requireOne(ConnectionInterface $con = null) Return the first ChildTaskComment matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildTaskComment requireOneById(int $id) Return the first ChildTaskComment filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildTaskComment requireOneByTaskId(int $task) Return the first ChildTaskComment filtered by the task column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildTaskComment requireOneByOwner(int $owner) Return the first ChildTaskComment filtered by the owner column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildTaskComment requireOneByTimeCreated(string $time_created) Return the first ChildTaskComment filtered by the time_created column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildTaskComment requireOneByContent(string $content) Return the first ChildTaskComment filtered by the content column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildTaskComment[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildTaskComment objects based on current ModelCriteria
 * @method     ChildTaskComment[]|ObjectCollection findById(int $id) Return ChildTaskComment objects filtered by the id column
 * @method     ChildTaskComment[]|ObjectCollection findByTaskId(int $task) Return ChildTaskComment objects filtered by the task column
 * @method     ChildTaskComment[]|ObjectCollection findByOwner(int $owner) Return ChildTaskComment objects filtered by the owner column
 * @method     ChildTaskComment[]|ObjectCollection findByTimeCreated(string $time_created) Return ChildTaskComment objects filtered by the time_created column
 * @method     ChildTaskComment[]|ObjectCollection findByContent(string $content) Return ChildTaskComment objects filtered by the content column
 * @method     ChildTaskComment[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class TaskCommentQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \Base\TaskCommentQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\TaskComment', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildTaskCommentQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildTaskCommentQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildTaskCommentQuery) {
            return $criteria;
        }
        $query = new ChildTaskCommentQuery();
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
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildTaskComment|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = TaskCommentTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(TaskCommentTableMap::DATABASE_NAME);
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
     * @return ChildTaskComment A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, task, owner, time_created, content FROM task_comments WHERE id = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            /** @var ChildTaskComment $obj */
            $obj = new ChildTaskComment();
            $obj->hydrate($row);
            TaskCommentTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildTaskComment|array|mixed the result, formatted by the current formatter
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
     * $objs = $c->findPks(array(12, 56, 832), $con);
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
     * @return $this|ChildTaskCommentQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(TaskCommentTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildTaskCommentQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(TaskCommentTableMap::COL_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id > 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildTaskCommentQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(TaskCommentTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(TaskCommentTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TaskCommentTableMap::COL_ID, $id, $comparison);
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
     * @return $this|ChildTaskCommentQuery The current query, for fluid interface
     */
    public function filterByTaskId($taskId = null, $comparison = null)
    {
        if (is_array($taskId)) {
            $useMinMax = false;
            if (isset($taskId['min'])) {
                $this->addUsingAlias(TaskCommentTableMap::COL_TASK, $taskId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($taskId['max'])) {
                $this->addUsingAlias(TaskCommentTableMap::COL_TASK, $taskId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TaskCommentTableMap::COL_TASK, $taskId, $comparison);
    }

    /**
     * Filter the query on the owner column
     *
     * Example usage:
     * <code>
     * $query->filterByOwner(1234); // WHERE owner = 1234
     * $query->filterByOwner(array(12, 34)); // WHERE owner IN (12, 34)
     * $query->filterByOwner(array('min' => 12)); // WHERE owner > 12
     * </code>
     *
     * @see       filterByUser()
     *
     * @param     mixed $owner The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildTaskCommentQuery The current query, for fluid interface
     */
    public function filterByOwner($owner = null, $comparison = null)
    {
        if (is_array($owner)) {
            $useMinMax = false;
            if (isset($owner['min'])) {
                $this->addUsingAlias(TaskCommentTableMap::COL_OWNER, $owner['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($owner['max'])) {
                $this->addUsingAlias(TaskCommentTableMap::COL_OWNER, $owner['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TaskCommentTableMap::COL_OWNER, $owner, $comparison);
    }

    /**
     * Filter the query on the time_created column
     *
     * Example usage:
     * <code>
     * $query->filterByTimeCreated('2011-03-14'); // WHERE time_created = '2011-03-14'
     * $query->filterByTimeCreated('now'); // WHERE time_created = '2011-03-14'
     * $query->filterByTimeCreated(array('max' => 'yesterday')); // WHERE time_created > '2011-03-13'
     * </code>
     *
     * @param     mixed $timeCreated The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildTaskCommentQuery The current query, for fluid interface
     */
    public function filterByTimeCreated($timeCreated = null, $comparison = null)
    {
        if (is_array($timeCreated)) {
            $useMinMax = false;
            if (isset($timeCreated['min'])) {
                $this->addUsingAlias(TaskCommentTableMap::COL_TIME_CREATED, $timeCreated['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($timeCreated['max'])) {
                $this->addUsingAlias(TaskCommentTableMap::COL_TIME_CREATED, $timeCreated['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TaskCommentTableMap::COL_TIME_CREATED, $timeCreated, $comparison);
    }

    /**
     * Filter the query on the content column
     *
     * Example usage:
     * <code>
     * $query->filterByContent('fooValue');   // WHERE content = 'fooValue'
     * $query->filterByContent('%fooValue%'); // WHERE content LIKE '%fooValue%'
     * </code>
     *
     * @param     string $content The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildTaskCommentQuery The current query, for fluid interface
     */
    public function filterByContent($content = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($content)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $content)) {
                $content = str_replace('*', '%', $content);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(TaskCommentTableMap::COL_CONTENT, $content, $comparison);
    }

    /**
     * Filter the query by a related \User object
     *
     * @param \User|ObjectCollection $user The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildTaskCommentQuery The current query, for fluid interface
     */
    public function filterByUser($user, $comparison = null)
    {
        if ($user instanceof \User) {
            return $this
                ->addUsingAlias(TaskCommentTableMap::COL_OWNER, $user->getPersonId(), $comparison);
        } elseif ($user instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(TaskCommentTableMap::COL_OWNER, $user->toKeyValue('PrimaryKey', 'PersonId'), $comparison);
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
     * @return $this|ChildTaskCommentQuery The current query, for fluid interface
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
     * @return ChildTaskCommentQuery The current query, for fluid interface
     */
    public function filterByTask($task, $comparison = null)
    {
        if ($task instanceof \Task) {
            return $this
                ->addUsingAlias(TaskCommentTableMap::COL_TASK, $task->getId(), $comparison);
        } elseif ($task instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(TaskCommentTableMap::COL_TASK, $task->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return $this|ChildTaskCommentQuery The current query, for fluid interface
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
     * @param   ChildTaskComment $taskComment Object to remove from the list of results
     *
     * @return $this|ChildTaskCommentQuery The current query, for fluid interface
     */
    public function prune($taskComment = null)
    {
        if ($taskComment) {
            $this->addUsingAlias(TaskCommentTableMap::COL_ID, $taskComment->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the task_comments table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(TaskCommentTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            TaskCommentTableMap::clearInstancePool();
            TaskCommentTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(TaskCommentTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(TaskCommentTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            TaskCommentTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            TaskCommentTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // TaskCommentQuery
