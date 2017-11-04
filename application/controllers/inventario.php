<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventario extends CI_Controller {

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
		$idtipo=10;	

		$query = $this->db->query('SELECT * FROM correlativos WHERE id like "'.$id.'"');
		$row = $query->result();
		$row = $row[0];
		$numero = $row->correlativo + 1;
						    	
		foreach($items as $v){

			$datetime = DateTime::createFromFormat('d/m/Y', $v->fecha);
			$fecha_formateada = $datetime->format('Y-m-d');

			$data = array(
		        'num_inventario' => $numero,
		        'id_producto' => $v->producto,
		        'id_bodega' => $datainven,
		        'stock' => $v->cantidad,
		        'fecha_inventario' => $fecha_formateada
			);

			$datos2 = array(

				'num_movimiento' => $numero,
		        'id_producto' => $v->producto,
		        'id_tipo_movimiento' => $idtipo,
		        'id_bodega' => $v->bodega,
		        'valor_producto' =>  0,
		        'cantidad_entrada' => $v->cantidad,
		        'fecha_movimiento' => $fecha_formateada
			);

			$this->db->insert('existencia_detalle', $datos2);

			$producto = $v->producto;

			$this->db->insert('inventario', $data); 
                
			$datos = array(
	         'stock' => $v->cantidad,
	    	);

    		$this->db->where('id', $producto);

    		$this->db->update('productos', $datos);

	    	$query = $this->db->query('SELECT * FROM existencia WHERE id_producto='.$producto.'');
	    	$row = $query->result();
			$row = $row[0];
	 
			if ($query->num_rows()>0){
			
	            if ($producto==($row->id_producto)){
				    $datos3 = array(
					'stock' => $v->cantidad,
			        'fecha_ultimo_movimiento' => date('Y-m-d H:i:s')
					);

					$this->db->where('id_producto', $producto);

		    	    $this->db->update('existencia', $datos3);
	    	    }else{

	    	    	$datos3 = array(
					'id_producto' => $producto,
			        'stock' =>  $v->cantidad,
			        'fecha_ultimo_movimiento' => date('Y-m-d H:i:s')
				
					);
					$this->db->insert('existencia', $datos3);
		    	 	}
				}else{
					if ($producto==($row->id_producto)){
					    $datos3 = array(
						'stock' => $v->cantidad,
				        'fecha_ultimo_movimiento' => date('Y-m-d H:i:s')
						);

						$this->db->where('id_producto', $producto);

			    	    $this->db->update('existencia', $datos3);
		    	    }else{

		    	    	$datos3 = array(
						'id_producto' => $producto,
				        'stock' =>  $v->cantidad,
				        'fecha_ultimo_movimiento' =>$fecha_formateada
					
						);
						$this->db->insert('existencia', $datos3);
			    	}
			

		
					}
   		};
	

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

         $this->Bitacora->logger("I", 'inventario', $numero);


        echo json_encode($resp);

	}

	public function update(){

		$resp = array();
		$datainven = json_decode($this->input->post('bodega'));
		$numero = json_decode($this->input->post('numero'));
		$items = json_decode($this->input->post('items'));
		$id = json_decode($this->input->post('id'));
		$idtipo=10;
		
		echo $id;		

		$datos = array(
		        'num_inventario' => $numero,
		        'id_bodega' => $datainven,
		        'fecha' => date('Y-m-d H:i:s')
		);

		$this->db->where('id', $id);

    	$this->db->update('inventario_inicial', $datos);

		foreach($items as $v){

			$id = $v->id;

			$data = array(
		        'num_inventario' => $numero,
		        'id_producto' => $v->id_producto,
		        'id_bodega' => $datainven,
		        'stock' => $v->stock,
		        'fecha_inventario' => $v->fecha_inventario
			);
		
			$datos2 = array(
				'num_movimiento' => $numero,
		        'id_producto' => $v->id_producto,
		        'id_bodega' => $datainven,
		        'id_tipo_movimiento' => $idtipo,
		        'valor_producto' =>  0,
		        'cantidad_entrada' => $v->stock,
		        'fecha_movimiento' => date('Y-m-d H:i:s')
			);

			$this->db->where('id', $id);

    		$this->db->update('inventario', $data);

			$datos = array(
	         'stock' => $v->stock,
	    	);

	    	$producto = $v->id_producto;

    		$this->db->where('id', $producto);

    		$this->db->update('productos', $datos);

	    	$query = $this->db->query('SELECT * FROM existencia WHERE id_producto='.$producto.		'');
	    	$row = $query->result();			
	 
			if ($query->num_rows()>0){
				$row = $row[0];			
	            if ($producto==($row->id_producto)){
				    $datos3 = array(
					'stock' => $v->stock,
			        'fecha_ultimo_movimiento' => date('Y-m-d H:i:s')
					);

					$this->db->where('id_producto', $producto);

		    	    $this->db->update('existencia', $datos3);
	    	    }
			}else{
				   	$datos3 = array(
					'num_movimiento' => $numero,
			        'id_producto' => $v->id_producto,
			        'id_bodega' => $datainven,
			        'id_tipo_movimiento' => $idtipo,
			        'valor_producto' =>  0,
			        'cantidad_entrada' => $v->stock,
			        'fecha_movimiento' => date('Y-m-d H:i:s')
					);

					$this->db->insert('existencia_detalle', $datos3);
					$datos4= array(
						'id_producto' => $producto,
				        'stock' =>  $v->stock,
				        'fecha_ultimo_movimiento' =>date('Y-m-d H:i:s')
					
					);
					$this->db->insert('existencia', $datos4);
		    }			
			
   		};

   		  
   		$this->Bitacora->logger("M", 'inventario', $numero);


        echo json_encode($resp);

	}

	public function eliminada(){
		
		$resp = array();
		$id = $this->input->post('data');
		
		if($id){
		$this->db->query('DELETE FROM `inventario` WHERE id like "'.$id.'"');

        $resp['success'] = true;
        }else {
        $resp['success'] = false;
        };
        

        echo json_encode($resp);

	}

	public function buscar(){

		$resp = array();

        $start = $this->input->post('start');
        $limit = $this->input->post('limit');
        $nombre = $this->input->post('nombre');

		$countAll = $this->db->count_all_results("inventario");

		if($nombre){
			$query = $this->db->query('SELECT acc.*, c.nombre as nom_producto, com.nombre as nom_bodega FROM inventario acc
			left join productos c on (acc.id_producto = c.id)
			left join bodegas com on (acc.id_bodega = com.id)
			WHERE acc.num_inventario like "%'.$nombre.'%" order by acc.id desc');
		
		

		$data = array();
		foreach ($query->result() as $row)
		{
			$data[] = $row;
		}

		}
        $resp['success'] = true;
        $resp['total'] = $countAll;
        $resp['data'] = $data;

        echo json_encode($resp);
	}
		

	public function getAll(){
		$resp = array();

        $start = $this->input->post('start');
        $limit = $this->input->post('limit');


        //filtro por nombre
        $nombre = $this->input->get('nombre');

		$countAll = $this->db->count_all_results("inventario");

		if($nombre){
			$query = $this->db->query('SELECT acc.*, c.nombre as nom_producto, com.nombre as nom_bodega FROM inventario acc
			left join productos c on (acc.id_producto = c.id)
			left join bodegas com on (acc.id_bodega = com.id)
			WHERE acc.num_inventario like "%'.$nombre.'%" order by acc.id desc');
		}else{
			
			$query = $this->db->query('SELECT acc.*, c.nombre as nom_producto, com.nombre as nom_bodega FROM inventario acc
			left join productos c on (acc.id_producto = c.id)
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
