<?php
class Ttkvod_Cache_Factory
{
	const CACHE_FILE = Lamb_View_Tag_List::CACHE_FILE;
	
	const CACHE_MEMCACHED = Lamb_View_Tag_List::CACHE_MEM;
	
	const CACHE_LOCAL_FILE = 0x40;
	
	const CACHE_LOCAL_MEMCACHED = 8;
	
	const CACHE_COMM_FILE = 0x10;
	
	const CACHE_DB_FILE = 0x20;
	
	const CACHE_HTML_FILE = Lamb_View_Tag_List::CACHE_HTML;
	
	const CACHE_LOCAL_CACHE = 0x80;
	
	/**
	 * @param array $options = array(
	 *					'timeout' =>  int(default:CONFIG['cache_cfg']['timeout'])
	 *					'path' => 
	 *					'identity' => null
	 *					'extention' => string(default:CONFIG['cache_cfg']['file_extendtion'])
	 *				)
	 * @return Ttkvod_Cache_File
	 */
	public static function getFileCache(array $options = array())
	{
		$cfg = Lamb_Registry::get(CONFIG);
		$opt = array(
			'timeout' => $cfg['cache_cfg']['timeout'],
			'extendtion' => $cfg['cache_cfg']['file_extendtion'],
			'identity' => null,
			'path' => ''
		);
		unset($cfg);
		Lamb_Utils::setOptions($opt, $options);
		$cache = new Ttkvod_Cache_File($opt['timeout'], $opt['identity']);
		$cache->setPath($opt['path']);
		if ($opt['extendtion']) {
			$cache->setOrGetExtendtion($opt['extendtion']);
		}
		return $cache;
	}
	
	/**
	 * @param array $options = array(
	 *				'timeout' => int(default:CONFIG['cache_cfg']['timeout']),
	 *				'identity' => string
	 *				'mem_host' => string(default:CONFIG['cache_cfg']['mem_host']),
	 *				'mem_port' => string(default:CONFIG['cache_cfg']['mem_port']),
	 *				'mem_pconnect' => boolean(default:CONFIG['cache_cfg']['mem_pconnect']),
	 *				'mem_connect_timeout' => int(default:CONFIG['cache_cfg']['mem_connect_timeout'])
	 *			)
	 * @return Lamb_Cache_Memcached
	 */
	public static function getMemcached(array $options = array())
	{
		$cfg = Lamb_Registry::get(CONFIG);
		$opt = array(
			'timeout' => $cfg['cache_cfg']['timeout'],
			'identity' => null,
			'mem_host' => $cfg['cache_cfg']['mem_host'],
			'mem_port' => $cfg['cache_cfg']['mem_port'],
			'mem_pconnect' => $cfg['cache_cfg']['mem_pconnect'],
			'mem_connect_timeout' => $cfg['cache_cfg']['mem_connect_timeout']
		);
		unset($cfg);
		Lamb_Utils::setOptions($opt, $options);
		return new Lamb_Cache_Memcached(array(
						'timeout' => $opt['mem_connect_timeout'],
						'host' => $opt['mem_host'],
						'port' => $opt['mem_port'],
						'type' => $opt['mem_pconnect'] ? Lamb_Cache_Memcached::T_PCONNECT : Lamb_Cache_Memcached::T_NORMAL
				), $opt['timeout'], $opt['identity']);
	}
	
	/**
	 * @param array $options = array(
	 *					'timeout' => int(default:CONFIG['cache_cfg']['timeout']),
	 *					'identity' => null,
	 *					'path' =>
	 *					'sql' => null,
	 *					'table' => null,
	 *					'pagesize' => 0
	 *				)
	 * @return Ttkvod_Cache_Local_File
	 */
	public static function getFileLocalCache(array $options = array())
	{
		$cfg = Lamb_Registry::get(CONFIG);
		$opt = array(
			'timeout' => $cfg['cache_cfg']['timeout'],
			'identity' => null,
			'path' => $cfg['cache_cfg']['local_path'],
			'sql' => null,
			'table' => null,
			'pagesize' => 0
		);
		unset($cfg);
		Lamb_Utils::setOptions($opt, $options);
		$cache = new Ttkvod_Cache_Local_File($opt['timeout'], $opt['identity'],
					$opt['sql'], $opt['table'], $opt['pagesize']);
		$cache->setPath($opt['path']);
		return $cache;
	}
	
	/**
	 * @param array $options = array(
	 *				'timeout' => int(default:CONFIG['cache_cfg']['timeout']),
	 *				'identity' => string
	 *				'mem_host' => string(default:CONFIG['cache_cfg']['mem_host']),
	 *				'mem_port' => string(default:CONFIG['cache_cfg']['mem_port']),
	 *				'mem_pconnect' => boolean(default:CONFIG['cache_cfg']['mem_pconnect']),
	 *				'mem_connect_timeout' => int(default:CONFIG['cache_cfg']['mem_connect_timeout']),
	 *				'sql' => null,
	 *				'table' => null,
	 *				'pagesize' => 0	 				
	 *			)
	 *	@return Ttkvod_Cache_Local_Memcached
	 */
	public static function getMemLocalCache(array $options = array())
	{
		$cfg =Lamb_Registry::get(CONFIG);
		$opt = array(
			'timeout' => $cfg['cache_cfg']['timeout'],
			'identity' => null,
			'mem_host' => $cfg['cache_cfg']['mem_host'],
			'mem_port' => $cfg['cache_cfg']['mem_port'],
			'mem_pconnect' => $cfg['cache_cfg']['mem_pconnect'],
			'mem_connect_timeout' => $cfg['cache_cfg']['mem_connect_timeout'],			
			'sql' => null,
			'table' => null,
			'pagesize' => 0
		);
		unset($cfg);
		Lamb_Utils::setOptions($opt, $options);	
		return new Ttkvod_Cache_Local_Memcached(array(
						'timeout' => $opt['mem_connect_timeout'],
						'host' => $opt['mem_host'],
						'port' => $opt['mem_port'],
						'type' => $opt['mem_pconnect'] ? Lamb_Cache_Memcached::T_PCONNECT : Lamb_Cache_Memcached::T_NORMAL
				), $opt['timeout'], $opt['identity'], $opt['sql'], $opt['table'], $opt['pagesize']);			
	}
	
	/**
	 * @param int $type
	 * @param array $options
	 * @return Lamb_Cache_Interface
	 */
	public static function getCache($type = null, array $options = null)
	{
		$cfg = Lamb_Registry::get(CONFIG);
		if (!$options) {
			$options = array();
		}
		if (!$type || !Lamb_Utils::isInt($type, true)) {
			$type = $cfg['cache_cfg']['type'];
		}
		if ($type == self::CACHE_LOCAL_CACHE) {
			$type = $cfg['cache_cfg']['type'] & self::CACHE_FILE ? self::CACHE_LOCAL_FILE : self::CACHE_LOCAL_MEMCACHED;
		}
		$cache = null;
		if ($type & self::CACHE_FILE) {
			if ($type & self::CACHE_DB_FILE) {
				$options['path'] = $cfg['cache_cfg']['db_path'];
			} else if ($type & self::CACHE_HTML_FILE) {
				$options['path'] = $cfg['cache_cfg']['html_path'];
			} else if ($type & self::CACHE_COMM_FILE || !isset($options['path'])) {
				$options['path'] = $cfg['cache_cfg']['comm_path'];
			}
			$cache = self::getFileCache($options);
		} else if ($type & self::CACHE_MEMCACHED) {
			$cache = self::getMemcached($options);
		} else if ($type == self::CACHE_LOCAL_FILE) {
			$cache = self::getFileLocalCache($options);
		} else if ($type == self::CACHE_LOCAL_MEMCACHED) {
			$cache = self::getMemLocalCache($options);
		}
		return $cache;
	}
}