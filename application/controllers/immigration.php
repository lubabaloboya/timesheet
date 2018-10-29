<?php

class controllers_immigration extends Controller {
    
    function __construct() {
        parent::__construct();
    }
    
    function visaTypesAjax() {
        if($this->pageHandler("immigration", "visa-types") === true) {
            $this->view->crud = $this->_acl->getCrudAccess($this->_currentUser);

            $button = new library_decorators_button();
            $button->addClass(array("add", "add-icon"));
            $button->addAttributes(array(
                "url"=>"/admin/index",
                "action"=>"visaType"
            ));
            $button->id = "addVisaType";
            $button->isDisabled = $this->view->crud->create;
            $button->icon = "plus-sign";
            $button->text = "Add";
            $button->tooltip = array(
                "title"=>"Create a new visa type",
                "place"=>"top"
            );
            
            $obj = $this->_request->getDataObj();
            $array = null;
            $page = new pagination();
            $page->setLimit($obj->limit);
            $page->setOffset($obj->offset);
            
            if(isset($obj->search)) {
                $search = new search("visaTypeID", "visa_types");
                $search->setFields(array(
                    "visaTypeName"
                ));
                $array = $search->getResults($obj->search);
                $totalItems = count($search->getResults($obj->search));
            } else {
                $page->query = "SELECT visaTypeID FROM visa_types";
                $totalItems = $page->totalItems($page->query);
            }

            $this->view->rows = $page->getPage($array);
            $this->view->request = $this->_request;
            
            $this->view->setView(array(
                "page"=>"immigration/visaTypes",
                "template"=>"empty",
                "header"=>false
            ));
            $this->_request->addHTML($this->view->getView());
            $this->_request->addResponseItems(array(
                "buttons"=>array(
                    $button->getElement()
                ),
                "tabData"=>array(
                    array("key"=>"items", "value"=>$totalItems)
                )
            ));
            echo $this->_request->response();
        }
    }
    
    function visaTypeDetailsAjax() {
         //Create request from post
        if($this->pageHandler("immigration", "visa-types-details") === true) {
            
            $this->crud = $this->_acl->getCrudAccess($this->_currentUser);
            
            $button = new library_decorators_button();
            $button->isDisabled = $this->crud->update;
            $button->addClass(array("edit", "edit-visa-type-details"));
            $button->text = "Edit";
            $button->tooltip = array(
                "title"=>"Update this visa types details",
                "place"=>"top"
            );
            $button->icon = "pencil";
            
            $this->view->model = new model_visaType($this->_request->id);
            $this->view->setView(array(
                "page"=>"immigration/visaTypeDetails",
                "template"=>"empty",
                "header"=>false
            ));
            $this->_request->addHTML($this->view->getView());
            $this->_request->addResponseItems(array(
                "buttons"=>array(
                    $button->getElement()
                )
            ));
            echo $this->_request->response();
        }
    }
    
    function visaDocumentationTypesAjax() {
         //Create request from post
        if($this->pageHandler("immigration", "visa-documentation-types") === true) {
            
            $this->crud = $this->_acl->getCrudAccess($this->_currentUser);
            
            $button = new library_decorators_button();
            $button->isDisabled = $this->crud->create;
            $button->addClass(array("add", "add-visa-documentation-type"));
            $button->text = "Add";
            $button->tooltip = array(
                "title"=>"Add Documentation to this visa type",
                "place"=>"top"
            );
            $button->icon = "plus-sign";
            
            $this->view->model = new model_visaType($this->_request->id);
            $this->view->setView(array(
                "page"=>"immigration/visaDocumentationType",
                "template"=>"empty",
                "header"=>false
            ));
            $this->_request->addHTML($this->view->getView());
            $this->_request->addResponseItems(array(
                "buttons"=>array(
                    $button->getElement()
                )
            ));
            echo $this->_request->response();
        }
    }
    
    function currentVisasAjax()  {
        if($this->pageHandler("immigration", "current-visas") === true) {
            $this->view->crud = $this->_acl->getCrudAccess($this->_currentUser);
            $this->view->user = $this->_currentUser;
            
            $button = new library_decorators_button();
            $button->addClass(array("add", "add-icon"));
            $button->addAttributes(array(
                "url"=>"/admin/index",
                "action"=>"visa",
                "event"=>"add-visa"
            ));
            $button->id = "addVisa";
            $button->isDisabled = $this->view->crud->create;
            $button->icon = "plus-sign";
            $button->text = "Add";
            $button->tooltip = array(
                "title"=>"Create a new visa",
                "place"=>"top"
            );
            
            $export = new library_decorators_button();
            $export->addClass(array("add", "export-current-visas"));
            $export->isDisabled = $this->view->crud->create;
            $export->icon = "plus-sign";
            $export->text = "Export Visas";
            $export->tooltip = array(
                "title"=>"Export list of visas that are currently in progress",
                "place"=>"top"
            );
            
            $obj = $this->_request->getDataObj();
            $array = null;
            $page = new pagination();
            $page->setLimit($obj->limit);
            $page->setOffset($obj->offset);
            
            if(isset($obj->search)) {
                //throw new userException("This service has been temporarily disabled");
                $search = new search("*", "search");
                $search->createTempTable("SELECT visaID, name, userSurname, username, companyName, expatriatePassportNumber FROM visas LEFT JOIN expatriates ON visaExpatriateID=expatriateID LEFT JOIN users ON expatriateUserID=userID INNER JOIN companies ON userCompanyID=companyID WHERE visaStatus < 8");
                $search->setFields(array(
                    "expatriatePassportNumber", 
                    "name", 
                    "userSurname", 
                    "username", 
                    "companyName"
                ));
                $array = $search->getResults($obj->search);
                $totalItems = count($search->getResults($obj->search));
                
            } else {
               
                if($this->_currentUser->roleID == 2) {
                    $page->types = "i";
                    $page->params = array($this->_currentUser->ID);
                    $page->query = "SELECT visaID FROM visas LEFT JOIN expatriates ON visaExpatriateID=expatriateID LEFT JOIN users ON expatriateUserID=userID WHERE visaCreatedBy=? AND visaStatus NOT BETWEEN 9 AND 11 ORDER BY visaStatus, visaID";
                } else if($this->_currentUser->roleID == 3) {
                    $page->types = "i";
                    $page->params = array($this->_currentUser->companyID);
                    $page->query = "SELECT visaID FROM visas LEFT JOIN expatriates ON visaExpatriateID=expatriateID LEFT JOIN users ON expatriateUserID=userID WHERE userCompanyID=? AND visaStatus NOT BETWEEN 9 AND 11 ORDER BY visaStatus, visaID";
                } else if($this->_currentUser->roleID == 4) {
                    $page->types = "i";
                    $page->params = array($this->_currentUser->ID);
                    $page->query = "SELECT visaID FROM visas LEFT JOIN expatriates ON visaExpatriateID=expatriateID LEFT JOIN users ON expatriateUserID=userID WHERE userID=? AND visaStatus < 9 ORDER BY visaStatus, visaID";
                } else {
                    $page->query = "SELECT visaID FROM visas WHERE visaStatus > 10 OR visaStatus < 9 ORDER BY visaStatus, visaID";
                }
                
                $totalItems = $page->totalItems($page->query);
            }

            $this->view->rows = $page->getPage($array);
            $this->view->request = $this->_request;
            
            $this->view->setView(array(
                "page"=>"immigration/visas",
                "template"=>"empty",
                "header"=>false
            ));
            $this->_request->addHTML($this->view->getView());
            $this->_request->addResponseItems(array(
                "buttons"=>array(
                    $button->getElement(),
                    $export->getElement()
                ),
                "tabData"=>array(
                    array("key"=>"items", "value"=>$totalItems)
                )
            ));
            echo $this->_request->response();
        }
    }
    
    function visasAjax()  {
        if($this->pageHandler("immigration", "visas") === true) {
            $this->view->crud = $this->_acl->getCrudAccess($this->_currentUser);
            $this->view->user = $this->_currentUser;
            
            $export = new library_decorators_button();
            $export->addClass(array("add", "export-completed-visas"));
            $export->isDisabled = $this->view->crud->create;
            $export->icon = "plus-sign";
            $export->text = "Export Visas";
            $export->tooltip = array(
                "title"=>"Export list of completed visas",
                "place"=>"top"
            );
            
            $obj = $this->_request->getDataObj();
            $array = null;
            $page = new pagination();
            $page->setLimit($obj->limit);
            $page->setOffset($obj->offset);
            
            if(isset($obj->search)) {
                $search = new search("*", "search");
                $search->createTempTable("SELECT visaID, name, userSurname, username, companyName, expatriatePassportNumber FROM visas LEFT JOIN expatriates ON visaExpatriateID=expatriateID LEFT JOIN users ON expatriateUserID=userID INNER JOIN companies ON userCompanyID=companyID");
                $search->setFields(array(
                    "name", 
                    "userSurname", 
                    "username", 
                    "companyName",
                    "expatriatePassportNumber"
                ));
                $array = $search->getResults($obj->search);
                $totalItems = count($search->getResults($obj->search));
                
            } else {
                
                if($this->_currentUser->roleID == 2) {
                    $page->types = "i";
                    $page->params = array($this->_currentUser->ID);
                    $page->query = "SELECT visaID FROM visas LEFT JOIN expatriates ON visaExpatriateID=expatriateID LEFT JOIN users ON expatriateUserID=userID WHERE visaCreatedBy=? ORDER BY visaStatus, visaID";
                } else if($this->_currentUser->roleID == 3) {
                    $page->types = "i";
                    $page->params = array($this->_currentUser->companyID);
                    $page->query = "SELECT visaID FROM visas LEFT JOIN expatriates ON visaExpatriateID=expatriateID LEFT JOIN users ON expatriateUserID=userID WHERE userCompanyID=? ORDER BY visaStatus, visaID";
                } else if($this->_currentUser->roleID == 4) {
                    $page->types = "i";
                    $page->params = array($this->_currentUser->ID);
                    $page->query = "SELECT visaID FROM visas LEFT JOIN expatriates ON visaExpatriateID=expatriateID LEFT JOIN users ON expatriateUserID=userID WHERE userID=? ORDER BY visaStatus, visaID";
                } else {
                    $page->query = "SELECT visaID FROM visas ORDER BY visaStatus, visaID";
                }
                
                $totalItems = $page->totalItems($page->query);
            }

            $this->view->rows = $page->getPage($array);
            $this->view->request = $this->_request;
            
            $this->view->setView(array(
                "page"=>"immigration/visas",
                "template"=>"empty",
                "header"=>false
            ));
            $this->_request->addHTML($this->view->getView());
            $this->_request->addResponseItems(array(
                "buttons"=>array(
                    $export->getElement()
                ),
                "tabData"=>array(
                    array("key"=>"items", "value"=>$totalItems)
                )
            ));
            echo $this->_request->response();
        }
    }
    
    
    function visaDetailsAjax() {
         //Create request from post
        if($this->pageHandler("immigration", "visas") === true) {
            
            $this->crud = $this->_acl->getCrudAccess($this->_currentUser);
            
            $button = new library_decorators_button();
            $button->isDisabled = $this->crud->update;
            $button->addClass(array("edit", "edit-visa-details"));
            $button->text = "Edit";
            $button->tooltip = array(
                "title"=>"Update this visa details",
                "place"=>"top"
            );
            $button->icon = "pencil";
            
            $this->view->model = new model_visa($this->_request->id);
            $this->view->user = $this->_currentUser;
            
            $this->view->setView(array(
                "page"=>"immigration/visaDetails",
                "template"=>"empty",
                "header"=>false
            ));
            $this->_request->addHTML($this->view->getView());
            $this->_request->addResponseItems(array(
                "buttons"=>array(
                    $button->getElement()
                )
            ));
            echo $this->_request->response();
        }
    }
    
    function visaDocumentationAjax() {
         //Create request from post
        if($this->pageHandler("immigration", "visa-documentation") === true) {
            
            $this->crud = $this->_acl->getCrudAccess($this->_currentUser);
            $this->view->user = $this->_currentUser;
            
            $this->view->visa = new model_visa($this->_request->id);
            $this->view->docs = new model_visaDocumentation();
            $this->view->reminders = new model_documentReminder();
            
            $this->view->setView(array(
                "page"=>"immigration/visaDocumentation",
                "template"=>"empty",
                "header"=>false
            ));
            $this->_request->addHTML($this->view->getView());
            echo $this->_request->response();
        }
    }
    
    function visaSpecialDocumentsAjax() {
         //Create request from post
        if($this->pageHandler("immigration", "special-documents") === true) {
            
            $this->crud = $this->_acl->getCrudAccess($this->_currentUser);
            $this->view->user = $this->_currentUser;
            
            $button = new library_decorators_button();
            $button->isDisabled = $this->crud->update;
            $button->addClass(array("add", "add-special-document"));
            $button->text = "Add";
            $button->tooltip = array(
                "title"=>"Add a new special document",
                "place"=>"top"
            );
            $button->icon = "plus";
            
            $this->view->model = new model_visa($this->_request->id);
            
            $this->view->setView(array(
                "page"=>"immigration/visaSpecialDocuments",
                "template"=>"empty",
                "header"=>false
            ));
            $this->_request->addHTML($this->view->getView());
            $this->_request->addResponseItems(array(
                "buttons"=>array(
                    $button->getElement()
                )
            ));
            echo $this->_request->response();
        }
    }
    
    function visaFinancialDocumentsAjax() {
         //Create request from post
        if($this->pageHandler("immigration", "financial-documents") === true) {
            
            $this->crud = $this->_acl->getCrudAccess($this->_currentUser);
            $this->view->user = $this->_currentUser;
            
            $button = new library_decorators_button();
            $button->isDisabled = $this->crud->update;
            $button->addClass(array("add", "add-financial-document"));
            $button->text = "Add";
            $button->tooltip = array(
                "title"=>"Add a new financial document",
                "place"=>"top"
            );
            $button->icon = "plus";
            
            $this->view->model = new model_visa($this->_request->id);
            
            $this->view->setView(array(
                "page"=>"immigration/visaFinancialDocuments",
                "template"=>"empty",
                "header"=>false
            ));
            $this->_request->addHTML($this->view->getView());
            $this->_request->addResponseItems(array(
                "buttons"=>array(
                    $button->getElement()
                )
            ));
            echo $this->_request->response();
        }
    }
    
    function visaCompanyDocumentationAjax() {
    
        if($this->pageHandler("immigration", "visa-company-documentation") === true) {
            
            $this->crud = $this->_acl->getCrudAccess($this->_currentUser);
            $this->view->user = $this->_currentUser;
            
            $visa = new model_visa($this->_request->id);
            $expatriate = new model_expatriate($visa->expatriateID);
            $user = new model_user($expatriate->userID);
            
            $company_docs = new model_companyDocumentation();
            $this->view->docs = $company_docs->getCompanyDocs($user->companyID);

            $this->view->setView(array(
                "page"=>"immigration/visaCompanyDocumentation",
                "template"=>"empty",
                "header"=>false
            ));
            $this->_request->addHTML($this->view->getView());
            echo $this->_request->response();
        }
    }

    function visaCommentsAjax() {
         //Create request from post
        if($this->pageHandler("immigration", "visa-comments") === true) {
            
            $this->crud = $this->_acl->getCrudAccess($this->_currentUser);
            
            $button = new library_decorators_button();
            $button->isDisabled = $this->crud->update;
            $button->addClass(array("add", "add-visa-comment"));
            $button->addAttributes(array(
                "url"=>"/immigration/index",
                "action"=>"visaComment"
            ));
            $button->text = "Add";
            $button->tooltip = array(
                "title"=>"Add Comment",
                "place"=>"top"
            );
            $button->icon = "plus";

            $this->view->model = new model_visa($this->_request->id);
            $this->view->setView(array(
                "page"=>"immigration/visaComments",
                "template"=>"empty",
                "header"=>false
            ));
            $this->_request->addHTML($this->view->getView());
            $this->_request->addResponseItems(array(
                "buttons"=>array(
                    $button->getElement()
                )
            ));
            echo $this->_request->response();
        }
    }
    
    function visaProgressAjax() {
         //Create request from post
        if($this->pageHandler("immigration", "visas") === true) {

            $this->view->model = new model_visa($this->_request->id);
            
            $this->view->setView(array(
                "page"=>"immigration/visaProgress",
                "template"=>"empty",
                "header"=>false
            ));
            $this->_request->addHTML($this->view->getView());
            echo $this->_request->response();
        }
    }
    
    function visaDocumentsChecklistAjax() {
        //Create request from post
        if ($this->pageHandler("immigration", "visa-documents-checklist") === true) {
            
            $visa = new model_visa($this->_request->id);
            $expatriate = new model_expatriate($visa->expatriateID);
            $user = new model_user($expatriate->userID);

            $this->view->visa = $visa;
            $this->view->expatriate = $expatriate;
            $this->view->user = $user;

            $visa_type_model = new model_visaType();
            $this->view->visa_type_name = $visa_type_model->getVisaTypeName($visa->visaTypeID);

            $visa_documentation_type_model = new model_visaDocumentationType();
            $this->view->visa_documentation_types = $visa_documentation_type_model->getDocumentChecklist($visa->ID);

            $button = new library_decorators_button();
            $button->addClass(array("download", "panel-button"));
            $button->icon = "print";
            $button->isDisabled = $this->view->crud->read;

            $button->id = "downloadCheckList";
            $button->text = "Download Checklist";
            $button->tooltip = array(
              "title" => "Print the Checklist",
              "place" => "top"
            );

            $this->_request->addResponseItems(array(
                "buttons" => array(
                  $button->getElement()
                )
            ));

            $this->view->setView(array(
                "page"=>"immigration/visaDocumentsChecklist",
                "template"=>"empty",
                "header"=>false
            ));

            $this->_request->addHTML($this->view->getView());
            echo $this->_request->response();

       }
    }


    function downloadVisaDocumentsChecklistAjax() {
        //Create request from post
        if ($this->pageHandler("immigration", "download-visa-documents-checklist") === true) {
            
            $visa = new model_visa($this->_request->id);
            $expatriate = new model_expatriate($visa->expatriateID);
            $user = new model_user($expatriate->userID);

            $this->view->visa = $visa;
            $this->view->expatriate = $expatriate;
            $this->view->user = $user;            

            $visa_type_model = new model_visaType();
            $this->view->visa_type_name = $visa_type_model->getVisaTypeName($visa->visaTypeID);

            $visa_documentation_type_model = new model_visaDocumentationType();
            $this->view->visa_documentation_types = $visa_documentation_type_model->getDocumentChecklist($visa->ID);

            $this->view->setView(array(
                "page"     => "immigration/visaDocumentsChecklist",
                "template" => "empty",
                "header"   => false
            ));

                      
            $doc = new documentation();
            $doc->setFilename("Visa Documents Checklist");
            $doc->setOptions(array(
              "dpi"           => 600,
              "orientation"   => "portrait",
              "margin-top"    => "0.5cm",
              "margin-bottom" => "0.5cm",
            ));
            $doc->setCSS(array(
              "/css/bootstrap.css",
              "/themes/backend/printables.css"
            ));
            $doc->setJavascript(array(
              "/plugins/jquery-1.12.0.min.js",
              "/plugins/isarray_functions.js"
            ));
            $url = $doc->saveTempPDF($this->view->getView());
            
            echo json_encode(array(
              "status" => true,
              "href"   => $url
            ));
       }
    }



    
    function visaDocumentReminderAjax() {
        //Create request from post
        if($this->pageHandler("immigration", "visa-document-reminder") === true) {

        $this->crud = $this->_acl->getCrudAccess($this->_currentUser);
        $this->view->user = $this->_currentUser;

        $this->view->reminders = new model_documentReminder();
        $this->view->userObj = new model_user();
        
        $this->view->setView(array(
            "page"=>"immigration/visaDocumentReminder",
            "template"=>"empty",
            "header"=>false
        ));

        $this->_request->addHTML($this->view->getView());
        echo $this->_request->response();

       }
   }


}