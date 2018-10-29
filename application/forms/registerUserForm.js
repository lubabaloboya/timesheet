[
    {
        "label":"Name", 
        "name":"name", 
        "element":"text",
        "classes": {
            "element": "form-control",
            "label": "label-control",
            "group": "form-group"
        },
        "validation": {
            "0": [
                {"name":"required", "value":true}
            ]
        }
    },
    {
        "label":"Surname", 
        "name":"userSurname", 
        "element":"text",
        "classes": {
            "element": "form-control",
            "label": "label-control",
            "group": "form-group"
        },
        "validation": {
            "0": [
                {"name":"required", "value":true}
            ]
        }
    },
    {
        "label":"Username", 
        "name":"username", 
        "element":"text",
        "classes": {
            "element": "form-control",
            "label": "label-control",
            "group": "form-group"
        },
        "validation": {
            "0": [
                {"name":"required", "value":true}
            ]
        }
    },
    {
        "label":"ID Number", 
        "name":"memberIDNumber", 
        "element":"text",
        "classes": {
            "element": "form-control",
            "label": "label-control",
            "group": "form-group"
        },
        "validation": {
            "0": [
                {"name":"required", "value":true}
            ]
        }
    },
    {
        "label":"Email", 
        "name":"userEmail", 
        "element":"text", 
        "classes": {
            "element": "form-control",
            "label": "label-control",
            "group": "form-group"
        },
        "validation": {
            "0": [
                {"name":"required", "value":true}
            ]
        }
    },
    {
        "label":"Confirm Email", 
        "name":"userConfirmEmail", 
        "element":"text", 
        "classes": {
            "element": "form-control",
            "label": "label-control",
            "group": "form-group"
        },
        "validation": {
            "0": [
                {"name":"required", "value":true},       
                {"name":"equalTo", "value":"#userEmail"}       
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
        "name":"registersUserSubmit"
    }
]

