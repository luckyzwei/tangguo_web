<?php
/**
 * Lamb Framework
 * @author С��
 * @package Lamb
 */
class Lamb_Upload
{
	const SUFFIX_CHECK_ALLOWS = 1;
	
	const SUFFIX_CHECK_UNALLOWS = 2;
	
	/**
	 * @var array �����ϴ�����չ��
	 */
	protected $_allowSuffixs = array(
					'.gif', '.jpg', '.png'
				);
	/**
	 * @var array �������ϴ�����չ�� ���ȼ����
	 */
	protected $_unallowSuffixs = array();
	
	/**
	 * @var int �ϴ��ļ����С��0Ϊ������ KB
	 */
	protected $_maxFilesize = 0; 
	
	/**
	 * @var int
	 */
	protected $_mCheckSuffixType = self::SUFFIX_CHECK_ALLOWS;
	
	
	public function __construct()
	{
	
	}
	
	/**
	 * @return array
	 */
	public function getAllowSuffixs()
	{
		return $this->_allowSuffixs;
	}
	
	/**
	 * @param string
	 *��@return Lamb_Upload
	 */
	public function addAllowSuffix($ext)
	{
		if (!in_array($ext, $this->getAllowSuffixs())) {
			$this->_allowSuffixs[] = $ext;
		}
		return $this;
	}
	
	/**
	 * @param string $ext
	 * @return boolean | int if exists
	 */
	public function hasAllowSuffix($ext)
	{
		return in_array($ext, $this->getAllowSuffixs());
	}
	
	/**
	 * @param string $ext
	 * @return Lamb_Upload
	 */
	public function removeAllowSuffix($ext)
	{
		if (false !== ($index = $this->hasAllowSuffix($ext))) {
			unset($this->_allowSuffixs[$index]);
		}
		return $this;
	}
	
	/**
	 * @return array
	 */
	public function getUnallowSuffixs()
	{
		return $this->_unallowSuffixs;
	}
	
	/**
	 * @param string
	 *��@return Lamb_Upload
	 */
	public function addUnallowSuffix($ext)
	{
		if (!in_array($ext, $this->getUnallowSuffixs())) {
			$this->_unallowSuffixs[] = $ext;
		}
		return $this;
	}
	
	/**
	 * @param string $ext
	 * @return boolean | int if exists
	 */
	public function hasUnallowSuffix($ext)
	{
		return in_array($ext, $this->getUnallowSuffixs());
	}
	
	/**
	 * @param string $ext
	 * @return Lamb_Upload
	 */
	public function removeUnallowSuffix($ext)
	{
		if (false !== ($index = $this->hasUnallowSuffix($ext))) {
			unset($this->_unallowSuffixs[$index]);
		}
		return $this;
	}
	
	/**  
	 * @param int $size
	 * @return int | Lamb_Uplaod
	 */
	public function setOrGetMaxFilesize($size = null)
	{
		if (null === $size) {
			return $this->_maxFilesize;
		}
		$this->_maxFilesize = (int)$size;
		return $this;
	}
	
	/**
	 * @param int $checkSuffixType
	 * @return int | Lamb_Upload
	 */
	public function setOrGetCheckSuffixType($checkSuffixType = null)
	{
		if (null === $checkSuffixType) {
			return $this->_mCheckSuffixType;
		}
		$this->_mCheckSuffixType = (int)$checkSuffixType;
		return $this;
	}
	
	/**
	 * @param array $aOptions = array(
	 *					'varname' => '', http��ʶ�� ���Ϊ����Ϊ���ļ��ϴ�
	 *					'is_keepname' => false, �Ƿ񱣴�ԭ�ļ���
	 *					'save_path' => '',�����·��
	 *					'is_safe_check' => true �Ƿ���Ҫ��չ�����ļ���С�ȼ��
	 *				)
	 * @return int | array ���������Ϊint�� -1Ϊû�п��ϴ����ļ� >=0��Ϊû��ͨ��������ļ�����
	 * 						����ɹ��򷵻سɹ����ļ����飬ÿ��Ԫ�ض����ļ���
	 */
	public function upload(array $aOptions)
	{
		$options = array(
			'varname' => '',
			'is_keepname' => false,
			'save_path' => '',
			'is_safe_check' => true
		);
		Lamb_Utils::setOptions($options, $aOptions);
		$attachments = $this->getAttachments($options['varname']);
		if (false === $attachments) { //û�п��ϴ����ļ�
			return -1;
		}
		if ($options['is_safe_check']) {//����
			if (($errorno = $this->checkSuffix($attachments)) >= 0) {//��չ�����ʧ��
				return $errorno;
			}
			if (($errorno = $this->checkSize($attachments)) >= 0) {//�ļ���С���ʧ��
				return $errorno;
			}
		}
		$aFiles = $this->_upload($options['save_path'], $attachments, $options['is_keepname']);
		return count($aFiles) ? $aFiles : -1;
	}	
	
	/**
	 * @param array $files �ļ��������ļ�������
	 * @return int -1 sucss >= 0 �����ĸ��ļ��ϴ�ʧ��
	 */
	public function checkSuffix(array $files)
	{
		$suffixType = $this->setOrGetCheckSuffixType();
		foreach ($files as $key => $file) {
			$suffix = Lamb_IO_File::getFileExt($file['name']);
			if ($suffixType === self::SUFFIX_CHECK_ALLOWS && !$this->hasAllowSuffix($suffix)) {
				return $key;
			}
			if ($suffixType === self::SUFFIX_CHECK_UNALLOWS && $this->hasUnallowSuffix($suffix)) {
				return $key;
			}
		}
		return -1;
	}

	/**
	 * @param array $files �ļ��������ļ�������
	 * @return int -1 sucss >= 0 �����ĸ��ļ��ϴ�ʧ��
	 */	
	public function checkSize(array $files)
	{
		$maxsize = $this->setOrGetMaxFilesize() * 1024;
		if ($maxsize > 0) {
			foreach ($files as $key => $file) {
				if ($maxsize < $file['size']) {
					return $key;
				}
			}
		}
		return -1;
	}
	
	/**
	 * @param string $varname ���$varmaeΪ����Ϊ���ļ��ϴ�
	 * @return array | false if not found
	 */
	public static function getAttachments($varname = '')
	{
		$ret = array();
		if(!$varname) {
			foreach ($_FILES as $v) {
				!empty($v['name']) && $v['error'] == 0 ? $ret[] = $v : '';
			}
			if (count($ret)<=0) {
				return false;
			}
		} else {
			if (!isset($_FILES[$varname]) || !is_array($_FILES[$varname])) {
				return false;
			}
			if (is_array($_FILES[$varname]['error'])) {
					if ($_FILES[$varname]['error'][$key] === 0) {
						$ret[] = array(
								'name' => $_FILES[$varname]['name'][$key],
								'tmp_name' => $_FILES[$varname]['tmp_name'][$key],
								'type' => $_FILES[$varname]['type'][$key],
								'size' => $_FILES[$varname]['size'][$key]
							);
					}
			} else if ($_FILES[$varname]['error'] === 0){
				$ret[0] = $_FILES[$varname];
			}			
		}
		return $ret;	
	}
	
	/**
	 * �ϴ��ļ�
	 *
	 * @param string $path
	 * @param array $attachments froms $_FILES
	 * @param boolean $isKepp
	 * @return array
	 */
	protected function _upload($path, array $attachments, $isKeep = false)
	{
		$aFileName=array();
		foreach($attachments as $k => $data) {		
			if (!$isKeep) {
				$filepath = Lamb_IO_File::generateCrc32EncodeFileNamePath($path . microtime(true) . rand(0, 1000), 
								Lamb_IO_File::getFileExt($data['name']));
			} else {
				$filepath = $path . $data['name'];
			}
			$filepath = Lamb_IO_File::getUniqueName($filepath);
			move_uploaded_file($data['tmp_name'], $filepath);
			@unlink($data['tmp_name']);
			$aFileName[] = $filepath;
		}
		return $aFileName;	
	}
}