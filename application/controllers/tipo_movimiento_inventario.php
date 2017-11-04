<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tipo_movimiento_inventario extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}

	public function save(){
		$resp = array();

		$id_tipo = $this->input->post('id_tipo');
		$nombre = $this->input->post('nombre');	
		$id_cuenta = $this->input->post('id_cuenta');
		$id_correccion = $this->input->post('id_correccion');
		$id_orden_compra = $this->input->post('id_orden_compra');
		$id_usuario = $this->input->post('id_usuario');
		$id_estad_compras = $this->input->post('id_estad_compras');
		$id_estad_consumo = $this->input->post('id_estad_consumo');
		$id_rut = $this->input->post('id_rut');
		$id_stock = $this->input->post('id_stock');

		$data = array(
	        'id_tipo' => $id_tipo,
	        'nombre' => strtoupper($nombre),
	        'id_cuenta' => $id_cuenta,
	        'id_usuario' => $id_usuario,
	        
	        'id_correccion' => $id_correccion ? 'on': 'off',
	        'id_orden_compra' => $id_orden_compra ? 'on': 'off',
	        'id_estad_compras' => $id_estad_compras ? 'on': 'off',
	        'id_estad_consumo' => $id_estad_consumo ? 'on': 'off',
	        'id_rut' => $id_rut ? 'on': 'off',
   	        'id_stock' => $id_stock ? 'on': 'off'
	    );

		$this->db->insert('tipo_movimiento', $data); 

        $resp['success'] = true;

         $this->Bitacora->logger("I", 'tipo_movimiento', $id_tipo);

        echo json_encode($resp);

	}

	public function update(){
		$resp = array();

		$id = $this->input->post('id');

		$id_tipo = $this->input->post('id_tipo');
		$nombre = $this->input->post('nombre');
		//checks
		$id_cuenta = $this->input->post('id_cuenta');
		$id_correccion = $this->input->post('id_correccion');
		$id_orden_compra = $this->input->post('id_orden_compra');
		$id_usuario = $this->input->post('id_usuario');
		$id_estad_compras = $this->input->post('id_estad_compras');
		$id_estad_consumo = $this->input->post('id_estad_consumo');
		$id_rut = $this->input->post('id_rut');
		$id_stock = $this->input->post('id_stock');

		$data = array(
			'id_tipo' => $id_tipo,
	        'nombre' => strtoupper($nombre),
	        'id_cuenta' => $id_cuenta,
	        'id_usuario' => $id_usuario,
	        'id_correccion' => $id_correccion ? 'on': 'off',
	        'id_orden_compra' => $id_orden_compra ? 'on': 'off',
	        'id_estad_compras' => $id_estad_compras ? 'on': 'off',
	        'id_estad_consumo' => $id_estad_consumo ? 'on': 'off',
	        'id_rut' => $id_rut ? 'on': 'off',
   	        'id_stock' => $id_stock ? 'on': 'off'
	    );

	    echo($id);

		$this->db->where('id', $id);
		
		$this->db->update('tipo_movimiento', $data); 

        $resp['success'] = true;

         $this->Bitacora->logger("M", 'tipo_movimiento', $id);


        echo json_encode($resp);

	}

	public function getAll(){
		$resp = array();

        $start = $this->input->post('start');
        $limit = $this->input->post('limit');


        //filtro por nombre
        $nombre = $this->input->post('nombre');

		$countAll = $this->db->count_all_results("tipo_movimiento");

		if($nombre){
			$query = $this->db->query('SELECT acc.*,  c.nombre as cuenta FROM tipo_movimiento acc
			left join cuenta_contable c on (acc.id_cuenta = c.id)
			WHERE acc.id_tipo like "'.$nombre.'" 
			limit '.$start.', '.$limit.'');
		}else{
			$query = $this->db->query('SELECT acc.*,  c.nombre as cuenta FROM tipo_movimiento acc
			left join cuenta_contable c on (acc.id_cuenta = c.id)
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
