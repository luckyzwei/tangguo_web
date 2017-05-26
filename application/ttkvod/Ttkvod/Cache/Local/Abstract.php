<?php
abstract class Ttkvod_Cache_Local_Abstract implements Lamb_Cache_Interface
{
	/**
	 * @var string 
	 */
	protected $_mTableKey;
	
	/**
	 * @var string
	 */
	protected $_mSqlKey;
	
	/**
	 * @var int
	 */
	protected $_mPagesize = null;
	
	/**
	 * @var string
	 */
	protected $_mTableName = 'cache_map';
	
	/**
	 * @var string
	 */
	protected $_mPath ;
	
	/**
	 * @param string $sql
	 * @param string $table
	 * @param int $pagesize
	 */
	public function __construct($path = null, $sql = null, $table = null, $pagesize = 0)
	{		
		if (null !== $path) {
			$this->getIdentity($path);	
		}
		if (null !== $sql) {
			$this->setOrGetSqlKey($sql);
		}
		if (null !== $sql) {
			$this->setOrGetTableKey($table);
		}
		$this->setOrGetPagesize($pagesize);
	}
	
	/**
	 * @param string $sql
	 * @return string | Ttkvod_Cache_Local_Abstract
	 * @throws Lamb_Cache_Exception
	 */
	public function setOrGetSqlKey($sql = null)
	{
		if (null === $sql) {
			if (!$this->_mSqlKey) {
				throw new Lamb_Cache_Exception('You must set sql key before get it');
			}
			return $this->_mSqlKey;
		}
		$this->_mSqlKey = Lamb_Utils::crc32FormatHex((string)$sql);
		return $this;
	}

	/**
	 * @param string $table
	 * @return string | Ttkvod_Cache_Local_Abstract
	 * @throws Lamb_Cache_Exception
	 */	
	public function setOrGetTableKey($table = null)
	{
		if (null === $table) {
			if (!$this->_mTableKey) {
				throw new Lamb_Cache_Exception('You must set table key before get it');
			}
			return $this->_mTableKey;
		}
		$this->_mTableKey = Lamb_Utils::crc32FormatHex((string)$table);
		return $this;	
	}
	
	/**
	 * @param int $pagesize
	 * @return string | Ttkvod_Cache_Local_Abstract
	 * @throws Lamb_Cache_Exception
	 */	
	public function setOrGetPagesize($pagesize = null)
	{
		if (null === $pagesize) {
			if (null === $this->_mPagesize) {
				throw new Lamb_Cache_Exception('You must set table key before get it');
			}
			return $this->_mPagesize;
		}
		$this->_mPagesize = (int)$pagesize;
		return $this;	
	}
	
	/**
	 * @param string $table
	 * @return string | Ttkvod_Cache_Local_Abstract
	 */
	public function setOrGetTableName($tablename = null)
	{
		if (null === $tablename) {
			return $this->_mTableName;
		}
		$this->_mTableName = (string)$tablename;
		return $this;
	}
	
	/**
	 * Lamb_Cache_Interface implements
	 */
	public function setIdentity($identity)
	{
		$this->_mPath = Lamb_Utils::crc32FormatHex((string)$identity);
	}

	/**
	 * Lamb_Cache_Interface implements
	 */	
	public function getIdentity()
	{
		return $this->_mPath;
	}
	
	/**
	 * 是否在表中记录该缓存的位置
	 *
	 * @return boolean
	 */
	public function isRecorded()
	{
		$sql = 'select path from ' . $this->setOrGetTableName() . ' where path=:path';
		$aPrepareSource = array(':path' => array($this->getIdentity(), PDO::PARAM_STR));
		return Lamb_App::getGlobalApp()->getDb()->getNumDataPrepare($sql, $aPrepareSource) > 0;
	}
	
	/**
	 * @param array $editData 如果$editData为数组，则当存在此记录就修改
	 *				array('sql_key' => ,'table_key' => , 'page_size' =>, 'path'=>)
	 * @return boolean
	 */
	public function writeRecord(array $editData = null)
	{
		$bRet = false;
		$aPrepareSource = array(
				1 => array($this->getIdentity(), PDO::PARAM_STR),
				2 => array($this->setOrGetSqlKey(), PDO::PARAM_STR),
				3 => array($this->setOrGetTableKey(), PDO::PARAM_STR),
				4 => array($this->setOrGetPagesize(), PDO::PARAM_INT)
			);
		if (!$this->isRecorded()) {
			$sql = 'insert into ' . $this->setOrGetTableName() .' (path, sql_key, table_key, pagesize) values (?,?,?,?)';
			$bRet = Lamb_App::getGlobalApp()->getDb()->quickPrepare($sql, $aPrepareSource, true);
		} else if ($editData) {
			$table = new Lamb_Db_Table($this->setOrGetTableName(), Lamb_Db_Table::UPDATE_PREPARE_MODE);
			$table->setOrGetWhere('path=?');
			$table->set($editData);
			$aPrepareSource[5] = array($this->getIdentity(), PDO::PARAM_STR);
			$bRet = $table->execute($aPrepareSource);
		}
		return $bRet;
	}

	/**
	 * @return boolean
	 */
	public function deleteRecord()
	{
		return Lamb_App::getGlobalApp()->getDb()->qucikPrepare('delete from ' . $this->setOrGetTableName() . 'where path=?',
													array( 1 => array($this->getIdentity(), PDO::PARAM_STR)));		
	}
	
	/**
	 * clear the cache
	 */
	abstract public function clear(array $condition = null);
}