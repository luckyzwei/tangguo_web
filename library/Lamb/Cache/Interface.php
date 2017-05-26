<?php
/**
 * Lamb Framework
 * @author С��
 * @package Lamb_Cache
 */
interface Lamb_Cache_Interface
{
	/** 
	 * ���û���ʱ�� 
	 *
	 * @param int $second
	 * @return Lamb_Cache_Interface
	 */
	public function setCacheTime($second);
	
	/**
	 * @return int
	 */
	public function getCacheTime();
	
	/**
	 * ���û���ı�ʶ
	 * �磺�ļ�����ı�ʶ��·��
	 * �ڴ滺���Ǽ�ֵ
	 *
	 * @param string | int $identity
	 * @return Lamb_Cache_Interface
	 */
	public function setIdentity($identity);
	
	/** 
	 * Get the cache's identity
	 *
	 * @return string | int
	 */
	public function getIdentity();
	
	/**
	 * Read data from cache
	 *
	 * @return mixed ���Ϊnull��Ϊ���滹δ�������Ѿ�����
	 */
	public function read();
	
	/**
	 * Write data to cache
	 *
	 * @return boolean
	 */
	public function write($data);
	
	/**
	 * Flush the cache
	 *
	 * @reutnr boolean is success?
	 */
	public function flush();
	
	/** 
	 * Retrieve the data whether in cached
	 *
	 * @return boolean
	 */
	public function isCached();
}