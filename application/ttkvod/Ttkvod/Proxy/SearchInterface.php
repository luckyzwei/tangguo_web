<?php
interface Ttkvod_Proxy_SearchInterface extends Ttkvod_Proxy_Interface
{
	/**
	 * @param string $keywords
	 * @param int $page
	 * @return string
	 */
	public function getUrl($keywords, $page);
}