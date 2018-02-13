<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Billboard extends CI_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->model('billboard_model');
    }

    public function index($company_code = 1){
        if(!empty($company_code)){
            $data['billboard'] = $this->billboard_model->get_by_company_code($company_code);
        }
        $view['content'] = $this->load->view('billboard',$data, TRUE);
        $this->load->view('layout', $view);
    }

    public function save(){
        $content = $this->input->post('content');
        $company_id = $this->input->post('company_id');
        $message = '';
        $type = 'success';
        $errors = '';

        if($company_id != '' && $content != ''){
            $inserted_id = $this->billboard_model->create($company_id, $content);
            if($inserted_id > 0 ) {
                $message = 'El registro ha sido guardado de forma exitosa';
                $type = 'success';
            }else{
                $message = 'No se pudo guardar el registro';
                $type = 'error';
            }
        }else{
            $message = 'Los parámetros enviados, no son válidos';
            $type = 'warning';
        }



        $data = array('message' => $message, 'type' => $type, 'errors' => $errors);
        header('Content-Type: application/json charset: utf-8');
        echo json_encode($data);
    }

    public function publish(){
        $billboard_id = $this->input->post('billboard_id');
        $flag = $this->input->post('publish');
        $message = '';
        $type = 'success';
        $errors = '';
        $data = NULL;


        if($billboard_id != '' && $flag != ''){

            $article = $this->billboard_model->publish($billboard_id, $flag);
            if($article) {
                if($article->published){
                    $message = 'El artículo ha sido publicado de forma exitosa';
                    $type = 'success';
                }else{
                    $message = 'El artículo ha sido ocultado de forma exitosa';
                    $type = 'info';
                }
                $data = ['published' => $article->published];
            }else{
                $message = 'No se pudo publicar el artículo';
                $type = 'error';
            }
        }else{
            $message = 'Los parámetros enviados, no son válidos';
            $type = 'warning';
        }

        $resp = array('message' => $message, 'type' => $type, 'errors' => $errors, 'data' => $data);
        header('Content-Type: application/json charset: utf-8');
        echo json_encode($resp);
    }

    public function show($company_code){
        $data['billboard'] = $this->billboard_model->get_by_company_code($company_code, TRUE);
        $this->load->view('visor', $data);
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
