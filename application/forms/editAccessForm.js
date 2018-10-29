[
    {
        "label":"Status", 
        "name":"status", 
        "element":"checkbox",
        "classes": {
            "element": "form-control",
            "group":"form-group",
            "label":"label-control"
        },
        "checkboxes": {
            "0": [
                {"name":"ON", "value":"1"},
                {"name":"OFF", "value":"0"}
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
        "name":"editAccessSubmit"
    }
]