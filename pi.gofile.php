<?php
/**
 * Gofile Class
 *
 * @package ExpressionEngine
 * @author Richard Whitmer/Godat Design
 * @copyright (c) 2016, Richard Whitmer
 * @license
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * @link https://github.com/panchesco/gofile
 * @since Version 3.4.4
 */
// ------------------------------------------------------------------------

/**
 * Gofile Plugin
 *
 * @package		ExpressionEngine
 * @subpackage		user
 * @category		Plugin
 * @author		Richard Whitmer
 * @link			https://github.com/panchesco/gofile
 */
// ------------------------------------------------------------------------
class Gofile
{
	
	public $return_data;
	public $base_path;
	public $base_url;
	public $file_id;
	public $group_id;
	public $guest_access = FALSE;

	function __construct()
	{
		$this->group_id = ee()->session->userdata('group_id');
		$this->base_path = '/' . trim(ee()->config->item('base_path'),'/');
		$this->base_url = trim(ee()->config->item('base_url'),'/');
	}
	
	//---------------------------------------------------------------------------
	
	public function download() 
	{
		$this->file_id = ee()->session->flashdata('file_id');
		
		if( ! $this->file_id) 
		{

			return 'There was a problem and the file cannot be downloaded.';
		
		}
		
		
		$row = $this->file_info_row($this->file_id);
		
		
		if($this->file_id!=0 AND $row)
		{
			
				$file = $row->file_path;
			
				if(file_exists($row->file_path)) 
				{
				
				// http://php.net/manual/en/function.readfile.php
					
				if (file_exists($file)) {
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename="'.basename($file).'"');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . filesize($file));
				readfile($file);
				exit;
				} 
							
			};
			
			
			} else {
				
			
			return 'There was a problem and the file cannot be downloaded.';
		}
		
	}
	
	
	/**
	 * Return template friendly info for a files record.
	 * @return array.
	 */
	public function file_info()
	{
		
		if(ee()->TMPL->fetch_param('file_id',0)) { 
			$this->file_id = ee()->TMPL->fetch_param('file_id');
		} else {
			$this->file_id = ee()->session->flashdata('file_id');
		}
		
		if($this->file_id!=0) 
		{
			$row = $this->file_info_row($this->file_id);
			
			} else {
			
			return ee()->TMPL->no_results();
		}

		
		if($row) 
		{
			return ee()->TMPL->parse_variables(ee()->TMPL->tagdata,array( (array) $row));
			
			} else {
				
			return ee()->TMPL->no_results();
		}
	}
	
	//---------------------------------------------------------------------------
	
	/**
	 * Set file id to session.
	 * @return void
	 */
	public function set_id()
	{
			ee()->session->set_flashdata('file_id',ee()->TMPL->fetch_param('file_id'));
		
		return;
	}
	
	//---------------------------------------------------------------------------
	
	/**
	 * Set file id to session.
	 * @return void
	 */
	public function reset_id()
	{
			if(ee()->TMPL->fetch_param('file_id'))
			{
				ee()->session->set_flashdata('file_id',ee()->TMPL->fetch_param('file_id'));
			} else {
				ee()->session->set_flashdata('file_id',ee()->session->flashdata('file_id'));
			}
			
		return;
	}
	
	//---------------------------------------------------------------------------
	
	/**
	 * Look at current userdata. Can user upload files?
	 * Returns y if yes, n if no
	 * @return string
	*/
	public function can_upload() 
	{
		$this->upload_destination(10);
		
		return ee()->session->userdata('can_upload_new_files');
	}
	    	 	
	//-----------------------------------------------------------------------------

/**
 * Return information about an upload destination to the template.
 * @return
*/
public function directory() 
{
		$id = ee()->TMPL->fetch_param('id');
		
		$upload_destination = $this->upload_destination($id);
	
}
    	 	
//-----------------------------------------------------------------------------
	
	/**
	 * Upload file/s to server. Return information about uploaded files to templats
	 * @return
	*/
	public function upload() 
	{
				$dir = FALSE;

				$directory_id = ee()->TMPL->fetch_param('directory_id');
				$allowed_types = ee()->TMPL->fetch_param('allowed_types',"txt");
				$max_size = ee()->TMPL->fetch_param('max_size',0) * 1024;
				$max_width = ee()->TMPL->fetch_param('max_width',1200);
				$max_height = ee()->TMPL->fetch_param('max_height',1200);
				$file_ext_tolower = ee()->TMPL->fetch_param('file_ext_tolower','y');
				$encrypt_name = ee()->TMPL->fetch_param('encrypt_name','y');
				$upload_path = ee()->TMPL->fetch_param('upload_path');
				$userfile	= ee()->TMPL->fetch_param('file_field','userfile');
				$guest_access = strtolower(ee()->TMPL->fetch_param('guest_access','n'));
				
				
				// If Directory ID, overwrite params with upload_preferences
				if($directory_id) 
				{
					
					if(substr($guest_access,0,1)=='y')
					{
						$this->guest_access = TRUE;
					}
					
					$dir = $this->upload_destination($directory_id);
					### Todo get these from ee
					
					
					// Is current user allowed?
					
					if( $dir && in_array(ee()->session->userdata('group_id'),$dir['no_access']))
					{
						return 'You do not have access to this directory';
					};
					
					if($this->can_upload() != 'y') {
						
						return 'You are not allowed to upload files';
					}

					
					if($dir !== FALSE) 
					{
						
						// Allowed types
						if($dir['allowed_types'] != 'all') 
						{
							$allowed_types = $dir['allowed_types'];
						}
						
						$upload_path = $dir['server_path'];
						$max_size = $dir['max_size']*1024;
						$max_height = $dir['max_height'];
						$max_width = $dir['max_width'];	
					}
				} elseif( ! file_exists($upload_path)) {
					return 'Upload path not valid';
				} 				

				$data = array();
		
				 $config['upload_path']   = $upload_path;
	                $config['allowed_types'] = $allowed_types;
	                $config['max_size']      = $max_size;
	                $config['max_width']     = $max_width;
	                $config['max_height']    = $max_height;
	                $config['file_ext_tolower'] = ($file_ext_tolower=='y') ? TRUE : FALSE;
	                $config['encrypt_name'] = ($encrypt_name=='y') ? TRUE : FALSE;

	                ee()->load->library('upload',$config);
				
				 // Upload multiple files.
				    $files = $_FILES;
				    $cpt = count($_FILES[$userfile]['name']);
				    for($i=0; $i<$cpt; $i++)
				    {           
				        $_FILES[$userfile]['name']= $files[$userfile]['name'][$i];
				        $_FILES[$userfile]['type']= $files[$userfile]['type'][$i];
				        $_FILES[$userfile]['tmp_name']= $files[$userfile]['tmp_name'][$i];
				        $_FILES[$userfile]['error']= $files[$userfile]['error'][$i];
				        $_FILES[$userfile]['size']= $files[$userfile]['size'][$i];    
				    
				        ee()->upload->initialize($config);
	
				        if(ee()->upload->do_upload($userfile)) {
					        $data[] = ee()->upload->data();
				        } 
				    }
				
				if( ! empty($data)) {	
				
				
				foreach($data as $key => $row)
				{
					$row['upload_location_id'] = $directory_id;
					
					// If there's a file directory, create a new row in exp_files and
					// set the saved properties to the data array.
					if($directory_id) 
					{
						$add = $this->add_file($row);	
						$data[$key]['file_id'] = $add->file_id;
						$data[$key]['upload_location_id'] = $add->upload_location_id;
						$data[$key]['title'] = $add->title;
						$data[$key]['description'] = $add->description;
						$data[$key]['credit'] = $add->credit;
						$data[$key]['location'] = $add->location;
					} else {
						$data[$key]['file_id'] = '';
						$data[$key]['upload_location_id'] = '';
						$data[$key]['title'] = '';
						$data[$key]['description'] = '';
						$data[$key]['credit'] = '';
						$data[$key]['location'] = '';
					}
				}
				
				return ee()->TMPL->parse_variables(ee()->TMPL->tagdata,$data);
				
				} else {
					
					return ee()->upload->display_errors('<p>','</p>');
					
					return ee()->TMPL->no_results();
				}
		
			 	
	}
	    	 	
	//-----------------------------------------------------------------------------	
	
		/**
	 * Return the file extension from a file name
	 * @param $filename string
	 * @return string
	 */
	 private function file_ext($file_name)
	 {
            $filename_array = explode('.',$file_name);
		return strtolower(array_pop($filename_array));
	 }
	 

	 
	 //-----------------------------------------------------------------------------
	
	/**
	 * Return template friendly info for a files record.
	 * @return object.
	 */
	private function file_info_row($file_id)
	{

		$sql = "
				SELECT 
				exp_files.file_id,
				exp_files.upload_location_id,
				exp_files.mime_type,
				exp_files.title,
				exp_files.file_name,
				exp_files.file_size,
				exp_files.description,
				exp_files.credit,
				exp_files.location,
				exp_upload_prefs.name AS directory,
				exp_upload_prefs.server_path AS file_path,
				exp_upload_prefs.url AS file_url,
				exp_upload_prefs.cat_group	
			FROM exp_files 
			LEFT OUTER JOIN exp_upload_prefs ON exp_upload_prefs.id = exp_files.upload_location_id
			WHERE exp_files.file_id = '" . $file_id . "'
			LIMIT 1
			";	
			
			$result = ee()->db->query($sql);
			$row = $result->row();
			
			if($row) 
			{
				
				$row->file_path = str_replace('{base_path}',$this->base_path,$row->file_path);
				$row->file_url = str_replace('{base_url}',$this->base_url,$row->file_url);
				$row->file_ext = $this->file_ext($row->file_name);
				$row->file_path.= $row->file_name;
				$row->file_url.= $row->file_name;
				$row->file_size_mb = (is_numeric($row->file_size)) ? round(($row->file_size /1024/1024),2) : 0;	
			} 			
			
			return $row;
	}
	
	//---------------------------------------------------------------------------
	
/**
 * Add a file to exp_files
 * @return boolean
*/
private function add_file($data) 
{

	$cols['title'] = str_replace($data['file_ext'],"",$data['orig_name']);
	$cols['upload_location_id'] = $data['upload_location_id'];
	$cols['mime_type'] = $data['file_type'];
	$cols['file_name'] = $data['file_name'];
	$cols['file_size'] = $data['file_size'];
	$cols['uploaded_by_member_id'] = ee()->session->userdata('member_id');
	$cols['modified_by_member_id'] = ee()->session->userdata('member_id');
	$cols['upload_date'] = time();
	$cols['modified_date'] = time();
	$cols['file_hw_original'] = trim($data['image_height'] . ' ' . $data['image_width'] );

	return ee('Model')->make('File',$cols)->save();
    	 	
}
    	 	
//-----------------------------------------------------------------------------
	
	/**
	 * Get upload prefs data row
	 * @param $id integer
	 * @return mixed boolean/array
	*/
	private function upload_destination($id) 
	{
		
		$dest = ee('Model')->get('UploadDestination')
				->filter('id',$id)
				->limit(1)
				->first();

		if($dest)
		{
			$data = $dest->toArray();
			
			
			// Set group_id for guests to no access.
			if( $this->guest_access!==FALSE)
			{
				$data['no_access'] = array();
			} else {
				$data['no_access'][] = 3;
			}
			
			// Check for member groups that don't have access to upload destination.
			$no_access = ($dest->NoAccess) ? $dest->NoAccess->toArray() : array();	
			
			// get group_ids for those groups and add it to $data array.
			foreach($no_access as $key => $row) 
			{
				$data['no_access'][] = $row['group_id'];
			}
			
			
			if( ! in_array($this->group_id,$data['no_access'])) 
			{
				return $data;	
			}
		
		}	
		
		return FALSE;
		 	
	}
	    	 	
	//-----------------------------------------------------------------------------

}
// End class Gofile