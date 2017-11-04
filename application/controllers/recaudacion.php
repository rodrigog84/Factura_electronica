<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Recaudacion extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}

	public function save(){

		$resp = array();
		$numcomp = json_decode($this->input->post('num_comprobante'));
		$fechacomp = json_decode($this->input->post('fecha'));
		$numdocum = json_decode($this->input->post('num_documento'));
		$numfactura = json_decode($this->input->post('num_documento'));
        $idfactura = json_decode($this->input->post('idfactura'));
		$documento = json_decode($this->input->post('documento'));
		$tipodocumento = json_decode($this->input->post('documento'));
		$idcliente = json_decode($this->input->post('id_cliente'));
		$idcaja = json_decode($this->input->post('id_caja'));
		$idcajero = json_decode($this->input->post('id_cajero'));
		$items = json_decode($this->input->post('items'));
		$recitems = json_decode($this->input->post('items'));
		$idrecauda = json_decode($this->input->post('idrecauda'));
		$contado = json_decode($this->input->post('contado'));
		$cheques = json_decode($this->input->post('cheques'));
		$otros = json_decode($this->input->post('otros'));
		
		$estado = "SI";

		if($idrecauda){		

			$cajas = array(

		         'efectivo' => $contado,
		         'cheques' => $cheques,
		         'otros' => $otros
		    );

		    $this->db->where('id', $idrecauda);
		  
		    $this->db->update('control_caja', $cajas);

	    }else{

	    	$cajas2 = array(

	    	 'id_caja' => $idcaja,
	    	 'id_cajero' => $idcajero,
	         'efectivo' => $contado,
	         'cheques' => $cheques,
	         'otros' => $otros
	    	);

	    	$this->db->insert('control_caja', $cajas2);    	


	    };

		$data2 = array(
	         'estado' => $estado
	    );
	    $this->db->where('id', $idfactura);
	  
	    $this->db->update('factura_clientes', $data2);
		
		$recaudacion = array(
	        'num_comp' => $numcomp,
	        'fecha' => date('Y-m-d'),
	        'id_cliente' => $idcliente,
			'num_doc' => $numdocum,
			'id_caja' => $idcaja,
			'id_ticket' => $idfactura,
		    'id_cajero' => $idcajero
		);

		$this->db->insert('recaudacion', $recaudacion); 
		$recauda = $this->db->insert_id();

		foreach($items as $v){			
			$recaudacion_detalle = array(				
		        'id_recaudacion' => $recauda,
		        'id_forma' => $v->id_forma,
		        'detalle' => $v->detalle,
		        'num_cheque' => $v->num_cheque,
		        'id_banco' => $v->id_banco,
		        'valor_pago' => $v->valor_pago,
		        'valor_cancelado' => $v->valor_cancelado,
		        'valor_vuelto' => $v->valor_vuelto,
		        'fecha_transac' => $v->fecha_transac,
		        'fecha_comp' => $v->fecha_comp
			);
			$numdoc = ($v->num_cheque);
			$idforma = ($v->id_forma);

			$this->db->insert('recaudacion_detalle', $recaudacion_detalle);
		}

		if ($documento == 2){

			if ($idforma == 4){

			$docu = array(
		         'num_comp' => $numdoc
		    );

		    $docu2 = array(
		         'num_factura' => $numdoc
		    );

		    $docu3 = array(
		         'num_movimiento' => $numdoc
		    );


			$this->db->where('id', $recauda);
		  
		    $this->db->update('recaudacion', $docu);

		    $numdocu = ($numdocum - 1);

		    $corr = array(
	         'correlativo' => $numdocu 
		    );

		    $this->db->where('id', $documento);
		  
		    $this->db->update('correlativos', $corr);		
	        
	        
			};

		    if ($idforma == 7){

			$docu = array(
		         'num_comp' => $numdoc
		    );

 			$docu2 = array(
		         'num_factura' => $numdoc
		    );

		    $docu3 = array(
		         'num_movimiento' => $numdoc
		    );

			$this->db->where('id', $recauda);
		  
		    $this->db->update('recaudacion', $docu);

		    $numdocu = ($numdocum - 1);

		    $corr = array(
	         'correlativo' => $numdocu 
		    );

		    $this->db->where('id', $documento);
		  
		    $this->db->update('correlativos', $corr);

	        };


		};

		$factura = $this->db->query('SELECT * FROM factura_clientes WHERE id like "'.$idfactura.'"');

		if($factura->num_rows()>0){
	   			$row = $factura->first_row();
	   			$fechavenc = $row->fecha_venc;
	   }


		/******* CUENTAS CORRIENTES ****/
		$total_cancelacion = 0;
		$total_factura_cta_cte = 0;
		foreach($recitems as $ri){ // SUMAR MONTOS PARA VER TOTAL CANCELACION
			$total_factura_cta_cte += $ri->valor_pago;
			if($ri->id_forma != 3 && $ri->id_forma != 5 ){ // NO CONSIDERA PAGOS A CREDITO
				$total_cancelacion += $ri->valor_pago;
				$formapago = $ri->id_forma ;

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
			        'fechaactualiza' => date('Y-m-d H:i:s')
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
		        'fecha' => date('Y-m-d H:i:s')
			);

			$this->db->insert('detalle_cuenta_corriente', $detalle_cuenta_corriente); 	
			$idDetalleCtaCte = $this->db->insert_id();		

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
				        'fecvencimiento' => $fechavenc,
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
        $resp['idrecauda'] = $recauda;
        
		
        $this->Bitacora->logger("I", 'recaudacion', $numcomp);


        echo json_encode($resp);

	}

	public function update(){
		
		
	}

	public function buscar(){

		
	}

	public function exportRecaudacionPDF(){

		//$idfactura = $this->input->get('idfactura');
		
		$numero = $this->input->get('idrecaudacion');

        
			$query = $this->db->query('SELECT acc.*, c.nombres as nom_cliente, c.rut as rut_cliente, v.nombre as nom_vendedor, v.id as id_vendedor, p.num_factura as num_ticket, t.descripcion as nom_docu, p.totalfactura as total, n.nombre as nom_caja, e.nombre as nom_cajero FROM recaudacion acc
			left join factura_clientes p on (acc.id_ticket = p.id)
			left join clientes c on (acc.id_cliente = c.id)
			left join cajas n on (acc.id_caja = n.id)
			left join cajeros e on (acc.id_cajero = e.id)
			left join tipo_documento t on (p.tipo_documento = t.id)			
			left join vendedores v on (p.id_vendedor = v.id)
			WHERE acc.id = '.$numero.'

		');
		
		//cotizacion header
		$row = $query->result();
		$row = $row[0];
		//items

		$items = $this->db->query('SELECT acc.*, t.nombre as desc_pago FROM recaudacion_detalle acc
			left join cond_pago t on (acc.id_forma = t.id)
			WHERE acc.id_recaudacion = '.$row->id.'

		');

		$datas_detalle = $items->result_array();


		//$items = $this->db->get_where('recaudacion_detalle', array('id_recaudacion' => $row->id));
		//print_r($items->result());exit;
		//variables generales
		$codigo = $row->num_comp;
		$nombre_contacto = $row->nom_cliente;
		$nom_caja = $row->nom_caja;
		$nom_cajero = $row->nom_cajero;		
		$rut_cliente = $row->rut_cliente;
		$numdocu = $row->num_doc;
		$nomdocu = $row->nom_docu;
		$montoNeto = 0;
		$ivaTotal = 0;
		$totalFactura = 0;
		
				
		$dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
		$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
 

		
            $header = '
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
		          <p>COMPROBANTE N°: '.$codigo.'</p>
		          <!--p>&nbsp;</p-->
		          <p>FECHA EMISION : '.date('d/m/Y').'</p>
			</td>
		  </tr>';


		  $header2 = '<tr>
			<td style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:center;" colspan="3"><h2>COMPROBANTE DE PAGO</h2></td>
		  </tr>
		  <tr>
		    <td colspan="3" width="987px" >
		    	<table width="987px" border="0">
		    		<tr>
		    			<td width="197px">Razon Social:</td>
		    			<td width="395px">'.$row->nom_cliente.'</td>
		    			<td width="197px">Rut:</td>
		    			<td width="395px">'.$row->rut_cliente.'</td>
		    		</tr>
		    		<tr>
		    			<td width="197px">Fecha Comprobante:</td>
		    			<td width="395px">'.$row->fecha.'</td>
		    		</tr>
		    		<tr>
		    			<td width="197px">Caja:</td>
		    			<td width="395px">'.$nom_caja.'</td>
		    			<td width="197px">Cajero:</td>
		    			<td width="395px">'.$nom_cajero.'</td>
		    		</tr>		    		
		    				    				    		
		    	</table>
			</td>
		  </tr>';

$body_header = '<tr>
		    <td colspan="3" >
		    	<table width="987px" cellspacing="0" cellpadding="0" >
		      <tr>
		        <td width="126px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;" >Forma de Pago</td>
		        <td width="100px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;" >Documento</td>
		        <td width="250px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" >Tipo</td>
		        <td width="250px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:center;" >Numero</td>
		        <td width="80px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" >Detalle</td>
		        <td width="100px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" >Valor</td>
		        <td width="80px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" >Cancelado</td>
		        <td width="80px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" >Vuelto</td>
		      </tr>';
              $debe = 0;
              $haber = 0;
              $vuelto = 0;
              $i = 0;
              $body_detail = '';
      foreach($datas_detalle as $detalle){

			$body_detail .= '<tr>
			<td style="text-align:left;font-size: 14px;">'.$detalle['desc_pago'].'</td>		
			<td style="text-align:left;font-size: 14px;">'.$detalle['num_cheque'].'</td>
			<td style="text-align:right;font-size: 14px;">'.$nomdocu.'</td>
			<td style="text-align:center;font-size: 14px;">'.$numdocu.'</td>
			<td style="text-align:center;font-size: 14px;">'.$detalle['detalle'].'</td>
			<td style="text-align:right;font-size: 14px;">'.number_format($detalle['valor_pago'], 0, ',', '.').'</td>
			<td align="right" style="font-size: 14px;">$ '.number_format($detalle['valor_cancelado'], 0, ',', '.').'</td>
			<td align="right" style="font-size: 14px;">$ '.number_format($detalle['valor_vuelto'], 0, ',', '.').'</td>
			</tr>';
			
            $debe += ($detalle['valor_pago']);
            $haber += $detalle['valor_cancelado'];
            $vuelto += $detalle['valor_vuelto'];
            
            $i++;
         }       

         //$body_detail .= '</table><td></tr></table></body></html>';
		// RELLENA ESPACIO
		while($i < 30){
			$spaces .= '<tr><td colspan="7">&nbsp;</td></tr>';
			$i++;
		}     

		$footer .= '<tr><td colspan="7">&nbsp;</td></tr></table></td>
		  </tr>
		  <tr>
		  	<td colspan="3" >
		    	<table width="987px" cellspacing="0" cellpadding="0" >
		      <tr>
		        <td width="827px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;font-size: 14px;" ><b>Totales</b></td>
		        <td width="30x"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;font-size: 14px;" ><b>&nbsp;</b></td>
		        <td width="80px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;font-size: 14px;" ><b>&nbsp;</b></td>
		        <td width="80px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;font-size: 14px;" ><b>$ '.number_format($haber, 0, ',', '.').'</b></td>
		        <td width="80px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;font-size: 14px;" ><b>$  '.number_format($vuelto, 0, ',', '.').'</b></td>
		      </tr>
		      	</table>
		  	</td>
		  </tr></table>
		</body>
		</html>';


	   /* $html .=  "<tr>";
	      $html .=  '<td bgcolor="#002221" style="color: #FFF" scope="col" colspan="5"><b>TOTALES</b></td>';
	      $html .=  '<td bgcolor="#002221" style="color: #FFF;text-align: right;" scope="col" ><b>'.number_format($debe, 0, ',', '.').'</b></td>';
	      $html .=  '<td bgcolor="#002221" style="color: #FFF;text-align: right;" scope="col"><b>'.number_format($haber, 0, ',', '.').'</b></td>';
	      $html .=  '</tr>';
	    $html .= '</table></td>';
        $html .= "</tr></table>";
*/

        $html = $header.$header2.$body_header.$body_detail.$footer;
        //echo $html; exit;
        //$html = $header.$header2.$body_header.$body_detail.$spaces;
			$this->load->library("mpdf");
			//include(defined('BASEPATH')."/libraries/MPDF54/mpdf.php");
			//include(dirname(__FILE__)."/../libraries/MPDF54/mpdf.php");

			$this->mpdf->mPDF(
				'',    // mode - default ''
				'',    // format - A4, for example, default ''
				8,     // font size - default 0
				'',    // default font family
				10,    // margin_left
				5,    // margin right
				16,    // margin top
				16,    // margin bottom
				9,     // margin header
				9,     // margin footer
				'L'    // L - landscape, P - portrait
				);  
			//echo $html; exit;
			$this->mpdf->WriteHTML($html);
			$this->mpdf->Output("Comprobante".$codigo."pdf", "I");

			exit;     
	}



		

	public function getAll(){

		$resp = array();

        $start = $this->input->post('start');
        $limit = $this->input->post('limit');


        //filtro por nombre
        $nombre = $this->input->get('nombre');
        $estado = "";

		$countAll = $this->db->count_all_results("recaudacion");

		if($nombre){
			$query = $this->db->query('SELECT acc.*, c.nombres as nom_cliente, c.rut as rut_cliente, v.nombre as nom_vendedor, v.id as id_vendedor, p.num_factura as num_ticket, p.totalfactura as total, n.nombre as nom_caja, e.nombre as nom_cajero FROM recaudacion acc
			left join factura_clientes p on (acc.id_ticket = p.id)
			left join clientes c on (acc.id_cliente = c.id)
			left join cajas n on (acc.id_caja = n.id)
			left join cajeros e on (acc.id_cajero = e.id)
			WHERE nom_caja like "%'.$nombre.'%"
			limit '.$start.', '.$limit.'');
		}else{
			$query = $this->db->query('SELECT acc.*, c.nombres as nom_cliente, c.rut as rut_cliente, v.nombre as nom_vendedor, v.id as id_vendedor, p.num_factura as num_ticket, p.totalfactura as total, n.nombre as nom_caja, e.nombre as nom_cajero FROM recaudacion acc
			left join factura_clientes p on (acc.id_ticket = p.id)
			left join clientes c on (acc.id_cliente = c.id)
			left join cajas n on (acc.id_caja = n.id)
			left join cajeros e on (acc.id_cajero = e.id)
			left join vendedores v on (p.id_vendedor = v.id) order by acc.id desc
			
			limit '.$start.', '.$limit.' ' 

		);
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

	public function exportarPdflibroRecauda()
         {
            
          $columnas = json_decode($this->input->get('cols'));
          $idcaja = $this->input->get('idcaja');
          $idcajero = $this->input->get('idcajero');
          $nomcaja = $this->input->get('nomcaja');
          $nomcajero = $this->input->get('nomcajero');
          $fecha = $this->input->get('fecha2');
          list($dia, $mes, $anio) = explode("-",$fecha);
          $fecha2 = $anio ."-". $mes ."-". $dia;
          $tipo = $this->input->get('tipo');

          if ($tipo == "DETALLE"){

            $this->load->database();

            $query = $this->db->query('SELECT acc.*, t.nombre as desc_pago,
            r.id_caja as id_caja, r.id_cajero as id_cajero, n.nombre as nom_caja,
            e.nombre as nom_cajero, r.num_comp as num_comp, b.nombre as nom_banco FROM recaudacion_detalle acc
            left join cond_pago t on (acc.id_forma = t.id)
            left join recaudacion r on (acc.id_recaudacion = r.id)
            left join cajas n on (r.id_caja = n.id)
            left join cajeros e on (r.id_cajero = e.id)
            left join banco b on (acc.id_banco = b.id)
            WHERE acc.fecha_comp = "'.$fecha.'"
            order by desc_pago asc');

            
            $header = '
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
		          <p>FECHA EMISION : '.date('d/m/Y').'</p>
			</td>
		  </tr>';              
              
		  $header2 = '<tr>
			<td style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:center;" colspan="3"><h2>LIBRO DE RECAUDACION</h2></td>
		  </tr>
		  <tr>
			<td>CAJA : '.$nomcaja.'</td>
			<td>CAJERO : '.$nomcajero.'</td>
			<td>FECHA : '.$fecha2.'</td>
		  </tr>
		  <tr>
			
		  </tr>
			<tr><td colspan="3">&nbsp;</td></tr>		  
			';              


		


		      $cancelado = 0;
		     
		      $i = 0;
              $body_detail = '';
              $users = $query->result_array();
              $despago = " ";

		      foreach($users as $v){

		      	if($v['desc_pago'] != $despago){
                 	if($despago != ""){
                 	   if ($cancelado > 0){	
                 		$body_detail .= '<tr>
						<td colspan="3" >
							<table width="987px" cellspacing="0" cellpadding="0" border="0">
						<tr>
						<td width="67px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;font-size: 14px;" ><b>TOTALES</b></td>
						<td width="67px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;font-size: 14px;" ><b></b></td>
						<td width="180px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;font-size: 14px;" ><b>'.$despago.'</b></td>
						<td width="80px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;font-size: 14px;" ><b>$ '.number_format($cancelado, 0, ',', '.').'</b></td>
						</tr></table>
						</tr>';
						$cancelado = 0;
						}
	                }

	                $body_header = '<tr>
					    <td colspan="3" >
					    	<table width="987px" cellspacing="0" cellpadding="0" >
					      <tr>
					        <td width="67"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;" >Comp.</td>
					        <td width="180px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;" >Forma Pago</td>
					        <td width="80px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" >Documento</td>
					        <td width="60px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" >Banco</td>
					        <td width="150px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:center;" >Detalle</td>
					        <td width="80px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;" >Total</td>
					        <td width="80px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" >Cancelado</td>
					        <td width="80px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" >Vuelto</td>
					        <td width="110px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" >Fecha transac</td>
					       
					       </tr>';

	                 }


		      	    list($dia, $mes, $anio) = explode("-",$v['fecha_transac']);
          			$fecha3 = $anio ."-". $mes ."-". $dia;          			  			
          			
					$body_detail .= '<tr>
					<td style="text-align:center;font-size: 14px;">'.$v['num_comp'].'</td>	
					<td style="text-align:left;font-size: 14px;">'.$v['desc_pago'].'</td>
					<td style="text-align:right;font-size: 14px;">'.$v['num_cheque'].'</td>
					<td style="text-align:right;font-size: 14px;">'.$v['nom_banco'].'</td>
					<td style="text-align:right;font-size: 14px;">'.$v['detalle'].'</td>
					<td style="text-align:right;font-size: 14px;">'.number_format($v['valor_pago'], 0, '.', ',').'</td>
					<td align="right" style="font-size: 14px;">'.number_format($v['valor_cancelado'], 0, '.', ',').'</td>
					<td align="right" style="font-size: 14px;">'.number_format($v['valor_vuelto'], 0, '.', ',').'</td>
					<td align="right" style="font-size: 14px;">'.$fecha3.'</td>
					</tr>';
					$cancelado += $v['valor_cancelado'];	        

			        $i++;
			        $despago = $v['desc_pago'];             	
			       	     
		           
		         }

		         $body_detail .= '<tr>
						<td colspan="3" >
						<table width="394px" cellspacing="0" cellpadding="0" border="0">
						<tr>
						<td width="67"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;" >Totales.</td>
						<td width="67px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;font-size: 14px;" ><b>&nbsp;</b></td>
						<td width="180px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;font-size: 14px;" ><b>'.$v['desc_pago'].'</b></td>
						<td width="80px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;font-size: 14px;" ><b>$ '.number_format($cancelado, 0, ',', '.').'</b></td>


						</tr></table>
						</tr>';

				$footer .= '<tr><td colspan="10">&nbsp;</td></tr></table></td>
				  </tr><tr>				  	
				  </td>
				  </tr></table>
				</body>
				</html>';			              
             
        $html = $header.$header2.$body_header.$body_detail.$footer;
        //echo $html; exit;
        //$html = $header.$header2.$body_header.$body_detail.$spaces;
			$this->load->library("mpdf");
			//include(defined('BASEPATH')."/libraries/MPDF54/mpdf.php");
			//include(dirname(__FILE__)."/../libraries/MPDF54/mpdf.php");

			$this->mpdf->mPDF(
				'',    // mode - default ''
				'',    // format - A4, for example, default ''
				8,     // font size - default 0
				'',    // default font family
				10,    // margin_left
				5,    // margin right
				16,    // margin top
				16,    // margin bottom
				9,     // margin header
				9,     // margin footer
				'L'    // L - landscape, P - portrait
				);  
			//echo $html; exit;
			$this->mpdf->WriteHTML($html);
			$this->mpdf->Output("LibroRecauda.pdf", "I");

			exit;            
               

            
          }else{

          	 $this->load->database();

                $query = $this->db->query('SELECT acc.*, c.nombres as nom_cliente, c.rut as rut_cliente, v.nombre as nom_vendedor, v.id as id_vendedor, p.num_factura as num_ticket, p.totalfactura as total, n.nombre as nom_caja, e.nombre as nom_cajero FROM recaudacion acc
                left join factura_clientes p on (acc.id_ticket = p.id)
                left join clientes c on (acc.id_cliente = c.id)
                left join cajas n on (acc.id_caja = n.id)
                left join cajeros e on (acc.id_cajero = e.id)
                left join vendedores v on (p.id_vendedor = v.id)
                WHERE acc.id_caja = "'.$idcaja.'" AND acc.id_cajero = "'.$idcajero.'" AND acc.fecha = "'.$fecha.'"');

                $header = '
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
		          <p>FECHA EMISION : '.date('d/m/Y').'</p>
			</td>
		  </tr>';              
              
		  $header2 = '<tr>
			<td style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:center;" colspan="3"><h2>LIBRO DE RECAUDACION GENERAL</h2></td>
		  </tr>
		  <tr>
			<td>CAJA : '.$nomcaja.'</td>
			<td>CAJERO : '.$nomcajero.'</td>
			<td>FECHA : '.$fecha2.'</td>
		  </tr>
			<tr><td colspan="3">&nbsp;</td></tr>		  
			';              


		$body_header = '<tr>
		    <td colspan="3" >
		    	<table width="987px" cellspacing="0" cellpadding="0" >
		      <tr>
		        <td width="67"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;" >Comp.</td>
		        <td width="130px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;" >Ticket Venta</td>
		        <td width="90px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" >Rut</td>
		        <td width="20px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:center;" ></td>
		        <td width="450px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:center;" >Cliente</td>
		        <td width="100px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;" >Total</td>
		        <td width="130px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" >Fecha Transac</td>
		       </tr>';


		      $cancelado = 0;
		      $vuelto = 0;
		      $totalfactura = 0;
              $i = 0;
              $body_detail = '';
              $users = $query->result_array();

		      foreach($users as $v){

		      	     $rutautoriza = $v['rut_cliente'];
					   	if (strlen($rutautoriza) == 8){
					      $ruta1 = substr($rutautoriza, -1);
					      $ruta2 = substr($rutautoriza, -4, 3);
					      $ruta3 = substr($rutautoriza, -7, 3);
					      $ruta4 = substr($rutautoriza, -8, 1);
					      $v['rut_cliente'] = ($ruta4.".".$ruta3.".".$ruta2."-".$ruta1);
					    };
					    if (strlen($rutautoriza) == 9){
					      $ruta1 = substr($rutautoriza, -1);
					      $ruta2 = substr($rutautoriza, -4, 3);
					      $ruta3 = substr($rutautoriza, -7, 3);
					      $ruta4 = substr($rutautoriza, -9, 2);
					      $v['rut_cliente'] = ($ruta4.".".$ruta3.".".$ruta2."-".$ruta1);
					   
					    };
					    if (strlen($rutautoriza) == 2){
					      $ruta1 = substr($rutautoriza, -1);
					      $ruta2 = substr($rutautoriza, -4, 1);
					      $v['rut_cliente'] = ($ruta2."-".$ruta1);
					     
					    };

					     list($dia, $mes, $anio) = explode("-",$v['fecha']);
          				$fecha3 = $anio ."-". $mes ."-". $dia;	      	    


		      	    

					$body_detail .= '<tr>
					<td style="text-align:center;font-size: 14px;">'.$v['num_comp'].'</td>	
					<td style="text-align:center;font-size: 14px;">'.$v['num_ticket'].'</td>
					<td style="text-align:right;font-size: 14px;">'.$v['rut_cliente'].'</td>
					<td style="text-align:right;font-size: 14px;"></td>
					<td style="text-align:left;font-size: 14px;">'.$v['nom_cliente'].'</td>
					<td style="text-align:right;font-size: 14px;">'.number_format($v['total'], 0, ',', '.').'</td>					
					<td align="right" style="font-size: 14px;">'.$fecha3.'</td>
					
					</tr>';
					
			      $cancelado += $v['total'];
			     	     
		            $i++;
		         }  

				$footer .= '<tr><td colspan="10">&nbsp;</td></tr></table></td>
				   </tr>
				  <tr>
				  	<td colspan="3" >
				    	<table width="987px" cellspacing="0" cellpadding="0" border="0">
				      <tr>
				        <td width="67px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;font-size: 14px;" ><b>Totales</b></td>
				        <td width="130px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;font-size: 14px;" ><b></b></td>
				        <td width="90px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;font-size: 14px;" ><b></b></td>
				        <td width="20px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;font-size: 14px;" ><b></b></td>
				        <td width="450px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;font-size: 14px;" ><b></b></td>
				        <td width="100px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;font-size: 14px;" ><b>$ '.number_format($cancelado, 0, ',', '.').'</b></td>
				        <td width="130px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;font-size: 14px;" ><b></b></td>
				      </tr>
				      	</table>
				  	</td>
				  </tr></table>
				</body>
				</html>';		              
             
        $html = $header.$header2.$body_header.$body_detail.$footer;
        //echo $html; exit;
        //$html = $header.$header2.$body_header.$body_detail.$spaces;
			$this->load->library("mpdf");
			//include(defined('BASEPATH')."/libraries/MPDF54/mpdf.php");
			//include(dirname(__FILE__)."/../libraries/MPDF54/mpdf.php");

			$this->mpdf->mPDF(
				'',    // mode - default ''
				'',    // format - A4, for example, default ''
				8,     // font size - default 0
				'',    // default font family
				10,    // margin_left
				5,    // margin right
				16,    // margin top
				16,    // margin bottom
				9,     // margin header
				9,     // margin footer
				'L'    // L - landscape, P - portrait
				);  
			//echo $html; exit;
			$this->mpdf->WriteHTML($html);
			$this->mpdf->Output("LibroRecauda.pdf", "I");

			exit;            

          	
          }          

		
        }


}
