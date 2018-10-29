<?php

class model_messages extends model {
    
    public $ID;
    public $title;
    public $body;
    public $created;
    public $ownerID;
    
    protected $_table = "messages";
    protected $_ref = "messageID";
    
    function __construct($id = null) {
        parent::__construct($id);
        $this->setUp();
    }
    
    function setUp() {
        $this->ID       = $this->result["messageID"];
        $this->title    = $this->result["messageTitle"];
        $this->body     = $this->result["messageBody"];
        $this->created  = isset($this->result["messageDateCreated"]) ? new DateTime($this->result["messageDateCreated"]) : NULL;
        $this->ownerID  = $this->result["messageOwnerID"];
    }
    
    private function addForm($request) {
        $array["path"] = "/forms/createMessage.js";
        $user = new model_user();
        $user->getCurrentUser();
        $array["list"]["messagesToUsersUserID"]=$this->_db->fetch_numeric("SELECT userID, CONCAT(name, ' ', userSurname) as name FROM users WHERE userID!=? ORDER BY name", "i", array($user->ID));
        $array = $this->formRender($array);
        $_SESSION["messages"] = false;
        return $this->updateForm($array, $request);
    }
    
    private function editForm($request) {
        $array["path"] = "/forms/editMessage.js";
        $array["values"] = $this->_db->fetch_all_stmt("SELECT * FROM messages WHERE messageID=?", "i", array($request->id), true);
        $array = $this->formRender($array);
        $_SESSION["messages"] = false;
        return $this->updateForm($array, $request);
    }
    
    private function broadcastForm($request) {
        $array["path"] = "/forms/createBroadcastMessage.js";
        $user = new model_user();
        $user->getCurrentUser();
        $array["list"]["messageRoles"]=$this->_db->fetch_numeric("SELECT roleID, roleName FROM roles WHERE roleID!=? ORDER BY roleName", "i", array(1));
        $array = $this->formRender($array);
        $_SESSION["messages"] = false;
        return $this->updateForm($array, $request);
    }
    
    function createForm(request $request) {
        if($request->subType == "default") {
            return $this->addForm($request);
        } else if($request->subType == "edit") {
            return $this->editForm($request);
        } else if($request->subType == "broadcast") {
            return $this->broadcastForm($request);
        }
    }
    
    function addUser($request) {
        $filter = new filter(array(
            "entryDoesNotExists"=>array("SELECT userID FROM users WHERE userID=?", "i", array($request->id))
        ));
        
        if($_SESSION["messages"] == false) {
            $_SESSION["messages"] = array();
        }
        
        if(empty($filter->errors)) {
            if(!in_array($request->id, $_SESSION["messages"])) {
                $_SESSION["messages"][] = $request->id;
                $array = array(
                    "status"=>true
                );
            } else {
                $array = array(
                    "status"=>false,
                    "message"=>"Entry has already been added"
                );
            }
        } else {
            $array = array(
                "status"=>false,
                "message"=>$filter->errors
            );
        }
        
        $request->addResponseItems($array);
        return $request->response();
    }
    
    function addRole($request) {
        $filter = new filter(array(
            "entryDoesNotExists"=>array("SELECT roleID FROM roles WHERE roleID=?", "i", array($request->id))
        ));
        
        if($_SESSION["messages"] == false) {
            $_SESSION["messages"] = array();
        }
        
        if(empty($filter->errors)) {
            if(!in_array($request->id, $_SESSION["messages"])) {
                $_SESSION["messages"][] = $request->id;
                $array = array(
                    "status"=>true
                );
            } else {
                $array = array(
                    "status"=>false,
                    "message"=>"Entry has already been added"
                );
            }
        } else {
            $array = array(
                "status"=>false,
                "message"=>$filter->errors
            );
        }
        
        $request->addResponseItems($array);
        return $request->response();
    }
    
    function createMessage($request) {
        $obj = $request->getDataObj();
        $filter = new filter(array(
            "required"=>array(
                array(
                    "Title"=>$obj->messageTitle,
                    "Message"=>$obj->messageBody
                )
            )
        ));
        
        if(empty($filter->errors)) {
            if(isset($_SESSION["messages"]) && count($_SESSION["messages"]) > 0) { //Chec to see if users have been added
                $data = $request->getDataArray();
                $user = new model_user();
                $user->getCurrentUser();
                $data["messageOwnerID"] = $user->ID;
                if($this->_db->insert($data, "messages") === true) {   
                    foreach($_SESSION["messages"] as $v) {// Then attach the message to each user account
                        $entries = array("messagesToUsersUserID"=>$v, "messagesToUsersMessageID"=>$this->_db->insert_id);
                        $this->_db->insert($entries, "messages_to_users");
                    }
                    $array = array(
                        "status"=>true,
                        "message"=>"Your message was successfully sent"
                    );
                } else {
                    $array = array(
                        "status"=>false,
                        "message"=>"Your request failed"
                    );
                }
            } else {
                $array = array(
                    "status"=>false,
                    "message"=>"The \"To List\" seems to be empty please try again"
                );
            }
        } else {
            $array = array(
                "status"=>false,
                "message"=>$filter->errors
            );
        }
        $request->addResponseItems($array);
        return $request->response();
    }
    
    function createBroadcastMessage($request) {
        $obj = $request->getDataObj();
        $filter = new filter(array(
            "required"=>array(
                array(
                    "Title"=>$obj->messageTitle,
                    "Message"=>$obj->messageBody
                )
            )
        ));
        
        if(empty($filter->errors)) {
            if(isset($_SESSION["messages"]) && count($_SESSION["messages"]) > 0) { //Check to see if users have been added
                $data = $request->getDataArray();
                $user = new model_user();
                $user->getCurrentUser();
                $data["messageOwnerID"] = $user->ID;
                if($this->_db->insert($data, "messages") === true) {   
                    $dbid = $this->_db->insert_id;
                    
                    foreach($_SESSION["messages"] as $v) {
                        // Then iterate through each user account and match the role
                        $results = $this->_db->fetch_all_stmt("SELECT userID FROM users WHERE userRoleID=?", "i", array($v));
                        

                        if(is_array($results) && count($results > 0)) {
                            //Send messages to all users that share the roles defined
                            foreach($results as $val) {
                                $entries = array(
                                    "messagesToUsersUserID"=>$val["userID"], 
                                    "messagesToUsersMessageID"=>$dbid
                                );
                                $this->_db->insert($entries, "messages_to_users");
                            }
                            
                            $array = array(
                                "status"=>true,
                                "message"=>"Your message was successfully sent"
                            );
                        } else {  
                            $array = array(
                                "status"=>true,
                                "message"=>"We were not able to add any users to message, please check that this role has been allocated to users, otherwise contact system administrator"
                            );
                        }  
                    }
                } else {
                    $array = array(
                        "status"=>false,
                        "message"=>"Your request failed, message was not sent"
                    );
                }
            } else {
                $array = array(
                    "status"=>false,
                    "message"=>"The \"To List\" seems to be empty please try again"
                );
            }
        } else {
            $array = array(
                "status"=>false,
                "message"=>$filter->errors
            );
        }
        $request->addResponseItems($array);
        return $request->response();
    }
    
    function create(request $request) {
        if($request->subType == "default") {
            return $this->createMessage($request);
        } else if($request->subType == "add-user") {
            return $this->addUser($request);
        } else if($request->subType == "add-role") {
            return $this->addRole($request);
        } else if($request->subType == "broadcast") {
            return $this->createBroadcastMessage($request);
        }
    }
    
    function getMessage($messageID) {
        $user = new model_user();
        $user->getCurrentUser();
        $result = $this->_db->fetch_all_stmt("SELECT messageID, messagesToUsersID, messageTitle, messageBody, messageOwnerID FROM messages_to_users INNER JOIN messages ON messagesToUsersMessageID=messageID WHERE messagesToUsersUserID=? AND messagesToUsersMessageID=?", "ii", array($user->ID, $messageID), true);
        $this->_db->update(array("messagesToUsersStatus"=>3), "messages_to_users", "messagesToUsersID", $result["messagesToUsersID"]);
        return $result;
    }

    
    protected function recogniseMessages() {
        $user = new model_user();
        $user->getCurrentUser();
        if($this->_db->update(array("messagesToUsersStatus"=>2), "messages_to_users", "messagesToUsersUserID", $user->ID) === true) {
            $array = array(
                "status"=>true
            );
        } else {
            $array = array(
                "status"=>false
            );
        }
        
        $request->addResponseItems($array);
        return $request->response();
    }
    
    
    private function updateMessage($request) {
        $obj = $request->getDataObj();
        $filter = new filter(array(
            "required"=>array(
                array(
                    "Title"=>$obj->messageTitle,
                    "Message"=>$obj->messageBody
                )
            )
        ));
        
        if(empty($filter->errors)) {         
            if($this->_db->update($request->getDataArray(), "messages", "messageID", $request->id) === true) {   
                $array = array(
                    "status"=>true,
                    "message"=>"Your request was succesful"
                );
            } else {
                $array = array(
                    "status"=>false,
                    "message"=>"Your request was unsuccesful"
                );
            }   
        } else {
            $array = array(
                "status"=>false,
                "message"=>$filter->errors
            );
        }
        
        $request->addResponseItems($array);
        return $request->response();
    }
    
    function update(request $request) {
        if($request->subType == "default") {
            return $this->updateMessage($request);
        } else if($request->subType == "recognition") {
            return $this->recogniseMessages();
        }
    }
    
    function removeUser($request) {
        if(in_array($request->id, $_SESSION["messages"])) {
            if(!isset($_SESSION["messages"])) {
                $_SESSION["messages"] = array();
            }
            
            $key = array_search($request->id, $_SESSION["messages"]);
            unset($_SESSION["messages"][$key]);
            $array = array(
                "status"=>true
            );
        } else {
            $array = array(
                "status"=>false,
                "message"=>"User does not seem to exist in the provided list"
            );
        }
        
        $request->addResponseItems($array);
        return $request->response();
    }
    
    protected function deleteMessage($request) {
        $user = new model_user();
        $user->getCurrentUser();
        $sql = "DELETE FROM messages_to_users WHERE messagesToUsersUserID=? AND messagesToUsersMessageID=?";
        if($this->_db->delete($sql, "ii", array($user->ID, $request->id)) === true) {
            $array = array(
                "status"=>true,
                "message"=>"Your request was successful"
            );
        } else {
            $array = array(
                "status"=>true,
                "message"=>"Your request was unsuccessful"
            );
        }
        $request->addResponseItems($array);
        return $request->response();
    }
    
    protected function hideOutboxMessage($request) {
        if($this->_db->update(array("messageStatus"=>2), "messages", "messageID", $request->id) === true) {
            $array = array(
                "status"=>true,
                "message"=>"Your request was successful"
            );
        } else {
            $array = array(
                "status"=>false,
                "message"=>"Your request was unsuccessful"
            );
        }
        $request->addResponseItems($array);
        return $request->response();
    }
    
    function delete(request $request) {
        if($request->subType == "default") {
            return $this->deleteMessage($request);
        } else if($request->subType == "remove-user") {
            return $this->removeUser($request);
        } else if($request->subType == "outbox") {
            return $this->hideOutboxMessage($request);
        }
    }
    
    function getStatus($userID, $messageID) {
        $sql = "SELECT messagesToUsersStatus FROM messages_to_users WHERE messagesToUsersUserID=? AND messagesToUsersMessageID=?";
        $result = $this->_db->fetch_all_stmt($sql, "ii", array($userID, $messageID), true);
        return $result["messagesToUsersStatus"];
    }
    
    function getUnreadMessages($userID) {
        $sql = "SELECT messageID FROM messages_to_users INNER JOIN messages ON messagesToUsersMessageID = messageID WHERE messagesToUsersUserID=? AND messagesToUsersStatus<3";
        return $this->_db->fetch_all_stmt($sql, "i", array($userID));
    }
    
    function getAllMessages($userID) {
        $sql = "SELECT messageID FROM messages_to_users INNER JOIN messages ON messagesToUsersMessageID = messageID WHERE messagesToUsersUserID=?";
        return $this->_db->fetch_all_stmt($sql, "i", array($userID));
    }
    
    function getOutboxMessages($userID) {
        $sql = "SELECT messageID FROM messages WHERE messageOwnerID=? AND messageStatus=1";
        return $this->_db->fetch_all_stmt($sql, "i", array($userID));
    }
    
    function totalNewMessages($userID) {
        $sql = "SELECT messagesToUsersID FROM messages_to_users WHERE messagesToUsersUserID=? AND messagesToUsersStatus=1";
        return $this->_db->rows($sql, "i", array($userID));
    }
    
    function totalUnreadMessages($userID) {
        $sql = "SELECT messagesToUsersID FROM messages_to_users WHERE messagesToUsersUserID=? AND messagesToUsersStatus<3";
        return $this->_db->rows($sql, "i", array($userID));
    }
    
    function totalRecognisedMessages($userID) {
        $sql = "SELECT messagesToUsersID FROM messages_to_users WHERE messagesToUsersUserID=? AND messagesToUsersStatus=2";
        return $this->_db->rows($sql, "i", array($userID));
    }
    
    function totalReadMessages($userID) {
        $sql = "SELECT messagesToUsersID FROM messages_to_users WHERE messagesToUsersUserID=? AND messagesToUsersStatus=3";
        return $this->_db->rows($sql, "i", array($userID));
    }
}