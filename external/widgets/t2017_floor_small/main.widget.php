<?php

/**
 * 楼层小图
 *
 * @return  array   $image_list
 */
class T2017_floor_smallWidget extends BaseWidget {

    var $_name = 't2017_floor_small';

    var $_ttl = 86400;

    function _get_data() {
        $cache_server = & cache_server();
        $key = $this->_get_cache_id();
        $data = $cache_server->get($key);
        if ($data === false) {
            
            $data = array(
                'model_id' => mt_rand(),
                'floor_name' => $this->options['floor_name'],
                'floor_no' => $this->options['floor_no'],
                'ads' => $this->options['ads']
            );
        }
        return $data;
    }

    function parse_config($input) {
        $result = array();
        $num = isset($input['goods_link_url']) ? count($input['goods_link_url']) : 0;
        if ($num > 0) {
            $images = $this->_upload_image($num);
            
            for ($i = 0; $i < $num; $i ++) {
                
                if (! empty($images[$i])) {
                    $input['ad_image_url'][$i] = $images[$i];
                }
                
                if (! empty($input['goods_link_url'][$i])) {
                    $result[] = array(
                        'goods_link_url' => $input['goods_link_url'][$i],
                        'ad_gname' => $input['ad_gname'][$i],
                        'ad_gprice' => $input['ad_gprice'][$i],
                        'ad_image_url' => $input['ad_image_url'][$i]
                    );
                }
            }
        }
        $input['ads'] = $result;
        unset($input['goods_link_url']);
        unset($input['ad_gname']);
        unset($input['ad_gprice']);
        unset($input['ad_image_url']);
        return $input;
    }

    function _upload_image($num) {
        import('uploader.lib');
        
        $images = array();
        for ($i = 0; $i < $num; $i ++) {
            $file = array();
            foreach ($_FILES['ad_image_file'] as $key => $value) {
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