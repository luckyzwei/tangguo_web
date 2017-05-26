<?php
class Ttkvod_Cache_Local_Memcached extends Ttkvod_Cache_Local_Abstract
{
	/**
	 * @var Lamb_Cache_Memcached
	 */
	protected $_mMemcached;
	
	/**
	 * @param array $connectOptions  
	 * @param int $cachetime
	 * @param string $path
	 * @param string $sql
	 * @param string $table
	 * @param int $pagesize
	 */
	public function __construct(array $connectOptions = null, $cachetime = null, $path = null, $sql = null, $table = null, $pagesize = 0)
	{
		parent::__construct($path, $sql, $table, $pagesize);
		$this->_mMemcached = new Lamb_Cache_Memcached($connectOptions, $cachetime, $path === null ? null : $this->getIdentity());
	}
	
	/**
	 * @param array $options = array('host'=>, 'port'=>, 'timeout'=>(default:15), 'type'=>[T_NOMAL|T_PCONNECT])
	 * @return Lamb_Cache_Memcached
	 */
	public function connect(array $connectOptions)
	{
		return $this->_mMemcached->connect($connectOptions);
	}

	/**
	 * @return boolean
	 */	
	public function close()
	{
		return $this->_mMemcached->close();
	}
	
	/**
	 * Lamb_Cache_Interface implemention
	 */
	public function setIdentity($identity)
	{
		parent::setIdentity($identity);
		$this->_mMemcached->setIdentity($this->getIdentity());
		return $this;
	}

	/**
	 * Lamb_Cache_Interface implemention
	 */	
	public function setCacheTime($second)
	{
		$this->_mMemcached->setCacheTime($second);
		return $this;
	}
	
	/**
	 * Lamb_Cache_Interface implemention
	 */	
	public function getCacheTime()
	{
		return $this->_mMemcached->getCacheTime();
	}
	
	/**
	 * Lamb_Cache_Interface implemention
	 */	
	public function isCached()
	{
		return $this->_mMemcached->isCached();
	}
	
	/**
	 * Lamb_Cache_Interface implemention
	 */		
	public function flush()
	{
		if ($this->_mMemcached->flush()) {
			return $this->deleteRecord();
		}
		return false;
	}
	
	/**
	 * Lamb_Cache_Interface implemention
	 */	
	public function read()
	{
		return $this->_mMemcached->read();	
	}

	/**
	 * Lamb_Cache_Interface implemention
	 */		
	public function write($data)
	{
		$this->_mMemcached->write($data);
		return $this->writeRecord();
	}
	
	/**
	 * Ttkvod_Cache_Local_Abstract implemention
	 */
	public function clear(array $condition = null)		
	{
		if (null === $condition) {
			$this->_mMemcached->flush();
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
			$memcached = $this->_mMemcached->getRawMemcached();
			foreach ($recordSet as $item) {
				$memcached->delete(trim($item['path']));
			}			
		}
		return $this;
	}
}