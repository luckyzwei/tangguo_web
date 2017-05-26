<?php
/**
 * Lamb Framework
 * Lamb_Db_RecordSet_Interfaceֻ�������Ѿ�ʵ����Traversable�ӿڵ�����
 * һ���Զ���������޷�ʵ��Traversable�ӿڣ�ֻ��PHP�ڲ������
 * 
 * @author С��
 * @package Lamb_Db_RecordSet
 */
interface Lamb_Db_RecordSet_Interface extends Traversable,Countable
{
	/**
	 * ��ȡ��ǰ��¼������������ǰҳ������
	 *
	 * @return int
	 */
	public function getRowCount();
	
	/**
	 * ��ȡ����Դ�е���Ŀ
	 *
	 * @return int
	 */
	public function getColumnCount();
	
	/**
	 * ������Դת��������
	 *
	 * @return array
	 */
	public function toArray();
}