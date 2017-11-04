<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ordencompra extends CI_Controller {



	public function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}

	public function updateforzada(){

		$resp = array();
		$data = json_decode($this->input->post('items'));
		$numero = $this->input->post('numero');
		$id = $this->input->post('id');
		$idbodega = $this->input->post('idbodega');
		$fecha = $this->input->post('fecha');
		$tipodocumento="4";
				

		$data2 = array(
	        'semicumplida' => "SI",
	        'cumplida' => "SI",
	        'forzada' => "SI"

	    	);

    	$this->db->where('id', $id);

		$this->db->update('orden_compra', $data2);


        $resp['success'] = true;

        $this->Bitacora->logger("M", 'orden_compra', $id);
        
        echo json_encode($resp);

	}

	public function update(){

		$resp = array();
		$data = json_decode($this->input->post('items'));
		$numero = $this->input->post('numero');
		$id = $this->input->post('id');
		$idbodega = $this->input->post('idbodega');
		$fecha = $this->input->post('fecha');
		$tipodocumento="4";
				

		$data2 = array(
	        'semicumplida' => "SI",
	        'id_bodega' => $idbodega
	    	);

    	$this->db->where('id', $id);

		$this->db->update('orden_compra', $data2);


		foreach($data as $v){

		   $producto = $v->id_producto;

		   $query = $this->db->query('SELECT * FROM productos WHERE id="'.$producto.'"');
		   if($query->num_rows()>0){

			 	$row = $query->first_row();

			 	$saldo = ($row->stock)+($v->stock);
			 	$ppm = ($row->p_promedio);
			 	$pmc = ($row->p_may_compra);
			 	$pco = ($row->p_costo);
			 	

		   };
	         $puc = ($v->valor);
	         if ($pmc < $v->valor){
	         	
	         	$pmc = ($v->valor);

	         };

             $ppm = (($pco+$puc)/2);


		   $prod = array(
	         'stock' => $saldo,
	         'p_ult_compra' => $puc,
	         'p_may_compra' => $pmc,
	         'p_promedio' => $ppm
	         
	    	);

	    	$this->db->where('id', $producto);

	    	$this->db->update('productos', $prod);

	    	///GRABAR EXISTENCIA DETALL

	    	$datos2 = array(

				'num_movimiento' => $numero,
		        'id_producto' => $v->id_producto,
		        'id_tipo_movimiento' => $tipodocumento,
		        'valor_producto' =>  $v->valor,
		        'cantidad_entrada' => $v->cantidad,
		        'fecha_movimiento' => $fecha
			);

			$this->db->insert('existencia_detalle', $datos2);


			 $query = $this->db->query('SELECT * FROM existencia WHERE id_producto='.$producto.'');
	    	 $row = $query->result();
			 $row = $row[0];
	 
			 if ($query->num_rows()>0){
			
	            if ($producto==($row->id_producto)){
				    $datos3 = array(
					'stock' => $saldo,
			        'fecha_ultimo_movimiento' => date('Y-m-d H:i:s')
					);

					$this->db->where('id_producto', $producto);

		    	    $this->db->update('existencia', $datos3);
	    	    }else{

	    	    	$datos3 = array(
					'id_producto' => $producto,
			        'stock' =>  $saldo,
			        'fecha_ultimo_movimiento' =>date('Y-m-d H:i:s')
				
					);
					$this->db->insert('existencia', $datos3);
				}
		    	 	
				}else{
					if ($producto==($row->id_producto)){
					    $datos3 = array(
						'stock' => $saldo,
				        'fecha_ultimo_movimiento' => date('Y-m-d H:i:s')
						);

						$this->db->where('id_producto', $producto);

			    	    $this->db->update('existencia', $datos3);
		    	    }else{

		    	    	$datos3 = array(
						'id_producto' => $producto,
				        'stock' =>  $saldo,
				        'fecha_ultimo_movimiento' =>date('Y-m-d H:i:s')
					
						);
						$this->db->insert('existencia', $datos3);
			    	}
			};
    	



	    	if ($v->stock==$v->cantidad){

	    		$data4 = array(
		        'cant_final' => $v->stock,
		        'valor_prom' => $v->valor,
		        'id_bodega' => $idbodega
		    	);

	    		$this->db->where('id', $v->id);

			    $this->db->update('orden_compra_item', $data4);	
	    		

	    	}else{

	    		$data4 = array(
		        'cant_final' => $v->stock,
		        'cantidad' => $v->stock,
		        'id_bodega' => $idbodega
		    	);

		    	$this->db->where('id', $v->id);

			    $this->db->update('orden_compra_item', $data4);	
	    		

	    		$cantidad = ($v->cantidad - $v->stock);

	    	$orden_compra_item = array(
		        'id_producto' => $v->id_producto,
		        'id_ordencompra' => $id,
		        'subtotal' => $v->precio_base,
		        'cantidad' => $cantidad,
		        'total' => $v->total,
		        'descuento' => $v->dcto,
		        'descuentoprct' => $v->descuentoprct,
		        'id_bodega' => $idbodega,
		        'totaliva' => $v->totaliva
			);



			$this->db->insert('orden_compra_item', $orden_compra_item);	

			$this->Bitacora->logger("I", 'orden_compra_item', $id);

	    	}

	    		
		}

		$rec = 0;
       
		$data = array();
		$query = $this->db->query('SELECT ctz.*, pro.nombre as nombre FROM orden_compra_item ctz
		INNER JOIN productos pro ON (ctz.id_producto = pro.id)
		WHERE ctz.id_ordencompra="'.$id.'" AND ctz.cant_final="'.$rec.'"');

	   	if($query->num_rows()==0){

	   		$data3 = array(
	        'cumplida' => "SI",
	      	);

	    	$this->db->where('id', $id);

			$this->db->update('orden_compra', $data3);
	   	}

        $resp['success'] = true;

        $this->Bitacora->logger("M", 'orden_compra', $id);
        $this->Bitacora->logger("M", 'orden_compra_item', $id);               



        echo json_encode($resp);

	}

	

	public function getAll(){
		$resp = array();

        $start = $this->input->get('start');
        $limit = $this->input->get('limit');
        $opcion = $this->input->get('opcion');
        $nombres = $this->input->get('nombre');
        $semi =" ";

		$countAll = $this->db->count_all_results("orden_compra");
		$data = array();
		$total = 0;

		if($opcion == "Rut"){

			$query = $this->db->query('SELECT ctz.*, pro.nombres as empresa, pro.rut as rut, pro.direccion as direccion, ciu.nombre as ciudad, com.nombre as comuna, gir.nombre as giro 
			FROM orden_compra ctz
			INNER JOIN clientes pro ON (ctz.id_proveedor = pro.id)
			INNER JOIN ciudad ciu ON (pro.id_ciudad = ciu.id)
			INNER JOIN comuna com ON (pro.id_comuna = com.id)
			INNER JOIN cod_activ_econ gir ON (pro.id_giro = gir.id)
			WHERE ctz.semicumplida="'.$semi.'" and pro.rut = '.$nombres.'
			order by ctz.id desc		
			limit '.$start.', '.$limit.''		 

			);

		}elseif($opcion == "Numero"){

			$query = $this->db->query('SELECT ctz.*, pro.nombres as empresa, pro.rut as rut, pro.direccion as direccion, ciu.nombre as ciudad, com.nombre as comuna, gir.nombre as giro 
			FROM orden_compra ctz
			INNER JOIN clientes pro ON (ctz.id_proveedor = pro.id)
			INNER JOIN ciudad ciu ON (pro.id_ciudad = ciu.id)
			INNER JOIN comuna com ON (pro.id_comuna = com.id)
			INNER JOIN cod_activ_econ gir ON (pro.id_giro = gir.id)
			WHERE ctz.semicumplida="'.$semi.'" and ctz.num_orden = '.$nombres.'
			order by ctz.id desc		
			limit '.$start.', '.$limit.''		 

			);

		}else if($opcion == "Nombre"){

			$sql_nombre = "";
	        $arrayNombre =  explode(" ",$nombres);

	        foreach ($arrayNombre as $nombre) {
	        	$sql_nombre .= "and pro.nombres like '%".$nombre."%' ";
	        }

			$query = $this->db->query('SELECT ctz.*, pro.nombres as empresa, pro.rut as rut, pro.direccion as direccion, ciu.nombre as ciudad, com.nombre as comuna, gir.nombre as giro 
			FROM orden_compra ctz
			INNER JOIN clientes pro ON (ctz.id_proveedor = pro.id)
			INNER JOIN ciudad ciu ON (pro.id_ciudad = ciu.id)
			INNER JOIN comuna com ON (pro.id_comuna = com.id)
			INNER JOIN cod_activ_econ gir ON (pro.id_giro = gir.id)
			WHERE ctz.semicumplida="'.$semi.'" ' . $sql_nombre . '
			order by ctz.id desc		
			limit '.$start.', '.$limit.'');

		}else if($opcion == "Todos"){

			$query = $this->db->query('SELECT ctz.*, pro.nombres as empresa, pro.rut as rut, pro.direccion as direccion, ciu.nombre as ciudad, com.nombre as comuna, gir.nombre as giro 
			FROM orden_compra ctz
			INNER JOIN clientes pro ON (ctz.id_proveedor = pro.id)
			INNER JOIN ciudad ciu ON (pro.id_ciudad = ciu.id)
			INNER JOIN comuna com ON (pro.id_comuna = com.id)
			INNER JOIN cod_activ_econ gir ON (pro.id_giro = gir.id)
			WHERE ctz.semicumplida="'.$semi.'"
			order by ctz.id desc		
			limit '.$start.', '.$limit.'');

		}else{

			$query = $this->db->query('SELECT ctz.*, pro.nombres as empresa, pro.rut as rut, pro.direccion as direccion, ciu.nombre as ciudad, com.nombre as comuna, gir.nombre as giro 
			FROM orden_compra ctz
			INNER JOIN clientes pro ON (ctz.id_proveedor = pro.id)
			INNER JOIN ciudad ciu ON (pro.id_ciudad = ciu.id)
			INNER JOIN comuna com ON (pro.id_comuna = com.id)
			INNER JOIN cod_activ_econ gir ON (pro.id_giro = gir.id)
			WHERE ctz.semicumplida="'.$semi.'"
			order by ctz.id desc		
			limit '.$start.', '.$limit.'');
		};


		foreach ($query->result() as $row)
		{

			$rutautoriza = $row->rut;
		   	if (strlen($rutautoriza) == 8){
		      $ruta1 = substr($rutautoriza, -1);
		      $ruta2 = substr($rutautoriza, -4, 3);
		      $ruta3 = substr($rutautoriza, -7, 3);
		      $ruta4 = substr($rutautoriza, -8, 1);
		      $row->rut = ($ruta4.".".$ruta3.".".$ruta2."-".$ruta1);
		    };
		    if (strlen($rutautoriza) == 9){
		      $ruta1 = substr($rutautoriza, -1);
		      $ruta2 = substr($rutautoriza, -4, 3);
		      $ruta3 = substr($rutautoriza, -7, 3);
		      $ruta4 = substr($rutautoriza, -9, 2);
		      $row->rut = ($ruta4.".".$ruta3.".".$ruta2."-".$ruta1);
		   
		    };
		    if (strlen($rutautoriza) == 2){
		      $ruta1 = substr($rutautoriza, -1);
		      $ruta2 = substr($rutautoriza, -4, 1);
		      $row->rut = ($ruta2."-".$ruta1);
		     
		    };
		    $row->iva = ($row->total - $row->pretotal);
			$data[] = $row;
			$total = $total +1;
			$countAll = $total;
		}
        $resp['success'] = true;
        $resp['total'] = $countAll;
        $resp['data'] = $data;

        echo json_encode($resp);
	}

	public function detalle(){
		$resp = array();

        $nombre = $this->input->get('nombre');
       
		$data = array();
		$query = $this->db->query('SELECT ctz.*, pro.nombre as nombre FROM orden_compra_item ctz
		INNER JOIN productos pro ON (ctz.id_producto = pro.id)
		WHERE ctz.id_ordencompra="'.$nombre.'"');

		foreach ($query->result() as $row)
		{
			$data[] = $row;
		}
        $resp['success'] = true;
        $resp['data'] = $data;

        echo json_encode($resp);
	}

	public function detallerec(){
		$resp = array();

        $nombre = $this->input->get('nombre');
        $rec = 0;
       
		$data = array();
		$query = $this->db->query('SELECT ctz.*, pro.nombre as nombre FROM orden_compra_item ctz
		INNER JOIN productos pro ON (ctz.id_producto = pro.id)
		WHERE ctz.id_ordencompra like "'.$nombre.'" AND ctz.cant_final="'.$rec.'"');

		foreach ($query->result() as $row)
		{
			$data[] = $row;
		}
        $resp['success'] = true;
        $resp['data'] = $data;

        echo json_encode($resp);
	}

	public function recepcion(){
		$resp = array();

        $start = $this->input->get('start');
        $limit = $this->input->get('limit');
        $nombre = "SI";


		$countAll = $this->db->count_all_results("orden_compra");
		$data = array();
		

		$query = $this->db->query('SELECT ctz.*, pro.nombres as empresa, pro.rut as rut, pro.direccion as direccion, ciu.nombre as ciudad, com.nombre as comuna, gir.nombre as giro 
		FROM orden_compra ctz
		INNER JOIN clientes pro ON (ctz.id_proveedor = pro.id)
		INNER JOIN ciudad ciu ON (pro.id_ciudad = ciu.id)
		INNER JOIN comuna com ON (pro.id_comuna = com.id)
		INNER JOIN cod_activ_econ gir ON (pro.id_giro = gir.id)


		WHERE ctz.semicumplida="'.$nombre.'"');

		foreach ($query->result() as $row)
		{
			$data[] = $row;
		}
        $resp['success'] = true;
        $resp['total'] = $countAll;
        $resp['data'] = $data;

        echo json_encode($resp);
	}

	public function forzada(){
		$resp = array();

        $start = $this->input->get('start');
        $limit = $this->input->get('limit');
        $nombre = "SI";


		$countAll = $this->db->count_all_results("orden_compra");
		$data = array();
		$query = $this->db->query('SELECT ctz.*, pro.nombres as empresa, pro.rut as rut, pro.direccion as direccion, ciu.nombre as ciudad, com.nombre as comuna, gir.nombre as giro 
		FROM orden_compra ctz
		INNER JOIN clientes pro ON (ctz.id_proveedor = pro.id)
		INNER JOIN ciudad ciu ON (pro.id_ciudad = ciu.id)
		INNER JOIN comuna com ON (pro.id_comuna = com.id)
		INNER JOIN cod_activ_econ gir ON (pro.id_giro = gir.id)
		WHERE ctz.forzada="'.$nombre.'"');

		foreach ($query->result() as $row)
		{
			$data[] = $row;
		}
        $resp['success'] = true;
        $resp['total'] = $countAll;
        $resp['data'] = $data;

        echo json_encode($resp);
	}

	public function save(){
		$resp = array();

		$idproveedor = $this->input->post('idproveedor');
		$dataproveedor = json_decode($this->input->post('dataproveedor'));
		$items = json_decode($this->input->post('items'));
		$fpretotal = $this->input->post('fpretotal');
		$fdcto = $this->input->post('fdcto');
		$ftotal = $this->input->post('ftotal');

		$orden = 4;
       		
		$query = $this->db->query('SELECT * FROM correlativos WHERE id like "'.$orden.'"');

		if($query->num_rows()>0){

			$ord = $query->result();
		    $ord = $ord[0];
		    $num_orden = $ord->correlativo+1;
	    }

	    $ad = 4;
		$data = array(
	        'correlativo' => $num_orden
	    );

		$this->db->where('id', $ad);
		
		$this->db->update('correlativos', $data); 

		$orden_compra = array(
			    'num_orden' => $num_orden,
	            'nombre_contacto' => ucfirst(strtolower($dataproveedor->nombre_contacto)),
				'telefono_contacto' => $dataproveedor->telefono_contacto,
				'mail_contacto' => $dataproveedor->mail_contacto,
		        'id_proveedor' => $idproveedor,
		        'descuento' => $fdcto,
		        'pretotal' => $fpretotal,
		        'total' => $ftotal,
				'fecha' => date('Y-m-d H:i:s'),
		);

		$this->db->insert('orden_compra', $orden_compra); 
		$idordencompra = $this->db->insert_id();

		foreach($items as $v){
			
			$orden_compra_item = array(
		        'id_producto' => $v->id,
		        'id_ordencompra' => $idordencompra,
		        'subtotal' => $v->precio_base,
		        'cantidad' => $v->cantidad,
		        'total' => $v->total,
		        'descuento' => $v->dcto,
		        'descuentoprct' => $v->descuentoprct,		        
		        'totaliva' => $v->totaliva
			);

			$this->db->insert('orden_compra_item', $orden_compra_item); 

		}

		
        $resp['success'] = true;

         $this->Bitacora->logger("I", 'orden_compra', $num_orden);


        echo json_encode($resp);
	}

	public function exportPDF(){
		
		$idordencompra = $this->input->get('idordencompra');
		
		$query = $this->db->query('SELECT * FROM orden_compra WHERE id like "'.$idordencompra.'"');

		if($query->num_rows()>0){

			$ord = $query->result();
		    $ord = $ord[0];
		    $emitido = "SI";
		    $ad = $ord->id;
	    }

	 	$data = array(
	        'emitida' => $emitido
	    );

		$this->db->where('id', $ad);
		
		$this->db->update('orden_compra', $data);

		$query = $this->db->query('SELECT 
			ctz.id, ctz.pretotal, ctz.total, ctz.num_orden, ctz.telefono_contacto, ctz.mail_contacto, ctz.nombre_contacto, cli.nombres as empresa , cli.rut as rut_empresa, cli.direccion as direccion_empresa, cli.fono as fono_empresa, ctz.observaciones, cae.nombre as giro_empresa, c.nombre as ciudad_empresa, v.nombre as nom_vendedor, pa.nombre as conpago FROM orden_compra ctz
			INNER JOIN clientes cli on (ctz.id_proveedor = cli.id)
			LEFT JOIN cod_activ_econ cae on cli.id_giro = cae.id
			LEFT JOIN cond_pago pa on cli.id_pago = pa.id
			LEFT JOIN ciudad c on cli.id_ciudad = c.id
			LEFT JOIN vendedores v on ctz.id_vendedor = v.id
			WHERE ctz.id = '.$idordencompra.'
		');

		//cotizacion header
		$row = $query->result();
		$row = $row[0];
		//items
		$items = $this->db->get_where('orden_compra_item', array('id_ordencompra' => $row->id));
		//variables generales
		$codigo = $row->num_orden;
		$nombre = $row->empresa;
		$fecha = $row->fecha;
        list($anio, $mes, $dia) = explode("-",$fecha); 
		$direccion = $row->direccion_empresa;
		$nombre_contacto = $row->nombre_contacto;
        $fono_contacto = $row->telefono_contacto;
				

		$html = '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Untitled Document</title>
		<style type="text/css">
		td {
			font-size: 16px;
		}
		p {
		}
		</style>
		</head>

		<body>
		<table width="987px" height="602" border="0">
		  <tr>
		    <td width="197px"><img src="http://localhost/Infosys_web/Infosys_web/resources/images/logoinfo&sys.jpg" width="150" height="136" /></td>
		    <td width="493px" style="font-size: 14px;text-align:center;vertical-align:text-top"	>
		    <p>GOTRU ALIMENTOS SPA.</p>
		    <p>RUT:78.549.450-4</p>
		    <p>8 ORIENTE, TALCA</p>
		    <p>Fonos: </p>
		    <p>http://www.gotru.cl</p>
		    </td>
		    <td width="296px" style="font-size: 16px;text-align:left;vertical-align:text-top"	>
		          <p>COTIZACION N°: '.$codigo.'</p>
		          <!--p>&nbsp;</p-->
		          <p>FECHA EMISION : '.$fecha.'</p>
		          <!--p>&nbsp;</p-->
		          <p>VALIDEZ ORDEN DE COMPRA : 15 DIAS</p>
		          <!--p>&nbsp;</p-->
		          <p>ESTADO : Pendiente</p>
			</td>
		  </tr>
		  <tr>
			<td style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:center;" colspan="3"><h1>ORDEN DE COMPRA</h1></td>
		  </tr>
		  <tr>
		    <td colspan="3" width="987px" >
		    	<table width="987px" border="0">
		    		<tr>
		    			<td width="197px">Sr.(es):</td>
		    			<td width="395px">'. $row->empresa .'</td>
		    			<td width="197px">Rut:</td>
		    			<td width="197px">'. number_format(substr($row->rut_empresa, 0, strlen($row->rut_empresa) - 1),0,".",".")."-".substr($row->rut_empresa,-1).'</td>
		    		</tr>
		    		<tr>
		    			<td width="197px">Direcci&oacute;n:</td>
		    			<td width="395px">'. $direccion .'</td>
		    			<td width="197px">Tel&eacute;fono:</td>
		    			<td width="197px">'. $row->fono_empresa .'</td>
		    		</tr>		    		
		    		<tr>
		    			<td width="197px">Giro:</td>
		    			<td width="395px">'. $row->giro_empresa .'</td>
		    			<td width="197px">Fax:</td>
		    			<td width="197px">&nbsp;</td>
		    		</tr>		    				    		
		    		<tr>
		    			<td width="197px">Ciudad:</td>
		    			<td width="395px">' . $row->ciudad_empresa .'</td>
		    			<td width="197px">O.C N°:</td>
		    			<td width="197px">&nbsp;</td>
		    		</tr>		    				    				    		
		    		<tr>
		    			<td width="197px">Contacto:</td>
		    			<td width="395px">' .$nombre_contacto.'</td>
		    			<td width="197px">Forma Pago:</td>
		    			<td width="197px">' .$condpago.'</td>


		    		</tr>		    				    				    				    		
		    	</table>
			</td>
		  </tr>
		  <tr>
		    <td colspan="3" >
		    	<table width="987px" cellspacing="0" cellpadding="0" >
		      <tr>
		        <td width="148px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" >Cantidad</td>
		        <td width="395px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:center;" >Descripci&oacute;n</td>
		        <td width="148px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" >Precio/Unidad</td>
		        <td width="148px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" >Precio/Oferta</td>
		        <td width="148px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" >Total</td>
		      </tr>';
		$descripciones = '';
		$i = 0;
		foreach($items->result() as $v){
			//$i = 0;
			//while($i < 30){
			$this->db->where('id', $v->id_producto);
			$producto = $this->db->get("productos");	
			$producto = $producto->result();
			$producto = $producto[0];
			
			$html .= '<tr>
			<td style="text-align:right">'.number_format($v->cantidad,0,'.',',').'&nbsp;&nbsp;</td>			
			<td style="text-align:left">'.$producto->nombre.'</td>			
			<td align="right">$ '.number_format($v->subtotal, 0, '.', ',').'</td>
			<td align="right">$ '.number_format($v->subtotal - ($v->descuento/$v->cantidad), 0, '.', ',').'</td>

			<td align="right">$ '.number_format($v->subtotal - ($v->descuento/$v->cantidad), 0, '.', ',').'</td>
			</tr>';
			
			//}
			$i++;
		}

		// RELLENA ESPACIO
		while($i < 30){
			$html .= '<tr><td colspan="5">&nbsp;</td></tr>';
			$i++;
		}


		$html .= '<tr><td colspan="5">&nbsp;</td></tr></table></td>
		  </tr>
		  <tr>
		  	<td colspan="3" style="border-top:1pt solid black;text-align:center;"><p><b>VALORES EN DETALLE NETOS+IVA</b></p></td>
		  </tr>
		  
		  <tr>
		  	<td colspan="2" rowspan="6" style="font-size: 12px;border-bottom:1pt solid black;border-top:1pt solid black;border-left:1pt solid black;border-right:1pt solid black;text-align:left;">&nbsp;</td>
		  	<td>
				<table width="296px" border="0">
					<tr>
						<td width="150px" style="font-size: 20px;text-align:left;">Afecto</td>
						<td width="146px" style="text-align:right;">$ '. number_format($row->pretotal, 0, '.', ',') .'</td>
					</tr>
				</table>
		  	</td>
		  </tr>	
		  <tr>
		  	<td>
				<table width="296px" border="0">
					<tr>
						<td width="150px" style="font-size: 20px;text-align:left;">IVA</td>
						<td width="146px" style="text-align:right;">$ '.number_format($row->total - ($row->subtotal), 0, '.', ',').'</td>
					</tr>
				</table>
		  	</td>		  
		  </tr>
		  <tr>
		  	<td>
				<table width="296px" border="0">
					<tr>
						<td width="150px" style="font-size: 20px;text-align:left;">Descuento</td>
						<td width="146px" style="text-align:right;">$ '. number_format($row->descuento, 0, ',', '.') .'</td>
					</tr>
				</table>
		  	</td>		  
		  </tr>	
		  <tr>
		  	<td>
				<table width="296px" border="0">
					<tr>
						<td width="150px" style="font-size: 20px;text-align:left;">Total</td>
						<td width="146px" style="text-align:right;">$ '. number_format($row->total, 0, '.', ',') .'</td>
					</tr>
				</table>
		  	</td>		  
		  </tr>
		  <tr>
		  	<td>&nbsp;</td>		  
		  </tr>
		  <tr>
		  	<td>&nbsp;</td>		  
		  </tr>		  		  		  	  
		  <tr>
		    <td colspan="2" style="text-align:right;font-style: italic;"><b>EL SERVICIO MARCA LA DIFERENCIA!!!</b></td>
		  </tr>
		  
		</table>
		</body>
		</html>
		';
		//==============================================================
		//==============================================================
		//==============================================================

		include(dirname(__FILE__)."/../libraries/MPDF54/mpdf.php");

		$mpdf= new mPDF(
			'',    // mode - default ''
			'',    // format - A4, for example, default ''
			0,     // font size - default 0
			'',    // default font family
			15,    // margin_left
			15,    // margin right
			16,    // margin top
			16,    // margin bottom
			9,     // margin header
			9,     // margin footer
			'L'    // L - landscape, P - portrait
			);  

		$mpdf->WriteHTML($html);
		$mpdf->Output("CF_{$codigo}.pdf", "I");
		
		exit;
	}

	
}









