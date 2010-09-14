<?php

/*
* Image Thumb
* 
* Generates an image thumbnail for a local image.  Caches thumbnails for 2 minutes before re-generating.
*
* @param string $image_path Local path to image
* @param int $height Maximum image height
* @param int $width Maximum image width
*
* @return string Web path to image thumbnail
*/
function image_thumb ($image_path, $height = FALSE, $width = FALSE)
{
	// Get the CodeIgniter super object
	$CI =& get_instance();
	$CI->load->helper('file_extension');
	
	// take off "px" from $height and $width
	if (strstr($height,'px')) {
		$height = str_replace('px','',$height);
	}
	
	if (strstr($width, 'px')) {
		$width = str_replace('px','',$width);
	}
	
	// generate image thumbnail filename from full path and dimensions
	$file_name = md5($image_path . $height . $width) . '.' . file_extension($image_path);

	// Path to image thumbnail
	$image_thumb = setting('path_image_thumbs') . $file_name;
	
	$modified_time = file_exists($image_thumb) ? filemtime($image_thumb) : FALSE;
	
	// if the cache is over 2 minutes old, we re-generate!
	if ($modified_time === FALSE or (time() - $modified_time > 120))
	{
		// load library
		$CI->load->library('image_lib');
		$CI->load->helper('get_available_image_library');
		
		// configuration
		$config['image_library']	= get_available_image_library();
		$config['library_path'] = $CI->config->item('image_library_path');
		$config['source_image']		= $image_path;
		$config['new_image']		= $image_thumb;
		$config['maintain_ratio']	= TRUE;
		if ($height) {
			$config['height'] = $height;
		}
		if ($width) {
			$config['width'] = $width;
		}
		
		$CI->image_lib->initialize($config);
		$CI->image_lib->resize();
		$CI->image_lib->clear();
	}

	return site_url(str_replace(FCPATH,'',$image_thumb));
}

/* End of file image_thumb_helper.php */
/* Location: ./application/helpers/image_thumb_helper.php */
