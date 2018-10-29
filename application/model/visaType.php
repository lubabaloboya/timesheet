<?php

class model_visaType extends model {
    
    public $ID;
    public $name;
    public $abbreviation;
    public $alert;
    
    protected $_table = "visa_types";
    protected $_ref = "visaTypeID";
    protected $_controller = "immigration";
    protected $_action = "visa-types";
            
    function __construct($id = null) {
        parent::__construct($id);
        $this->setUp();
    }
    
    function setUp() {
        $this->ID           = $this->result["visaTypeID"];
        $this->countryID    = $this->result["visaCountryID"];
        $this->name         = $this->result["visaTypeName"];
        $this->abbreviation = $this->result["visaTypeAbreviation"];
        $this->alert        = $this->result["visaTypeAlert"];
    }
    
    protected function addForm($request) {
        $array["path"] = "/forms/addVisaTypeForm.js";
        $countries = $this->_db->fetch_numeric("SELECT countryID, countryName FROM countries WHERE countryStatus = 1 ORDER BY countryName ASC");
        $array["list"] = array(
            "visaCountryID"=>$countries,
        );
        $array = $this->formRender($array);     
        return $this->updateForm($array, $request);
    }
    
    protected function editForm($request) {
        $array["path"] = "/forms/addVisaTypeForm.js";
        $array["values"] = $this->_db->fetch_all_stmt("SELECT * FROM visa_types WHERE visaTypeID=?", "i", array($request->id), true);
        $countries = $this->_db->fetch_numeric("SELECT countryID, countryName FROM countries WHERE countryStatus = 1 ORDER BY countryName ASC");
        $array["list"] = array(
            "visaCountryID"=>$countries,
        );
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
    
    protected function createVisaType(request $request) {
        $obj = $request->getDataObj();
        $filter = new filter(array(
            "required"=>array(
                array (
                    "Name"=>$obj->visaTypeName,
                    "Abbreviation"=>$obj->visaTypeAbreviation,
                    "Alert"=>$obj->visaTypeAlert
                )
            ),
            "number"=>array(
                array (
                    "Alert"=>$obj->visaTypeAlert
                )
            ),
            "maxLength"=>array(
                array(
                    "Name"=>array($obj->visaTypeName, 45),
                    "Abbreviation"=>array($obj->visaTypeAbreviation, 10),
                    "Alert"=>array($obj->visaTypeAlert, 3)
                )
            ),
            "min"=>array(
                array(
                    "Name"=>array($obj->visaTypeAlert, 1)
                )
            )
        ));
        
        if(empty($filter->errors)) {
            if($this->_db->insert($request->getDataArray(), "visa_types") === true) {
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
        if($request->subType == "default") {
            $array["page"] = "immigration/visaTypeView";
            return $array;
        }
    }
    
    function create(\request $request) {
        if($request->subType == "default") {
            return $this->createVisaType($request);
        }
    }
    
    function getDocumentation() {
        $result = $this->_db->fetch_all_stmt("SELECT visaDocumentationTypeID FROM visa_documentation_types WHERE visaDocumentationTypeVisaTypeID=? ORDER BY visaDocumentationTypeOrder", "i", array($this->ID));
        $array = array();
        
        if(is_array($result)) {
            foreach($result as $doc) {
                $array[] = new model_visaDocumentationType($doc["visaDocumentationTypeID"]);
            }
            return $array;
        } else {
            throw new appException("Could not retrive visa documentation types from database");
        }
    }
    
    protected function updateVisaType(request $request) {
        $obj = $request->getDataObj();
        $filter = new filter(array(
            "minLength"=>array(
                array (
                    "Name"=>@$obj->visaTypeName,
                    "Abbreviation"=>@$obj->visaTypeAbreviation,
                    "Alert"=>@$obj->visaTypeAlert
                )
            ),
            "number"=>array(
                array (
                    "Alert"=>@$obj->visaTypeAlert
                )
            ),
            "maxLength"=>array(
                array(
                    "Name"=>array(@$obj->visaTypeName, 45),
                    "Abbreviation"=>array(@$obj->visaTypeAbreviation, 10),
                    "Alert"=>array(@$obj->visaTypeAlert, 3)
                )
            ),
            "min"=>array(
                array(
                    "Name"=>array(@$obj->visaTypeAlert, 1)
                )
            )
        ));
        
        if(empty($filter->errors)) {
            if($this->_db->update($request->getDataArray(), "visa_types", "visaTypeID", $request->id) === true) {
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
        switch($request->subType) {
            case "default": return $this->updateVisaType($request);
        }
    }

    function getVisaTypeName($visaTypeId)
    {        
        $type = new model_visaType($visaTypeId);

        //strtoupper : converts a string to uppercase        
        return strtoupper($type->name);
    }
}
