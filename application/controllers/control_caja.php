<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Control_caja extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}

	public function save(){
		

	}

	public function update(){
		
	}

	public function getAll(){
		$resp = array();

        $start = $this->input->post('start');
        $limit = $this->input->post('limit');


        //filtro por nombre
        $nombre = $this->input->get('nombre');

		$countAll = $this->db->count_all_results("control_caja");

		if($nombre){
			$query = $this->db->query('SELECT * FROM control_caja WHERE nombre like "%'.$nombre.'%"
			limit '.$start.', '.$limit.'');
		}else{
			
			$query = $this->db->query('SELECT acc.*, c.nombre as nom_caja, ca.nombre as nom_cajero FROM control_caja acc
			left join cajas c on (acc.id_caja = c.id)
			left join cajeros ca on (acc.id_cajero = ca.id) order by acc.id desc
		    limit '.$start.', '.$limit.' ' );
			
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
