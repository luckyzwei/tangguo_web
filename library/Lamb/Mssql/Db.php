<?php
/**
 * Lamb Framework
 * @author С��
 * @package Lamb_Mssql
 */
class Lamb_Mssql_Db extends Lamb_Db_Abstract
{
	/**
	 * Lamb_Db_Abstract 
	 * @override
	 */
	public function end()
	{
		return $this->errorCode() == 0x0 ? $this->commit() : $this->rollBack();
	}
	
	/**
	 * @override
	 */
	public function getRowCountEx($strSql, $bIncludeUnion=false, $bRetSql = false)
	{
		$nRowNum		=	-1;
		$strTempSql		=	$strSql;
		$strField		=	'';
		if(preg_match('/^select\s+top\s+(\d+)/is',$strSql,$aMatchs)){
			return $aMatchs[1];
		}

		$strSql 	=	preg_replace('/[^(]order[^()]*/is','',$strSql);
		if($bIncludeUnion==false){
			if($strField = Lamb_App::getGlobalApp()->getSqlHelper()->getSqlField($strSql)){
				$strSql = preg_replace('/'.preg_quote($strField).'/s',' count(*) as num ',$strSql,1);
			}
		}else{
			$strSql		=	"select count(*) as num from ($strSql) __TEMP__";
		}
		if ($bRetSql) {
			return $strSql;
		}
		if(($nRowNum = $this->getRowCount($strSql)) == -1 ){
			$nRowNum	=	$this->getRowCountDynamic($strSql);
		}
		return $nRowNum;
	}	
}