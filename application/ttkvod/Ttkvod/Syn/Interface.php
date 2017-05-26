<?php
interface Ttkvod_Syn_Interface
{
	/**
	 * @param string $identity
	 * @param string $strRemoteName 
	 * @param string $path
	 * @return boolean
	 */
	public function write($identity, $strRemoteName = null, $path = null)
}