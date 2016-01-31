<?php
/**
 * webapp
 *chirw
 *curd example,only for study  
 */
namespace Controllers; 
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Phalcon\Paginator\Adapter\QueryBuilder as PaginatorQueryBuilder;
class TopicController extends ControllerBase
{

    /**
     * list
     */
    public function indexAction()
    {
        error_reporting(E_ALL & ~E_NOTICE);
        //$this->persistent->parameters = null;
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, "Topic", $_POST);
            $this->persistent->parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }
        if ($numberPage ==null) {

        $this->persistent->parameters = null;
        }

        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = array();
        }
        if($this->request->getPost("topic")){
        	$parameters["conditions"] .= " and topictype = 2 and status = 0"; //status:
        }else{
        	$parameters["conditions"] = "topictype = 2 and status = 0"; //status:
        }
        //$parameters["order"] = "id";
        
        


        $builder = $this->modelsManager->createBuilder()
            ->columns('*')
            ->from('Topic')
                        ->where($parameters['conditions'], $parameters['bind'])
            ->orderBy('id');

        $paginator = new PaginatorQueryBuilder(
            array(
                "builder" => $builder,
                "limit"   => 20,
                "page"    => $numberPage
            )
        );


        $this->view->page = $paginator->getPaginate();
    }

    /**
     * Displays the creation form
     */
    public function newAction()
    {

    }

    /**
     * edit
     *
     * @param string $id
     */
    public function editAction($id)
    {

        if (!$this->request->isPost()) {

            $topic = Topic::findFirstByid($id);
            if (!$topic) {
                $this->flash->error("topic was not found");

                return $this->dispatcher->forward(array(
                    "controller" => "topic",
                    "action" => "index"
                ));
            }

            $this->view->id = $topic->id;

            $this->tag->setDefault("id", $topic->id);
            $this->tag->setDefault("topic", $topic->topic);
            $this->tag->setDefault("topictype", $topic->topictype);
            $this->tag->setDefault("groupid", $topic->groupid);
       
            
        }
    }

    /**
     * add
     */
    public function createAction()
    {

        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "topic",
                "action" => "index"
            ));
        }

        $topic = new Topic();

        $topic->topic = $this->request->getPost("topic");
        $topic->info = $this->request->getPost("info");
        $topic->topictype = $this->request->getPost("topictype");
        $topic->subtime = time();
        $topic->adminname = $this->auth['name'];
        $topic->status = 0;
        

        if (!$topic->save()) {
            foreach ($topic->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "topic",
                "action" => "new"
            ));
        }

        $this->flash->success("add success");

        $this->response->redirect("topic/index");

    }

    /**
     * save edit
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "topic",
                "action" => "index"
            ));
        }

        $id = $this->request->getPost("id");

        $topic = Topic::findFirstByid($id);
        if (!$topic) {
            $this->flash->error("topic does not exist " . $id);

            return $this->dispatcher->forward(array(
                "controller" => "topic",
                "action" => "index"
            ));
        }

        $topic->topic = $this->request->getPost("topic");
        $topic->topictype = $this->request->getPost("topictype");
        $topic->unsubtime = time();
        

        if (!$topic->save()) {

            foreach ($topic->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "topic",
                "action" => "edit",
                "params" => array($topic->id)
            ));
        }

        $this->flash->success("edit success");

        $this->response->redirect("topic/index");

    }

    /**
     * delete
     *
     * @param string $id
     */
    public function deleteAction($id)
    {

        $topic = Topic::findFirstByid($id);
        if (!$topic) {
            $this->flash->error("the  topic not exist");

            $this->response->redirect("topic/index");
        }
        $topic->status = 1;
        
        if (!$topic->save()) {

            foreach ($topic->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "topic",
                "action" => "index"
            ));
        }

        $this->flash->success("delete success");

        $this->response->redirect("topic/index");
    }

}
