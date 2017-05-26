<?php
class Ttkvod_Cache_Local_File extends Ttkvod_Cache_Local_Abstract
{
	/**
	 * @var Ttkvod_Cache_File
	 */
	protected $_mFileCache;
	
	/**  
	 * @param int $cachetime
	 * @param string $path
	 * @param string $sql
	 * @param string $table
	 * @param int $pagesize
	 */
	public function __construct($cachetime = null, $path = null, $sql = null, $table = null, $pagesize = 0)
	{
		parent::__construct($path, $sql, $table, $pagesize);
		$this->_mFileCache = new Ttkvod_Cache_File($cachetime, null === $path ? null : $this->getIdentity());
	}
	
	/**
	 * @string $path
	 * @return Ttkvod_LocalCache
	 * @throws Lamb_IO_Exception
	 */
	public function setPath($path)
	{
		$this->_mFileCache->setPath($path);
		return $this;
	}
	
	/**
	 * @return string
	 * @throws Lamb_IO_Exception
	 */
	public function getPath()
	{
		return $this->_mFileCache->getPath();
	}
	
	/**
	 * @param string $extendtion
	 * @return string | Ttkvod_LocalCache
	 */
	public function setOrGetExtendtion($extendtion = null)
	{
		if (null === $extendtion) {
			return $this->_mFileCache->setOrGetExtendtion(null);
		}
		$this->_mFileCache->setOrGetExtendtion($extendtion);
		return $this;
	}	
	
	/**
	 * Lamb_Cache_Interface implemention
	 */
	public function setIdentity($identity)
	{
		parent::setIdentity($identity);
		$this->_mFileCache->setIdentity($this->getIdentity());
		return $this;
	}

	/**
	 * Lamb_Cache_Interface implemention
	 */	
	public function setCacheTime($second)
	{
		$this->_mFileCache->setCacheTime($second);
		return $this;
	}
	
	/**
	 * Lamb_Cache_Interface implemention
	 */	
	public function getCacheTime()
	{
		return $this->_mFileCache->getCacheTime();
	}
	
	/**
	 * Lamb_Cache_Interface implemention
	 */	
	public function read()
	{
		return $this->_mFileCache->read();
	}

	/**
	 * Lamb_Cache_Interface implemention
	 */	
	public function write($data)
	{
		$this->_mFileCache->write($data);
		return $this->writeRecord();
	}	

	/**
	 * Lamb_Cache_Interface implemention
	 */		
	public function flush()
	{
		if ($this->_mFileCache->flush()) {
			return $this->deleteRecord();
		}
		return false;
	}
	
	/**
	 * Lamb_Cache_Interface implemention
	 */		
	public function isCached()
	{
		return $this->_mFileCache->isCached();
	}
	
	/**
	 * @param string $path
	 * @param array $condition
	 */
	public function clear(array $condition = null)
	{
		$path = $this->getIdentity();
		if (null === $condition) {
			Lamb_IO_File::delFileUnderDir($path);
			Lamb_App::getGlobalApp()->getDb->execute('truncate table ' . $this->setOrGetTableName());
		} else {
			$app = Lamb_App::getGlobalApp();
			$sql = 'select path from ' . $this->setOrGetTableName() . ' where 1=1';
			$sqlHelper = $app->getSqlHelper();
			foreach ($condition as $key => $val) {
				$sql .= ' and ' . $sqlHelper->escapeField($key) . "='" . $sqlHelper->escape($val) . "'";
			}
			$recordSet = $app->getDb()->query($sql);
			unset($sqlHelper, $app);
			foreach ($recordSet as $item) {
				Lamb_IO_File::delete($this->_mFileCache->getPath() . trim($item['path']) . $this->_mFileCache->setOrGetExtendtion());
			}
		}
		return $this;
	}
}