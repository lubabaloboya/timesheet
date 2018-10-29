[
    {
        "label":"Name", 
        "name":"visaDocumentationTypeName", 
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
        "label":"Description", 
        "name":"visaDocumentationTypeDescription", 
        "element":"textarea", 
        "classes": {
            "element": "form-control",
            "group":"form-group",
            "label":"label-control"
        },
        "validation": {
            "0": [
                {"name":"required", "value":true},
                {"name":"maxLength", "value":500}
            ]
        }
    },
    {
        "label":"Expiry", 
        "name":"visaDocumentationTypeExpiry", 
        "element":"checkbox", 
        "classes": {
            "element":"form-control",
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
                {"name":"required", "value":true},
                {"name":"number", "value":true},
                {"name":"maxLength", "value":1},
                {"name":"minLength", "value":1},
                {"name":"max", "value":1},
                {"name":"min", "value":0}
            ]
        }
    },
    {
        "label":"Phase", 
        "name":"visaDocumentationTypePhase", 
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
                {"name":"maxLength", "value":1}
            ]
        }
    },
    {
        "label":"Order Number", 
        "name":"visaDocumentationTypeOrder", 
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
        "name":"addVisaDocumentationType"
    }
]

