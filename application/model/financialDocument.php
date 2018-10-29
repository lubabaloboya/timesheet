<?php

class model_financialDocument extends model {
    
    public $ID;
    public $visaID;
    public $name;
    public $extension;
    public $status;
    public $dateAdded;
    public $dateUploaded;
    
    protected $_controller = "immigration";
    protected $_action = "financial-documents";
    protected $_table = "financial_documents";
    protected $_ref = "financialDocumentID";

    function __construct($id = null) {
        parent::__construct($id);
        $this->setUp();
    }
    
    function setUp() {
        $this->ID           = $this->result["financialDocumentID"];
        $this->visaID       = $this->result["financialDocumentVisaID"];
        $this->name         = $this->result["financialDocumentName"];
        $this->extension    = $this->result["financialDocumentExtension"];
        $this->status       = $this->result["financialDocumentStatus"];
        $this->dateAdded    = $this->dateSetup($this->result["financialDocumentDateAdded"]);
        $this->dateUploaded = $this->dateSetup($this->result["financialDocumentDateUploaded"]);
    }
    
    protected function addForm(request $request) {
        $array["path"] = "/forms/addFinancialDocumentForm.js";
        $array["values"] = null;
        $array = $this->formRender($array);
        return $this->updateForm($array, $request);
    }
    
    protected function editForm(request $request) {
        $array["path"] = "/forms/addFinancialDocumentForm.js";
        $array["values"] = $this->_db->fetch_all_stmt("SELECT financialDocumentName FROM financial_documents WHERE financialDocumentID=?", "i", array($request->id), true);

        $array = $this->formRender($array);
        return $this->updateForm($array, $request);
    }
    
    protected function uploadFileForm(request $request) {
        $array["path"] = "/forms/uploadFinancialDocumentForm.js";
        $array = $this->formRender($array);
        return $this->updateForm($array, $request);
    }
    
    function createForm(\request $request) {
        switch($request->subType) {
            case "default": return $this->addForm($request);
            case "edit": return $this->editForm($request);
            case "upload-file": return $this->uploadFileForm($request);
        }
    }
    
    protected function createFinancialDocument(request $request) {
        $obj = $request->getDataObj();
        $filter = new filter(array(
            "required"=>array(
                array(
                    "Name"=>$obj->financialDocumentName
                )
            )
        ));
        
        if(empty($filter->errors)) {
            $data = $request->getDataArray();
            $data["financialDocumentVisaID"] = $request->id;
            
            if($this->_db->insert($data, "financial_documents") === true) {
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
        if($request->subType == "default") {
            return $this->createFinancialDocument($request);
        }
    }
    
    function readDocument(request $request) {
        $uploader = new uploader();

        $doc = new model_financialDocument($request->id);
        $visa = new model_visa($doc->visaID);
            
        $path = ADMIN_PATH . "/financial-documentation/" . $visa->ID ."/". $doc->ID . $doc->extension;
        
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
            case "view-document": return $this->readDocument($request);
        }
        
    }
    
    protected function updateFinancialDocument(request $request) {
        $obj = $request->getDataObj();
        $filter = new filter(array(
            "minLength"=>array(
                array(
                    "Name"=>array(@$obj->financialDocumentName, 1)
                )
            )
        ));
        
        if(empty($filter->errors)) {
            $data = $request->getDataArray();
            
            if($this->_db->update($data, "financial_documents", "financialDocumentID", $request->id) === true) {

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
    
    function uploadFile($request) {
       
        $filter = new filter(array(
            "validFile"=>array($_FILES["document"], array("pdf","docx","doc","xlsx","jpeg","jpg","png"), 5)
        ));
        
        if(empty($filter->errors)) {
            $uploader = new uploader($_FILES["document"]);
            $data = $request->getDataArray();
            
            $data["financialDocumentStatus"] = 1; //File uploaded
            $data["financialDocumentDateUploaded"] = date("Y-m-d H:i");
            $data["financialDocumentExtension"] = $uploader->getExtension();
            
            $doc = new model_financialDocument($request->id);
            $visa = new model_visa($doc->visaID);
            
            if($this->_db->update($data, "financial_documents", "financialDocumentID", $request->id) === true) {
    
                $uploader = new uploader($_FILES["document"]);
                $uploader->basePath = ADMIN_PATH . "/financial-documentation/" . $visa->ID;
                $uploader->newFilename = $doc->ID . $uploader->getExtension();
                
                if($uploader->transferFile()) {
                    $array["message"][] = "Document successfully uploaded";
                } else {
                    $array["message"][] = $uploader->error;
                }
                
                
                $array["message"][] = "Your request was successful";
                $array["status"] = true;
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
            case "default": return $this->updateFinancialDocument($request);
            case "upload-file": return $this->uploadFile($request);
        }
    }
    
    function deleteDocument(request $request) {
        
        $doc = new model_financialDocument($request->id);
        $visa = new model_visa($doc->visaID);


        $sql = "DELETE FROM financial_documents WHERE financialDocumentID=?";
        
        if($this->_db->delete($sql, "i", array($request->id)) === true) {
            
            
            $dir = ADMIN_PATH . "/financial-documentation/" . $visa->ID;
            $path =  $dir . "/" . $doc->ID . ".pdf";
            
            if(file_exists($path)) {
                $uploader = new uploader();
                $uploader->removeFile($path , $dir);
            }
            
            $array = [
                "status"=>true,
                "message"=>"Your request was successful"
            ];
        } else {
            $array = [
                "status" => false,
                "message" =>$this->deleteMsg($this->_db->dbError)
            ];
        }


        //Return json object as response
        $request->addResponseItems($array);
        return $request->response();
    }
    
    function delete(\request $request) {
        switch($request->subType) {
            case "default": return $this->deleteDocument($request);
        }
    }
}