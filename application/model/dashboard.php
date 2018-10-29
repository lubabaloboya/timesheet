<?php

class model_dashboard extends Model {
    
    function __construct() {
        parent::__construct();
    }
    
    function getExpats() {
        
    }
    
    function getExptariateGeoBreakdown() {
        
        $sql = "SELECT countryName, count(expatriateHomeCountryID) as totalExpats FROM `expatriates` INNER JOIN countries ON `expatriateHomeCountryID` = countryID GROUP BY `expatriateHomeCountryID`";
        $header = [
            ['Country', 'Expats'],
        ];
        $array = $this->_db->fetch_all_stmt($sql);
        $countries = [];
        foreach($array as $v) {
            $countries[] = array($v["countryName"], $v["totalExpats"]);
        }
        
        return array_merge($header, $countries);
    }
    
    function getExpatriateTableBreakdown() {
        $sql = "SELECT countryName, count(expatriateHomeCountryID) as totalExpats FROM `expatriates` INNER JOIN countries ON `expatriateHomeCountryID` = countryID GROUP BY `expatriateHomeCountryID` ORDER BY totalExpats DESC LIMIT 10";
        
        return $this->_db->fetch_all_stmt($sql);
    }
    
    function management(model_user $user) {
        return $user->roleID === 1;
    }
    
    function user(model_user $user) {
        return $user->roleID === 2;
    }
    
    function customer(model_user $user) {
        return $user->roleID === 3;
    }
    
    function getTotalExpats() {
        $sql = "SELECT expatriateID FROM `expatriates`";
        
        return $this->_db->rows($sql);
    }
        
    // Only count the company if it is not work permits
    function getTotalCustomers() {
        $sql = "SELECT companyID FROM `companies` WHERE companyID > 1";
        
        return $this->_db->rows($sql);
    }
    
    function getTotalOpenVisas() {
        $sql = "SELECT visaID FROM `visas` WHERE visaStatus < 9 OR visaStatus > 10";
        
        return $this->_db->rows($sql);
    }
    
    function getTotalCompletedVisas() {
        $sql = "SELECT visaID FROM `visas` WHERE visaStatus = 9 OR visaStatus = 10";
        
        return $this->_db->rows($sql);
    }
    
    function getTotalIssuedVisas() {
        $sql = "SELECT visaID FROM `visas` WHERE visaStatus = 9";
        
        return $this->_db->rows($sql);
    }
    
    function getTotalOpenVisasByCompany($id) {
        $sql = "SELECT visaID FROM visas INNER JOIN expatriates ON visaExpatriateID=expatriateID INNER JOIN users on expatriateUserID=userID WHERE userCompanyID = " . $id . " AND visaStatus NOT BETWEEN 9 AND 11";
         
        return $this->_db->rows($sql);
    }
    
    function getTotalCompletedVisasByCompany($id) {
        $sql = "SELECT visaID FROM visas INNER JOIN expatriates ON visaExpatriateID=expatriateID INNER JOIN users on expatriateUserID=userID WHERE userCompanyID = " . $id . " AND visaStatus BETWEEN 9 AND 10";
         
        return $this->_db->rows($sql);
    }
    
    function getTotalDeniedVisas() {
        $sql = "SELECT visaID FROM `visas` WHERE visaStatus = 10";
        
        return $this->_db->rows($sql);
    }
    
    function monthlyVisaBreakdown() {

        $data[] = ['Month', date('Y') . ' Visas'];
        
        $results = $this->_db->fetch_all_stmt("SELECT DATE_FORMAT(`visaDateCreated`, '%M') as month, count(visaID) as total FROM `visas` WHERE DATE_FORMAT(`visaDateCreated`, '%Y') = DATE_FORMAT(CURDATE(), '%Y') GROUP BY DATE_FORMAT(`visaDateCreated`, '%M') ORDER BY visaDateCreated");
        
        foreach($results as $v) {
            $data[] = array($v["month"], $v["total"]);
        }
        
        return $data;
        
    }
    
    function getActiveVisas () {     
        
        $results = $this->_db->fetch_all_stmt("SELECT visaCreatedBy, count(*) as 'total',visaCreatedBy, visaStatus FROM visas WHERE visaStatus NOT BETWEEN 9 AND 11 GROUP BY visaCreatedBy");
        $visas = $this->_db->fetch_all_stmt("SELECT COUNT(*) 'duplicate', visaCreatedBy, visaStatus FROM visas WHERE visaStatus NOT BETWEEN 9 AND 11 GROUP BY visaCreatedBy, visaStatus HAVING COUNT(*) > 0 ORDER BY COUNT(*) DESC");
        
        foreach ($results as $key => $value) {
            $results[$key]["status"] = [];
            foreach ($visas as $visa) {
                if($value['visaCreatedBy'] === $visa['visaCreatedBy']) {
                    $results[$key]["status"] [] = $visa;
                }
            }
        }

        return $results;
    }
    
    function getOpenVisasByCompany ($company_id) {
        $results = $this->_db->fetch_all_stmt("SELECT visaID, visaExpatriateID, visaVisaTypeID, visaStatus, CONCAT(name, ' ', userSurname) as name, visaTypeName FROM visas INNER JOIN expatriates ON visaExpatriateID=expatriateID INNER JOIN users ON expatriateUserID=userID INNER JOIN visa_types ON visaVisaTypeID=visaTypeID WHERE userCompanyID= " . $company_id . " AND (visaStatus < 9 OR visaStatus = 12)");
        
        $open_visas = [];
        foreach($results as $k => $v) {
            $open_visas[$v["visaTypeName"]][] = $v;
        }
        return $open_visas;
    }
    
    function getCompletedVisasByCompany ($id) {
        $results = $this->_db->fetch_all_stmt("SELECT visaID, visaExpatriateID, DATEDIFF(visaDateExpiry, NOW()) as days, visaDateExpiry, CONCAT(name, ' ', userSurname) as name, visaTypeName FROM visas INNER JOIN expatriates ON visaExpatriateID=expatriateID lEFT JOIN users ON expatriateUserID=userID INNER JOIN visa_types ON visaVisaTypeID=visaTypeID WHERE userCompanyID= " . $id . " AND visaStatus = 9");
        
        return $results ? $results : [];
    }
 
}
