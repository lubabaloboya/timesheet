<?php

class model_visaComment extends model {
    
    public $ID;
    public $userID;
    public $visaID;
    public $text;
    public $dateCreated;
    
    protected $_table = "visa_comments";
    protected $_ref = "visaCommentID";
    protected $_controller = "immigration";
    protected $_action = "visa-comments";
            
    function __construct($id = null) {
        parent::__construct($id);
        $this->setUp();
    }
    
    function setUp() {
        $this->ID               = $this->result["visaCommentID"];
        $this->userID           = $this->result["visaCommentUserID"];
        $this->visaID           = $this->result["visaCommentVisaID"];
        $this->text             = $this->result["visaCommentText"];
        $this->dateCreated      = $this->dateSetup($this->result["visaCommentDateCreated"]);
    }
    
    protected function addForm($request) {
        $array["path"] = "/forms/addVisaCommentForm.js";
        $array = $this->formRender($array);     
        return $this->updateForm($array, $request);
    }
    
    protected function editForm($request) {
        $array["path"] = "/forms/addVisaCommentForm.js";
        $array["values"] = $this->_db->fetch_all_stmt("SELECT * FROM visa_comments WHERE visaCommentID=?", "i", array($request->id), true);
        $array = $this->formRender($array);        
        return $this->updateForm($array, $request);
    }
            
    function createForm(request $request) {
        if($request->subType == "default"){
            return $this->addForm($request);
        } else if($request->subType == "edit") {
            return $this->editForm($request);
        }
    }
    
    protected function addVisaComment(request $request) {
        $obj = $request->getDataObj();
        $filter = new filter(array(
            "required"=>array(
                array (
                    "Comment"=>$obj->visaCommentText
                )
            ),
            "maxLength"=>array(
                array(
                    "Comment"=>array($obj->visaCommentText, 500)
                )
            )
        ));
        
        if(empty($filter->errors)) {
            $data = $request->getDataArray();
            $user = new model_user();
            $user->getCurrentUser();
            $data["visaCommentUserID"] = $user->ID;
            $data["visaCommentVisaID"] = $request->id;
            
            if($this->_db->insert($data, "visa_comments") === true) {
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
    
    function create(\request $request) {
        return $this->addVisaComment($request);
    }
    
    protected function editVisaComment(request $request) {
        $obj = $request->getDataObj();
        $filter = new filter(array(
            "minLength"=>array(
                array (
                    "Comment"=>array(@$obj->visaCommentText, 1)
                )
            ),
            "maxLength"=>array(
                array(
                    "Comment"=>array(@$obj->visaCommentText, 500)
                )
            )
        ));
        
        if(empty($filter->errors)) {
            if($this->_db->update($request->getDataArray(), "visa_comments", "visaCommentID", $request->id) === true) {
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
    
    function update(\request $request) {
        return $this->editVisaComment($request);
    }
    
    protected function deleteVisaComment(request $request) {
        if($this->_db->delete("DELETE FROM visa_comments WHERE visaCommentID=?", "i", array($request->id)) === true) {
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

    function delete(\request $request) {
       if($request->subType == "default") {
           return $this->deleteVisaComment($request);
       }
    }
}
