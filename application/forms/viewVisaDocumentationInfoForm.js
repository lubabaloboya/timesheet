[
    {
        "label":"", 
        "name":"visaDocumentationTypeDescription", 
        "element":"textarea",
        "disabled":true,
        "classes": {
            "element": "form-control",
            "label": "label-control",
            "group": "form-group"
        },
        "validation": {
            "0": [
                {"name":"required", "value":true},
                {"name":"maxLength", "value":1000},
                {"name":"rows", "value": "10"},
                {"name":"cols", "value": "30"}
            ]
        }
    },
    {
        "label":"Send", 
        "element":"button", 
        "disabled":true,
        "display": "none",
        "classes": {
            "element": "btn btn-default form-btn document-information",
            "label": "label-control"
        },
        "name":"addCommentSubmits"
    }
]
