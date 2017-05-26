<?php
/**
 * Lamb Framework
 * @author С��
 * @package Lamb_App_ErrorHandle
 */
interface Lamb_App_ErrorHandler_Interface
{
	/**
	 * @param Exception $e
	 * @return void
	 */
	public function handle(Exception $e);
}