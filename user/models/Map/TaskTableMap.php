<?php

namespace Map;

use \Task;
use \TaskQuery;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\InstancePoolTrait;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\DataFetcher\DataFetcherInterface;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\RelationMap;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Map\TableMapTrait;


/**
 * This class defines the structure of the 'tasks' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class TaskTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = '.Map.TaskTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'default';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'tasks';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Task';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Task';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 11;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 11;

    /**
     * the column name for the id field
     */
    const COL_ID = 'tasks.id';

    /**
     * the column name for the title field
     */
    const COL_TITLE = 'tasks.title';

    /**
     * the column name for the owner field
     */
    const COL_OWNER = 'tasks.owner';

    /**
     * the column name for the assignee field
     */
    const COL_ASSIGNEE = 'tasks.assignee';

    /**
     * the column name for the project field
     */
    const COL_PROJECT = 'tasks.project';

    /**
     * the column name for the start_date field
     */
    const COL_START_DATE = 'tasks.start_date';

    /**
     * the column name for the estimated_time field
     */
    const COL_ESTIMATED_TIME = 'tasks.estimated_time';

    /**
     * the column name for the priority field
     */
    const COL_PRIORITY = 'tasks.priority';

    /**
     * the column name for the time_created field
     */
    const COL_TIME_CREATED = 'tasks.time_created';

    /**
     * the column name for the time_completed field
     */
    const COL_TIME_COMPLETED = 'tasks.time_completed';

    /**
     * the column name for the description field
     */
    const COL_DESCRIPTION = 'tasks.description';

    /**
     * The default string format for model objects of the related table
     */
    const DEFAULT_STRING_FORMAT = 'YAML';

    // sortable behavior
    /**
     * rank column
     */
    const RANK_COL = "tasks.priority";


        /**
    * If defined, the `SCOPE_COL` contains a json_encoded array with all columns.
    * @var boolean
    */
    const MULTI_SCOPE_COL = true;


    /**
    * Scope column for the set
    */
    const SCOPE_COL = '["tasks.ASSIGNEE","tasks.START_DATE"]';


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        self::TYPE_PHPNAME       => array('Id', 'Title', 'OwnerId', 'AssigneeId', 'ProjectId', 'StartDate', 'EstimatedTime', 'Priority', 'TimeCreated', 'TimeCompleted', 'Description', ),
        self::TYPE_CAMELNAME     => array('id', 'title', 'ownerId', 'assigneeId', 'projectId', 'startDate', 'estimatedTime', 'priority', 'timeCreated', 'timeCompleted', 'description', ),
        self::TYPE_COLNAME       => array(TaskTableMap::COL_ID, TaskTableMap::COL_TITLE, TaskTableMap::COL_OWNER, TaskTableMap::COL_ASSIGNEE, TaskTableMap::COL_PROJECT, TaskTableMap::COL_START_DATE, TaskTableMap::COL_ESTIMATED_TIME, TaskTableMap::COL_PRIORITY, TaskTableMap::COL_TIME_CREATED, TaskTableMap::COL_TIME_COMPLETED, TaskTableMap::COL_DESCRIPTION, ),
        self::TYPE_FIELDNAME     => array('id', 'title', 'owner', 'assignee', 'project', 'start_date', 'estimated_time', 'priority', 'time_created', 'time_completed', 'description', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'Title' => 1, 'OwnerId' => 2, 'AssigneeId' => 3, 'ProjectId' => 4, 'StartDate' => 5, 'EstimatedTime' => 6, 'Priority' => 7, 'TimeCreated' => 8, 'TimeCompleted' => 9, 'Description' => 10, ),
        self::TYPE_CAMELNAME     => array('id' => 0, 'title' => 1, 'ownerId' => 2, 'assigneeId' => 3, 'projectId' => 4, 'startDate' => 5, 'estimatedTime' => 6, 'priority' => 7, 'timeCreated' => 8, 'timeCompleted' => 9, 'description' => 10, ),
        self::TYPE_COLNAME       => array(TaskTableMap::COL_ID => 0, TaskTableMap::COL_TITLE => 1, TaskTableMap::COL_OWNER => 2, TaskTableMap::COL_ASSIGNEE => 3, TaskTableMap::COL_PROJECT => 4, TaskTableMap::COL_START_DATE => 5, TaskTableMap::COL_ESTIMATED_TIME => 6, TaskTableMap::COL_PRIORITY => 7, TaskTableMap::COL_TIME_CREATED => 8, TaskTableMap::COL_TIME_COMPLETED => 9, TaskTableMap::COL_DESCRIPTION => 10, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'title' => 1, 'owner' => 2, 'assignee' => 3, 'project' => 4, 'start_date' => 5, 'estimated_time' => 6, 'priority' => 7, 'time_created' => 8, 'time_completed' => 9, 'description' => 10, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, )
    );

    /**
     * Initialize the table attributes and columns
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('tasks');
        $this->setPhpName('Task');
        $this->setIdentifierQuoting(false);
        $this->setClassName('\\Task');
        $this->setPackage('');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('tasks_id_seq');
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('title', 'Title', 'VARCHAR', true, 128, null);
        $this->addForeignKey('owner', 'OwnerId', 'INTEGER', 'users', 'person', true, null, null);
        $this->addForeignKey('assignee', 'AssigneeId', 'INTEGER', 'users', 'person', true, null, null);
        $this->addForeignKey('project', 'ProjectId', 'INTEGER', 'projects', 'id', false, null, null);
        $this->addColumn('start_date', 'StartDate', 'DATE', true, null, 'now()');
        $this->addColumn('estimated_time', 'EstimatedTime', 'DOUBLE', false, 53, 0);
        $this->addColumn('priority', 'Priority', 'INTEGER', true, null, null);
        $this->addColumn('time_created', 'TimeCreated', 'TIMESTAMP', true, null, 'now()');
        $this->addColumn('time_completed', 'TimeCompleted', 'TIMESTAMP', false, null, null);
        $this->addColumn('description', 'Description', 'LONGVARCHAR', false, null, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('UserRelatedByAssigneeId', '\\User', RelationMap::MANY_TO_ONE, array (
  0 =>
  array (
    0 => ':assignee',
    1 => ':person',
  ),
), 'CASCADE', 'CASCADE', null, false);
        $this->addRelation('UserRelatedByOwnerId', '\\User', RelationMap::MANY_TO_ONE, array (
  0 =>
  array (
    0 => ':owner',
    1 => ':person',
  ),
), 'RESTRICT', 'CASCADE', null, false);
        $this->addRelation('Project', '\\Project', RelationMap::MANY_TO_ONE, array (
  0 =>
  array (
    0 => ':project',
    1 => ':id',
  ),
), 'CASCADE', 'CASCADE', null, false);
        $this->addRelation('TaskComment', '\\TaskComment', RelationMap::ONE_TO_MANY, array (
  0 =>
  array (
    0 => ':task',
    1 => ':id',
  ),
), 'CASCADE', 'CASCADE', 'TaskComments', false);
        $this->addRelation('TaskTimeRecord', '\\TaskTimeRecord', RelationMap::ONE_TO_MANY, array (
  0 =>
  array (
    0 => ':task',
    1 => ':id',
  ),
), 'CASCADE', 'CASCADE', 'TaskTimeRecords', false);
    } // buildRelations()

    /**
     *
     * Gets the list of behaviors registered for this table
     *
     * @return array Associative array (name => parameters) of behaviors
     */
    public function getBehaviors()
    {
        return array(
            'sortable' => array('rank_column' => 'priority', 'use_scope' => 'true', 'scope_column' => 'assignee,start_date', ),
        );
    } // getBehaviors()
    /**
     * Method to invalidate the instance pool of all tables related to tasks     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in related instance pools,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        TaskCommentTableMap::clearInstancePool();
        TaskTimeRecordTableMap::clearInstancePool();
    }

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return string The primary key hash of the row
     */
    public static function getPrimaryKeyHashFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        // If the PK cannot be derived from the row, return NULL.
        if ($row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] === null) {
            return null;
        }

        return (string) $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
    }

    /**
     * Retrieves the primary key from the DB resultset row
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, an array of the primary key columns will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return mixed The primary key of the row
     */
    public static function getPrimaryKeyFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        return (int) $row[
            $indexType == TableMap::TYPE_NUM
                ? 0 + $offset
                : self::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)
        ];
    }

    /**
     * The class that the tableMap will make instances of.
     *
     * If $withPrefix is true, the returned path
     * uses a dot-path notation which is translated into a path
     * relative to a location on the PHP include_path.
     * (e.g. path.to.MyClass -> 'path/to/MyClass.php')
     *
     * @param boolean $withPrefix Whether or not to return the path with the class name
     * @return string path.to.ClassName
     */
    public static function getOMClass($withPrefix = true)
    {
        return $withPrefix ? TaskTableMap::CLASS_DEFAULT : TaskTableMap::OM_CLASS;
    }

    /**
     * Populates an object of the default type or an object that inherit from the default.
     *
     * @param array  $row       row returned by DataFetcher->fetch().
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                 One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     * @return array           (Task object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = TaskTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = TaskTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + TaskTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = TaskTableMap::OM_CLASS;
            /** @var Task $obj */
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            TaskTableMap::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }

    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @param DataFetcherInterface $dataFetcher
     * @return array
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function populateObjects(DataFetcherInterface $dataFetcher)
    {
        $results = array();

        // set the class once to avoid overhead in the loop
        $cls = static::getOMClass(false);
        // populate the object(s)
        while ($row = $dataFetcher->fetch()) {
            $key = TaskTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = TaskTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                /** @var Task $obj */
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                TaskTableMap::addInstanceToPool($obj, $key);
            } // if key exists
        }

        return $results;
    }
    /**
     * Add all the columns needed to create a new object.
     *
     * Note: any columns that were marked with lazyLoad="true" in the
     * XML schema will not be added to the select list and only loaded
     * on demand.
     *
     * @param Criteria $criteria object containing the columns to add.
     * @param string   $alias    optional table alias
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(TaskTableMap::COL_ID);
            $criteria->addSelectColumn(TaskTableMap::COL_TITLE);
            $criteria->addSelectColumn(TaskTableMap::COL_OWNER);
            $criteria->addSelectColumn(TaskTableMap::COL_ASSIGNEE);
            $criteria->addSelectColumn(TaskTableMap::COL_PROJECT);
            $criteria->addSelectColumn(TaskTableMap::COL_START_DATE);
            $criteria->addSelectColumn(TaskTableMap::COL_ESTIMATED_TIME);
            $criteria->addSelectColumn(TaskTableMap::COL_PRIORITY);
            $criteria->addSelectColumn(TaskTableMap::COL_TIME_CREATED);
            $criteria->addSelectColumn(TaskTableMap::COL_TIME_COMPLETED);
            $criteria->addSelectColumn(TaskTableMap::COL_DESCRIPTION);
        } else {
            $criteria->addSelectColumn($alias . '.id');
            $criteria->addSelectColumn($alias . '.title');
            $criteria->addSelectColumn($alias . '.owner');
            $criteria->addSelectColumn($alias . '.assignee');
            $criteria->addSelectColumn($alias . '.project');
            $criteria->addSelectColumn($alias . '.start_date');
            $criteria->addSelectColumn($alias . '.estimated_time');
            $criteria->addSelectColumn($alias . '.priority');
            $criteria->addSelectColumn($alias . '.time_created');
            $criteria->addSelectColumn($alias . '.time_completed');
            $criteria->addSelectColumn($alias . '.description');
        }
    }

    /**
     * Returns the TableMap related to this object.
     * This method is not needed for general use but a specific application could have a need.
     * @return TableMap
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function getTableMap()
    {
        return Propel::getServiceContainer()->getDatabaseMap(TaskTableMap::DATABASE_NAME)->getTable(TaskTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
        $dbMap = Propel::getServiceContainer()->getDatabaseMap(TaskTableMap::DATABASE_NAME);
        if (!$dbMap->hasTable(TaskTableMap::TABLE_NAME)) {
            $dbMap->addTableObject(new TaskTableMap());
        }
    }

    /**
     * Performs a DELETE on the database, given a Task or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or Task object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param  ConnectionInterface $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
     public static function doDelete($values, ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(TaskTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Task) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(TaskTableMap::DATABASE_NAME);
            $criteria->add(TaskTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = TaskQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) {
            TaskTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) {
                TaskTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the tasks table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return TaskQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a Task or Criteria object.
     *
     * @param mixed               $criteria Criteria or Task object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(TaskTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from Task object
        }

        if ($criteria->containsKey(TaskTableMap::COL_ID) && $criteria->keyContainsValue(TaskTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.TaskTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = TaskQuery::create()->mergeWith($criteria);

        // use transaction because $criteria could contain info
        // for more than one table (I guess, conceivably)
        return $con->transaction(function () use ($con, $query) {
            return $query->doInsert($con);
        });
    }

} // TaskTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
TaskTableMap::buildTableMap();
