<?php

/**
 * 首页分类导航挂件
 *
 * @return  array   $category_list
 */
class T2017_index_naviWidget extends BaseWidget {

    var $_name = 't2017_index_navi';

    var $_ttl = 86400;

    function _get_data() {
        $cache_server = & cache_server();
        $key = $this->_get_cache_id();
        $data = $cache_server->get($key);
        if ($data === false) {
            
            $gcate_num = empty($this->options['gcate_num']) ? 4 : intval($this->options['gcate_num']);
            $mcate_num = empty($this->options['mcate_num']) ? 12 : intval($this->options['mcate_num']);
            
            $market_mod = & m('market');
            $gcates_mod = & m('gcategory');
            
            $markets = $market_mod->get_list(1);
            
            $gcategories = $gcates_mod->get_list_limit(0, true, true, $gcate_num);
            
            foreach ($gcategories as $key => $value) {
                if ($value['store_id'] > 0) {
                    unset($gcategories[$key]);
                }
            }
            
            $gcategories = array_slice($gcategories, 0, $gcate_num);
            $gcategories = array_values($gcategories);
            
            foreach ($gcategories as $key => $value) {
                $gcates_children = $gcates_mod->get_list_limit($value['cate_id'], true, false, 200);
                // $gcates_children = array_slice($gcates_children,0,200);
                $gcategories[$key]['children'] = $gcates_children;
            }
            
            $img_count = count($this->options['ads']);
            is_array($this->options['ads']) && $imgs = array_values($this->options['ads']);
            
            if ($img_count <= 3) {
                $data['ads'] = $this->options['ads'];
            } else {
                $data['ads'] = array_slice($this->options['ads'], 0, 3);
            }
            
            foreach ($gcategories as $gk => $gv){
                !empty($imgs[$gk*3+3]) && $gcategories[$gk]['ads'][] = $imgs[$gk*3+3]; 
                !empty($imgs[$gk*3+4]) && $gcategories[$gk]['ads'][] = $imgs[$gk*3+4]; 
                !empty($imgs[$gk*3+5]) && $gcategories[$gk]['ads'][] = $imgs[$gk*3+5]; 
            }
            
            $data['all_markets'] = $markets;
            $data['markets'] = array_slice($markets, 0, $mcate_num);
            $data['gcategory'] = $gcategories;
            
            $cache_server->set($key, $data, $this->_ttl);
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
                        'ad_image_url' => $input['ad_image_url'][$i]
                    );
                }
            }
        }
        $input['ads'] = $result;
        unset($input['goods_link_url']);
        unset($input['ad_gname']);
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

    function _get_market() {
        $market_mod = & m('market');
        $markets = $market_mod->get_list(- 1, true);
        
        import('tree.lib');
        $tree = new Tree();
        $tree->setTree($markets, 'mk_id', 'parent_id', 'mk_name');
        return $tree->getOptions(3);
    }
}

?>