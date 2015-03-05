<?php
class EmptyAction extends Action {
    public function _empty() {
		header("HTTP/1.1 404 Not Found");
		$this->display("Index:404");
    }

}

?>
