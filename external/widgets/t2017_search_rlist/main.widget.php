<?php

/**
 * 图片挂件
 *
 * @return  array   $image_list
 */
class T2017_search_rlistWidget extends BaseWidget
{
    var $_name = 't2017_search_rlist';
	var $_ttl  = 86400;

    function _get_data()
    {
		$cache_server =& cache_server();
        $key = $this->_get_cache_id();
        $data = $cache_server->get($key);
        if($data === false)
        {
            
			$data = array(
		    	'model_id' 	=> mt_rand(),
				'width'    	=> $this->options['width'] ? intval($this->options['width']) : 225,
				'height'   	=> $this->options['height'] ? intval($this->options['height']) : 330,
				'ads'   	=> $this->options['ads']
			);
        }
        return $data;
    }

    function parse_config($input)
    {
        $result = array();
        $num    = isset($input['goods_link_url']) ? count($input['goods_link_url']) : 0;
        if ($num > 0)
        {
            $images = $this->_upload_image($num);
            for ($i = 0; $i < $num; $i++)
            {
                if (!empty($images[$i]))
                {
                    $input['ad_image_url'][$i] = $images[$i];
                }
    
                if (!empty($input['ad_image_url'][$i]))
                {
                    $result[] = array(
                        'ad_image_url' => $input['ad_image_url'][$i],
                        'goods_name'  => $input['goods_name'][$i],
                        'goods_link_url'  => $input['goods_link_url'][$i],
                        'store_name'  => $input['store_name'][$i],
                        'store_link_url'  => $input['store_link_url'][$i],
                        'goods_price'  => $input['goods_price'][$i],
                    );
                }
            }
        }       
		$input['ads'] = $result;
		unset($input['ad_image_url']);
		unset($input['goods_name']);
		unset($input['goods_link_url']);
		unset($input['store_name']);
		unset($input['store_link_url']);
		unset($input['goods_price']);
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