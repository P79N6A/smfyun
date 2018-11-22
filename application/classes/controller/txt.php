<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

class Controller_Txt extends Controller {
  public function action_txt(){
   $curr = Request::current()->uri();
   echo Request::current()->param('hash');
  }
}
?>
