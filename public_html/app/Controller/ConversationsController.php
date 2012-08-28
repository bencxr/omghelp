<?php
App::uses('AppController', 'Controller');
/**
 * Conversations Controller
 *
 * @property Conversation $Conversation
 */
class ConversationsController extends AppController {

    /**
     * index method
     *
     * @return void
     */
    public function index() {
        $this->Conversation->recursive = 0;
        $this->set('conversations', $this->paginate());
    }

    public function setimage($id = null) {
        $this->Conversation->id = $id;
        if (!$this->Conversation->exists()) {
            throw new NotFoundException(__('Invalid conversation'));
        }

        $conversation = $this->Conversation->read(null,$id);
        $conversation["Conversation"]["imagePhotoURL"] = $_POST["imageurl"];
        $conversation["Conversation"]["imageEnabled"] = "1";
        if ($this->Conversation->save($conversation)) {
            echo "success";
        } else {
            echo "failed";
        }
        exit;
    }

    public function setoverlay($id = null) {
        $this->Conversation->id = $id;
        if (!$this->Conversation->exists()) {
            throw new NotFoundException(__('Invalid conversation'));
        }

        $conversation = $this->Conversation->read(null,$id);
        $conversation["Conversation"]["imagePhotoURL"] = $_POST["imageurl"];
        $conversation["Conversation"]["imageOverlayData"] = $_POST["overlaydata"];
        $conversation["Conversation"]["imageEnabled"] = "1";
        if ($this->Conversation->save($conversation)) {
            echo "success";
        } else {
            echo "failed";
        }
        exit;
    }

    public function removeimage($id = null) {
        $this->Conversation->id = $id;
        if (!$this->Conversation->exists()) {
            throw new NotFoundException(__('Invalid conversation'));
        }

        $conversation = $this->Conversation->read(null,$id);
        $conversation["Conversation"]["imagePhotoURL"] = "";
        $conversation["Conversation"]["imageOverlayData"] = "";
        $conversation["Conversation"]["imageEnabled"] = "0";
        if ($this->Conversation->save($conversation)) {
            echo "success";
        } else {
            echo "failed";
        }
        exit;
    }

    public function accept($id = null) {

        $user = $this->Auth->user();
        $this->Conversation->id = $id;
        if (!$this->Conversation->exists()) {
            throw new NotFoundException(__('Invalid conversation'));
        }

        $conversation = $this->Conversation->read(null,$id);
        if ($conversation["Conversation"]["helper_id"] == "") {
            $conversation["Conversation"]["helper_id"] = $user["id"];
            if ($this->Conversation->save($conversation)) {
                $this->redirect("/conversations/view/$id");
            } else {
                exit("could not update the helper id!");
            }
        } else {
            $this->redirect("/");
        }

        print_r($conversation);
    }

    public function listUnanswered() {

        $this->loadModel('User');
        $user = $this->Auth->user();
        $userDetails = $this->User->findByid($user["id"]);

        $categoryids = $this->extractHelpCategories($userDetails);
        $conversations = $this->Conversation->find('all', array('conditions' => array('category_id' => $categoryids, 'completed' => '', 'helper_id' => '', 'Conversation.created >' => date('Y-m-d H:i:s', strtotime('15 minutes ago')) )));

        $results = array();
        foreach ($conversations as $conversation) {
            $result["sessionID"] = $conversation["Conversation"]["sessionID"];
            $result["userName"] = "User_".$conversation["Conversation"]["clientID"];
            $result["id"] = $conversation["Conversation"]["id"];
            $result["age"] = $this->timeDiff($conversation["Conversation"]["created"], date(DATE_RFC822));
            $result["category"] = $conversation["Category"]["name"];

            $results[] = $result;
        }

        $this->layout = 'json';
        $this->set('unanswered', $results);
    }

    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null) {
        $this->Conversation->id = $id;
        if (!$this->Conversation->exists()) {
            throw new NotFoundException(__('Invalid conversation'));
        }

        $conversation = $this->Conversation->read(null,$id);
        $this->set('conversation', $conversation);
    }

    /**
     * add method
     *
     * @return void
     */
    public function add() {

        $categories = $this->Conversation->Category->find('list');
        $helpers = $this->Conversation->Helper->find('list');
        $this->set(compact('categories', 'helpers'));

        if ($this->request->is('post')) {

            App::import('Vendor', 'opentok/OpenTokSDK');
            $apiObj = new OpenTokSDK( API_Config::API_KEY, API_Config::API_SECRET );
            $session = $apiObj->createSession( $_SERVER["REMOTE_ADDR"] );
            $sessionId = $session->getSessionId();;
            $this->request->data['Conversation']['sessionID'] = $sessionId;
            $this->request->data['Conversation']['token1'] = $apiObj->generateToken($sessionId);
            $this->request->data['Conversation']['token2'] = $apiObj->generateToken($sessionId);
            $this->Conversation->create();
            if ($this->Conversation->save($this->request->data)) {
                $this->Session->setFlash(__('The conversation has been saved'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The conversation could not be saved. Please, try again.'));
            }
        }
    }

    function setcompleted($id = null) {
        $conversation = $this->Conversation->findById($id);

        if (!$conversation) {
            echo "no such conversation!!!";
            exit;
        }
        
        $conversation["Conversation"]["completed"] = 1;

        if ($this->Conversation->save($conversation)) {
            $result["result"] = "success";
            $this->redirect('/');
        } else {
            $result["result"] = "failed";
        }
    }

    /**
     * edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function edit($id = null) {
        $categories = $this->Conversation->Category->find('list');
        $helpers = $this->Conversation->Helper->find('list');
        $this->set(compact('categories', 'helpers'));
        $this->Conversation->id = $id;
        if (!$this->Conversation->exists()) {
            throw new NotFoundException(__('Invalid conversation'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Conversation->save($this->request->data)) {
                $this->Session->setFlash(__('The conversation has been saved'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The conversation could not be saved. Please, try again.'));
            }
        } else {
            $this->request->data = $this->Conversation->read(null, $id);
        }
    }

    /**
     * delete method
     *
     * @throws MethodNotAllowedException
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->Conversation->id = $id;
        if (!$this->Conversation->exists()) {
            throw new NotFoundException(__('Invalid conversation'));
        }
        if ($this->Conversation->delete()) {
            $this->Session->setFlash(__('Conversation deleted'));
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Conversation was not deleted'));
        $this->redirect(array('action' => 'index'));
    }


    function timeDiff($firstTime,$lastTime)
    {

        // convert to unix timestamps
        $firstTime=strtotime($firstTime);
        $lastTime=strtotime($lastTime);

        // perform subtraction to get the difference (in seconds) between times
        $timeDiff=$lastTime-$firstTime;

        $mins = floor($timeDiff/60);
        $secs = $timeDiff - $mins*60;

        // return the difference
        return $mins." mins, ".$secs." seconds";
    }

    function extractHelpCategories($user) {
        $ids = array();
        foreach ($user["HelpsCategories"] as $category) {
            $ids[] = $category["id"];
        }

        return $ids;
    }

}
