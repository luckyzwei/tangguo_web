<?php
class Ttkvod_Ftp
{
	protected $m_hHandle;
	
	public $m_bAutoClose;
	/**
	 * @param array $aParam = array(
	 * 	'host' =>
	 * 	'prot' => [0]
	 * 	'username' =>
	 * 	'password' =>
	 * 	'timeout' => [90]
	 * )
	 */
	public function __construct($aParam = null)
	{
		$this->m_hHandle = null;
		$this->m_bAutoClose = true;
		if ($aParam) {
			$this->init($host, $aUserInfo);
		}
	}
	
	public function __destruct()
	{
		if ($this->m_bAutoClose) {
			$this->close();
		}
	}
	
	public function close()
	{
		$bRet = false;
		if ($this->m_hHandle) {
			$bRet = ftp_close($this->m_hHandle);
		}
		return $bRet;
	}
	
	public function init($aParam)
	{
		$aOption = array('port' => 0, 'timeout' => 90);
		CGlobalApi::setOptions($aOption, $aParam);
		$ret = ftp_connect($aOption['host'], $aOption['port'], $aOption['timeout']);
		if (!$ret) {
			return false;
		}
		if (!ftp_login($ret, $aOption['username'], $aOption['password'])) {
			return false;
		}
		$this->m_hHandle = $ret;
		return true;
	}
	
	/**
	 * �첽�ϴ���FTP�������� 
	 * ���$mixLocalFile��Http��Դ������ftp_nb_fput
	 *
	 * @param string $strRemoteFileName Զ���ļ���
	 * @param string | resource $mixLocalFile �����ļ���������Դ
	 * @param int $nMode ָ�����͵��ļ����ı��ͻ��Ƕ�����
	 * @param int $nStartPos ����
	 */
	public function nbPut($strRemoteFileName, $mixLocalFile, $nMode = FTP_ASCII, $nStartPos = 0)
	{
		if (!$this->m_hHandle) return false;
		$strFuncName = is_resource($mixLocalFile) ? 'ftp_nb_fput' : 'ftp_nb_put';
		return $strFuncName($this->m_hHandle, $strRemoteFileName, $mixLocalFile, $nMode, $nStartPos);
	}
	
	public function nbContinue()
	{
		return ftp_nb_continue($this->m_hHandle);
	}
	
	public function put($strRemoteFileName, $mixLocalFile, $nMode = FTP_ASCII, $nStartPos = 0)
	{
		if (!$this->m_hHandle) return false;
		$strFuncName = is_resource($mixLocalFile) ? 'ftp_fput' : 'ftp_put';
		return $strFuncName($this->m_hHandle, $strRemoteFileName, $mixLocalFile, $nMode, $nStartPos);		
	}
	
	public function setOption($nName, $mixValue)
	{
		return ftp_set_option($this->m_hHandle, $nName, $mixValue);
	}
	
	public function getOption($nName)
	{
		return ftp_get_option($this->m_hHandle, $nName);
	}
	
	public function chdir($strDir)
	{
		return ftp_chdir($this->m_hHandle, $strDir);
	}
	
	/**
	 * ��NB�ຯ���򵥵�ѭ����װ
	 * @param array $aParam = array(
	 * 	'remote_file' =>
	 *  'local_file' =>
	 *  'mode' => FTP_ASCII,
	 *  'start_pos' => 0,
	 *  'call_back' =>
	 *  'func_param' => call_back���õĲ���
	 * )
	 */
	public function nbRun($aParam)
	{
		$aOptions = array(
			'mode' => FTP_ASCII,
			'start_pos' => 0,
			'call_back' => '',
			'func_param' => array()
		);
		CGlobalApi::setOptions($aOptions, $aParam);
		$ret = $this->nbPut($aOptions['remote_file'], $aOptions['local_file'], $aOptions['mode'], $aOptions['start_pos']);
		if (is_callable($aOptions['call_back'], false, $call_name)) {
			while ($ret == FTP_MOREDATA) {
				call_user_func_array($call_name, $aOptions['func_param']);
				$ret = $this->nbContinue();
			}
		}
		else {
			while ($ret == FTP_MOREDATA) {
				$ret = $this->nbContinue();
			}
		}
		return $ret == FTP_FINISHED;
	}
}