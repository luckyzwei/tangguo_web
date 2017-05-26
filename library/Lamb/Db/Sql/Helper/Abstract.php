<?php
/**
 * Lamb Framework
 * Lamb_Db_Sql_Helper_Abstract��SQL�����߳����࣬��Ҫ����
 * ��ȡ��ҳSQL������SQL��䣬����ÿ��SQL���洦��ķ�ʽ��һ��
 * ��˽������������������涼Ҫ�̳д˳�����
 *
 * @author С��
 * @package Lamb_Db_Sql_Helper
 */
abstract class Lamb_Db_Sql_Helper_Abstract
{

	/**
	 * ���ɷ�ҳ��SQL�������SQL��䲻�ð�����ҳ�����
	 *
	 * @param string $strSql
	 * @param int $nPageSize
	 * @param int $nPage
	 * @param boolean $bIncludeUnion
	 * @return string
	 */
	public function getPageSql($strSql, $nPageSize, $nPage = 1, $bIncludeUnion=false)
	{
		return $this->getLimitSql($strSql, $nPageSize, ($nPage-1) * $nPageSize, $bIncludeUnion);
	}
	
	/**
	 * ��ȡSQL����е���������
	 *
	 * @param string $sql
	 * @return string
	 */
	public function getSqlField($sql)
	{
		$aMatchs	=	array();
		$strFields	=	'';
		if(preg_match('/^select(.+?)from(.+?)/is', $sql, $aMatchs)){
			$strFields	=	$aMatchs[1];
		}
		return $strFields;
	}
	
	/**
	 * �ж�SQL������Ƿ���UNION�ؼ���
	 * ע���˷�����̫�ɿ�
	 *
	 * @param string $sql
	 * @return boolean 
	 */
	public function hasUnionKey($sql)
	{
		return strpos(strtolower($sql), ' union ') ? true : false;
	}	
	
	/**
	 * ���ɻ�ȡָ��offset�Լ��̶����ȼ�¼��SQL��� 
	 *
	 * @param string $sql
	 * @param int $nLimit
	 * @param int $nOffset
	 * @param boolean $bIncludeUnion
	 * @return string
	 */
	abstract public function getLimitSql($sql, $nLimit, $nOffset = 0, $bIncludeUnion=false);
	
	/**
	 * ת��SQL����еķǷ��ַ�
	 *
	 * @param string $sql
	 * @return string
	 */
	abstract public function escape($sql);
	
	/**
	 * ֻת��ģ�������ķǷ��ַ����������escapeת��
	 *
	 * @param string $sql
	 * @return string
	 */
	abstract public function escapeBlur($sql);
	
	/**
	 * ת��ģ�������ķǷ��ַ���������escapeת��
	 *
	 * @param string $sql
	 * @return string
	 */
	abstract public function escapeBlurEncoded($sql);
	
	/**
	 * ���ɻ�ȡָ��offset�Լ��̶����ȼ�¼��Ԥ����SQL��� 
	 * ע��SQLԤ���������ʹ��:g_limit��Ϊ���ù̶���¼���Ȳ�����
	 * :g_offset��Ϊ����ƫ��λ�ò�����
	 *
	 * @param string $sql
	 * @param boolean $bIncludeUnion
	 */
	abstract public function getPrePareLimitSql($sql, $bIncludeUnion = false);

	/** 
	 * ����������ת�壬��ֹ����ؼ���ͬ����ͬ��
	 *
	 * @param string $field
	 * @return string 
	 */	
	abstract public function escapeField($field);
}