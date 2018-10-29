[
    {
        "label":"Name", 
        "name":"name", 
        "element":"text", 
        "classes": {
            "element": "form-control",
            "label": "label-control",
            "group": "form-group"
        },
        "validation": {
            "0": [
                {"name":"required", "value":true}
            ]
        }
    },
    {
        "label":"Surname", 
        "name":"userSurname", 
        "element":"text", 
        "classes": {
            "element": "form-control",
            "label": "label-control",
            "group": "form-group"
        },
        "validation": {
            "0": [
                {"name":"required", "value":true}
            ]
        }
    },
    {
        "label":"Username", 
        "name":"username", 
        "element":"text", 
        "classes": {
            "element": "form-control",
            "label": "label-control",
            "group": "form-group"
        },
        "validation": {
            "0": [
                {"name":"required", "value":true}
            ]
        }
    },
    {
        "label":"Email", 
        "name":"userEmail", 
        "element":"text", 
        "classes": {
            "element": "form-control",
            "label": "label-control",
            "group": "form-group"
        },
        "validation": {
            "0": [
                {"name":"required", "value":true}
            ]
        }
    },
    {
        "label":"Company", 
        "name":"userCompanyID", 
        "element":"select", 
        "classes": {
            "element": "form-control required",
            "label": "label-control",
            "group": "form-group"
        }
    },
    {
        "label":"Role", 
        "name":"userRoleID", 
        "element":"select", 
        "classes": {
            "element": "form-control",
            "label": "label-control",
            "group": "form-group"
        },
        "validation": {
            "0": [
                {"name":"required", "value":true}
            ]
        }
    },
    {
        "label":"Active", 
        "name":"userStatus", 
        "element":"checkbox", 
        "classes": {
            "element": "form-control",
            "group":"form-group",
            "label":"label-control"
        },
        "checkboxes": {
            "0": [
                {"name":"Yes", "value":"1"},
                {"name":"No", "value":"0"}
            ]
        },
        "validation": {
            "0": [
                {"name":"required", "value":true}
            ]
        }
    },
    {
        "label":"Home Country", 
        "name":"expatriateHomeCountryID", 
        "element":"select", 
        "classes": {
            "element": "form-control",
            "group":"form-group hide expatriate",
            "label":"label-control"
        },
        "validation": {
            "0": [
                {"name":"required", "value":true},
                {"name":"number", "value":true},
                {"name":"maxLength", "value":9}
            ]
        }
    },
    {
        "label":"Host Country", 
        "name":"expatriateHostCountryID", 
        "element":"select", 
        "classes": {
            "element": "form-control",
            "group":"form-group hide expatriate",
            "label":"label-control"
        },
        "validation": {
            "0": [
                {"name":"number", "value":true},
                {"name":"maxLength", "value":9}
            ]
        }
    },
    {
        "label":"Passport Number", 
        "name":"expatriatePassportNumber", 
        "element":"text", 
        "classes": {
            "element": "form-control",
            "group":"form-group hide expatriate",
            "label":"label-control"
        },
        "validation": {
            "0": [
                {"name":"required", "value":true},
                {"name":"maxLength", "value":45}
            ]
        }
    },
        {
        "label":"Passport Expiry Date", 
        "name":"expatriatePassportExpiryDate", 
        "element":"text", 
        "readonly":true,
        "classes": {
            "element": "form-control full-datepicker",
            "group":"form-group hide expatriate",
            "label":"label-control"
        }
    },
    {
        "label":"Job Title", 
        "name":"expatriateJobTitle", 
        "element":"select", 
        "classes": {
            "element": "form-control changeable",
            "group":"form-group hide expatriate",
            "label":"label-control"
        },
        "validation": {
            "0": [
                {"name":"maxLength", "value":45}
            ]
        }
    },
    {
        "label":"Job Description", 
        "name":"expatriateJobDescription", 
        "element":"textarea", 
        "classes": {
            "element": "form-control",
            "group":"form-group hide expatriate",
            "label":"label-control"
        },
        "validation": {
            "0": [
                {"name":"maxLength", "value":500}
            ]
        }
    },
    {
        "label":"Send", 
        "element":"button", 
        "name":"editUserSubmit",
        "disabled":true,
        "classes": {
            "element": "btn btn-default form-btn",
            "label": "label-control"
        }
    }
]

