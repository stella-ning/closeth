<?php

/**
 * @name 金牌档口
 * @return  array
 */
class Zwd_goldstoresWidget extends BaseWidget
{
    var $_name = 'zwd_goldstores';
    var $_ttl  = 86400;

    function _get_data()
    {
        $cache_server =& cache_server();
        $key = $this->_get_cache_id();
        $data = $cache_server->get($key);
        if($data === false)
        {  
            $images = $this->options['goods_image'];
            $goods_ids = $this->options['goods_id'];
            $goods_names = $this->options['goods_name'];
            $goods_link = $this->options['goods_link'];
            $goods_price = $this->options['goods_price'];
            
            $goods = array();
            for($i=0;$i<4;$i++)
            {
                $goods[$i] = array(
                   'goods_id'=>$goods_ids[$i],
                   'goods_name'=>$goods_names[$i],
                   'goods_link'=>$goods_link[$i],
                   'goods_price'=>$goods_price[$i],
                   'goods_image'=>$images[$i]
                );
            }
            
            $data = array(
            		'sname'  => $this->options['sname'],
            		'slink'  => $this->options['slink'],
            		'goods'   => $goods            		
            );
           
            $cache_server->set($key, $data, $this->_ttl);
        }
        return $data;
    }
    
    function parse_config($input)
    {
    	
    	$num    = 4;
    	
	    $images = $this->_upload_image($num); 
	    for ($i = 0; $i < $num ; $i++)
	    {
	        if (!empty($images[$i]))
	        {
	            $input['goods_image'][$i] = $images[$i];
	        }    	    
	        /* if (!empty($input['ad_image_url'][$i]))
	        {
	            $result['images'][] = array(
	                'ad_image_url' => $input['ad_image_url'][$i],
	                'ad_link_url'  => $input['ad_link_url'][$i],
	                'ad_title_url'  => $input['ad_title_url'][$i],
	            );
	        } */
	    }
    	    	
    	
    	return $input;
    }
  
    
    function _upload_image($num)
    {
        import('uploader.lib');
    
        $images = array();
        for ($i = 0; $i < $num; $i++)
        {
            $file = array();
            foreach ($_FILES['goods_img'] as $key => $value)
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