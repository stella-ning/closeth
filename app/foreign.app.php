<?php
/**
 *外国服装网
 */

class ForeignApp extends MallbaseApp {
   

    function index() {   	
        
        $this->display("foreign.index.html");
    }
    
    function delivery_query(){
        $this->display("foreign.delivery.query.html");
    }

}

?>
