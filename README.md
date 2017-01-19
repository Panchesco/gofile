#Gofile

Added file handling tools for ExpressionEngine 3 templates 


#Tags


###{exp:gofile:can_upload}

Can the current user upload files? 
Returns session userdata can_upload_new_files variable value of y or n.

```
{if "{exp:gofile:can_upload}"=="y"} 
Congratulations, you can upload files!
{if:else}
Sorry, you can't upload files.
{/if}
```

###{exp:gofile:set_id}

Set or reset file_id in EE Flashdata.

```
// Set the flashdata file_id
{exp:gofile:reset_id file_id="{segment_4}"}


// Reset the flashdata file_id
{exp:gofile:reset_id}

```

###{exp:gofile:set_id}

Set the file ID to EE Flashdata.

```
{exp:gofile:set_id file_id="{segment_4}"}

```


###{exp:gofile:download}


Use this tag pair to handle a force download for a file using the EE Files file_id.

1. Set a file id to EE's flash data.
2. Return information about the file to template tags.
3. Force download of the file.

To create a forced download, you'll need a minimum of two template pages, though you'll probably want to use three. 
	
1. A page that lists available files with links to a "Download" page.
2. A Download page where the file_id is set via the {exp:gofile:set_id} tag. Add an http meta referesh pointing to the template that forces the download.
3. A "Force Download" page with the {exp:gofile:download} tag.

Because of how headers are sent to force a file download, the "Force Download" page will only be seen if something went wrong with the download. On that template, you may want to use a meta refresh to point to an error message page. 

###Example Pages for a Forced Download

#### Page one: File Listing

```
<ul>
{exp:file:entries limit="20" paginate="both" directory_id="3"}
	 <li>{title} <a href="{path="resouces/downloads"}/file_id/{file_id}">Download</a></li>
{/exp:file:entries}
</ul>
```

#### Page Two: Download Page Snippet

```
{!-- Get the file_id from the url and set it to flashdata --}
{exp:gofile:set_id file_id="{segment_4}"}

{!-- HTML with meta refresh pointing to resources/downloader --}
<html>
<head>
	<title>Resources / Downloads</title>
	<meta http-equiv="refresh" content="2;url={path="resources/downloader"}">
</head>
<body>
	<p>Your download should begin shortly. If not, <a href="{path="resources/downloader}">click here</a>.</p>
</body>
</html>

```

#### Page Three: Force Dowload Page

```
{!-- Gofile download tag --}
{exp:gofile:download}
{!-- HTML with meta refresh pointing to error message page --}
<html>
<head>
	<title>Resources / Downloads</title>
	<meta http-equiv="refresh" content="0;url={path="resources/error"}">
</head>
<body>
	<p>There was a problem and your file could not be downloaded.</p>
</body>
</html>

```

#Tag Pairs

###{exp:gofile:upload} 

Upload a submitted file/s to a File Manager directory or another path. Returns variables for the completed upload.

####Parameters
| Parameter | Required? | Description |Default|Options
| --- | --- | --- | --- |
| allowed_types | no | Pipe-separted list of file types | txt | |
| directory_id | no | upload_location_id | |	|
| encrypt_name | no | Encrypt the filename of the uploaded file? | y | y, n |
| guest_access | no | Allow non-logged in user to upload files? | n | y, n |
| file_field	| no | Name of file field in form | userfile | |
| max_height	| no | Maximum height in pixels of uploaded images | 1200 | |
| max_size	| no | Maximum size in megabytes uploaded files | 0 | |
| max_width	| no | Maximum width in pixels of uploaded images | 1200 | |
| upload_path	| no | full path on server to upload to |  | |

Note: If directory_id is present, the settings and file limits for that Upload Directory will be used instead of the allowed_types, max_height, max_size, max_width, and upload_path parameters.

####Variables

{file_name}<br>
{file_type}<br>
{file_path}<br>
{full_path}<br>
{raw_name}<br>
{orig_name}<br>
{file_ext}<br>
{file_size}<br>
{is_image}<br>
{image_width}<br>
{image_height}<br>
{image_type}<br>
{image_size_str}<br>
{file_id}<br>
{upload_location_id}<br>
{title}<br>
{description}<br>
{credit}<br>
{location}<br>

###{exp:gofile:file_info} 

Display file info for a file.

####Parameters
| Parameter | Required? | Description | Default | Options |
| --- | --- | --- | --- | --- |
| file_id | Yes |	The file_id of an Upload File Entry |  |  |


####Variables

{file_id}<br>
{upload_location_id}<br>
{mime_type}<br>
{file_name}<br>
{file_size}<br>
{description}<br>
{credit}<br>
{location}<br>
{directory}<br>
{file_path}<br>
{file_url}<br>
{file_ext}<br>
{file_size_mb}<br>

#####Example

```
{exp:gofile:file_info file_id="{segment_4}"}
<h2>{title}</h2>
<p>{description}</p>
<p>{credit}, {location}</p>
<p><a class="{file_ext} download" target="_blank" href="{file_url}" rel="nofollow">Download {file_ext}{file_size} MB</a></p>
{/exp:gofile:file_info}
```



##Change Log

1.2.1 

Messages moved to language file.


1.2.0 

Tags added:

* {exp:gofile:can_upload}
* {exp:gofile:upload}
	
1.1.0 

Initial release with Inital release with tags:
    
* {exp:gofile:set_id}
* {exp:gofile:reset_id}
* {exp:gofile:download}
	
	



	



