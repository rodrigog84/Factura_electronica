<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Descuentos extends CI_Controller {

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
	        'porcentaje' => $data->porcentaje
	          
		);

		$this->db->insert('tabla_descuento', $data); 

        $resp['success'] = true;

        $this->Bitacora->logger("I", 'tabla_descuento', $id);

        echo json_encode($resp);

	}

	public function update(){
		$resp = array();

		$data = json_decode($this->input->post('data'));
		$id = $data->id;
		$data = array(
	        'nombre' => strtoupper($data->nombre),
	        'porcentaje' => $data->porcentaje
	    );
		$this->db->where('id', $id);
		
		$this->db->update('tabla_descuento', $data); 

        $resp['success'] = true;

        $this->Bitacora->logger("M", 'tabla_descuento', $id);

        echo json_encode($resp);

	}

	public function getAll(){
		$resp = array();

        $start = $this->input->post('start');
        $limit = $this->input->post('limit');


        //filtro por nombre
        $nombre = $this->input->get('nombre');

		$countAll = $this->db->count_all_results("tabla_descuento");

		if($nombre){
			$query = $this->db->query('SELECT * FROM tabla_descuento WHERE nombre like "%'.$nombre.'%"
			limit '.$start.', '.$limit.'');
		}else{
			
			$query = $this->db->query('SELECT * FROM tabla_descuento limit '.$start.', '.$limit.'');
			
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
