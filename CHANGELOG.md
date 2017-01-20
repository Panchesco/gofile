#Change Log

##1.2.5

* Adds create_upload_path method. 
* Adds logic in upload method/tag for creating a new directory if $can_define_path property is true. 
* Shortens some language variable names
* Code formatting.


##1.2.4

* Adds allowed_types method for setting CI config['allowed_types'] from EE File Manager setting.

##1.2.3

* Adds upload_url parameter and file_url variable to {exp:gofile:upload} tag pair.

##1.2.2

* Author data for a file is now available in {exp:gofile:file_info} tag
* Method: file_info_row now uses EE3 Model service
* Converts file_id passed in param to integer when passing it to Model


##1.2.1 

Messages moved to language file


##1.2.0 

Tags added:

* {exp:gofile:can_upload}
* {exp:gofile:upload}
	
##1.1.0 

Inital release with tags:
    
* {exp:gofile:set_id}
* {exp:gofile:reset_id}
* {exp:gofile:download}