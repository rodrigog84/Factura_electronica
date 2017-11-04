<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tipo_movimientodiario extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}

	public function save(){

		$resp = array();

		$fecha = $this->input->post('fecha');
		$id_tipom = $this->input->post('id_tipom');
		$id_tipomd = $this->input->post('id_tipomd');
		$id_correlativo = $this->input->post('id_correlativo');
		$rut = $this->input->post('rut');
		$id_bodegaent = $this->input->post('id_bodegaent');
		$id_bodegasal = $this->input->post('id_bodegasal');
		$detalle = $this->input->post('detalle');
		$items = json_decode($this->input->post('items'));

		echo $id_tipom;

		if(!$id_bodegaent){
			$id_bodegaent = 0;
		}

		if(!$id_bodegasal){
			$id_bodegasal = 0;
		}

		$numero = $id_correlativo;
		   		
		$query = $this->db->query('SELECT * FROM correlativos WHERE id like "'.$numero.'"');

		if($query->num_rows()>0){

			$ord = $query->result();
		    $ord = $ord[0];
		    $numero = $ord->correlativo+1;
	    }

	    $ad = $id_correlativo;
		$data = array(
	        'correlativo' => $numero
	    );

		$this->db->where('id', $ad);
		
		$this->db->update('correlativos', $data); 

		$movimiento = array(
			    'numero' => $numero,
	            'fecha' => $fecha,
				'id_tipom' => $id_tipom,
				'id_tipomd' => $id_tipomd,
		        'rut' => $rut,
		        'id_bodegaent' => $id_bodegaent,
		        'id_bodegasal' => $id_bodegasal,
		        'detalle' => $detalle,
		);

		$this->db->insert('movimientodiario', $movimiento); 
		$idmovimiento = $this->db->insert_id();

		foreach($items as $v){

		  if ($v->producto>0){

			$movimiento_diario = array(
		        'id_movimiento' => $idmovimiento,
		        'id_tipom' => $id_tipom,
		        'id_tipomd' => $id_tipomd,
		        'id_producto' => $v->producto,
		        'cantidad' => $v->cantidad,
		        'valor' => $v->valor,
		        'stock' => $v->stock,
		        'fecha' => $fecha,
			);

			if ($v->producto>0){
				$this->db->insert('movimientodiario_detalle', $movimiento_diario);
			};
			
			
			$producto = $v->producto;

			$query = $this->db->query('SELECT * FROM productos WHERE id like "'.$producto.'"');
			if($query->num_rows()>0){

			 	$row = $query->first_row();

			 	$producto = $row->id;

			 	if ($id_tipom == 1){

			 	   $saldo = ($row->stock)+($v->cantidad);

			 	};

			 	if ($id_tipom == 2){

			 	   $saldo = ($row->stock)-($v->cantidad);

			 	};

			 };

			

		     $query = $this->db->query('SELECT * FROM existencia WHERE id_producto like "'.$producto.'"');
	    	
			 if ($query->num_rows()>0){

			 	$row = $query->result();
			 	$row = $row[0];
	 
			
	            if ($producto==($row->id_producto)){
				    $datos3 = array(
						'stock' => $saldo,
				        'fecha_ultimo_movimiento' => $fecha
					);

					$this->db->where('id_producto', $producto);

		    	    $this->db->update('existencia', $datos3);

		    	    if ($id_tipom == 1){
		    	    $existesi = array(

						'num_movimiento' => $numero,
				        'id_producto' => $v->producto,
				        'id_tipo_movimiento' => $id_correlativo,
				        'valor_producto' =>  $v->valor,
				        'cantidad_entrada' => $v->cantidad,
				        'id_bodega' => $id_bodegaent,
				        'fecha_movimiento' => $fecha
					);

					};

					if ($id_tipom == 2){

					$existesi = array(

						'num_movimiento' => $numero,
				        'id_producto' => $v->producto,
				        'id_tipo_movimiento' => $id_correlativo,
				        'valor_producto' =>  $v->valor,
				        'cantidad_salida' => $v->cantidad,
				        'id_bodega' => $id_bodegasal,
				        'fecha_movimiento' => $fecha
					);

					};

					$this->db->insert('existencia_detalle', $existesi);

	    	     }
	         }else{

	         	$nexiste = array(
					'id_producto' => $producto,
			        'stock' =>  $saldo,
			        'fecha_ultimo_movimiento' =>date('Y-m-d H:i:s')
				
				);
				$this->db->insert('existencia', $nexiste);

				$existesi = array(

						'num_movimiento' => $numero,
				        'id_producto' => $v->producto,
				        'id_tipo_movimiento' => $id_correlativo,
				        'valor_producto' =>  $v->valor,
				        'cantidad_entrada' => $v->cantidad,
				        'id_bodega' => $id_bodegaent,
				        'fecha_movimiento' => $fecha
				);

				$this->db->insert('existencia_detalle', $existesi);

	         };

			$datos = array(
	         'stock' => $saldo,
	    	);

	    	$this->db->where('id', $producto);

	    	$this->db->update('productos', $datos);

	    }


		}


		
        $resp['success'] = true;

        $this->Bitacora->logger("I", 'movimientodiario', $numero);


        echo json_encode($resp);

	}

	public function update(){
	}

	public function getAll(){
		$resp = array();

        $start = $this->input->post('start');
        $limit = $this->input->post('limit');


        //filtro por nombre
        $nombre = $this->input->post('nombre');

		$countAll = $this->db->count_all_results("movimientodiario");

		if($nombre){
			$query = $this->db->query('SELECT acc.*,  e.nombre as nom_bodegaent, s.nombre as nom_bodegasal, t.nombre as nom_tipomd FROM movimientodiario acc
			left join bodegas e on (acc.id_bodegaent = e.id)
			left join bodegas s on (acc.id_bodegasal = s.id)
			left join tipo_movimiento t on (acc.id_tipomd = t.id)
			WHERE acc.id_tipom like "'.$nombre.'" 
			limit '.$start.', '.$limit.'');
		}else{
			$query = $this->db->query('SELECT acc.*,  e.nombre as nom_bodegaent, s.nombre as nom_bodegasal, t.nombre as nom_tipomd FROM movimientodiario acc
			left join bodegas e on (acc.id_bodegaent = e.id)
			left join bodegas s on (acc.id_bodegasal = s.id)
			left join tipo_movimiento t on (acc.id_tipomd = t.id)
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
