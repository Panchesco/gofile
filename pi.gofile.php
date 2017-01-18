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
 * @link https://github.com/panchesco
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
 * @link			https://github.com/panchesco
 */
// ------------------------------------------------------------------------
class Gofile
{
	
	public $return_data;
	public $base_path;
	public $base_url;
	public $file_id;

	function __construct()
	{
		$this->base_path = '/' . trim(ee()->config->item('base_path'),'/');
		$this->base_url = trim(ee()->config->item('base_url'),'/');
		
	}
	
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

}
// End class Gofile