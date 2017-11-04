<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cotizaciones extends CI_Controller {



	public function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}

	public function getAll(){

		$resp = array();
		$start = $this->input->get('start');
        $limit = $this->input->get('limit');
        $opcion = $this->input->get('opcion');
        $nombres = $this->input->get('nombre');

        if(!$nombres){        	
        	$opcion= "Todos";
        }

		$countAll = $this->db->count_all_results("cotiza_cotizaciones");
		$data = array();

		if($opcion == "Rut"){

			$query = $this->db->query('SELECT ctz.*, cli.direccion as direccion, cli.id as id_cliente, cli.nombres as nombre, cli.id_giro as giro, g.nombre as nombre_giro, cli.rut as rut FROM cotiza_cotizaciones ctz
				left join clientes cli ON (ctz.id_cliente = cli.id)
				left join cod_activ_econ g on (cli.id_giro = g.id)
				WHERE rut = '.$nombres.'
			');

		}elseif($opcion == "Numero"){

			$query = $this->db->query('SELECT ctz.*, cli.direccion as direccion, cli.id as id_cliente, cli.nombres as nombre, cli.id_giro as giro, g.nombre as nombre_giro, cli.rut as rut FROM cotiza_cotizaciones ctz
				left join clientes cli ON (ctz.id_cliente = cli.id)
				left join cod_activ_econ g on (cli.id_giro = g.id)
				WHERE ctz.num_cotiza = '.$nombres.'');

		}else if($opcion == "Nombre"){

			$sql_nombre = "";
	        $arrayNombre =  explode(" ",$nombres);

	        foreach ($arrayNombre as $nombre) {
	        	$sql_nombre .= "cli.nombres like '%".$nombre."%' and ";
	        }


			$query = $this->db->query('SELECT ctz.*, cli.direccion as direccion, cli.id as id_cliente, cli.nombres as nombre, cli.id_giro as giro, g.nombre as nombre_giro, cli.rut as rut FROM cotiza_cotizaciones ctz
			left join clientes cli ON (ctz.id_cliente = cli.id)
			left join cod_activ_econ g on (cli.id_giro = g.id)
			WHERE ' . $sql_nombre . ' 1 = 1');

			$total = 0;

		  foreach ($query->result() as $row)
			{
				$total = $total +1;
			
			}

			$countAll = $total;
			

		}else if($opcion == "Todos"){

			$query = $this->db->query('SELECT ctz.*, cli.direccion as direccion, cli.id as id_cliente, cli.nombres as nombre, cli.id_giro as giro, g.nombre as nombre_giro, cli.rut as rut FROM cotiza_cotizaciones ctz
			left join clientes cli ON (ctz.id_cliente = cli.id)
			left join cod_activ_econ g on (cli.id_giro = g.id) order by ctz.id desc		
			limit '.$start.', '.$limit.'');

		}else if($opcion == "Contacto"){

			$sql_nombre = "";
	        $arrayNombre =  explode(" ",$nombres);

	        foreach ($arrayNombre as $nombre) {
	        	$sql_nombre .= "ctz.nombre_contacto like '%".$nombre."%' and ";
	        }


			$query = $this->db->query('SELECT ctz.*, cli.direccion as direccion, cli.id as id_cliente, cli.nombres as nombre, cli.id_giro as giro, g.nombre as nombre_giro, cli.rut as rut FROM cotiza_cotizaciones ctz
			left join clientes cli ON (ctz.id_cliente = cli.id)
			left join cod_activ_econ g on (cli.id_giro = g.id)
			WHERE ' . $sql_nombre . ' 1 = 1');

			$total = 0;

		  foreach ($query->result() as $row)
			{
				$total = $total +1;
			
			}

			$countAll = $total;
		};


		foreach ($query->result() as $row)
		{
			$this->db->where('id_cotizacion', $row->id);
			$cproductos = $this->db->count_all_results("cotiza_cotizaciones_items");
			$row->cproductos = $cproductos;
			$data[] = $row;
		}
        $resp['success'] = true;
        $resp['total'] = $countAll;
        $resp['data'] = $data;

        echo json_encode($resp);
	}

	public function edita(){

		$resp = array();
		$idcotiza = $this->input->get('idcotiza');

		if ($idcotiza){
		$countAll = $this->db->count_all_results("cotiza_cotizaciones");
		$data = array();
		$query = $this->db->query('SELECT ctz.*, cli.direccion as direccion, cli.id as id_cliente, cli.nombres as nombres, cli.id_pago as id_pago,cli.id_giro as giro, g.nombre as nombre_giro, cli.rut as rut FROM cotiza_cotizaciones ctz
			left join clientes cli ON (ctz.id_cliente = cli.id)
			left join cod_activ_econ g on (cli.id_giro = g.id)
			WHERE ctz.id = '.$idcotiza.'
		');

		$row1 = $query->result();
		$row = $row1[0];		

	   	$row = $query->first_row();
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
			
	   	$resp['cliente'] = $row;
	    $resp['success'] = true;
        echo json_encode($resp);
        }
	}

	public function edita2(){

		$resp = array();
		$idcotiza = $this->input->post('idcotiza');

		if ($idcotiza){

		$items = $this->db->get_where('cotiza_cotizaciones_items', array('id_cotizacion' => $idcotiza));

	   	$data = array();

	   	foreach($items->result() as $item){
			$this->db->where('id', $item->id_producto);
			$producto = $this->db->get("productos");	
			$producto = $producto->result();
			$producto = $producto[0];
			$item->nombre = $producto->nombre;
			$item->precio_base = $item->subtotal;
			$item->dcto = $item->descuento;	
			$data[] = $item;
		}	     	
	    $resp['success'] = true;
        $resp['data'] = $data; 
        echo json_encode($resp);
        }
	}

	public function save(){
		$resp = array();

		$idcliente = $this->input->post('idcliente');
		$numcotiza = $this->input->post('numcotiza');
		$vendedor = $this->input->post('vendedor');		
		$datacliente = json_decode($this->input->post('datacliente'));
		$items = json_decode($this->input->post('items'));
		$neto = $this->input->post('neto');
		$afecto = $this->input->post('afecto');
		$descuento = $this->input->post('descuento');
		$fecha = $this->input->post('fecha');
		$ftotal = $this->input->post('total');
		$fiva = $this->input->post('iva');
		$idcontacto = $this->input->post('idcontacto');
		$observa = $this->input->post('observa');

		$cotiza_cotizaciones = array(
	        'id_cliente' => $idcliente,
	        'num_cotiza' => $numcotiza,
	        'id_vendedor' => $vendedor,
	        'id_contacto' => $idcontacto,
	        'nombre_contacto' => $datacliente->contacto,
			'telefono_contacto' => $datacliente->telefono,
			'email_contacto' => $datacliente->mail,
		    'neto' => $neto,
		    'descuento' => $descuento,
		    'afecto' => $afecto,
		    'iva' => $fiva,
	        'total' => $ftotal,	       
	        'observaciones' => $observa,
	        'fecha' =>  date('Y-m-d'),
		);

		$contactos = array(
	        'id_cliente' => $idcliente,
	        'nombre' => $datacliente->contacto,
			'fono' => $datacliente->telefono,
			'email' => $datacliente->mail,
		);		
		
		if ($idcontacto){

			$this->db->where('id', $idcontacto);
			$this->db->update('contactos', $contactos);			

		}else{

			$this->db->insert('contactos', $contactos);
			$idcontacto = $this->db->insert_id();	

		};

		$this->db->insert('cotiza_cotizaciones', $cotiza_cotizaciones); 
		$idcotizacion = $this->db->insert_id();

		foreach($items as $v){
			$cotiza_cotizaciones_items = array(
		        'id_producto' => $v->id,
		        'id_cotizacion' => $idcotizacion,
		        'subtotal' => $v->precio_base,
		        'id_descuento' => $v->id_descuento,
		        'cantidad' => $v->cantidad,
		        'total' => $v->total,
		        'neto' => $v->neto,
		        'iva' => $v->iva,
		        'descuento' => $v->dcto
			);

			$this->db->insert('cotiza_cotizaciones_items', $cotiza_cotizaciones_items); 

		}

		
        $resp['success'] = true;
        $resp['id'] = $idcotizacion;

        echo json_encode($resp);
	}

	public function save2(){

		$resp = array();
        $id = $this->input->post('id');
		$idcliente = $this->input->post('idcliente');
		$vendedor = $this->input->post('vendedor');	
		$numcotiza = $this->input->post('numcotiza');
		$datacliente = json_decode($this->input->post('datacliente'));
		$items = json_decode($this->input->post('items'));
		$neto = $this->input->post('neto');
		$afecto = $this->input->post('afecto');
		$descuento = $this->input->post('descuento');
		$fecha = $this->input->post('fecha');
		$ftotal = $this->input->post('total');
		$fiva = $this->input->post('iva');
		$idcontacto = $this->input->post('idcontacto');
		$observa = $this->input->post('observa');	


		$query = $this->db->query('DELETE  FROM cotiza_cotizaciones_items WHERE id_cotizacion = '.$id.'');		

		$contactos = array(
	        'id_cliente' => $idcliente,
	        'nombre' => $datacliente->contacto,
			'fono' => $datacliente->telefono,
			'email' => $datacliente->mail,
		);

		if ($idcontacto){

			$this->db->where('id', $idcontacto);
			$this->db->update('contactos', $contactos);			

		}else{

			$this->db->insert('contactos', $contactos);
			$idcontacto = $this->db->insert_id();	

		};

		$cotiza_cotizaciones = array(
	        'id_cliente' => $idcliente,
	        'num_cotiza' => $numcotiza,
	        'id_vendedor' => $vendedor,
	        'id_contacto' => $idcontacto,
	        'nombre_contacto' => $datacliente->contacto,
			'telefono_contacto' => $datacliente->telefono,
			'email_contacto' => $datacliente->mail,
		    'neto' => $neto,
		    'descuento' => $descuento,
		    'afecto' => $afecto,
		    'iva' => $fiva,
	        'total' => $ftotal,	       
	        'observaciones' => $observa,
	        'fecha' =>  date('Y-m-d'),
		);

		$this->db->where('id', $id);
		$this->db->update('cotiza_cotizaciones', $cotiza_cotizaciones); 
		$idcotiza = $this->db->insert_id();
		$idcotiza = $id;

		foreach($items as $v){
			$cotiza_cotizaciones_items = array(
		       'id_producto' => $v->id,
		        'id_cotizacion' => $idcotiza,
		        'subtotal' => $v->precio_base,
		        'id_descuento' => $v->id_descuento,
		        'cantidad' => $v->cantidad,
		        'total' => $v->total,
		        'neto' => $v->neto,
		        'iva' => $v->iva,
		        'descuento' => $v->dcto
			);

			$this->db->insert('cotiza_cotizaciones_items', $cotiza_cotizaciones_items); 

		}

		
        $resp['success'] = true;

        echo json_encode($resp);
	}

	public function exportPDF(){
		$idcotizacion = $this->input->get('idcotizacion');

		$query = $this->db->query('SELECT 
			ctz.id, ctz.iva, ctz.neto, ctz.afecto, ctz.descuento, ctz.total, ctz.num_cotiza, ctz.telefono_contacto, ctz.email_contacto, ctz.nombre_contacto, cli.nombres as empresa , cli.rut as rut_empresa, cli.direccion as direccion_empresa, cli.fono as fono_empresa, ctz.observaciones, cae.nombre as giro_empresa, c.nombre as ciudad_empresa, v.nombre as nom_vendedor, pa.nombre as conpago FROM cotiza_cotizaciones ctz
			INNER JOIN clientes cli on (ctz.id_cliente = cli.id)
			LEFT JOIN cod_activ_econ cae on cli.id_giro = cae.id
			LEFT JOIN cond_pago pa on cli.id_pago = pa.id
			LEFT JOIN ciudad c on cli.id_ciudad = c.id
			LEFT JOIN vendedores v on ctz.id_vendedor = v.id
			WHERE ctz.id = '.$idcotizacion.'
		');

		//cotizacion header
		$row = $query->result();
		$row = $row[0];
		//items
		$items = $this->db->get_where('cotiza_cotizaciones_items', array('id_cotizacion' => $row->id));
		//variables generales
		$codigo = $row->num_cotiza;
		$nombre_contacto = $row->nombre_contacto;
		$observacion = $row->observaciones;
		$vendedor = $row->nom_vendedor;
		$condpago = $row->conpago;
				

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
		          <p>FECHA EMISION : '.date('d/m/Y').'</p>
		          <!--p>&nbsp;</p-->
		          <p>VALIDEZ DE COTIZACION : 15 DIAS</p>
		          <!--p>&nbsp;</p-->
		          <p>ESTADO : Pendiente</p>
			</td>
		  </tr>
		  <tr>
			<td style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:center;" colspan="3"><h1>COTIZACION</h1></td>
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
		    			<td width="395px">'. $row->direccion_empresa .'</td>
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
			$totaliva = 

			$html .= '<tr>
			<td style="text-align:right">'.number_format($v->cantidad,0,'.',',').'&nbsp;&nbsp;</td>			
			<td style="text-align:left">'.$producto->nombre.'</td>			
			<td align="right">$ '.number_format($v->subtotal, 0, '.', ',').'</td>
			<td align="right">$ '.number_format($v->subtotal - ($v->descuento/$v->cantidad), 0, '.', ',').'</td>

			<td align="right">$ '.number_format($v->total, 0, '.', ',').'</td>
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
		  	<td colspan="3" style="text-align:left;">Cotiza: '.$vendedor.'</td>
		  </tr>
		  <tr>
		  	<td colspan="2" rowspan="6" style="font-size: 12px;border-bottom:1pt solid black;border-top:1pt solid black;border-left:1pt solid black;border-right:1pt solid black;text-align:left;">'.$observacion.'</td>
		  	<td>
				<table width="296px" border="0">
					<tr>
						<td width="150px" style="font-size: 20px;text-align:left;">Afecto</td>
						<td width="146px" style="text-align:right;">$ '. number_format($row->afecto, 0, '.', ',') .'</td>
					</tr>
				</table>
		  	</td>
		  </tr>	
		  <tr>
		  	<td>
				<table width="296px" border="0">
					<tr>
						<td width="150px" style="font-size: 20px;text-align:left;">IVA</td>
						<td width="146px" style="text-align:right;">$ '. number_format($row->iva, 0, '.', ',') .'</td>
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

	public function enviarMail(){

		$idcotizacion = $this->input->post('idcotiza');
		$mensaje = $this->input->post('mensaje') != '' ? $this->input->post('mensaje') : "Envio de Cotizacion Pdf";
		$email = $this->input->post('email');

		$query = $this->db->query('SELECT 
			ctz.id, ctz.iva, ctz.neto, ctz.afecto, ctz.descuento, ctz.total, ctz.num_cotiza, ctz.telefono_contacto, ctz.email_contacto, ctz.nombre_contacto, cli.nombres as empresa , cli.rut as rut_empresa, cli.direccion as direccion_empresa, cli.fono as fono_empresa, ctz.observaciones, cae.nombre as giro_empresa, c.nombre as ciudad_empresa, v.nombre as nom_vendedor, pa.nombre as conpago FROM cotiza_cotizaciones ctz
			INNER JOIN clientes cli on (ctz.id_cliente = cli.id)
			LEFT JOIN cod_activ_econ cae on cli.id_giro = cae.id
			LEFT JOIN cond_pago pa on cli.id_pago = pa.id
			LEFT JOIN ciudad c on cli.id_ciudad = c.id
			LEFT JOIN vendedores v on ctz.id_vendedor = v.id
			WHERE ctz.id = '.$idcotizacion);
		
		//cotizacion header
		$row = $query->result();
		$row = $row[0];
		//items
		$items = $this->db->get_where('cotiza_cotizaciones_items', array('id_cotizacion' => $row->id));
		//variables generales
		$codigo = $row->num_cotiza;
		$nombre_contacto = $row->nombre_contacto;
		$observacion = $row->observaciones;
		$vendedor = $row->nom_vendedor;
		$condpago = $row->conpago;
				

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
		          <p>FECHA EMISION : '.date('d/m/Y').'</p>
		          <!--p>&nbsp;</p-->
		          <p>VALIDEZ DE COTIZACION : 15 DIAS</p>
		          <!--p>&nbsp;</p-->
		          <p>ESTADO : Pendiente</p>
			</td>
		  </tr>
		  <tr>
			<td style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:center;" colspan="3"><h1>COTIZACION</h1></td>
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
		    			<td width="395px">'. $row->direccion_empresa .'</td>
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
			$totaliva = 

			$html .= '<tr>
			<td style="text-align:right">'.number_format($v->cantidad,0,'.',',').'&nbsp;&nbsp;</td>			
			<td style="text-align:left">'.$producto->nombre.'</td>			
			<td align="right">$ '.number_format($v->subtotal, 0, '.', ',').'</td>
			<td align="right">$ '.number_format($v->subtotal - ($v->descuento/$v->cantidad), 0, '.', ',').'</td>

			<td align="right">$ '.number_format($v->total, 0, '.', ',').'</td>
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
		  	<td colspan="3" style="text-align:left;">Cotiza: '.$vendedor.'</td>
		  </tr>
		  <tr>
		  	<td colspan="2" rowspan="6" style="font-size: 12px;border-bottom:1pt solid black;border-top:1pt solid black;border-left:1pt solid black;border-right:1pt solid black;text-align:left;">'.$observacion.'</td>
		  	<td>
				<table width="296px" border="0">
					<tr>
						<td width="150px" style="font-size: 20px;text-align:left;">Afecto</td>
						<td width="146px" style="text-align:right;">$ '. number_format($row->afecto, 0, '.', ',') .'</td>
					</tr>
				</table>
		  	</td>
		  </tr>	
		  <tr>
		  	<td>
				<table width="296px" border="0">
					<tr>
						<td width="150px" style="font-size: 20px;text-align:left;">IVA</td>
						<td width="146px" style="text-align:right;">$ '. number_format($row->iva, 0, '.', ',') .'</td>
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
        //$html = $header.$header2.$body_data; 
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
			$file = date("YmdHis").".pdf";
			$this->mpdf->Output('./tmp/'.$file, 'F');

			$this->load->model('facturaelectronica');
			$email_data = $this->facturaelectronica->get_email();

			if(count($email_data) > 0){
				$this->load->library('email');
				$config['protocol']    = $email_data->tserver_intercambio;
				$config['smtp_host']    = $email_data->host_intercambio;
				$config['smtp_port']    = $email_data->port_intercambio;
				$config['smtp_timeout'] = '7';
				$config['smtp_user']    = $email_data->email_intercambio;
				$config['smtp_pass']    = $email_data->pass_intercambio;
				$config['charset']    = 'utf-8';
				$config['newline']    = "\r\n";
				$config['mailtype'] = 'html'; // or html
				$config['validation'] = TRUE; // bool whether to validate email or not      			

				$this->email->initialize($config);		  		

			    $this->email->from($email_data->email_intercambio, NOMBRE_EMPRESA);
			    $this->email->to($email);

			    //$this->email->bcc(array('rodrigo.gonzalez@info-sys.cl','cesar.moraga@info-sys.cl','sergio.arriagada@info-sys.cl','rene.gonzalez@info-sys.cl')); 
			    $this->email->subject('Envio de Cotizacion');
			    $this->email->message($mensaje);

			    $this->email->attach('./tmp/'.$file,'attachment', 'Cotizacion.pdf');			


			    try {
			      $this->email->send();
			      //var_dump($this->email->print_debugger()); exit;
			      unlink('./tmp/'.$file);
			      	        exit;
			    } catch (Exception $e) {
			      echo $e->getMessage() . '<br />';
			      echo $e->getCode() . '<br />';
			      echo $e->getFile() . '<br />';
			      echo $e->getTraceAsString() . '<br />';
			      echo "no";

			    }
		    }

		exit;
	}
}











