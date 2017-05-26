<?php
/**
 *	php通过pdo调用mssql存储过程，eg:
 *	假如现有mssql存储过程int test varchar(100) in,int in,int output，该存储过程会有一个记录集
 *	php pdo实现方法：$db = new PDO(...);$smt = $db->prepare(exec :return = test :param1,:param2,:output);
 *	$return = $param1 = $param2 = $output = ''; 
 *	$smt->bindParam(':return', $return, PDO:PARAM_INT|PDO::PARAM_INPUT_OUPUT, PDO::SQLSRV_PARAM_OUT_DEFAULT_SIZE);
 *	$smt->bindParam(':output', $output, [同上]) 注：bindParam第4个参数只对output参数有效，对input参数无效且出错
 *	$smt->bindParam(':param1', $param1, PDO:PARAM_STR);$smt->bindParam(':param2', $param2, PDO:PARAM_INT);
 *	$smt->execute();$smt->fetchAll() 注：取出的是test的记录集，$smt->nextRowset();echo $output;echo $return
 *	注：取出的是output值和return值
 */
class Ttkvod_BuildComment
{
	/**
	 * @var int
	 */
	protected $m_nLoopNum;
	
	/**
	 * @var string
	 */
	protected $m_strSqlCommand;
	
	public function __construct($strSqlCommand = 'getComment')
	{
		$this->m_strSqlCommand = $strSqlCommand;
		$this->m_nLoopNum = 0;
	}
	
	/**
	 * @return int
	 */
	public function getLoopNum()
	{
		return $this->m_nLoopNum;
	}
	
	/**
	 * @param string $strSql
	 * @param int $nParentId
	 * @param boolean $bReturn
	 * @param string $extValue
	 * @return false | string 
	 */
	public function getComment($strSql, $nParentId, $bReturn = false, $extValue = null)
	{
		$objRecordSet = Lamb_App::getGlobalApp()->getDb()->prepare('exec '.$this->m_strSqlCommand.' :sql,:parentid,:num');
		$objRecordSet->bindValue(':sql', $strSql, PDO::PARAM_STR);
		$objRecordSet->bindValue(':parentid', $nParentId, PDO::PARAM_INT);
		$objRecordSet->bindParam(':num', $this->m_nLoopNum, PDO::PARAM_INT|PDO::PARAM_INPUT_OUTPUT, PDO::SQLSRV_PARAM_OUT_DEFAULT_SIZE);
		if ( !$objRecordSet->execute() || !($aData = $objRecordSet->fetchAll())){
			return false;
		}
		$objRecordSet->nextRowset();	
		if ($this->m_nLoopNum <= 0) {
			return false;
		}
		$objRecordSet = null;
		$aNewData = array();
		$id = 0;
		for ($i = 0; $i < $this->m_nLoopNum; $i++) {
			$id = 'prev'.$aData[$i]['id'];
			//unset($aData[$i]['id']);
			$aNewData[$id] = $aData[$i];
			$aNewData[$id]['name'] = Lamb_App_Response::encodeURIComponent($aNewData[$id]['name']);
			$aNewData[$id]['content'] = Lamb_App_Response::encodeURIComponent($aNewData[$id]['content']);
		}
		for ($i = $this->m_nLoopNum, $j = count($aData); $i < $j; $i++) {
			$id = 'prev'.$aData[$i]['id'];
			//unset($aData[$i]['id']);
			if (!array_key_exists($id, $aNewData)) {
				$aNewData[$id] = $aData[$i];
				$aNewData[$id]['name'] = Lamb_App_Response::encodeURIComponent($aNewData[$id]['name']);
				$aNewData[$id]['content'] = Lamb_App_Response::encodeURIComponent($aNewData[$id]['content']);					
			}
		}
		$aNewData['loop_num'] = $this->m_nLoopNum;
		if ($extValue) {
			$aNewData['extval'] = $extValue;
		}
		if ($bReturn) {
			return json_encode($aNewData);
		}
		else {
			echo json_encode($aNewData);
			exit();
		}
	}
}