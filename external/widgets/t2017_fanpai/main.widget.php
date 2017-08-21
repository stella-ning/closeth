<?php

/**
 * 翻牌效果
 *
 * @return  array   $image_list
 */
class T2017_fanpaiWidget extends BaseWidget
{
    var $_name = 't2017_fanpai';
	var $_ttl  = 86400;

    function _get_data()
    {
		$cache_server =& cache_server();
        $key = $this->_get_cache_id();
        $data = $cache_server->get($key);
        if($data === false)
        {
           $ads1 = array();
           $ads2 = array();
           if(is_array($this->options['ads']) && count($this->options['ads']) > 5){
               $ads1 = array_slice($this->options['ads'],0,5);
               $ads2 = array_slice($this->options['ads'],5,count($this->options['ads']) - 5);
           }
           if(is_array($this->options['ads']) && count($this->options['ads']) <= 5){
               $ads1 = $this->options['ads'];
           }
            
			$data = array(			    
		    	'model_name' 	=> $this->options['model_name'],
		    	'show_all_linkurl' 	=> $this->options['show_all_linkurl'],
				'ads1'   	=> $ads1,
				'ads2'   	=> $ads2
			);
        }
        return $data;
    }

    function parse_config($input)
    {
        $result = array();
        $num    = isset($input['ad_link_url']) ? count($input['ad_link_url']) : 0;
        if ($num > 0)
        {
            $images = $this->_upload_image($num);
            for ($i = 0; $i < $num; $i++)
            {
                if (!empty($images[$i]))
                {
                    $input['ad_image_url'][$i] = $images[$i];
                }
    
                if (!empty($input['ad_link_url'][$i]))
                {
                    $result[] = array(
                        'ad_image_url' => $input['ad_image_url'][$i],
                        'ad_link_url'  => $input['ad_link_url'][$i],
                        'ad_title'=>$input['ad_title'][$i]
                    );
                }
            }
        }       
		$input['ads'] = $result;
		unset($input['ad_image_url']);
		unset($input['ad_link_url']);
		unset($input['ad_title']);
        return $input;
    }

    function _upload_image($num)
    {
        import('uploader.lib');

        $images = array();
        for ($i = 0; $i < $num; $i++)
        {
            $file = array();
            foreach ($_FILES['ad_image_file'] as $key => $value)
            {
                $file[$key] = $value[$i];
            }

            if ($file['error'] == UPLOAD_ERR_OK)
            {
                $uploader = new Uploader();
                $uploader->allowed_type(IMAGE_FILE_TYPE);
                $uploader->addFile($file);
                $uploader->root_dir(ROOT_PATH);
                $images[$i] = $uploader->save('data/files/mall/template', $uploader->random_filename());
            }
        }

        return $images;
    }
}

?>