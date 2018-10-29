<?php

class model_company extends model {
    
    public $ID;
    public $name;
    public $contactNumber;
    public $address;
    public $email;
    
    protected $_table = "companies";
    protected $_ref = "companyID";
    protected $_controller = "admin";
    protected $_action = "companies";
    
    function __construct($id = null) {
        parent::__construct($id);
        $this->setUp();
    }
    
    function setUp() {
        $this->ID               = $this->result["companyID"];
        $this->name             = $this->result["companyName"];
        $this->contactNumber    = $this->result["companyContactNumber"];
        $this->address          = $this->result["companyAddress"];
        $this->email            = $this->result["companyEmail"];
    }
    
    
    protected function addForm($request) {
        $array["path"] = "/forms/addCompanyForm.js";
        $array = $this->formRender($array);
        return $this->updateForm($array, $request);
    }
    
    protected function editForm($request) {
        $array["path"] = "/forms/addCompanyForm.js";
        $array["values"] = $this->_db->fetch_all_stmt("SELECT * FROM companies WHERE companyID=?", "i", array($request->id), true);
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
   
    protected function createCompany(request $request) {
        $obj = $request->getDataObj();
        $filter_array = array(
            "required"=>array(
                array (
                    "Name"=>$obj->companyName,
                    "Contact Number"=>$obj->companyContactNumber
                )
            ),
            "maxLength"=>array(
                array(
                    "Name"=>array($obj->companyName, 45),
                    "Contact Number"=>array($obj->companyContactNumber, 45),
                    "Address"=>array($obj->companyAddress, 300),
                    "Email"=>array($obj->companyEmail, 45)
                )
            )
        );
        
        if(isset($_FILES["visas"])) {
            $filter_array["validFile"] =array($_FILES["visas"], array("csv", "CSV"), 0.5);
            $filter_array["required"][0]["Delimiter"] = $obj->delimiter;
        }
        
        $filter = new filter($filter_array);

        if(empty($filter->errors)) {
            $data = $request->getDataArray();
            
            if(isset($data["delimiter"])) { 
                unset($data["delimiter"]); 
            }
            
            if(isset($_FILES["visas"])) {
                $import = new model_importVisas($_FILES["visas"], $obj->delimiter);
                $import->validateCSV();
            }
            
            if(empty($import->errors)) {
                if($this->_db->insert($this->extractor($request->getDataArray(), "company"), "companies") === true) {

                    if(isset($_FILES["visas"])) {
                        $import->import($this->_db->insert_id);
                    }

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
                    "message"=>$import->errors
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
        if($request->subType == "default") {
            return $this->createCompany($request);
        }
    }
    
    //The update function has been added to company fie
    function updateCompany($request) {
        $obj = $request->getDataObj();
/*        $filter = new filter(array(
            "minLength"=>array(
                array(
                    "Name"=>array(@$obj->name, 1),
                    "Surname"=>array(@$obj->userSurname, 1),
                    "Username"=>array(@$obj->username, 1),
                    "Email"=>array(@$obj->userEmail, 1),
                    "Company"=>array(@$obj->userCompanyID, 1),
                    "Role"=>array(@$obj->userRoleID, 1)
                )
            ),
            "number"=>array(
                array(
                     "Post Code"=>$obj->postCode
                )
            )
        ));*/

        if(empty($filter->errors)) {
            
            if($this->_db->update($this->extractor($request->getDataArray(), "company"), "companies", "companyID", $request->id) === true) {
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
    
    function update(request $request) {
        if($request->subType == "default") {
            return $this->updateCompany($request);
        }
    }
    
    function read(request $request) {
        if($request->subType == "default") {
            $array["page"] = "admin/companyView";
            return $array;
        }
    }
    
    
    // Adding the delete function to company 
    
    function deleteCompany(request $request) {

        $sql = "DELETE FROM companies WHERE companyID=?";

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

    function delete(request $request) {
       if($request->subType == "default") {
           return $this->deleteCompany($request);
       }
    }
}
