<?php
/**
 * Lamb Framework
 * @author 小羊
 * @package Lamb_Db
 */
class Lamb_Db_Table implements Serializable
{
	const UPDATE_MODE = 1;
	
	const INSERT_MODE = 2;
	
	const UPDATE_PREPARE_MODE = 4;
	
	const INSERT_PREPARE_MODE = 8;
	
	/**
	 * @var boolean
	 */
	protected $_mIsToStringEscape = true;
	
	/**
	 * @var string $_table 数据库表名
	 */
	protected $_mTable;
	
	/**
	 * @var int 模式 update or insert
	 */
	protected $_mMode;
	
	/**
	 * @var Lamb_Db_Abstract
	 */
	protected $_mDb = null;
	
	/**
	 * @var array $_mFields 存储待操作列的键值对集合
	 */
	protected $_mFields = array();
	
	/**
	 * @var string $_mWhere 保存条件语句，仅在$mode为update时有效
	 */
	protected $_mWhere = '';
	
	/**
	 * @param string $table
	 * @param int $mode
	 */
	public function __construct($table, $mode = self::UPDATE_MODE)
	{
		$this->setOrGetTable($table);
		$this->setOrGetMode($mode);
	}
	
	/**
	 * @param string $table
	 * @return string | Lamb_Db_Table
	 */
	public function setOrGetTable($table = null)
	{
		if (null === $table) {
			return $this->_mTable;
		}
		$this->_mTable = (string)$table;
		return $this;
	}
	
	/**
	 * @param int $mode
	 * @return int | Lamb_Db_Table
	 */
	public function setOrGetMode($mode = null)
	{
		if (null === $mode) {
			return $this->_mMode;
		}
		$this->_mMode = (int)$mode;
		return $this;
	}
	
	/**
	 * @param Lamb_Db_Abstract $db
	 * @return Lamb_Db_Abstract | Lamb_Db_Table
	 */
	public function setOrGetDb(Lamb_Db_Abstract $db = null)
	{
		if (null === $db) {
			if (null === $this->_mDb) {
				$this->setOrGetDb(Lamb_App::getGlobalApp()->getDb());
			}
			return $this->_mDb;
		}
		$this->_mDb = $db;
		return $this;
	}
	
	/**
	 * @param string $where
	 * @return string | Lamb_Db_Table
	 */
	public function setOrGetWhere($where = null)
	{
		if (null === $where) {
			return $this->_mWhere;
		}
		$this->_mWhere = (string)$where;
		return $this;
	}

	/**
	 * @param string $where
	 * @return boolean | Lamb_Db_Table
	 */	
	public function setOrGetToStringEscape($escape = null)
	{
		if (null === $escape) {
			return $this->_mIsToStringEscape;
		}
		$this->_mIsToStringEscape = (boolean)$escape;
		return $this;
	}
	
	/**
	 * 设置_mFields值，如果$key存在并且$val为null则删除该键值
	 * 对于prepare语句 $val可以为?或者:key
	 * 对于update语句可以实现x=x+1的功能那么$val必须为数组array(值, 符号(+-*))
	 * 如果x=x+1，则$this->x = array(1, '+')
	 *
	 *
	 * @param string $key
	 * @param string | int $val
	 * @return void
	 */
	public function __set($key, $val)
	{
		if ($val === null && isset($this->_mFields[$key])) {
			unset($this->_mFields[$key]);
		} else {
			$this->_mFields[$key] = $val;
		}
	}
	
	/**
	 * 获取_mFields中指定的$key的值
	 * 
	 * @param string $key
	 * @return string | int
	 */
	public function __get($key)
	{
		return isset($this->_mFields[$key]) ? $this->_mFields[$key] : null;
	}
	
	/**
	 * 批量设置字段
	 *
	 * @param array $fields
	 * @return Lamb_Db_Table
	 */
	public function set(array $fields)
	{
		foreach ($fields as $key => $val) {
			$this->__set($key, $val);
		}
		return $this;
	}
	
	/**
	 * 获取_mFields集合，如果参数$key为null则返回整个
	 * 
	 * @param string $key
	 * @return string | array
	 */
	public function get($key = null)
	{
		if (null === $key) {
			return $this->_mFields;
		}
		return $this->__get($key);
	}
	
	/**
	 * @return Lamb_Db_Table
	 */
	public function flush()
	{
		$this->_mFields = array();
		$this->_mWhere = '';
		return $this;
	}
	
	/**
	 * 获取update格式的SQL语句，其中的列的修改是根据$_mFields中的设置
	 *
	 * @param boolean $escape
	 * @return string
	 */
	public function getUpdateSql($escape = true, $where = '')
	{
		if (empty($where)) {
			$where = $this->setOrGetWhere();
		}
		
		if(!$this->_mTable || !count($this->_mFields)) {
			return '';
		}
		
		$sqlHelper = Lamb_App::getGlobalApp()->getSqlHelper();
		$sql = "update {$this->_mTable} set ";
		$part = array();
		$mode = $this->setOrGetMode();
		
		foreach ($this->_mFields as $key => $val) {
			$key = $sqlHelper->escapeField($key);
			$partsql = $key . '=';
			
			if (is_array($val)) {
				$partsql .= $key . $val[1];
				$val = $val[0]; 
			}
			
			if ($mode == self::UPDATE_PREPARE_MODE && ($val == '?' || $val{0} == ':')) {
				$partsql .= $val;
			} else {
				if ($escape) {
					$val = $sqlHelper->escape($val);
				}			
				$partsql .= "'$val'";
			}
			
			$part[] = $partsql;
		}
		
		$sql .= implode(',', $part);
		
		if ($where) {
			$sql .= ' where ' . $where;
		}
		
		return $sql;	
	}
	
	/**
	 * 获取update预处理格式的SQL语句，其中的列的修改是根据$_mFields中的设置
	 *
	 * @param boolean $escape
	 * @return string	 
	 */
	public function getUpdatePrepareSql($escape = true, $where = '')
	{
		$mode = $this->setOrGetMode();
		$this->setOrGetMode(self::UPDATE_PREPARE_MODE);
		$sql = $this->getUpdateSql($escape, $where);
		$this->setOrGetMode($mode);
		return $sql;
	}
	
	/**
	 * 获取insert格式的SQL语句，其中的列的修改是根据$_mFields中的设置
	 *
	 * @param boolean $escape
	 * @return string
	 */	
	public function getInsertSql($escape = true)
	{	
		if (!$this->_mTable || !count($this->_mFields)) {
			return '';
		}
		
		$sqlHelper = Lamb_App::getGlobalApp()->getSqlHelper();
		$sql = 'insert into ' . $this->_mTable;
		$part1 = $part2 = array();
		$mode = $this->setOrGetMode();
		
		foreach ($this->_mFields as $key => $val) {
			$part1[] = $sqlHelper->escapeField($key);
			
			if ($mode == self::INSERT_PREPARE_MODE && ($val == '?' || $val{0} == ':')) {
				$part2[] = $val;
			} else {
				if ($escape) {
					$val = $sqlHelper->escape($val);
				}
				$part2[] = "'$val'";
			}			
		}
		
		return $sql . ' (' . implode(',', $part1) . ') values (' . implode(',', $part2) . ')';
	}	
	
	/**
	 * 获取insert预处理格式的SQL语句，其中的列的修改是根据$_mFields中的设置
	 *
	 * @param boolean $escape
	 * @return string
	 */
	public function getInsertPrepareSql($escape = true)
	{		
		$mode = $this->setOrGetMode();
		$this->setOrGetMode(self::INSERT_PREPARE_MODE);
		$sql = $this->getInsertSql($escape);
		$this->setOrGetMode($mode);
		return $sql;
	}
	
	/**
	 * Get the sql statment
	 *
	 * @return string
	 */
	public function __toString()
	{
		$sql = '';
		$escape = $this->setOrGetToStringEscape();
		switch($this->setOrGetMode()) {
			case self::UPDATE_MODE:
				return $this->getUpdateSql($escape);
			case self::INSERT_MODE:
				return $this->getInsertSql($escape);
			case self::UPDATE_PREPARE_MODE:
				return $this->getUpdatePrepareSql($escape);
			case self::INSERT_PREPARE_MODE:
				return $this->getInsertPrepareSql($escape);
			
		}
		return $sql;
	}
	
	/**
	 * @param array $aPrepareSource
	 * @return boolean is success
	 */
	public function execute(array $aPrepareSource = null)
	{
		$ret = false;
		$sql = $this->__toString();
		if ($sql) {
			$db = $this->setOrGetDb();
			$mode = $this->setOrGetMode();
			if (($mode == self::UPDATE_PREPARE_MODE || $mode == self::INSERT_PREPARE_MODE)) {
				$objRecord = $db->prepare($sql);
				if ($aPrepareSource) {
					Lamb_Db_Abstract::batchBindValue($objRecord, $aPrepareSource);
				}
				if ($objRecord) {
					return $objRecord->execute() ? $objRecord->rowCount() : 0;
				}
			} else {
				 return $db->exec($sql);
			}
		}
		return $ret;
	}
	
	/**
	 * the Serializable implemention
	 */
	public function serialize()
	{
		$data = array(
			'table' => $this->setOrGetTable(),
			'fields' => $this->get(),
			'where' => $this->setOrGetWhere(),
			'toStringEscape' => $this->setOrGetToStringEscape(),
			'mode' => $this->setOrGetMode()
		);
		return serialize($data);
	}

	/**
	 * the Serializable implemention
	 */	
	public function unserialize($source)
	{
		$data = unserialize($source);
		if ($data && is_array($data)) {
			$this->setOrGetTable($data['tables'])
				 ->setOrGetWhere($data['where'])
				 ->setOrGetToStringEscape($data['toStringEscape'])
				 ->setOrGetMode($data['mode']);
			foreach ($data['fields'] as $key => $val) {
				$this->__set($key, $val);
			}
		}
	}		
}