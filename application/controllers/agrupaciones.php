<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Agrupaciones extends CI_Controller {

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
	        'codigo' => $data->codigo,
	        'id_familia' => $data->id_familia,
	        'id_subfamilia' => $data->id_subfamilia
	        
		);

		$this->db->insert('agrupacion', $data); 

        $resp['success'] = true;

        $this->Bitacora->logger("I", 'agrupacion', $id);

        echo json_encode($resp);

	}

	public function update(){
		$resp = array();

		$data = json_decode($this->input->post('data'));
		$id = $data->id;
		$data = array(

		    'nombre' => strtoupper($data->nombre),
	        'codigo' => $data->codigo,
	        'id_familia' => $data->id_familia,
	        'id_subfamilia' => $data->id_subfamilia
	    );
		$this->db->where('id', $id);
		
		$this->db->update('agrupacion', $data); 

        $resp['success'] = true;

        $this->Bitacora->logger("M", 'agrupacion', $id);



        echo json_encode($resp);

	}

	public function getAll(){
		$resp = array();

        $start = $this->input->post('start');
        $limit = $this->input->post('limit');
        //filtro por nombre
        $nombre = $this->input->get('nombre');

		$countAll = $this->db->count_all_results("agrupacion");

		if($nombre){
			$query = $this->db->query('SELECT agg.*, fa.nombre as nom_familia, sb.nombre as nom_subfamilia FROM agrupacion agg
			left join familias fa on (agg.id_familia = fa.id)
			left join subfamilias sb on (agg.id_subfamilia = sb.id)
			WHERE agg.nom_producto like "%'.$nombre.'%"');
		}else{
			$query = $this->db->query('SELECT agg.*, fa.nombre as nom_familia, sb.nombre as nom_subfamilia FROM agrupacion agg
			left join familias fa on (agg.id_familia = fa.id)
			left join subfamilias sb on (agg.id_subfamilia = sb.id)
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
