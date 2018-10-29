[
    {
        "label":"Name", 
        "name":"visaTypeName", 
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
        "label":"Abbreviation", 
        "name":"visaTypeAbreviation", 
        "element":"text", 
        "classes": {
            "element": "form-control",
            "group":"form-group",
            "label":"label-control"
        },
        "validation": {
            "0": [
                {"name":"required", "value":true}
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
        "label":"Alert", 
        "name":"visaTypeAlert", 
        "element":"text", 
        "classes": {
            "element": "form-control",
            "group":"form-group",
            "label":"label-control"
        },
        "validation": {
            "0": [
                {"name":"required", "value":true},
                {"name":"number", "value":true},
                {"name":"maxLength", "value":3},
                {"name":"min", "value":1}
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
        "name":"addVisaTypeeSubmit"
    }
]


