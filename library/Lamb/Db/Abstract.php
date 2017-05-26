<?php
/**
 * Lamb Framework
 * @author С��
 * @package Lamb_Db
 */
abstract class Lamb_Db_Abstract extends PDO
{
	/**
	 * �����Ĵ�$aPrepareSource�а�Ԥ����ֵ��$stmt������
	 *
	 * @param &PDOStatement $stmt
	 * @param array $aPrepareSource [
	 *									SQL����������������Ӧ��ֵ��ֵ������
	 *								]
	 * @return void
	 */
	public static function batchBindValue(PDOStatement &$stmt, array $aPrepareSource)
	{
		foreach ($aPrepareSource as $strKey => $aItem) {
			$stmt->bindValue($strKey, $aItem[0], $aItem[1]);
		}
		unset($stmt);
	}
	
	/**
	 * ʹ�ù������α��ѯ��¼��
	 * ע���˷���������һ�������ܣ��������޷���ȡ��¼��������
	 * ���صļ�¼������ʱҪ�ǵ�ע�� eg:$recordset = null
	 * 
	 * @param string $strSql
	 * @param &array $aPrepareSource ���Ϊnull��ʹ��Ԥ�����ѯ
	 * @return Lamb_Db_RecordSet_Interface implemention
	 */
	public function dynamicSelect($strSql, array $aPrepareSource = null)
	{
		$objRecordSet	=	null;
		try{
			if($objRecordSet = $this->prepare($strSql, array(PDO::ATTR_CURSOR=>PDO::CURSOR_SCROLL))){
				if ($aPrepareSource) {
					self::batchBindValue($objRecordSet, $aPrepareSource);
				}
				$objRecordSet->execute();
			}
		}catch(Exception $e){}
		return $objRecordSet;
	}	

	/**
	 * ͨ������������SQL����ȡ��¼��������
	 * sql count(*) as num from table [where ....]
	 *
	 * @param string $strSql 
	 * @param string $strNumKey ��ȡ�����¼����������
	 * @return int
	 */
	public function getRowCount($strSql,$strNumKey='num')
	{
		$nRowNum		=	-1;
		if($objRecordSet	=	$this->query($strSql)){
			$arr	=	$objRecordSet->fetch();
			$nRowNum=	$arr[$strNumKey];
			$objRecordSet	=	null;
		}
		return $nRowNum;
	}
	
	/**
	 * ����dynamicSelect��ȡ��¼��������
	 *
	 * @param string $strSql
	 * @return int ���ʧ���򷵻�-1
	 */
	public function getRowCountDynamic($strSql)
	{
		$nRowCount		=	-1;
		if($objRecordSet = $this->dynamicSelect($strSql)){
			$nRowCount	=	$objRecordSet->rowCount();
			$objRecordSet->closeCursor();
		}
		return $nRowCount;	
	}
	
	/**
	 * ʹ��SQLԤ��������ȡ��¼�����������ڲ�������
	 * getRowCountEx�����ʧ����������ܲ��dynamicSelect
	 * 
	 * @param string $strSql
	 * @param & array $aPrepareSource
	 * @param boolean $bIncludeUnion
	 * @return int ���ʧ���򷵻�-1
	 */
	public function getPrepareRowCount($strSql, array $aPrepareSource, $bIncludeUnion = false)
	{
		$nRowNum = -1;
		$strNewSql = $this->getRowCountEx($strSql, $bIncludeUnion, true);
		if (Lamb_Utils::isInt($strNewSql, true)) {
			return $strNewSql;
		}
		$stmt = $this->prepare($strNewSql);
		self::batchBindValue($stmt, $aPrepareSource);
		$stmt->execute();
		if (($arr = $stmt->fetch())) {
			$nRowNum = $arr['num'];
		}
		else {
			$stmt = null;
			if ($stmt = $this->dynamicSelect($strSql, $aPrepareSource)) {
				$nRowNum	=	$stmt->rowCount();
				$stmt->closeCursor();				
			}
		}
		$stmt = null;
		return $nRowNum;
	}
	
	/**
	 * ִ��һ��SQL��䣬�����ظļ�¼���Լ���¼������
	 * ����ʹ�������ܲ��dynamicSelect��ûʹ�ø�Ч��getRowCountEx
	 * �������Ĳ�ѯ����Ҫ��Ϊ���ô˷����ĳ���һ���ǻ�ȡһ���ļ�¼�������
	 * ���ڿ��ܻ�����
	 * 
	 * @param string $strSql
	 * @param boolean $bGetData
	 * @return array ���$bGetData = false �򷵻ؼ�¼����
	 *				 ���Ϊtrue���򷵻�һ��array('num' => ������'data' => ��¼����)
	 */
	public function getNumData($strSql, $bGetData=false)
	{
		if(!$bGetData) return $this->getRowCountDynamic($strSql);
		$aResult		=	array('num'=>-1,'data'=>null);
		if($objRecordSet = $this->dynamicSelect($strSql)){
			$aResult['num']	=	$objRecordSet->rowCount();
			$aResult['data']=	$objRecordSet->fetch();
			$objRecordSet->closeCursor();
		}
		return $aResult;
	}
	
	/**
	 * ʹ��SQLԤ�����ȡͬgetNumDataһ���Ĺ���
	 *
	 * @param string $strSql
	 * @param & array $aPrepareSource
	 * @param boolean $bGetData
	 * @return array ���$bGetData = false �򷵻ؼ�¼����
	 *				 ���Ϊtrue���򷵻�һ��array('num' => ������'data' => ��¼����)
	 */
	public function getNumDataPrepare($strSql, array $aPrepareSource = null, $bGetData = false)
	{
		$aResult = $bGetData ? array('num' => -1, 'data' => null) : -1;
		$objRecordSet = $this->quickPrepare($strSql, $aPrepareSource);
		if ($objRecordSet) {
			if ($bGetData) {
				$aData = $objRecordSet->fetchAll();
				$aResult['data'] = @$aData[0];
				$aResult['num'] = count($aData);
			}
			else {
				$aResult = count($objRecordSet->fetchAll());
			}
			$objRecordSet = null;
		}
		return $aResult;
	}
	
	/**
	 * ����ʹ��SQLԤ����ִ��SQL���
	 * ע�����صļ�¼������ʱҪ�ǵ�ע�� eg:$recordset = null
	 *
	 * @param string $strSql
	 * @param & array $aPrepareSource
	 * @param boolean $bExec ���Ϊtrue�����PODStatement::execute�����ؼ�¼��
	 * @return Lamb_Db_RecordSet_Interface
	 */
	public function quickPrepare($strSql, array $aPrepareSource=null, $bExec = false)
	{
		$objRecordSet = null;
		$objRecordSet = $this->prepare($strSql);
		self::batchBindValue($objRecordSet, $aPrepareSource);
		if ($bExec) {
			$objRecordSet = $objRecordSet->execute();
		}
		else {
			$objRecordSet->execute();
		}
		return $objRecordSet;
	}	
	
	/**
	 * ��ѯָ��ƫ�ƹ̶����ȵļ�¼��
	 * ע�����صļ�¼������ʱҪ�ǵ�ע�� eg:$recordset = null
	 *
	 * @param string $strSql
	 * @param int $nLimit
	 * @param int $nOffset
	 * @param boolean $bIncludeUnion
	 * @return Lamb_Db_RecordSet_Interface
	 */ 
	public function limitSelect($strSql,$nLimit,$nOffset=0,$bIncludeUnion=false)
	{
		$objRecordSet	=	null;
		if($strNewSql = Lamb_App::getGlobalApp()->getSqlHelper()->getLimitSql($strSql,$nLimit,$nOffset,$bIncludeUnion)){
			$objRecordSet = $this->query($strNewSql);
		}
		return $objRecordSet;
	}			

	/**
	 * ��ʼһ����������ɹ��򷵻�true����false
	 *
	 * @return boolean
	 */
	public function begin()
	{
		return $this->beginTransaction();
	}	
	
	/**
	 * �ύ��ع�һ��ʧ������ύ��ع��ɹ��򷵻�true����false
	 *
	 * @return boolean
	 */
	abstract public function end();
	
	/**
	 * �Ľ�getRowCount������SQL�������ʹ��sql count(*) as num from table�����ĸ�ʽ
	 * ��ͨ���κ�һ��SQL���eg:select * from test �����Զ����������ϵĸ�ʽ
	 * �������$bRetSqlΪtrue�򷵻ؽ������SQL����ִ�н������SQL��䲢���ؽ��
	 *
	 * @param string $strSql
	 * @param boolean $bIncludeUnion SQL������Ǻ���union�ؼ���
	 * @param boolean $bRetSql
	 * @return string | int ���$bRetSqlΪtrue�򷵻ش�����SQL�����򷵻�����
	 *						���ʧ���򷵻�-1
	 */
	abstract public function getRowCountEx($strSql,$bIncludeUnion=false, $bRetSql = false);
}