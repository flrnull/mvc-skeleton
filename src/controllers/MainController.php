<?php

/**
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

class MainController extends Controller {
    
    public function indexAction() {   
        return $this->render('main/index.html');
    }
}