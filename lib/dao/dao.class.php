<?php
/**
 * The dao and sql class file of ZenTaoPHP framework.
 *
 * The author disclaims copyright to this source code.  In place of
 * a legal notice, here is a blessing:
 * 
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */

/**
 * DAO, data access object.
 * 
 * @package framework
 */
class dao
{
    /**
     * The global app object.
     * 全局对象$global
     * 
     * @var object
     * @access protected
     */
    protected $app;

    /**
     * The global config object.
     * 全局对象$config
     * 
     * @var object
     * @access protected
     */
    protected $config;

    /**
     * The global lang object.
     * 全局对象$lang
     * 
     * @var object
     * @access protected
     */
    protected $lang;

    /**
     * The global dbh(database handler) object.
     * 全局对象$dbh
     * 
     * @var object
     * @access protected
     */
    protected $dbh;

    /**
     * The global slaveDBH(database handler) object.
     * 全局对象$slaveDBH
     * 
     * @var object
     * @access protected
     */
    protected $slaveDBH;

    /**
     * The sql object, used to create the query sql.
     * sql对象，用于生成sql语句
     * 
     * @var object
     * @access protected
     */
    public $sqlobj;

    /**
     * The table of current query.
     * 正在使用的表
     * 
     * @var string
     * @access public
     */
    public $table;

    /**
     * The alias of $this->table.
     * $this->table的别名
     * 
     * @var string
     * @access public
     */
    public $alias;

    /**
     * The fields will be returned.
     * 查询的字段
     * 
     * @var string
     * @access public
     */
    public $fields;

    /**
     * The query mode, raw or magic.
     * 查询模式，raw模式用于正常的select update等sql拼接操作，magic模式用于findByXXX等魔术方法
     * 
     * This var is used to diff dao::from() with sql::from().
     *
     * @var string
     * @access public
     */
    public $mode;

    /**
     * The query method: insert, select, update, delete, replace.
     * 执行方式：insert, select, update, delete, replace
     *
     * @var string
     * @access public
     */
    public $method;

    /**
     * The queries executed. Every query will be saved in this array.
     * 执行的请求，所有的查询都保存在该数组。
     * 
     * @var array
     * @access public
     */
    static public $querys = array();

    /**
     * The errors.
     * 存放错误的数组。
     * 
     * @var array
     * @access public
     */
    static public $errors = array();

    /**
     * The construct method.
     * 构造方法。
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        global $app, $config, $lang, $dbh, $slaveDBH;
        $this->app      = $app;
        $this->config   = $config;
        $this->lang     = $lang;
        $this->dbh      = $dbh;
        $this->slaveDBH = $slaveDBH ? $slaveDBH : false;

        $this->reset();
    }

    /**
     * Set the $table property.
     * 设置$table属性。
     * 
     * @param  string $table 
     * @access private
     * @return void
     */
    private function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * Set the $alias property.
     * 设置$alias属性。
     * 
     * @param  string $alias 
     * @access private
     * @return void
     */
    private function setAlias($alias)
    {
        $this->alias = $alias;
    }

    /**
     * Set the $fields property.
     * 设置$fields属性。
     * 
     * @param  string $fields 
     * @access private
     * @return void
     */
    private function setFields($fields)
    {
        $this->fields = $fields;
    }

    /**
     * Reset the vars.
     * 重置属性。
     * 
     * @access private
     * @return void
     */
    private function reset()
    {
        $this->setFields('');
        $this->setTable('');
        $this->setAlias('');
        $this->setMode('');
        $this->setMethod('');
    }

    //-------------------- According to the query method, call according method of sql class. --------------------//
    //-------------------- 根据请求的方式，调用sql类相应的方法。 --------------------//

    /**
     * Set the query mode. If the method if like findByxxx, the mode is magic. Else, the mode is raw.
     * 设置请求模式。像findByxxx之类的方法，使用的是magic模式；其他方法使用的是raw模式。
     * 
     * @param  string $mode     magic|raw
     * @access private
     * @return void
     */
    private function setMode($mode = '')
    {
        $this->mode = $mode;
    }

    /**
     * Set the query method: select|update|insert|delete|replace 
     * 设置请求方法：select|update|insert|delete|replace 。
     * 
     * @param  string $method 
     * @access private
     * @return void
     */
    private function setMethod($method = '')
    {
        $this->method = $method;
    }

    /**
     * The select method, call sql::select().
     * select方法，调用sql::select()。
     * 
     * @param  string $fields 
     * @access public
     * @return object the dao object self.
     */
    public function select($fields = '*')
    {
        $this->setMode('raw');
        $this->setMethod('select');
        $this->sqlobj = sql::select($fields);
        return $this;
    }

    /**
     * The update method, call sql::update().
     * update方法，调用sql::update()。
     * 
     * @param  string $table 
     * @access public
     * @return object the dao object self.
     */
    public function update($table)
    {
        $this->setMode('raw');
        $this->setMethod('update');
        $this->sqlobj = sql::update($table);
        $this->setTable($table);
        return $this;
    }

    /**
     * The delete method, call sql::delete().
     * delete方法，调用sql::delete()。
     * 
     * @access public
     * @return object the dao object self.
     */
    public function delete()
    {
        $this->setMode('raw');
        $this->setMethod('delete');
        $this->sqlobj = sql::delete();
        return $this;
    }

    /**
     * The insert method, call sql::insert().
     * insert方法，调用sql::insert()。
     * 
     * @param  string $table 
     * @access public
     * @return object the dao object self.
     */
    public function insert($table)
    {
        $this->setMode('raw');
        $this->setMethod('insert');
        $this->sqlobj = sql::insert($table);
        $this->setTable($table);
        return $this;
    }

    /**
     * The replace method, call sql::replace().
     * replace方法，调用sql::replace()。
     * 
     * @param  string $table 
     * @access public
     * @return object the dao object self.
     */
    public function replace($table)
    {
        $this->setMode('raw');
        $this->setMethod('replace');
        $this->sqlobj = sql::replace($table);
        $this->setTable($table);
        return $this;
    }

    /**
     * Set the from table.
     * 设置要操作的表。
     * 
     * @param  string $table 
     * @access public
     * @return object the dao object self.
     */
    public function from($table) 
    {
        $this->setTable($table);
        if($this->mode == 'raw') $this->sqlobj->from($table);
        return $this;
    }

    /**
     * Set the fields.
     * 设置字段。
     * 
     * @param  string $fields 
     * @access public
     * @return object the dao object self.
     */
    public function fields($fields)
    {
        $this->setFields($fields);
        return $this;
    }

    /**
     * Alias a table, equal the AS keyword. (Don't use AS, because it's a php keyword.)
     * 表别名，相当于sql里的AS。（as是php的关键词，使用alias代替）
     * 
     * @param  string $alias 
     * @access public
     * @return object the dao object self.
     */
    public function alias($alias)
    {
        if(empty($this->alias)) $this->setAlias($alias);
        $this->sqlobj->alias($alias);
        return $this;
    }

    /**
     * Set the data to update or insert.
     * 设置需要更新或插入的数据。
     * 
     * @param  object $data  the data object or array
     * @access public
     * @return object the dao object self.
     */
    public function data($data)
    {
        if(!is_object($data)) $data = (object)$data;
        $this->sqlobj->data($data);
        return $this;
    }

    //-------------------- The sql related method. --------------------//
    //-------------------- sql相关的方法。 --------------------//

    /**
     * Get the sql string.
     * 获取sql字符串。
     * 
     * @access public
     * @return string the sql string after process.
     */
    public function get()
    {
        return $this->processSQL();
    }

    /**
     * Print the sql string.
     * 打印sql字符串。
     * 
     * @access public
     * @return void
     */
    public function printSQL()
    {
        echo $this->processSQL();
    }

    /**
     * Process the sql, replace the table, fields.
     * 处理sql语句，替换表和字段。
     * 
     * @access private
     * @return string the sql string after process.
     */
    private function processSQL()
    {
        $sql = $this->sqlobj->get();

        /** 
         * If the mode is magic, process the $fields and $table.
         * 如果是magic模式，处理表和字段。
         **/
        if($this->mode == 'magic')
        {
            if($this->fields == '') $this->fields = '*';
            if($this->table == '')  $this->app->error('Must set the table name', __FILE__, __LINE__, $exit = true);
            $sql = sprintf($this->sqlobj->get(), $this->fields, $this->table);
        }

        self::$querys[] = $sql;
        return $sql;
    }

    //-------------------- Query related methods. --------------------//
    //-------------------- 查询相关方法。 --------------------//
    
    /**
     * Set the dbh. 
     * 设置$dbh，数据库连接句柄。
     * 
     * You can use like this: $this->dao->dbh($dbh), thus you can handle two database.
     *
     * @param  object $dbh 
     * @access public
     * @return object the dao object self.
     */
    public function dbh($dbh)
    {
        $this->dbh = $dbh;
        return $this;
    }

    /**
     * Query the sql, return the statement object.
     * 执行SQL语句，返回PDOStatement结果集。
     * 
     * @access public
     * @return object   the PDOStatement object.
     */
    public function query()
    {
        if(!empty(dao::$errors)) return new PDOStatement();   // If any error, return an empty statement object to make sure the remain method to execute.

        $sql = $this->processSQL();
        try
        {
            $method = $this->method;
            $this->reset();

            if($this->slaveDBH and $method == 'select')
            {
                return $this->slaveDBH->query($sql);
            }
            else
            {
                return $this->dbh->query($sql);
            }
        }
        catch (PDOException $e) 
        {
            $this->app->error($e->getMessage() . "<p>The sql is: $sql</p>", __FILE__, __LINE__, $exit = true);
        }
    }

    /**
     * Page the records, set the limit part auto.
     * 将记录进行分页，自动
     * 
     * @param  object $pager 
     * @access public
     * @return object the dao object self.
     */
    public function page($pager)
    {
        if(!is_object($pager)) return $this;

        /*
         * If the record total is 0, compute it. 
         * 如果$pager的总记录为0，需要计算总结果数。
         **/
        if($pager->recTotal == 0)
        {
            /* Get the SELECT, FROM position, thus get the fields, replace it by count(*). */
            $sql       = $this->get();
            $selectPOS = strpos($sql, 'SELECT') + strlen('SELECT');
            $fromPOS   = strpos($sql, 'FROM');
            $fields    = substr($sql, $selectPOS, $fromPOS - $selectPOS );
            $sql       = str_replace($fields, ' COUNT(*) AS recTotal ', $sql);

            /*
             * Remove the part after order and limit.
             * 去掉SQL语句中order和limit之后的部分。
             **/
            $subLength = strlen($sql);
            $orderPOS  = strripos($sql, 'order');
            $limitPOS  = strripos($sql , 'limit');
            if($limitPOS) $subLength = $limitPOS;
            if($orderPOS) $subLength = $orderPOS;
            $sql = substr($sql, 0, $subLength);
            self::$querys[] = $sql;

            /* 
             * Get the records count.
             * 获取记录数。
             **/
            try
            {
                $row = $this->dbh->query($sql)->fetch(PDO::FETCH_OBJ);
            }
            catch (PDOException $e) 
            {
                $this->app->error($e->getMessage() . "<p>The sql is: $sql</p>", __FILE__, __LINE__, $exit = true);
            }

            $pager->setRecTotal($row->recTotal);
            $pager->setPageTotal();
        }
        $this->sqlobj->limit($pager->limit());
        return $this;
    }

    /**
    /* Execute the sql. It's different with query(), which return the stmt object. But this not.
     * 执行SQL。query()会返回stmt对象，该方法只返回更改或删除的记录数。
     * 
     * @access public
     * @return int the modified or deleted records. 更改或删除的记录数。
     */
    public function exec()
    {
        if(!empty(dao::$errors)) return new PDOStatement();   // If any error, return an empty statement object to make sure the remain method to execute.

        $sql = $this->processSQL();
        try
        {
            $this->reset();
            return $this->dbh->exec($sql);
        }
        catch (PDOException $e) 
        {
            $this->app->error($e->getMessage() . "<p>The sql is: $sql</p>", __FILE__, __LINE__, $exit = true);
        }
    }

    //-------------------- Fetch related methods. -------------------//
    //-------------------- Fetch相关方法。 -------------------//

    /**
     * Fetch one record.
     * 获取一个记录。
     * 
     * @param  string $field        if the field is set, only return the value of this field, else return this record
     *                              如果已经设置获取的字段，则只返回这个字段的值，否则返回这个记录。
     * @access public
     * @return object|mixed
     */
    public function fetch($field = '')
    {
        if(empty($field)) return $this->query()->fetch();
        $this->setFields($field);
        $result = $this->query()->fetch(PDO::FETCH_OBJ);
        if($result) return $result->$field;
    }

    /**
     * Fetch all records.
     * 获取所有记录。
     * 
     * @param  string $keyField     the key field, thus the return records is keyed by this field
     *                              返回以该字段做键的记录
     * @access public
     * @return array the records
     */
    public function fetchAll($keyField = '')
    {
        $stmt = $this->query();
        if(empty($keyField)) return $stmt->fetchAll();
        $rows = array();
        while($row = $stmt->fetch()) $rows[$row->$keyField] = $row;
        return $rows;
    }

    /**
     * Fetch all records and group them by one field.
     * 获取所有记录并将按照字段分组。
     * 
     * @param  string $groupField   the field to group by        分组的字段
     * @param  string $keyField     the field of key             键字段
     * @access public
     * @return array the records.
     */
    public function fetchGroup($groupField, $keyField = '')
    {
        $stmt = $this->query();
        $rows = array();
        while($row = $stmt->fetch())
        {
            empty($keyField) ?  $rows[$row->$groupField][] = $row : $rows[$row->$groupField][$row->$keyField] = $row;
        }
        return $rows;
    }

    /**
     * Fetch array like key=>value.
     * 获取的记录是以关联数组的形式
     *
     * If the keyFiled and valueField not set, use the first and last in the record.
     * 如果没有设置参数，用首末两键作为参数。
     * 
     * @param  string $keyField 
     * @param  string $valueField 
     * @access public
     * @return array
     */
    public function fetchPairs($keyField = '', $valueField = '')
    {
        $pairs = array();
        $ready = false;
        $stmt  = $this->query();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            if(!$ready)
            {
                if(empty($keyField)) $keyField = key($row);
                if(empty($valueField)) 
                {
                    end($row);
                    $valueField = key($row);
                }
                $ready = true;
            }

            $pairs[$row[$keyField]] = $row[$valueField];
        }
        return $pairs;
    }

    /**
     * Return the last insert ID.
     * 返回最后插入的ID。
     * 
     * @access public
     * @return int
     */
    public function lastInsertID()
    {
        return $this->dbh->lastInsertID();
    }

    //-------------------- Magic methods. --------------------//
    //-------------------- 魔术方法。 --------------------//

    /**
     * Use it to do some convenient queries.
     * 解析dao的方法名，处理魔术方法。
     * 
     * @param  string $funcName  the function name to be called
     * @param  array  $funcArgs  the params
     * @access public
     * @return object the dao object self.
     */
    public function __call($funcName, $funcArgs)
    {
        $funcName = strtolower($funcName);

        /* 
         * findByxxx, xxx as will be in the where.
         * 如果是findByxxx，转换为where条件语句。
         **/
        if(strpos($funcName, 'findby') !== false)
        {
            $this->setMode('magic');
            $field = str_replace('findby', '', $funcName);
            if(count($funcArgs) == 1)
            {
                $operator = '=';
                $value    = $funcArgs[0];
            }
            else
            {
                $operator = $funcArgs[0];
                $value    = $funcArgs[1];
            }
            $this->sqlobj = sql::select('%s')->from('%s')->where($field, $operator, $value);
            return $this;
        }
        /* 
         * Fetch10. 
         * 获取指定个数的记录：fetch10 获取10条记录。
         **/
        elseif(strpos($funcName, 'fetch') !== false)
        {
            $max  = str_replace('fetch', '', $funcName);
            $stmt = $this->query();

            $rows = array();
            $key  = isset($funcArgs[0]) ? $funcArgs[0] : '';
            $i    = 0;
            while($row = $stmt->fetch())
            {
                $key ? $rows[$row->$key] = $row : $rows[] = $row;
                $i ++;
                if($i == $max) break;
            }
            return $rows;
        }
        /* 
         * Others, call the method in sql class.
         * 其他的方法，转到sqlobj对象执行。
         **/
        else
        {
            /* 
             * Create the max counts of sql class methods, and then create $arg0, $arg1...
             * 使用$arg0, $arg1... 生成调用的参数。
             **/
            for($i = 0; $i < SQL::MAX_ARGS; $i ++)
            {
                ${"arg$i"} = isset($funcArgs[$i]) ? $funcArgs[$i] : null;
            }
            $this->sqlobj->$funcName($arg0, $arg1, $arg2);
            return $this;
        }
    }

    //-------------------- Checking.--------------------//
    //-------------------- 条件检查。 --------------------//
    
    /**
     * Check a filed is satisfied with the check rule.
     * 检查字段是否满足条件。
     * 
     * @param  string $fieldName    the field to check
     * @param  string $funcName     the check rule
     * @access public
     * @return object the dao object self.
     */
    public function check($fieldName, $funcName)
    {
        /* 
         * If no this field in the data, return.
         * 如果没数据中没有该字段，直接返回。
         **/
        if(!isset($this->sqlobj->data->$fieldName)) return $this;

        /* Set the field label and value. */
        global $lang, $config, $app;
        $table      = strtolower(str_replace($config->db->prefix, '', $this->table));
        $fieldLabel = isset($lang->$table->$fieldName) ? $lang->$table->$fieldName : $fieldName;
        $value = $this->sqlobj->data->$fieldName;
        
        /* 
         * Check unique.
         * 检查唯一性。
         **/
        if($funcName == 'unique')
        {
            $args = func_get_args();
            $sql  = "SELECT COUNT(*) AS count FROM $this->table WHERE `$fieldName` = " . $this->sqlobj->quote($value); 
            if(isset($args[2])) $sql .= ' AND ' . $args[2];
            try
            {
                 $row = $this->dbh->query($sql)->fetch();
                 if($row->count != 0) $this->logError($funcName, $fieldName, $fieldLabel, array($value));
            }
            catch (PDOException $e) 
            {
                $this->app->error($e->getMessage() . "<p>The sql is: $sql</p>", __FILE__, __LINE__, $exit = true);
            }
        }
        else
        {
            /* 
             * Create the params.
             * 创建参数。
             **/
            $funcArgs = func_get_args();
            unset($funcArgs[0]);
            unset($funcArgs[1]);

            for($i = 0; $i < VALIDATER::MAX_ARGS; $i ++)
            {
                ${"arg$i"} = isset($funcArgs[$i + 2]) ? $funcArgs[$i + 2] : null;
            }
            $checkFunc = 'check' . $funcName;
            if(validater::$checkFunc($value, $arg0, $arg1, $arg2) === false)
            {
                $this->logError($funcName, $fieldName, $fieldLabel, $funcArgs);
            }
        }

        return $this;
    }

    /**
     * Check a field, if satisfied with the condition.
     * 检查一个字段是否满足条件。
     * 
     * @param  string $condition 
     * @param  string $fieldName 
     * @param  string $funcName 
     * @access public
     * @return object the dao object self.
     */
    public function checkIF($condition, $fieldName, $funcName)
    {
        if(!$condition) return $this;
        $funcArgs = func_get_args();
        for($i = 0; $i < VALIDATER::MAX_ARGS; $i ++)
        {
            ${"arg$i"} = isset($funcArgs[$i + 3]) ? $funcArgs[$i + 3] : null;
        }
        $this->check($fieldName, $funcName, $arg0, $arg1, $arg2);
        return $this;
    }

    /**
     * Batch check some fileds.
     * 批量检查字段。
     * 
     * @param  string $fields       the fields to check, join with ,
     * @param  string $funcName 
     * @access public
     * @return object the dao object self.
     */
    public function batchCheck($fields, $funcName)
    {
        $fields = explode(',', str_replace(' ', '', $fields));
        $funcArgs = func_get_args();
        for($i = 0; $i < VALIDATER::MAX_ARGS; $i ++)
        {
            ${"arg$i"} = isset($funcArgs[$i + 2]) ? $funcArgs[$i + 2] : null;
        }
        foreach($fields as $fieldName) $this->check($fieldName, $funcName, $arg0, $arg1, $arg2);
        return $this;
    }

    /**
     * Batch check fields on the condition is true.
     * 批量检查字段是否满足条件。
     * 
     * @param  string $condition 
     * @param  string $fields 
     * @param  string $funcName 
     * @access public
     * @return object the dao object self.
     */
    public function batchCheckIF($condition, $fields, $funcName)
    {
        if(!$condition) return $this;
        $fields = explode(',', str_replace(' ', '', $fields));
        $funcArgs = func_get_args();
        for($i = 0; $i < VALIDATER::MAX_ARGS; $i ++)
        {
            ${"arg$i"} = isset($funcArgs[$i + 2]) ? $funcArgs[$i + 2] : null;
        }
        foreach($fields as $fieldName) $this->check($fieldName, $funcName, $arg0, $arg1, $arg2);
        return $this;
    }

    /**
     * Check the fields according the the database schema.
     * 根据数据库结构检查字段。
     * 
     * @param  string $skipFields   fields to skip checking
     * @access public
     * @return object the dao object self.
     */
    public function autoCheck($skipFields = '')
    {
        $fields     = $this->getFieldsType();
        $skipFields = ",$skipFields,";

        foreach($fields as $fieldName => $validater)
        {
            if(strpos($skipFields, $fieldName) !== false) continue; // skip it.
            if(!isset($this->sqlobj->data->$fieldName)) continue;
            if($validater['rule'] == 'skip') continue;
            $options = array();
            if(isset($validater['options'])) $options = array_values($validater['options']);
            for($i = 0; $i < VALIDATER::MAX_ARGS; $i ++)
            {
                ${"arg$i"} = isset($options[$i]) ? $options[$i] : null;
            }
            $this->check($fieldName, $validater['rule'], $arg0, $arg1, $arg2);
        }
        return $this;
    }

    /**
     * Log the error.
     * 记录错误到日志。
     * 
     * For the error notice, see module/common/lang.
     * module/common/lang中定义了错误提示信息。
     *
     * @param  string $checkType    the check rule
     * @param  string $fieldName    the field name
     * @param  string $fieldLabel   the field label
     * @param  array  $funcArgs     the args
     * @access public
     * @return void
     */
    public function logError($checkType, $fieldName, $fieldLabel, $funcArgs = array())
    {
        global $lang;
        $error    = $lang->error->$checkType;
        $replaces = array_merge(array($fieldLabel), $funcArgs);     // the replace values.

        /*
         * Just a string, cycle the $replaces.
         * 如果$error错误信息是一个字符串，进行替换。
         **/
        if(!is_array($error))
        {
            foreach($replaces as $replace)
            {
                $pos = strpos($error, '%s');
                if($pos === false) break;
                $error = substr($error, 0, $pos) . $replace . substr($error, $pos + 2);
            }
        }
        /*
         * If the error define is an array, select the one which %s counts match the $replaces.
         * 如果error错误信息是一个数组，选择一个%s满足替换个数的进行替换。
         **/
        else
        {
            /*
             * Remove the empty items.
             * 去掉空值项。
             **/
            foreach($replaces as $key => $value) if(is_null($value)) unset($replaces[$key]);
            $replacesCount = count($replaces);
            foreach($error as $errorString)
            {
                if(substr_count($errorString, '%s') == $replacesCount)
                {
                    $error = vsprintf($errorString, $replaces);
                }
            }
        }
        dao::$errors[$fieldName][] = $error;
    }

    /**
     * Judge any error or not.
     * 判断是否有错误。
     * 
     * @access public
     * @return bool
     */
    public function isError()
    {
        return !empty(dao::$errors);
    }

    /**
     * Get the errors.
     * 获取错误。
     * 
     * @access public
     * @return array
     */
    public function getError()
    {
        $errors = dao::$errors;
        dao::$errors = array();     // Must clear it.
        return $errors;
    }

    /**
     * Get the defination of fields of the table.
     * 获取表的字段类型。
     * 
     * @access private
     * @return array
     */
    private function getFieldsType()
    {
        try
        {
            $this->dbh->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
            $sql = "DESC $this->table";
            $rawFields = $this->dbh->query($sql)->fetchAll();
            $this->dbh->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
        }
        catch (PDOException $e) 
        {
            $this->app->error($e->getMessage() . "<p>The sql is: $sql</p>", __FILE__, __LINE__, $exit = true);
        }

        foreach($rawFields as $rawField)
        {
            $firstPOS = strpos($rawField->type, '(');
            $type     = substr($rawField->type, 0, $firstPOS > 0 ? $firstPOS : strlen($rawField->type));
            $type     = str_replace(array('big', 'small', 'medium', 'tiny', 'var'), '', $type);
            $field    = array();

            if($type == 'enum' or $type == 'set')
            {
                $rangeBegin  = $firstPOS + 2;                       // Remove the first quote.
                $rangeEnd    = strrpos($rawField->type, ')') - 1;   // Remove the last quote.
                $range       = substr($rawField->type, $rangeBegin, $rangeEnd - $rangeBegin);
                $field['rule'] = 'reg';
                $field['options']['reg']  = '/' . str_replace("','", '|', $range) . '/';
            }
            elseif($type == 'char')
            {
                $begin  = $firstPOS + 1;
                $end    = strpos($rawField->type, ')', $begin);
                $length = substr($rawField->type, $begin, $end - $begin);
                $field['rule']   = 'length';
                $field['options']['max'] = $length;
                $field['options']['min'] = 0;
            }
            elseif($type == 'int')
            {
                $field['rule'] = 'int';
            }
            elseif($type == 'float' or $type == 'double')
            {
                $field['rule'] = 'float';
            }
            elseif($type == 'date')
            {
                $field['rule'] = 'date';
            }
            else
            {
                $field['rule'] = 'skip';
            }
            $fields[$rawField->field] = $field;
        }
        return $fields;
    }
}

/**
 * The SQL class.
 * SQL类。
 * 
 * @package framework
 */
class sql
{
    /**
     * The max count of params of all methods.
     * 所有方法的最大参数个数。
     * 
     */
    const MAX_ARGS = 3;

    /**
     * The sql string.
     * SQL字符串。
     * 
     * @var string
     * @access private
     */
    private $sql = '';

    /**
     * The global $dbh.
     * 全局变量$dbh。
     * 
     * @var object
     * @access protected
     */
    protected $dbh;

    /**
     * The data to update or insert.
     * 更新或插入日期。
     * 
     * @var mix
     * @access protected
     */
    public $data;

    /**
     * Is the first time to call set.
     * 是否是第一次设置。
     * 
     * @var bool    
     * @access private;
     */
    private $isFirstSet = true;

    /**
     * If in the logic of judge condition or not.
     * 是否是在条件语句中。
     * 
     * @var bool
     * @access private;
     */
    private $inCondition = false;

    /**
     * The condition is true or not.
     * 条件是否为真。
     * 
     * @var bool
     * @access private;
     */
    private $conditionIsTrue = false;

    /**
     * Magic quote or not.
     * 是否开启特殊字符转义。
     * 
     * @var bool
     * @access public
     */
     public $magicQuote; 

    /**
     * The construct function.
     * 构造方法
     * 
     * @param  string $table 
     * @access private
     * @return void
     */
    private function __construct($table = '')
    {
        global $dbh;
        $this->dbh        = $dbh;
        $this->magicQuote = get_magic_quotes_gpc();
    }

    /**
     * The factory method.
     * 工厂方法。
     * 
     * @param  string $table 
     * @access public
     * @return object the sql object.
     */
    public static function factory($table = '')
    {
        return new sql($table);
    }

    /**
     * The sql is select.
     * select语句。
     * 
     * @param  string $field 
     * @access public
     * @return object the sql object.
     */
    public static function select($field = '*')
    {
        $sqlobj = self::factory();
        $sqlobj->sql = "SELECT $field ";
        return $sqlobj;
    }

    /**
     * The sql is update.
     * update语句。
     * 
     * @param  string $table 
     * @access public
     * @return object the sql object.
     */
    public function update($table)
    {
        $sqlobj = self::factory();
        $sqlobj->sql = "UPDATE $table SET ";
        return $sqlobj;
    }

    /**
     * The sql is insert.
     * insert语句。
     * 
     * @param  string $table 
     * @access public
     * @return object the sql object.
     */
    public function insert($table)
    {
        $sqlobj = self::factory();
        $sqlobj->sql = "INSERT INTO $table SET ";
        return $sqlobj;
    }

    /**
     * The sql is replace.
     * replace语句。
     * 
     * @param  string $table 
     * @access public
     * @return object the sql object.
     */
    public function replace($table)
    {
        $sqlobj = self::factory();
        $sqlobj->sql = "REPLACE $table SET ";
        return $sqlobj;
    }

    /**
     * The sql is delete.
     * delete语句。
     * 
     * @access public
     * @return object the sql object.
     */
    public function delete()
    {
        $sqlobj = self::factory();
        $sqlobj->sql = "DELETE ";
        return $sqlobj;
    }

    /**
     * Join the data items by key = value.
     * 将关联数组转换为sql语句中 `key` = value 的形式。
     * 
     * @param  object $data 
     * @access public
     * @return object the sql object.
     */
    public function data($data)
    {
        $this->data = $data;
        foreach($data as $field => $value) $this->sql .= "`$field` = " . $this->quote($value) . ',';
        $this->sql = rtrim($this->sql, ',');    // Remove the last ','.
        return $this;
    }

    /**
     * Add an '(' at left.
     * 在左边添加'('。
     * 
     * @param  int    $count 
     * @access public
     * @return ojbect the sql object.
     */
    public function markLeft($count = 1)
    {
        $this->sql .= str_repeat('(', $count);
        return $this;
    }

    /**
     * Add an ')' at right.
     * 在右边增加')'。
     * 
     * @param  int    $count 
     * @access public
     * @return object the sql object.
     */
    public function markRight($count = 1)
    {
        $this->sql .= str_repeat(')', $count);
        return $this;
    }

    /**
     * The set part.
     * SET部分。
     * 
     * @param  string $set 
     * @access public
     * @return object the sql object.
     */
    public function set($set)
    {
        if($this->isFirstSet)
        {
            $this->sql .= " $set ";
            $this->isFirstSet = false;
        }
        else
        {
            $this->sql .= ", $set";
        }
        return $this;
    }

    /**
     * Create the from part.
     * 创建From部分。
     * 
     * @param  string $table 
     * @access public
     * @return object the sql object.
     */
    public function from($table)
    {
        $this->sql .= "FROM $table";
        return $this;
    }

    /**
     * Create the Alias part.
     * 创建Alias部分，Alias转为AS。
     * 
     * @param  string $alias 
     * @access public
     * @return object the sql object.
     */
    public function alias($alias)
    {
        $this->sql .= " AS $alias ";
        return $this;
    }

    /**
     * Create the left join part.
     * 创建LEFT JOIN部分。
     * 
     * @param  string $table 
     * @access public
     * @return object the sql object.
     */
    public function leftJoin($table)
    {
        $this->sql .= " LEFT JOIN $table";
        return $this;
    }

    /**
     * Create the on part.
     * 创建ON部分。
     * 
     * @param  string $condition 
     * @access public
     * @return object the sql object.
     */
    public function on($condition)
    {
        $this->sql .= " ON $condition ";
        return $this;
    }

    /**
     * Begin condition judge.
     * 开始条件判断。
     * 
     * @param  bool $condition 
     * @access public
     * @return object the sql object.
     */
    public function beginIF($condition)
    {
        $this->inCondition = true;
        $this->conditionIsTrue = $condition;
        return $this;
    }

    /**
     * End the condition judge.
     * 结束条件判断。
     * 
     * @access public
     * @return object the sql object.
     */
    public function fi()
    {
        $this->inCondition = false;
        $this->conditionIsTrue = false;
        return $this;
    }

    /**
     * Create the where part.
     * 创建WHERE部分。
     * 
     * @param  string $arg1     the field name
     * @param  string $arg2     the operator
     * @param  string $arg3     the value
     * @access public
     * @return object the sql object.
     */
    public function where($arg1, $arg2 = null, $arg3 = null)
    {
        if($this->inCondition and !$this->conditionIsTrue) return $this;
        if($arg3 !== null)
        {
            $value     = $this->quote($arg3);
            $condition = "`$arg1` $arg2 " . $this->quote($arg3);
        }
        else
        {
            $condition = $arg1;
        }

        $this->sql .= " WHERE $condition ";
        return $this;
    } 

    /**
     * Create the AND part.
     * 创建AND部分。
     * 
     * @param  string $condition 
     * @access public
     * @return object the sql object.
     */
    public function andWhere($condition)
    {
        if($this->inCondition and !$this->conditionIsTrue) return $this;
        $this->sql .= " AND $condition ";
        return $this;
    }

    /**
     * Create the OR part.
     * 创建OR部分。
     * 
     * @param  bool  $condition 
     * @access public
     * @return object the sql object.
     */
    public function orWhere($condition)
    {
        if($this->inCondition and !$this->conditionIsTrue) return $this;
        $this->sql .= " OR $condition ";
        return $this;
    }

    /**
     * Create the '='.
     * 创建'='部分。
     * 
     * @param  string $value 
     * @access public
     * @return object the sql object.
     */
    public function eq($value)
    {
        if($this->inCondition and !$this->conditionIsTrue) return $this;
        $this->sql .= " = " . $this->quote($value);
        return $this;
    }

    /**
     * Create '!='.
     * 创建'!='。
     * 
     * @param  string $value 
     * @access public
     * @return void the sql object.
     */
    public function ne($value)
    {
        if($this->inCondition and !$this->conditionIsTrue) return $this;
        $this->sql .= " != " . $this->quote($value);
        return $this;
    }

    /**
     * Create '>'.
     * 创建'>'。
     * 
     * @param  string $value 
     * @access public
     * @return object the sql object.
     */
    public function gt($value)
    {
        if($this->inCondition and !$this->conditionIsTrue) return $this;
        $this->sql .= " > " . $this->quote($value);
        return $this;
    }

    /**
     * Create '<'.
     * 创建'<'。
     * 
     * @param  mixed  $value 
     * @access public
     * @return object the sql object.
     */
    public function lt($value)
    {
        if($this->inCondition and !$this->conditionIsTrue) return $this;
        $this->sql .= " < " . $this->quote($value);
        return $this;
    }

    /**
     * Create "between and"
     * 创建"between and"。
     * 
     * @param  string $min 
     * @param  string $max 
     * @access public
     * @return object the sql object.
     */
    public function between($min, $max)
    {
        if($this->inCondition and !$this->conditionIsTrue) return $this;
        $min = $this->quote($min);
        $max = $this->quote($max);
        $this->sql .= " BETWEEN $min AND $max ";
        return $this;
    }

    /**
     * Create in part.
     * 创建IN部分。
     * 
     * @param  string|array $ids   list string by ',' or an array
     * @access public
     * @return object the sql object.
     */
    public function in($ids)
    {
        if($this->inCondition and !$this->conditionIsTrue) return $this;
        $this->sql .= helper::dbIN($ids);
        return $this;
    }

    /**
     * Create not in part.
     * 创建'NOT IN'部分。
     * 
     * @param  string|array $ids   list string by ',' or an array
     * @access public
     * @return object the sql object.
     */
    public function notin($ids)
    {
        if($this->inCondition and !$this->conditionIsTrue) return $this;
        $this->sql .= ' NOT ' . helper::dbIN($ids);
        return $this;
    }

    /**
     * Create the like by part.
     * 创建LIKE部分。
     * 
     * @param  string $string 
     * @access public
     * @return object the sql object.
     */
    public function like($string)
    {
        if($this->inCondition and !$this->conditionIsTrue) return $this;
        $this->sql .= " LIKE " . $this->quote($string);
        return $this;
    }

    /**
     * Create the order by part.
     * 创建ORDER BY部分。
     * 
     * @param  string $order 
     * @access public
     * @return object the sql object.
     */
    public function orderBy($order)
    {
        $order = str_replace(array('|', '', '_'), ' ', $order);
        $order = str_replace('left', '`left`', $order); // process the left to `left`.
        $this->sql .= " ORDER BY $order";
        return $this;
    }

    /**
     * Create the limit part.
     * 创建LIMIT部分。
     * 
     * @param  string $limit 
     * @access public
     * @return object the sql object.
     */
    public function limit($limit)
    {
        if(empty($limit)) return $this;
        stripos($limit, 'limit') !== false ? $this->sql .= " $limit " : $this->sql .= " LIMIT $limit ";
        return $this;
    }

    /**
     * Create the groupby part.
     * 创建GROUP BY部分。
     * 
     * @param  string $groupBy 
     * @access public
     * @return object the sql object.
     */
    public function groupBy($groupBy)
    {
        $this->sql .= " GROUP BY $groupBy";
        return $this;
    }

    /**
     * Create the having part.
     * 创建HAVING部分。
     * 
     * @param  string $having 
     * @access public
     * @return object the sql object.
     */
    public function having($having)
    {
        $this->sql .= " HAVING $having";
        return $this;
    }

    /**
     * Get the sql string.
     * 获取SQL字符串。
     * 
     * @access public
     * @return string
     */
    public function get()
    {
        return $this->sql;
    }

    /**
     * Quote a var.
     * 对字段加转义。
     * 
     * @param  mixed  $value 
     * @access public
     * @return mixed
     */
    public function quote($value)
    {
        if($this->magicQuote) $value = stripslashes($value);
        return $this->dbh->quote($value);
    }
}
