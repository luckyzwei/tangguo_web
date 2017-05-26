<?php
abstract class Ttkvod_Proxy_Abstract
{
	public $mIsDebuger = false;
	
	public function debug($str, $filename = 'debug.txt')
	{
		$path = DATA_PATH . 'log/';
		if ($this->mIsDebuger) {
			echo $str;return;
			if (strpos($filename, '.txt') === false) {
				$filename .= '.txt';
			}
			$path .= $filename;
			file_put_contents($path, $str . "\r\n", FILE_APPEND);
		}
	}
}