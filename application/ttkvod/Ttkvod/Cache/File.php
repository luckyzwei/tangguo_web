<?php
class Ttkvod_Cache_File extends Lamb_Cache_File
{
	/**
	 * @var string
	 */
	protected $_mPath = null;
	
	/**
	 * @var string
	 */
	protected $_mExtendtion = '.txt';
	
	/**
	 * @string $path
	 * @return Ttkvod_LocalCache
	 * @throws Lamb_IO_Exception
	 */
	public function setPath($path)
	{
		if (!Lamb_IO_File::exists($path)) {
			throw new Lamb_IO_Exception("path \"{$path}\" passed to setPath is not exits");
		}
		$this->_mPath = (string)$path;
		return $this;
	}
	
	/**
	 * @return string
	 * @throws Lamb_IO_Exception
	 */
	public function getPath()
	{
		if (null === $this->_mPath) {
			throw new Lamb_Exception('You must set the path when you get the path');
		}
		return $this->_mPath;
	}
	
	/**
	 * @param string $extendtion
	 * @return string | Ttkvod_LocalCache
	 */
	public function setOrGetExtendtion($extendtion = null)
	{
		if (null === $extendtion) {
			return $this->_mExtendtion;
		}
		$this->_mExtendtion = (string)$extendtion;
		return $this;
	}
	
	/**
	 * Lamb_Cache_Interface implementions
	 */
	public function setIdentity($identity)
	{
		$this->_mIdentity = $this->_mPath . ((string)$identity) . $this->setOrGetExtendtion();
		return $this;
	}				
}