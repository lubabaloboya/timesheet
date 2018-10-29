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
        "name":"specialDocumentDateExpiry", 
        "element":"text",
        "readonly":true,
        "classes": {
            "element": "form-control future-datepicker",
            "group":"form-group",
            "label":"label-control"
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