<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Venta extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}

	public function save(){

		$resp = array();

		$idcliente = $this->input->post('idcliente');
		$numticket = $this->input->post('numeroticket');
		$fechapreventa = $this->input->post('fechapreventa');
		$vendedor = $this->input->post('vendedor');
		$datacliente = json_decode($this->input->post('datacliente'));
		$items = json_decode($this->input->post('items'));
		$neto = $this->input->post('neto');
		$desc = $this->input->post('descuento');
		$fiva = $this->input->post('iva');
		$fafecto = $this->input->post('afecto');
		$ftotal = $this->input->post('total');
		
		$preventa = array(
	        'num_ticket' => $numticket,
	        'fecha_venta' => $fechapreventa,
	        'id_cliente' => $idcliente,
	        'id_vendedor' => $vendedor,
	        'neto' => $neto,
	        'desc' => $desc,
	        'total' => $ftotal
		);

		$this->db->insert('preventa', $preventa); 
		$idpreventa = $this->db->insert_id();

		foreach($items as $v){
			$preventa_detalle = array(
		        'id_producto' => $v->id,
		        'id_ticket' => $idpreventa,
		        'neto' => $v->precio,
		        'cantidad' => $v->cantidad,
		        'neto' => $v->total,
		        'desc' => $v->dcto,
		        'iva' => $v->iva,
		        'total' => $v->totaliva,
		        'fecha' => $fechapreventa,
			);

		$producto = $v->id;

		$this->db->insert('preventa_detalle', $preventa_detalle);

		$query = $this->db->query('SELECT * FROM productos WHERE id="'.$producto.'"');
		 if($query->num_rows()>0){

		 	$row = $query->first_row();

		 	$saldo = ($row->stock)-($v->cantidad); 

		 };

		$datos = array(
         'stock' => $saldo,
    	);

    	$this->db->where('id', $producto);

    	$this->db->update('productos', $datos);
    	
		}

		
        $resp['success'] = true;
		$resp['idpreventa'] = $idpreventa;

		$this->Bitacora->logger("I", 'preventa', $idpreventa);
		$this->Bitacora->logger("I", 'preventa_detalle', $idpreventa);
        

        echo json_encode($resp);
	}

	public function update(){
		$resp = array();

		$data = json_decode($this->input->post('data'));
		$id = $data->id;
		$data = array(
	        'num_ticket' => $data->num_ticket,
	        'fecha_venta' => $data->fecha_venta,
	        'id_cliente' => $data->id_cliente,
	        'id_vendedor' => $data->id_vendedor,
	        'neto' => $data->neto,
	        'desc' => $data->desc,
	        'total' => $data->total
	    );
		$this->db->where('id', $id);
		
		$this->db->update('preventa', $data); 

        $resp['success'] = true;

         $this->Bitacora->logger("M", 'preventa', $id);


        echo json_encode($resp);

	}

	public function getAll(){
		$resp = array();

        $start = $this->input->post('start');
        $limit = $this->input->post('limit');


        //filtro por nombre
        $nombre = $this->input->get('nombre');

		$countAll = $this->db->count_all_results("preventa");

		if($nombre){
			$query = $this->db->query('SELECT acc.*, c.nombres as nom_cliente, c.rut as rut_cliente, v.nombre as nom_vendedor	FROM preventa acc
			left join clientes c on (acc.id_cliente = c.id)
			left join vendedores v on (acc.id_vendedor = v.id)
			WHERE nombre like "%'.$nombre.'%"
			limit '.$start.', '.$limit.'');
		}else{
			$query = $this->db->query('SELECT acc.*, c.nombres as nom_cliente, c.rut as rut_cliente, v.nombre as nom_vendedor	FROM preventa acc
			left join clientes c on (acc.id_cliente = c.id)
			left join vendedores v on (acc.id_vendedor = v.id)');
		}

		$data = array();
		foreach ($query->result() as $row)

		{
			$rutautoriza = $row->rut_cliente;
		   	if (strlen($rutautoriza) == 8){
		      $ruta1 = substr($rutautoriza, -1);
		      $ruta2 = substr($rutautoriza, -4, 3);
		      $ruta3 = substr($rutautoriza, -7, 3);
		      $ruta4 = substr($rutautoriza, -8, 1);
		    };
		    if (strlen($rutautoriza) == 9){
		      $ruta1 = substr($rutautoriza, -1);
		      $ruta2 = substr($rutautoriza, -4, 3);
		      $ruta3 = substr($rutautoriza, -7, 3);
		      $ruta4 = substr($rutautoriza, -9, 2);
		   
		    };

		    $row->rut_cliente = ($ruta4.".".$ruta3.".".$ruta2."-".$ruta1);
			$data[] = $row;
		}
        $resp['success'] = true;
        $resp['total'] = $countAll;
        $resp['data'] = $data;

        echo json_encode($resp);
	}
}
