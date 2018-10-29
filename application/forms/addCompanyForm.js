[
    {
        "label":"Name", 
        "name":"companyName", 
        "element":"text", 
        "classes": {
            "element": "form-control required",
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
        "label":"Contact Number", 
        "name":"companyContactNumber", 
        "element":"text", 
        "classes": {
            "element": "form-control required",
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
        "label":"Address", 
        "name":"companyAddress", 
        "element":"textarea", 
        "classes": {
            "element": "form-control required",
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
        "label":"Email", 
        "name":"companyEmail", 
        "element":"text", 
        "classes": {
            "element": "form-control required",
            "group":"form-group",
            "label":"label-control"
        },
        "validation": {
            "0": [
                {"name":"maxLength", "value":45}
            ]
        }
    },
    {
        "label":"Excel File (Multiple Visas)", 
        "name":"visas", 
        "element":"file",
        "classes": {
            "element": "form-control no-post",
            "label": "label-control",
            "group": "form-group"
        },
        "validation": {
            "0": [
                {"name":"maxFileSize", "value":5},
                {"name":"extension", "value":"csv|CSV"}
            ]
        }
    },
    {
        "label":"Delimiter", 
        "name":"delimiter", 
        "element":"text",
        "value":",",
        "classes": {
            "element": "form-control changed",
            "label": "label-control",
            "group": "form-group"
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
        "name":"addCompanySubmit"
    }
]

