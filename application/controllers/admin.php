<?php

class controllers_admin extends library_controllers_adminController {
    
    function __construct() {
        parent::__construct();
    }

    function applicationUpdatesAjax() {
        if($this->_request->validSession() === true) {
            $array["list"][] = $this->getApplicationMessages();
            echo json_encode($array);
        } else {
            return http_response_code(401);
        }
    }
    
    function dashboardAjax() {
        //Create request from post
        if($this->pageHandler("admin", "dashboard") === true) {
                  
            $this->view->crud = $this->_acl->getCrudAccess($this->_currentUser);
            
            $dashboard = new model_dashboard();
            
            if($dashboard->management($this->_currentUser)) {
                $page = 'managementDashboard';
            } else if($dashboard->user($this->_currentUser)) {
                $page = 'userDashboard';
            } else if($dashboard->customer($this->_currentUser)) {
                $page = 'clientDashboard';
                $this->view->id = $this->_currentUser->companyID; 
            } 
            
            $this->view->dashboard = $dashboard;
            $this->view->user = $this->_currentUser;

            $this->view->setView(array(
                "page"=>"admin/" . $page,
                "template"=>"empty",
                "header"=>false
            ));
            $this->_request->addHTML($this->view->getView());
            echo $this->_request->response();
        }
    }
    
    function expatriateBreakdownAjax() {
        if($this->pageHandler("admin", "expatriate-breakdown") === true) {

            $dashboard = new model_dashboard();
            
            if($dashboard->management($this->_currentUser) === false) {
                throw new userException("You are not authorised to complete this action");
            }

            $this->view->setView(array(
                "page"=>"admin/graphs/expatriateBreakdown",
                "template"=>"empty",
                "header"=>false
            ));
            
            $this->_request->addHTML($this->view->getView());
            echo $this->_request->response();
            
        }
    }
       
    function getExpatriateDataAjax() {
        if($this->pageHandler("admin", "expatriate-breakdown") === true) {

            $dashboard = new model_dashboard();
            
            if($dashboard->management($this->_currentUser) === false) {
                throw new userException("You are not authorised to complete this action");
            }

            $this->_request->addResponseItems([
                "data" => $dashboard->getExptariateGeoBreakdown()
            ]);
            echo $this->_request->response();
            
        }
    }
    
    function expatriateTableBreakdownAjax() {
        if($this->pageHandler("admin", "expatriate-breakdown") === true) {

            $this->view->dashboard = new model_dashboard();
            
            if($this->view->dashboard->management($this->_currentUser) === false) {
                throw new userException("You are not authorised to complete this action");
            }

            $this->view->setView(array(
                "page"=>"admin/graphs/expatriateTableBreakdown",
                "template"=>"empty",
                "header"=>false
            ));
            
            $this->_request->addHTML($this->view->getView());
            echo $this->_request->response();
            
        }
    }
    
    
    function activeVisaSummaryAjax() {
        if($this->pageHandler("admin", "active-visa-summary") === true ){
            
            $this->view->dashboard = new model_dashboard();
                    
            if($this->view->dashboard->management($this->_currentUser) === false) {
                throw new userException("You are not authorised to complete this action");
            }
            
            $this->view->setView(array(
                "page"=>"admin/graphs/activeVisaSummary",
                "template"=>"empty",
                "header"=>false
            ));
            
            $this->_request->addHTML($this->view->getView());
            echo $this->_request->response();
        } 
    }
    
        
    
    function monthlyVisaBreakdownAjax() {
        if($this->pageHandler("admin", "visa-monthly-breakdown") === true) {

            $this->view->dashboard = new model_dashboard();
            
            if($this->view->dashboard->management($this->_currentUser) === false) {
                throw new userException("You are not authorised to complete this action");
            }

            $this->view->setView(array(
                "page"=>"admin/graphs/visaMonthlyBreakdown",
                "template"=>"empty",
                "header"=>false
            ));
            
            $this->_request->addHTML($this->view->getView());
            echo $this->_request->response();
            
        }
    }
 
    function getMonthlyVisaBreakdownAjax() {
        if($this->pageHandler("admin", "visa-monthly-breakdown") === true) {

            $dashboard = new model_dashboard();
            
            if($dashboard->management($this->_currentUser) === false) {
                throw new userException("You are not authorised to complete this action");
            }

            $this->_request->addResponseItems([
                "data" => $dashboard->monthlyVisaBreakdown()
            ]);
            echo $this->_request->response();
            
        }
    }
    
    function profileIndexAjax() {
         if($this->_request->validSession() === true) {
            //Create relevant model for CRUD operations
            $str = "model_" . $this->_request->action;
            $this->_model = new $str();
            switch($this->_request->type) {
                case "form": echo $this->_model->createForm($this->_request);
                   break;
                case "create": echo $this->_model->create($this->_request);
                    break;
                case "read":   
                        $jsonArray = $this->_model->read($this->_request);
                        if(is_array($jsonArray)) {
                            echo $this->read($jsonArray);
                        } else {
                            echo $jsonArray;      
                        }
                    break;
                case "update": echo $this->_model->update($this->_request);
                    break;
                case "delete": echo $this->_model->delete($this->_request);
                    break;
            }
        } else {
            return http_response_code(401);
        }
    }
    
    public function indexAction()
    {
        if(authentication::loggedIn() == true) {
            $this->view->paginator = true;
            $this->view->user = $this->_currentUser;
             
            $page = new pagination();
            $this->view->limits = $page->limits();
            
            $config = new model_configuration();

            $this->view->setView(array(
                "page"=>"admin/index",
                "template"=>"admin",
                "header"=>array(
                    "title"=>$config->siteTitle,
                    "theme"=>"backend",
                    "js"=>array("admin.js")
                )
            ));
            $this->view->render();
        } else {
            header("location: /");
        }
    }
    
    function profileAjax() {
        //Create request from post
        if($this->_request->validSession() == true) {
            $this->view->crud = $this->_acl->getCrudAccess($this->_currentUser);

            $user = new model_user();
            $user->getCurrentUser();

            $this->view->setView(array(
                "page"=>"admin/profileView",
                "template"=>"empty",
                "header"=>false
            ));
            
            $this->_request->addHTML($this->view->getView());
            echo $this->_request->response();
        } else {
            http_response_code(401);
        }
    }
    
    function profileDetailsAjax() {
         //Create request from post
        if($this->_request->validSession() == true) {
            
            $this->crud = $this->_acl->getCrudAccess($this->_currentUser);
            $this->view->model = $this->_currentUser;
            
            $icon = new library_decorators_button();
            $icon->isDisabled = $this->crud->update;
            $icon->addClass(array("edit", "edit-profile-details"));
            $icon->text = "Edit";
            $icon->addAttributes(array(
                "parent-value"=>$this->view->model->ID
            ));
            $icon->tooltip = array(
                "title"=>"Update user details",
                "place"=>"top"
            );
            $icon->icon = "pencil";

            $this->view->setView(array(
                "page"=>"admin/userDetails",
                "template"=>"empty",
                "header"=>false
            ));
            $this->_request->addHTML($this->view->getView());
            $this->_request->addResponseItems(array(
                "buttons"=>array(
                    $icon->getElement()
                )
            ));
            echo $this->_request->response();
        } else {
            return http_response_code(401);
        }
    }
    
    function expatriatesAjax() {
        //Create request from post
        if($this->pageHandler("admin", "expatriates") === true) {
                  
            $this->view->crud = $this->_acl->getCrudAccess($this->_currentUser);
            
            $obj = $this->_request->getDataObj();
            $array = null;
            $page = new pagination();
            $page->setLimit($obj->limit);
            $page->setOffset($obj->offset);
            
            if(isset($obj->search)) {
                $search = new search("*", "search");
                $search->createTempTable("SELECT visaID, userID, name, userSurname, username, companyName, expatriatePassportNumber FROM visas LEFT JOIN expatriates ON visaExpatriateID=expatriateID LEFT JOIN users ON expatriateUserID=userID INNER JOIN companies ON userCompanyID=companyID");
                $search->setFields(array(
                    "name", 
                    "userSurname", 
                    "username",
                    "expatriatePassportNumber",
                    "companyName"
                ));
                $array = $search->getResults($obj->search);
                $totalItems = count($search->getResults($obj->search));
            } else {
                
                $user = new model_user($this->_currentUser->ID);
                
                if ($user->roleID === 3) {
                    $page->query = "SELECT userID FROM users WHERE userRoleID=4 AND userCompanyID = " . $user->companyID . " ";
                } else {
                    $page->query = "SELECT userID FROM users WHERE userRoleID=4";
                }
                
                $totalItems = $page->totalItems($page->query);
            }

            $this->view->rows = $page->getPage($array);
            $this->view->request = $this->_request;
            
            $this->view->setView(array(
                "page"=>"admin/expatriates",
                "template"=>"empty",
                "header"=>false
            ));
            $this->_request->addHTML($this->view->getView());
            $this->_request->addResponseItems(array(
                "tabData"=>array(
                    array("key"=>"items", "value"=>$totalItems)
                )
            ));
            echo $this->_request->response();
        }
    }
    
    
    function expatriateDetailsAjax() {
         //Create request from post
         if($this->pageHandler("admin", "expatriates") === true) {
            
            $this->crud = $this->_acl->getCrudAccess($this->_currentUser);
            $this->view->model = new model_expatriate($this->_request->id);
            
            $icon = new library_decorators_button();
            $icon->isDisabled = $this->crud->update;
            $icon->addClass(array("edit", "edit-expatriate-details"));
            $icon->text = "Edit";
            $icon->tooltip = array(
                "title"=>"Update expatriate details",
                "place"=>"top"
            );
            $icon->icon = "pencil";

            $this->view->setView(array(
                "page"=>"admin/expatriateDetails",
                "template"=>"empty",
                "header"=>false
            ));
            $this->_request->addHTML($this->view->getView());
            $this->_request->addResponseItems(array(
                "buttons"=>array(
                    $icon->getElement()
                )
            ));
            echo $this->_request->response();
        } else {
            return http_response_code(401);
        }
    }
    
    function companiesAjax() {
        //Create request from post
        if($this->pageHandler("admin", "companies") === true) {
            $this->view->crud = $this->_acl->getCrudAccess($this->_currentUser);

            $button = new library_decorators_button();
            $button->addClass(array("add", "add-icon"));
            $button->addAttributes(array(
                "url"=>"/admin/index",
                "action"=>"company"
            ));
            $button->id = "addCompanyButton";
            $button->isDisabled = $this->view->crud->create;
            $button->icon = "plus-sign";
            $button->text = "Add";
            $button->tooltip = array(
                "title"=>"Create a new company",
                "place"=>"top"
            );
            
            $obj = $this->_request->getDataObj();
            $array = null;
            $page = new pagination();
            $page->setLimit($obj->limit);
            $page->setOffset($obj->offset);
            
            if(isset($obj->search)) {
                $search = new search("companyID", "companies");
                $search->setFields(array(
                    "companyName"
                ));
                $array = $search->getResults($obj->search);
                $totalItems = count($search->getResults($obj->search));
            } else {
                $page->query = "SELECT companyID FROM companies";
                $totalItems = $page->totalItems($page->query);
            }

            $this->view->rows = $page->getPage($array);
            $this->view->request = $this->_request;
            
            $this->view->setView(array(
                "page"=>"admin/companies",
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
    
    function companyDetailsAjax() {
         //Create request from post
        if($this->pageHandler("admin", "companies") === true) {
            
            $this->crud = $this->_acl->getCrudAccess($this->_currentUser);
            
            $button = new library_decorators_button();
            $button->isDisabled = $this->crud->update;
            $button->addClass(array("edit", "edit-company-details"));
            $button->text = "Edit";
            $button->tooltip = array(
                "title"=>"Edit company details",
                "place"=>"top"
            );
            $button->icon = "pencil";
            
            $this->view->model = new model_company($this->_request->id);
            
            $this->view->setView(array(
                "page"=>"admin/companyDetails",
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
    
    function companyDocumentsAjax() {
         //Create request from post
        if($this->pageHandler("admin", "company-documents") === true) {
            
            $this->crud = $this->_acl->getCrudAccess($this->_currentUser);
            $this->view->user = $this->_currentUser;
            
            $button = new library_decorators_button();
            $button->isDisabled = $this->crud->update;
            $button->addClass(array("add", "add-company-document"));
            $button->text = "Add";
            $button->tooltip = array(
                "title"=>"Add a new company document",
                "place"=>"top"
            );
            $button->icon = "plus";
            
            $company_docs = new model_companyDocumentation();
            $this->view->docs = $company_docs->getCompanyDocs($this->_request->id);
            
            $this->view->setView(array(
                "page"=>"admin/companyDocuments",
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

    function countriesAjax() {
        //Create request from post
        if($this->pageHandler("admin", "countries") === true) {
            $this->view->crud = $this->_acl->getCrudAccess($this->_currentUser);

            $this->view->request = $this->_request;
            $country = new model_country();
            $this->view->rows = $country->getCountries();

            $this->view->setView(array(
                "page"=>"admin/countries",
                "template"=>"empty",
                "header"=>false
            ));
            $this->_request->addHTML($this->view->getView());
            $this->_request->addResponseItems(array(
 
            ));
            echo $this->_request->response();
        }
    }

    function countryDetailsAjax() {
        //Create request from post
       if($this->pageHandler("admin", "country-details") === true) {

           $this->crud = $this->_acl->getCrudAccess($this->_currentUser);
           
           $this->view->model = new model_company($this->_request->id);
           
           $this->view->setView(array(
               "page"=>"admin/countryDetails",
               "template"=>"empty",
               "header"=>false
           ));
           $this->_request->addHTML($this->view->getView());
           $this->_request->addResponseItems(array(

           ));
           echo $this->_request->response();
       }
   }
}