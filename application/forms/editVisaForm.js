[
    {
        "label":"Expatriate", 
        "name":"visaExpatriateID", 
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
                {"name":"maxLength", "value":5}
            ]
        }
    },
    {
        "label":"Visa Type", 
        "name":"visaVisaTypeID", 
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
                {"name":"maxLength", "value":5}
            ]
        }
    },
    {
        "label":"Visa Status", 
        "name":"visaStatus", 
        "element":"select", 
        "classes": {
            "element": "form-control",
            "group":"form-group",
            "label":"label-control"
        },
        "validation": {
            "0": [
                {"name":"number", "value":true}
            ]
        }
    },
    {
        "label":"Visa Owner", 
        "name":"visaCreatedBy", 
        "element":"select", 
        "classes": {
            "element": "form-control",
            "group":"form-group",
            "label":"label-control"
        },
        "validation": {
            "0": [
                {"name":"number", "value":true}
            ]
        }
    },
    {
        "label":"Visa Host Country", 
        "name":"visaCountryID", 
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
        "label":"On Hold Date", 
        "name":"visaDateOnhold", 
        "element":"text", 
        "readonly":true,
        "classes": {
            "element": "form-control full-datepicker",
            "group":"form-group hide onholdVisa",
            "label":"label-control"
        },
        "validation": {
            "0": [
                {"name":"required", "value":true}
            ]
        }
    },
    {
        "label":"Appointment Date", 
        "name":"visaDateAppointment", 
        "element":"text", 
        "readonly":true,
        "classes": {
            "element": "form-control full-datepicker",
            "group":"form-group",
            "label":"label-control"
        }
    },
    {
        "label":"Date Submitted", 
        "name":"visaDateSubmitted", 
        "element":"text", 
        "readonly":true,
        "disabled":true,
        "classes": {
            "element": "form-control full-datepicker",
            "group":"form-group",
            "label":"label-control"
        }
    },
    {
        "label":"Date Declined", 
        "name":"visaDateDeclined", 
        "element":"text", 
        "readonly":true,
        "disabled":true,
        "classes": {
            "element": "form-control full-datepicker",
            "group":"form-group",
            "label":"label-control"
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
        "name":"addVisaSubmit"
    }
]