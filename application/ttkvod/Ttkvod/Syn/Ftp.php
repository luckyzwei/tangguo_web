<?php
class Ttkvod_Syn_Ftp implements Ttkvod_Syn_Interface
{
	/**
	 * @var Ttkvod_Ftp
	 */
	protected $mFtp = null;
	
	/**
	 * @var string
	 */
	protected $mSavePath = '';
	
	/**
	 * @var string
	 */
	protected $mLocalPathPrev = '';	
	
	/**
	 * @var boolean
	 */
	protected $mIsQuickQuit = false;
	
	/**
	 * @param array $aOptions = array(
	 *			'host' => 
	 *			'port' => 0
	 *			'username' => 
	 *			'password' => 
	 *			'timeout' => 90
	 *			'savepath' => '',
	 *			'quickquit' => false,
	 *			'local_path_prev' => 
	 *		)
	 * @throws Lamb_Exception
	 */
	public function __construct(array $aOptions)
	{
		$options = array(
			'host' => '', 'port' => '0', 'username' => '', 'password' => '',
			'timeout' => 90, 'savepath' => '', 'quickquit' => false, 'local_path_prev' => ''
		);
		Lamb_Utils::setOptions($options, $aOptions);
		$this->mFtp = new Ttkvod_Ftp();
		if (!$this->mFtp->init($options)) {
			throw new Lamb_Exception('Initialize ftp option failed,please check the arguments ' . print($options, true));
		}
		$this->mSavePath = $options['savepath'];
		$this->mLocalPathPrev = $options['local_path_prev'];
		$this->mIsQuickQuit = $options['quickquit'];
	}
	
	/**
	 * Ttkvod_Syn_Interface implemention
	 */
	public function write($identity, $strRemoteName = null, $path = null)
	{
		if ($path === null) {
			$path = $this->mSavePath;
		}
		if ($strRemoteName === null) {
			$strRemoteName = basename($strRemoteName);
		}
		$identity = $this->getRealIdentity($identity);
		if (!Lamb_IO_File::exists($identity)) {
			return false;
		}
		$file = fopen($identity, 'r');
		if (!$this->mFtp->chdir($path)) {
			return false;
		}
		if ($this->mIsQuickQuit) {
			$bRet = $this->mFtp->mbRun(array(
					'remote_file' => $strRemoteName,
					'local_file' => $file,
					'mode' => FTP_BINARY
				));
		} else {
			$bRet = $this->mFtp->put($strRemoteName, $file, FTP_BINARY);
		}
		fclose($file);
		return $bRet;
	}
	
	/**
	 * 由于local_path_prev参数的影响，需要获取真正原始的URI
	 *
	 * @param string $identity
	 * @return string
	 */
	public function getRealIdentity($identity)
	{
		if (!Lamb_Utils::isHttp($identity)) {
			$identity = $this->mLocalPathPrev . $identity;
		}
		return $identity;		
	}	
}