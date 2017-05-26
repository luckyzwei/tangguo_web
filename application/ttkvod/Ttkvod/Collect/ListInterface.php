<?php
/**
 * $array = collect($url)
 * $array = array(
 *		'url' => string,
 *		'datetime' => int,
 *		'externls' => mixed 
 *	)
 */
interface Ttkvod_Collect_ListInterface extends Ttkvod_Collect_Interface
{
	/**
	 * @param int $page
	 * @return string
	 */
	public function getUrl($page);
}