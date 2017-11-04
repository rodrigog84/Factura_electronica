<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Guias extends CI_Controller {



	public function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}

	public function marcarguias(){

		$resp = array();
		$idfactura = $this->input->post('factura');
		$tipo = 3;

		$query = $this->db->query('SELECT acc.*, c.nombres as nombre_cliente, c.rut as rut_cliente, v.nombre as nom_vendedor	FROM factura_clientes acc
		left join clientes c on (acc.id_cliente = c.id)
		left join vendedores v on (acc.id_vendedor = v.id)
		WHERE acc.tipo_documento in ( '.$tipo.') and acc.id = '.$idfactura.''			
		);

		if($query->num_rows()>0){
	   		$row = $query->first_row();
	   		$id = ($row->id);

	   		$data3 = array(
	         'id_factura' => 1,
		    );

		    $this->db->where('id', $id);
		  
		    $this->db->update('factura_clientes', $data3);

		    $this->Bitacora->logger("M", 'factura_clientes', $id); 
			

	     };

	     $resp['success'] = true;
	     echo json_encode($resp);

		
	}

	public function numerofactura(){

		$resp = array();
		$factura = $this->input->get('factura');
       		
		$query = $this->db->query('SELECT * FROM correlativos WHERE id like "'.$factura.'"');

		$id = $data->id;
		$data = array(
	        'nombre' => strtoupper($data->nombre),
	        'correlativo' => $data->correlativo
	    );
		$this->db->where('id', $id);
		
		$this->db->update('correlativos', $data); 

        $resp['success'] = true;

		if($query->num_rows()>0){
	   			$row = $query->first_row();
	   			$resp['cliente'] = $row;
	   

	        $resp['success'] = true;
	        echo json_encode($resp);

	   }else{
	   	    $resp['success'] = false;
	   	    echo json_encode($resp);
	        return false;
	   }

	
	 }

	
	public function getAll(){
		
		$resp = array();
		$start = $this->input->get('start');
        $limit = $this->input->get('limit');
        $opcion = $this->input->get('opcion');
        $nombres = $this->input->get('nombre');
        $tipo = 3;
        $countAll = $this->db->count_all_results("factura_clientes");
		$data = array();
		$total = 0;
	

        if($opcion == "Rut"){
		
			$query = $this->db->query('SELECT acc.*, c.nombres as nombre_cliente, c.rut as rut_cliente, v.nombre as nom_vendedor	FROM factura_clientes acc
			left join clientes c on (acc.id_cliente = c.id)
			left join vendedores v on (acc.id_vendedor = v.id)
			WHERE acc.tipo_documento in ( '.$tipo.') and c.rut = '.$nombres.'
			order by acc.id desc		
			limit '.$start.', '.$limit.''		 

		);

		$total = 0;

		  foreach ($query->result() as $row)
			{
				$total = $total +1;
			
			}

			$countAll = $total;

	    }else if($opcion == "Nombre"){

	    	
			$sql_nombre = "";
	        $arrayNombre =  explode(" ",$nombres);

	        foreach ($arrayNombre as $nombre) {
	        	$sql_nombre .= "and c.nombres like '%".$nombre."%' ";
	        }
	        	    	
			$query = $this->db->query('SELECT acc.*, c.nombres as nombre_cliente, c.rut as rut_cliente, v.nombre as nom_vendedor	FROM factura_clientes acc
			left join clientes c on (acc.id_cliente = c.id)
			left join vendedores v on (acc.id_vendedor = v.id)
			WHERE acc.tipo_documento in ( '.$tipo.') ' . $sql_nombre . '
			order by acc.id desc		
			limit '.$start.', '.$limit.''
						
			);

			$total = 0;

		  foreach ($query->result() as $row)
			{
				$total = $total +1;
			
			}

			$countAll = $total;
	 
		}else if($opcion == "Todos"){

			
			$data = array();
			$query = $this->db->query('SELECT acc.*, c.nombres as nombre_cliente, c.rut as rut_cliente, v.nombre as nom_vendedor	FROM factura_clientes acc
			left join clientes c on (acc.id_cliente = c.id)
			left join vendedores v on (acc.id_vendedor = v.id)
			WHERE acc.tipo_documento in ( '.$tipo.')
			order by acc.id desc'	
			
			);


			$total = 0;

		  foreach ($query->result() as $row)
			{
				$total = $total +1;
			
			}

			$countAll = $total;
	

		}else{

			
		$data = array();
		$query = $this->db->query('SELECT * 
					FROM (SELECT row_number() OVER (ORDER BY acc.id) AS rownum,  acc.*, c.nombres as nombre_cliente, c.rut as rut_cliente, v.nombre as nom_vendedor	FROM factura_clientes acc
			left join clientes c on (acc.id_cliente = c.id)
			left join vendedores v on (acc.id_vendedor = v.id)
			WHERE acc.tipo_documento in ( '.$tipo.')) AS A
			WHERE A.rownum BETWEEN ('.$start.') AND ('.($start + $limit).')
			order by id desc'	

			);


		}		
		
		foreach ($query->result() as $row)
		{
			$rutautoriza = $row->rut_cliente;
		   	if (strlen($rutautoriza) == 8){
		      $ruta1 = substr($rutautoriza, -1);
		      $ruta2 = substr($rutautoriza, -4, 3);
		      $ruta3 = substr($rutautoriza, -7, 3);
		      $ruta4 = substr($rutautoriza, -8, 1);
		      $row->rut_cliente = ($ruta4.".".$ruta3.".".$ruta2."-".$ruta1);
		    };
		    if (strlen($rutautoriza) == 9){
		      $ruta1 = substr($rutautoriza, -1);
		      $ruta2 = substr($rutautoriza, -4, 3);
		      $ruta3 = substr($rutautoriza, -7, 3);
		      $ruta4 = substr($rutautoriza, -9, 2);
		      $row->rut_cliente = ($ruta4.".".$ruta3.".".$ruta2."-".$ruta1);
		   
		    };
		    if (strlen($rutautoriza) == 2){
		      $ruta1 = substr($rutautoriza, -1);
		      $ruta2 = substr($rutautoriza, -4, 1);
		      $row->rut_cliente = ($ruta2."-".$ruta1);
		     
		    };
		    $total = $total +1;
			
		 
			$data[] = $row;
		}

		//$countAll = $total;
        $resp['success'] = true;
        $resp['total'] = $countAll;
        $resp['data'] = $data;

        echo json_encode($resp);
	}

	public function pendientes(){
		
		$resp = array();
		$start = $this->input->get('start');
        $limit = $this->input->get('limit');
        $opcion = $this->input->get('opcion');
        $nombres = $this->input->get('nombre');
        $tipo = 3;
        $countAll = $this->db->count_all_results("factura_clientes");
		$data = array();
		$total = 0;
	

        if($opcion == "Rut"){
		
			$query = $this->db->query('SELECT acc.*, c.nombres as nombre_cliente, c.rut as rut_cliente, v.nombre as nom_vendedor	FROM factura_clientes acc
			left join clientes c on (acc.id_cliente = c.id)
			left join vendedores v on (acc.id_vendedor = v.id)
			WHERE acc.tipo_documento in ('.$tipo.') and c.rut = '.$nombres.'
			and acc.id_factura = 0
			order by acc.id desc		
			limit '.$start.', '.$limit.''		 

		);

		$total = 0;

		  foreach ($query->result() as $row)
			{
				$total = $total +1;
			
			}

			$countAll = $total;

	    }else if($opcion == "Nombre"){

	    	
			$sql_nombre = "";
	        $arrayNombre =  explode(" ",$nombres);

	        foreach ($arrayNombre as $nombre) {
	        	$sql_nombre .= "and c.nombres like '%".$nombre."%' ";
	        }
	        	    	
			$query = $this->db->query('SELECT acc.*, c.nombres as nombre_cliente, c.rut as rut_cliente, v.nombre as nom_vendedor	FROM factura_clientes acc
			left join clientes c on (acc.id_cliente = c.id)
			left join vendedores v on (acc.id_vendedor = v.id)
			WHERE acc.tipo_documento in ( '.$tipo.') ' . $sql_nombre . '
			and acc.id_factura = 0
			order by acc.id desc		
			limit '.$start.', '.$limit.''
						
			);

			$total = 0;

		  foreach ($query->result() as $row)
			{
				$total = $total +1;
			
			}

			$countAll = $total;
	 
		}else if($opcion == "Id"){

			
			$data = array();
			$query = $this->db->query('SELECT acc.*, c.nombres as nombre_cliente, c.rut as rut_cliente, v.nombre as nom_vendedor	FROM factura_clientes acc
			left join clientes c on (acc.id_cliente = c.id)
			left join vendedores v on (acc.id_vendedor = v.id)
			WHERE acc.tipo_documento in ( '.$tipo.') and acc.id_cliente = '.$nombres.'
			and acc.id_factura = 0
			order by acc.id desc		
			limit '.$start.', '.$limit.''		 

			);



			$total = 0;

		  foreach ($query->result() as $row)
			{
				$total = $total +1;
			
			}

			$countAll = $total;
	

		}else if($opcion == "Todos"){

			
			$data = array();
			$query = $this->db->query('SELECT acc.*, c.nombres as nombre_cliente, c.rut as rut_cliente, v.nombre as nom_vendedor	FROM factura_clientes acc
			left join clientes c on (acc.id_cliente = c.id)
			left join vendedores v on (acc.id_vendedor = v.id)
			WHERE acc.tipo_documento in ( '.$tipo.') and acc.id_factura = 0
			order by acc.id desc'	
			
			);


			$total = 0;

		  foreach ($query->result() as $row)
			{
				$total = $total +1;
			
			}

			$countAll = $total;
	

		}else{

			
		$data = array();
		$query = $this->db->query('SELECT acc.*, c.nombres as nombre_cliente, c.rut as rut_cliente, v.nombre as nom_vendedor	FROM factura_clientes acc
			left join clientes c on (acc.id_cliente = c.id)
			left join vendedores v on (acc.id_vendedor = v.id)
			WHERE acc.tipo_documento in ( '.$tipo.') and acc.id_factura = 0
			order by acc.id desc		
			limit '.$start.', '.$limit.''	

			);


		}
		
		
		foreach ($query->result() as $row)
		{
			$rutautoriza = $row->rut_cliente;
		   	if (strlen($rutautoriza) == 8){
		      $ruta1 = substr($rutautoriza, -1);
		      $ruta2 = substr($rutautoriza, -4, 3);
		      $ruta3 = substr($rutautoriza, -7, 3);
		      $ruta4 = substr($rutautoriza, -8, 1);
		      $row->rut_cliente = ($ruta4.".".$ruta3.".".$ruta2."-".$ruta1);
		    };
		    if (strlen($rutautoriza) == 9){
		      $ruta1 = substr($rutautoriza, -1);
		      $ruta2 = substr($rutautoriza, -4, 3);
		      $ruta3 = substr($rutautoriza, -7, 3);
		      $ruta4 = substr($rutautoriza, -9, 2);
		      $row->rut_cliente = ($ruta4.".".$ruta3.".".$ruta2."-".$ruta1);
		   
		    };
		    if (strlen($rutautoriza) == 2){
		      $ruta1 = substr($rutautoriza, -1);
		      $ruta2 = substr($rutautoriza, -4, 1);
		      $row->rut_cliente = ($ruta2."-".$ruta1);
		     
		    };
		    $total = $total +1;
			
		 
			$data[] = $row;
		}

		//$countAll = $total;
        $resp['success'] = true;
        $resp['total'] = $countAll;
        $resp['data'] = $data;

        echo json_encode($resp);
	}

	public function save(){
		
		$resp = array();

		$idcliente = $this->input->post('idcliente');
		$numfactura = $this->input->post('numfactura');
		$fechafactura = $this->input->post('fechafactura');
		$observacion = $this->input->post('observacion');
		$idobserva = $this->input->post('idobserva');
		$fechavenc = $this->input->post('fechavenc');
		$vendedor = $this->input->post('idvendedor');
		$items = json_decode($this->input->post('items'));
		$neto = $this->input->post('netofactura');
		$fiva = $this->input->post('ivafactura');
		$fafecto = $this->input->post('afectofactura');
		$ftotal = $this->input->post('totalfacturas');
		$tipodocumento = 1;


		$data3 = array(
	         'correlativo' => $numfactura
	    );
	    $this->db->where('id', $tipodocumento);
	  
	    $this->db->update('correlativos', $data3);
			
		$factura_cliente = array(
			'tipo_documento' => $tipodocumento,
	        'id_cliente' => $idcliente,
	        'num_factura' => $numfactura,
	        'id_vendedor' => $vendedor,
	        'sub_total' => $neto,
	        'observacion' => $observacion,
	        'id_observa' => $idobserva,
	        'descuento' => ($neto - $fafecto),
	        'neto' => $neto,
	        'iva' => $fiva,
	        'totalfactura' => $ftotal,
	        'fecha_factura' => $fechafactura,
	        'fecha_venc' => $fechavenc,
	        'forma' => 1
		);

		$this->db->insert('factura_clientes', $factura_cliente); 
		$idfactura = $this->db->insert_id();

		foreach($items as $v){
			$factura_clientes_item = array(
		        'id_guia' => $v->id_guia,
		        'id_factura' => $idfactura,
		        'num_guia' => $v->num_guia,
		        'neto' => $v->neto,
		        'iva' => $v->iva,
		        'total' => $v->total
			);
		
		$this->db->insert('detalle_factura_glosa', $factura_clientes_item);

			$data3 = array(
	         'id_factura' => $idfactura,
		    );

		    $this->db->where('id', $v->id_guia);
		  
		    $this->db->update('factura_clientes', $data3);

		    $this->db->where('id', $idfactura);
		  
		    $this->db->update('factura_clientes', $data3);

		}


		/******* CUENTAS CORRIENTES ****/

		 $query = $this->db->query("SELECT cc.id as idcuentacontable FROM cuenta_contable cc WHERE cc.nombre = 'FACTURAS POR COBRAR'");
		 $row = $query->result();
		 $row = $row[0];
		 $idcuentacontable = $row->idcuentacontable;	


			// VERIFICAR SI CLIENTE YA TIENE CUENTA CORRIENTE
		 $query = $this->db->query("SELECT co.idcliente, co.id as idcuentacorriente  FROM cuenta_corriente co
		 							WHERE co.idcuentacontable = '$idcuentacontable' and co.idcliente = '" . $idcliente . "'");
    	 $row = $query->result();
	
		if ($query->num_rows()==0){	
			$cuenta_corriente = array(
		        'idcliente' => $idcliente,
		        'idcuentacontable' => $idcuentacontable,
		        'saldo' => $ftotal,
		        'fechaactualiza' => $fechafactura
			);
			$this->db->insert('cuenta_corriente', $cuenta_corriente); 
			$idcuentacorriente = $this->db->insert_id();


		}else{
			$row = $row[0];
			$query = $this->db->query("UPDATE cuenta_corriente SET saldo = saldo + " . $ftotal . " where id = " .  $row->idcuentacorriente );
			$idcuentacorriente =  $row->idcuentacorriente;
		}

		$detalle_cuenta_corriente = array(
	        'idctacte' => $idcuentacorriente,
	        'tipodocumento' => $tipodocumento,
	        'numdocumento' => $numfactura,
	        'saldoinicial' => $ftotal,
	        'saldo' => $ftotal,
	        'fechavencimiento' => $fechavenc,
	        'fecha' => $fechafactura
		);

		$this->db->insert('detalle_cuenta_corriente', $detalle_cuenta_corriente); 	


		$cartola_cuenta_corriente = array(
	        'idctacte' => $idcuentacorriente,
	        'idcuenta' => $idcuentacontable,
	        'tipodocumento' => $tipodocumento,
	        'numdocumento' => $numfactura,
	        'glosa' => 'Registro de Factura en Cuenta Corriente',
	        'fecvencimiento' => $fechavenc,
	        'valor' => $ftotal,
	        'origen' => 'VENTA',
	        'fecha' => $fechafactura
		);

		$this->db->insert('cartola_cuenta_corriente', $cartola_cuenta_corriente); 			

		/*****************************************/


        $resp['success'] = true;
		$resp['idfactura'] = $idfactura;

		$this->Bitacora->logger("I", 'factura_clientes', $idfactura);

		
        

        echo json_encode($resp);
	}

	

}









