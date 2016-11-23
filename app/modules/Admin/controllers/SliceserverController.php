<?php
namespace Controllers;

//use Phalcon\Mvc\Controller;
use \Models\Sliceserver as Slicemodel;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Phalcon\Paginator\Adapter\QueryBuilder as PaginatorQueryBuilder;
use \Models\Admin;
use Phalcon\Config;
use \Models\Monitorsolution as Monitorsolution;
use \Models\Monitor as Monitor;

class SliceserverController extends ControllerBase{

	public function initialize(){
		parent::initialize();
		$configs = new Config(require(dirname(__DIR__) . '/../../config/config.php'));
		$this->serverowner =get_object_vars($configs->serverowner);
		//$this->serverowner = array('1'=>'滨州','2'=>'泰国','3'=>'QA环境','4'=>'ceshitest');
		$this->view->setVar('activeinfo','sliceserver');
	}

	public function indexAction(){
		$numberPage = 1;
        if ($this->request->isPost()) {
			if($_POST['serverstatus'] == 'all'){unset($_POST['serverstatus']);}
			if($_POST['msid'] == '0'){unset($_POST['msid']);}
            $query = Criteria::fromInput($this->di, "Models\Sliceserver", $_POST);
            $this->persistent->parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }
        if ($numberPage == null) {
            $this->persistent->parameters = null;
        }

        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = array();
        }
		//名称
        $ssname = "";
        if ($this->request->getPost("ssname")) {
            $ssname = $this->request->getPost("ssname");
			if(isset($parameters["conditions"])){
            	$parameters["conditions"] .= " and  ssname like '%".$ssname."%'   ";
			}else{
				$parameters["conditions"] .= " ssname like '%".$ssname."%'   ";
			}
        }
        $this->view->setVar('ssname',$ssname);

		//状态
		$serverstatus = '';
		if($this->request->getPost("serverstatus") || $this->request->getPost("serverstatus") == '0'){
			$serverstatus = $this->request->getPost("serverstatus");
			if(isset($parameters["conditions"])){
				$parameters["conditions"] .= ' and serverstatus='.$serverstatus;
			}else{
				$parameters["conditions"] = ' serverstatus='.$serverstatus;
			}
		}
		$this->view->setVar('serverstatus',$serverstatus);

		//监控方案
		$msid = '';
		if($this->request->getPost("msid")){
			$msid = $this->request->getPost("msid");
			if(isset($parameters["conditions"])){
				$parameters["conditions"] .= ' and msid = '.$msid;
			}else{
				$parameters["conditions"] = ' msid = '.$msid;
			}
		}
		$this->view->setVar('msid',$msid);

        if (isset($parameters["conditions"])) {
            $builder = $this->modelsManager->createBuilder()
                ->columns('*')
                ->from('Models\Sliceserver')
                ->where($parameters['conditions'], $parameters['bind'])
                ->orderBy('ssid desc');
        } else {
            $builder = $this->modelsManager->createBuilder()
                ->columns('*')
                ->from('Models\Sliceserver')
                ->orderBy('ssid desc');
        }

        $paginator = new PaginatorQueryBuilder(
            array(
                "builder" => $builder,
                "limit"   => 10,
                "page"    => $numberPage
            )
        );

		//获取监控方案
		$this->monitorsolution = new \Models\Monitorsolution;
		$monitorsolution = $this->monitorsolution->find(['status=1']);
		$this->view->setVar("searchmonitorsolution", $monitorsolution);
		$middle = array();
		foreach($monitorsolution as $s){
			$middle[$s->msid] = $s;
		}
		$monitorsolution = $middle;

		/*$curpage = 1;
		if($numberPage = $this->request->getQuery('page','int')){
			$curpage = $numberPage;
		}
		$ssname = '';
		$where = 1;
		if($this->request->isPost()){
			$ssname = $this->request->getPost('ssname');
			$where = ' ssname LIKE "%'.$ssname.'%"';
		}
		$this->view->setVar('ssname',$ssname);
		//$data = Slicemodel::find(["$where"]);
		if ($where !=1) {
            $builder = $this->modelsManager->createBuilder()
                ->columns('*')
                ->from('Models\Monitor')
                ->where($parameters['conditions'], $parameters['bind'])
                ->orderBy('mcid');
        } else {
            $builder = $this->modelsManager->createBuilder()
                ->columns('*')
                ->from('Models\Monitor')
                ->orderBy('mcid');
        }

        $paginator = new PaginatorQueryBuilder(
            array(
                "builder" => $builder,
                "limit"   => 10,
                "page"    => $numberPage
            )
        );
		/*$paginator = new PaginatorQueryBuilder(
            array(
                "builder" => $data,
                "limit"   => 1,
                "page"    => $curpage
            )
        );
		$paginator = new Paginator(
			array(
				'data'=>$data,
				'limit'=>10,
				'page'=>$curpage
				)
			);*/
        $this->view->page = $paginator->getPaginate();
		$this->view->setVar('serverowner',$this->serverowner);
		$this->view->setVar("monitorsolution", $monitorsolution);

	}
	//添加
	public function addAction(){
		$sliceserver = Monitorsolution::find(['status=1']);
		$msid= $this->tag->select(
				[
						"msid",
						$sliceserver,
						"using" => [
								"msid",
								"name",
						],
						"useEmpty" => true,
						"emptyText"  => "请选择",
						"emptyValue" => "",
						"required" => "",
				]
		);
		$this->view->setVar("msid", $msid);
		$this->view->setVar('serverowner',$this->serverowner);
	}

	public function createAction(){
		if($this->request->isPost()){
			$post_data = $this->request->getPost();

			//处理归属地
			if($post_data['serverowner']){
				$post_data['serverowner'] = $this->serverowner[$post_data['serverowner']];
			}else{
				$post_data['serverowner'] = '';
			}
			$post_data['createtime'] =$post_data['updatetime'] = time();
			if($this->session->has('auth')){
				$auth = $this->session->get('auth');
				$post_data['creator'] = $auth['id'];
			}else{
				$post_data['creator'] = '';
			}
			if(!$post_data['maxnum']){
				$post_data['maxnum'] = 1;
			}
			$post_data['serverstatus'] = 1;
			$Slicemodel  = new Slicemodel;
			$success = $Slicemodel->save($post_data);
			if(!$success){
				foreach ($Slicemodel->getMessages() as $message) {
	                $this->flash->error($message);
	            }
				return $this->dispatcher->forward(array(
					"controller" => "sliceserver",
					"action" => "add"
				));
			}
			$this::logAction('添加切片服务器', "添加了切片服务器:$post_data[ssname]");
			$this->flash->success("添加成功");        
        	$this->response->redirect("sliceserver/index");
			
		}
	}

	public function editAction($id)
	{

		if (!$this->request->isPost()) {
			$info = Slicemodel::findFirstByssid($id);
			if (!$info) {
				$this->flash->error("没有找到该数据");
				return $this->dispatcher->forward(array(
						"controller" => "sliceserver",
						"action" => "index"
				));
			}

			$this->view->setVar('ssid',$id);
			$this->tag->setDefault('ssid',$id);
			$this->tag->setDefault('ssname',$info->ssname);
			$this->tag->setDefault('serverip',$info->serverip);
			$this->tag->setDefault('maxnum',$info->maxnum);
			$this->tag->setDefault('apiurl',$info->apiurl);
			$this->tag->setDefault('serverstatus',$info->serverstatus);
			$this->tag->setDefault('remark',$info->remark);
			$this->tag->setDefault("msid", $info->msid);

			$this->tag->setDefault("serverowner", "$info->serverowner");
			$selectarr=   [
					"serverowner",
					$this->serverowner,
			];
			$sliceserver = Monitorsolution::find(['status=1']);
			$msid = $this->tag->selectStatic(
					[
							"msid",
							$sliceserver,
							"using" => [
									"msid",
									"name",
							],

					]
			);
			$this->view->setVar("msid", $msid);

			$typeid= $this->tag->selectStatic($selectarr);
			$this->view->setVar("serverowner", $typeid);
		}
	}

	public function saveAction()
	{
		if (!$this->request->isPost()) {
			return $this->dispatcher->forward(array(
					"controller" => "sliceserver",
					"action" => "index"
			));
		}

		$selectarr=   [
				"serverowner",
				$this->serverowner,
		];
		$typeid= $this->tag->selectStatic($selectarr);
		$this->view->setVar("serverowner", $typeid);
		$id = $this->request->getPost("ssid");

		$slicemodel = Slicemodel::findFirstByssid($id);
		$slicemodel->ssname = $this->request->getPost("ssname");
		$slicemodel->serverowner = $this->serverowner[$this->request->getPost("serverowner")];
		$slicemodel->serverip = $this->request->getPost("serverip");
		$slicemodel->maxnum = $this->request->getPost("maxnum");
		$slicemodel->apiurl = $this->request->getPost("apiurl");
		$slicemodel->remark = $this->request->getPost("remark");
		$slicemodel->updatetime = time();
		$slicemodel->msid = $this->request->getPost("msid");

		if (!$slicemodel->save()) {
			foreach ($slicemodel->getMessages() as $message) {
				$this->flash->error($message);
			}

			return $this->dispatcher->forward(array(
					"controller" => "sliceserver",
					"action" => "edit",
					"params" => array($slicemodel->ssid)
			));
		}
		$this->tag->setDefault("serverowner", $this->request->getPost("serverowner"));

		$this::logAction('修改切片服务器', "修改了切片服务器:$slicemodel->ssname");

		$this->flash->success("修改成功");
		$this->response->redirect("sliceserver/index");
	}

	public function deleteAction($id){
		$info = Slicemodel::findFirstByssid($id);
		if(!$info){
			$this->flash->error('server not find');
			return $this->dispatcher->forward(array(
					'controller'=>'sliceserver',
					'action'=>'index'
				)
				
			);
		}
		if(Monitor::findFirstByssid($id)){
			$this->flash->error('切片服务器已被使用，删除失败');
			return $this->dispatcher->forward(array(
							'controller'=>'sliceserver',
							'action'=>'index'
					)

			);
		}
		if(!$info->delete()){
			foreach ($info->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "sliceserver",
                "action" => "index"
            ));
		}

		$this::logAction('删除切片服务器', "删除了服务器:$info->ssname");

        $this->flash->success("删除成功");
        $this->response->redirect("sliceserver/index");
	}
	

	/*启用或禁用*/
    public function updatestatusAction($id,$status){
        $msg = array('0'=>'禁用','1'=>'启用');
        $Slicemodel = Slicemodel::findFirstByssid($id);
        //var_dump($Slicemodel);
        if($status == 1){
			            $Slicemodel->serverstatus = 1;
        }else{
			if(Monitor::findFirstByssid($id)){
				$this->flash->error('切片服务器已被使用，禁用失败');
				return $this->dispatcher->forward(array(
								'controller'=>'sliceserver',
								'action'=>'index'
						)

				);
			}
            $Slicemodel->serverstatus = 0;
        }
        if(!$Slicemodel->save()){
            $this->flash->error($msg[$Slicemodel->serverstatus].'失败');
            foreach ($Slicemodel->getMessages() as $message) {
                $this->flash->error($message);
            }
        }else{
            $this->flash->success($msg[$Slicemodel->serverstatus].'成功');
        }
        $this->response->redirect("sliceserver/index");
    }

    /*信息详情*/

    public function detailAction($id){
    	$info = Slicemodel::findFirstByssid($id);
		if (!$info) {
			$this->flash->error("没有找到该数据");
			return $this->dispatcher->forward(array(
					"controller" => "sliceserver",
					"action" => "index"
			));
		}
		if($info->serverstatus ==1){
			$serverstatus = '正常';
		}else{
			$serverstatus = '禁用';
		}
		/*if($info->serverowner ==0){
			$serverowner = '';
		}else{
			$serverowner = $this->serverowner[$info->serverowner];
		}*/
		$createtime= '';
		if(!empty($info->createtime)){
			$createtime = date('Y-m-d H:i:s',$info->createtime);
		}
		$updatetime= '';
		if(!empty($info->updatetime)){
			$updatetime = date('Y-m-d H:i:s',$info->updatetime);
		}
		$creator = '';
		if(!empty($info->creator)){
			$creatorinfo = Admin::findFirstByid($info->creator);
			if($creatorinfo){
				$creator = $creatorinfo->username;
			}
			
		}
		if($info->msid > 0){
			$solution = Monitorsolution::findFirstBymsid($info->msid);
			$this->tag->setDefault('msid',$solution->name);
		}

		$this->view->setVar('ssid',$id);
		$this->tag->setDefault('ssid',$id);
		$this->tag->setDefault('ssname',$info->ssname);
		$this->tag->setDefault('serverip',$info->serverip);
		$this->tag->setDefault('maxnum',$info->maxnum);
		$this->tag->setDefault('apiurl',$info->apiurl);
		$this->tag->setDefault('serverstatus',$serverstatus);
		$this->tag->setDefault('remark',$info->remark);
		$this->tag->setDefault("serverowner", $info->serverowner);
		$this->tag->setDefault("createtime",$createtime );
		$this->tag->setDefault("updatetime", $updatetime);
		$this->tag->setDefault("creator", $creator);
    }

}
