<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Caja extends CI_Controller {

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
	      	'id_cajero' => ($data->id_cajero),
	        'correlativo' => strtoupper($data->correlativo)
	       	          
		);

		$this->db->insert('cajas', $data); 

        $resp['success'] = true;

        $this->Bitacora->logger("I", 'cajas', $id);

        echo json_encode($resp);

	}

	public function update(){

		$resp = array();
		$data = json_decode($this->input->post('data'));
		$id = $data->id;
		$data = array(
			'nombre' => strtoupper($data->nombre),
	      	'id_cajero' => ($data->id_cajero),
	        'correlativo' => strtoupper($data->correlativo)
	       	          
	    );

		$this->db->where('id', $id);
		
		$this->db->update('cajas', $data); 

        $resp['success'] = true;

        $this->Bitacora->logger("M", 'cajas', $id);

        echo json_encode($resp);

	}

	public function getAll(){

		$resp = array();

        $start = $this->input->post('start');
        $limit = $this->input->post('limit');


        //filtro por nombre
        $nombre = $this->input->get('nombre');

		$countAll = $this->db->count_all_results("cajas");

		if($nombre){
			$query = $this->db->query('SELECT acc.*, con.nombre as nom_cajero FROM cajas acc 
			left join cajeros con on (acc.id_cajero = con.id)
			WHERE acc.nombres like "%'.$nombres.'%"');
		}else{
			
			$query = $this->db->query('SELECT acc.*, con.nombre as nom_cajero FROM cajas acc 
			left join cajeros con on (acc.id_cajero = con.id)
			limit '.$start.', '.$limit.'');
			
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
