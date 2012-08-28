<?php
// app/Controller/UsersController.php
class UsersController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('add', 'login', 'logout');
    }

    public function dashboard() {
    }

    public function login() {
        if ($this->request->is('post')) {
            if ($this->Auth->login()) {
                $this->redirect($this->Auth->redirect());
            } else {
                $this->Session->setFlash(__('Invalid username or password, try again'));
            }
        }
    }

    public function logout() {
        $this->redirect($this->Auth->logout());
    }

    public function index() {
        $this->loadModel("User");
        $this->loadModel("Conversation");
        $this->loadModel("Categories");

        if (!$this->Auth->user()) {
            $this->redirect(array("action" => "login"));
        }

        $user = $this->Auth->user();
        $userDetails = $this->User->findByid($user["id"]);

        $unfinishedConversation = $this->Conversation->find('first',array( 'conditions' => array('helper_id' => $user["id"], 'completed' => '')));

        if ($unfinishedConversation) {
            $this->redirect(array('controller' => 'conversations', 'action' => 'view', $unfinishedConversation["Conversation"]["id"]));
        }

        // display the categories user is in
        $helpcategories = $this->Categories->find('list',array('fields'=>array('id','name'), 'conditions' => array('not' => array('parent_id' => null))));
        $this->set(compact('helpcategories'));
        $this->data = $this->User->read(null, $user["id"]);

        $this->User->recursive = 0;
        $this->set('users', $this->paginate());
        
        $completedConversation = $this->Conversation->find('count',array( 'conditions' => array('helper_id' => $user["id"], 'completed' => '1')));
        $this->set('completedConversations', $completedConversation);
    }

    public function view($id = null) {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        $this->set('user', $this->User->read(null, $id));
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->User->create();

            if (isset($this->request->data['User']['password'])) {
                $this->request->data['User']['password'] = AuthComponent::password($this->request->data['User']['password']);
            }

            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('The user has been saved'));
                $id = $this->User->getInsertID();
                $this->request->data['User']['id'] = $id;
                $this->Auth->login($this->request->data['User']);
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
            }
        }
    }

    public function edit($id = null) {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {

            $this->Session->write("Auth", $this->data); 
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('Your information has been updated.'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
            }
        } else {
            $this->request->data = $this->User->read(null, $id);
            unset($this->request->data['User']['password']);
        }
    }

    public function delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->User->delete()) {
            $this->Session->setFlash(__('User deleted'));
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('User was not deleted'));
        $this->redirect(array('action' => 'index'));
    }
}
