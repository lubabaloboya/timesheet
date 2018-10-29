<?php

class model_companyDocumentation extends model {
    
    public $ID;
    public $companyID;
    public $name;
    public $extension;
    public $dateUploaded;
    
    protected $_table = "company_documentation";
    protected $_ref = "companyDocumentationID";
    protected $_controller = "admin";
    protected $_action = "company-documentation";
    
    function __construct($id = null) {
        parent::__construct($id);
        $this->setUp();
    }
    
    function setUp() {
        $this->ID                       = $this->result["companyDocumentationID"];
        $this->companyID                = $this->result["companyDocumentationCompanyID"];
        $this->name                     = $this->result["companyDocumentationName"];
        $this->extension                = $this->result["companyDocumentationExtension"];
        $this->dateUploaded             = $this->dateSetup($this->result["companyDocumentationDateUploaded"]);
    }
    
    protected function addCompanyDocumentForm(request $request) {
        $array["path"] = "/forms/uploadCompanyDocumentForm.js";
        $array = $this->formRender($array);        
        return $this->updateForm($array, $request);
    }
    protected function updateCompanyDocumentForm(request $request) {
        $array["path"] = "/forms/uploadCompanyDocumentForm.js";
        $array["values"] = $this->_db->fetch_all_stmt("SELECT companyDocumentationName FROM company_documentation WHERE companyDocumentationID=?", "i", array($request->id), true);
        $array = $this->formRender($array);        
        return $this->updateForm($array, $request);
    }
    
    function createForm(request $request) {
        switch($request->subType) {
            case "default": return $this->addCompanyDocumentForm($request);
            case "edit": return $this->updateCompanyDocumentForm($request);
        }
    }
    
    function addCompanyDocument($request) {
        $filter = new filter(array(
            "validFile"=>array($_FILES["document"], array("pdf","docx","doc","xlsx","jpeg","jpg","png"), 5)
        ));
        
        if(empty($filter->errors)) {
            
            $uploader = new uploader($_FILES["document"]);
            $data = $request->getDataArray();
            $data["companyDocumentationCompanyID"] = $request->id;
            $data["companyDocumentationExtension"] = $uploader->getExtension();
            
            if($this->_db->insert($data,"company_documentation") === true) {
                
                $company_docs = new model_companyDocumentation($this->_db->insert_id);
                $company = new model_company($company_docs->companyID);
                
                $uploader = new uploader($_FILES["document"]);
                $uploader->basePath = ADMIN_PATH . "/company-docs/" . $company->ID;
                $uploader->newFilename = $company_docs->ID . $uploader->getExtension();
                
                $array["status"] = true;
                
                if($uploader->transferFile()) {
                    $array["message"][] = "Document successfully uploaded";
                } else {
                    $array["status"] = false;
                    $array["message"][] = $uploader->error;
                }
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
        switch($request->subType) {
            case "default": return $this->addCompanyDocument($request);
        }
    }
    
    
    function readDocument(request $request) {
        $uploader = new uploader();
        
        $company_docs = new model_companyDocumentation($request->id);
        $company = new model_company($company_docs->companyID);
        
        $path = ADMIN_PATH . "/company-docs/" . $company->ID ."/". $company_docs->ID . $company_docs->extension; 
        
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
            case "default": return $this->readDocument($request);
        }
    }
    
    protected function updateCompanyDocument(request $request) {
        $obj = $request->getDataObj();

        if(!empty($obj)) {
            $data = $request->getDataArray();
            
            $company_docs = new model_companyDocumentation($request->id);
            $company = new model_company($company_docs->companyID);
                
            if($this->_db->update($data, "company_documentation", "companyDocumentationID", $request->id)) {
               
                $array = array(
                    "status"=>true,
                    "message"=>"Your request was successful"
                );
                
            } else {
                
                
                
            }
        } else {
            
            if($this->updateCompanyDocumentFile($request)) {
                $array = array(
                    "status"=>true,
                    "message"=>"Document successfully uploaded"
                );
            } else {
                $array = array(
                    "status"=>false,
                    "message"=>"Your request was unsuccessful"
                );
            }
            
        }
        
        $request->addResponseItems($array);
        return $request->response();
    }

    protected function updateCompanyDocumentFile($request) {

        $filter = new filter(array(
            "validFile"=>array($_FILES["document"], array("pdf","docx","doc","xlsx","jpeg","jpg","png"), 5)
        ));

        if(empty($filter->errors)) {
            $uploader = new uploader($_FILES["document"]);
            $data = $request->getDataArray();
            $data["companyDocumentationExtension"] = $uploader->getExtension();

            if($this->_db->update($data, "company_documentation", "companyDocumentationID", $request->id)) {

                $company_docs = new model_companyDocumentation($request->id);
                $company = new model_company($company_docs->companyID);
                
                $uploader = new uploader($_FILES["document"]);
                $uploader->basePath = ADMIN_PATH . "/company-docs/" . $company->ID;

                $oldFile = $uploader->basePath ."/" . $company_docs->ID . $uploader->getExtension();
                
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }

                $uploader->newFilename = $company_docs->ID . $uploader->getExtension();
                
                $array["status"] = true;
                
                if($uploader->transferFile()) {
                    return true;
                } else {
                    return false;
                }
            } else {
                $array = array(
                    "status"=>false,
                    "message"=>"Your request was unsuccessful"
                );
            }

        } else {

            return false;
            
        }

    }
    
    function update(request $request) {
        switch($request->subType) {
            case "default": return $this->updateCompanyDocument($request);
        }
    }
   
    function getCompanyDocs($companyID) {
        $array = array();
        
        $result = $this->_db->fetch_all_stmt("SELECT companyDocumentationID FROM company_documentation WHERE companyDocumentationCompanyID=?", "i", array($companyID));
        
        foreach($result as $v) {
            $array[] = new model_companyDocumentation($v["companyDocumentationID"]);
        }
        
        return $array;
    }

    function deleteDocument(request $request) {
        
        $company_docs = new model_companyDocumentation($request->id);
        $company = new model_company($company_docs->companyID);
        $uploader = new uploader();
        $path = ADMIN_PATH . "/company-docs/" . $company->ID . "/" ;

        $uploader->removeFile($path . "/" . $company_docs->ID . $company_docs->extension, $path);
        
        $sql = "DELETE FROM company_documentation WHERE companyDocumentationID=?";

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
       switch($request->subType) {
           case "default": return $this->deleteDocument($request);
       }
    }
    

}