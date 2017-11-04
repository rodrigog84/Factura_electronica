<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Existencias extends CI_Controller {

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
        $start = $this->input->get('start');
        $limit = $this->input->get('limit');
        $nombres = $this->input->get('nombre');
           
		$countAll = $this->db->count_all_results("existencia");
        
		if($nombres){
			$sql_nombre = "";
	        $arrayNombre =  explode(" ",$nombres);
	        foreach ($arrayNombre as $nombre) {
	        	$sql_nombre .= "c.nombre like '%".$nombre."%' and ";
	        }

			$query = $this->db->query('SELECT acc.*, c.nombre as nom_producto FROM existencia acc
			left join productos c on (acc.id_producto = c.id)
			WHERE ' . $sql_nombre . ' 1 = 1');
		}else{
			$query = $this->db->query('SELECT acc.*, c.nombre as nom_producto FROM existencia acc
			left join productos c on (acc.id_producto = c.id) order by acc.id desc
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
