<?php

class model_expatriate extends model {
    public $ID;
    public $userID;
    public $passportNumber;
    public $homeCountryID;
    public $hostCountryID;
    public $jobTitle;
    public $jobDescription;
    public $passportExpiryDate;
    
    protected $_table = "expatriates";
    protected $_ref = "expatriateID";
    protected $_controller = "admin";
    protected $_action = "expatriates";
            
    function __construct($id = null) {
        parent::__construct($id);
        $this->setUp();
    }
    
    function setUp() {
        $this->ID                   = $this->result["expatriateID"];
        $this->userID               = $this->result["expatriateUserID"];
        $this->passportNumber       = $this->result["expatriatePassportNumber"];
        $this->homeCountryID        = $this->result["expatriateHomeCountryID"];
        $this->hostCountryID        = $this->result["expatriateHostCountryID"];
        $this->jobTitle             = $this->result["expatriateJobTitle"];
        $this->jobDescription       = $this->result["expatriateJobDescription"];
        $this->passportExpiryDate   = $this->result["expatriatePassportExpiryDate"];
    }
    
    protected function addForm($request) {
        $array["path"] = "/forms/addExpatriateForm.js";

        $countries = $this->_db->fetch_numeric("SELECT countryID, countryName FROM countries");
        
        $array["list"] = array(
            "expatriateUserID"=>$this->_db->fetch_numeric("SELECT userID, CONCAT(name, ' ', userSurname) as userName FROM users WHERE userStatus = 1 AND userRoleID = 4"),
            "expatriateHostCountryID"=>$countries,
            "expatriateHomeCountryID"=>$countries
        );
        
        $list = new model_dropDownList();
        $array["list"] = $list->getFormDropDownLists("expatriate", $array["list"]);
        $array = $this->formRender($array);     
        return $this->updateForm($array, $request);
    }
    
    protected function editForm($request) {
        $array["path"] = "/forms/addExpatriateForm.js";
        $array["values"] = $this->_db->fetch_all_stmt("SELECT * FROM expatriates WHERE expatriateID=?", "i", array($request->id), true);
        $countries = $this->_db->fetch_numeric("SELECT countryID, countryName FROM countries");
        
        $array["list"] = array(
            "expatriateUserID"=>$this->_db->fetch_numeric("SELECT userID, CONCAT(name, ' ', userSurname) as userName FROM users WHERE userStatus = 1 AND userRoleID = 4"),
            "expatriateHostCountryID"=>$countries,
            "expatriateHomeCountryID"=>$countries
        );
        
        $list = new model_dropDownList();
        $array["list"] = $list->getFormDropDownLists("expatriate", $array["list"]);
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
    
    protected function updateExpatriate(request $request) {
        $obj = $request->getDataObj();
        $filter = new filter(array(
            "minLength"=>array(
                array (
                    "Home Country"=>array(@$obj->expatriateHomeCountryID, 1),
                    "Host Country"=>array(@$obj->expatriateHostCountryID, 1)
                )
            ),
            "number"=>array(
                array(
                    "Home Country"=>@$obj->expatriateHomeCountryID,
                    "Host Country"=>@$obj->expatriateHostCountryID
                )
            ),
            "maxLength"=>array(
                array(
                    "Home Country"=>array(@$obj->expatriateHomeCountryID, 4),
                    "Host Country"=>array(@$obj->expatriateHostCountryID, 4),
                    "Job Title"=>array(@$obj->expatriateHostCountryID, 45),
                    "Job Description"=>array(@$obj->expatriateHostCountryID, 500)
                )
            )
        ));
        
        if(empty($filter->errors)) {
            $check = $this->_db->fetch_all_stmt("SELECT expatriateUserID FROM expatriates WHERE expatriateID=?", "i", array($request->id), true);
            $data = $request->getDataArray();

            if(isset($data["expatriatePassportExpiryDate"])) {
                $date = new DateTime($data["expatriatePassportExpiryDate"]);
                $data["expatriatePassportExpiryDate"] = $date->format("Y-m-d");
            }

            if(isset($check["expatriateUserID"])) {
                $bool = $this->_db->update($data, "expatriates", "expatriateID", $request->id);
            } else {
                $data["expatriateID"] = $request->id;
                $bool = $this->_db->insert($data, "expatriates");
            }
            
            if($bool === true) {
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
        if($request->subType == "default") {
            return $this->updateExpatriate($request);
        }
    }
    
     function read(request $request) {
        if($request->subType == "default") {
            $array["page"] = "admin/expatriateView";
            return $array;
        }
    }
    
    function deleteExpatriate(request $request) {
        $sql = "DELETE FROM expatriates WHERE expatriateID=?";

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

    function delete(\request $request) {
       if($request->subType == "default") {
           return $this->deleteExpatriate($request);
       }
    }
    
    function getExpatriate($userID) {

        $sql = "SELECT * FROM expatriates WHERE expatriateUserID=?";
        $result = $this->_db->fetch_all_stmt($sql, "i", array($userID), true);
        

        if(!empty($result)) {
            $this->result = $result; 
            $this->setUp();
        } else {
            return false;
        }
    }
}
