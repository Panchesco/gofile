#Gofile

Use this plugin to handle a force download for a file using the EE Files file_id.

1. Set a file id to EE's flash data.
2. Return information about the file to template tags.
3. Force download of the file.

##Usage

###Tags

####{exp:gofile:set_id file_id="{segment_4}"}

Set the file ID to EE Flashdata.

####{exp:gofile:file_info} Tag Pair


Display file info for a file.


####Parameters
| Parameter | Required? | Description |Default|Options
| --- | --- | --- | --- |
| file_id | Yes |	The file_id of an Upload File Entry |  | 


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

##Creating A Forced Download for A File

To create a forced download, you'll need a minimum of two template pages, though you'll probably want to use three. 
	
1. A page that lists available files with links to a "Download" page.
2. A Download page where the file_id is set via the {exp:gofile:set_id} tag. Add an http meta referesh pointing to the template that forces the download.
3. A "Force Download" page with the {exp:gofile:download} tag.

Because of how headers are sent to force a file download, the "Force Download" page will only be seen if something went wrong with the download. On that template, you may want to use a meta refresh to point to an error message page. 

###Example Pages

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


###Change Log


1.2.0 Tags added:

* {exp:gofile:can_upload}
* {exp:gofile:upload}
	
1.1.0 Initial release with Inital release with tags:
    
* {exp:gofile:set_id}
* {exp:gofile:reset_id}
* {exp:gofile:download}
	
	



	



