<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Notification extends CI_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->library('push_notification');
    }

    public function index(){
        $view['content'] = $this->load->view('notification',NULL, TRUE);
        $this->load->view('layout', $view);
    }

    public function test_notification(){
        $julio='d1ank4irhFA:APA91bEuD9N69cveBjNCCvDegjZKEuQAvOBiCuJQaY3KZxmsQr5mTdZUynG9JmAYk499g3AkYzeGbrzb-uxcmRd7fWKDmG9C2jxSIVddepv0p_eq756aK_PEKuZMUVSQE0Yq1mcsZpgw';
        $arley='ci9nTWbLg3g:APA91bFbVhgOeMYtxfyd3L-fvxI4thHZRr8c0viFQIHH-LRLW_EQZcDJ-wB55qz3m-5VpgDhZ4pAJ5p-oL8XQYYIdlTBo7TFB-2bgfyec6tc0LDsEhKmMWEqgwzENRwBBW7zFGyaJPjb';
        $this->push_notification->send_to_android($julio, "Mensaje desde la web", "Este es el mensaje de Julio");
    }

    public function send_to_all(){
        $title = $this->input->post('title');
        $body = $this->input->post('comment');
        $company_id = $this->input->post('company_id');
        $type = '';
        $message = '';
        $errors = '';

        if($title != '' && $body != '' && $company_id != ''){
            $this->load->model('randomuser_model');
            $devices = $this->randomuser_model->get_devices_token_by_company_code($company_id);
            if($devices){
                foreach ($devices as $device){
                    $this->push_notification->send_to_android($device->device_token, $title, $body);
                }
                $type = 'success';
                $message = 'Notificación enviada';
            }else{
                $type = 'warning';
                $message = 'No hay dispositivos registrados para el envío de notificaciones';
            }
        }else{
            $type = 'error';
            $errors = 'Debe agregar un título y un mensaje';
        }

        $data = array('message' => $message, 'type' => $type, 'errors' => $errors);
        header('Content-Type: application/json charset: utf-8');
        echo json_encode($data);
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */