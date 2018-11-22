<?php defined('SYSPATH') or die('No direct script access.');

abstract class Controller_API extends Kohana_Controller_REST {

    public $output_format = 'json';
    public $apikey = 'XAWOKigvw1odRygJO6AkJcRQii1iNh5o';

    // For handling incoming data from POST and PUT
    public $input_content;
    public $input_content_type;
    public $input_content_length;

    const DATE = 'Y-m-d H:i:s';

    protected $_action_map = array
    (
        'GET'    => 'get',
        'PUT'    => 'put',
        'POST'   => 'post',
        'DELETE' => 'delete',
    );

    public function before() {
        parent::before();

        $sess = Session::instance();

        if ($_GET['format']) $this->output_format = $_GET['format'];
        //$this->input_content = Request::$body;
        $this->input_content_type = (isset($_SERVER['CONTENT_TYPE'])) ? $_SERVER['CONTENT_TYPE'] : FALSE;
        $this->input_content_length = (isset($_SERVER['CONTENT_LENGTH'])) ? $_SERVER['CONTENT_LENGTH'] : FALSE;
    }

    public function after() {
        parent::after();
    }

    protected function token2user() {
        $token = $_GET['token'];
        if (!$token) return false;

        $tk = ORM::factory('user_token')->where('token', '=', $token)->find();

        $sha1 = sha1(Request::$user_agent);

        if ($tk->id) {
            if (($tk->user_agent == $sha1) && ($tk->expires > time())) {
                return $tk->user;
            } else {
                //非法请求删 token
                $tk->delete();
                return false;
            }

        } else {
            return false;
        }
    }

    protected function error($msg, $code=500) {
        $data = array('msg'=>$msg, 'code'=>$code);
        $this->output($data);
    }

    /**
     * Handling of output data set in action methods with $this->rest_output($data).
     *
     * @param array|object $data
     * @param int $http_status
     */
    protected function output($data = array(), $http_status = 200) {
        if (empty($data)) {
            Request::instance()->status = 404;
            return;
        }

        if (!$data['code']) $data['code'] = $http_status;
        Request::instance()->status = $http_status;

        $format_method = '_format_' . $this->output_format;

        // If the format method exists, call and return the output in that format
        if (method_exists($this, $format_method)) {
            $output_data = $this->$format_method($data);

            header("Content-Type:". File::mime_by_ext($this->output_format));
            header("Content-length:". (string) strlen($output_data));
            Request::instance()->response = $output_data;

        } else  {
            Request::instance()->response = var_export($data, true);
        }
    }

    // ------------------------------------------------------------------------------------
    // Format the output data to requested type
    private function _format_json($data = array()) {
        return json_encode($data);
    }

    private function _format_php($data = array()) {
        return serialize($data);
    }

}
