<?php
/**
 * The dao and sql class file of ZenTaoPHP.
 *
 * ZenTaoPHP is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * ZenTaoPHP is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with ZenTaoPHP.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright   Copyright 2009, Chunsheng Wang
 * @author      Chunsheng Wang <wwccss@gmail.com>
 * @package     ZenTaoPHP
 * @version     $Id: dao.class.php 1467 2009-10-24 08:04:12Z wwccss $
 * @link        http://www.zentao.cn
 */

/**
 * DAO类。提供各种便利的数据库操作方法。
 * 
 * @package ZenTaoPHP
 */
class dao
{
    /**
     * 全局的$app对象。
     * 
     * @var object
     * @access protected
     */
    protected $app;

    /**
     * 全局的$config对象。 
     * 
     * @var object
     * @access protected
     */
    protected $config;

    /**
     * 全局的$lang对象。
     * 
     * @var object
     * @access protected
     */
    protected $lang;

    /**
     * 全局的$dbh（数据库访问句柄）对象。
     * 
     * @var object
     * @access protected
     */
    protected $dbh;

    /**
     * 当前model所对应的table name。
     * 
     * @var object
     * @access private
     */
    public $table;

    /**
     * 当前查询所返回的字段列表。
     * 
     * @var object
     * @access private
     */
    public $fields;

    /**
     * 查询的模式，现在支持两种，一种是通过魔术方法，一种是直接拼写sql查询。
     * 
     * 主要用来区分dao::from()方法和sql::from()方法。
     *
     * @var object
     * @access private
     */
    public $mode;

    /**
     * 执行的sql查询列表。
     * 
     * 用来记录当前页面所有的sql查询。
     *
     * @var array
     * @access public
     */
    static public $querys = array();

    /**
     * 数据检查结果。
     * 
     * @var array
     * @access public
     */
    static public $errors = array();

    /**
     * 构造函数。
     * 
     * 设置当前model对应的表名，并引用全局的变量。
     *
     * @param string $table   表名。
     * @access public
     * @return void
     */
    public function __construct($table = '')
    {
        global $app, $config, $lang, $dbh;
        $this->app    = $app;
        $this->config = $config;
        $this->lang   = $lang;
        $this->dbh    = $dbh;

        $this->reset();
    }

    /**
     * 设置数据表。
     * 
     * @param string $table   表名。
     * @access private
     * @return void
     */
    private function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * 设置返回的字段列表。
     * 
     * @param string $fields   字段列表。
     * @access private
     * @return void
     */
    private function setFields($fields)
    {
        $this->fields = $fields;
    }

    /**
     * 重新设置table, field, mode。
     * 
     * @access private
     * @return void
     */
    private function reset()
    {
        $this->setFields('');
        $this->setTable('');
        $this->setMode('');
    }

    //-------------------- 根据查询方式的不同，调用SQL类的对应方法。--------------------//

    /**
     * 设置查询模式。magic是通过findby之类的魔术方法进行查询的，而raw则直接拼装sql进行查询。
     * 
     * @param string mode   查询模式： empty|magic|raw
     * @access private
     * @return void
     */
    private function setMode($mode = '')
    {
        $this->mode = $mode;
    }
    
    /* select：调用SQL类的select方法。*/
    public function select($fields = '*')
    {
        $this->setMode('raw');
        $this->sqlobj = sql::select($fields);
        return $this;
    }

    /* update：调用SQL类的update方法。*/
    public function update($table)
    {
        $this->setMode('raw');
        $this->sqlobj = sql::update($table);
        $this->setTable($table);
        return $this;
    }

    /* delete：调用SQL类的delete方法。*/
    public function delete()
    {
        $this->setMode('raw');
        $this->sqlobj = sql::delete();
        return $this;
    }

    /* insert：调用SQL类的insert方法。*/
    public function insert($table)
    {
        $this->setMode('raw');
        $this->sqlobj = sql::insert($table);
        $this->setTable($table);
        return $this;
    }

    /* replace：调用SQL类的replace方法。*/
    public function replace($table)
    {
        $this->setMode('raw');
        $this->sqlobj = sql::replace($table);
        $this->setTable($table);
        return $this;
    }

    /* from: 设定要查询的table name。*/
    public function from($table) 
    {
        $this->setTable($table);
        if($this->mode == 'raw') $this->sqlobj->from($table);
        return $this;
    }

    /* fields方法：设置要查询的字段列表。*/
    public function fields($fields)
    {
        $this->setFields($fields);
        return $this;
    }

    //-------------------- 拼装之后的SQL相关处理方法。--------------------//

    /* 返回SQL语句。*/
    public function get()
    {
        return $this->processSQL();
    }

    /* 打印SQL语句。*/
    public function printSQL()
    {
        echo $this->processSQL();
    }

    /* 处理SQL，将table和fields字段替换成对应的值。*/
    private function processSQL()
    {
        if($this->mode == 'magic')
        {
            if($this->fields == '') $this->fields = '*';
            if($this->table == '')  $this->app->error('Must set the table name', __FILE__, __LINE__, $exit = true);
            $sql = sprintf($this->sqlobj->get(), $this->fields, $this->table);
            self::$querys[] = $sql;
            return $sql;
        }
        else
        {
            $sql = $this->sqlobj->get();
            self::$querys[] = $sql;
            return $sql;
        }
    }

    //-------------------- SQL查询相关的方法。--------------------//
    
    /* 执行sql查询，返回stmt对象。*/
    public function query()
    {
        /* 如果dao::$errors不为空，返回一个空的stmt对象，这样后续的方法调用还可以继续。*/
        if(!empty(dao::$errors)) return new PDOStatement();

        /* 处理一下SQL语句。*/
        $sql = $this->processSQL();
        try
        {
            $this->reset();
            return $this->dbh->query($sql);
        }
        catch (PDOException $e) 
        {
            $this->app->error($e->getMessage() . "<p>The sql is: $sql</p>", __FILE__, __LINE__, $exit = true);
        }
    }

    /* 执行分页。*/
    public function page($pager)
    {
        if(!is_object($pager)) return $this;

        /* 没有传递recTotal，则自己进行计算。*/
        if($pager->recTotal == 0)
        {
            /* 获得SELECT和FROM的位置，据此算出查询的字段，然后将其替换为count(*)。*/
            $sql       = $this->get();
            $selectPOS = strpos($sql, 'SELECT') + strlen('SELECT');
            $fromPOS   = strpos($sql, 'FROM');
            $fields    = substr($sql, $selectPOS, $fromPOS - $selectPOS );
            $sql       = str_replace($fields, ' COUNT(*) AS recTotal ', $sql);

            /* 取得order 或者limit的位置，将后面的去掉。*/
            $subLength = strlen($sql);
            $orderPOS  = strripos($sql, 'order');
            $limitPOS  = strripos($sql , 'limit');
            if($limitPOS) $subLength = $limitPOS;
            if($orderPOS) $subLength = $orderPOS;
            $sql = substr($sql, 0, $subLength);
            self::$querys[] = $sql;

            /* 获得记录总数。*/
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

    /* 执行sql查询，返回受影响的记录数。*/
    public function exec()
    {
        /* 如果dao::$errors不为空，返回一个空的stmt对象，这样后续的方法调用还可以继续。*/
        if(!empty(dao::$errors)) return new PDOStatement();

        /* 处理一下SQL语句。*/
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

    //-------------------- 数据获取相关的方法。--------------------//

    /* 返回一条记录，如果指定了$field字段, 则直接返回该字段对应的值。*/
    public function fetch($field = '')
    {
        if(empty($field)) return $this->query()->fetch();
        $this->setFields($field);
        $result = $this->query()->fetch(PDO::FETCH_OBJ);
        if($result) return $result->$field;
    }

    /* 返回全部的结果。如果指定了$keyField，则以keyField的值作为key。*/
    public function fetchAll($keyField = '')
    {
        $stmt = $this->query();
        if(empty($keyField)) return $stmt->fetchAll();
        $rows = array();
        while($row = $stmt->fetch()) $rows[$row->$keyField] = $row;
        return $rows;
    }

    /* 返回结果并按照某个字段进行分组。*/
    public function fetchGroup($groupField, $keyField = '')
    {
        $stmt = $this->query();
        $rows = array();
        while($row = $stmt->fetch())
        {
            empty($keyField) ?  $rows[$row->$groupField][] = $row : $rows[$groupField][$row->$keyField] = $row;
        }
        return $rows;
    }

    /* fetchPairs方法：如果没有指定key和value字段，则取行字段里面的第一个作为key，最后一个作为value。*/
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

    /* 获取最后插入的id。*/
    public function lastInsertID()
    {
        return $this->dbh->lastInsertID();
    }

    //-------------------- 各种魔术方法。--------------------//

    /**
     * 魔术方法，籍此提供各种便利的查询方法。
     * 
     * @param string $funcName  被调用的方法名。
     * @param array  $funcArgs  传入的参数列表。
     * @access public
     * @return void
     */
    public function __call($funcName, $funcArgs)
    {
        /* 将funcName转为小写。*/
        $funcName = strtolower($funcName);

        /* findBy类的方法。*/
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
            $this->sqlobj = sql::select('%s')->from('%s')->where($field, $operator, $value);    // 使用占位符，执行查询之前替换为相应的字段和表名。
            return $this;
        }
        /* fetch10方法，真正的数据查询。*/
        elseif(strpos($funcName, 'fetch') !== false)
        {
            $max  = str_replace('fetch', '', $funcName);
            $stmt = $this->query();

            /* 设定了key字段。 */
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
        /* 其余的都直接调用sql类里面的方法。*/
        else
        {
            /* 取SQL类方法中参数个数最大值，生成一个最大集合的参数列表。。*/
            for($i = 0; $i < SQL::MAX_ARGS; $i ++)
            {
                ${"arg$i"} = isset($funcArgs[$i]) ? $funcArgs[$i] : null;
            }
            $this->sqlobj->$funcName($arg0, $arg1, $arg2);
            return $this;
        }
    }

    //-------------------- 数据检查相关的方法。--------------------//
    
    /* 按照某个规则检查值是否符合要求。*/
    public function check($fieldName, $funcName)
    {
        /* 如果data变量里面没有这个字段，直接返回。*/
        if(!isset($this->sqlobj->data->$fieldName)) return $this;

        /* 引用全局的config, lang。*/
        global $lang, $config;
        $table = str_replace($config->db->prefix, '', $this->table);
        $fieldLabel = isset($lang->$table->$fieldName) ? $lang->$table->$fieldName : $fieldName;
        $value = $this->sqlobj->data->$fieldName;
        
        if($funcName == 'unique')
        {
            $args  = func_get_args();
            $sql = "SELECT COUNT(*) AS count FROM $this->table WHERE `$fieldName` = " . $this->dbh->quote($value); 
            if(isset($args[2])) $sql .= ' AND ' . $args[2];
            $row = $this->dbh->query($sql)->fetch();
            if($row->count != 0) $this->logError($funcName, $fieldName, $fieldLabel, array($value));
        }
        else
        {
            /* 取validate类方法中参数个数最大值，生成一个最大集合的参数列表。。*/
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

    /* 如果满足某一个条件，按照某个规则检查值是否符合要求。*/
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

    /* 批量检查。*/
    public function batchCheck($fields, $funcName)
    {
        $fields = explode(',', $fields);
        $funcArgs = func_get_args();
        for($i = 0; $i < VALIDATER::MAX_ARGS; $i ++)
        {
            ${"arg$i"} = isset($funcArgs[$i + 2]) ? $funcArgs[$i + 2] : null;
        }
        foreach($fields as $fieldName) $this->check($fieldName, $funcName, $arg0, $arg1, $arg2);
        return $this;
    }
 
    /* 自动根据数据库中表的字段格式进行检查。*/
    public function autoCheck($skipFields = '')
    {
        $fields     = $this->getFieldsType();
        $skipFields = ",$skipFields,";

        foreach($fields as $fieldName => $validater)
        {
            if(strpos($skipFields, $fieldName) !== false) continue;    // 忽略。
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

    /* 记录错误。*/
    public function logError($checkType, $fieldName, $fieldLabel, $funcArgs = array())
    {
        global $lang;
        $error    = $lang->error->$checkType;
        $replaces = array_merge(array($fieldLabel), $funcArgs);

        /* 如果error不是数组，只是字符串，则循环replace，依次替换%s。*/
        if(!is_array($error))
        {
            foreach($replaces as $replace)
            {
                $pos = strpos($error, '%s');
                if($pos === false) break;
                $error = substr($error, 0, $pos) . $replace . substr($error, $pos +2);
            }
        }
        /* 如果error是一个数组，则从数组中挑选%s个数与replace元素个数相同的。*/
        else
        {
            /* 去掉replace中空白的元素。*/
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

    /* 判断这次查询是否有错误。*/
    public function isError()
    {
        return !empty(dao::$errors);
    }

    /* 返回error。*/
    public function getError()
    {
        $errors = dao::$errors;
        dao::$errors = array();
        return $errors;
    }

    /* 获得某一个表的字段类型。*/
    private function getFieldsType()
    {
        $this->dbh->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
        $rawFields = $this->dbh->query("DESC $this->table")->fetchAll();
        $this->dbh->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
        foreach($rawFields as $rawField)
        {
            $firstPOS = strpos($rawField->type, '(');
            $type     = substr($rawField->type, 0, $firstPOS > 0 ? $firstPOS : strlen($rawField->type));
            $type     = str_replace(array('big', 'small', 'medium', 'tiny', 'var'), '', $type);
            $field    = array();

            if($type == 'enum' or $type == 'set')
            {
                $rangeBegin  = $firstPOS + 2;  // 将第一个引号去掉。
                $rangeEnd    = strrpos($rawField->type, ')') - 1; // 将最后一个引号去掉。
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
 * SQL查询封装类。
 * 
 * @package ZenTaoPHP
 */
class sql
{
    /**
     * 所有方法的参数个数最大值。
     * 
     */
    const MAX_ARGS = 3;

    /**
     * SQL语句。
     * 
     * @var string
     * @access private
     */
    private $sql = '';

    /**
     * 数据表名。
     * 
     * @var string
     * @access private
     */
    private $table = '';

    /**
     * 全局的$dbh（数据库访问句柄）对象。
     * 
     * @var object
     * @access protected
     */
    protected $dbh;

    /**
     * INSERT或者UPDATE时赋给的数据。
     * 
     * @var mix
     * @access protected
     */
    public $data;

    /**
     * 是否是首次调用set。
     * 
     * @var bool    
     * @access private;
     */
    private $isFirstSet = true;


    /* 构造函数。*/
    private function __construct($table = '')
    {
        global $dbh;
        $this->dbh   = $dbh;
        $this->table = $table;
    }

    /* 实例化方法，通过该方法实例对象。*/
    public function factory($table = '')
    {
        return new sql($table);
    }

    /* 查询语句开始。*/
    public function select($field = '*')
    {
        $sqlobj = self::factory();
        $sqlobj->sql = "SELECT $field ";
        return $sqlobj;
    }

    /* 更新语句开始。*/
    public function update($table)
    {
        $sqlobj = self::factory();
        $sqlobj->sql = "UPDATE $table SET ";
        return $sqlobj;
    }

    /* 插入语句开始。*/
    public function insert($table)
    {
        $sqlobj = self::factory();
        $sqlobj->sql = "INSERT INTO $table SET ";
        return $sqlobj;
    }

    /* 替换语句开始。*/
    public function replace($table)
    {
        $sqlobj = self::factory();
        $sqlobj->sql = "REPLACE $table SET ";
        return $sqlobj;
    }

    /* 删除语句开始。*/
    public function delete()
    {
        $sqlobj = self::factory();
        $sqlobj->sql = "DELETE ";
        return $sqlobj;
    }

    /* 给定一个key=>value结构的数组或者对象，拼装成key = value的形式。*/
    public function data($data)
    {
        $this->data = $data;
        foreach($data as $field => $value) $this->sql .= "`$field` = " . $this->dbh->quote($value) . ',';
        $this->sql = rtrim($this->sql, ',');    // 去掉最后面的逗号。
        return $this;
    }

    /* 加左边的括弧。*/
    public function markLeft($count = 1)
    {
        $this->sql .= str_repeat('(', $count);
        return $this;
    }

    /* 加右边的括弧。*/
    public function markRight($count = 1)
    {
        $this->sql .= str_repeat(')', $count);
        return $this;
    }

    /* SET key=value。*/
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

    /* 设定要查询的表名。*/
    public function from($table)
    {
        $this->sql .= "FROM $table";
        return $this;
    }

    /* 设置别名。*/
    public function alias($alias)
    {
        $this->sql .= " AS $alias ";
    }

    /* 设定LEFT JOIN语句。*/
    public function leftJoin($table)
    {
        $this->sql .= " LEFT JOIN $table";
        return $this;
    }

    /* 设定ON条件。*/
    public function on($condition)
    {
        $this->sql .= " ON $condition ";
        return $this;
    }

    /* WHERE语句部分开始。*/
    public function where($arg1, $arg2 = null, $arg3 = null)
    {
        if($arg3 !== null)
        {
            $value     = $this->dbh->quote($arg3);
            $condition = "`$arg1` $arg2 " . $this->dbh->quote($arg3);
        }
        else
        {
            $condition = $arg1;
        }

        $this->sql .= " WHERE $condition ";
        return $this;
    } 

    /* 追加AND条件。*/
    public function andWhere($condition)
    {
        $this->sql .= " AND $condition ";
        return $this;
    }

    /* 追加OR条件。*/
    public function orWhere($condition)
    {
        $this->sql .= " OR $condition ";
        return $this;
    }

    /* 等于。*/
    public function eq($value)
    {
        $this->sql .= " = " . $this->dbh->quote($value);
        return $this;
    }

    /* 不等于。*/
    public function ne($value)
    {
        $this->sql .= " != " . $this->dbh->quote($value);
        return $this;
    }

    /* 大于。*/
    public function gt($value)
    {
        $this->sql .= " > " . $this->dbh->quote($value);
        return $this;
    }

    /* 小于。*/
    public function lt($value)
    {
        $this->sql .= " < " . $this->dbh->quote($value);
        return $this;
    }

    /* 生成between语句。*/
    public function between($min, $max)
    {
        $this->sql .= " BETWEEN $min AND $max ";
        return $this;
    }

    /* 生成 IN部分语句。*/
    public function in($ids)
    {
        $this->sql .= helper::dbIN($ids);
        return $this;
    }

    /* 生成LIKE部分语句。*/
    public function like($string)
    {
        $this->sql .= " LIKE " . $this->dbh->quote($string);
        return $this;
    }

    /* 设定ORDER BY。*/
    public function orderBy($order)
    {
        $order = str_replace('|', ' ', $order);
        $this->sql .= " ORDER BY $order";
        return $this;
    }

    /* 设定LIMIT。*/
    public function limit($limit)
    {
        if(empty($limit)) return $this;
        stripos($limit, 'limit') !== false ? $this->sql .= " $limit " : $this->sql .= " LIMIT $limit ";
        return $this;
    }

    /* 设定GROUP BY。*/
    public function groupBy($groupBy)
    {
        $this->sql .= " GROUP BY $groupBy";
        return $this;
    }

    /* 设定having。*/
    public function having($having)
    {
        $this->sql .= " HAVING $having";
        return $this;
    }

    /* 返回拼装好的语句。*/
    public function get()
    {
        return $this->sql;
    }
}
