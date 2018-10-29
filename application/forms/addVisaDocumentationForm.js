[
    {
        "label":"Document", 
        "name":"document", 
        "element":"file", 
        "classes": {
            "element": "form-control no-post",
            "group":"form-group",
            "label":"label-control"
        },
        "validation": {
            "0": [
                {"name":"maxSize", "value":5},
                {"name":"extension", "value":"pdf|PDF|docx|DOCX|doc|DOC|xlsx|XLSX|jpeg|JPEG|jpg|JPG|png|PNG"}
            ]
        }
    },
    {
        "label":"Expiry", 
        "name":"visaDocumentationDateExpiry", 
        "element":"text",
        "readonly":true,
        "classes": {
            "element": "form-control full-datepicker",
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
        "label":"Document Not Required", 
        "name":"visaDocumentationNotRequired", 
        "element":"checkbox",
        "value": 0,
        "classes": {
            "element": "form-control changed",
            "group":"form-group",
            "label":"label-control"
        },
        "checkboxes": {
            "0": [
                {"name":"Yes", "value":1},
                {"name":"No", "value":0}
            ]
        }
    },
    {
        "label":"Upload", 
        "element":"button",
        "disabled":true,
        "classes": {
            "element": "btn btn-default form-btn",
            "label": "label-control"
        },
        "name":"uploadVisaDocumentSubmit"
    }
]