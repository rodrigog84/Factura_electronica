<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Detalle_movimiento extends CI_Controller {

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

        $nombre = $this->input->post('nombre');

		$countAll = $this->db->count_all_results("movimientodiario_detalle");

		if($nombre){
			$query = $this->db->query('SELECT acc.*,  t.nombre as nom_tipom, s.nombre as nom_tipom, p.nombre as nom_producto FROM movimientodiario_detalle acc
			left join tipo_movimiento t on (acc.id_tipom = t.id)
			left join tipo_movimiento s on (acc.id_tipomd = s.id)
			left join productos p on (acc.id_producto = p.id)			
			WHERE acc.id_movimiento like "'.$nombre.'"');
		}else{
			$query = $this->db->query('SELECT acc.*,  t.nombre as nom_tipom, s.nombre as nom_tipom FROM movimientodiario_detalle acc
			left join tipo_movimiento t on (acc.id_tipom = t.id)
			left join tipo_movimiento s on (acc.id_tipomd = s.id)
			left join productos p on (acc.id_producto = p.id)');
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
