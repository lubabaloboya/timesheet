<?php

class model_visaDocumentationType extends model {
    
    public $ID;
    public $visaTypeID;
    public $name;
    public $description;
    public $expiry;
    public $phase;
    
    protected $_table = "visa_documentation_types";
    protected $_ref = "visaDocumentationTypeID";
    protected $_controller = "immigration";
    protected $_action = "visa-documentation-types";
    
    function __construct($id = null) {
        parent::__construct($id);
        $this->setUp();
    }
    
    function setUp() {
        $this->ID               = $this->result["visaDocumentationTypeID"];
        $this->visaTypeID       = $this->result["visaDocumentationTypeVisaTypeID"];
        $this->name             = $this->result["visaDocumentationTypeName"];
        $this->description      = $this->result["visaDocumentationTypeDescription"];
        $this->expiry           = $this->result["visaDocumentationTypeExpiry"];
        $this->phase            = $this->result["visaDocumentationTypePhase"];
    }
    
    private function addForm($request) {
        $array["path"] = "/forms/addVisaDocumentationTypeForm.js";
        $array["values"] = null;
        $list = new model_dropDownList();
        $array["list"] = $list->getFormDropDownLists("visaDocumentationType", array("list"=>array()));
        $array = $this->formRender($array);
        return $this->updateForm($array, $request);
    }
    
    private function editForm($request) {
        $array["path"] = "/forms/addVisaDocumentationTypeForm.js";
        $array["values"] = $this->_db->fetch_all_stmt("SELECT * FROM visa_documentation_types WHERE visaDocumentationTypeID=?", "i", array($request->id), true);
        $list = new model_dropDownList();
        $array["list"] = $list->getFormDropDownLists("visaDocumentationType", array("list"=>array()));
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
    
    function updateVisaDocumentation($document_type_id, $visa_type_id) {
      //Here we must check for any open visas
      $sql = "SELECT visaID FROM visas WHERE visaVisaTypeID=? AND visaStatus IN (1, 2, 3, 4)";
      $visas = $this->_db->fetch_all_stmt($sql, "i", array($visa_type_id));

      if(count($visas) > 0) {

        foreach($visas as $visa) {
          $data = [
              "visaDocumentationVisaDocumentationTypeID"=>$document_type_id,
              "visaDocumentationVisaID" => $visa["visaID"]
          ];

          $this->_db->insert($data, "visa_documentation");
        }

      }
    }
    
    function createVisaDocumentationType(request $request) {
        $obj = $request->getDataObj();
        $filter = new filter(array(
            "required"=>array(
                array(
                    "Name"=>$obj->visaDocumentationTypeName,
                    "Expiry"=>$obj->visaDocumentationTypeExpiry,
                    "Phase"=>$obj->visaDocumentationTypePhase
                )
            ),
            "number"=>array(
                array(
                    "Expiry"=>$obj->visaDocumentationTypeExpiry,
                    "Phase"=>$obj->visaDocumentationTypePhase
                )
            ),
            "maxLength"=>array(
                array(
                    "Name"=>array($obj->visaDocumentationTypeName, 45),
                    "Description"=>array($obj->visaDocumentationTypeDescription, 500),
                    "Expiry"=>array($obj->visaDocumentationTypeExpiry, 1),
                    "Phase"=>array($obj->visaDocumentationTypePhase, 1)
                )
            ),
            "entryExists"=>array("SELECT visaDocumentationTypeID FROM visa_documentation_types WHERE visaDocumentationTypeVisaTypeID=? AND visaDocumentationTypeName=?", "is", array($request->id, $obj->visaDocumentationTypeName))
        ));
        
        if(empty($filter->errors)) {
            $data = $request->getDataArray();
            $data["visaDocumentationTypeVisaTypeID"] = $request->id;
            if($this->_db->insert($data, "visa_documentation_types") === true) {
              $new_doc_id = $this->_db->insert_id;
              
              $this->updateVisaDocumentation($new_doc_id, $request->id);
              
              $array = array(
                  "status"=>true,
                  "message"=>"Your request was successful"
              );
            } else {
                $array = array(
                    "status"=>false,
                    "message"=>$filer->errors
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
           return $this->createVisaDocumentationType($request);
        }
    }
    
    function updateVisaDocumentationType($request) {
        $obj = $request->getDataObj();
        $filter = new filter(array(
            "minLength"=>array(
                array(
                    "Name"=>array(@$obj->visaDocumentationTypeName, 1),
                    "Expiry"=>array(@$obj->visaDocumentationTypeExpiry, 1),
                    "Phase"=>array(@$obj->visaDocumentationTypePhase, 1)
                )
            ),
            "number"=>array(
                array(
                    "Expiry"=>@$obj->visaDocumentationTypeExpiry,
                    "Phase"=>@$obj->visaDocumentationTypePhase
                )
            ),
            "maxLength"=>array(
                array(
                    "Name"=>array(@$obj->visaDocumentationTypeName, 45),
                    "Description"=>array(@$obj->visaDocumentationTypeDescription, 500),
                    "Expiry"=>array(@$obj->visaDocumentationTypeExpiry, 1),
                    "Phase"=>array(@$obj->visaDocumentationTypePhase, 1)
                )
            ),
        ));

        if(empty($filter->errors)) {
            if($this->_db->update($request->getDataArray(), "visa_documentation_types", "visaDocumentationTypeID", $request->id) === true) {
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
            return $this->updateVisaDocumentationType($request);
        }
    }
    
    //Remove visa documentation from any open visas that may have this document already added
    function removeVisaDocumentation($visa_doc_id) {
      
      $doc_type = new model_visaDocumentationType($visa_doc_id);
      $sql = "SELECT visaID FROM visas WHERE visaVisaTypeID=? AND visaStatus IN (1, 2, 3, 4)";
      $visas = $this->_db->fetch_all_stmt($sql, "i", array($doc_type->visaTypeID));

      if(count($visas) > 0) {
        
        foreach($visas as $visa) {
          $sql = "DELETE FROM visa_documentation WHERE visaDocumentationVisaID=? AND  visaDocumentationVisaDocumentationTypeID=?";
          $this->_db->delete($sql, "ii", array($visa["visaID"], $doc_type->ID));
          
        }
        
      }
      
    }
    
    function deleteVisaDocumentationType(request $request) {
        $sql = "DELETE FROM visa_documentation_types WHERE visaDocumentationTypeID=?";
        
        $this->removeVisaDocumentation($request->id);
        
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
           return $this->deleteVisaDocumentationType($request);
       }
    }
    
    function getVisaDocTemplate($visaTypeID) {
        return $this->_db->fetch_all_stmt("SELECT visaDocumentationTypeID FROM visa_documentation_types WHERE visaDocumentationTypeVisaTypeID=?", "i", array($visaTypeID));
    }


    
    
    function getDocumentChecklist($visaID) {

        $sql = "SELECT * FROM visa_documentation LEFT JOIN visa_documentation_types ON visaDocumentationTypeID = visaDocumentationVisaDocumentationTypeID WHERE visaDocumentationVisaID=?";
        return $this->_db->fetch_all_stmt($sql, "i", array($visaID));

    }

}