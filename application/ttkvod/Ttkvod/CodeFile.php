<?php
class Ttkvod_CodeFile
{
 //��֤��λ��
 private $mCheckCodeNum  = 4;

 //��������֤��
 private $mCheckCode   = '';
 
 //��֤���ͼƬ
 private $mCheckImage  = '';

 //��������
 private $mDisturbColor  = '';

 //��֤���ͼƬ���
 private $mCheckImageWidth = '80';

 //��֤���ͼƬ���
 private $mCheckImageHeight  = '20';
 
 public $mSessionVarName = 'randval';

 /**
 *
 * @brief  ���ͷ
 *
 */
 private function OutFileHeader()
 {
  header ("Content-type: image/png");
 header ("Cache-Control: no-cache");
 }

 /**
 *
 * @brief  ������֤��
 *
 */
 private function CreateCheckCode()
 {
  $this->mCheckCode = strtoupper(substr(md5(rand()),0,$this->mCheckCodeNum)); 
  //session_save_path(dirname(__FILE__)."/session");
  @session_start(); 
  $randval=$this->mCheckCode;
  $_SESSION[$this->mSessionVarName]=$this->mCheckCode;
  return $this->mCheckCode;
 }

 /**
 *
 * @brief  ������֤��ͼƬ
 *
 */
 private function CreateImage()
 {
  $this->mCheckImage =@imagecreate ($this->mCheckImageWidth,$this->mCheckImageHeight);
  imagecolorallocate ($this->mCheckImage, 200, 200, 200);
  return $this->mCheckImage;
 }

 /**
 *
 * @brief  ����ͼƬ�ĸ�������
 *
 */
 private function SetDisturbColor()
 {
  for ($i=0;$i<=128;$i++)
  {
   $this->mDisturbColor = imagecolorallocate ($this->mCheckImage, rand(0,255), rand(0,255), rand(0,255));
   imagesetpixel($this->mCheckImage,rand(2,128),rand(2,38),$this->mDisturbColor);
  }
 }

 /**
 *
 * @brief  ������֤��ͼƬ�Ĵ�С
 *
 * @param  $width  ��
 *
 * @param  $height �� 
 *
 */
 public function SetCheckImageWH($width,$height)
 {
  if($width==''||$height=='')return false;
  $this->mCheckImageWidth  = $width;
  $this->mCheckImageHeight = $height;
  return true;
 }

 /**
 *
 * @brief  ����֤��ͼƬ�����������֤��
 *
 */
 private function WriteCheckCodeToImage()
 {
  for ($i=0;$i<$this->mCheckCodeNum;$i++)
  {
   $bg_color = imagecolorallocate ($this->mCheckImage, rand(0,255), rand(0,128), rand(0,255));
   $x = floor($this->mCheckImageWidth/$this->mCheckCodeNum)*$i;
   $y = rand(0,$this->mCheckImageHeight-15);
   imagechar ($this->mCheckImage, 5, $x, $y, $this->mCheckCode[$i], $bg_color);
  }
 }

 /**
 *
 * @brief  �����֤��ͼƬ
 *
 */
 public function OutCheckImage()
 {
  $this ->OutFileHeader();
  $this ->CreateCheckCode();
  $this ->CreateImage();
  $this ->SetDisturbColor();
  $this ->WriteCheckCodeToImage();
  imagepng($this->mCheckImage);
  imagedestroy($this->mCheckImage);
 }
}