[
    {
        "label":"Home Country", 
        "name":"expatriateHomeCountryID", 
        "element":"select", 
        "classes": {
            "element": "form-control",
            "group":"form-group",
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
            "group":"form-group",
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
            "group":"form-group",
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
            "group":"form-group",
            "label":"label-control"
        }
    },
    {
        "label":"Job Title", 
        "name":"expatriateJobTitle", 
        "element":"select", 
        "classes": {
            "element": "form-control changeable",
            "group":"form-group",
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
            "group":"form-group",
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
        "disabled":true,
        "classes": {
            "element": "btn btn-default form-btn",
            "label": "label-control"
        },
        "name":"addExpatriateSubmit"
    }
]

