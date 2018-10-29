[
    {
        "label":"Name", 
        "name":"financialDocumentName", 
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
        "label":"Status", 
        "name":"financialDocumentStatus", 
        "element":"checkbox", 
        "classes": {
            "element": "form-control",
            "group":"form-group",
            "label":"label-control"
        },
        "checkboxes": {
            "0": [
                {"name":"Approve", "value":"3"},
                {"name":"Decline", "value":"0"}
            ]
        },
        "validation": {
            "0": [
                {"name":"required", "value":true}
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
        "name":"addRoleSubmit"
    }
]

