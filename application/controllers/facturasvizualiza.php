<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Facturasvizualiza extends CI_Controller {



	public function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}

	public function saveobserva(){

		$resp = array();
		$rut = $this->input->post('rut');
		$nombre = $this->input->post('nombre');
		$camion = $this->input->post('camion');
		$carro = $this->input->post('carro');
		$fono = $this->input->post('fono');
		$numero = $this->input->post('numero');

		$observa = array(
			'rut' => $rut,
			'nombre' => $nombre,
	        'id_documento' => $numero,
	        'fono' => $fono,
	        'pat_camion' => $camion,
	        'pat_carro' => $carro	          
		);

		$this->db->insert('observacion_facturas', $observa);
		$idobserva = $this->db->insert_id();
		$resp['success'] = true;
		$resp['idobserva'] = $idobserva;
		echo json_encode($resp); 


	}

	public function validaRut(){

		
		$resp = array();
		$rut = $this->input->get('valida');
        $iddocu = 1;
		
		if(strpos($rut,"-")==false){
	        $RUT[0] = substr($rut, 0, -1);
	        $RUT[1] = substr($rut, -1);
	    }else{
	        $RUT = explode("-", trim($rut));
	    }
	    $elRut = str_replace(".", "", trim($RUT[0]));
	    $factor = 2;
	    $suma=0;
	    for($i = strlen($elRut)-1; $i >= 0; $i--):
	        $factor = $factor > 7 ? 2 : $factor;
	        $suma += $elRut{$i}*$factor++;
	    endfor;
	    $resto = $suma % 11;
	    $dv = 11 - $resto;
	    if($dv == 11){
	        $dv=0;
	    }else if($dv == 10){
	        $dv="k";
	    }else{
	        $dv=$dv;
	    }

	   if($dv == trim(strtolower($RUT[1]))){

	   	    $query = $this->db->query('SELECT * FROM observacion_facturas 
	   	    WHERE rut like "'.$rut.'"');

	   	    if($query->num_rows()>0){

			   $data = array();
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
				$data[] = $row;
				}
	   			$resp['observa'] = $row;
	   			$resp['existe'] = true;
	   			$resp['success'] = true;
	   			echo json_encode($resp);
	        	return false;

	   		}else{

	   			$rutautoriza = $rut;
		   	if (strlen($rutautoriza) == 8){
		      $ruta1 = substr($rutautoriza, -1);
		      $ruta2 = substr($rutautoriza, -4, 3);
		      $ruta3 = substr($rutautoriza, -7, 3);
		      $ruta4 = substr($rutautoriza, -8, 1);
		      $rut = ($ruta4.".".$ruta3.".".$ruta2."-".$ruta1);
		    };
		    if (strlen($rutautoriza) == 9){
		      $ruta1 = substr($rutautoriza, -1);
		      $ruta2 = substr($rutautoriza, -4, 3);
		      $ruta3 = substr($rutautoriza, -7, 3);
		      $ruta4 = substr($rutautoriza, -9, 2);
		      $rut = ($ruta4.".".$ruta3.".".$ruta2."-".$ruta1);		   
		    };
		     if (strlen($rutautoriza) == 2){
		      $ruta1 = substr($rutautoriza, -1);
		      $ruta2 = substr($rutautoriza, -4, 1);
		      $rut = ($ruta2."-".$ruta1);		     
		    };		  
			
	        $resp['rut'] = $rut;
	        $resp['existe'] = false;
	        $resp['success'] = true;
	        echo json_encode($resp);
	        return false;

	   		};

	   		
	   }else{
	   	    $resp['success'] = false;
	   	    echo json_encode($resp);
	        return false;
	   }
	
	}

	public function numerofactura(){

		$resp = array();
		$factura = $this->input->get('factura');
       		
		$query = $this->db->query('SELECT * FROM correlativos WHERE id like "'.$factura.'"');

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

		$countAll = $this->db->count_all_results("factura_clientes");
		$data = array();
		$query = $this->db->query('SELECT acc.*, c.nombres as nombre_cliente, c.rut as rut_cliente, v.nombre as nom_vendedor	FROM factura_clientes acc
			left join clientes c on (acc.id_cliente = c.id)
			left join vendedores v on (acc.id_vendedor = v.id)
			
			limit '.$start.', '.$limit.' ' 

		);

		$data = array();
		
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


		 
			$data[] = $row;
		}
        $resp['success'] = true;
        $resp['total'] = $countAll;
        $resp['data'] = $data;

        echo json_encode($resp);
	}

	public function save(){
		
		$resp = array();

		$idcliente = $this->input->post('idcliente');
		$numfactura = $this->input->post('numfactura');
		$observacion = $this->input->post('observacion');
		$idobserva = $this->input->post('idobserva');
		$fechafactura = $this->input->post('fechafactura');
		$fechavenc = $this->input->post('fechavenc');
		$vendedor = $this->input->post('idvendedor');
		$datacliente = json_decode($this->input->post('datacliente'));
		$items = json_decode($this->input->post('items'));
		$recitems = json_decode($this->input->post('recitems'));
		$neto = $this->input->post('netofactura');
		$fiva = $this->input->post('ivafactura');
		$fafecto = $this->input->post('afectofactura');
		$ftotal = $this->input->post('totalfacturas');
		$ftotal_unformat = $this->input->post('totalfacturasUnformat');
		$tipodocumento = $this->input->post('tipodocumento');
		$formapago = $this->input->post('formapago');
		$idpreventa = $this->input->post('preventa');
		$numcheque = $this->input->post('numcheque');
		
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
	        'observacion' => $observacion,
	        'id_observa' => $idobserva,
	        'sub_total' => $neto,
	        'descuento' => ($neto - $fafecto),
	        'neto' => $neto,
	        'id_cond_venta' => $formapago,
	        'iva' => $fiva,
	        'totalfactura' => $ftotal,
	        'fecha_factura' => $fechafactura,
	        'fecha_venc' => $fechavenc,
	                  
		);

		$this->db->insert('factura_clientes', $factura_cliente); 
		$idfactura = $this->db->insert_id();

		$preventa = array(

			'id_documento' => $idfactura

		);

		$this->db->where('id', $idpreventa);
		$this->db->update('preventa', $preventa);

		foreach($items as $v){
			$factura_clientes_item = array(
		        'id_producto' => $v->id_producto,
		        'id_factura' => $idfactura,
		        'num_factura' => $numfactura,
		        'precio' => $v->valor_unit,
		        'cantidad' => $v->cantidad,
		        'neto' => $v->neto,
		        'descuento' => $v->desc,
		        'iva' => $v->iva,
		        'totalproducto' => $v->total,
		        'fecha' => $fechafactura,
		        'id_despacho' => 0
			);

		$producto = $v->id_producto;

		$this->db->insert('detalle_factura_cliente', $factura_clientes_item);
		
		$query = $this->db->query('SELECT * FROM productos WHERE id="'.$producto.'"');
		if($query->num_rows()>0){

		 	$row = $query->first_row();
		 	$saldo = ($row->stock)-($v->cantidad); 

		};

		$query2 = $this->db->query('SELECT * FROM existencia WHERE id_producto='.$producto.'');
    	$row = $query2->result();
			 
		if ($query2->num_rows()>0){

			$row = $row[0];
			
	        if ($producto==($row->id_producto)){
				$datos3 = array(
					'stock' => $saldo,
			        'fecha_ultimo_movimiento' => $fechafactura
			    );

					$this->db->where('id_producto', $producto);

		    	    $this->db->update('existencia', $datos3);
	    	}else{

    	    	$datos3 = array(
				'id_producto' => $producto,
		        'stock' =>  $saldo,
		        'fecha_ultimo_movimiento' =>$fechafactura
			
				);
				$this->db->insert('existencia', $datos3);
	    	 	}
		}else{
			  	$datos3 = array(
				'id_producto' => $producto,
		        'stock' =>  $saldo,
		        'fecha_ultimo_movimiento' =>$fechafactura
			
				);
				$this->db->insert('existencia', $datos3);
	    	
		}; /******** if CONSULTA *******/


		$datos2 = array(
			'num_movimiento' => $numfactura,
	        'id_producto' => $v->id_producto,
	        'id_tipo_movimiento' => $tipodocumento,
	        'valor_producto' =>  $v->valor_unit,
	        'cantidad_salida' => $v->cantidad,
	        'fecha_movimiento' => $fechafactura
		);

		$this->db->insert('existencia_detalle', $datos2);

		$datos = array(
         'stock' => $saldo,
    	);

    	$this->db->where('id', $producto);

    	$this->db->update('productos', $datos);
    	
		}; /****** forech *******/


		/******* CUENTAS CORRIENTES ****/
		$total_cancelacion = 0;
		$total_factura_cta_cte = 0;
		foreach($recitems as $ri){ // SUMAR MONTOS PARA VER TOTAL CANCELACION
			$total_factura_cta_cte += $ri->valor_pago;
			if($ri->id_forma != 3 && $ri->id_forma != 5 ){ // NO CONSIDERA PAGOS A CREDITO
				$total_cancelacion += $ri->valor_pago;
			}
		}
		 

		 if($tipodocumento == 1 || $tipodocumento == 2){
		 	 $nombre_cuenta = $tipodocumento == 1 ? "FACTURAS POR COBRAR" : "BOLETAS POR COBRAR";
		 	 //$nombre_cuenta = "FACTURAS POR COBRAR";
			 $query = $this->db->query("SELECT cc.id as idcuentacontable FROM cuenta_contable cc WHERE cc.nombre = '$nombre_cuenta'");
			 $row = $query->result();
			 $row = $row[0];
			 $idcuentacontable = $row->idcuentacontable;	

			 if($tipodocumento == 2 && $formapago != 3 && $formapago != 5){
			 	 $idcliente = 1;
				 $query = $this->db->query("SELECT id  FROM clientes
				 							WHERE rut = '19' limit 1");		
				 $datos_cliente	= $query->row();
				 $idcliente = $datos_cliente->id;
			 }

				// VERIFICAR SI CLIENTE YA TIENE CUENTA CORRIENTE
			 $query = $this->db->query("SELECT co.idcliente, co.id as idcuentacorriente  FROM cuenta_corriente co
			 							WHERE co.idcuentacontable = '$idcuentacontable' and co.idcliente = '" . $idcliente . "'");
	    	 $row = $query->result();
		
			if ($query->num_rows()==0){	
				$cuenta_corriente = array(
			        'idcliente' => $idcliente,
			        'idcuentacontable' => $idcuentacontable,
			        'saldo' => $total_factura_cta_cte,
			        'fechaactualiza' => $fechafactura
				);
				$this->db->insert('cuenta_corriente', $cuenta_corriente); 
				$idcuentacorriente = $this->db->insert_id();


			}else{
				$row = $row[0];
				$query = $this->db->query("UPDATE cuenta_corriente SET saldo = saldo + " . $total_factura_cta_cte . " where id = " .  $row->idcuentacorriente );
				$idcuentacorriente =  $row->idcuentacorriente;
			}

			$detalle_cuenta_corriente = array(
		        'idctacte' => $idcuentacorriente,
		        'tipodocumento' => $tipodocumento,
		        'numdocumento' => $numfactura,
		        'saldoinicial' => $total_factura_cta_cte,
		        'saldo' => $total_factura_cta_cte,
		        'fechavencimiento' => $fechavenc,
		        'fecha' => $fechafactura
			);

			$this->db->insert('detalle_cuenta_corriente', $detalle_cuenta_corriente); 	
			$idDetalleCtaCte = $this->db->insert_id();		

			$cartola_cuenta_corriente = array(
		        'idctacte' => $idcuentacorriente,
		        'idcuenta' => $idcuentacontable,
		        'tipodocumento' => $tipodocumento,
		        'numdocumento' => $numfactura,
		        'glosa' => 'Registro de Factura en Cuenta Corriente',
		        'fecvencimiento' => $fechavenc,
		        'valor' => $total_factura_cta_cte,
		        'origen' => 'VENTA',
		        'fecha' => $fechafactura
			);

			$this->db->insert('cartola_cuenta_corriente', $cartola_cuenta_corriente); 	




			/*  (tabla cond_pago): 

			$formapago : 1 = CONTADO (caja)
			$formapago : 2 = PAGO CHEQUE (cheques por cobrar)
			$formapago : 3 = CREDITO 30 DIAS (nada)
			$formapago : 4 = TARJETA CREDITO (tarjetas)
			$formapago : 5 = CREDITO 60 DIAS (nada)
			$formapago : 6 = TRANSFERENCIA BANCARIA (caja)
			$formapago : 7 = TARJETA DEBITO (caja)

			*/
			$correlativo_cta_cte = null;
			$array_cuentas = array();
			foreach($recitems as $ri){
				$formapago = $ri->id_forma;
				if($formapago == 1 || $formapago == 6 || $formapago == 7){
					$cuenta_cuadratura = 3;
				}else if($formapago == 2){
					$cuenta_cuadratura = 18;
				}else if($formapago == 4){
					$cuenta_cuadratura = 19;
				}

				
				if($formapago != 3 && $formapago != 5 ){ // SI ES CREDITO, NO SE GENERA CANCELACION
					// OBTENEMOS CORRELATIVO CANCELACION


					if(is_null($correlativo_cta_cte)){ // si son varias formas de pago, entonces sólo en la primera genera el movimiento
						 $query = $this->db->query("SELECT correlativo FROM correlativos WHERE nombre = 'CANCELACIONES CTA CTE'");
						 $row = $query->row();
						 $correlativo_cta_cte = $row->correlativo;	


						// guarda movimiento cuenta corriente (comprobante de ingreso ??? )
						$data = array(
					      	'numcomprobante' => $correlativo_cta_cte,
					        'tipo' => 'INGRESO',
					        'proceso' => 'CANCELACION',
					        'glosa' => 'Cancelación de Factura por Caja',
					        'fecha' => date("Y-m-d H:i:s")
						);

						$this->db->insert('movimiento_cuenta_corriente', $data); 
						$idMovimiento = $this->db->insert_id();		
					

						// actualiza correlativo
						$query = $this->db->query("UPDATE correlativos SET correlativo = correlativo + 1 where nombre = 'CANCELACIONES CTA CTE'");

						//Detalle movimiento CARGO

						$data = array(
					      	'idmovimiento' => $idMovimiento,
					        'tipo' => 'CTACTE',
					        'idctacte' => $idcuentacorriente,
					        'idcuenta' => $idcuentacontable,
					        'tipodocumento' => $tipodocumento,
					        'numdocumento' => $numfactura,		
					        'glosa' => 'Cancelación de Factura por Caja',		        
					        'fecvencimiento' => null,		        
					        'debe' => 0,
					        'haber' => $total_cancelacion
						);

						$this->db->insert('detalle_mov_cuenta_corriente', $data); 								
					}
		

					// DETALLE MOVIMIENTO CUADRATURA
					$docpago = $formapago == 2 ? $ri->num_cheque : 0;
					if(!in_array($cuenta_cuadratura, $array_cuentas)){ 
						$data = array(
					      	'idmovimiento' => $idMovimiento,
					        'tipo' => 'CUADRATURA',
					        'idctacte' => null,
					        'idcuenta' => $cuenta_cuadratura,
					        'docpago' => $docpago,
					        'tipodocumento' => null,
					        'numdocumento' => null,		
					        'glosa' => 'Cancelación de Factura por Caja',		        
					        'fecvencimiento' => null,		        
					        //'debe' => $ftotal_unformat,
					        'debe' => $ri->valor_pago,
					        'haber' => 0
						);			
						$this->db->insert('detalle_mov_cuenta_corriente', $data); 	
						array_push($array_cuentas,$cuenta_cuadratura);
					}else{ // se actualiza la cuenta cuadratura (debería suceder sólo con caja)
						$query = $this->db->query("UPDATE detalle_mov_cuenta_corriente SET debe = debe + " . $ri->valor_pago . " where idmovimiento = " .  $idMovimiento . " and idcuenta  = " . $cuenta_cuadratura );

					}


							

					// genera cartola de cancelacion
					$data = array(
				      	'idctacte' => $idcuentacorriente,
				        'idcuenta' => $idcuentacontable,
				        'idmovimiento' => $idMovimiento,
				        'tipodocumento' => $tipodocumento,
				        'numdocumento' => $numfactura,
				        'fecvencimiento' => $fechafactura,
				        'glosa' => 'Cancelación de Factura por Caja',		        
				        //'valor' => $ftotal_unformat,
				        'valor' => $ri->valor_pago,
				        'origen' => 'CTACTE',
				        'fecha' => date("Y-m-d")
					);

					$this->db->insert('cartola_cuenta_corriente', $data);
										
					// REBAJA SALDO
					
					$query = $this->db->query("UPDATE cuenta_corriente SET saldo = saldo - " . $ri->valor_pago . " where id = " .  $idcuentacorriente );
					$query = $this->db->query("UPDATE detalle_cuenta_corriente SET saldo = saldo - " . $ri->valor_pago . " where id = " .  $idDetalleCtaCte );
				}


			} // end foreach




		}
		/*****************************************/

        $resp['success'] = true;
		$resp['idfactura'] = $idfactura;

		$this->Bitacora->logger("I", 'factura_clientes', $idfactura);

		
        

        echo json_encode($resp);
	}


	public function exportPDF(){

		$idfactura = $this->input->get('idfactura');
		$numero = $this->input->get('numfactura');
		$cabecera = $this->db->get_where('factura_clientes', array('id' => $idfactura));	
		$tipodocumento = 1;
		foreach($cabecera->result() as $v){  
				$tipodocumento = $v->tipo_documento; 
		}

		if($tipodocumento == 1){
				$this->exportFacturaPDF($idfactura,$numero);

		}else{

				$this->exportBoletaPDF($idfactura,$numero);

		}

	}

	public function exportFacturaPDF($idfactura,$numero){

		//$idfactura = $this->input->get('idfactura');
		//$numero = $this->input->get('numfactura');

        if ($idfactura){
		$query = $this->db->query('SELECT acc.*, c.direccion as direccion, e.nombre as giro, c.nombres as nombre_cliente, c.rut as rut_cliente, m.nombre as nombre_comuna, s.nombre as nombre_ciudad, v.nombre as nom_vendedor, ob.nombre as nom_observ, ob.rut as rut_obs, c.fono FROM factura_clientes acc
			left join clientes c on (acc.id_cliente = c.id)
			left join cod_activ_econ e on (c.id_giro = e.id)
			left join comuna m on (c.id_comuna = m.id)
			left join ciudad s on (c.id_ciudad = s.id)
			left join vendedores v on (acc.id_vendedor = v.id)
			left join observacion_facturas ob on (acc.id_observa = ob.id)
			WHERE acc.id = '.$idfactura.'');
		}else{
			$query = $this->db->query('SELECT acc.*, c.direccion as direccion, e.nombre as giro, c.nombres as nombre_cliente, c.rut as rut_cliente, m.nombre as nombre_comuna, s.nombre as nombre_ciudad, v.nombre as nom_vendedor, ob.nombre as nom_observ, ob.rut as rut_obs, c.fono FROM factura_clientes acc
			left join clientes c on (acc.id_cliente = c.id)
			left join cod_activ_econ e on (c.id_giro = e.id)
			left join comuna m on (c.id_comuna = m.id)
			left join ciudad s on (c.id_ciudad = s.id)
			left join vendedores v on (acc.id_vendedor = v.id)
			left join observacion_facturas ob on (acc.id_observa = ob.id)
			WHERE acc.num_factura = '.$numero.'

		');


		}

		//cotizacion header
		$row = $query->result();
		$row = $row[0];
		$fecha = $row->fecha_venc;
		list($anio, $mes, $dia) = explode("-",$fecha);
		$fecha2 = $row->fecha_factura;
		list($anio2, $mes2, $dia2) = explode("-",$fecha2);
		 
		//items
		$items = $this->db->get_where('detalle_factura_cliente', array('id_factura' => $row->id));
		//print_r($items->result());exit;
		//variables generales
		$codigo = $row->num_factura;
		$nombre_contacto = $row->nombre_cliente;
		$observacion = $row->observacion;
		$rut_cliente = $row->rut_cliente;
		$rut_obs = $row->rut_obs;
		$nom_obs = $row->nom_observ;
		$direccion = $row->direccion;
		$comuna = $row->nombre_comuna;
		$ciudad = $row->nombre_ciudad;
		$fecha = $row->fecha_venc;
		$giro = $row->giro;
		$fono = $row->fono;
		$cabecera = $this->db->get_where('factura_clientes', array('id' => $row->id));		
		$montoNeto = 0;
	    $ivaTotal = 0;
		$totalFactura = 0;
		foreach($cabecera->result() as $reg){
			$montoNeto = $reg->neto;
			$ivaTotal = $reg->iva;
			$totalFactura = $reg->totalfactura;
		}
				
		$dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
		$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
		 

		$html = '
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<style type="text/css">
<!--
.cajaInput {
	border: 1px dotted #ED1B24;
}
.style5 {color: #FF0000; font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 12px; }
.style6 {	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: bold;
}
.colorTextoFijo {	color:#008F9F;
	font-weight: bold;
	font:Arial, Helvetica, sans-serif;
}
.lineaDivisoria {
	border-bottom-style:dotted;
	border-bottom-color:#ED1B24;
	border-bottom-width:1px;
	height: 2px;
}
.cajaInputIzq {
	border-top-width: 1px;
	border-bottom-width: 1px;
	border-left-width: 1px;
	border-right-width: 1px;
	border-top-style: dotted;
	border-bottom-style: dotted;
	border-left-style: dotted;
	border-right-style: dotted;
	border-top-color: #ED1B24;
	border-bottom-color: #ED1B24;
	border-left-color: #ED1B24;
	border-right-color: #ED1B24;
}
.style9 {font-size: 8px;
font-family: Arial, Helvetica, sans-serif;
}
.style12 {color: #FFFFFF}
.style13 {font-size: 12px; font-family: Arial, Helvetica, sans-serif;}
.style14 {font-family: Arial, Helvetica, sans-serif; font-size: 12px; font-weight: bold; color: #FFFFFF; }
.style15 {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10px;
	font-weight: bold;
	color: #FFFFFF;
}
-->
</style>
</head>

<body>
   <table width="987px" border="0">
   	 
      <tr>
      	<td width="250px"  height="110px">&nbsp;</td>
        <td width="408px"  height="110px" style="font-size: 20px;vertical-align:bottom;">&nbsp;'. $dia2 . '&nbsp;&nbsp;&nbsp;&nbsp;'.$mes2.'&nbsp;&nbsp;&nbsp;&nbsp;'.$anio2.'</td>
        <td width="329px"  height="110px" >
        <table width="329px" border="0">
        	<tr >
        		<td width="329px" height="100px" style="font-size: 20px;vertical-align:bottom;"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$codigo.'</b></td>
        	</tr>
        	<tr >
        		<td width="329px" height="10px">&nbsp;</td>
        	</tr>        	
        </table>
        </td>
      </tr> 
      <tr>
      <td colspan="3" height="110px">
      	<table width="987px" border="0">
      	<tr>
      		<td width="100px" height="30px">&nbsp;</td>
      		<td width="400px" height="30px">'. $nombre_contacto .'</td>
      		<td width="278px" height="30px">&nbsp;</td>
      		<td width="209px" height="30px">' . number_format(substr($rut_cliente,0,strlen($rut_cliente) - 1),0,".",".")."-".substr($rut_cliente,-1) . '</td>
      	</tr>
      	<tr>
      		<td width="100px" height="15px">&nbsp;</td>
      		<td width="400px" height="15px">'. $direccion .'</td>
      		<td width="278px" height="15px">&nbsp;</td>
      		<td width="209px" height="15px">' . $comuna .'</td>
      	</tr>
      	<tr>
      		<td width="100px" height="15px">&nbsp;</td>
      		<td width="400px" height="15px">' . $ciudad . '</td>
      		<td width="278px" height="15px">' . $fono . '</td>
      		<td width="209px" height="15px">' .  $giro . '</td>
      	</tr>
      	<tr>
      		<td width="100px" height="20px">&nbsp;</td>
      		<td width="400px" height="20px">&nbsp;</td>
      		<td width="278px" height="20px">&nbsp;</td>
      		<td width="209px" height="20px">&nbsp;</td>
      	</tr>      	
      	</table>
      </td>
      </tr>
      <tr>
		      	<td colspan="3" >
		      	<table width="987px" border="0">';
		 $tamano_maximo = 460;
		 $i = 1;
		foreach($items->result() as $v){      
			$this->db->where('id', $v->id_producto);
			$producto = $this->db->get("productos");	
			$producto = $producto->result();
			$producto = $producto[0];		

		      $html .= '
		      		
				      	<tr>
				      		<td width="100px" height="20px">' . $producto->codigo . '</td>
				      		<td width="420px" height="20px">' . $producto->nombre . '</td>
				      		<td width="100px" height="20px">' . $v->cantidad . '</td>
				      		<td width="78px" height="20px">UNI</td>
				      		<td width="100px" height="20px">' . number_format($v->precio, 0, ',', '.') . '</td>
				      		<td width="70px" height="20px">' . number_format($v->descuento, 0, ',', '.') . '</td>
				      		<td width="119px" height="20px">' . number_format($v->neto, 0, ',', '.') . '</td>
				      	</tr>
				     ';
		      $i++;
		      $tamano_maximo = $tamano_maximo - 20;
  	}

  	while($tamano_maximo > 0){
  		$html .= '<tr><td colspan="7" height="20px">&nbsp;</td></tr>';
  		$tamano_maximo = $tamano_maximo - 20;	
  	}

    $html .= ' </table>
		      	</td>
		      </tr>
		      <tr>
		      <td  colspan="3" >
		      	<table width="987px" border="0">
		      	<tr >
		      		<td rowspan="3" width="100px" height="60px">&nbsp;</td>
		      		<td rowspan="3" width="420px" height="60px">' . valorEnLetras($totalFactura) . '</td>
		      		<td rowspan="3" width="348px" height="60px">&nbsp;</td>
		      		<td width="119px" height="20px">' . number_format($montoNeto, 0, ',', '.') . '</td>
		      	</tr>		      	
		      	<tr>
			      	<td width="119px" height="20px">' . number_format($ivaTotal, 0, ',', '.') . '</td>
		      	</tr>
		      	<tr>
			      	<td width="119px" height="20px">' . number_format($totalFactura, 0, ',', '.') . '</td>
		      	</tr>		      	
		      	</table>
		      </td>

		      </tr>
		      <tr>
		      	<td  colspan="3" >
		      		<table width="987px" border="0">
		      			<tr>
		      				<td width="698px" height="40px">&nbsp;</td>
		      				<td width="289px" height="40px">'. $nom_obs .'</td>
		      			</tr>
		      			<tr>
		      				<td width="698px" height="20px">&nbsp;</td>
		      				<td width="289px" height="20px">'. number_format(substr($rut_obs,0,strlen($rut_obs) - 1),0,".",".")."-".substr($rut_obs,-1) .'</td>
		      			</tr>		      			
		      		</table>
		      	</td>
		      </tr>
		      </table>';
 

    	/*$html .= '<tr>		        
		      	<td >OBSERVACION : '.$observacion.'</td>
		      	</tr>
		      	<tr>
		      	<td >NOMBRE : '.$nom_obs.'</td>
		      	</tr>	
		      	<td >RUT : '.$rut_obs.'</td>
		      	<tr>		       
		        </tr>';
	*/
      
      $html .='
</body>
</html>
		';
		//==============================================================
		//==============================================================
		//==============================================================
		$this->load->library("mpdf");
		//include(defined('BASEPATH')."/libraries/MPDF54/mpdf.php");
		//include(dirname(__FILE__)."/../libraries/MPDF54/mpdf.php");

		$this->mpdf->mPDF(
			'',    // mode - default ''
			'letter',    // format - A4, for example, default ''
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

		$this->mpdf->WriteHTML($html);
		$this->mpdf->Output("CF_{$codigo}.pdf", "I");		
		exit;
	}


	public function exportBoletaPDF($idfactura,$numero){

		//$idfactura = $this->input->get('idfactura');
		//$numero = $this->input->get('numfactura');

        if ($idfactura){
		$query = $this->db->query('SELECT acc.*, c.direccion as direccion, e.nombre as giro, c.nombres as nombre_cliente, c.rut as rut_cliente, m.nombre as nombre_comuna, s.nombre as nombre_ciudad, v.nombre as nom_vendedor FROM factura_clientes acc
			left join clientes c on (acc.id_cliente = c.id)
			left join cod_activ_econ e on (c.id_giro = e.id)
			left join comuna m on (c.id_comuna = m.id)
			left join ciudad s on (c.id_ciudad = s.id)
			left join vendedores v on (acc.id_vendedor = v.id)
			WHERE acc.id = '.$idfactura.'
		');
		}else{
			$query = $this->db->query('SELECT acc.*, c.direccion as direccion, e.nombre as giro, c.nombres as nombre_cliente, c.rut as rut_cliente, m.nombre as nombre_comuna, s.nombre as nombre_ciudad, v.nombre as nom_vendedor FROM factura_clientes acc
			left join clientes c on (acc.id_cliente = c.id)
			left join cod_activ_econ e on (c.id_giro = e.id)
			left join comuna m on (c.id_comuna = m.id)
			left join ciudad s on (c.id_ciudad = s.id)
			left join vendedores v on (acc.id_vendedor = v.id)
			WHERE acc.num_factura = '.$numero.'

		');


		}

		//cotizacion header
		$row = $query->result();
		$row = $row[0];
		//items
		$items = $this->db->get_where('detalle_factura_cliente', array('id_factura' => $row->id));
		//print_r($items->result());exit;
		//variables generales
		$codigo = $row->num_factura;
		$nombre_contacto = $row->nombre_cliente;
		$rut_cliente = $row->rut_cliente;
		$direccion = $row->direccion;
		$comuna = $row->nombre_comuna;
		$ciudad = $row->nombre_ciudad;
		$giro = $row->giro;


		$cabecera = $this->db->get_where('factura_clientes', array('id' => $row->id));		
		$montoNeto = 0;
		$ivaTotal = 0;
		$totalFactura = 0;
		foreach($cabecera->result() as $reg){
			$montoNeto = $reg->neto;
			$ivaTotal = $reg->iva;
			$totalFactura = $reg->totalfactura;
		}
				
$dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
 

		$html = '
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<style type="text/css">
<!--
.cajaInput {
	border: 1px dotted #ED1B24;
}
.style5 {color: #FF0000; font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 12px; }
.style6 {	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: bold;
}
.colorTextoFijo {	color:#008F9F;
	font-weight: bold;
	font:Arial, Helvetica, sans-serif;
}
.lineaDivisoria {
	border-bottom-style:dotted;
	border-bottom-color:#ED1B24;
	border-bottom-width:1px;
	height: 2px;
}
.cajaInputIzq {
	border-top-width: 1px;
	border-bottom-width: 1px;
	border-left-width: 1px;
	border-right-width: 1px;
	border-top-style: dotted;
	border-bottom-style: dotted;
	border-left-style: dotted;
	border-right-style: dotted;
	border-top-color: #ED1B24;
	border-bottom-color: #ED1B24;
	border-left-color: #ED1B24;
	border-right-color: #ED1B24;
}
.style9 {font-size: 8px;
font-family: Arial, Helvetica, sans-serif;
}
.style12 {color: #FFFFFF}
.style13 {font-size: 12px; font-family: Arial, Helvetica, sans-serif;}
.style14 {font-family: Arial, Helvetica, sans-serif; font-size: 12px; font-weight: bold; color: #FFFFFF; }
.style15 {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10px;
	font-weight: bold;
	color: #FFFFFF;
}
-->
</style>
</head>

<body>
   <table width="650" border="0" cellpadding="0" cellspacing="0">
   	  <tr>
        <td  width="450" ><span class="style6">&nbsp;</span></td>
        <td class="style6"><center><img src="http://www.info-sys.cl/images/logowebinfosys.png" width="150" height="115" /></center></td>
      </tr>
      <tr>
        <td width="450"><span class="style6">&nbsp;</span></td>
        <td class="style6"><center>R.U.T.:99.999.999-9</center></td>
      </tr>
      <tr>
        <td width="450"><span class="style6">&nbsp;</span></td>
        <td class="style6"><center>BOLETA DE VENTA</center></td>

      </tr>
      <tr>
        <td width="450"><span class="style6">&nbsp;</span><span class="colorTextoFijo"></span></td>
		<td class="style6"><center>Nº '.$codigo.'</center></td>
      </tr>
    </table>
    <p align="right"><b>' . $dias[date('w')]." ".date('d')." de ".$meses[date('n')-1]. " del ".date('Y') . '</b></p>

    <br><br>
  <table>
  <tr>
    <td>&nbsp;</td>
    <td class="lineaDivisoria">&nbsp;</td>

    <td>&nbsp;</td>
  </tr>
  </table>

  <table border="0" cellspacing="0" cellpadding="0">
  <tr>
        <td width="63" height="19"><div align="center" class="style6">CANTIDAD</div></td>

        <td width="397"><div align="center" class="style6">DESCRIPCI&Oacute;N</div></td>
        <td width="65"><div align="center" class="style6">P.UNITARIO</div></td>
        <td width="54" ><div align="center" class="style6">DESCUENTO</div></td>
        <td width="54" ><div align="center" class="style6">TOTAL</div></td>
        </tr>
      ';
      $i = 1;
	foreach($items->result() as $v){      
			$this->db->where('id', $v->id_producto);
			$producto = $this->db->get("productos");	
			$producto = $producto->result();
			$producto = $producto[0];

     $html .= '<tr>
        <td height="20" class="cajaInputIzq">'.$v->cantidad.'</td>

        <td class="cajaInputIzq"><b>'.$producto->nombre.'</b></td>
        <td class="cajaInputIzq">$'.number_format($v->precio, 0, ',', '.').'</td>
        <td class="cajaInput">$'.number_format($v->descuento, 0, ',', '.').'</td>
        <td class="cajaInput"><b>$'.number_format($v->totalproducto, 0, ',', '.').'</b></td>
        </tr>';
        $i++;

    }

    if($i < 15){
    	for($j=$i;$j<=15;$j++){
		        $html .= '<tr>
		        <td class="cajaInputIzq">&nbsp;</td>
		        <td class="cajaInputIzq">&nbsp;</td>
		        <td class="cajaInputIzq">&nbsp;</td>
		        <td class="cajaInputIzq">&nbsp;</td>
		        <td class="cajaInputIzq">&nbsp;</td>
		        </tr>';
    	}
    }

      
      $html .='
		<tr>
      	<td >&nbsp;</td>
      	<td >&nbsp;</td>
      	<td colspan="2" class="cajaInputIzq">TOTAL</td>
      	<td class="cajaInputIzq"><b>$'.number_format($totalFactura, 0, ',', '.').'</b></td>
        </tr>                
</table>
</body>
</html>
		';
		//==============================================================
		//==============================================================
		//==============================================================
		$this->load->library("mpdf");
		//include(defined('BASEPATH')."/libraries/MPDF54/mpdf.php");
		//include(dirname(__FILE__)."/../libraries/MPDF54/mpdf.php");

		$this->mpdf->mPDF(
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

		$this->mpdf->WriteHTML($html);
		$this->mpdf->Output("CF_{$codigo}.pdf", "I");

		/*$mpdf= new mPDF(
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
		*/
		exit;
	}	
}









