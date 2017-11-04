<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Familias extends CI_Controller {

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
	        'nombre' => strtoupper($data->nombre),
	        'codigo' => $data->codigo
		);

		$this->db->insert('familias', $data); 

        $resp['success'] = true;

        $this->Bitacora->logger("I", 'familias', $id);

        echo json_encode($resp);

	}

	public function update(){
		$resp = array();

		$data = json_decode($this->input->post('data'));
		$id = $data->id;
		$data = array(
	        'nombre' => strtoupper($data->nombre),
	        'codigo' => $data->codigo
	    );
		$this->db->where('id', $id);
		
		$this->db->update('familias', $data); 

        $resp['success'] = true;

        $this->Bitacora->logger("M", 'familias', $id);



        echo json_encode($resp);

	}

	public function getAll(){
		$resp = array();

        $start = $this->input->post('start');
        $limit = $this->input->post('limit');
        //filtro por nombre
        $nombre = $this->input->get('nombre');

		$countAll = $this->db->count_all_results("familias");

		if($nombre){
			$query = $this->db->query('SELECT * FROM familias WHERE nombre like "%'.$nombre.'%" 
			limit '.$start.', '.$limit.'');
		}else{
			$query = $this->db->query('SELECT * FROM familias limit '.$start.', '.$limit.'');
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
