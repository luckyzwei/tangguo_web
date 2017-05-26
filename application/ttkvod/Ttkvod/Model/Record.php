<?php 
class Ttkvod_Model_Record
{
	public $mDb = null;

	 public function __construct()
	 {
	 	$this->mDb = Lamb_App::getGlobalApp()->getDb();
	 }	
	 
	 public function add(array $data)
	 {
	 	$table = new Lamb_Db_Table('record', Lamb_Db_Table::INSERT_MODE);
		return $table->set($data)->execute();
	 }
	 
	 public function get($id, $column = '*')
	 {
	 	if (!Lamb_Utils::isInt($id, true)) {
			return false;
		}
		$sql = "select {$column} from record where id = :id";
		$res = $this->mDb->getNumDataPrepare($sql, array(':id' => array($id, PDO::PARAM_INT)), true);
		return $res['data'];
	 }
	 
	 public function delete($id)
	 {
	 	if (!Lamb_Utils::isInt($id, true)) {
			return false;
		}
		return $this->mDb->getPrepareRowCount('delete record where id = :id', array(':id' => array($id, PDO::PARAM_INT)), true);
	 }
	 
	 public function update(array $data)
	 {
	 	if (!Lamb_Utils::isInt($data['id'], true)) {
			return false;
		}
		$sql = 'id = :id';
		$id = $data['id'];
		unset($data['id']);
		$table = new Lamb_Db_Table('record' ,Lamb_Db_Table::UPDATE_PREPARE_MODE);
		return $table->setOrGetDb($this->mDb)->set($data)->setOrGetWhere($sql)->execute(array(':id' => array($id, PDO::PARAM_INT)));
	 }
}
?>