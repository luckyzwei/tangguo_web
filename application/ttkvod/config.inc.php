<?php
require_once 'config.const.php';
return array (
	'controllor_path' => APP_PATH . 'controllors/',
	'manage_controllor_path' => APP_PATH . 'manage/controllors/',
	'view_path' => APP_PATH . 'views/',
	'manage_view_path' => APP_PATH . 'manage/views/',
	'view_runtime_path' => DATA_PATH . '/~runtime/',
	'theme_path' => ROOT . 'themes/',
	'template' => 'flat',
	'scws_rule_path' => ROOT . '../scws/rules.ini',
	'scws_dict_path' => ROOT . '../scws/dict.xdb',
	//'encode_key' => 'tl)~t@y|m(^kj#lb%`%t$t^h*n(i)o%5',
	'encode_key' => '$-_-!^_^%#(&^@_|_@+_@)^&',
	'form_rank_key' => 'm3n93adb04$fd12!',
	'form_rank_expire' => 180,
	'week_lock_path' => DATA_PATH . '/week.lock',
	'item_text_ad_path' => DATA_PATH . '/item_text_ad.txt',
	'out_services_hash' => array(
		'ttkcoll' => array(
					'key' => 'm2c9ja710n4h6b81ap4sd3q',
					'expire' => 600
				),
		'ttkitem' => array(
					'key' => 'lamb2003cachettkvod12fa',
					'expire' => 600,
					'id' => 'ttkitem'
				)
	),
	'db_cfg' => array (
				'dsn' => 'sqlsrv:Database=tangguo_web;Server=182.16.63.114;MultipleActiveResultSets=true;LoginTimeout=10;TransactionIsolation=' . PDO::SQLSRV_TXN_READ_UNCOMMITTED,
				'username' => 'tangguo_web',
				'password' => 'tangguo@!21919799$'
			),
	'cache_cfg' => array (
				'timeout' => 3600, //<=0则关闭缓存
				'type' => Ttkvod_Cache_Factory::CACHE_MEMCACHED | Ttkvod_Cache_Factory::CACHE_HTML_FILE,//1-文件缓存 2-Memcached缓存
				'db_path' => CACHE_PATH . 'db/',
				'html_path' => CACHE_PATH . 'html/',
				'local_path' => CACHE_PATH . 'local/',
				'comm_path' => CACHE_PATH . 'comm/',
				'file_extendtion' => '.txt',
				'mem_host' => 'localhost',
				'mem_port' => 11211,
				'mem_pconnect' => true,
				'mem_connect_timeout' => 10,
				'cache_suffix' => '_index'
			),
	'channels' => array(
		1 => array(
				'name' => '电影',
				'item_template' => 'vodshow'
			),
		2 => array(
				'name' => '电视剧',
				'item_template' => 'vodshow'
			),
		3 => array(
				'name' => '动漫',
				'item_template' => 'vodshow'
			),
		4 => array(
				'name' => '综艺',
				'item_template' => 'vodshow'
				
			)
	),		
	'member_notice_num' => 30,
	'member_net_playlist_num' => 30,
	'admin_super_username' => '',
	'admin_allow_ips' => array(
		
	),
	'img_url_group' => array(
		
	),
	'cdn_host' => 'www.tangguoyy.com',
	'admin' => array(
		'username' => 'jude',
		'password' => '8b3a88a36e12b8c097cf70595593ae29'
	),
) + require(DATA_PATH . 'config.var.php');