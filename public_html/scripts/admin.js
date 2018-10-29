$(document).ready(function() {

    //Initialise main tabs for navigation purposes
    $("body").off("click", ".tab-creator");
    $("body").on("click", ".tab-creator", function(e) {
        var options = {
            title: $(e.currentTarget).attr("tab-title"),
            url: "",
            search: true,
            page: 1,
            identifier: "menu",
            limit: $("select.limit").val() == "all" ? 1 : $("select.limit").val(),
            icon: ""
        }
        
        switch(e.currentTarget.id) {
            case "dashboard":
                options.url = "/admin/dashboard";
                options.search = false;
                options.page = false;
                options.limit = false;
                options.updateSuccess = function(data) {
                    getPanelDetails("/admin/expatriate-breakdown", "#expatriateBreakdown", data.uniqueID);
                    getPanelDetails("/admin/expatriate-table-breakdown", "#expatriateTableBreakdown", data.uniqueID);
                    getPanelDetails("/admin/monthly-visa-breakdown", "#monthlyVisaBreakdown", data.uniqueID);
                    getPanelDetails("/admin/active-visa-summary", "#activeVisaSummary", data.uniqueID);
                }
                break
            case "users":
                options.url = "/admin/users";
                break
            case "companies":
                options.url = "/admin/companies";
                options.button = false;
                break
            case "expatriates":
                options.url = "/admin/expatriates";
                options.button = false;
                break
            case "countries":
                options.url = "/admin/countries";
                options.button = false;
                break
            case "viewMessages":
                options.url = "/admin/view-messages";
                break
            case "roles":
                options.url = "/admin/roles";
                options.button = false;
                break
            case "access":
                options.url = "/admin/access";
                options.button = false;
                break;
            case "visaTypes":
                options.url = "/immigration/visa-types";
                options.button = false;
                break
            case "visas":
                options.url = "/immigration/visas";
                options.button = false;
                break
            case "currentVisas":
                options.url = "/immigration/current-visas";
                options.button = false;
                break
            case "documentReminder":
                options.url = "/immigration/visa-document-reminder";
                options.button = false;
                break
            case "profile":
                options.url = "/admin/profile";
                options.search = false;
                options.page = false;
                options.limit = false;
                options.updateSuccess = function(data) {
                    getPanelDetails("/admin/profile-details", "#profileDetails", data.uniqueID);
                }
                break
        }
        
        var tab = new isarray_tabs(options);
        
        if(tab.checkForDuplicateTab(options.title, options.identifier)) {
            if(!$(e.currentTarget).hasClass("disabled")) {
                tab.addTab();
                tab.updateTab();
            }
        }
    });
    
    
    $("body").off("click", ".view-icon");
    $("body").on("click", ".view-icon", function(e) {
        
        var options = {
            type:"read",
            title: $(e.currentTarget).closest("tr").find("td.tab-title").text(),
            url: $(e.currentTarget).attr("data-url"),
            uniqueID: $(".nav-tabs li.active").attr("id"),
            identifier: "view",
            action: $(e.currentTarget).attr("data-action"),
            val: $(e.currentTarget).attr("data-value")
        }
        console.log($(e.currentTarget).attr("data-id"));
        switch($(e.currentTarget).attr("data-id")) {
            case "viewUser":
                options.updateSuccess = function(data) {
                    getPanelDetails("/admin/user-details", "#userDetails", data.uniqueID);
                }
                break;
            case "viewExpatriate":
                options.updateSuccess = function(data) {
                    getPanelDetails("/admin/expatriate-details", "#expatriateDetails", data.uniqueID);
                }
                break;
            case "viewVisaType":
                options.updateSuccess = function(data) {
                    getPanelDetails("/immigration/visa-type-details", "#visaTypeDetails", data.uniqueID);
                    getPanelDetails("/immigration/visa-documentation-types", "#visaDocumentationTypes", data.uniqueID);
                }
                break;
            case "viewVisa":
                options.updateSuccess = function(data) {
                    getPanelDetails("/immigration/visa-details", "#visaDetails", data.uniqueID);
                    getPanelDetails("/immigration/visa-documentation", "#visaDocumentation", data.uniqueID);
                    getPanelDetails("/immigration/visa-comments", "#visaComments", data.uniqueID);
                    getPanelDetails("/immigration/visa-progress", "#visaProgress", data.uniqueID);
                    getPanelDetails("/immigration/visa-special-documents", "#visaSpecialDocuments", data.uniqueID); 
                    getPanelDetails("/immigration/visa-financial-documents", "#visaFinancialDocuments", data.uniqueID);
                    getPanelDetails("/immigration/visa-company-documentation", "#visaCompanyDocumentation", data.uniqueID);
                }
                break;
            case "viewCompany":
                options.updateSuccess = function(data) {
                    getPanelDetails("/admin/company-details", "#companyDetails", data.uniqueID);
                    getPanelDetails("/admin/company-documents", "#companyDocuments", data.uniqueID);
                }
                break;
            case "viewCountry":
                options.updateSuccess = function(data) {
                    getPanelDetails("/admin/country-details", "#viewCountry", data.uniqueID);
                }
                break;
        }
        
        var tab = new isarray_tabs(options);
 
        if(tab.checkForDuplicateTab(options.title, options.identifier)) {
            if(!$(e.currentTarget).hasClass("disabled")) {
                tab.addTab();
                tab.updateTab();
            }
        }
    });
    
    $(".nav-tabs").on("beforeTabAdded afterTabAdded beforeRemoveTab afterRemoveTab show.bs.tab shown.bs.tab", function(e, passedEvent) {
        
        switch(e.type) {
            case "beforeTabAdded":
                break;
            case "afterTabAdded":
                break;
            case "beforeRemoveTab":
                break;
            case "afterRemoveTab":
                break;
            case "show":
                break;
            case "shown":
                var currentTab = $(e.target).parent();
                var tab = new isarray_tabs();
                tab.uniqueID = currentTab.attr("id").replace("tab-", "")
                tab.updateTab();
                break;
        }
    });
});





