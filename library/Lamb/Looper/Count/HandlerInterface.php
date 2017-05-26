<?php
/**
 * Lamb Framework
 * @author РЎСт
 * @package Lamb_Looper_Count
 */
interface Lamb_Looper_Count_HandlerInterface
{
	/**
	 * @param int $currentCount
	 * @param mixed $external
	 * @return boolean - true looper continue - false looper break
	 */
	public function handle($currentCount, $external = null);
}