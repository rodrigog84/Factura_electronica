<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tipo_documento extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}

	public function save(){
		
	}

	public function update(){
		
	}

	public function getAll(){
		$resp = array();

        $start = $this->input->post('start');
        $limit = $this->input->post('limit');


        //filtro por nombre
        $nombre = $this->input->get('nombre');

		$countAll = $this->db->count_all_results("tipo_documento");

		if($nombre){
			$query = $this->db->query('SELECT * FROM tipo_documento WHERE id like "'.$nombre.'"
			limit '.$start.', '.$limit.'');
		}else{
			$query = $this->db->query('SELECT * FROM tipo_documento limit '.$start.', '.$limit.'');
		}

		$data = array();
		foreach ($query->result() as $row)
		{
			$data[] = $row;
		}
        $resp['success'] = true;
        $resp['total'] = $countAll;
        $resp['data'] = $data;

        echo json_encode($resp);
	}
}
