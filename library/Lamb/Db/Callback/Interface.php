<?php
/**
 * Lamb Framework
 * @author РЎСт
 * @package Lamb_Db
 */
interface Lamb_Db_Callback_Interface
{
	/**
	 * Get the database object
	 *
	 * @return Lamb_Db_Abstract
	 */
	public function getDb();
}