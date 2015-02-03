<?php

namespace Base;

use \Task as ChildTask;
use \TaskQuery as ChildTaskQuery;
use \Exception;
use \PDO;
use Map\TaskTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'tasks' table.
 *
 *
 *
 * @method     ChildTaskQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildTaskQuery orderByTitle($order = Criteria::ASC) Order by the title column
 * @method     ChildTaskQuery orderByOwnerId($order = Criteria::ASC) Order by the owner column
 * @method     ChildTaskQuery orderByAssigneeId($order = Criteria::ASC) Order by the assignee column
 * @method     ChildTaskQuery orderByProjectId($order = Criteria::ASC) Order by the project column
 * @method     ChildTaskQuery orderByStartDate($order = Criteria::ASC) Order by the start_date column
 * @method     ChildTaskQuery orderByEstimatedTime($order = Criteria::ASC) Order by the estimated_time column
 * @method     ChildTaskQuery orderByPriority($order = Criteria::ASC) Order by the priority column
 * @method     ChildTaskQuery orderByTimeCreated($order = Criteria::ASC) Order by the time_created column
 * @method     ChildTaskQuery orderByTimeCompleted($order = Criteria::ASC) Order by the time_completed column
 * @method     ChildTaskQuery orderByDescription($order = Criteria::ASC) Order by the description column
 *
 * @method     ChildTaskQuery groupById() Group by the id column
 * @method     ChildTaskQuery groupByTitle() Group by the title column
 * @method     ChildTaskQuery groupByOwnerId() Group by the owner column
 * @method     ChildTaskQuery groupByAssigneeId() Group by the assignee column
 * @method     ChildTaskQuery groupByProjectId() Group by the project column
 * @method     ChildTaskQuery groupByStartDate() Group by the start_date column
 * @method     ChildTaskQuery groupByEstimatedTime() Group by the estimated_time column
 * @method     ChildTaskQuery groupByPriority() Group by the priority column
 * @method     ChildTaskQuery groupByTimeCreated() Group by the time_created column
 * @method     ChildTaskQuery groupByTimeCompleted() Group by the time_completed column
 * @method     ChildTaskQuery groupByDescription() Group by the description column
 *
 * @method     ChildTaskQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildTaskQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildTaskQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildTaskQuery leftJoinUserRelatedByAssigneeId($relationAlias = null) Adds a LEFT JOIN clause to the query using the UserRelatedByAssigneeId relation
 * @method     ChildTaskQuery rightJoinUserRelatedByAssigneeId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the UserRelatedByAssigneeId relation
 * @method     ChildTaskQuery innerJoinUserRelatedByAssigneeId($relationAlias = null) Adds a INNER JOIN clause to the query using the UserRelatedByAssigneeId relation
 *
 * @method     ChildTaskQuery leftJoinUserRelatedByOwnerId($relationAlias = null) Adds a LEFT JOIN clause to the query using the UserRelatedByOwnerId relation
 * @method     ChildTaskQuery rightJoinUserRelatedByOwnerId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the UserRelatedByOwnerId relation
 * @method     ChildTaskQuery innerJoinUserRelatedByOwnerId($relationAlias = null) Adds a INNER JOIN clause to the query using the UserRelatedByOwnerId relation
 *
 * @method     ChildTaskQuery leftJoinProject($relationAlias = null) Adds a LEFT JOIN clause to the query using the Project relation
 * @method     ChildTaskQuery rightJoinProject($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Project relation
 * @method     ChildTaskQuery innerJoinProject($relationAlias = null) Adds a INNER JOIN clause to the query using the Project relation
 *
 * @method     ChildTaskQuery leftJoinTaskComment($relationAlias = null) Adds a LEFT JOIN clause to the query using the TaskComment relation
 * @method     ChildTaskQuery rightJoinTaskComment($relationAlias = null) Adds a RIGHT JOIN clause to the query using the TaskComment relation
 * @method     ChildTaskQuery innerJoinTaskComment($relationAlias = null) Adds a INNER JOIN clause to the query using the TaskComment relation
 *
 * @method     ChildTaskQuery leftJoinTaskTimeRecord($relationAlias = null) Adds a LEFT JOIN clause to the query using the TaskTimeRecord relation
 * @method     ChildTaskQuery rightJoinTaskTimeRecord($relationAlias = null) Adds a RIGHT JOIN clause to the query using the TaskTimeRecord relation
 * @method     ChildTaskQuery innerJoinTaskTimeRecord($relationAlias = null) Adds a INNER JOIN clause to the query using the TaskTimeRecord relation
 *
 * @method     \UserQuery|\ProjectQuery|\TaskCommentQuery|\TaskTimeRecordQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildTask findOne(ConnectionInterface $con = null) Return the first ChildTask matching the query
 * @method     ChildTask findOneOrCreate(ConnectionInterface $con = null) Return the first ChildTask matching the query, or a new ChildTask object populated from the query conditions when no match is found
 *
 * @method     ChildTask findOneById(int $id) Return the first ChildTask filtered by the id column
 * @method     ChildTask findOneByTitle(string $title) Return the first ChildTask filtered by the title column
 * @method     ChildTask findOneByOwnerId(int $owner) Return the first ChildTask filtered by the owner column
 * @method     ChildTask findOneByAssigneeId(int $assignee) Return the first ChildTask filtered by the assignee column
 * @method     ChildTask findOneByProjectId(int $project) Return the first ChildTask filtered by the project column
 * @method     ChildTask findOneByStartDate(string $start_date) Return the first ChildTask filtered by the start_date column
 * @method     ChildTask findOneByEstimatedTime(double $estimated_time) Return the first ChildTask filtered by the estimated_time column
 * @method     ChildTask findOneByPriority(int $priority) Return the first ChildTask filtered by the priority column
 * @method     ChildTask findOneByTimeCreated(string $time_created) Return the first ChildTask filtered by the time_created column
 * @method     ChildTask findOneByTimeCompleted(string $time_completed) Return the first ChildTask filtered by the time_completed column
 * @method     ChildTask findOneByDescription(string $description) Return the first ChildTask filtered by the description column *

 * @method     ChildTask requirePk($key, ConnectionInterface $con = null) Return the ChildTask by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildTask requireOne(ConnectionInterface $con = null) Return the first ChildTask matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildTask requireOneById(int $id) Return the first ChildTask filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildTask requireOneByTitle(string $title) Return the first ChildTask filtered by the title column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildTask requireOneByOwnerId(int $owner) Return the first ChildTask filtered by the owner column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildTask requireOneByAssigneeId(int $assignee) Return the first ChildTask filtered by the assignee column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildTask requireOneByProjectId(int $project) Return the first ChildTask filtered by the project column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildTask requireOneByStartDate(string $start_date) Return the first ChildTask filtered by the start_date column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildTask requireOneByEstimatedTime(double $estimated_time) Return the first ChildTask filtered by the estimated_time column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildTask requireOneByPriority(int $priority) Return the first ChildTask filtered by the priority column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildTask requireOneByTimeCreated(string $time_created) Return the first ChildTask filtered by the time_created column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildTask requireOneByTimeCompleted(string $time_completed) Return the first ChildTask filtered by the time_completed column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildTask requireOneByDescription(string $description) Return the first ChildTask filtered by the description column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildTask[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildTask objects based on current ModelCriteria
 * @method     ChildTask[]|ObjectCollection findById(int $id) Return ChildTask objects filtered by the id column
 * @method     ChildTask[]|ObjectCollection findByTitle(string $title) Return ChildTask objects filtered by the title column
 * @method     ChildTask[]|ObjectCollection findByOwnerId(int $owner) Return ChildTask objects filtered by the owner column
 * @method     ChildTask[]|ObjectCollection findByAssigneeId(int $assignee) Return ChildTask objects filtered by the assignee column
 * @method     ChildTask[]|ObjectCollection findByProjectId(int $project) Return ChildTask objects filtered by the project column
 * @method     ChildTask[]|ObjectCollection findByStartDate(string $start_date) Return ChildTask objects filtered by the start_date column
 * @method     ChildTask[]|ObjectCollection findByEstimatedTime(double $estimated_time) Return ChildTask objects filtered by the estimated_time column
 * @method     ChildTask[]|ObjectCollection findByPriority(int $priority) Return ChildTask objects filtered by the priority column
 * @method     ChildTask[]|ObjectCollection findByTimeCreated(string $time_created) Return ChildTask objects filtered by the time_created column
 * @method     ChildTask[]|ObjectCollection findByTimeCompleted(string $time_completed) Return ChildTask objects filtered by the time_completed column
 * @method     ChildTask[]|ObjectCollection findByDescription(string $description) Return ChildTask objects filtered by the description column
 * @method     ChildTask[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class TaskQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \Base\TaskQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Task', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildTaskQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildTaskQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildTaskQuery) {
            return $criteria;
        }
        $query = new ChildTaskQuery();
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
     * @return ChildTask|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = TaskTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(TaskTableMap::DATABASE_NAME);
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
     * @return ChildTask A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, title, owner, assignee, project, start_date, estimated_time, priority, time_created, time_completed, description FROM tasks WHERE id = :p0';
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
            /** @var ChildTask $obj */
            $obj = new ChildTask();
            $obj->hydrate($row);
            TaskTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildTask|array|mixed the result, formatted by the current formatter
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
     * @return $this|ChildTaskQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(TaskTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildTaskQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(TaskTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return $this|ChildTaskQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(TaskTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(TaskTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TaskTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the title column
     *
     * Example usage:
     * <code>
     * $query->filterByTitle('fooValue');   // WHERE title = 'fooValue'
     * $query->filterByTitle('%fooValue%'); // WHERE title LIKE '%fooValue%'
     * </code>
     *
     * @param     string $title The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildTaskQuery The current query, for fluid interface
     */
    public function filterByTitle($title = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($title)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $title)) {
                $title = str_replace('*', '%', $title);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(TaskTableMap::COL_TITLE, $title, $comparison);
    }

    /**
     * Filter the query on the owner column
     *
     * Example usage:
     * <code>
     * $query->filterByOwnerId(1234); // WHERE owner = 1234
     * $query->filterByOwnerId(array(12, 34)); // WHERE owner IN (12, 34)
     * $query->filterByOwnerId(array('min' => 12)); // WHERE owner > 12
     * </code>
     *
     * @see       filterByUserRelatedByOwnerId()
     *
     * @param     mixed $ownerId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildTaskQuery The current query, for fluid interface
     */
    public function filterByOwnerId($ownerId = null, $comparison = null)
    {
        if (is_array($ownerId)) {
            $useMinMax = false;
            if (isset($ownerId['min'])) {
                $this->addUsingAlias(TaskTableMap::COL_OWNER, $ownerId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($ownerId['max'])) {
                $this->addUsingAlias(TaskTableMap::COL_OWNER, $ownerId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TaskTableMap::COL_OWNER, $ownerId, $comparison);
    }

    /**
     * Filter the query on the assignee column
     *
     * Example usage:
     * <code>
     * $query->filterByAssigneeId(1234); // WHERE assignee = 1234
     * $query->filterByAssigneeId(array(12, 34)); // WHERE assignee IN (12, 34)
     * $query->filterByAssigneeId(array('min' => 12)); // WHERE assignee > 12
     * </code>
     *
     * @see       filterByUserRelatedByAssigneeId()
     *
     * @param     mixed $assigneeId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildTaskQuery The current query, for fluid interface
     */
    public function filterByAssigneeId($assigneeId = null, $comparison = null)
    {
        if (is_array($assigneeId)) {
            $useMinMax = false;
            if (isset($assigneeId['min'])) {
                $this->addUsingAlias(TaskTableMap::COL_ASSIGNEE, $assigneeId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($assigneeId['max'])) {
                $this->addUsingAlias(TaskTableMap::COL_ASSIGNEE, $assigneeId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TaskTableMap::COL_ASSIGNEE, $assigneeId, $comparison);
    }

    /**
     * Filter the query on the project column
     *
     * Example usage:
     * <code>
     * $query->filterByProjectId(1234); // WHERE project = 1234
     * $query->filterByProjectId(array(12, 34)); // WHERE project IN (12, 34)
     * $query->filterByProjectId(array('min' => 12)); // WHERE project > 12
     * </code>
     *
     * @see       filterByProject()
     *
     * @param     mixed $projectId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildTaskQuery The current query, for fluid interface
     */
    public function filterByProjectId($projectId = null, $comparison = null)
    {
        if (is_array($projectId)) {
            $useMinMax = false;
            if (isset($projectId['min'])) {
                $this->addUsingAlias(TaskTableMap::COL_PROJECT, $projectId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($projectId['max'])) {
                $this->addUsingAlias(TaskTableMap::COL_PROJECT, $projectId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TaskTableMap::COL_PROJECT, $projectId, $comparison);
    }

    /**
     * Filter the query on the start_date column
     *
     * Example usage:
     * <code>
     * $query->filterByStartDate('2011-03-14'); // WHERE start_date = '2011-03-14'
     * $query->filterByStartDate('now'); // WHERE start_date = '2011-03-14'
     * $query->filterByStartDate(array('max' => 'yesterday')); // WHERE start_date > '2011-03-13'
     * </code>
     *
     * @param     mixed $startDate The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildTaskQuery The current query, for fluid interface
     */
    public function filterByStartDate($startDate = null, $comparison = null)
    {
        if (is_array($startDate)) {
            $useMinMax = false;
            if (isset($startDate['min'])) {
                $this->addUsingAlias(TaskTableMap::COL_START_DATE, $startDate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($startDate['max'])) {
                $this->addUsingAlias(TaskTableMap::COL_START_DATE, $startDate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TaskTableMap::COL_START_DATE, $startDate, $comparison);
    }

    /**
     * Filter the query on the estimated_time column
     *
     * Example usage:
     * <code>
     * $query->filterByEstimatedTime(1234); // WHERE estimated_time = 1234
     * $query->filterByEstimatedTime(array(12, 34)); // WHERE estimated_time IN (12, 34)
     * $query->filterByEstimatedTime(array('min' => 12)); // WHERE estimated_time > 12
     * </code>
     *
     * @param     mixed $estimatedTime The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildTaskQuery The current query, for fluid interface
     */
    public function filterByEstimatedTime($estimatedTime = null, $comparison = null)
    {
        if (is_array($estimatedTime)) {
            $useMinMax = false;
            if (isset($estimatedTime['min'])) {
                $this->addUsingAlias(TaskTableMap::COL_ESTIMATED_TIME, $estimatedTime['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($estimatedTime['max'])) {
                $this->addUsingAlias(TaskTableMap::COL_ESTIMATED_TIME, $estimatedTime['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TaskTableMap::COL_ESTIMATED_TIME, $estimatedTime, $comparison);
    }

    /**
     * Filter the query on the priority column
     *
     * Example usage:
     * <code>
     * $query->filterByPriority(1234); // WHERE priority = 1234
     * $query->filterByPriority(array(12, 34)); // WHERE priority IN (12, 34)
     * $query->filterByPriority(array('min' => 12)); // WHERE priority > 12
     * </code>
     *
     * @param     mixed $priority The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildTaskQuery The current query, for fluid interface
     */
    public function filterByPriority($priority = null, $comparison = null)
    {
        if (is_array($priority)) {
            $useMinMax = false;
            if (isset($priority['min'])) {
                $this->addUsingAlias(TaskTableMap::COL_PRIORITY, $priority['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($priority['max'])) {
                $this->addUsingAlias(TaskTableMap::COL_PRIORITY, $priority['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TaskTableMap::COL_PRIORITY, $priority, $comparison);
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
     * @return $this|ChildTaskQuery The current query, for fluid interface
     */
    public function filterByTimeCreated($timeCreated = null, $comparison = null)
    {
        if (is_array($timeCreated)) {
            $useMinMax = false;
            if (isset($timeCreated['min'])) {
                $this->addUsingAlias(TaskTableMap::COL_TIME_CREATED, $timeCreated['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($timeCreated['max'])) {
                $this->addUsingAlias(TaskTableMap::COL_TIME_CREATED, $timeCreated['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TaskTableMap::COL_TIME_CREATED, $timeCreated, $comparison);
    }

    /**
     * Filter the query on the time_completed column
     *
     * Example usage:
     * <code>
     * $query->filterByTimeCompleted('2011-03-14'); // WHERE time_completed = '2011-03-14'
     * $query->filterByTimeCompleted('now'); // WHERE time_completed = '2011-03-14'
     * $query->filterByTimeCompleted(array('max' => 'yesterday')); // WHERE time_completed > '2011-03-13'
     * </code>
     *
     * @param     mixed $timeCompleted The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildTaskQuery The current query, for fluid interface
     */
    public function filterByTimeCompleted($timeCompleted = null, $comparison = null)
    {
        if (is_array($timeCompleted)) {
            $useMinMax = false;
            if (isset($timeCompleted['min'])) {
                $this->addUsingAlias(TaskTableMap::COL_TIME_COMPLETED, $timeCompleted['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($timeCompleted['max'])) {
                $this->addUsingAlias(TaskTableMap::COL_TIME_COMPLETED, $timeCompleted['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TaskTableMap::COL_TIME_COMPLETED, $timeCompleted, $comparison);
    }

    /**
     * Filter the query on the description column
     *
     * Example usage:
     * <code>
     * $query->filterByDescription('fooValue');   // WHERE description = 'fooValue'
     * $query->filterByDescription('%fooValue%'); // WHERE description LIKE '%fooValue%'
     * </code>
     *
     * @param     string $description The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildTaskQuery The current query, for fluid interface
     */
    public function filterByDescription($description = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($description)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $description)) {
                $description = str_replace('*', '%', $description);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(TaskTableMap::COL_DESCRIPTION, $description, $comparison);
    }

    /**
     * Filter the query by a related \User object
     *
     * @param \User|ObjectCollection $user The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildTaskQuery The current query, for fluid interface
     */
    public function filterByUserRelatedByAssigneeId($user, $comparison = null)
    {
        if ($user instanceof \User) {
            return $this
                ->addUsingAlias(TaskTableMap::COL_ASSIGNEE, $user->getPersonId(), $comparison);
        } elseif ($user instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(TaskTableMap::COL_ASSIGNEE, $user->toKeyValue('PrimaryKey', 'PersonId'), $comparison);
        } else {
            throw new PropelException('filterByUserRelatedByAssigneeId() only accepts arguments of type \User or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the UserRelatedByAssigneeId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildTaskQuery The current query, for fluid interface
     */
    public function joinUserRelatedByAssigneeId($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('UserRelatedByAssigneeId');

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
            $this->addJoinObject($join, 'UserRelatedByAssigneeId');
        }

        return $this;
    }

    /**
     * Use the UserRelatedByAssigneeId relation User object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \UserQuery A secondary query class using the current class as primary query
     */
    public function useUserRelatedByAssigneeIdQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinUserRelatedByAssigneeId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'UserRelatedByAssigneeId', '\UserQuery');
    }

    /**
     * Filter the query by a related \User object
     *
     * @param \User|ObjectCollection $user The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildTaskQuery The current query, for fluid interface
     */
    public function filterByUserRelatedByOwnerId($user, $comparison = null)
    {
        if ($user instanceof \User) {
            return $this
                ->addUsingAlias(TaskTableMap::COL_OWNER, $user->getPersonId(), $comparison);
        } elseif ($user instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(TaskTableMap::COL_OWNER, $user->toKeyValue('PrimaryKey', 'PersonId'), $comparison);
        } else {
            throw new PropelException('filterByUserRelatedByOwnerId() only accepts arguments of type \User or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the UserRelatedByOwnerId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildTaskQuery The current query, for fluid interface
     */
    public function joinUserRelatedByOwnerId($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('UserRelatedByOwnerId');

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
            $this->addJoinObject($join, 'UserRelatedByOwnerId');
        }

        return $this;
    }

    /**
     * Use the UserRelatedByOwnerId relation User object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \UserQuery A secondary query class using the current class as primary query
     */
    public function useUserRelatedByOwnerIdQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinUserRelatedByOwnerId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'UserRelatedByOwnerId', '\UserQuery');
    }

    /**
     * Filter the query by a related \Project object
     *
     * @param \Project|ObjectCollection $project The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildTaskQuery The current query, for fluid interface
     */
    public function filterByProject($project, $comparison = null)
    {
        if ($project instanceof \Project) {
            return $this
                ->addUsingAlias(TaskTableMap::COL_PROJECT, $project->getId(), $comparison);
        } elseif ($project instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(TaskTableMap::COL_PROJECT, $project->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByProject() only accepts arguments of type \Project or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Project relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildTaskQuery The current query, for fluid interface
     */
    public function joinProject($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Project');

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
            $this->addJoinObject($join, 'Project');
        }

        return $this;
    }

    /**
     * Use the Project relation Project object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \ProjectQuery A secondary query class using the current class as primary query
     */
    public function useProjectQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinProject($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Project', '\ProjectQuery');
    }

    /**
     * Filter the query by a related \TaskComment object
     *
     * @param \TaskComment|ObjectCollection $taskComment the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildTaskQuery The current query, for fluid interface
     */
    public function filterByTaskComment($taskComment, $comparison = null)
    {
        if ($taskComment instanceof \TaskComment) {
            return $this
                ->addUsingAlias(TaskTableMap::COL_ID, $taskComment->getTaskId(), $comparison);
        } elseif ($taskComment instanceof ObjectCollection) {
            return $this
                ->useTaskCommentQuery()
                ->filterByPrimaryKeys($taskComment->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByTaskComment() only accepts arguments of type \TaskComment or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the TaskComment relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildTaskQuery The current query, for fluid interface
     */
    public function joinTaskComment($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('TaskComment');

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
            $this->addJoinObject($join, 'TaskComment');
        }

        return $this;
    }

    /**
     * Use the TaskComment relation TaskComment object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \TaskCommentQuery A secondary query class using the current class as primary query
     */
    public function useTaskCommentQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinTaskComment($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'TaskComment', '\TaskCommentQuery');
    }

    /**
     * Filter the query by a related \TaskTimeRecord object
     *
     * @param \TaskTimeRecord|ObjectCollection $taskTimeRecord the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildTaskQuery The current query, for fluid interface
     */
    public function filterByTaskTimeRecord($taskTimeRecord, $comparison = null)
    {
        if ($taskTimeRecord instanceof \TaskTimeRecord) {
            return $this
                ->addUsingAlias(TaskTableMap::COL_ID, $taskTimeRecord->getTaskId(), $comparison);
        } elseif ($taskTimeRecord instanceof ObjectCollection) {
            return $this
                ->useTaskTimeRecordQuery()
                ->filterByPrimaryKeys($taskTimeRecord->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByTaskTimeRecord() only accepts arguments of type \TaskTimeRecord or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the TaskTimeRecord relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildTaskQuery The current query, for fluid interface
     */
    public function joinTaskTimeRecord($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('TaskTimeRecord');

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
            $this->addJoinObject($join, 'TaskTimeRecord');
        }

        return $this;
    }

    /**
     * Use the TaskTimeRecord relation TaskTimeRecord object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \TaskTimeRecordQuery A secondary query class using the current class as primary query
     */
    public function useTaskTimeRecordQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinTaskTimeRecord($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'TaskTimeRecord', '\TaskTimeRecordQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildTask $task Object to remove from the list of results
     *
     * @return $this|ChildTaskQuery The current query, for fluid interface
     */
    public function prune($task = null)
    {
        if ($task) {
            $this->addUsingAlias(TaskTableMap::COL_ID, $task->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the tasks table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(TaskTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            TaskTableMap::clearInstancePool();
            TaskTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(TaskTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(TaskTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            TaskTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            TaskTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // TaskQuery
