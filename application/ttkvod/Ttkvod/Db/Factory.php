<?php
class Ttkvod_Db_Factory
{
	const	T_FULL		=	1;
	const	T_SIMPLE	=	2;
	const	T_DEFAULT	=	4;
	
	/**
	 * @var array
	 */
	protected static $s_aSingleInstances	=	array(
			self::T_FULL	=>	null,
			self::T_SIMPLE	=>	null,
			self::T_DEFAULT	=>	null
	);
	
	/**
	 * @param array $cfg
	 * @return Lamb_Mssql_Db
	 */
	public static function fullInstance($cfg)
	{
		try{
			$objInstance	=	new Lamb_Mssql_Db($cfg['dsn'], $cfg['username'], $cfg['password'], array(
										PDO::SQLSRV_ATTR_ENCODING => PDO::SQLSRV_ENCODING_SYSTEM,
										PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_NAMED
									));
			$objInstance->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('Lamb_Db_RecordSet', array($objInstance)));
		}catch (Exception $e){
			die('Connect database error');
		}
		return $objInstance;
	}
	
	/**
	 * @param array $cfg
	 * @return Lamb_Mssql_Db
	 */
	public static function simpleInstance($cfg)
	{
		try{
			$objInstance	=	new Lamb_Mssql_Db($cfg['dsn'], $cfg['username'], $cfg['password'], array(
										PDO::SQLSRV_ATTR_ENCODING => PDO::SQLSRV_ENCODING_SYSTEM
									));
			$objInstance->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('Lamb_Db_RecordSet', array($objInstance)));
		}catch(Exception $e){
			die('Connect database error');
		}
		return $objInstance;	
	}
	
	/**
	 * @param array $cfg
	 * @return Lamb_Mssql_Db
	 */
	public static function defaultInstance($cfg)
	{
		try{
			$objInstance	=	new Lamb_Mssql_Db($cfg['dsn'], $cfg['username'], $cfg['password']);
		}catch(Exception $e){
			die('Connect database error');
		}
		return $objInstance;
	}
	
	/**
	 * @param int $nType
	 * @return Lamb_Mssql_Db
	 */
	public static function singleInstance($nType = self::T_FULL)
	{
		if(array_key_exists($nType, self::$s_aSingleInstances)) {
			if(!($objInstance = self::$s_aSingleInstances[$nType])) {
				$cfg = Lamb_Registry::get(CONFIG);
				switch($nType) {
					case self::T_FULL:
						$objInstance	=	self::fullInstance($cfg['db_cfg']);
						break;
					case self::T_SIMPLE:
						$objInstance	=	self::simpleInstance($cfg['db_cfg']);
						break;
					case self::T_DEFAULT:
						$objInstance	=	self::defaultInstance($cfg['db_cfg']);
						break;
				}
				self::$s_aSingleInstances[$nType]	=	$objInstance;
			}
			return $objInstance;
		}
		return null;
	}	
}