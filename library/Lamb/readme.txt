Lamb Framework v1.0
2012-07-14 21:18
author:С��
description:
	�˿���Ǵ�Zend framework�õ�����������Zend�������ռ��ʽ����Namespace1_Namespace2_Classname�����ļ�Classname.php����
	Namespace1/Namespace2/Classname.php��ʽ��·����ŵġ�Lamb Framework�������ռ���LambΪ��ͷ��������Zend����Zend��ͷ
	Lamb Framework��һ�������͵�MVC��ܣ�����PDO���ݶ���汾�����ݿ⣬�Դ�ģ�����棬��ʵ���˻�����ǩ�Լ��Զ����ǩ��ģ���
	�����Դ��л��棬ʹ�ô˿���Ƽ���·���ṹ
	application
		--controllors
		--views
	library
		--Lamb
	public
		--css
		--html
		--~runtime
	Hello world:
	  -public/index.php
		//����Lamb framework·��λ��
		set_include_path('Lamb framework���ڵ�Ŀ¼���磺../library/'
			PATH_SEPARATOR . get_include_path);
		//��ȡ�������	
		require_once 'Lamb/Loader.php';
		//ʵ����������
		$loader = Lamb_Loader::getInstance();
		//��ʼ��App����
		$app = Lamb_App::getInstance();
		$app->setControllorPath('Controllor���ڵ�·�����磺../application/controllors/')
			->setViewPath('���Ҫ�õ�ģ�����������Ҫ���ô˺�����setViewRuntimePath���磺../application/views/')
			->setViewRuntimePath('����ģ���ļ������Ļ���·�����磺./~runtime/')
			//���Ҫʹ�����ݿ������������setSqlHelper����SQL��������setDbCallback��ȡDb����ʵ�����ݿ����
			->setSqlHelper('Lamb_Db_Sql_Helper_Abstract ��������������')
			->setDbCallback('Php�Ϸ��Ļص�����������ʵ��Lamb_Db_Callback_Interfaced�ӿڵĶ���')
			->run()//����;
		-application/controllors/indexControllor.php
		 class indexControllor extends Lamb_Controllor_Abstract //Ĭ�ϵ�controllor��index
		 {
		 	public function indexAction() //Ĭ�ϵ�action��index
			{
				echo 'hello world';
				//����ʹ��View��ģ������
				include $this->view->load('hello');
			}
		 }
		 -application/views/hello.html
		 <h1>Hello world</h1>
	�����Ǹ�����Ľ��ܣ�
	-Lamb_Loader
		����������󲿷���Zend_Loader_Autoloader��Zend_Loader��Ľ��
		_defaultInternalAutoload�����Ĭ�ϼ��������ü������ڲ�����getDefaultClassAutoloader��ȡ������class������
		Ĭ�ϵĸ���ľ�̬����loadClass����Ȼ��Ҳ�����ڵõ�Loader��������setDefaultClassAutoloader����Ĭ�ϵļ�����
		�÷�����ʵ�������ռ�ͬ�����»���_�ָ�ķ�ʽ��Ĭ��ֻ������Lamb_��ͷ���࣬���setFallbackAutoloader(true),
		��᳢�Լ��ز����������ռ���࣬����û�����Լ��������ռ�Ҳ����Ĭ�ϵļ�����������Ե���registerNamespaces
		ע�������ռ伴�ɣ�����û�����Լ��������ռ�����Զ��������������Ե���unsiftNamespacesAutoloaders����
		pushNamespaceAutoloaders��������Բ��������ռ�����Զ���ļ��������������2���������贫�˵ڶ�������
		���һ�������ռ�ע��ע��������������ֻҪ����һ��������������ɹ����򲻻��ٵ��������
	-Lamb_App 
		Ӧ�ó����࣬������Ӧ�ó�������࣬��Ӧ�ó����View,Dispatcher,Router,Request,Response,Db,SqlHelper���
		���й����ά����Lamb_App���õ���ģʽͨ������Lamb_App::getInstance()��ȡApp����
		App�ڹ����ʱ�������Ĭ�ϵ�View,Dispatcher,Router,Request,Response���󣬲�������ע�ᵽLamb_Registry��
		�У��Ա��ڳ�����κεط�������ͨ������Lamb_App::getGlobalApp()��ȡȫ��App����
		��Ȼ�û������ڵõ�App�����ͨ������SetView,SetDispatcher,SetRouter,SetReuqest,SetResponse�ȷ��������Լ������
		����û�Ҫʵ���Լ���App������̳�Lamb_App������Lamb_App�����乹�캯�����Զ�����ע�ᵽLamb_Registry����
		��Ҳ���Ե���Lamb_App::setGlobalApp()����Lamb_App::getGlobalApp()���ص���
		�ڵ���Lamb_App::getInstance()�õ�App����󣬱���Ҫ����Lamb_App::setControllorPath����Controllor���ڵ�·��
		���Ӧ�ó���Ҫʹ��View�����Ҫ����Lamb_App::setViewPath����ģ���ļ���·����Lamb_App::setViewRuntimePath����
		ģ����������ļ������·��
		���������Ҫʹ�õ����ݿ⣬������ڵõ�App����󣬵���Lamb_App::setSqlHelper��Lamb_App::setDbCallback
		����SQL�����࣬sqlHelper��Lamb_Db_Sql_Helper_Abstract����ÿ����ͬ�汾�����ݿ⹤�����ʵ�ֲ�һ������˽������
		setDbCallback�����û�ȡLamb_Db_Abstract����Ļص������������ڳ�������Ҫ�õ�Lamb_Db_Abstract����ʱͨ������
		Lamb_App::getDb��ȡ���ݿ����
		��Appʹ��Router,Dispatcher���ʱ�����������׳��쳣������Ե���Lamb_App::setErrorHandle���ô�����Щ�������
		Ĭ�Ͻ�ֱ���׳��쳣Lamb_App::setErrorHandle���õ���Lamb_App_ErrorHandle_Interfacesʵ�ֵ����ࡣ
	-Lamb_App_Router
		·���� �����ڳ�����κεط�����Lamb_App::getGlobalApp()->getRouter()�õ�ȫ��Router
		Ĭ�ϵĸ�ʽ��?s=controllor/action/val1/name1/var2/name2������Ὣ����·�ɵĲ������ɵ���injectRequest
		������ע�뵽Request�����У����Ե���setRouterParamName����·�ɲ�������Ĭ����s������setUrlDelimiter���ò����ָ���Ĭ����/
		parse����Ϊ����Ľ�����������·�ɵĲ���������url����������ת����·�ɸ�ʽ��·��
	-Lamb_App_Dispatcher
		�ַ��� �����ڳ�����κεط�����Lamb_App::getGlobalApp()->getDispatcher()�õ�ȫ��Router
		��Lamb_App_Router�����ȡ��Ϣ�����ö�Ӧ��controllor��ִ�иö�Ӧ��action������
		����Ҫ������controllor��·��������setControllorPath���ã���ȻҲ���Ե���Lamb_App::setControllorPath
		Ҫ�����е�controllor�඼Ҫ��controllor��β�����testControllor��·�ɲ���Ϊtest
		���е�action��Ҫ��action��β�����testAction��Ĭ�ϵ�controllor��indexControllor,Ĭ�ϵ�action
		��indexAction����Ȼ�û�Ҳ���Ե���setOrGetDefaultControllor��setOrGetDefaultAction����Ĭ�ϵ�controllor��action
		���⻹��������controllor��action�ı𣬵���setAlias��Ҳ����˵����·�ɲ���Ϊs=index/test
		�������indexControllorʵ���ǵ���indexAliasControllor,testActionʵ�ʵ�����testAliasAction
	-Lamb_App_Request
		Http������ �����ڳ�����κεط�����Lamb_App::getGlobalApp()->getRequest()�õ�ȫ��Request
		�󲿷��Ǹ���Zend�е�Request��д�ģ�Request��ʵ����__get��__isset��������˻�ȡ$_userParams,$_GET,$_POST,$_COOKIE,$_SERVER,$_ENV
		��ֵ���������������һ����ȡ����ȡֵ���Ⱥ�˳����ǰ���������˳��
		Request����һ��UserParams���ϣ��ü�����Ҫ�������Router�������·�ɲ������Զ���URIʱ�����Ĳ������磺s=index/test/v1/n1/v2/n2��
		����Router�����󽫻��v1=>n1,v2=>n2�����ļ�ֵ�Ա��浽Request��UserParams������.
		����ؼ�����setRequestUri�������û�������������Ҫ������URI��ַ����������ˣ�������parse_url������������������Ĳ���ע�뵽UserParams����
		�У����Ĭ�ϲ��������򽫲����κ����飬ֱ������PHPԭ�е�GET,POST�ȼ���
	-Lamb_App_Response
		Http��Ӧ�࣬�����ڳ�����κεط�����Lamb_App::getGlobalApp()->getResponse()�õ�ȫ��Response
		����Ƚϼ򵥣����ǰ�setCookie,setHeader,redirect�ȷ�����װ����
	-Lamb_View
		��ͼ�࣬�����ڳ�����κεط�����Lamb_App::getGlobalApp()->getView()�õ�ȫ��View
		����������Ǹ������ģ���ļ����������2�����͵ı�ǩ��
		��һ���ǻ�����ǩ��������ǩֻʵ��2�֣�1���Ǳ�����ǩ�����ʽ��{$var},{$arrvar[index]}2����layout��ǩ�����Ǽ��ز���������
			ģ���ļ������ʽ{layout template}��
			��չ�����ౣ����PHP��ǩ�����ã�ͬʱ�û�Ҳ���Զ����Լ��Ļ�����ǩ���䲽���ǣ�1.�̳�Lamb_View�� 2�����ø����setBaseTagParseMap
			������ע�������ǩ������������ʽ��ע����ø÷��������ĵ�һ������$key��������һ��Ҫʵ��parse_basetag_$key������Viewƥ�䵽��
			������ǩ���ַ������������Ӧ��parse_basetag_$key����������˱�ǩ
		�ڶ������Զ����ǩ��
			�Զ����ǩ�ĸ�ʽ{tag:�ñ�ǩ��������ȫ�����������ռ��磺Lamb_View_Tag_List[������]}do something{/tag:Lamb_View_Tag_List}
			�Զ����ǩҪʵ��Lamb_View_Tag_Interface�ӿڻ��߼̳�Lamb_View_Tag_Abstractc������
			Lamb framework�Ѿ�Ĭ��ʵ����Lamb_View_Tag_List�б��ǩ Lamb_View_Tag_Page��ǩ����2����ǩʵ���˾��󲿷ַ�ҳ�Լ��б��ǩ
			���ҿ������û��棬����ɲμ�Lamb_View_Tag_List��Lamb_View_Tag_Page�ĵ�
	-Lamb_Db_Abstract
		���ݿ���������࣬����̳�PDO�������Ԥ�����ѯ�Ѿ���ȡ��¼�������������Ż�����ʵ�ʵ�Ӧ���У��û�������ݲ�ͬ�����ݿ�����
		�̳в�ʵ�ָ���δʵ�ֵĳ��󷽷�����Lamb framework�У�ֻ��Lamb_Db_Abstract����������ʵ�ֵķ�����
		������ڵõ�App����ʱ������setDbCallback���û�ȡ������Ķ���ص���������������Ϳ������κεط�����
		Lamb_App::getGlobalApp()->getDb()��ȡ���ݲ����������Ϊ����Dbcallback�����ߵ��ûص�������getDb�������׳��쳣
		��ԭʼ��PDO����PDO::query����������һ��PDOStatement���󣬵�Lamb framework�涨���ڵõ�db�����Ժ�һ�ɵ���
		PDO::setAttribute(PDO::ATTR_STATEMENT_CLASS,array('Lamb_Db_RecordSet', array($objInstance)))����������PDO::query
		���صĶ�����Lamb_Db_RecordSet�����������Ķ���
		ĿǰLamb frameworkֻʵ������Lamb_Mssql_Db
	-Lamb_Db_RecordSet
		��¼�����󣬸ö�����ֱ��ʵ������ֻ����Lamb_Db_Abstract::query����prepare�������ظö���
		����̳���PDOStatement�࣬��ʵ����Lamb_Db_RecordSet_Interface�ӿ��еķ����������Ż���
		rowCount�����������Ƕ��ڲ�ѯ�ļ�¼���Ƚϴ��ʱ��ͨ������getRowCount������ȡ��¼��������
		�����и�����֮�������Ƕ�����union�ؼ��ֵ�SQL����޷�100%�����жϣ��������ʱ���û���Ҫ����
		setHasUnion�������ø�SQL����Ƿ���Union�ؼ���
		����û���Ҫʵ���Լ���RecordSet������ʵ��Lamb_Db_RecordSet_CustomInterface�ӿ�����������Դ����ֱ��
		�̳�Lamb_Db_RecordSet��
	-Lamb_Db_Sql_Helper_Abstract
		SQL���߳����࣬��SQL�е�Select�������Ĺ����࣬���ڲ�ͬ�����ݿ�������ܺ��в�ͬ�����ݿ��﷨����˴���Ϊ
		�����࣬�û���ʵ�ʵ�Ӧ���У�����̳д��࣬ʵ����δʵ�ֵķ��������ڵõ�App����󣬵���Lamb_App::setSqlHelper
		�����Զ���SQL�����࣬����������κεط�����Lamb_App::getGlobalApp()->getSqlHelper()�õ�sqlHelper����
		Lamb frameworkĬ��ֻʵ����Lamb_Mssql_Sql_Helper����
	-Lamb_Db_Table
		���ݿ���࣬��װ�˻��������ݿ�Ĳ�ѯ���޸ģ�������䣬����ʹ�ô������SQL�Ĳ�ѯ���޸ģ�����
	-Lamb_Db_Select
		���ݿ��ѯ�࣬�����װ�����ݿ�Ĳ������������ͨ��ѯ���������ѯ����ҳ��ѯ��Ԥ�����ѯ��Ԥ�����ҳ��ѯ
	-Lamb_Cache_File
		�ļ�����
	-Lamb_Cache_Memcached
		Memcached������
	-Lamb_Registry
		ע��ȫ���࣬ͨ������Lamb_Registry::set()������Ȼ���ڳ�����κεط������Ե���Lamb_Registry::get()�õ�
		�Ƚϼ򵥵�һ����
	-Lamb_Upload
		�ϴ��࣬����ʵ��һ�������ļ��ϴ������������ϴ��ļ�����չ������С��
	-Lamb_Utils
		�����࣬�Գ��õ�һЩ�������жϣ����ݵļ��ܵ�
	-Lamb_Debuger
		������