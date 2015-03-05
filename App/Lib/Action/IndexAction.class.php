<?php
class IndexAction extends Action {

    protected function _initialize() {
        header("Content-Type:text/html; charset=utf-8");
    }

    public function index() {
        $as['last_anno']=M('anno')->order('id desc')->limit(1)->getField('content');
        $this->assign($as);
        $this->display();
    }
}

?>
