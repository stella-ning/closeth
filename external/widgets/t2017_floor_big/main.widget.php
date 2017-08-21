<?php

/**
 * 楼层大图
 *
 * @return  array   $image_list
 */
class T2017_floor_bigWidget extends BaseWidget {

    var $_name = 't2017_floor_big';

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
        $num = isset($input['store_link_url']) ? count($input['store_link_url']) : 0;
        if ($num > 0) {
            $images_store = $this->_upload_store_image($num);
            $images_goods = $this->_upload_image($num * 3);
            for ($i = 0; $i < $num * 3; $i ++) {
                if (! empty($images_goods[$i]))
                    $input['ad_image_url'][$i] = $images_goods[$i];
            }
            for ($i = 0; $i < $num; $i ++) {
                $tmp_goods = array();
                for ($j = $i * 3; $j < $i * 3 + 3; $j ++) {
                    $tmp_goods[] = array(
                        'ad_gname' => $input['ad_gname'][$j],
                        'ad_gprice' => $input['ad_gprice'][$j],
                        'goods_link_url' => $input['goods_link_url'][$j],
                        'ad_image_url' => $input['ad_image_url'][$j]
                    );
                }
                
                if (! empty($images_store[$i])) {
                    $input['store_image_url'][$i] = $images_store[$i];
                }
                
                if (! empty($input['store_link_url'][$i])) {
                    $result[] = array(
                        'store_image_url' => $input['store_image_url'][$i],
                        'ad_sname' => $input['ad_sname'][$i],
                        'ad_saddr' => $input['ad_saddr'][$i],
                        'store_link_url' => $input['store_link_url'][$i],
                        'goods_arr' => $tmp_goods
                    );
                }
                
                unset($tmp_goods['ad_gname']);
                unset($tmp_goods['ad_gprice']);
                unset($tmp_goods['goods_link_url']);
                unset($tmp_goods['ad_image_url']);
            }
        }
        $input['ads'] = $result;
        unset($input['store_image_url']);
        unset($input['ad_sname']);
        unset($input['ad_saddr']);
        unset($input['store_link_url']);
        unset($input['goods_arr']);
        return $input;
    }

    function _upload_store_image($num) {
        import('uploader.lib');
        
        $images = array();
        for ($i = 0; $i < $num; $i ++) {
            $file = array();
            foreach ($_FILES['store_img_file'] as $key => $value) {
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