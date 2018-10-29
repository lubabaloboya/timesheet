<?php

class model_visaDocumentation extends model {
    
    public $ID;
    public $visaDocumentationTypeID;
    public $visaID;
    public $extension;
    public $status;
    public $dateAdded;
    public $dateUploaded;
    public $dateExpiry;
    public $notRequired;
    
    protected $_table = "visa_documentation";
    protected $_ref = "visaDocumentationID";
    protected $_controller = "immigration";
    protected $_action = "visa-documentation";
    
    function __construct($id = null) {
        parent::__construct($id);
        $this->setUp();
    }
    
    function setUp() {
        $this->ID                       = $this->result["visaDocumentationID"];
        $this->visaDocumentationTypeID  = $this->result["visaDocumentationVisaDocumentationTypeID"];
        $this->visaID                   = $this->result["visaDocumentationVisaID"];
        $this->extension                = $this->result["visaDocumentationExtension"];
        $this->status                   = $this->result["visaDocumentationStatus"];
        $this->dateAdded                = $this->dateSetup($this->result["visaDocumentationDateAdded"]);
        $this->dateUploaded             = $this->dateSetup($this->result["visaDocumentationDateUploaded"]);
        $this->dateExpiry               = $this->dateSetup($this->result["visaDocumentationDateExpiry"]);
        $this->notRequired              = $this->result["visaDocumentationNotRequired"];
    }
    
    function uploadDocumentationForm($request) {  
        $obj = $request->getDataObj();
        $array["path"] = "/forms/addVisaDocumentationForm.js";
        $array = $this->formRender($array);     
        
        $doc = new model_visaDocumentation($request->id);
        $doc_type = new model_visaDocumentationType($doc->visaDocumentationTypeID);
        if($doc_type->expiry === 0) {
            unset($array["form"][1]);
        }

        return $this->updateForm($array, $request);
    }
    
    protected function validateDocumentationForm(request $request) {
        $obj = $request->getDataObj();
        $array["path"] = "/forms/validateDocumentForm.js";
        $array = $this->formRender($array);        
        return $this->updateForm($array, $request);
    }

    protected function readDocumentInfoForm(request $request) {

        $form = new form(APPLICATION_PATH . "/forms/viewVisaDocumentationInfoForm.js");
        $values = $this->_db->fetch_all_stmt("SELECT visaDocumentationTypeDescription FROM visa_documentation INNER JOIN visa_documentation_types ON visaDocumentationVisaDocumentationTypeID=visaDocumentationTypeID WHERE visaDocumentationID=?", "i", array($request->id), true);
        $form->setValues($values);
        $form->injectValues();
        return $this->updateForm($form->getForm(), $request);  
        
    }
    
    function createForm(request $request) {
        switch($request->subType) {
            case "upload-document": return $this->uploadDocumentationForm($request);
            case "validate-document": return $this->validateDocumentationForm($request);
        }
    }
    
    function uploadVisaDocument(request $request) {
        
        $obj = $request->getDataObj();
        $filter_array = array();
        
        if($obj->visaDocumentationNotRequired == 0) {
            $filter_array["validFile"] = array($_FILES["document"], array("pdf","docx","doc","xlsx","jpeg","jpg","png"), 5);
        }
        
        $filter = new filter($filter_array);
         $data = $request->getDataArray();
         
        if(empty($filter->errors)) {
            
            if($obj->visaDocumentationNotRequired == 0) {
                $uploader = new uploader($_FILES["document"]);
               

                $data["visaDocumentationStatus"] = 1;
                $data["visaDocumentationExtension"] = $uploader->getExtension();
                

                if(isset($data["visaDocumentationDateExpiry"])) {
                    $date = new DateTime($data["visaDocumentationDateExpiry"]);
                    $data["visaDocumentationDateExpiry"] = $date->format("Y-m-d");
                }
            }
            
            $data["visaDocumentationDateUploaded"] = date("Y-m-d H:i");

            if($this->_db->update($data, "visa_documentation", "visaDocumentationID", $request->id) === true){
                
                $visa_docs = new model_visaDocumentation($request->id);
                $visa = new model_visa($visa_docs->visaID);
                    
                if(isset($obj->visaDocumentationNotRequired) && $obj->visaDocumentationNotRequired == 0) {
                    $uploader = new uploader($_FILES["document"]);
                    $uploader->basePath = ADMIN_PATH . "/documentation/" . $visa->ID;
                    $uploader->newFilename = $visa_docs->ID . $uploader->getExtension();
                     if($uploader->transferFile()) {
                        $array["message"][] = "Document successfully uploaded";
                    } else {
                        $array["status"] = false;
                        $array["message"][] = $uploader->error;
                    }
                } else {
                    $array["message"] = "Your request was successful";
                }
                
                $status = $this->updateVisaStatus($visa);
                
                $array["status"] = true;
                $array["visaStatus"] = $visa->getStatusName($status);
                
               
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
        if($request->subType == "upload-document") {
            return $this->uploadVisaDocument($request);
        }
    }
    
    
    function readDocument(request $request) {
        $uploader = new uploader();
        
        $visa_docs = new model_visaDocumentation($request->id);
        $visa = new model_visa($visa_docs->visaID);
        
        $path = ADMIN_PATH . "/documentation/" . $visa->ID ."/". $visa_docs->ID . $visa_docs->extension; 

        if(file_exists($path)) {
            if($filename = $uploader->moveFile($path)) {
                $array = array(
                    "status"=>true,
                    "href"=>"/tmp/".$filename
                );  
            } else {
                $array = array(
                    "status"=>false,
                    "message"=>"Were unable to move the file for viewing"
                );  
            }
        } else {
            $array = array(
                "status"=>false,
                "message"=>"File does not exists"
            );   
        }
        $request->addResponseItems($array);
        return $request->response();
    }
    
    function read(request $request) {
        switch($request->subType) {
            case "read-document": return $this->readDocument($request);;
            case "read-document-info": return $this->readDocumentInfoForm($request);
        }
    }
    
    function updateVisaStatus($visa) {
        $phases = [1, 2, 3, 4]; //Phases
         $status = 1;
        //Want to confirm that the new status falls within the document stages
        //Otherwise the status update is not relevant at this point
        if(in_array($visa->status, $phases)) {
           
            //Check if the current phase has been completed
            if($visa->isPhaseComplete(1)) {
                $status = 2;
            }
            if($visa->isPhaseComplete(1) && $visa->isPhaseComplete(2)) {
                $status = 3;
            }
            if($visa->isPhaseComplete(1) && $visa->isPhaseComplete(2) && $visa->isPhaseComplete(3)) {
                $status = 4;
            }
            if($visa->isPhaseComplete(1) && $visa->isPhaseComplete(2) && $visa->isPhaseComplete(3) && $visa->isPhaseComplete(4)) {
                $status = 5;
            }
            $visa->statusUpdate($status);
        }
        
        return $status;
    }
    
    protected function validateDocumentation(request $request) {
        $obj = $request->getDataObj();
        $filterArray = array(
            "required"=>array(
                array(
                    "Validate"=>$obj->visaDocumentationStatus
                )
            )
        );
        
        //If the document is declined we must have a reason
        if(isset($obj->visaDocumentationStatus) && $obj->visaDocumentationStatus == 3) {
            $filter["required"][0]["Reason"] = $obj->reason;
        }
        
        $filter = new filter($filterArray);
        
        if(empty($filter->errors)) {
            $data = $request->getDataArray();
            $doc = new model_visaDocumentation($request->id);
            $visa = new model_visa($doc->visaID);
                
            if(isset($obj->reason)) {
                //Get the user information
                
                $expat = new model_expatriate($visa->expatriateID);
                $user = new model_user($expat->userID);
                
                //Send declining email
                $this->declineEmail($user, $obj->reason);
                
                //Unset reason, not needed any further
                unset($data["reason"]);
            }
            
            if($this->_db->update($data, "visa_documentation", "visaDocumentationID", $request->id)) {
                
                $status = $this->updateVisaStatus($visa);
    
                $array = array(
                    "status"=>true,
                    "visaStatus"=> $visa->getStatusName($status),
                    "message"=>"Your request was successful"
                );
            } else {
                $array = array(
                    "status"=>false,
                    "message"=>"Your request was unsucessful"
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
    
    protected function reinstateDocument(request $request) {
        $obj = $request->getDataObj();
        $filter = new filter(array(
            "required"=>array(
                array(
                    "Reinstate Document"=>$obj->visaDocumentationNotRequired
                )
            )
        ));
        
        if(empty($filter->errors)) {
            $data = $request->getDataArray();
            $doc = new model_visaDocumentation($request->id);
            $visa = new model_visa($doc->visaID);

            if($this->_db->update($data, "visa_documentation", "visaDocumentationID", $request->id)) {
                
                $status = $this->updateVisaStatus($visa);
    
                $array = array(
                    "status"=>true,
                    "visaStatus"=> $visa->getStatusName($status),
                    "message"=>"Your request was successful"
                );
            } else {
                $array = array(
                    "status"=>false,
                    "message"=>"Your request was unsucessful"
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
        switch($request->subType) {
            case "validate-document": return $this->validateDocumentation($request);
            case "reinstate-document": return $this->reinstateDocument($request);
        }
    }
    
    function addVisaDocumentation(model_visa $visa) {
        
        $visa_type_docs = new model_visaDocumentationType();
        $docs = $visa_type_docs->getVisaDocTemplate($visa->visaTypeID);
        
        $data = array(
            "visaDocumentationVisaID"=>$visa->ID
        );
        
        foreach($docs as $v) {
            $data["visaDocumentationVisaDocumentationTypeID"] = $v["visaDocumentationTypeID"];
            
            $this->_db->insert($data, "visa_documentation");
            
        }
        
    }
    
    function getVisaDocs($visaID) {
        $array = array();
            
        $result = $this->_db->fetch_all_stmt("SELECT visaDocumentationID FROM visa_documentation LEFT JOIN visa_documentation_types ON visaDocumentationVisaDocumentationTypeID = visaDocumentationTypeID WHERE visaDocumentationVisaID=? ORDER BY visaDocumentationTypePhase, visaDocumentationTypeOrder","i", array($visaID));
        
        foreach($result as $v) {
            $array[] = new model_visaDocumentation($v["visaDocumentationID"]);
        }
        
        return $array;
    }
    
    
    function isDocumentUploaded($visaID, $typeID) {
        if(file_exists(ADMIN_PATH . "/documentation/" .  $visaID . "/" . $typeID . ".pdf")) {
            return true;
        } else {
            return false;
        }
    }
    
    function deleteDocument(request $request) {

        $data = array(
            "visaDocumentationStatus"=>0
        );
        
        if($this->_db->update($data, "visa_documentation", "visaDocumentationID", $request->id) === true) {
            
            $document = new model_visaDocumentation($request->id);
            $visa = new model_visa($document->visaID);
            $uploader = new uploader();
            $dir = ADMIN_PATH . "/documentation/" . $visa->ID;
            $path = $dir . "/" . $document->ID . $document->extension;
                    
            if(file_exists($path)) {
                $uploader->removeFile($path , $dir);
            }
            
            $status = $this->updateVisaStatus($visa);
            
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
       if($request->subType == "delete-visa-document") {
           return $this->deleteDocument($request);
       }
    }
    

}