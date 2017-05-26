<?php
class  testControllor extends Ttkvod_Controllor
{

	public function getControllorName()
	{
		return 'test';
	}
/*
	public function indexAction()
	{
		$page = trim($this->mRequest->p);
		if (!Lamb_Utils::isInt($page, true)) {
			$page = 1;
		}
		
		$db = $this->mApp->getDb();
		$path = ROOT . 'emails/email_' . $page . '.txt';
		$sql = "select email from member order by registerTime";
		$num = $db->getRowCount('select count(uid) as num from member');
		$pagesize = 50000;
		$pages = ceil($num / $pagesize);
		
		
		if ($page > $pages) {
			exit('task over');
		}
		$sql = $this->mApp->getSqlHelper()->getPageSql($sql, $pagesize, $page);
		
		$data = $db->query($sql)->toArray();
		$email = array();
		
		foreach ($data as $item) {
			$email[] = $item['email'];
		}
		$email = implode("\r\n", $email);
		file_put_contents($path, $email);

		$html = "当前第<b>{$page}</b>页，生成完毕，2秒后将进入下一页";
		$nexturl = '/?s=test/index/p/' . ($page + 1);
		$html .= "<script>setTimeout(function(){location.href='{$nexturl}'}, 2000)</script>";
		$this->mResponse->eecho($html);
	}*/
	
	public function testAction()
	{
		$db = $this->getApiDb();
		
		$ret = $db->query('select * from member where id < 10')->toArray();
		
		foreach ($ret as $i => $it) {
			$ret[$i]['username'] = iconv("UTF-8", "GBK//IGNORE", $it['username']);
		}

		Lamb_Debuger::debug($ret);
	}
	
	public function getApiDb()
	{
		$dsn = 'sqlsrv:Database=ttk_user_api;Server=121.40.218.51,1543;MultipleActiveResultSets=true;LoginTimeout=10;TransactionIsolation=' . PDO::SQLSRV_TXN_READ_UNCOMMITTED;
		$username = 'ttk_user_api';
		$password = 's_a_d_m_y_s_9185~';
				
		try{
			$objInstance	=	new Lamb_Mssql_Db($dsn, $username, $password, array(
										PDO::SQLSRV_ATTR_ENCODING => PDO::SQLSRV_ENCODING_SYSTEM,
										PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_NAMED
									));
			$objInstance->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('Lamb_Db_RecordSet', array($objInstance)));
		}catch (Exception $e){
			echo '123';
			die('Connect database error');
		}
		return $objInstance;
	}
	
}
?>