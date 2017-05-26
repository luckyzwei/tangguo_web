<?php
interface Ttkvod_Model_LinkRouter_Interface
{
	const T_MODE_STATIC = 1;
	
	const T_MODE_DYNAMIC = 2;
	
	const T_MODE_HOSTNAME = 3;
	
	const T_MODE_STATIC_PATH = 4;
	
	/**
	 * @return string
	 */
	public function getHost();
	
	/**
	 * @param array $params
	 * @return string
	 */
	public function getStaticLink(array $params);

	/**
	 * @param array $params
	 * @return string
	 */	
	public function getStaticLinkPath(array $params);

	/**
	 * @param array $params
	 * @return string
	 */	
	public function getDynamicLink(array $params);
}