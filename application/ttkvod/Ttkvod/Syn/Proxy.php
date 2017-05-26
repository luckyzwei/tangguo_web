<?php
class Ttkvod_Syn_Proxy implements Ttkvod_Syn_Interface
{	
	const T_LOCAL = 2;
	
	const T_HTTP = 3;
	
	const T_FTP = 4;
	
	/**
	 * @var Ttkvod_Syn_Interface
	 */
	protected $mEntity = null;
	
	/**
	 * @var string
	 */
	protected $mSavePath = null;
	
	/** 
	 * @param array $aOptions = array(
	 *			'type' => 1, //1-直接引用,2-保存本地,3-http同步，4-ftp同步,5-远程POST
	 *			'save_path' => '',
	 *			'http_param_name' => '',
	 *			'http_syn_url' => '',
	 *			'ftp_host' => '',
	 *			'ftp_port' => '',
	 *			'ftp_username' => '',
	 *			'ftp_password' => '',
	 *			'remote_post_url' => ''	
	 * )
	 */
	public function __construct(array $aOptions = null)
	{
		if (null === $aOptions) {
			$cfg = Lamb_Registry::get(CONFIG);
			$aOptions = $cfg['syn'];
			unset($cfg);
		}
		if (in_array($aOptions['type'], array(self:T_FTP, self::;T_HTTP))) {
			$options = array(
				'savepath' => $aOptions['save_path'],
				'param_name' => $aOptions['http_param_name'],
				'url' => $aOptions['remote_post_url'],
				'host' => $aOptions['ftp_host'],
				'port' => $aOptions['ftp_port'],
				'username' => $aOptions['ftp_username'],
				'password' => $aOptions['ftp_password']
			);
			if ($aOptions['type'] === self::T_FTP) {
				$this->mEntity = new Ttkvod_Syn_Ftp($options);
			} else {
				$options['ispost'] = $aOptions['http_is_post'];
				$this->mEntity = new Ttkvod_Syn_Http($options);
			}
		}
		$this->mSavePath = $aOptions['save_path'];
	}
	
	/**
	 * Ttkvod_Syn_Interface implemention
	 */
	public function write($identity, $strRemoteName = null, $path = null)
	{
		if ($this->mEntity instanceof Ttkvod_Syn_Interface) {
			return $this->mEntity($identity, $strRemoteName, $path);
		} else {
			return false;
		}
	}
}