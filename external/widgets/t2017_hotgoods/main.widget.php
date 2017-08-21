<?php

/**
 * 热门商品
 *
 * @return  array   $image_list
 */
class T2017_hotgoodsWidget extends BaseWidget {

    var $_name = 't2017_hotgoods';

    var $_ttl = 86400;

    function _get_data() {
        $cache_server = & cache_server();
        $key = $this->_get_cache_id();
        $data = $cache_server->get($key);
        if ($data === false) {
            
            $data = array(
                'model_id' => mt_rand(),
                'model_name' => $this->options['model_name'],
                'ads' => $this->options['ads'],
                'pages' => ceil(count($this->options['ads']) / 10)
            );
        }
        return $data;
    }

    function parse_config($input) {
        $result = array();
        $num = isset($input['ad_link_url']) ? count($input['ad_link_url']) : 0;
        if ($num > 0) {
            $images1 = $this->_upload_image1($num);
            $images2 = $this->_upload_image2($num);
            for ($i = 0; $i < $num; $i ++) {
                if (! empty($images1[$i])) {
                    $input['ad_image_url1'][$i] = $images1[$i];
                }
                if (! empty($images2[$i])) {
                    $input['ad_image_url2'][$i] = $images2[$i];
                }
                
                if (! empty($input['ad_image_url1'][$i])) {
                    $result[] = array(
                        'ad_image_url1' => $input['ad_image_url1'][$i],
                        'ad_image_url2' => $input['ad_image_url2'][$i],
                        'ad_link_url' => $input['ad_link_url'][$i],
                        'ad_gname' => $input['ad_gname'][$i],
                        'ad_price' => $input['ad_price'][$i]
                    );
                }
            }
        }
        $input['ads'] = $result;
        unset($input['ad_image_url1']);
        unset($input['ad_image_url2']);
        unset($input['ad_link_url']);
        unset($input['ad_gname']);
        unset($input['ad_price']);
        return $input;
    }

    function _upload_image1($num) {
        import('uploader.lib');
        
        $images = array();
        for ($i = 0; $i < $num; $i ++) {
            $file = array();
            foreach ($_FILES['ad_image_file1'] as $key => $value) {
                $file[$key] = $value[$i];
            }
            
            if ($file['error'] == UPLOAD_ERR_OK) {
                $uploader = new Uploader();
                $uploader->allowed_type(IMAGE_FILE_TYPE);
                $uploader->addFile($file);
                $uploader->root_dir(ROOT_PATH);
                $images[$i] = $uploader->save('data/files/mall/template', $uploader->random_filename());
            }
        }
        
        return $images;
    }
    
    function _upload_image2($num) {
        import('uploader.lib');
        
        $images = array();
        for ($i = 0; $i < $num; $i ++) {
            $file = array();
            foreach ($_FILES['ad_image_file2'] as $key => $value) {
                $file[$key] = $value[$i];
            }
            
            if ($file['error'] == UPLOAD_ERR_OK) {
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