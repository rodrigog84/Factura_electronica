<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Detallebitacora extends CI_Controller {



	public function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}
	
	public function getAll(){
		$resp = array();

        $start = $this->input->get('start');
        $limit = $this->input->get('limit');

        $filter = $this->input->get('nombre');

		$countAll = $this->db->count_all_results("bitacora");

		if($filter){
			$query = $this->db->query('SELECT acc.*, c.nombre as nom_usuario, c.apellido as apellido_usuario FROM bitacora acc
			left join usuario c on (acc.usuario = c.id)
			WHERE c.nombre like "%'.$filter.'%"
			');
		}else{
			$query = $this->db->query('SELECT acc.*, c.nombre as nom_usuario, c.apellido as apellido_usuario FROM bitacora acc 
			left join usuario c on (acc.usuario = c.id) order by acc.id desc
			limit '.$start.', '.$limit.' '
            );
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

