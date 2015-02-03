<?php

namespace Base;

use \Project as ChildProject;
use \ProjectQuery as ChildProjectQuery;
use \Task as ChildTask;
use \TaskComment as ChildTaskComment;
use \TaskCommentQuery as ChildTaskCommentQuery;
use \TaskQuery as ChildTaskQuery;
use \TaskTimeRecord as ChildTaskTimeRecord;
use \TaskTimeRecordQuery as ChildTaskTimeRecordQuery;
use \User as ChildUser;
use \UserQuery as ChildUserQuery;
use \DateTime;
use \Exception;
use \PDO;
use Map\TaskTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\BadMethodCallException;
use Propel\Runtime\Exception\LogicException;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Parser\AbstractParser;
use Propel\Runtime\Util\PropelDateTime;

/**
 * Base class that represents a row from the 'tasks' table.
 *
 *
 *
* @package    propel.generator..Base
*/
abstract class Task implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Map\\TaskTableMap';


    /**
     * attribute to determine if this object has previously been saved.
     * @var boolean
     */
    protected $new = true;

    /**
     * attribute to determine whether this object has been deleted.
     * @var boolean
     */
    protected $deleted = false;

    /**
     * The columns that have been modified in current object.
     * Tracking modified columns allows us to only update modified columns.
     * @var array
     */
    protected $modifiedColumns = array();

    /**
     * The (virtual) columns that are added at runtime
     * The formatters can add supplementary columns based on a resultset
     * @var array
     */
    protected $virtualColumns = array();

    /**
     * The value for the id field.
     * @var        int
     */
    protected $id;

    /**
     * The value for the title field.
     * @var        string
     */
    protected $title;

    /**
     * The value for the owner field.
     * @var        int
     */
    protected $owner;

    /**
     * The value for the assignee field.
     * @var        int
     */
    protected $assignee;

    /**
     * The value for the project field.
     * @var        int
     */
    protected $project;

    /**
     * The value for the start_date field.
     * Note: this column has a database default value of: (expression) now()
     * @var        \DateTime
     */
    protected $start_date;

    /**
     * The value for the estimated_time field.
     * Note: this column has a database default value of: 0
     * @var        double
     */
    protected $estimated_time;

    /**
     * The value for the priority field.
     * @var        int
     */
    protected $priority;

    /**
     * The value for the time_created field.
     * Note: this column has a database default value of: (expression) now()
     * @var        \DateTime
     */
    protected $time_created;

    /**
     * The value for the time_completed field.
     * @var        \DateTime
     */
    protected $time_completed;

    /**
     * The value for the description field.
     * @var        string
     */
    protected $description;

    /**
     * @var        ChildUser
     */
    protected $aUserRelatedByAssigneeId;

    /**
     * @var        ChildUser
     */
    protected $aUserRelatedByOwnerId;

    /**
     * @var        ChildProject
     */
    protected $aProject;

    /**
     * @var        ObjectCollection|ChildTaskComment[] Collection to store aggregation of ChildTaskComment objects.
     */
    protected $collTaskComments;
    protected $collTaskCommentsPartial;

    /**
     * @var        ObjectCollection|ChildTaskTimeRecord[] Collection to store aggregation of ChildTaskTimeRecord objects.
     */
    protected $collTaskTimeRecords;
    protected $collTaskTimeRecordsPartial;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var boolean
     */
    protected $alreadyInSave = false;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildTaskComment[]
     */
    protected $taskCommentsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildTaskTimeRecord[]
     */
    protected $taskTimeRecordsScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see __construct()
     */
    public function applyDefaultValues()
    {
        $this->estimated_time = 0;
    }

    /**
     * Initializes internal state of Base\Task object.
     * @see applyDefaults()
     */
    public function __construct()
    {
        $this->applyDefaultValues();
    }

    /**
     * Returns whether the object has been modified.
     *
     * @return boolean True if the object has been modified.
     */
    public function isModified()
    {
        return !!$this->modifiedColumns;
    }

    /**
     * Has specified column been modified?
     *
     * @param  string  $col column fully qualified name (TableMap::TYPE_COLNAME), e.g. Book::AUTHOR_ID
     * @return boolean True if $col has been modified.
     */
    public function isColumnModified($col)
    {
        return $this->modifiedColumns && isset($this->modifiedColumns[$col]);
    }

    /**
     * Get the columns that have been modified in this object.
     * @return array A unique list of the modified column names for this object.
     */
    public function getModifiedColumns()
    {
        return $this->modifiedColumns ? array_keys($this->modifiedColumns) : [];
    }

    /**
     * Returns whether the object has ever been saved.  This will
     * be false, if the object was retrieved from storage or was created
     * and then saved.
     *
     * @return boolean true, if the object has never been persisted.
     */
    public function isNew()
    {
        return $this->new;
    }

    /**
     * Setter for the isNew attribute.  This method will be called
     * by Propel-generated children and objects.
     *
     * @param boolean $b the state of the object.
     */
    public function setNew($b)
    {
        $this->new = (boolean) $b;
    }

    /**
     * Whether this object has been deleted.
     * @return boolean The deleted state of this object.
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * Specify whether this object has been deleted.
     * @param  boolean $b The deleted state of this object.
     * @return void
     */
    public function setDeleted($b)
    {
        $this->deleted = (boolean) $b;
    }

    /**
     * Sets the modified state for the object to be false.
     * @param  string $col If supplied, only the specified column is reset.
     * @return void
     */
    public function resetModified($col = null)
    {
        if (null !== $col) {
            if (isset($this->modifiedColumns[$col])) {
                unset($this->modifiedColumns[$col]);
            }
        } else {
            $this->modifiedColumns = array();
        }
    }

    /**
     * Compares this with another <code>Task</code> instance.  If
     * <code>obj</code> is an instance of <code>Task</code>, delegates to
     * <code>equals(Task)</code>.  Otherwise, returns <code>false</code>.
     *
     * @param  mixed   $obj The object to compare to.
     * @return boolean Whether equal to the object specified.
     */
    public function equals($obj)
    {
        if (!$obj instanceof static) {
            return false;
        }

        if ($this === $obj) {
            return true;
        }

        if (null === $this->getPrimaryKey() || null === $obj->getPrimaryKey()) {
            return false;
        }

        return $this->getPrimaryKey() === $obj->getPrimaryKey();
    }

    /**
     * Get the associative array of the virtual columns in this object
     *
     * @return array
     */
    public function getVirtualColumns()
    {
        return $this->virtualColumns;
    }

    /**
     * Checks the existence of a virtual column in this object
     *
     * @param  string  $name The virtual column name
     * @return boolean
     */
    public function hasVirtualColumn($name)
    {
        return array_key_exists($name, $this->virtualColumns);
    }

    /**
     * Get the value of a virtual column in this object
     *
     * @param  string $name The virtual column name
     * @return mixed
     *
     * @throws PropelException
     */
    public function getVirtualColumn($name)
    {
        if (!$this->hasVirtualColumn($name)) {
            throw new PropelException(sprintf('Cannot get value of inexistent virtual column %s.', $name));
        }

        return $this->virtualColumns[$name];
    }

    /**
     * Set the value of a virtual column in this object
     *
     * @param string $name  The virtual column name
     * @param mixed  $value The value to give to the virtual column
     *
     * @return $this|Task The current object, for fluid interface
     */
    public function setVirtualColumn($name, $value)
    {
        $this->virtualColumns[$name] = $value;

        return $this;
    }

    /**
     * Logs a message using Propel::log().
     *
     * @param  string  $msg
     * @param  int     $priority One of the Propel::LOG_* logging levels
     * @return boolean
     */
    protected function log($msg, $priority = Propel::LOG_INFO)
    {
        return Propel::log(get_class($this) . ': ' . $msg, $priority);
    }

    /**
     * Export the current object properties to a string, using a given parser format
     * <code>
     * $book = BookQuery::create()->findPk(9012);
     * echo $book->exportTo('JSON');
     *  => {"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * @param  mixed   $parser                 A AbstractParser instance, or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param  boolean $includeLazyLoadColumns (optional) Whether to include lazy load(ed) columns. Defaults to TRUE.
     * @return string  The exported data
     */
    public function exportTo($parser, $includeLazyLoadColumns = true)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        return $parser->fromArray($this->toArray(TableMap::TYPE_PHPNAME, $includeLazyLoadColumns, array(), true));
    }

    /**
     * Clean up internal collections prior to serializing
     * Avoids recursive loops that turn into segmentation faults when serializing
     */
    public function __sleep()
    {
        $this->clearAllReferences();

        return array_keys(get_object_vars($this));
    }

    /**
     * Get the [id] column value.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the [title] column value.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get the [owner] column value.
     *
     * @return int
     */
    public function getOwnerId()
    {
        return $this->owner;
    }

    /**
     * Get the [assignee] column value.
     *
     * @return int
     */
    public function getAssigneeId()
    {
        return $this->assignee;
    }

    /**
     * Get the [project] column value.
     *
     * @return int
     */
    public function getProjectId()
    {
        return $this->project;
    }

    /**
     * Get the [optionally formatted] temporal [start_date] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw DateTime object will be returned.
     *
     * @return string|DateTime Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getStartDate($format = NULL)
    {
        if ($format === null) {
            return $this->start_date;
        } else {
            return $this->start_date instanceof \DateTime ? $this->start_date->format($format) : null;
        }
    }

    /**
     * Get the [estimated_time] column value.
     *
     * @return double
     */
    public function getEstimatedTime()
    {
        return $this->estimated_time;
    }

    /**
     * Get the [priority] column value.
     *
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Get the [optionally formatted] temporal [time_created] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw DateTime object will be returned.
     *
     * @return string|DateTime Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getTimeCreated($format = NULL)
    {
        if ($format === null) {
            return $this->time_created;
        } else {
            return $this->time_created instanceof \DateTime ? $this->time_created->format($format) : null;
        }
    }

    /**
     * Get the [optionally formatted] temporal [time_completed] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw DateTime object will be returned.
     *
     * @return string|DateTime Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getTimeCompleted($format = NULL)
    {
        if ($format === null) {
            return $this->time_completed;
        } else {
            return $this->time_completed instanceof \DateTime ? $this->time_completed->format($format) : null;
        }
    }

    /**
     * Get the [description] column value.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of [id] column.
     *
     * @param  int $v new value
     * @return $this|\Task The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[TaskTableMap::COL_ID] = true;
        }

        return $this;
    } // setId()

    /**
     * Set the value of [title] column.
     *
     * @param  string $v new value
     * @return $this|\Task The current object (for fluent API support)
     */
    public function setTitle($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->title !== $v) {
            $this->title = $v;
            $this->modifiedColumns[TaskTableMap::COL_TITLE] = true;
        }

        return $this;
    } // setTitle()

    /**
     * Set the value of [owner] column.
     *
     * @param  int $v new value
     * @return $this|\Task The current object (for fluent API support)
     */
    public function setOwnerId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->owner !== $v) {
            $this->owner = $v;
            $this->modifiedColumns[TaskTableMap::COL_OWNER] = true;
        }

        if ($this->aUserRelatedByOwnerId !== null && $this->aUserRelatedByOwnerId->getPersonId() !== $v) {
            $this->aUserRelatedByOwnerId = null;
        }

        return $this;
    } // setOwnerId()

    /**
     * Set the value of [assignee] column.
     *
     * @param  int $v new value
     * @return $this|\Task The current object (for fluent API support)
     */
    public function setAssigneeId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->assignee !== $v) {
            $this->assignee = $v;
            $this->modifiedColumns[TaskTableMap::COL_ASSIGNEE] = true;
        }

        if ($this->aUserRelatedByAssigneeId !== null && $this->aUserRelatedByAssigneeId->getPersonId() !== $v) {
            $this->aUserRelatedByAssigneeId = null;
        }

        return $this;
    } // setAssigneeId()

    /**
     * Set the value of [project] column.
     *
     * @param  int $v new value
     * @return $this|\Task The current object (for fluent API support)
     */
    public function setProjectId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->project !== $v) {
            $this->project = $v;
            $this->modifiedColumns[TaskTableMap::COL_PROJECT] = true;
        }

        if ($this->aProject !== null && $this->aProject->getId() !== $v) {
            $this->aProject = null;
        }

        return $this;
    } // setProjectId()

    /**
     * Sets the value of [start_date] column to a normalized version of the date/time value specified.
     *
     * @param  mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return $this|\Task The current object (for fluent API support)
     */
    public function setStartDate($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->start_date !== null || $dt !== null) {
            if ($dt !== $this->start_date) {
                $this->start_date = $dt;
                $this->modifiedColumns[TaskTableMap::COL_START_DATE] = true;
            }
        } // if either are not null

        return $this;
    } // setStartDate()

    /**
     * Set the value of [estimated_time] column.
     *
     * @param  double $v new value
     * @return $this|\Task The current object (for fluent API support)
     */
    public function setEstimatedTime($v)
    {
        if ($v !== null) {
            $v = (double) $v;
        }

        if ($this->estimated_time !== $v) {
            $this->estimated_time = $v;
            $this->modifiedColumns[TaskTableMap::COL_ESTIMATED_TIME] = true;
        }

        return $this;
    } // setEstimatedTime()

    /**
     * Set the value of [priority] column.
     *
     * @param  int $v new value
     * @return $this|\Task The current object (for fluent API support)
     */
    public function setPriority($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->priority !== $v) {
            $this->priority = $v;
            $this->modifiedColumns[TaskTableMap::COL_PRIORITY] = true;
        }

        return $this;
    } // setPriority()

    /**
     * Sets the value of [time_created] column to a normalized version of the date/time value specified.
     *
     * @param  mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return $this|\Task The current object (for fluent API support)
     */
    public function setTimeCreated($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->time_created !== null || $dt !== null) {
            if ($dt !== $this->time_created) {
                $this->time_created = $dt;
                $this->modifiedColumns[TaskTableMap::COL_TIME_CREATED] = true;
            }
        } // if either are not null

        return $this;
    } // setTimeCreated()

    /**
     * Sets the value of [time_completed] column to a normalized version of the date/time value specified.
     *
     * @param  mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return $this|\Task The current object (for fluent API support)
     */
    public function setTimeCompleted($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->time_completed !== null || $dt !== null) {
            if ($dt !== $this->time_completed) {
                $this->time_completed = $dt;
                $this->modifiedColumns[TaskTableMap::COL_TIME_COMPLETED] = true;
            }
        } // if either are not null

        return $this;
    } // setTimeCompleted()

    /**
     * Set the value of [description] column.
     *
     * @param  string $v new value
     * @return $this|\Task The current object (for fluent API support)
     */
    public function setDescription($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->description !== $v) {
            $this->description = $v;
            $this->modifiedColumns[TaskTableMap::COL_DESCRIPTION] = true;
        }

        return $this;
    } // setDescription()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
            if ($this->estimated_time !== 0) {
                return false;
            }

        // otherwise, everything was equal, so return TRUE
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array   $row       The row returned by DataFetcher->fetch().
     * @param int     $startcol  0-based offset column which indicates which restultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @param string  $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                  One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                            TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false, $indexType = TableMap::TYPE_NUM)
    {
        try {

            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : TaskTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : TaskTableMap::translateFieldName('Title', TableMap::TYPE_PHPNAME, $indexType)];
            $this->title = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : TaskTableMap::translateFieldName('OwnerId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->owner = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : TaskTableMap::translateFieldName('AssigneeId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->assignee = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : TaskTableMap::translateFieldName('ProjectId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->project = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : TaskTableMap::translateFieldName('StartDate', TableMap::TYPE_PHPNAME, $indexType)];
            $this->start_date = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : TaskTableMap::translateFieldName('EstimatedTime', TableMap::TYPE_PHPNAME, $indexType)];
            $this->estimated_time = (null !== $col) ? (double) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 7 + $startcol : TaskTableMap::translateFieldName('Priority', TableMap::TYPE_PHPNAME, $indexType)];
            $this->priority = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 8 + $startcol : TaskTableMap::translateFieldName('TimeCreated', TableMap::TYPE_PHPNAME, $indexType)];
            $this->time_created = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 9 + $startcol : TaskTableMap::translateFieldName('TimeCompleted', TableMap::TYPE_PHPNAME, $indexType)];
            $this->time_completed = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 10 + $startcol : TaskTableMap::translateFieldName('Description', TableMap::TYPE_PHPNAME, $indexType)];
            $this->description = (null !== $col) ? (string) $col : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 11; // 11 = TaskTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException(sprintf('Error populating %s object', '\\Task'), 0, $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {
        if ($this->aUserRelatedByOwnerId !== null && $this->owner !== $this->aUserRelatedByOwnerId->getPersonId()) {
            $this->aUserRelatedByOwnerId = null;
        }
        if ($this->aUserRelatedByAssigneeId !== null && $this->assignee !== $this->aUserRelatedByAssigneeId->getPersonId()) {
            $this->aUserRelatedByAssigneeId = null;
        }
        if ($this->aProject !== null && $this->project !== $this->aProject->getId()) {
            $this->aProject = null;
        }
    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param      boolean $deep (optional) Whether to also de-associated any related objects.
     * @param      ConnectionInterface $con (optional) The ConnectionInterface connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(TaskTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildTaskQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aUserRelatedByAssigneeId = null;
            $this->aUserRelatedByOwnerId = null;
            $this->aProject = null;
            $this->collTaskComments = null;

            $this->collTaskTimeRecords = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see Task::setDeleted()
     * @see Task::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(TaskTableMap::DATABASE_NAME);
        }

        $con->transaction(function () use ($con) {
            $deleteQuery = ChildTaskQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $this->setDeleted(true);
            }
        });
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see doSave()
     */
    public function save(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(TaskTableMap::DATABASE_NAME);
        }

        return $con->transaction(function () use ($con) {
            $isInsert = $this->isNew();
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
            } else {
                $ret = $ret && $this->preUpdate($con);
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                TaskTableMap::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }

            return $affectedRows;
        });
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see save()
     */
    protected function doSave(ConnectionInterface $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            // We call the save method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aUserRelatedByAssigneeId !== null) {
                if ($this->aUserRelatedByAssigneeId->isModified() || $this->aUserRelatedByAssigneeId->isNew()) {
                    $affectedRows += $this->aUserRelatedByAssigneeId->save($con);
                }
                $this->setUserRelatedByAssigneeId($this->aUserRelatedByAssigneeId);
            }

            if ($this->aUserRelatedByOwnerId !== null) {
                if ($this->aUserRelatedByOwnerId->isModified() || $this->aUserRelatedByOwnerId->isNew()) {
                    $affectedRows += $this->aUserRelatedByOwnerId->save($con);
                }
                $this->setUserRelatedByOwnerId($this->aUserRelatedByOwnerId);
            }

            if ($this->aProject !== null) {
                if ($this->aProject->isModified() || $this->aProject->isNew()) {
                    $affectedRows += $this->aProject->save($con);
                }
                $this->setProject($this->aProject);
            }

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                    $affectedRows += 1;
                } else {
                    $affectedRows += $this->doUpdate($con);
                }
                $this->resetModified();
            }

            if ($this->taskCommentsScheduledForDeletion !== null) {
                if (!$this->taskCommentsScheduledForDeletion->isEmpty()) {
                    \TaskCommentQuery::create()
                        ->filterByPrimaryKeys($this->taskCommentsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->taskCommentsScheduledForDeletion = null;
                }
            }

            if ($this->collTaskComments !== null) {
                foreach ($this->collTaskComments as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->taskTimeRecordsScheduledForDeletion !== null) {
                if (!$this->taskTimeRecordsScheduledForDeletion->isEmpty()) {
                    \TaskTimeRecordQuery::create()
                        ->filterByPrimaryKeys($this->taskTimeRecordsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->taskTimeRecordsScheduledForDeletion = null;
                }
            }

            if ($this->collTaskTimeRecords !== null) {
                foreach ($this->collTaskTimeRecords as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @throws PropelException
     * @see doSave()
     */
    protected function doInsert(ConnectionInterface $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[TaskTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . TaskTableMap::COL_ID . ')');
        }
        if (null === $this->id) {
            try {
                $dataFetcher = $con->query("SELECT nextval('tasks_id_seq')");
                $this->id = $dataFetcher->fetchColumn();
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', 0, $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(TaskTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'id';
        }
        if ($this->isColumnModified(TaskTableMap::COL_TITLE)) {
            $modifiedColumns[':p' . $index++]  = 'title';
        }
        if ($this->isColumnModified(TaskTableMap::COL_OWNER)) {
            $modifiedColumns[':p' . $index++]  = 'owner';
        }
        if ($this->isColumnModified(TaskTableMap::COL_ASSIGNEE)) {
            $modifiedColumns[':p' . $index++]  = 'assignee';
        }
        if ($this->isColumnModified(TaskTableMap::COL_PROJECT)) {
            $modifiedColumns[':p' . $index++]  = 'project';
        }
        if ($this->isColumnModified(TaskTableMap::COL_START_DATE)) {
            $modifiedColumns[':p' . $index++]  = 'start_date';
        }
        if ($this->isColumnModified(TaskTableMap::COL_ESTIMATED_TIME)) {
            $modifiedColumns[':p' . $index++]  = 'estimated_time';
        }
        if ($this->isColumnModified(TaskTableMap::COL_PRIORITY)) {
            $modifiedColumns[':p' . $index++]  = 'priority';
        }
        if ($this->isColumnModified(TaskTableMap::COL_TIME_CREATED)) {
            $modifiedColumns[':p' . $index++]  = 'time_created';
        }
        if ($this->isColumnModified(TaskTableMap::COL_TIME_COMPLETED)) {
            $modifiedColumns[':p' . $index++]  = 'time_completed';
        }
        if ($this->isColumnModified(TaskTableMap::COL_DESCRIPTION)) {
            $modifiedColumns[':p' . $index++]  = 'description';
        }

        $sql = sprintf(
            'INSERT INTO tasks (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case 'id':
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case 'title':
                        $stmt->bindValue($identifier, $this->title, PDO::PARAM_STR);
                        break;
                    case 'owner':
                        $stmt->bindValue($identifier, $this->owner, PDO::PARAM_INT);
                        break;
                    case 'assignee':
                        $stmt->bindValue($identifier, $this->assignee, PDO::PARAM_INT);
                        break;
                    case 'project':
                        $stmt->bindValue($identifier, $this->project, PDO::PARAM_INT);
                        break;
                    case 'start_date':
                        $stmt->bindValue($identifier, $this->start_date ? $this->start_date->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                    case 'estimated_time':
                        $stmt->bindValue($identifier, $this->estimated_time, PDO::PARAM_STR);
                        break;
                    case 'priority':
                        $stmt->bindValue($identifier, $this->priority, PDO::PARAM_INT);
                        break;
                    case 'time_created':
                        $stmt->bindValue($identifier, $this->time_created ? $this->time_created->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                    case 'time_completed':
                        $stmt->bindValue($identifier, $this->time_completed ? $this->time_completed->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                    case 'description':
                        $stmt->bindValue($identifier, $this->description, PDO::PARAM_STR);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), 0, $e);
        }

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @return Integer Number of updated rows
     * @see doSave()
     */
    protected function doUpdate(ConnectionInterface $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();

        return $selectCriteria->doUpdate($valuesCriteria, $con);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param      string $name name
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                     TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                     Defaults to TableMap::TYPE_PHPNAME.
     * @return mixed Value of field.
     */
    public function getByName($name, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = TaskTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getId();
                break;
            case 1:
                return $this->getTitle();
                break;
            case 2:
                return $this->getOwnerId();
                break;
            case 3:
                return $this->getAssigneeId();
                break;
            case 4:
                return $this->getProjectId();
                break;
            case 5:
                return $this->getStartDate();
                break;
            case 6:
                return $this->getEstimatedTime();
                break;
            case 7:
                return $this->getPriority();
                break;
            case 8:
                return $this->getTimeCreated();
                break;
            case 9:
                return $this->getTimeCompleted();
                break;
            case 10:
                return $this->getDescription();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     *                    TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                    Defaults to TableMap::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to TRUE.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = TableMap::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {

        if (isset($alreadyDumpedObjects['Task'][$this->hashCode()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Task'][$this->hashCode()] = true;
        $keys = TaskTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getTitle(),
            $keys[2] => $this->getOwnerId(),
            $keys[3] => $this->getAssigneeId(),
            $keys[4] => $this->getProjectId(),
            $keys[5] => $this->getStartDate(),
            $keys[6] => $this->getEstimatedTime(),
            $keys[7] => $this->getPriority(),
            $keys[8] => $this->getTimeCreated(),
            $keys[9] => $this->getTimeCompleted(),
            $keys[10] => $this->getDescription(),
        );

        $utc = new \DateTimeZone('utc');
        if ($result[$keys[5]] instanceof \DateTime) {
            // When changing timezone we don't want to change existing instances
            $dateTime = clone $result[$keys[5]];
            $result[$keys[5]] = $dateTime->setTimezone($utc)->format('Y-m-d\TH:i:s\Z');
        }

        if ($result[$keys[8]] instanceof \DateTime) {
            // When changing timezone we don't want to change existing instances
            $dateTime = clone $result[$keys[8]];
            $result[$keys[8]] = $dateTime->setTimezone($utc)->format('Y-m-d\TH:i:s\Z');
        }

        if ($result[$keys[9]] instanceof \DateTime) {
            // When changing timezone we don't want to change existing instances
            $dateTime = clone $result[$keys[9]];
            $result[$keys[9]] = $dateTime->setTimezone($utc)->format('Y-m-d\TH:i:s\Z');
        }

        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aUserRelatedByAssigneeId) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'user';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'users';
                        break;
                    default:
                        $key = 'User';
                }

                $result[$key] = $this->aUserRelatedByAssigneeId->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aUserRelatedByOwnerId) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'user';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'users';
                        break;
                    default:
                        $key = 'User';
                }

                $result[$key] = $this->aUserRelatedByOwnerId->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aProject) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'project';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'projects';
                        break;
                    default:
                        $key = 'Project';
                }

                $result[$key] = $this->aProject->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collTaskComments) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'taskComments';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'task_commentss';
                        break;
                    default:
                        $key = 'TaskComments';
                }

                $result[$key] = $this->collTaskComments->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collTaskTimeRecords) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'taskTimeRecords';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'task_time_recordss';
                        break;
                    default:
                        $key = 'TaskTimeRecords';
                }

                $result[$key] = $this->collTaskTimeRecords->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param  string $name
     * @param  mixed  $value field value
     * @param  string $type The type of fieldname the $name is of:
     *                one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                Defaults to TableMap::TYPE_PHPNAME.
     * @return $this|\Task
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = TaskTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param  int $pos position in xml schema
     * @param  mixed $value field value
     * @return $this|\Task
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setTitle($value);
                break;
            case 2:
                $this->setOwnerId($value);
                break;
            case 3:
                $this->setAssigneeId($value);
                break;
            case 4:
                $this->setProjectId($value);
                break;
            case 5:
                $this->setStartDate($value);
                break;
            case 6:
                $this->setEstimatedTime($value);
                break;
            case 7:
                $this->setPriority($value);
                break;
            case 8:
                $this->setTimeCreated($value);
                break;
            case 9:
                $this->setTimeCompleted($value);
                break;
            case 10:
                $this->setDescription($value);
                break;
        } // switch()

        return $this;
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     * TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     * The default key type is the column's TableMap::TYPE_PHPNAME.
     *
     * @param      array  $arr     An array to populate the object from.
     * @param      string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = TableMap::TYPE_PHPNAME)
    {
        $keys = TaskTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setId($arr[$keys[0]]);
        }
        if (array_key_exists($keys[1], $arr)) {
            $this->setTitle($arr[$keys[1]]);
        }
        if (array_key_exists($keys[2], $arr)) {
            $this->setOwnerId($arr[$keys[2]]);
        }
        if (array_key_exists($keys[3], $arr)) {
            $this->setAssigneeId($arr[$keys[3]]);
        }
        if (array_key_exists($keys[4], $arr)) {
            $this->setProjectId($arr[$keys[4]]);
        }
        if (array_key_exists($keys[5], $arr)) {
            $this->setStartDate($arr[$keys[5]]);
        }
        if (array_key_exists($keys[6], $arr)) {
            $this->setEstimatedTime($arr[$keys[6]]);
        }
        if (array_key_exists($keys[7], $arr)) {
            $this->setPriority($arr[$keys[7]]);
        }
        if (array_key_exists($keys[8], $arr)) {
            $this->setTimeCreated($arr[$keys[8]]);
        }
        if (array_key_exists($keys[9], $arr)) {
            $this->setTimeCompleted($arr[$keys[9]]);
        }
        if (array_key_exists($keys[10], $arr)) {
            $this->setDescription($arr[$keys[10]]);
        }
    }

     /**
     * Populate the current object from a string, using a given parser format
     * <code>
     * $book = new Book();
     * $book->importFrom('JSON', '{"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     * TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     * The default key type is the column's TableMap::TYPE_PHPNAME.
     *
     * @param mixed $parser A AbstractParser instance,
     *                       or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param string $data The source data to import from
     * @param string $keyType The type of keys the array uses.
     *
     * @return $this|\Task The current object, for fluid interface
     */
    public function importFrom($parser, $data, $keyType = TableMap::TYPE_PHPNAME)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        $this->fromArray($parser->toArray($data), $keyType);

        return $this;
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(TaskTableMap::DATABASE_NAME);

        if ($this->isColumnModified(TaskTableMap::COL_ID)) {
            $criteria->add(TaskTableMap::COL_ID, $this->id);
        }
        if ($this->isColumnModified(TaskTableMap::COL_TITLE)) {
            $criteria->add(TaskTableMap::COL_TITLE, $this->title);
        }
        if ($this->isColumnModified(TaskTableMap::COL_OWNER)) {
            $criteria->add(TaskTableMap::COL_OWNER, $this->owner);
        }
        if ($this->isColumnModified(TaskTableMap::COL_ASSIGNEE)) {
            $criteria->add(TaskTableMap::COL_ASSIGNEE, $this->assignee);
        }
        if ($this->isColumnModified(TaskTableMap::COL_PROJECT)) {
            $criteria->add(TaskTableMap::COL_PROJECT, $this->project);
        }
        if ($this->isColumnModified(TaskTableMap::COL_START_DATE)) {
            $criteria->add(TaskTableMap::COL_START_DATE, $this->start_date);
        }
        if ($this->isColumnModified(TaskTableMap::COL_ESTIMATED_TIME)) {
            $criteria->add(TaskTableMap::COL_ESTIMATED_TIME, $this->estimated_time);
        }
        if ($this->isColumnModified(TaskTableMap::COL_PRIORITY)) {
            $criteria->add(TaskTableMap::COL_PRIORITY, $this->priority);
        }
        if ($this->isColumnModified(TaskTableMap::COL_TIME_CREATED)) {
            $criteria->add(TaskTableMap::COL_TIME_CREATED, $this->time_created);
        }
        if ($this->isColumnModified(TaskTableMap::COL_TIME_COMPLETED)) {
            $criteria->add(TaskTableMap::COL_TIME_COMPLETED, $this->time_completed);
        }
        if ($this->isColumnModified(TaskTableMap::COL_DESCRIPTION)) {
            $criteria->add(TaskTableMap::COL_DESCRIPTION, $this->description);
        }

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @throws LogicException if no primary key is defined
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = ChildTaskQuery::create();
        $criteria->add(TaskTableMap::COL_ID, $this->id);

        return $criteria;
    }

    /**
     * If the primary key is not null, return the hashcode of the
     * primary key. Otherwise, return the hash code of the object.
     *
     * @return int Hashcode
     */
    public function hashCode()
    {
        $validPk = null !== $this->getId();

        $validPrimaryKeyFKs = 0;
        $primaryKeyFKs = [];

        if ($validPk) {
            return crc32(json_encode($this->getPrimaryKey(), JSON_UNESCAPED_UNICODE));
        } elseif ($validPrimaryKeyFKs) {
            return crc32(json_encode($primaryKeyFKs, JSON_UNESCAPED_UNICODE));
        }

        return spl_object_hash($this);
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param       int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {
        return null === $this->getId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of \Task (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setTitle($this->getTitle());
        $copyObj->setOwnerId($this->getOwnerId());
        $copyObj->setAssigneeId($this->getAssigneeId());
        $copyObj->setProjectId($this->getProjectId());
        $copyObj->setStartDate($this->getStartDate());
        $copyObj->setEstimatedTime($this->getEstimatedTime());
        $copyObj->setPriority($this->getPriority());
        $copyObj->setTimeCreated($this->getTimeCreated());
        $copyObj->setTimeCompleted($this->getTimeCompleted());
        $copyObj->setDescription($this->getDescription());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getTaskComments() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addTaskComment($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getTaskTimeRecords() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addTaskTimeRecord($relObj->copy($deepCopy));
                }
            }

        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param  boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return \Task Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }

    /**
     * Declares an association between this object and a ChildUser object.
     *
     * @param  ChildUser $v
     * @return $this|\Task The current object (for fluent API support)
     * @throws PropelException
     */
    public function setUserRelatedByAssigneeId(ChildUser $v = null)
    {
        if ($v === null) {
            $this->setAssigneeId(NULL);
        } else {
            $this->setAssigneeId($v->getPersonId());
        }

        $this->aUserRelatedByAssigneeId = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildUser object, it will not be re-added.
        if ($v !== null) {
            $v->addTaskRelatedByAssigneeId($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildUser object
     *
     * @param  ConnectionInterface $con Optional Connection object.
     * @return ChildUser The associated ChildUser object.
     * @throws PropelException
     */
    public function getUserRelatedByAssigneeId(ConnectionInterface $con = null)
    {
        if ($this->aUserRelatedByAssigneeId === null && ($this->assignee !== null)) {
            $this->aUserRelatedByAssigneeId = ChildUserQuery::create()->findPk($this->assignee, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aUserRelatedByAssigneeId->addTasksRelatedByAssigneeId($this);
             */
        }

        return $this->aUserRelatedByAssigneeId;
    }

    /**
     * Declares an association between this object and a ChildUser object.
     *
     * @param  ChildUser $v
     * @return $this|\Task The current object (for fluent API support)
     * @throws PropelException
     */
    public function setUserRelatedByOwnerId(ChildUser $v = null)
    {
        if ($v === null) {
            $this->setOwnerId(NULL);
        } else {
            $this->setOwnerId($v->getPersonId());
        }

        $this->aUserRelatedByOwnerId = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildUser object, it will not be re-added.
        if ($v !== null) {
            $v->addTaskRelatedByOwnerId($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildUser object
     *
     * @param  ConnectionInterface $con Optional Connection object.
     * @return ChildUser The associated ChildUser object.
     * @throws PropelException
     */
    public function getUserRelatedByOwnerId(ConnectionInterface $con = null)
    {
        if ($this->aUserRelatedByOwnerId === null && ($this->owner !== null)) {
            $this->aUserRelatedByOwnerId = ChildUserQuery::create()->findPk($this->owner, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aUserRelatedByOwnerId->addTasksRelatedByOwnerId($this);
             */
        }

        return $this->aUserRelatedByOwnerId;
    }

    /**
     * Declares an association between this object and a ChildProject object.
     *
     * @param  ChildProject $v
     * @return $this|\Task The current object (for fluent API support)
     * @throws PropelException
     */
    public function setProject(ChildProject $v = null)
    {
        if ($v === null) {
            $this->setProjectId(NULL);
        } else {
            $this->setProjectId($v->getId());
        }

        $this->aProject = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildProject object, it will not be re-added.
        if ($v !== null) {
            $v->addTask($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildProject object
     *
     * @param  ConnectionInterface $con Optional Connection object.
     * @return ChildProject The associated ChildProject object.
     * @throws PropelException
     */
    public function getProject(ConnectionInterface $con = null)
    {
        if ($this->aProject === null && ($this->project !== null)) {
            $this->aProject = ChildProjectQuery::create()->findPk($this->project, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aProject->addTasks($this);
             */
        }

        return $this->aProject;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param      string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('TaskComment' == $relationName) {
            return $this->initTaskComments();
        }
        if ('TaskTimeRecord' == $relationName) {
            return $this->initTaskTimeRecords();
        }
    }

    /**
     * Clears out the collTaskComments collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addTaskComments()
     */
    public function clearTaskComments()
    {
        $this->collTaskComments = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collTaskComments collection loaded partially.
     */
    public function resetPartialTaskComments($v = true)
    {
        $this->collTaskCommentsPartial = $v;
    }

    /**
     * Initializes the collTaskComments collection.
     *
     * By default this just sets the collTaskComments collection to an empty array (like clearcollTaskComments());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initTaskComments($overrideExisting = true)
    {
        if (null !== $this->collTaskComments && !$overrideExisting) {
            return;
        }
        $this->collTaskComments = new ObjectCollection();
        $this->collTaskComments->setModel('\TaskComment');
    }

    /**
     * Gets an array of ChildTaskComment objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildTask is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildTaskComment[] List of ChildTaskComment objects
     * @throws PropelException
     */
    public function getTaskComments(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collTaskCommentsPartial && !$this->isNew();
        if (null === $this->collTaskComments || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collTaskComments) {
                // return empty collection
                $this->initTaskComments();
            } else {
                $collTaskComments = ChildTaskCommentQuery::create(null, $criteria)
                    ->filterByTask($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collTaskCommentsPartial && count($collTaskComments)) {
                        $this->initTaskComments(false);

                        foreach ($collTaskComments as $obj) {
                            if (false == $this->collTaskComments->contains($obj)) {
                                $this->collTaskComments->append($obj);
                            }
                        }

                        $this->collTaskCommentsPartial = true;
                    }

                    return $collTaskComments;
                }

                if ($partial && $this->collTaskComments) {
                    foreach ($this->collTaskComments as $obj) {
                        if ($obj->isNew()) {
                            $collTaskComments[] = $obj;
                        }
                    }
                }

                $this->collTaskComments = $collTaskComments;
                $this->collTaskCommentsPartial = false;
            }
        }

        return $this->collTaskComments;
    }

    /**
     * Sets a collection of ChildTaskComment objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $taskComments A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildTask The current object (for fluent API support)
     */
    public function setTaskComments(Collection $taskComments, ConnectionInterface $con = null)
    {
        /** @var ChildTaskComment[] $taskCommentsToDelete */
        $taskCommentsToDelete = $this->getTaskComments(new Criteria(), $con)->diff($taskComments);


        $this->taskCommentsScheduledForDeletion = $taskCommentsToDelete;

        foreach ($taskCommentsToDelete as $taskCommentRemoved) {
            $taskCommentRemoved->setTask(null);
        }

        $this->collTaskComments = null;
        foreach ($taskComments as $taskComment) {
            $this->addTaskComment($taskComment);
        }

        $this->collTaskComments = $taskComments;
        $this->collTaskCommentsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related TaskComment objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related TaskComment objects.
     * @throws PropelException
     */
    public function countTaskComments(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collTaskCommentsPartial && !$this->isNew();
        if (null === $this->collTaskComments || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collTaskComments) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getTaskComments());
            }

            $query = ChildTaskCommentQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByTask($this)
                ->count($con);
        }

        return count($this->collTaskComments);
    }

    /**
     * Method called to associate a ChildTaskComment object to this object
     * through the ChildTaskComment foreign key attribute.
     *
     * @param  ChildTaskComment $l ChildTaskComment
     * @return $this|\Task The current object (for fluent API support)
     */
    public function addTaskComment(ChildTaskComment $l)
    {
        if ($this->collTaskComments === null) {
            $this->initTaskComments();
            $this->collTaskCommentsPartial = true;
        }

        if (!$this->collTaskComments->contains($l)) {
            $this->doAddTaskComment($l);
        }

        return $this;
    }

    /**
     * @param ChildTaskComment $taskComment The ChildTaskComment object to add.
     */
    protected function doAddTaskComment(ChildTaskComment $taskComment)
    {
        $this->collTaskComments[]= $taskComment;
        $taskComment->setTask($this);
    }

    /**
     * @param  ChildTaskComment $taskComment The ChildTaskComment object to remove.
     * @return $this|ChildTask The current object (for fluent API support)
     */
    public function removeTaskComment(ChildTaskComment $taskComment)
    {
        if ($this->getTaskComments()->contains($taskComment)) {
            $pos = $this->collTaskComments->search($taskComment);
            $this->collTaskComments->remove($pos);
            if (null === $this->taskCommentsScheduledForDeletion) {
                $this->taskCommentsScheduledForDeletion = clone $this->collTaskComments;
                $this->taskCommentsScheduledForDeletion->clear();
            }
            $this->taskCommentsScheduledForDeletion[]= clone $taskComment;
            $taskComment->setTask(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Task is new, it will return
     * an empty collection; or if this Task has previously
     * been saved, it will retrieve related TaskComments from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Task.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildTaskComment[] List of ChildTaskComment objects
     */
    public function getTaskCommentsJoinUser(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildTaskCommentQuery::create(null, $criteria);
        $query->joinWith('User', $joinBehavior);

        return $this->getTaskComments($query, $con);
    }

    /**
     * Clears out the collTaskTimeRecords collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addTaskTimeRecords()
     */
    public function clearTaskTimeRecords()
    {
        $this->collTaskTimeRecords = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collTaskTimeRecords collection loaded partially.
     */
    public function resetPartialTaskTimeRecords($v = true)
    {
        $this->collTaskTimeRecordsPartial = $v;
    }

    /**
     * Initializes the collTaskTimeRecords collection.
     *
     * By default this just sets the collTaskTimeRecords collection to an empty array (like clearcollTaskTimeRecords());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initTaskTimeRecords($overrideExisting = true)
    {
        if (null !== $this->collTaskTimeRecords && !$overrideExisting) {
            return;
        }
        $this->collTaskTimeRecords = new ObjectCollection();
        $this->collTaskTimeRecords->setModel('\TaskTimeRecord');
    }

    /**
     * Gets an array of ChildTaskTimeRecord objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildTask is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildTaskTimeRecord[] List of ChildTaskTimeRecord objects
     * @throws PropelException
     */
    public function getTaskTimeRecords(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collTaskTimeRecordsPartial && !$this->isNew();
        if (null === $this->collTaskTimeRecords || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collTaskTimeRecords) {
                // return empty collection
                $this->initTaskTimeRecords();
            } else {
                $collTaskTimeRecords = ChildTaskTimeRecordQuery::create(null, $criteria)
                    ->filterByTask($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collTaskTimeRecordsPartial && count($collTaskTimeRecords)) {
                        $this->initTaskTimeRecords(false);

                        foreach ($collTaskTimeRecords as $obj) {
                            if (false == $this->collTaskTimeRecords->contains($obj)) {
                                $this->collTaskTimeRecords->append($obj);
                            }
                        }

                        $this->collTaskTimeRecordsPartial = true;
                    }

                    return $collTaskTimeRecords;
                }

                if ($partial && $this->collTaskTimeRecords) {
                    foreach ($this->collTaskTimeRecords as $obj) {
                        if ($obj->isNew()) {
                            $collTaskTimeRecords[] = $obj;
                        }
                    }
                }

                $this->collTaskTimeRecords = $collTaskTimeRecords;
                $this->collTaskTimeRecordsPartial = false;
            }
        }

        return $this->collTaskTimeRecords;
    }

    /**
     * Sets a collection of ChildTaskTimeRecord objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $taskTimeRecords A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildTask The current object (for fluent API support)
     */
    public function setTaskTimeRecords(Collection $taskTimeRecords, ConnectionInterface $con = null)
    {
        /** @var ChildTaskTimeRecord[] $taskTimeRecordsToDelete */
        $taskTimeRecordsToDelete = $this->getTaskTimeRecords(new Criteria(), $con)->diff($taskTimeRecords);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->taskTimeRecordsScheduledForDeletion = clone $taskTimeRecordsToDelete;

        foreach ($taskTimeRecordsToDelete as $taskTimeRecordRemoved) {
            $taskTimeRecordRemoved->setTask(null);
        }

        $this->collTaskTimeRecords = null;
        foreach ($taskTimeRecords as $taskTimeRecord) {
            $this->addTaskTimeRecord($taskTimeRecord);
        }

        $this->collTaskTimeRecords = $taskTimeRecords;
        $this->collTaskTimeRecordsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related TaskTimeRecord objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related TaskTimeRecord objects.
     * @throws PropelException
     */
    public function countTaskTimeRecords(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collTaskTimeRecordsPartial && !$this->isNew();
        if (null === $this->collTaskTimeRecords || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collTaskTimeRecords) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getTaskTimeRecords());
            }

            $query = ChildTaskTimeRecordQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByTask($this)
                ->count($con);
        }

        return count($this->collTaskTimeRecords);
    }

    /**
     * Method called to associate a ChildTaskTimeRecord object to this object
     * through the ChildTaskTimeRecord foreign key attribute.
     *
     * @param  ChildTaskTimeRecord $l ChildTaskTimeRecord
     * @return $this|\Task The current object (for fluent API support)
     */
    public function addTaskTimeRecord(ChildTaskTimeRecord $l)
    {
        if ($this->collTaskTimeRecords === null) {
            $this->initTaskTimeRecords();
            $this->collTaskTimeRecordsPartial = true;
        }

        if (!$this->collTaskTimeRecords->contains($l)) {
            $this->doAddTaskTimeRecord($l);
        }

        return $this;
    }

    /**
     * @param ChildTaskTimeRecord $taskTimeRecord The ChildTaskTimeRecord object to add.
     */
    protected function doAddTaskTimeRecord(ChildTaskTimeRecord $taskTimeRecord)
    {
        $this->collTaskTimeRecords[]= $taskTimeRecord;
        $taskTimeRecord->setTask($this);
    }

    /**
     * @param  ChildTaskTimeRecord $taskTimeRecord The ChildTaskTimeRecord object to remove.
     * @return $this|ChildTask The current object (for fluent API support)
     */
    public function removeTaskTimeRecord(ChildTaskTimeRecord $taskTimeRecord)
    {
        if ($this->getTaskTimeRecords()->contains($taskTimeRecord)) {
            $pos = $this->collTaskTimeRecords->search($taskTimeRecord);
            $this->collTaskTimeRecords->remove($pos);
            if (null === $this->taskTimeRecordsScheduledForDeletion) {
                $this->taskTimeRecordsScheduledForDeletion = clone $this->collTaskTimeRecords;
                $this->taskTimeRecordsScheduledForDeletion->clear();
            }
            $this->taskTimeRecordsScheduledForDeletion[]= clone $taskTimeRecord;
            $taskTimeRecord->setTask(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Task is new, it will return
     * an empty collection; or if this Task has previously
     * been saved, it will retrieve related TaskTimeRecords from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Task.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildTaskTimeRecord[] List of ChildTaskTimeRecord objects
     */
    public function getTaskTimeRecordsJoinUser(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildTaskTimeRecordQuery::create(null, $criteria);
        $query->joinWith('User', $joinBehavior);

        return $this->getTaskTimeRecords($query, $con);
    }

    /**
     * Clears the current object, sets all attributes to their default values and removes
     * outgoing references as well as back-references (from other objects to this one. Results probably in a database
     * change of those foreign objects when you call `save` there).
     */
    public function clear()
    {
        if (null !== $this->aUserRelatedByAssigneeId) {
            $this->aUserRelatedByAssigneeId->removeTaskRelatedByAssigneeId($this);
        }
        if (null !== $this->aUserRelatedByOwnerId) {
            $this->aUserRelatedByOwnerId->removeTaskRelatedByOwnerId($this);
        }
        if (null !== $this->aProject) {
            $this->aProject->removeTask($this);
        }
        $this->id = null;
        $this->title = null;
        $this->owner = null;
        $this->assignee = null;
        $this->project = null;
        $this->start_date = null;
        $this->estimated_time = null;
        $this->priority = null;
        $this->time_created = null;
        $this->time_completed = null;
        $this->description = null;
        $this->alreadyInSave = false;
        $this->clearAllReferences();
        $this->applyDefaultValues();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references and back-references to other model objects or collections of model objects.
     *
     * This method is used to reset all php object references (not the actual reference in the database).
     * Necessary for object serialisation.
     *
     * @param      boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep) {
            if ($this->collTaskComments) {
                foreach ($this->collTaskComments as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collTaskTimeRecords) {
                foreach ($this->collTaskTimeRecords as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        $this->collTaskComments = null;
        $this->collTaskTimeRecords = null;
        $this->aUserRelatedByAssigneeId = null;
        $this->aUserRelatedByOwnerId = null;
        $this->aProject = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(TaskTableMap::DEFAULT_STRING_FORMAT);
    }

    /**
     * Code to be run before persisting the object
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preSave(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after persisting the object
     * @param ConnectionInterface $con
     */
    public function postSave(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before inserting to database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preInsert(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after inserting to database
     * @param ConnectionInterface $con
     */
    public function postInsert(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before updating the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preUpdate(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after updating the object in database
     * @param ConnectionInterface $con
     */
    public function postUpdate(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before deleting the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preDelete(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after deleting the object in database
     * @param ConnectionInterface $con
     */
    public function postDelete(ConnectionInterface $con = null)
    {

    }


    /**
     * Derived method to catches calls to undefined methods.
     *
     * Provides magic import/export method support (fromXML()/toXML(), fromYAML()/toYAML(), etc.).
     * Allows to define default __call() behavior if you overwrite __call()
     *
     * @param string $name
     * @param mixed  $params
     *
     * @return array|string
     */
    public function __call($name, $params)
    {
        if (0 === strpos($name, 'get')) {
            $virtualColumn = substr($name, 3);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }

            $virtualColumn = lcfirst($virtualColumn);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }
        }

        if (0 === strpos($name, 'from')) {
            $format = substr($name, 4);

            return $this->importFrom($format, reset($params));
        }

        if (0 === strpos($name, 'to')) {
            $format = substr($name, 2);
            $includeLazyLoadColumns = isset($params[0]) ? $params[0] : true;

            return $this->exportTo($format, $includeLazyLoadColumns);
        }

        throw new BadMethodCallException(sprintf('Call to undefined method: %s.', $name));
    }

}
