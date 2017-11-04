<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Subfamilias extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}

	public function save(){
		
		$resp = array();
		$data = json_decode($this->input->post('data'));
		$id = $data->codigo;
		$data = array(
	        'nombre' => strtoupper($data->nombre),
	        'codigo' => $data->codigo,
	        'id_familias' => $data->id_familias
	        
		);

		$this->db->insert('subfamilias', $data); 

		 $this->Bitacora->logger("I", 'subfamilias', $id);

        $resp['success'] = true;

      

        echo json_encode($resp);

	}

	public function update(){
		
		$resp = array();
		$data = json_decode($this->input->post('data'));
		$id = $data->id;
		$data = array(

		    'nombre' => strtoupper($data->nombre),
	        'codigo' => $data->codigo,
	        'id_familias' => $data->id_familias

	    );
		$this->db->where('id', $id);
		
		$this->db->update('subfamilias', $data); 

        $resp['success'] = true;

         $this->Bitacora->logger("M", 'subfamilias', $id);

        echo json_encode($resp);

	}

	public function getAll(){
		$resp = array();

        $start = $this->input->post('start');
        $limit = $this->input->post('limit');
        //filtro por nombre
        $nombre = $this->input->get('nombre');

		$countAll = $this->db->count_all_results("subfamilias");

		if($nombre){
			$query = $this->db->query('SELECT agg.*, fa.nombre as nom_familia FROM subfamilias agg
			left join familias fa on (agg.id_familia = fa.id)
			WHERE agg.nom_producto like "%'.$nombre.'%"');

			$total = 0;

		  foreach ($query->result() as $row)
			{
				$total = $total +1;
			
			}

			$countAll = $total;
			
		}else{
			$query = $this->db->query('SELECT agg.*, fa.nombre as nom_familia FROM subfamilias agg
			left join familias fa on (agg.id_familias = fa.id)
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
