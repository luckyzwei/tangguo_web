<?php
/**
 * Lamb Framework
 * @author С��
 * @package Lamb_View_Tag
 */
abstract class Lamb_View_Tag_Abstract implements Lamb_View_Tag_Interface
{
	/**
	 * @var array id ע���
	 */
	protected static $sRegistryMap = array();
	
	/**
	 * ����ָ������������������Դ�����л�ȡ����ֵ
	 * �����Խ�������ֵ�е�PHP����
	 * 
	 * @param string $strAttrName ������
	 * @param string $strAttrs ����Դ����
	 * @param boolean $bParseVar �Ƿ��������ֵ�е�PHP����
	 * @param boolean $hasPrev ������ı���ǰ���Ƿ�Ҫ�ӵ����Ż���˫����
	 *							�����ȡ������ֵֻ����ֱ���������Խ���ֵ��Ϊfalse
	 *							�磺sql='@$abc@' $hasPrev=false������ 'sql' => $abc
	 *							�������ֵ���ܻ���ֱ������ַ����Ľ�ϣ���Ҫ����ֵ��true
	 *							�磺sql='select * from @$table@' $hasPrev=true������ 'sql' => 'select * from'.$table.''
	 * @return string
	 */
	public static function getTagAttribute($strAttrName, $strAttrs, $bParseVar = true, $hasPrev = true)
	{
		$strPatt		=	'/\b'.$strAttrName.'=([\'"])(.*?)\1/is';
		if(!preg_match($strPatt,$strAttrs,$aMatches)) return false;
		$strAttrValue	=	self::codeAddslashes(trim($aMatches[2]));
		if(!$bParseVar) return $strAttrValue;
		return self::parseVar($strAttrValue, $hasPrev);	
	}
	
	/**
	 * ��������ֵ�е�PHP����
	 * 
	 * @param string $strAttrValue �����������ֵ
	 * @param boolean $isNumber ����ֵ�Ƿ�������
	 * @param string $hasPrev ��������ֵ���ַ�����Ҫ�Ӷ����
	 * @return string
	 */
	public static function parseVar($strAttrValue ,$hasPrev = true, $strPrev="'")
	{
		$strVarPatt		=	'/@(\$.*?)@/is';
		return preg_replace($strVarPatt, $hasPrev ? $strPrev . '.$1.' . $strPrev : '$1', $strAttrValue);
	}
	
	/**
	 * ת������ֵ�е�\ '
	 * 
	 * @param string $str Ҫ����������
	 * @param int $num ��б�ܵĸ�����ʵ�ʸ�����$num*2 
	 * @return string
	 */
	public static function codeAddslashes($str,$num=1)
	{
		return preg_replace('/(\')/s',str_repeat('\\',$num*2).'$1',preg_replace('/\\\(?!\')/s','\\\\\\',$str));
	}
	
	/**
	 * ͨ��ID������ע���Ա���һ����ǩ���ã����$dataParam Ϊnull 
	 * ��$id���ڣ���ɾ����ID��ע��
	 *
	 * @param string | int $id
	 * @param mixed $dataParam
	 * @return boolean true -> success false -> fail or exists
	 */
	public static function registerById($id, $dataParam)
	{
		if ($dataParam === null) {
			if (array_key_exists($id, self::$sRegistryMap)) {
				unset(self::$sRegistryMap[$id]);
			}
		} else {
			self::$sRegistryMap[$id] = $dataParam;
		}
		return true;
	}
	
	/**
	 * ��ȡ�Ѿ�ע��Ĳ���
	 *
	 * @param string | id $id
	 * @return mixed if not found return null
	 */
	public static function getRegisterdById($id)
	{
		$ret = null;
		if (array_key_exists($id, self::$sRegistryMap)) {
			$ret = self::$sRegistryMap[$id];
		}
		return $ret;
	}
}