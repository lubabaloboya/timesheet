[    
    {
        "label":"Validate", 
        "name":"visaDocumentationStatus", 
        "element":"checkbox", 
        "classes": {
            "element": "form-control",
            "group":"form-group",
            "label":"label-control"
        },
        "checkboxes": {
            "0": [
                {"name":"Valid", "value":"2"},
                {"name":"Decline", "value":"3"}
            ]
        },
        "validation": {
            "0": [
                {"name":"required", "value":true}
            ]
        }
    },
    {
        "label":"Reason", 
        "name":"reason", 
        "element":"textarea",
        "disabled":true,
        "classes": {
            "element": "form-control",
            "group":"form-group",
            "label":"label-control"
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

