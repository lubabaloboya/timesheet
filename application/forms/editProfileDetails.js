[
    {
        "label":"Username", 
        "name":"username", 
        "element":"text", 
        "classes": {
            "element": "form-control required",
            "group":"form-group",
            "label":"label-control"
        }
    },
    {
        "label":"Name", 
        "name":"name", 
        "element":"text", 
        "classes": {
            "element": "form-control required",
            "group":"form-group",
            "label":"label-control"
        }
    },

    {
        "label":"Surname", 
        "name":"userSurname", 
        "element":"text", 
        "classes": {
            "element": "form-control required",
            "group":"form-group",
            "label":"label-control"
        }
    },
    {
        "label":"New Password", 
        "name":"userPassword", 
        "element":"password", 
        "classes": {
            "element": "form-control",
            "group":"form-group",
            "label":"label-control"
        },
        "validation": {
            "0": [
                {"name":"containNumber", "value": true},
                {"name":"containChar", "value": true},
                {"name":"containAlpha", "value": true},
                {"name":"containUpper", "value": true},
                {"name":"minlength", "value":8}
            ]
        }
    },
    {
        "label":"Confirm Password", 
        "name":"confirmPassword", 
        "element":"password", 
        "classes": {
            "element": "form-control",
            "group":"form-group",
            "label":"label-control"
        },
        "validation": {
            "0": [
                {"name":"equalTo", "value":"#userPassword"}
            ]
        }
    },
    {
        "label":"Email", 
        "name":"userEmail", 
        "element":"text", 
        "classes": {
            "element": "form-control required",
            "group":"form-group",
            "label":"label-control"
        }
    },
    {
        "label":"Send", 
        "element":"button", 
        "name":"editProfileDetailsSubmit",
        "disabled": true,
        "classes": {
            "element": "btn btn-default form-btn",
            "label": "label-control"
        }
    }
]

