<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Notacredito extends CI_Controller {



	public function __construct()
	{
		parent::__construct();
		$this->load->helper('format');
		$this->load->database();
	}

	public function save2(){
		
		$resp = array();

		$numfactura_asoc = $this->input->post('numfactura_asoc'); //ID OBTENIDO PARA REBAJAR EN CUENTA CORRIENTE

		$idcliente = $this->input->post('idcliente');
		$numfactura = $this->input->post('docurelacionado');
		$numdocuemnto = $this->input->post('numdocumento');
		$idfactura = $this->input->post('idfactura');
		$fechafactura = $this->input->post('fechafactura');
		$fechavenc = $this->input->post('fechavenc');
		$vendedor = $this->input->post('idvendedor');
		$datacliente = json_decode($this->input->post('datacliente'));
		$items = json_decode($this->input->post('items'));
		$neto = $this->input->post('netofactura');
		$fiva = $this->input->post('ivafactura');
        $relacionado = $this->input->post('docurelacionado');
		$fafecto = $this->input->post('afectofactura');
		$ftotal = $this->input->post('totalfacturas');
		$tipodocumento = $this->input->post('tipodocumento');
		//$tipodocumento = 11;

			
		$factura_cliente = array(
			'tipo_documento' => $tipodocumento,
	        'id_cliente' => $idcliente,
	        'num_factura' => $numdocuemnto,
	        'id_vendedor' => $vendedor,
	        'sub_total' => $neto,
	        'neto' => $neto,
	        'iva' => $fiva,
	        'totalfactura' => $ftotal,
	        'fecha_factura' => $fechafactura,
	        'id_factura' => $numfactura,
	        'fecha_venc' => $fechavenc,
	        'forma' => 1,	          
	        'id_sucursal' => 0,
	        'id_factura' => 0,
	        'observacion' => '',
	        'id_observa' => 0,
	        'id_despacho' => 0,
	        'id_cond_venta' => 0,
	        'descuento' => 0,
	        'estado' => '',
	          
		);

		$this->db->insert('factura_clientes', $factura_cliente); 
		$idfactura = $this->db->insert_id();

		foreach($items as $v){
			$factura_clientes_item = array(
		        'id_factura' => $idfactura,
		        'id_producto' => 0,
		        'id_guia' => 0,
		        'num_guia' => 0,
		        'cantidad' => 0,
		        'kilos' => 0,
		        'precio' => 0,		        
		        'glosa' => $v->glosa,
		        'neto' => $v->neto,
		        'iva' => $v->iva,
		        'total' => $v->total
			);

		$this->db->insert('detalle_factura_glosa', $factura_clientes_item);
    	
		}

		$data3 = array(
	         'id_factura' => $relacionado,
		    );

		   
		    $this->db->where('id', $idfactura);
		  
		    $this->db->update('factura_clientes', $data3);
		


        /*****************************************/

     if($tipodocumento == 102){  // SI ES NOTA DE CREDITO ELECTRONICA
            header('Content-type: text/plain; charset=ISO-8859-1');
            $this->load->model('facturaelectronica');
            $config = $this->facturaelectronica->genera_config();
            include $this->facturaelectronica->ruta_libredte();

            $tipo_nota_credito = 2;
            $glosa = 'Correccion factura '. $numfactura_asoc;

            $empresa = $this->facturaelectronica->get_empresa();
            $datos_empresa_factura = $this->facturaelectronica->get_empresa_factura($idfactura);

            //$detalle_factura = $this->facturaelectronica->get_detalle_factura($idfactura);
            $detalle_factura = $this->facturaelectronica->get_detalle_factura_glosa($idfactura);

            $lista_detalle = array();
            $i = 0;
            foreach ($detalle_factura as $detalle) {

				$lista_detalle[$i]['NmbItem'] = $detalle->glosa;
				$lista_detalle[$i]['QtyItem'] = 1;
                $lista_detalle[$i]['PrcItem'] = floor($detalle->neto);
            
                $i++;
            }



            // datos
            $nota_credito = [
                'Encabezado' => [
                    'IdDoc' => [
                        'TipoDTE' => 61,
                        'Folio' => $numdocuemnto,
                    ],
                    'Emisor' => [
                        'RUTEmisor' => $empresa->rut.'-'.$empresa->dv,
                        'RznSoc' => substr($empresa->razon_social,0,100), //LARGO DE RAZON SOCIAL NO PUEDE SER SUPERIOR A 100 CARACTERES
                        'GiroEmis' => substr($empresa->giro,0,80), //LARGO DE GIRO DEL EMISOR NO PUEDE SER SUPERIOR A 80 CARACTERES
                        'Acteco' => $empresa->cod_actividad,
                        'DirOrigen' => substr($empresa->dir_origen,0,70), //LARGO DE DIRECCION DE ORIGEN NO PUEDE SER SUPERIOR A 70 CARACTERES
                        'CmnaOrigen' => substr($empresa->comuna_origen,0,20), //LARGO DE COMUNA DE ORIGEN NO PUEDE SER SUPERIOR A 20 CARACTERES
                    ],
                    'Receptor' => [
                        'RUTRecep' => substr($datos_empresa_factura->rut_cliente,0,strlen($datos_empresa_factura->rut_cliente) - 1)."-".substr($datos_empresa_factura->rut_cliente,-1),
                        'RznSocRecep' => substr($datos_empresa_factura->nombre_cliente,0,100), //LARGO DE RAZON SOCIAL NO PUEDE SER SUPERIOR A 100 CARACTERES
                        'GiroRecep' => substr($datos_empresa_factura->giro,0,40), //LARGO DEL GIRO NO PUEDE SER SUPERIOR A 40 CARACTERES
                        'DirRecep' => substr($datos_empresa_factura->direccion,0,70), //LARGO DE DIRECCION NO PUEDE SER SUPERIOR A 70 CARACTERES
                        'CmnaRecep' => substr($datos_empresa_factura->nombre_comuna,0,20), //LARGO DE COMUNA NO PUEDE SER SUPERIOR A 20 CARACTERES
                    ],
                    'Totales' => [
                        // estos valores serán calculados automáticamente
                        'MntNeto' => 0,
                        'TasaIVA' => \sasco\LibreDTE\Sii::getIVA(),
                        'IVA' => 0,
                        'MntTotal' => 0,
                    ],                  
                ],
                'Detalle' => $lista_detalle,
                'Referencia' => [
                    'TpoDocRef' => 33,
                    'FolioRef' => $numfactura,
                    'CodRef' => $tipo_nota_credito,
                    'RazonRef' => $glosa,
                ]               
            ];          


            //FchResol y NroResol deben cambiar con los datos reales de producción
            $caratula = [
                //'RutEnvia' => '11222333-4', // se obtiene de la firma
                'RutReceptor' => '60803000-K',
                'FchResol' => $empresa->fec_resolucion,
                'NroResol' => $empresa->nro_resolucion
            ];

            $Firma = new sasco\LibreDTE\FirmaElectronica($config['firma']); //lectura de certificado digital        
            $caf = $this->facturaelectronica->get_content_caf_folio($numdocuemnto,61);
            $Folios = new sasco\LibreDTE\Sii\Folios($caf->caf_content);

            $DTE = new \sasco\LibreDTE\Sii\Dte($nota_credito);

            $DTE->timbrar($Folios);
            $DTE->firmar($Firma);       

            // generar sobre con el envío del DTE y enviar al SII
            $EnvioDTE = new \sasco\LibreDTE\Sii\EnvioDte();

            $EnvioDTE->agregar($DTE);
            $EnvioDTE->setFirma($Firma);
            $EnvioDTE->setCaratula($caratula);
            $EnvioDTE->generar();
            if ($EnvioDTE->schemaValidate()) { // REVISAR PORQUÉ SE CAE CON ESTA VALIDACION
                
                $track_id = 0;
                $xml_dte = $EnvioDTE->generar();

                $tipo_envio = $this->facturaelectronica->busca_parametro_fe('envio_sii'); //ver si está configurado para envío manual o automático

                if($tipo_envio == 'automatico'){
                    $track_id = $EnvioDTE->enviar();
                }               

                //$track_id = 0;

                $nombre_dte = $numdocuemnto."_61_".$idfactura."_".date("His").".xml"; // nombre archivo
                $path = date('Ym').'/'; // ruta guardado
                if(!file_exists('./facturacion_electronica/dte/'.$path)){
                    mkdir('./facturacion_electronica/dte/'.$path,0777,true);
                }               
                $f_archivo = fopen('./facturacion_electronica/dte/'.$path.$nombre_dte,'w');
                fwrite($f_archivo,$xml_dte);
                fclose($f_archivo);

/*
                $this->db->where('f.folio', $numdocuemnto);
                $this->db->where('c.tipo_caf', 61);
                $this->db->update('folios_caf f inner join caf c on f.idcaf = c.id',array('dte' => $xml_dte,
                                                                                          'estado' => 'O',
                                                                                          'idfactura' => $idfactura,
                                                                                          'path_dte' => $path,
                                                                                          'archivo_dte' => $nombre_dte,
                                                                                          'trackid' => $track_id
                                                                                          )); 
*/

				$this->db->query("update f
								 set dte = '" . iconv('','UTF-8//IGNORE',$xml_dte) . "',
								 	 estado = 'O',
								 	 idfactura = '" . $idfactura . "',
								 	 path_dte = '" . $path . "',
								 	 archivo_dte = '" . $nombre_dte . "',
								 	 trackid = '" . $track_id . "'
								 from folios_caf f
								 inner join caf c on f.idcaf = c.id
								 where f.folio = '" .$numdocuemnto . "'
								 and c.tipo_caf = '61'"
								 ); 

				if($track_id != 0 && $datos_empresa_factura->e_mail != ''){ //existe track id, se envía correo
						$this->facturaelectronica->envio_mail_dte($idfactura);
				}

            }                   
            
        }

        $resp['success'] = true;
		$resp['idfactura'] = $idfactura;

		$this->Bitacora->logger("I", 'factura_clientes', $idfactura);	
        

        echo json_encode($resp);
	}

	public function save(){
		
		$resp = array();
		$numfactura_asoc = $this->input->post('numfactura_asoc'); //ID OBTENIDO PARA REBAJAR EN CUENTA CORRIENTE


		$idcliente = $this->input->post('idcliente');
		$numfactura = $this->input->post('numfactura_asoc');
		$numdocuemnto = $this->input->post('numdocumento');
		$idfactura = $this->input->post('idfactura');
		$fechafactura = $this->input->post('fechafactura');
		$fechavenc = $this->input->post('fechavenc');
		$vendedor = $this->input->post('idvendedor');
		$datacliente = json_decode($this->input->post('datacliente'));
		$items = json_decode($this->input->post('items'));
		$neto = $this->input->post('netofactura');
		$fiva = $this->input->post('ivafactura');
		$fafecto = $this->input->post('afectofactura');
		$ftotal = $this->input->post('totalfacturas');
		$tipodocumento = $this->input->post('tipodocumento');

	
		$factura_cliente = array(
			'tipo_documento' => $tipodocumento,
	        'id_cliente' => $idcliente,
	        'num_factura' => $numdocuemnto,
	        'id_vendedor' => $vendedor,
	        'sub_total' => $neto,
	        'descuento' => ($neto - $fafecto),
	        'neto' => $neto,
	        'iva' => $fiva,
	        'totalfactura' => $ftotal,
	        'fecha_factura' => $fechafactura,
	        'id_factura' => $numfactura,
	        'fecha_venc' => $fechavenc,
	        'id_sucursal' => 0,
	        'id_factura' => 0,
	        'observacion' => '',
	        'id_observa' => 0,
	        'id_despacho' => 0,
	        'id_cond_venta' => 0,
	        'descuento' => 0,
	        'estado' => '',
	        'forma' => 0	        	          
		);

		$this->db->insert('factura_clientes', $factura_cliente); 
		$idfactura = $this->db->insert_id();

		foreach($items as $v){

			$factura_clientes_item = array(
		        'id_producto' => $v->idproducto,
		        'id_factura' => $idfactura,
		        'num_factura' => $numdocuemnto,
		        'precio' => $v->precio,
		        'cantidad' => $v->cantidad,
		        'neto' => $v->total,
		        'descuento' => $v->dcto,
		        'iva' => $v->iva,
		        'totalproducto' => $v->totaliva,
		        'fecha' => $fechafactura,
		        'id_despacho' => 0
			);

		$producto = $v->id;

		$this->db->insert('detalle_factura_cliente', $factura_clientes_item);
		
		}

		/*****************************************/


		if($tipodocumento == 102){  // SI ES NOTA DE CREDITO ELECTRONICA
			header('Content-type: text/plain; charset=ISO-8859-1');
			$this->load->model('facturaelectronica');
			$config = $this->facturaelectronica->genera_config();
			include $this->facturaelectronica->ruta_libredte();

			$tipo_nota_credito = $this->input->post('tipo_nota_credito');
			$glosa = $tipo_nota_credito == 1 ? 'Anula factura '. $numfactura_asoc : 'Correccion factura '. $numfactura_asoc;

			$empresa = $this->facturaelectronica->get_empresa();
			$datos_empresa_factura = $this->facturaelectronica->get_empresa_factura($idfactura);

			$detalle_factura = $this->facturaelectronica->get_detalle_factura($idfactura);

			$lista_detalle = array();
			$i = 0;
			foreach ($detalle_factura as $detalle) {
				$lista_detalle[$i]['NmbItem'] = $detalle->nombre;
				$lista_detalle[$i]['QtyItem'] = $detalle->cantidad;
				//$lista_detalle[$i]['PrcItem'] = floor($detalle->precio/1.19);
				$lista_detalle[$i]['PrcItem'] = round($detalle->precio/1.19,0);

				if($detalle->descuento != 0){
					//$porc_descto = round(($detalle->descuento/($detalle->cantidad*$lista_detalle[$i]['PrcItem'])*100),0);
					//$lista_detalle[$i]['DescuentoPct'] = $porc_descto;		
					//$lista_detalle[$i]['PrcItem'] =- $lista_detalle[$i]['PrcItem']*$porc_descto;
					$total_sin_iva = round($detalle->totalproducto/1.19,0);
					$descuento = abs(($lista_detalle[$i]['PrcItem']*$detalle->cantidad) - $total_sin_iva);
					$lista_detalle[$i]['DescuentoMonto'] = $descuento;
				}				
				//$lista_detalle[$i]['DescuentoMonto'] = $detalle->descuento;
				$i++;
			}



			// datos
			$nota_credito = [
			    'Encabezado' => [
			        'IdDoc' => [
			            'TipoDTE' => 61,
			            'Folio' => $numdocuemnto,
			        ],
			        'Emisor' => [
			            'RUTEmisor' => $empresa->rut.'-'.$empresa->dv,
			            'RznSoc' => substr($empresa->razon_social,0,100), //LARGO DE RAZON SOCIAL NO PUEDE SER SUPERIOR A 100 CARACTERES
			            'GiroEmis' => substr($empresa->giro,0,80), //LARGO DE GIRO DEL EMISOR NO PUEDE SER SUPERIOR A 80 CARACTERES
			            'Acteco' => $empresa->cod_actividad,
			            'DirOrigen' => substr($empresa->dir_origen,0,70), //LARGO DE DIRECCION DE ORIGEN NO PUEDE SER SUPERIOR A 70 CARACTERES
			            'CmnaOrigen' => substr($empresa->comuna_origen,0,20), //LARGO DE COMUNA DE ORIGEN NO PUEDE SER SUPERIOR A 20 CARACTERES
			        ],
			        'Receptor' => [
			            'RUTRecep' => substr($datos_empresa_factura->rut_cliente,0,strlen($datos_empresa_factura->rut_cliente) - 1)."-".substr($datos_empresa_factura->rut_cliente,-1),
			            'RznSocRecep' => substr($datos_empresa_factura->nombre_cliente,0,100), //LARGO DE RAZON SOCIAL NO PUEDE SER SUPERIOR A 100 CARACTERES
			            'GiroRecep' => substr($datos_empresa_factura->giro,0,40), //LARGO DEL GIRO NO PUEDE SER SUPERIOR A 40 CARACTERES
			            'DirRecep' => substr($datos_empresa_factura->direccion,0,70), //LARGO DE DIRECCION NO PUEDE SER SUPERIOR A 70 CARACTERES
			            'CmnaRecep' => substr($datos_empresa_factura->nombre_comuna,0,20), //LARGO DE COMUNA NO PUEDE SER SUPERIOR A 20 CARACTERES
			        ],
		            'Totales' => [
		                // estos valores serán calculados automáticamente
		                'MntNeto' => 0,
		                'TasaIVA' => \sasco\LibreDTE\Sii::getIVA(),
		                'IVA' => 0,
		                'MntTotal' => 0,
		            ],			        
			    ],
				'Detalle' => $lista_detalle,
		        'Referencia' => [
		            'TpoDocRef' => 33,
		            'FolioRef' => $numfactura,
		            'CodRef' => $tipo_nota_credito,
		            'RazonRef' => $glosa,
		        ]				
			];			


			//FchResol y NroResol deben cambiar con los datos reales de producción
			$caratula = [
			    //'RutEnvia' => '11222333-4', // se obtiene de la firma
			    'RutReceptor' => '60803000-K',
			    'FchResol' => $empresa->fec_resolucion,
			    'NroResol' => $empresa->nro_resolucion
			];

			$Firma = new sasco\LibreDTE\FirmaElectronica($config['firma']); //lectura de certificado digital		
			$caf = $this->facturaelectronica->get_content_caf_folio($numdocuemnto,61);
			$Folios = new sasco\LibreDTE\Sii\Folios($caf->caf_content);

			$DTE = new \sasco\LibreDTE\Sii\Dte($nota_credito);

			$DTE->timbrar($Folios);
			$DTE->firmar($Firma);		

			// generar sobre con el envío del DTE y enviar al SII
			$EnvioDTE = new \sasco\LibreDTE\Sii\EnvioDte();

			$EnvioDTE->agregar($DTE);
			$EnvioDTE->setFirma($Firma);
			$EnvioDTE->setCaratula($caratula);
			$EnvioDTE->generar();
			if ($EnvioDTE->schemaValidate()) { // REVISAR PORQUÉ SE CAE CON ESTA VALIDACION
				
				$track_id = 0;
			    $xml_dte = $EnvioDTE->generar();

			    $tipo_envio = $this->facturaelectronica->busca_parametro_fe('envio_sii'); //ver si está configurado para envío manual o automático

			    if($tipo_envio == 'automatico'){
				    $track_id = $EnvioDTE->enviar();
			    }			    

			    //$track_id = 0;

				$nombre_dte = $numdocuemnto."_61_".$idfactura."_".date("His").".xml"; // nombre archivo
				$path = date('Ym').'/'; // ruta guardado
				if(!file_exists('./facturacion_electronica/dte/'.$path)){
					mkdir('./facturacion_electronica/dte/'.$path,0777,true);
				}				
				$f_archivo = fopen('./facturacion_electronica/dte/'.$path.$nombre_dte,'w');
				fwrite($f_archivo,$xml_dte);
				fclose($f_archivo);


			  /*  $this->db->where('f.folio', $numdocuemnto);
			    $this->db->where('c.tipo_caf', 61);
				$this->db->update('folios_caf f inner join caf c on f.idcaf = c.id',array('dte' => $xml_dte,
																						  'estado' => 'O',
																						  'idfactura' => $idfactura,
																						  'path_dte' => $path,
																						  'archivo_dte' => $nombre_dte,
																						  'trackid' => $track_id
																						  )); 
*/
				$this->db->query("update f
								 set dte = '" . iconv('','UTF-8//IGNORE',$xml_dte) . "',
								 	 estado = 'O',
								 	 idfactura = '" . $idfactura . "',
								 	 path_dte = '" . $path . "',
								 	 archivo_dte = '" . $nombre_dte . "',
								 	 trackid = '" . $track_id . "'
								 from folios_caf f
								 inner join caf c on f.idcaf = c.id
								 where f.folio = '" .$numdocuemnto . "'
								 and c.tipo_caf = '61'"); 


				/*if($track_id != 0 && $datos_empresa_factura->e_mail != ''){ //existe track id, se envía correo
						$this->facturaelectronica->envio_mail_dte($idfactura);
				}*/

			}					
			
		}


        $resp['success'] = true;
		$resp['idfactura'] = $idfactura;

		$this->Bitacora->logger("I", 'factura_clientes', $idfactura);

		
        

        echo json_encode($resp);
	}


	
	public function getAllnc(){
		
		$resp = array();
		$start = $this->input->get('start');
        $limit = $this->input->get('limit');
        $opcion = $this->input->get('opcion');
        $nombres = $this->input->get('nombre');
        $tipo = "11";
        $tipo2 = "102";

		$countAll = $this->db->count_all_results("factura_clientes");
		$data = array();
		$total = 0;

		if($opcion == "Rut"){
		
			$query = $this->db->query('SELECT acc.*, c.nombres as nombre_cliente, c.rut as rut_cliente, v.nombre as nom_vendedor, td.descripcion as tipo_doc	FROM factura_clientes acc
			left join clientes c on (acc.id_cliente = c.id)
			left join vendedores v on (acc.id_vendedor = v.id)
			left join tipo_documento td on (acc.tipo_documento = td.id)
			WHERE acc.tipo_documento in ('.$tipo.','.$tipo2.') and c.rut = '.$nombres.'
			order by acc.id desc		
			limit '.$start.', '.$limit.''		 

		);

	    }else if($opcion == "Nombre"){

	    	
			$sql_nombre = "";
	        $arrayNombre =  explode(" ",$nombres);

	        foreach ($arrayNombre as $nombre) {
	        	$sql_nombre .= "and c.nombres like '%".$nombre."%' ";
	        }
	        	    	
			$query = $this->db->query('SELECT acc.*, c.nombres as nombre_cliente, c.rut as rut_cliente, v.nombre as nom_vendedor, td.descripcion as tipo_doc	FROM factura_clientes acc
			left join clientes c on (acc.id_cliente = c.id)
			left join vendedores v on (acc.id_vendedor = v.id)
			left join tipo_documento td on (acc.tipo_documento = td.id)
			WHERE acc.tipo_documento in ('.$tipo.','.$tipo2.') ' . $sql_nombre . '
			order by acc.id desc		
			limit '.$start.', '.$limit.''	
			
			);
	 
		}else if($opcion == "Todos"){

			
			$query = $this->db->query('SELECT acc.*, c.nombres as nombre_cliente, c.rut as rut_cliente, v.nombre as nom_vendedor, td.descripcion as tipo_doc	FROM factura_clientes acc
			left join clientes c on (acc.id_cliente = c.id)
			left join vendedores v on (acc.id_vendedor = v.id)
			left join tipo_documento td on (acc.tipo_documento = td.id)
			WHERE acc.tipo_documento in ('.$tipo.','.$tipo2.')
			order by acc.id desc		
			limit '.$start.', '.$limit.''	
			
			);
	

		}else{

			
		$query = $this->db->query('SELECT * 
					FROM (SELECT row_number() OVER (ORDER BY acc.id) AS rownum, acc.*, c.nombres as nombre_cliente, c.rut as rut_cliente, v.nombre as nom_vendedor, td.descripcion as tipo_doc	FROM factura_clientes acc
			left join clientes c on (acc.id_cliente = c.id)
			left join vendedores v on (acc.id_vendedor = v.id)
			left join tipo_documento td on (acc.tipo_documento = td.id)
			WHERE acc.tipo_documento in ('.$tipo.','.$tipo2.')) AS A
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
			$countAll = $total;
		}

        $resp['success'] = true;
        $resp['total'] = $countAll;
        $resp['data'] = $data;

        echo json_encode($resp);
	}

	public function validaproducto(){
		
		$resp = array();
		$idproducto = $this->input->post('idproducto');
		$idfactura = $this->input->post('idfactura');

		$query = $this->db->query('SELECT * FROM detalle_factura_cliente 
		WHERE id_producto like '.$idproducto.' AND id_factura like '.$idfactura.'');
    	$row = $query->first_row();
		
		if($query->num_rows()>0){
			$resp['success'] = true;		 	
		 }else {
		 	$resp['success'] = false;
		};

		$resp['cliente'] = $row;
        
        echo json_encode($resp);
	}

	
	public function exportNotacreditoPDF(){

		$idfactura = $this->input->get('idfactura');
		$numero = $this->input->get('numfactura');

        if ($idfactura){
		$query = $this->db->query('SELECT acc.*, c.direccion as direccion, e.nombre as giro, c.nombres as nombre_cliente, c.rut as rut_cliente, m.nombre as nombre_comuna, s.nombre as nombre_ciudad, v.nombre as nom_vendedor FROM factura_clientes acc
			left join clientes c on (acc.id_cliente = c.id)
			left join cod_activ_econ e on (c.id_giro = e.id)
			left join comuna m on (c.id_comuna = m.id)
			left join ciudad s on (c.id_ciudad = s.id)
			left join vendedores v on (acc.id_vendedor = v.id)		
			WHERE acc.id = '.$idfactura.'');
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
		$fecha = $row->fecha_venc;
		$numfact = $row->id_factura;
		list($anio, $mes, $dia) = explode("-",$fecha);
		$fecha2 = $row->fecha_factura;
		list($anio2, $mes2, $dia2) = explode("-",$fecha2);
		 
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
		$fecha = $row->fecha_venc;
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
   <table width="987px" border="0">
      <tr>
        <td width="987px">
          <table width="987px" border="0">
          <tr>
            <td width="740px" height="80px">&nbsp;</td>
	        <td width="247px" height="80px" style="font-size: 25px;vertical-align:bottom;"> N°'.$codigo.'</td>
          </tr>
          </table>
        </td>
      </tr> 
      <tr>
        <td width="987px">
          <table width="987px" border="0">
          <tr>
            <td width="90px" height="60px">&nbsp;</td>
	        <td width="90px" height="60px" style="vertical-align:bottom;">'.$dia2.'</td>
	        <td width="350px" height="60px" style="vertical-align:bottom;">'.month2string($mes2).'</td>
	        <td width="70px" height="60px" style="vertical-align:bottom;">'.$anio2.'</td>
	        <td width="387px" height="60px" style="vertical-align:bottom;">&nbsp;</td>
          </tr>
          </table>
        </td>
      </tr>       
      <tr>
        <td width="987px">
          <table width="987px" border="0">
          <tr>
            <td width="120px" height="20px">&nbsp;</td>
	        <td width="700px" height="20px" style="vertical-align:bottom;">'.$nombre_contacto.'</td>
	        <td width="100px" height="20px" style="vertical-align:bottom;">'.number_format(substr($rut_cliente,0,strlen($rut_cliente) - 1),0,".",".")."-".substr($rut_cliente,-1).'</td>
	        <td width="67px" height="20px" style="vertical-align:bottom;">&nbsp;</td>
          </tr>
          </table>
        </td>
      </tr>             
      <tr>
        <td width="987px">
          <table width="987px" border="0">
          <tr>
            <td width="120px" height="20px">&nbsp;</td>
	        <td width="700px" height="20px" style="vertical-align:bottom;">'.$direccion.'</td>
	        <td width="100px" height="20px" style="vertical-align:bottom;">'.$ciudad.'</td>
	        <td width="67px" height="20px" style="vertical-align:bottom;">&nbsp;</td>
          </tr>
          </table>
        </td>
      </tr>                   
      <tr>
      <td width="987px">
         <table width="987px" border="0">
          <tr>
	      	<td width="120px" height="20px">&nbsp;</td>
	        <td width="867px">' . $giro . '</td>
          </tr>
          </table>	        
       </td>
      </tr> 
      <tr>
      <td  width="120px" height="50px">&nbsp;</td>
      </tr>                  
      <tr>
            <td width="987px" >
            <table width="987px" border="0">';
     $tamano_maximo = 180;
     $i = 1;
    foreach($items->result() as $v){      
      $this->db->where('id', $v->id_producto);
      $producto = $this->db->get("productos");  
      $producto = $producto->result();
      $producto = $producto[0];   

          $html .= '
              
                <tr>
                  <td width="50px" height="20px">&nbsp;</td>
                  <td width="100px" height="20px">' . $v->cantidad . '</td>
                  <td width="600px" height="20px">' . $producto->nombre . '</td>
                  <td width="150px" height="20px">' . number_format($v->precio, 0, ',', '.') . '</td>                  
                  <td width="87px" height="20px">' . number_format($v->totalproducto, 0, ',', '.') . '</td>
                </tr>
             ';
          $i++;
          $tamano_maximo = $tamano_maximo - 20;
    }

    while($tamano_maximo > 0){
      $html .= '<tr><td colspan="7" height="20px">&nbsp;</td></tr>';
      $tamano_maximo = $tamano_maximo - 20; 
    }


	 $html .= '</table></td></tr>
      <tr>
      <td width="987px">
         <table width="987px" border="0">
          <tr>
	      	<td width="150px" height="20px">&nbsp;</td>
	        <td width="750px" height="20px">' . valorEnLetras($totalFactura) . '</td>
	        <td width="87px"  height="20px">' . number_format($montoNeto, 0, ',', '.') . '</td>
          </tr>
          </table>	        
       </td>
      </tr> 
      <tr>
      <td width="987px">
         <table width="987px" border="0">
          <tr>
	      	<td width="150px" height="20px">&nbsp;</td>
	        <td width="750px" height="20px">&nbsp;</td>
	        <td width="87px"  height="20px">' . number_format($ivaTotal, 0, ',', '.') . '</td>
          </tr>
          </table>	        
       </td>
      </tr> 
      <tr>
      <td width="987px">
         <table width="987px" border="0">
          <tr>
	      	<td width="150px" height="20px">&nbsp;</td>
	        <td width="750px" height="20px">&nbsp;</td>
	        <td width="87px"  height="20px">' . number_format($totalFactura, 0, ',', '.') . '</td>
          </tr>
          </table>	        
       </td>
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
		$fecha = $row->fecha_venc;
		list($anio, $mes, $dia) = explode("-",$fecha);
		$fecha2 = $row->fecha_factura;
		list($anio2, $mes2, $dia2) = explode("-",$fecha2);

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
        <td width="450"><span class="style6">&nbsp;</span><span class="colorTextoFijo"></span></td>
		<td class="style6"><center>'.$codigo.'</center></td>
      </tr>
    </table>
    <p align="right"><b>'.$dia2.'/'.$mes2.'/'.$anio2.'</b></p>
    <p align="right"><b>'.$dia.'/'.$mes.'/'.$anio.'</b></p>
    <br><br>
  <table>
  <tr>
    <td>&nbsp;</td>
   
    <td>&nbsp;</td>
  </tr>
  </table>

  <table border="0" cellspacing="0" cellpadding="0">
  		<tr>
         <td>&nbsp;</td>        
        </tr>
        <tr>
         <td>&nbsp;</td>        
        </tr>
        <tr>
         <td>&nbsp;</td>        
        </tr>
        <tr>
         <td>&nbsp;</td>        
        </tr>
      ';
      $i = 1;
	foreach($items->result() as $v){      
			$this->db->where('id', $v->id_producto);
			$producto = $this->db->get("productos");	
			$producto = $producto->result();
			$producto = $producto[0];

     $html .= '<tr>
        <td >'.$v->cantidad.'</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td ><b>'.$producto->nombre.'</b></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
      	<td >&nbsp;</td>
      	<td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
        
        <td >'.number_format($v->precio, 0, ',', '.').'</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >'.number_format($v->descuento, 0, ',', '.').'</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
      	<td >&nbsp;</td>
      	<td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
      	<td >&nbsp;</td>
      	<td >&nbsp;</td>
        <td ><b>'.number_format($v->totalproducto, 0, ',', '.').'</b></td>
        </tr>';
        $i++;

    }

    if($i < 15){
    	for($j=$i;$j<=15;$j++){
		        $html .= '<tr>
		        <td >&nbsp;</td>
		        <td >&nbsp;</td>
		        <td >&nbsp;</td>
		        <td >&nbsp;</td>
		        <td >&nbsp;</td>
		        
		        </tr>';
    	}
    }

      
      $html .='
      	<tr>
      	<td >&nbsp;</td>
      	<td >&nbsp;</td>
      	<td >&nbsp;</td>
      	<td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
      	<td >&nbsp;</td>
      	<td >&nbsp;</td>
      	<td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
      	<td >&nbsp;</td>
      	<td >&nbsp;</td>
      	<td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
      	<td >&nbsp;</td>
      	<td >&nbsp;</td>
      	<td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
      	<td >&nbsp;</td>
      	<td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
      	<td >&nbsp;</td>
        <td >&nbsp;</td>
        <td >&nbsp;</td>
        <td ><b>'.number_format($totalFactura, 0, ',', '.').'</b></td>
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











