<?php
class Ttkvod_OutServices_Method
{
	public function create($param, &$errorCode = Ttkvod_OutServices_Server::S_OK)
	{
		$errorCode = Ttkvod_OutServices_Server::S_OK;
		return print_r($param, true);
	}
	
	public function clearNoticeCache($param, &$errorCode = Ttkvod_OutServices_Server::S_OK)
	{
		$errorCode = Ttkvod_OutServices_Server::E_METHOD_PARAMS_ERROR;
		$video = new Ttkvod_Model_Video;
		if (isset($param['ids'])) {
			$ids = explode(',', $param['ids']);
			foreach ($ids as $id) {
				if (Lamb_Utils::isInt($id, true)) {
					Ttkvod_Model_Notice::clearCacheByVid($id);
					//Lamb_IO_File::putContents(ROOT . 'sys.txt', $id, FILE_APPEND);
					if ($cache = $video->getPlayDataCache($id)) {
						$cache->flush();
					}
				}
			}
			$errorCode = Ttkvod_OutServices_Server::S_OK;
		} 
	}
}