<?php
App::uses('AppController', 'Controller');
class MobileapiController extends AppController {
    public $name = 'MobileAPI';

    public $uses = array();

    public function beforeFilter() {

        $this->Auth->allow('*');
    }

    public function startup($clientID = "default", $vendorID = 1) {

        $this->loadModel('Conversation');
        $conversation = $this->Conversation->find('first', array('conditions' => array('clientID' => "$clientID", 'completed' => '', 'Conversation.created >' => date('Y-m-d H:i:s', strtotime('15 minutes ago')))));

        if ($conversation) {
            $this->redirect(array("action" => "getconversation", $conversation["Conversation"]["sessionID"]));
        } else {
            $this->redirect(array("controller" => "categories", "action" => "listjson"));
        }
    }

    public function getconversation($sessionId) {

        if (!$sessionId || $sessionId=="") exit("provide sessionId");

        $this->loadModel('Conversation');
        $conversation = $this->Conversation->find('first', array('conditions' => array('sessionId' => "$sessionId")));

        if (!$conversation) {
            echo "no such conversation!!!";
        }
        
        $result["SessionId"] = $conversation["Conversation"]["sessionID"];
        $result["Token"] = $conversation["Conversation"]["token1"];
        $result["HelperName"] = $conversation["Helper"]["fullname"];
        $result["CategoryName"] = $conversation["Category"]["name"];
        $enabled = "false";
        if ($conversation["Conversation"]["imageEnabled"]) { $enabled = "true"; }
        $result["Image"]["Enabled"] = $enabled;
        $result["Image"]["Photo"] = $conversation["Conversation"]["imagePhotoURL"];
        // $result["Image"]["Overlay"] = base64_encode($conversation["Conversation"]["imageOverlayData"]);
        $result["Image"]["Overlay"] = ($conversation["Conversation"]["imageOverlayData"]);
        $completed = "false";
        if ($conversation["Conversation"]["completed"]) { $completed = "true"; }
        $result["Completed"] = $completed;
        $this->response->type('json');
        $this->layout = 'json';
        $json = json_encode($result);
        $this->set('json', $json);
        $this->render('conversation');
    }

    public function startconversation($clientID, $categoryID) {

        if (!$clientID || !$categoryID || $categoryID=="" || $clientID=="") exit("provide clientID and categoryID");

        App::import('Vendor', 'opentok/OpenTokSDK');
        $apiObj = new OpenTokSDK( API_Config::API_KEY, API_Config::API_SECRET );
        $session = $apiObj->createSession( $_SERVER["REMOTE_ADDR"]  );
        $sessionID = $session->getSessionId();

        $data = array();
        $data["Conversation"]["category_id"] = $categoryID;
        $data["Conversation"]["sessionID"] = $sessionID;
        $data["Conversation"]["clientID"] = $clientID;
        $data['Conversation']['token1'] = $apiObj->generateToken($sessionID);
        $data['Conversation']['token2'] = $apiObj->generateToken($sessionID);

        $this->loadModel('Conversation');
        $category = $this->Conversation->Category->findById($categoryID);

        if ($this->Conversation->save($data)) {

            $conversation["SessionId"] = $sessionID;
            $conversation["Token"] = $data["Conversation"]["token1"];
            $conversation["HelperName"] = "Waiting for support..";
            $conversation["CategoryName"] = $category["Category"]["name"];
 
            $this->response->type('json'); 
            $this->layout = 'json';
            $json = json_encode($conversation);
            $this->set('json', $json);

            $this->render('conversation');
        }
    }
    
    public function closeconversation($sessionId) {

        if (!$sessionId || $sessionId=="") exit("provide sessionId");

        $this->loadModel('Conversation');
        $conversation = $this->Conversation->find('first', array('conditions' => array('sessionId' => "$sessionId")));

        if (!$conversation) {
            echo "no such conversation!!!";
        }
        
        $conversation["Conversation"]["completed"] = 1;

        if ($this->Conversation->save($conversation)) {
            $result["result"] = "success";
        } else {
            $result["result"] = "failed";
        }

        $this->response->type('json'); 
        $this->layout = 'json';
        $json = json_encode($result);
        $this->set('json', $json);
        $this->render('conversation');
    }
}
