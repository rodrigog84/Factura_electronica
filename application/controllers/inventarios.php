<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventarios extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}

	public function save(){
		$resp = array();
		$datainven = json_decode($this->input->post('bodega'));
		$items = json_decode($this->input->post('items'));
		$id = json_decode($this->input->post('indice'));
			

		$query = $this->db->query('SELECT * FROM correlativos WHERE id like "'.$id.'"');
		$row = $query->result();
		$row = $row[0];
		$numero = $row->correlativo + 1;
		
		$iventar = array(

			'num_inventario' => $numero,
	        'id_bodega' => $datainven,
	        'fecha' => $fecha_formateada

		); 

		$this->db->insert('inventario_inicial', $iventar); 
		    	
		foreach($items as $v){

		$datetime = DateTime::createFromFormat('d/m/Y', $v->fecha);
		$fecha_formateada = $datetime->format('Y-m-d');

		$data = array(
	        'num_inventario' => $numero,
	        'id_producto' => $v->producto,
	        'id_bodega' => $v->bodega,
	        'stock' => $v->cantidad,
	        'fecha_inventario' => $fecha_formateada
		);

		$producto = $v->producto;

		$this->db->insert('inventario', $data); 

		$datos = array(
         'stock' => $v->cantidad,
    	);

    	$this->db->where('id', $producto);

    	$this->db->update('productos', $datos);

		}

		$iventar = array(

			'num_inventario' => $numero,
	        'id_bodega' => $datainven,
	        'fecha' => $fecha_formateada

		); 

		$this->db->insert('inventario_inicial', $iventar); 
		    	

		$datos = array(
         'correlativo' => $numero
    	);
		
		$this->db->where('id', $id);
		  
		$this->db->update('correlativos', $datos);

        $resp['success'] = true;

        $this->Bitacora->logger("I", 'inventario', $producto);

        echo json_encode($resp);

	}

	public function update(){
		
		$resp = array();
		$data = json_decode($this->input->post('data'));
		$id = $data->id;
		$data = array(
	        'num_inventario' => $data->num_inventario,
	        'id_producto' => $data->id_producto,
	        'id_bodega' => $data->id_bodega,
	        'stock' => $data->stock,
	        'fecha_inventario' => $data->fecha_inventario
	    );
		$this->db->where('id', $id);
		
		$this->db->update('inventario', $data); 

        $resp['success'] = true;

        $this->Bitacora->logger("I", 'inventario', $id);

        echo json_encode($resp);

	}

	public function getAll(){
		$resp = array();

        $start = $this->input->post('start');
        $limit = $this->input->post('limit');


        //filtro por nombre
        $nombre = $this->input->get('nombre');

		$countAll = $this->db->count_all_results("inventario_inicial");

		if($nombre){
			$query = $this->db->query('SELECT acc.*, com.nombre as nom_bodega FROM inventario_inicial acc
			left join bodegas com on (acc.id_bodega = com.id)
			WHERE acc.apellido_paterno like "%'.$nombre.'%" order by acc.id desc');
		}else{
			
			$query = $this->db->query('SELECT acc.*, com.nombre as nom_bodega FROM inventario_inicial acc
			left join bodegas com on (acc.id_bodega = com.id)  order by acc.id desc
			limit '.$start.', '.$limit.'' ); 
			
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
