<?php

namespace Base;

use \Project as ChildProject;
use \ProjectMember as ChildProjectMember;
use \ProjectMemberQuery as ChildProjectMemberQuery;
use \ProjectQuery as ChildProjectQuery;
use \Task as ChildTask;
use \TaskQuery as ChildTaskQuery;
use \Exception;
use \PDO;
use Map\ProjectTableMap;
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

/**
 * Base class that represents a row from the 'projects' table.
 *
 *
 *
* @package    propel.generator..Base
*/
abstract class Project implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Map\\ProjectTableMap';


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
     * The value for the parent field.
     * @var        int
     */
    protected $parent;

    /**
     * The value for the description field.
     * @var        string
     */
    protected $description;

    /**
     * @var        ChildProject
     */
    protected $aProjectRelatedByParent;

    /**
     * @var        ObjectCollection|ChildProjectMember[] Collection to store aggregation of ChildProjectMember objects.
     */
    protected $collProjectMembers;
    protected $collProjectMembersPartial;

    /**
     * @var        ObjectCollection|ChildProject[] Collection to store aggregation of ChildProject objects.
     */
    protected $collProjectsRelatedById;
    protected $collProjectsRelatedByIdPartial;

    /**
     * @var        ObjectCollection|ChildTask[] Collection to store aggregation of ChildTask objects.
     */
    protected $collTasks;
    protected $collTasksPartial;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var boolean
     */
    protected $alreadyInSave = false;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildProjectMember[]
     */
    protected $projectMembersScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildProject[]
     */
    protected $projectsRelatedByIdScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildTask[]
     */
    protected $tasksScheduledForDeletion = null;

    /**
     * Initializes internal state of Base\Project object.
     */
    public function __construct()
    {
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
     * Compares this with another <code>Project</code> instance.  If
     * <code>obj</code> is an instance of <code>Project</code>, delegates to
     * <code>equals(Project)</code>.  Otherwise, returns <code>false</code>.
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
     * @return $this|Project The current object, for fluid interface
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
     * Get the [parent] column value.
     *
     * @return int
     */
    public function getParent()
    {
        return $this->parent;
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
     * @return $this|\Project The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[ProjectTableMap::COL_ID] = true;
        }

        return $this;
    } // setId()

    /**
     * Set the value of [title] column.
     *
     * @param  string $v new value
     * @return $this|\Project The current object (for fluent API support)
     */
    public function setTitle($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->title !== $v) {
            $this->title = $v;
            $this->modifiedColumns[ProjectTableMap::COL_TITLE] = true;
        }

        return $this;
    } // setTitle()

    /**
     * Set the value of [parent] column.
     *
     * @param  int $v new value
     * @return $this|\Project The current object (for fluent API support)
     */
    public function setParent($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->parent !== $v) {
            $this->parent = $v;
            $this->modifiedColumns[ProjectTableMap::COL_PARENT] = true;
        }

        if ($this->aProjectRelatedByParent !== null && $this->aProjectRelatedByParent->getId() !== $v) {
            $this->aProjectRelatedByParent = null;
        }

        return $this;
    } // setParent()

    /**
     * Set the value of [description] column.
     *
     * @param  string $v new value
     * @return $this|\Project The current object (for fluent API support)
     */
    public function setDescription($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->description !== $v) {
            $this->description = $v;
            $this->modifiedColumns[ProjectTableMap::COL_DESCRIPTION] = true;
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

            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : ProjectTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : ProjectTableMap::translateFieldName('Title', TableMap::TYPE_PHPNAME, $indexType)];
            $this->title = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : ProjectTableMap::translateFieldName('Parent', TableMap::TYPE_PHPNAME, $indexType)];
            $this->parent = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : ProjectTableMap::translateFieldName('Description', TableMap::TYPE_PHPNAME, $indexType)];
            $this->description = (null !== $col) ? (string) $col : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 4; // 4 = ProjectTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException(sprintf('Error populating %s object', '\\Project'), 0, $e);
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
        if ($this->aProjectRelatedByParent !== null && $this->parent !== $this->aProjectRelatedByParent->getId()) {
            $this->aProjectRelatedByParent = null;
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
            $con = Propel::getServiceContainer()->getReadConnection(ProjectTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildProjectQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aProjectRelatedByParent = null;
            $this->collProjectMembers = null;

            $this->collProjectsRelatedById = null;

            $this->collTasks = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see Project::setDeleted()
     * @see Project::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(ProjectTableMap::DATABASE_NAME);
        }

        $con->transaction(function () use ($con) {
            $deleteQuery = ChildProjectQuery::create()
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
            $con = Propel::getServiceContainer()->getWriteConnection(ProjectTableMap::DATABASE_NAME);
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
                ProjectTableMap::addInstanceToPool($this);
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

            if ($this->aProjectRelatedByParent !== null) {
                if ($this->aProjectRelatedByParent->isModified() || $this->aProjectRelatedByParent->isNew()) {
                    $affectedRows += $this->aProjectRelatedByParent->save($con);
                }
                $this->setProjectRelatedByParent($this->aProjectRelatedByParent);
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

            if ($this->projectMembersScheduledForDeletion !== null) {
                if (!$this->projectMembersScheduledForDeletion->isEmpty()) {
                    \ProjectMemberQuery::create()
                        ->filterByPrimaryKeys($this->projectMembersScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->projectMembersScheduledForDeletion = null;
                }
            }

            if ($this->collProjectMembers !== null) {
                foreach ($this->collProjectMembers as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->projectsRelatedByIdScheduledForDeletion !== null) {
                if (!$this->projectsRelatedByIdScheduledForDeletion->isEmpty()) {
                    \ProjectQuery::create()
                        ->filterByPrimaryKeys($this->projectsRelatedByIdScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->projectsRelatedByIdScheduledForDeletion = null;
                }
            }

            if ($this->collProjectsRelatedById !== null) {
                foreach ($this->collProjectsRelatedById as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->tasksScheduledForDeletion !== null) {
                if (!$this->tasksScheduledForDeletion->isEmpty()) {
                    \TaskQuery::create()
                        ->filterByPrimaryKeys($this->tasksScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->tasksScheduledForDeletion = null;
                }
            }

            if ($this->collTasks !== null) {
                foreach ($this->collTasks as $referrerFK) {
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

        $this->modifiedColumns[ProjectTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . ProjectTableMap::COL_ID . ')');
        }
        if (null === $this->id) {
            try {
                $dataFetcher = $con->query("SELECT nextval('projects_id_seq')");
                $this->id = $dataFetcher->fetchColumn();
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', 0, $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(ProjectTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'id';
        }
        if ($this->isColumnModified(ProjectTableMap::COL_TITLE)) {
            $modifiedColumns[':p' . $index++]  = 'title';
        }
        if ($this->isColumnModified(ProjectTableMap::COL_PARENT)) {
            $modifiedColumns[':p' . $index++]  = 'parent';
        }
        if ($this->isColumnModified(ProjectTableMap::COL_DESCRIPTION)) {
            $modifiedColumns[':p' . $index++]  = 'description';
        }

        $sql = sprintf(
            'INSERT INTO projects (%s) VALUES (%s)',
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
                    case 'parent':
                        $stmt->bindValue($identifier, $this->parent, PDO::PARAM_INT);
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
        $pos = ProjectTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
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
                return $this->getParent();
                break;
            case 3:
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

        if (isset($alreadyDumpedObjects['Project'][$this->hashCode()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Project'][$this->hashCode()] = true;
        $keys = ProjectTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getTitle(),
            $keys[2] => $this->getParent(),
            $keys[3] => $this->getDescription(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aProjectRelatedByParent) {

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

                $result[$key] = $this->aProjectRelatedByParent->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collProjectMembers) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'projectMembers';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'project_memberss';
                        break;
                    default:
                        $key = 'ProjectMembers';
                }

                $result[$key] = $this->collProjectMembers->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collProjectsRelatedById) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'projects';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'projectss';
                        break;
                    default:
                        $key = 'Projects';
                }

                $result[$key] = $this->collProjectsRelatedById->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collTasks) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'tasks';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'taskss';
                        break;
                    default:
                        $key = 'Tasks';
                }

                $result[$key] = $this->collTasks->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
     * @return $this|\Project
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = ProjectTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param  int $pos position in xml schema
     * @param  mixed $value field value
     * @return $this|\Project
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
                $this->setParent($value);
                break;
            case 3:
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
        $keys = ProjectTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setId($arr[$keys[0]]);
        }
        if (array_key_exists($keys[1], $arr)) {
            $this->setTitle($arr[$keys[1]]);
        }
        if (array_key_exists($keys[2], $arr)) {
            $this->setParent($arr[$keys[2]]);
        }
        if (array_key_exists($keys[3], $arr)) {
            $this->setDescription($arr[$keys[3]]);
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
     * @return $this|\Project The current object, for fluid interface
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
        $criteria = new Criteria(ProjectTableMap::DATABASE_NAME);

        if ($this->isColumnModified(ProjectTableMap::COL_ID)) {
            $criteria->add(ProjectTableMap::COL_ID, $this->id);
        }
        if ($this->isColumnModified(ProjectTableMap::COL_TITLE)) {
            $criteria->add(ProjectTableMap::COL_TITLE, $this->title);
        }
        if ($this->isColumnModified(ProjectTableMap::COL_PARENT)) {
            $criteria->add(ProjectTableMap::COL_PARENT, $this->parent);
        }
        if ($this->isColumnModified(ProjectTableMap::COL_DESCRIPTION)) {
            $criteria->add(ProjectTableMap::COL_DESCRIPTION, $this->description);
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
        $criteria = ChildProjectQuery::create();
        $criteria->add(ProjectTableMap::COL_ID, $this->id);

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
     * @param      object $copyObj An object of \Project (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setTitle($this->getTitle());
        $copyObj->setParent($this->getParent());
        $copyObj->setDescription($this->getDescription());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getProjectMembers() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addProjectMember($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getProjectsRelatedById() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addProjectRelatedById($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getTasks() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addTask($relObj->copy($deepCopy));
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
     * @return \Project Clone of current object.
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
     * Declares an association between this object and a ChildProject object.
     *
     * @param  ChildProject $v
     * @return $this|\Project The current object (for fluent API support)
     * @throws PropelException
     */
    public function setProjectRelatedByParent(ChildProject $v = null)
    {
        if ($v === null) {
            $this->setParent(NULL);
        } else {
            $this->setParent($v->getId());
        }

        $this->aProjectRelatedByParent = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildProject object, it will not be re-added.
        if ($v !== null) {
            $v->addProjectRelatedById($this);
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
    public function getProjectRelatedByParent(ConnectionInterface $con = null)
    {
        if ($this->aProjectRelatedByParent === null && ($this->parent !== null)) {
            $this->aProjectRelatedByParent = ChildProjectQuery::create()->findPk($this->parent, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aProjectRelatedByParent->addProjectsRelatedById($this);
             */
        }

        return $this->aProjectRelatedByParent;
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
        if ('ProjectMember' == $relationName) {
            return $this->initProjectMembers();
        }
        if ('ProjectRelatedById' == $relationName) {
            return $this->initProjectsRelatedById();
        }
        if ('Task' == $relationName) {
            return $this->initTasks();
        }
    }

    /**
     * Clears out the collProjectMembers collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addProjectMembers()
     */
    public function clearProjectMembers()
    {
        $this->collProjectMembers = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collProjectMembers collection loaded partially.
     */
    public function resetPartialProjectMembers($v = true)
    {
        $this->collProjectMembersPartial = $v;
    }

    /**
     * Initializes the collProjectMembers collection.
     *
     * By default this just sets the collProjectMembers collection to an empty array (like clearcollProjectMembers());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initProjectMembers($overrideExisting = true)
    {
        if (null !== $this->collProjectMembers && !$overrideExisting) {
            return;
        }
        $this->collProjectMembers = new ObjectCollection();
        $this->collProjectMembers->setModel('\ProjectMember');
    }

    /**
     * Gets an array of ChildProjectMember objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildProject is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildProjectMember[] List of ChildProjectMember objects
     * @throws PropelException
     */
    public function getProjectMembers(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collProjectMembersPartial && !$this->isNew();
        if (null === $this->collProjectMembers || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collProjectMembers) {
                // return empty collection
                $this->initProjectMembers();
            } else {
                $collProjectMembers = ChildProjectMemberQuery::create(null, $criteria)
                    ->filterByProject($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collProjectMembersPartial && count($collProjectMembers)) {
                        $this->initProjectMembers(false);

                        foreach ($collProjectMembers as $obj) {
                            if (false == $this->collProjectMembers->contains($obj)) {
                                $this->collProjectMembers->append($obj);
                            }
                        }

                        $this->collProjectMembersPartial = true;
                    }

                    return $collProjectMembers;
                }

                if ($partial && $this->collProjectMembers) {
                    foreach ($this->collProjectMembers as $obj) {
                        if ($obj->isNew()) {
                            $collProjectMembers[] = $obj;
                        }
                    }
                }

                $this->collProjectMembers = $collProjectMembers;
                $this->collProjectMembersPartial = false;
            }
        }

        return $this->collProjectMembers;
    }

    /**
     * Sets a collection of ChildProjectMember objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $projectMembers A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildProject The current object (for fluent API support)
     */
    public function setProjectMembers(Collection $projectMembers, ConnectionInterface $con = null)
    {
        /** @var ChildProjectMember[] $projectMembersToDelete */
        $projectMembersToDelete = $this->getProjectMembers(new Criteria(), $con)->diff($projectMembers);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->projectMembersScheduledForDeletion = clone $projectMembersToDelete;

        foreach ($projectMembersToDelete as $projectMemberRemoved) {
            $projectMemberRemoved->setProject(null);
        }

        $this->collProjectMembers = null;
        foreach ($projectMembers as $projectMember) {
            $this->addProjectMember($projectMember);
        }

        $this->collProjectMembers = $projectMembers;
        $this->collProjectMembersPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ProjectMember objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related ProjectMember objects.
     * @throws PropelException
     */
    public function countProjectMembers(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collProjectMembersPartial && !$this->isNew();
        if (null === $this->collProjectMembers || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collProjectMembers) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getProjectMembers());
            }

            $query = ChildProjectMemberQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProject($this)
                ->count($con);
        }

        return count($this->collProjectMembers);
    }

    /**
     * Method called to associate a ChildProjectMember object to this object
     * through the ChildProjectMember foreign key attribute.
     *
     * @param  ChildProjectMember $l ChildProjectMember
     * @return $this|\Project The current object (for fluent API support)
     */
    public function addProjectMember(ChildProjectMember $l)
    {
        if ($this->collProjectMembers === null) {
            $this->initProjectMembers();
            $this->collProjectMembersPartial = true;
        }

        if (!$this->collProjectMembers->contains($l)) {
            $this->doAddProjectMember($l);
        }

        return $this;
    }

    /**
     * @param ChildProjectMember $projectMember The ChildProjectMember object to add.
     */
    protected function doAddProjectMember(ChildProjectMember $projectMember)
    {
        $this->collProjectMembers[]= $projectMember;
        $projectMember->setProject($this);
    }

    /**
     * @param  ChildProjectMember $projectMember The ChildProjectMember object to remove.
     * @return $this|ChildProject The current object (for fluent API support)
     */
    public function removeProjectMember(ChildProjectMember $projectMember)
    {
        if ($this->getProjectMembers()->contains($projectMember)) {
            $pos = $this->collProjectMembers->search($projectMember);
            $this->collProjectMembers->remove($pos);
            if (null === $this->projectMembersScheduledForDeletion) {
                $this->projectMembersScheduledForDeletion = clone $this->collProjectMembers;
                $this->projectMembersScheduledForDeletion->clear();
            }
            $this->projectMembersScheduledForDeletion[]= clone $projectMember;
            $projectMember->setProject(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Project is new, it will return
     * an empty collection; or if this Project has previously
     * been saved, it will retrieve related ProjectMembers from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Project.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildProjectMember[] List of ChildProjectMember objects
     */
    public function getProjectMembersJoinPerson(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildProjectMemberQuery::create(null, $criteria);
        $query->joinWith('Person', $joinBehavior);

        return $this->getProjectMembers($query, $con);
    }

    /**
     * Clears out the collProjectsRelatedById collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addProjectsRelatedById()
     */
    public function clearProjectsRelatedById()
    {
        $this->collProjectsRelatedById = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collProjectsRelatedById collection loaded partially.
     */
    public function resetPartialProjectsRelatedById($v = true)
    {
        $this->collProjectsRelatedByIdPartial = $v;
    }

    /**
     * Initializes the collProjectsRelatedById collection.
     *
     * By default this just sets the collProjectsRelatedById collection to an empty array (like clearcollProjectsRelatedById());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initProjectsRelatedById($overrideExisting = true)
    {
        if (null !== $this->collProjectsRelatedById && !$overrideExisting) {
            return;
        }
        $this->collProjectsRelatedById = new ObjectCollection();
        $this->collProjectsRelatedById->setModel('\Project');
    }

    /**
     * Gets an array of ChildProject objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildProject is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildProject[] List of ChildProject objects
     * @throws PropelException
     */
    public function getProjectsRelatedById(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collProjectsRelatedByIdPartial && !$this->isNew();
        if (null === $this->collProjectsRelatedById || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collProjectsRelatedById) {
                // return empty collection
                $this->initProjectsRelatedById();
            } else {
                $collProjectsRelatedById = ChildProjectQuery::create(null, $criteria)
                    ->filterByProjectRelatedByParent($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collProjectsRelatedByIdPartial && count($collProjectsRelatedById)) {
                        $this->initProjectsRelatedById(false);

                        foreach ($collProjectsRelatedById as $obj) {
                            if (false == $this->collProjectsRelatedById->contains($obj)) {
                                $this->collProjectsRelatedById->append($obj);
                            }
                        }

                        $this->collProjectsRelatedByIdPartial = true;
                    }

                    return $collProjectsRelatedById;
                }

                if ($partial && $this->collProjectsRelatedById) {
                    foreach ($this->collProjectsRelatedById as $obj) {
                        if ($obj->isNew()) {
                            $collProjectsRelatedById[] = $obj;
                        }
                    }
                }

                $this->collProjectsRelatedById = $collProjectsRelatedById;
                $this->collProjectsRelatedByIdPartial = false;
            }
        }

        return $this->collProjectsRelatedById;
    }

    /**
     * Sets a collection of ChildProject objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $projectsRelatedById A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildProject The current object (for fluent API support)
     */
    public function setProjectsRelatedById(Collection $projectsRelatedById, ConnectionInterface $con = null)
    {
        /** @var ChildProject[] $projectsRelatedByIdToDelete */
        $projectsRelatedByIdToDelete = $this->getProjectsRelatedById(new Criteria(), $con)->diff($projectsRelatedById);


        $this->projectsRelatedByIdScheduledForDeletion = $projectsRelatedByIdToDelete;

        foreach ($projectsRelatedByIdToDelete as $projectRelatedByIdRemoved) {
            $projectRelatedByIdRemoved->setProjectRelatedByParent(null);
        }

        $this->collProjectsRelatedById = null;
        foreach ($projectsRelatedById as $projectRelatedById) {
            $this->addProjectRelatedById($projectRelatedById);
        }

        $this->collProjectsRelatedById = $projectsRelatedById;
        $this->collProjectsRelatedByIdPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Project objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Project objects.
     * @throws PropelException
     */
    public function countProjectsRelatedById(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collProjectsRelatedByIdPartial && !$this->isNew();
        if (null === $this->collProjectsRelatedById || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collProjectsRelatedById) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getProjectsRelatedById());
            }

            $query = ChildProjectQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProjectRelatedByParent($this)
                ->count($con);
        }

        return count($this->collProjectsRelatedById);
    }

    /**
     * Method called to associate a ChildProject object to this object
     * through the ChildProject foreign key attribute.
     *
     * @param  ChildProject $l ChildProject
     * @return $this|\Project The current object (for fluent API support)
     */
    public function addProjectRelatedById(ChildProject $l)
    {
        if ($this->collProjectsRelatedById === null) {
            $this->initProjectsRelatedById();
            $this->collProjectsRelatedByIdPartial = true;
        }

        if (!$this->collProjectsRelatedById->contains($l)) {
            $this->doAddProjectRelatedById($l);
        }

        return $this;
    }

    /**
     * @param ChildProject $projectRelatedById The ChildProject object to add.
     */
    protected function doAddProjectRelatedById(ChildProject $projectRelatedById)
    {
        $this->collProjectsRelatedById[]= $projectRelatedById;
        $projectRelatedById->setProjectRelatedByParent($this);
    }

    /**
     * @param  ChildProject $projectRelatedById The ChildProject object to remove.
     * @return $this|ChildProject The current object (for fluent API support)
     */
    public function removeProjectRelatedById(ChildProject $projectRelatedById)
    {
        if ($this->getProjectsRelatedById()->contains($projectRelatedById)) {
            $pos = $this->collProjectsRelatedById->search($projectRelatedById);
            $this->collProjectsRelatedById->remove($pos);
            if (null === $this->projectsRelatedByIdScheduledForDeletion) {
                $this->projectsRelatedByIdScheduledForDeletion = clone $this->collProjectsRelatedById;
                $this->projectsRelatedByIdScheduledForDeletion->clear();
            }
            $this->projectsRelatedByIdScheduledForDeletion[]= $projectRelatedById;
            $projectRelatedById->setProjectRelatedByParent(null);
        }

        return $this;
    }

    /**
     * Clears out the collTasks collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addTasks()
     */
    public function clearTasks()
    {
        $this->collTasks = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collTasks collection loaded partially.
     */
    public function resetPartialTasks($v = true)
    {
        $this->collTasksPartial = $v;
    }

    /**
     * Initializes the collTasks collection.
     *
     * By default this just sets the collTasks collection to an empty array (like clearcollTasks());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initTasks($overrideExisting = true)
    {
        if (null !== $this->collTasks && !$overrideExisting) {
            return;
        }
        $this->collTasks = new ObjectCollection();
        $this->collTasks->setModel('\Task');
    }

    /**
     * Gets an array of ChildTask objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildProject is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildTask[] List of ChildTask objects
     * @throws PropelException
     */
    public function getTasks(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collTasksPartial && !$this->isNew();
        if (null === $this->collTasks || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collTasks) {
                // return empty collection
                $this->initTasks();
            } else {
                $collTasks = ChildTaskQuery::create(null, $criteria)
                    ->filterByProject($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collTasksPartial && count($collTasks)) {
                        $this->initTasks(false);

                        foreach ($collTasks as $obj) {
                            if (false == $this->collTasks->contains($obj)) {
                                $this->collTasks->append($obj);
                            }
                        }

                        $this->collTasksPartial = true;
                    }

                    return $collTasks;
                }

                if ($partial && $this->collTasks) {
                    foreach ($this->collTasks as $obj) {
                        if ($obj->isNew()) {
                            $collTasks[] = $obj;
                        }
                    }
                }

                $this->collTasks = $collTasks;
                $this->collTasksPartial = false;
            }
        }

        return $this->collTasks;
    }

    /**
     * Sets a collection of ChildTask objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $tasks A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildProject The current object (for fluent API support)
     */
    public function setTasks(Collection $tasks, ConnectionInterface $con = null)
    {
        /** @var ChildTask[] $tasksToDelete */
        $tasksToDelete = $this->getTasks(new Criteria(), $con)->diff($tasks);


        $this->tasksScheduledForDeletion = $tasksToDelete;

        foreach ($tasksToDelete as $taskRemoved) {
            $taskRemoved->setProject(null);
        }

        $this->collTasks = null;
        foreach ($tasks as $task) {
            $this->addTask($task);
        }

        $this->collTasks = $tasks;
        $this->collTasksPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Task objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Task objects.
     * @throws PropelException
     */
    public function countTasks(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collTasksPartial && !$this->isNew();
        if (null === $this->collTasks || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collTasks) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getTasks());
            }

            $query = ChildTaskQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProject($this)
                ->count($con);
        }

        return count($this->collTasks);
    }

    /**
     * Method called to associate a ChildTask object to this object
     * through the ChildTask foreign key attribute.
     *
     * @param  ChildTask $l ChildTask
     * @return $this|\Project The current object (for fluent API support)
     */
    public function addTask(ChildTask $l)
    {
        if ($this->collTasks === null) {
            $this->initTasks();
            $this->collTasksPartial = true;
        }

        if (!$this->collTasks->contains($l)) {
            $this->doAddTask($l);
        }

        return $this;
    }

    /**
     * @param ChildTask $task The ChildTask object to add.
     */
    protected function doAddTask(ChildTask $task)
    {
        $this->collTasks[]= $task;
        $task->setProject($this);
    }

    /**
     * @param  ChildTask $task The ChildTask object to remove.
     * @return $this|ChildProject The current object (for fluent API support)
     */
    public function removeTask(ChildTask $task)
    {
        if ($this->getTasks()->contains($task)) {
            $pos = $this->collTasks->search($task);
            $this->collTasks->remove($pos);
            if (null === $this->tasksScheduledForDeletion) {
                $this->tasksScheduledForDeletion = clone $this->collTasks;
                $this->tasksScheduledForDeletion->clear();
            }
            $this->tasksScheduledForDeletion[]= $task;
            $task->setProject(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Project is new, it will return
     * an empty collection; or if this Project has previously
     * been saved, it will retrieve related Tasks from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Project.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildTask[] List of ChildTask objects
     */
    public function getTasksJoinUserRelatedByAssigneeId(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildTaskQuery::create(null, $criteria);
        $query->joinWith('UserRelatedByAssigneeId', $joinBehavior);

        return $this->getTasks($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Project is new, it will return
     * an empty collection; or if this Project has previously
     * been saved, it will retrieve related Tasks from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Project.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildTask[] List of ChildTask objects
     */
    public function getTasksJoinUserRelatedByOwnerId(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildTaskQuery::create(null, $criteria);
        $query->joinWith('UserRelatedByOwnerId', $joinBehavior);

        return $this->getTasks($query, $con);
    }

    /**
     * Clears the current object, sets all attributes to their default values and removes
     * outgoing references as well as back-references (from other objects to this one. Results probably in a database
     * change of those foreign objects when you call `save` there).
     */
    public function clear()
    {
        if (null !== $this->aProjectRelatedByParent) {
            $this->aProjectRelatedByParent->removeProjectRelatedById($this);
        }
        $this->id = null;
        $this->title = null;
        $this->parent = null;
        $this->description = null;
        $this->alreadyInSave = false;
        $this->clearAllReferences();
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
            if ($this->collProjectMembers) {
                foreach ($this->collProjectMembers as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collProjectsRelatedById) {
                foreach ($this->collProjectsRelatedById as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collTasks) {
                foreach ($this->collTasks as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        $this->collProjectMembers = null;
        $this->collProjectsRelatedById = null;
        $this->collTasks = null;
        $this->aProjectRelatedByParent = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(ProjectTableMap::DEFAULT_STRING_FORMAT);
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
