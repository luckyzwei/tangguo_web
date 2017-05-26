<?php 
class Ttkvod_Model_Admin 
{
	 public $mDb = null;

	 public function __construct()
	 {
	 	$this->mDb = Lamb_App::getGlobalApp()->getDb();
	 }	
	 /*
	 *	@param array $data[name] string
	 *		   array $data[password] string
	 *	return bool
	 */
	 public function add(array $data)
	 {
	 	if (!isset($data['name'], $data['password'])) {
			return false;
		}
		$table = new Lamb_Db_Table('admin', Lamb_Db_Table::INSERT_MODE);
		return $table->set($data)->execute();
	 }
	 
	 /*
	 *	@param array $data[name] string
	 *		   array $data[password] string
	 *	return bool || data
	 */
	 public function login(array $data, $isReturnData = true)
	 {
	 	if (!isset($data['name'], $data['password'])) {
			return false;
		}
		$sql = 'select isAdmin, id from admin where name = :name and password = :password';
		$prepareSource = array(
			':name' => array($data['name'], PDO::PARAM_STR),
			':password' => array($data['password'], PDO::PARAM_STR)
		);
		$res = $this->mDb->getNumDataPrepare($sql, $prepareSource, true);
		
		if (!$res['data']) {
			return false;
		}
		
		return $isReturnData ? $res['data'] : true;
	 }
	 
	 public function update(array $data, $isUpdateName = false)
	 {
	 	$table = new Lamb_Db_Table('admin' ,Lamb_Db_Table::UPDATE_PREPARE_MODE);
		$sql = '';
		foreach (array('id', 'name') as $val) {
			if (isset($data[$val])) {
				$sql .= $val . ' = :' . $val ;
				$prepareSource[':' . $val] = array($data[$val], $val == 'id' ? PDO::PARAM_INT : PDO::PARAM_STR);
				break;
			}
		}
		unset($data['id']);
		if (!$isUpdateName) {
			unset($data['name']);
		}
		return $table->setOrGetDb($this->mDb)->set($data)->setOrGetWhere($sql)->execute($prepareSource);
	 }
	 
	 public function get(array $data,  $column = '*')
	 {
		$sql = "select {$column} from admin where ";
		foreach (array('id', 'name') as $val) {
			if (isset($data[$val])) {
				$sql .= $val . ' = :' . $val ;
				$prepareSource[':' . $val] = array($data[$val], $val == 'id' ? PDO::PARAM_INT : PDO::PARAM_STR);
				break;
			}
		}
		$res = $this->mDb->getNumDataPrepare($sql, $prepareSource, true);
		return $res['data'];
	 }
	 
	 /*
	 *	@param array $data[name] string || $data[id] int
	 *		   
	 *	return int 1 || 0
	 */
	 public function delete(array $data)
	 {
	 	$sql = 'delete admin where ';
		$prepareSource = array();
		foreach(array('id', 'name') as $val) {
			if (isset($data[$val])) {
				$sql .= $val . ' = :' . $val ;
				$prepareSource[':' . $val] = array($data[$val], $val == 'id' ? PDO::PARAM_INT : PDO::PARAM_STR);
				break;
			}
		}
		return $this->mDb->getPrepareRowCount($sql, $prepareSource, true);
	 }
}
?>