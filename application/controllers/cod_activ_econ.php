<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cod_activ_econ extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}

	public function save(){
		$resp = array();

		$data = json_decode($this->input->post('data'));
		$id = $data->nombre;

		$data = array(
	      	'codigo' => strtoupper($data->codigo),
	        'nombre' => strtoupper($data->nombre)

		);

		$this->db->insert('cod_activ_econ', $data); 

        $resp['success'] = true;

        $this->Bitacora->logger("I", 'cod_activ_econ', $id);

        echo json_encode($resp);

	}

	public function update(){
		$resp = array();

		$data = json_decode($this->input->post('data'));
		$id = $data->id;
		$data = array(
	        'codigo' => strtoupper($data->codigo),
	        'nombre' => strtoupper($data->nombre)

	    );
		$this->db->where('id', $id);
		
		$this->db->update('cod_activ_econ', $data); 

        $resp['success'] = true;

        $this->Bitacora->logger("M", 'cod_activ_econ', $id);

        echo json_encode($resp);

	}

	public function getAll(){
		
		$resp = array();
        $start = $this->input->post('start');
        $limit = $this->input->post('limit');
        $nombres = $this->input->post('nombre');

		$countAll = $this->db->count_all_results("cod_activ_econ");

		if($nombres){
			$query = $this->db->query('SELECT * FROM cod_activ_econ WHERE nombre like "%'.$nombres.'%" limit '.$start.', '.$limit.'');
		}else{
			
			$query = $this->db->query('SELECT * FROM cod_activ_econ limit '.$start.', '.$limit.'');
			
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
