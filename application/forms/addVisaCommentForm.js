[
    {
        "label":"Comment", 
        "name":"visaCommentText", 
        "element":"textarea",
        "classes": {
            "element": "form-control",
            "label": "label-control",
            "group": "form-group"
        },
        "validation": {
            "0": [
                {"name":"required", "value":true},
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
        "name":"addCommentSubmits"
    }
]
