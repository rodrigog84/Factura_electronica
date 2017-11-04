<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tipo_documentocompras extends CI_Controller {

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
	        'nombre' => strtoupper($data->nombre)
	        
		);

		$this->db->insert('tipo_documento_compras', $data); 

        $resp['success'] = true;

        $this->Bitacora->logger("I", 'tipo_documento_compras', $id);

        echo json_encode($resp);

	}

	public function update(){
		$resp = array();

		$data = json_decode($this->input->post('data'));
		$id = $data->id;
		$data = array(
	        'nombre' => strtoupper($data->nombre)
	    );
		$this->db->where('id', $id);
		
		$this->db->update('tipo_documento_compras', $data); 

		$this->Bitacora->logger("M", 'tipo_documento_compras', $id);

        $resp['success'] = true;

        echo json_encode($resp);

	}

	public function getAll(){

		$resp = array();

        $start = $this->input->post('start');
        $limit = $this->input->post('limit');
        //filtro por nombre
        $nombre = $this->input->post('nombre');

		$countAll = $this->db->count_all_results("tipo_documento_compras");

		if($nombre){
			$query = $this->db->query('SELECT * FROM tipo_documento_compras WHERE nombre like "%'.$nombre.'%" 
			limit '.$start.', '.$limit.'');
		}else{
			$query = $this->db->query('SELECT * FROM tipo_documento_compras limit '.$start.', '.$limit.'');
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
