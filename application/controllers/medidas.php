<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Medidas extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}

	public function save(){
		$resp = array();

		$data = json_decode($this->input->post('data'));
		$id = strtoupper($data->nombre);

		$data = array(
	        'nombre' => strtoupper($data->nombre)
		);

		$this->db->insert('mae_medida', $data); 

        $resp['success'] = true;

        $this->Bitacora->logger("I", 'mae_medida', $id);

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
		
		$this->db->update('mae_medida', $data); 

        $resp['success'] = true;
          
        $this->Bitacora->logger("M", 'mae_medida', $id);


        echo json_encode($resp);

	}

	public function getAll(){
		$resp = array();

        $start = $this->input->post('start');
        $limit = $this->input->post('limit');


        $nombre = $this->input->get('nombre');

		$countAll = $this->db->count_all_results("mae_medida");

		if($nombre){
			$query = $this->db->query('SELECT * FROM mae_medida WHERE nombre like "%'.$nombre.'%" limit '.$start.', '.$limit.'');
		}else{
			
			$query = $this->db->query('SELECT * FROM mae_medida');
			
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
