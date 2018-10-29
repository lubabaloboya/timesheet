<?php

class model_accessControl extends model {
    
    public $ID;
    public $controller;
    public $action;
    public $status;
    
    protected $_controller  = "admin";
    protected $_action      = "access-controls";
    protected $_table       = "access_controls";
    protected $_ref         = "accessControlID";

    function __construct($id = null) {
        parent::__construct($id);
        $this->setUp();
    }
    
    function setUp() {
        $this->ID           = $this->result["accessControlID"];
        $this->controller   = $this->result["controller"];
        $this->action       = $this->result["action"];
        $this->status       = $this->result["status"];
    }

    function editForm($request) {
        $sql = "SELECT * FROM access_controls WHERE accessControlID=?";
        $array["path"] = "/forms/editAccessForm.js";
        $array["values"] = $this->_db->fetch_all_stmt($sql, "i", array($request->id), true);
        $array = $this->formRender($array);
        return $this->updateForm($array, $request);
    }
    
    function createForm(request $request) {
        if($request->subType == "edit") {
            return $this->editForm($request);
        } 
    }
    
    function create(\request $request) {
        return false;
    }
    
    function read(request $request) {
        if($request->subType == "default") {
            $array["page"] = "admin/accessControlView";
            return $array;
        }
    }
    
    function updateStatus($request) {
        if($this->_db->update($request->getDataArray(), "access_controls", "accessControlID", $request->id) === true) {
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
        return $array;
    }
    
    function updateAccess($request) {
        $sql = "SELECT * FROM access_control_to_roles WHERE accessControlID=? AND roleID=?";
        $obj = $request->getDataObj();
        $result = $this->_db->fetch_all_stmt($sql, "ii", array($request->id, $obj->roleID), true);
        $array = json_decode($result["accessRights"], true);
        $array[str_replace("_", "-", $obj->type)] = (int) $obj->value;
        $result["accessRights"] = json_encode($array);
        if($this->_db->update($result, "access_control_to_roles", "accessControlToRolesID", $result["accessControlToRolesID"])) {
            $array = array(
                "status"=>true,
            );
        } else {
            $array = array(
                "status"=>true,
                "message"=>"Your request was successful"
            );
        }
        
        return $array;
    }
    
    function update(\request $request) {
        if($request->subType == "status") {
            $array = $this->updateStatus($request);
        } else {
            $array = $this->updateAccess($request);
        }        
        $request->addResponseItems($array);
        return $request->response();
    }
    
    function delete(\request $request) {
        return false;
    }
    
    function getAccessArrays() {
        return $this->_db->fetch_all_stmt("SELECT * FROM access_control_to_roles WHERE accessControlID=?", "i", array($this->ID));
    }

}