<?php

class model_role extends model {
    
    public $ID;
    public $name;
    public $default;
    
    protected $_db;
    protected $_controller = "admin";
    protected $_action = "roles";
    protected $_table = "roles";
    protected $_ref = "roleID";

    function __construct($id = null) {
        parent::__construct($id);
        $this->setUp();
    }
    
    function setUp() {
        $this->ID       = $this->result["roleID"];
        $this->name     = $this->result["roleName"];
        $this->default  = $this->result["roleDefault"];
    }

    private function addForm($request) {
        $array["path"] = "/forms/addRoleForm.js";
        $array["values"] = null;
        $array = $this->formRender($array);
        return $this->updateForm($array, $request);
    }
    
    private function editForm($request) {
        $array["path"] = "/forms/addRoleForm.js";
        $array["values"] = $this->_db->fetch_all_stmt("SELECT * FROM roles WHERE roleID=?", "i", array($request->id), true);
        $array = $this->formRender($array);
        return $this->updateForm($array, $request);
    }
    
    function createForm(request $request) {
        if($request->subType == "edit") {
           return $this->editForm($request);
        } else if($request->subType == "default"){
           return $this->addForm($request);
        }
    }
    
    //Retrieve access from database
    protected function getAccessControls() {
        return $this->_db->fetch_all_stmt("SELECT * FROM access_controls");
    }
    
    protected function updateAccessControls($id) {
        $json = json_encode(array(
            "access"=>0, 
            "create"=>0, 
            "read"=>0, 
            "update"=>0, 
            "delete"=>0)
        );
        
        foreach($this->getAccessControls() as $v) {
            $array = array(
                "accessControlID"=>$v["accessControlID"],
                "roleID"=>$id,
                "accessRights"=>$json
            );
            $this->_db->insert($array, "access_control_to_roles");
        }
    }

    function create(request $request) {
        $obj = $request->getDataObj();
        $filter = new filter(array(
            "required"=>array(
                array(
                    "Name"=>$obj->roleName
                )
            ),
            "entryExists"=>array("SELECT * FROM roles WHERE roleName=?", "s", array($obj->roleName))
        ));
        
        if(empty($filter->errors)) {
            if($this->_db->insert($request->getDataArray(), "roles") === true) {
                
                $this->updateAccessControls($this->_db->insert_id);
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
        } else {
            $array = array(
                "status"=>false,
                "message"=>$filter->errors
            );
        }
        
        $request->addResponseItems($array);
        return $request->response();
    }
    
    function read(request $request) {
        return false;
    }
    
    function validate($id) {
        $role = new model_role($id);
        
        if($role->default == 1) {
            return false;
        }
        
        return true;
    }
    
    function update(request $request) {
        $obj = $request->getDataObj();
        $filter = new filter(array(
            "required"=>array(
                array(
                    "Name"=>$obj->roleName
                )
            ),
            "entryExists"=>array("SELECT * FROM roles WHERE roleName=?", "s", array($obj->roleName))
        ));
        
        if($this->validate($request->id)) {
            if(empty($filter->errors)) {
                if($this->_db->update($request->getDataArray(), "roles", "roleID", $request->id) === true) {
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
            } else {
                $array = array(
                    "status"=>false,
                    "message"=>$filter->errors
                );
            }
        } else {
            $array = array(
                "status"=>false,
                "message"=>"This action is not allowed"
            );
        }
        
        $request->addResponseItems($array);
        return $request->response();
    }
    
    function delete(request $request) {
       
        $sql = "DELETE FROM roles WHERE roleID=?";
        if($this->_db->delete($sql, "i", array($request->id)) === true) {
            $array = array(
                "status" => true,
                "message" => "Your request was successful"
            );
        } else {
            $array = array(
                "status" => false,
                "message" =>$this->deleteMsg($this->_db->dbError)
            );
        }

        //Return json object as response
        $request->addResponseItems($array);
        return $request->response();
    }
}