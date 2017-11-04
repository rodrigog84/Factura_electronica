<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Existencias2 extends CI_Controller {

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
           
		$countAll = $this->db->count_all_results("existencia_detalle");
        
		if($nombres){
			
			$query = $this->db->query('SELECT acc.*, c.nombre as nom_producto, cor.nombre as nom_tipo_movimiento, bod.nombre as nom_bodega FROM existencia_detalle acc
			left join productos c on (acc.id_producto = c.id)
			left join correlativos cor on (acc.id_tipo_movimiento = cor.id)
			left join bodegas bod on (acc.id_bodega = bod.id)
			WHERE acc.id_producto="'.$nombres.'"');
		}else{
			$query = $this->db->query('SELECT acc.*, c.nombre as nom_producto, cor.nombre as nom_tipo_movimiento, bod.nombre as nom_bodega FROM existencia_detalle acc
			left join productos c on (acc.id_producto = c.id)
			left join correlativos cor on (acc.id_tipo_movimiento = cor.id)
			left join bodegas bod on (acc.id_bodega = bod.id) order by acc.id desc
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
