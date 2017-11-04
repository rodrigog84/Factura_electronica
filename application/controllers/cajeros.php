<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cajeros extends CI_Controller {

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
	      	'rut' => ($data->rut),
	        'direccion' => strtoupper($data->direccion),
	        'fono' => ($data->fono),
	        'comision' => ($data->comision),
	        'estado' => $data->estado
	          
		);

		$this->db->insert('cajeros', $data); 

        $resp['success'] = true;

        $this->Bitacora->logger("I", 'cajeros', $id);

        echo json_encode($resp);

	}

	public function update(){
		$resp = array();

		$data = json_decode($this->input->post('data'));
		$id = $data->id;
		$data = array(
			'nombre' => strtoupper($data->nombre),
	      	'rut' => ($data->rut),
	        'direccion' => strtoupper($data->direccion),
	        'fono' => ($data->fono),
	        'comision' => ($data->comision),
	        'estado' => $data->estado
	    );

		$this->db->where('id', $id);
		
		$this->db->update('cajeros', $data); 

        $resp['success'] = true;

        $this->Bitacora->logger("M", 'cajeros', $id);

        echo json_encode($resp);

	}

	public function getAll(){
		$resp = array();

        $start = $this->input->post('start');
        $limit = $this->input->post('limit');


        //filtro por nombre
        $nombre = $this->input->get('nombre');

		$countAll = $this->db->count_all_results("cajeros");

		if($nombre){
			$query = $this->db->query('SELECT * FROM cajeros WHERE nombre like "%'.$nombre.'%"
			limit '.$start.', '.$limit.'');
		}else{
			
			$query = $this->db->query('SELECT * FROM cajeros limit '.$start.', '.$limit.'');
			
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
