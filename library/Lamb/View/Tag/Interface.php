<?php
/**
 * Lamb Framework
 * @author С��
 * @package Lamb_View_Tag
 */
interface Lamb_View_Tag_Interface
{
	/**
	 * @param string $content ��ǩ���ǩ������֮�������
	 * @param string $property ��ǩ������
	 * @return string
	 */
	public function parse($content, $property);
}