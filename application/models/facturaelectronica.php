<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Ion Auth Model
*
* Version: 2.5.2
*
* Author:  Ben Edmunds
* 		   ben.edmunds@gmail.com
*	  	   @benedmunds
*
* Added Awesomeness: Phil Sturgeon
*
* Location: http://github.com/benedmunds/CodeIgniter-Ion-Auth
*
* Created:  10.01.2009
*
* Last Change: 3.22.13
*
* Changelog:
* * 3-22-13 - Additional entropy added - 52aa456eef8b60ad6754b31fbdcc77bb
*
* Description:  Modified auth system based on redux_auth with extensive customization.  This is basically what Redux Auth 2 should be.
* Original Author name has been kept but that does not mean that the method has not been modified.
*
* Requirements: PHP5 or above
*
*/

class Facturaelectronica extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper('cookie');
		$this->load->helper('date');
	}



	public function ruta_libredte(){
		$base_path = __DIR__;
		$base_path = str_replace("\\", "/", $base_path);
		$path = $base_path . "/../libraries/inc.php";		
		return $path;
	}

	public function genera_config(){
		$config = [
		    'firma' => [
		        'file' => $this->ruta_certificado(),
		        'pass' => $this->busca_parametro_fe('cert_password'),
		    ],
		];

		return $config;
	}


	public function ruta_certificado(){
		$base_path = __DIR__;
		$base_path = str_replace("\\", "/", $base_path);
		$path = $base_path . "/../../facturacion_electronica/certificado/certificado.p12";		
		return $path;
	}

	 public function busca_parametro_fe($parametro){
		$this->db->select('valor ')
		  ->from('param_fe')
		  ->where('nombre',$parametro);
		$query = $this->db->get();
		$parametro = $query->row();	
		return $parametro->valor;
	 }	


	 public function set_parametro_fe($parametro,$valor){
		  $this->db->where('nombre',$parametro);
		  $this->db->update('param_fe',array('valor' => $valor));
		return 1;
	 }		 


	 public function put_trackid($idfactura,$trackid){
		  $this->db->where('idfactura',$idfactura);
		  $this->db->update('folios_caf',array('trackid' => $trackid));
		return 1;
	 }		 

	 public function contribuyentes_autorizados($start = null,$limit = null){

	 	//$tabla_contribuyentes = $this->busca_parametro_fe('tabla_contribuyentes');
	 	$tabla_contribuyentes = 'contribuyentes_autorizados';

	 	$countAll = $this->db->count_all_results($tabla_contribuyentes);

		$data = $this->db->select("rut, dv, concat(rut,'-',dv) as rut_contribuyente, razon_social, nro_resolucion, format(fec_resolucion,'dd/MM/yyyy','en-US') as fec_resolucion, mail, url",false)
		  ->from($tabla_contribuyentes)
		  ->order_by('razon_social');

		$data = is_null($start) || is_null($limit) ? $data : $data->limit($limit,$start);
		$query = $this->db->get();
		//echo $this->db->last_query();
		return array('total' => $countAll, 'data' => $query->result());

	 }

	 public function log_libros($start = null,$limit = null){

	 	$countAll = $this->db->count_all_results('log_libros');
		$data = $this->db->select('id, mes, anno, tipo_libro, archivo, date_format(created_at,"%d/%m/%Y") as fecha_creacion',false)
		  ->from('log_libros')
		  ->order_by('anno','desc')
		  ->order_by('mes','desc');

		$data = is_null($start) || is_null($limit) ? $data : $data->limit($limit,$start);
		$query = $this->db->get();
		return array('total' => $countAll, 'data' => $query->result());

	 }

	public function get_empresa(){
		$this->db->select('rut, dv, razon_social, giro, cod_actividad, dir_origen, comuna_origen, fec_resolucion, nro_resolucion, logo ')
		  ->from('empresa')
		  ->limit(1);
		$query = $this->db->get();
		return $query->row();
	 }


	public function datos_dte_periodo($mes,$anno){
		$this->db->select('f.folio, f.path_dte, f.archivo_dte, f.dte, f.pdf, f.pdf_cedible, f.trackid, c.tipo_caf, tc.nombre as tipo_doc ')
		  ->from('folios_caf f')
		  ->join('caf c','f.idcaf = c.id')
		  ->join('tipo_caf tc','c.tipo_caf = tc.id')
		  ->join('factura_clientes fc','f.idfactura = fc.id','left')
		  //->where('left(fc.fecha_factura,7)',$anno."-".$mes);
		  ->where('left(f.updated_at,7)',$anno."-".$mes) //AUN TENEMOS FACTURAS QUE NO SE EMITEN POR EL SISTEMA
		  ->where('f.estado','O');
		$query = $this->db->get();
		return $query->result();
	}



	public function datos_dte_proveedores_periodo($mes,$anno){
		$this->db->select('d.id, d.idproveedor, d.dte, d.envios_recibos, d.recepcion_dte, d.resultado_dte ')
		  ->from('dte_proveedores d')
		  ->where('left(d.fecha_documento,7)',$anno."-".$mes);
		$query = $this->db->get();
		return $query->result();
	}


	public function valida_existe_libro($mes,$anno,$tipo){
		$this->db->select('id, mes, anno, tipo_libro ')
		  ->from('log_libros')
		  ->where('mes',$mes)
		  ->where('anno',$anno)
		  ->where('tipo_libro',$tipo);
		$query = $this->db->get();
		return count($query->result()) > 0 ? true : false;
	}


	public function put_log_libros($mes,$anno,$tipo,$archivo){

			$array_insert = array(
						'mes' => $mes,
						'anno' => $anno,
						'tipo_libro' => $tipo,
						'archivo' => $archivo
						);

		$this->db->insert('log_libros',$array_insert); 
		return true;
	}



	public function get_empresa_factura($id_factura){

		$tabla_contribuyentes = $this->busca_parametro_fe('tabla_contribuyentes');

		$this->db->select('c.nombres as nombre_cliente, c.rut as rut_cliente, c.direccion, m.nombre as nombre_comuna, s.nombre as nombre_ciudad, c.fono, e.nombre as giro, isnull(ca.mail,c.e_mail) as e_mail',false)
		  ->from('factura_clientes acc')
		  ->join('clientes c','acc.id_cliente = c.id','left')
		  ->join('cod_activ_econ e','c.id_giro = e.id','left')
		  ->join('comuna m','c.id_comuna = m.id','left')		  
		  ->join('ciudad s','c.id_ciudad = s.id','left')	
		  ->join($tabla_contribuyentes . ' ca','c.rut = concat(ca.rut,ca.dv)','left')
		  ->where('acc.id',$id_factura)
		  ->limit(1);
		$query = $this->db->get();
		return $query->row();
	 }	 


	public function get_detalle_factura($id_factura){
		$this->db->select('p.nombre, f.precio, f.cantidad, f.descuento , f.iva, f.totalproducto')
		  ->from('detalle_factura_cliente f')
		  ->join('productos p','f.id_producto = p.id')
		  ->where('f.id_factura',$id_factura);
		$query = $this->db->get();
		return $query->result();
	 }

	public function get_detalle_factura_glosa($id_factura){
		$this->db->select('f.glosa, f.neto, f.iva, f.total ')
		  ->from('detalle_factura_glosa f')
		  ->where('f.id_factura',$id_factura);
		$query = $this->db->get();
		return $query->result();
	 }	 


	public function get_content_caf_folio($folio,$tipo_documento){
		$this->db->select('f.estado, c.archivo, c.caf_content ')
		  ->from('caf c')
		  ->join('folios_caf f','f.idcaf = c.id')
		  ->where('f.folio',$folio)
		  ->where('c.tipo_caf',$tipo_documento)
		  ->limit(1);
		  $query = $this->db->get();
		  $caf = $query->row();					  
		  return $caf;
	 }	 

	public function datos_dte($idfactura){

		$this->db->select('f.id, f.folio, f.path_dte, f.archivo_dte, f.dte, f.pdf, f.pdf_cedible, f.trackid, c.tipo_caf, tc.nombre as tipo_doc, cae.nombre as giro ')
		  ->from('folios_caf f')
		  ->join('caf c','f.idcaf = c.id')
		  ->join('tipo_caf tc','c.tipo_caf = tc.id')
		  ->join('factura_clientes fc','f.idfactura = fc.id','left')
		  ->join('clientes cl','fc.id_cliente = cl.id','left')
		  ->join('cod_activ_econ cae','cl.id_giro = cae.id','left')

		  ->where('f.idfactura',$idfactura)
		  ->limit(1);
		$query = $this->db->get();
		return $query->row();
	}	



	public function get_libro_by_id($idlibro){
		$this->db->select('id, mes, anno, tipo_libro, archivo, created_at ')
		  ->from('log_libros')
		  ->where('id',$idlibro);
		$query = $this->db->get();
		return $query->row();
	}	

	public function datos_dte_by_trackid($trackid){
		$this->db->select('f.id, f.folio, f.path_dte, f.archivo_dte, f.dte, f.pdf, f.pdf_cedible, f.trackid, c.tipo_caf, tc.nombre as tipo_doc ')
		  ->from('folios_caf f')
		  ->join('caf c','f.idcaf = c.id')
		  ->join('tipo_caf tc','c.tipo_caf = tc.id')
		  ->where('f.trackid',$trackid)
		  ->limit(1);
		$query = $this->db->get();
		return $query->row();
	}	



	public function datos_dte_provee($iddte){
		$this->db->select('d.id, p.nombres as proveedor, p.e_mail, d.path_dte, d.arch_rec_dte, d.arch_res_dte, d.arch_env_rec, date_format(d.fecha_documento,"%d/%m/%Y") as fecha_documento , date_format(d.created_at,"%d/%m/%Y") as fecha_creacion ',false)
		  ->from('dte_proveedores d')
		  ->join('proveedores p','d.idproveedor = p.id')
		  ->where('d.id',$iddte)
		  ->order_by('d.id','desc');
		$query = $this->db->get();
		return $query->row();
	}


	 public function exportFePDF($idfactura,$tipo_consulta,$cedible = null){

	 	include $this->ruta_libredte();
	 	if($tipo_consulta == 'id'){
	 		$factura = $this->datos_dte($idfactura);
	 	}else if($tipo_consulta == 'trackid'){
	 		$factura = $this->datos_dte_by_trackid($idfactura);
	 	}


	 	$nombre_pdf = is_null($cedible) ? $factura->pdf : $factura->pdf_cedible;

	 	//file_exists 
	 	$crea_archivo = true;
	 	if($nombre_pdf != ''){
			$base_path = __DIR__;
			$base_path = str_replace("\\", "/", $base_path);
			$file = $base_path . "/../../facturacion_electronica/pdf/".$factura->path_dte.$nombre_pdf;		 		
	 		if(file_exists($file)){
	 			$crea_archivo = false;
	 		}
	 	}

	 	if($crea_archivo){
			// sin límite de tiempo para generar documentos
			set_time_limit(0);
		 	// archivo XML de EnvioDTE que se generará
		 	$archivo = "./facturacion_electronica/dte/".$factura->path_dte.$factura->archivo_dte;
		 	if(file_exists($archivo)){
		 		$content_xml = file_get_contents($archivo);
		 	}else{
		 		$content_xml = $factura->dte;
		 	}

		 	// Cargar EnvioDTE y extraer arreglo con datos de carátula y DTEs
		 	$EnvioDte = new \sasco\LibreDTE\Sii\EnvioDte();
		 	$EnvioDte->loadXML($content_xml);
			$Caratula = $EnvioDte->getCaratula();
			$Documentos = $EnvioDte->getDocumentos();	 	

			if(!file_exists('./facturacion_electronica/pdf/'.$factura->path_dte)){
				mkdir('./facturacion_electronica/pdf/'.$factura->path_dte,0777,true);
			}		

			$base_path = __DIR__;
			$base_path = str_replace("\\", "/", $base_path);
			$path_pdf = $base_path . "/../../facturacion_electronica/pdf/".$factura->path_dte;				


			$empresa = $this->get_empresa();
			foreach ($Documentos as $DTE) {
			    if (!$DTE->getDatos())
			        die('No se pudieron obtener los datos del DTE');
			    $pdf = new \sasco\LibreDTE\Sii\PDF\Dte(false); // =false hoja carta, =true papel contínuo (false por defecto si no se pasa)
			    $pdf->setFooterText();
			    $pdf->setLogo('./facturacion_electronica/images/logo_empresa.png'); // debe ser PNG!
			    $pdf->setGiroCliente($factura->giro); 
			    $pdf->setGiroEmisor($empresa->giro); 
			    $pdf->setResolucion(['FchResol'=>$Caratula['FchResol'], 'NroResol'=>$Caratula['NroResol']]);
			    /*if(!is_null($cedible)){
			    	$pdf->setCedible(true);
			    }*/
			    $pdf->agregar($DTE->getDatos(), $DTE->getTED());
			    if($factura->tipo_caf == 33 || $factura->tipo_caf == 34 || $factura->tipo_caf == 52){
				    $pdf->setCedible(true);
				    $pdf->agregar($DTE->getDatos(), $DTE->getTED());			    	
			    }


			    //$pdf->Output('facturacion_electronica/pdf/'.$factura->path_dte.'dte_'.$Caratula['RutEmisor'].'_'.$DTE->getID().'.pdf', 'FI');
			    $archivo = 'dte_'.$Caratula['RutEmisor'].'_'.$DTE->getID();
			    $nombre_archivo = $archivo.".pdf";
			    //$tipo_generacion = is_null($cedible) ? 'FI' : 'F';
			    $tipo_generacion = 'FI';
			    $pdf->Output($path_pdf.$nombre_archivo, $tipo_generacion);
			    $nombre_campo = is_null($cedible) ? 'pdf' : 'pdf_cedible';

			    $this->db->where('idfactura', $idfactura);
				$this->db->update('folios_caf',array($nombre_campo => $nombre_archivo)); 		    

			}		

		}else{

			$filename = $nombre_pdf; /* Note: Always use .pdf at the end. */

			header('Content-type: application/pdf');
			header('Content-Disposition: inline; filename="' . $filename . '"');
			header('Content-Transfer-Encoding: binary');
			header('Content-Length: ' . filesize($file));
			header('Accept-Ranges: bytes');

			@readfile($file);


		}
	}
	 


	public function exportFePDFCompra($idcompra){


		$dte = $this->facturaelectronica->get_provee_by_id($idcompra);
		$path_archivo = "./facturacion_electronica/dte_provee_tmp/".$dte->path;

    	$xml_content = file_get_contents($path_archivo.$dte->filename);


	 	include $this->ruta_libredte();
		$EnvioDte = new \sasco\LibreDTE\Sii\EnvioDte();
		$EnvioDte->loadXML($xml_content);
		$Caratula = $EnvioDte->getCaratula();
		$Documentos = $EnvioDte->getDocumentos();


		$path_pdf = './facturacion_electronica/pdf_compra/';
		if(!file_exists($path_pdf.$dte->path)){
			mkdir($path_pdf.$dte->path,0777,true);
		}	

		$base_path = __DIR__;
		$base_path = str_replace("\\", "/", $base_path);
		$path_pdf = $base_path . "/../../facturacion_electronica/pdf_compra/".$dte->path;				

		// directorio temporal para guardar los PDF
			// procesar cada DTEs e ir agregándolo al PDF
		foreach ($Documentos as $DTE) {
		    if (!$DTE->getDatos())
		        die('No se pudieron obtener los datos del DTE');
		    $pdf = new \sasco\LibreDTE\Sii\PDF\Dte(false); // =false hoja carta, =true papel contínuo (false por defecto si no se pasa)
		    $pdf->setFooterText();
		    $pdf->setLogo('./facturacion_electronica/images/logo_empresa.png'); // debe ser PNG!
		    $pdf->setResolucion(['FchResol'=>$Caratula['FchResol'], 'NroResol'=>$Caratula['NroResol']]);
		    //$pdf->setCedible(true);
		    $pdf->agregar($DTE->getDatos(), $DTE->getTED());

		    //echo $dir.'/dte_'.$Caratula['RutEmisor'].'_'.$DTE->getID().'.pdf'; exit;
		    $archivo = 'dte_'.$Caratula['RutEmisor'].'_'.$DTE->getID();
		    $nombre_archivo = $archivo.".pdf";		    
		    $pdf->Output($path_pdf.$nombre_archivo, 'FI');
		}

		//$data_archivo = basename($path_archivo.$dte->filename);
		//print_r($xml_content); 	 	
	}





	public function carga_contribuyentes($path_base,$archivo){

		$this->db->trans_start();
		$this->db->query('truncate contribuyentes_autorizados'); 

		$base_path = __DIR__;
		$base_path = str_replace("\\", "/", $base_path);
		
		$file = $base_path . "/../../facturacion_electronica/base_contribuyentes/".$path_base.$archivo;				



		$this->db->query('LOAD DATA LOW_PRIORITY LOCAL INFILE "' . $file . '" REPLACE INTO TABLE contribuyentes_autorizados FIELDS TERMINATED BY ";" LINES TERMINATED BY "\n" IGNORE 1 LINES (rut,razon_social,nro_resolucion,fec_resolucion,mail,url);'); 

		$tabla_contribuyentes = $this->busca_parametro_fe('tabla_contribuyentes');
		$tabla_inserta = $tabla_contribuyentes == 'contribuyentes_autorizados_1' ? 'contribuyentes_autorizados_2' : 'contribuyentes_autorizados_1';

		$this->db->query("insert into " . $tabla_inserta . " (rut,dv,razon_social,nro_resolucion,fec_resolucion,mail,url)
						select SUBSTRING_INDEX(rut, '-', 1) as rut, SUBSTRING_INDEX(rut, '-', -1) as dv, razon_social, nro_resolucion, concat(SUBSTRING(fec_resolucion,7,4),'-',SUBSTRING(fec_resolucion,4,2),'-',SUBSTRING(fec_resolucion,1,2)) as fec_resolucion, mail, url  from contribuyentes_autorizados");

		$array_insert = array(
						'nombre_archivo' => $archivo,
						'ruta' => $path_base,
						);

		$this->db->insert('log_cargas_bases_contribuyentes',$array_insert); 


		$this->set_parametro_fe('tabla_contribuyentes',$tabla_inserta);

		$this->db->query('truncate '. $tabla_contribuyentes);

		$this->db->trans_complete(); 		

	 }	 


	 public function registro_email($data){

		$this->db->select('id')
		  ->from('email_fe');
		$query = $this->db->get();
		$email = $query->row();	 		

        	if(count($email) > 0){ //actualizar
        		$this->db->where('id',1);
        		$this->db->update('email_fe',$data);
        	}else{ //insertar
        		$data['created_at'] = date("Y-m-d H:i:s");
				$this->db->insert('email_fe',$data);
        	}	 	
        return true;
	 }

	public function get_email(){
		$this->db->select('email_contacto, pass_contacto, tserver_contacto, port_contacto, host_contacto, email_intercambio, pass_intercambio, tserver_intercambio, port_intercambio, host_intercambio ')
		  ->from('email_fe');
		$query = $this->db->get();
		return $query->row();
	 }


	public function get_provee_by_id($idcompra){
		$this->db->select('l.id, l.path, l.filename, l.rutemisor, l.dvemisor, l.fecemision, l.procesado, c.razon_social, c.mail, l.fecenvio, l.created_at, l.envios_recibos, l.recepcion_dte, l.resultado_dte, l.arch_env_rec, l.arch_rec_dte, l.arch_res_dte, estado_res_dte ',false)
		  ->from('lectura_dte_email l')
		  ->join('contribuyentes_autorizados_1 c','l.rutemisor = c.rut','left')
		  ->where('l.id',$idcompra);
		$query = $this->db->get();
		//echo $this->db->last_query(); exit;
		return $query->row();
	 }




	public function reporte_provee($start,$limit){

		$data_provee = $this->db->select('count(l.id) as cantidad ')
		  ->from('lectura_dte_email l');
		
		$query = $this->db->get();   

        $result_cantidad = $query->row()->cantidad; 



		$data_provee = $this->db->select("l.id, c.razon_social, concat(l.rutemisor,'-',l.dvemisor) rutemisor, c.mail, l.fecemision, l.fecenvio, l.created_at, l.procesado",false)
		  ->from('lectura_dte_email l')
		  ->join('contribuyentes_autorizados_1 c','l.rutemisor = c.rut','left')
		  ->order_by('l.id');

		$data_provee = !$limit ? $data_provee : $data_provee->limit($limit,$start);

		$query = $this->db->get();
		//echo $this->db->last_query();
		$result = $query->result();
		//var_dump($result); exit;
		 return array('cantidad' => $result_cantidad,'data' => $result);
	}


	 public function dte_compra($dte){
	 	echo $dte['filename']."<br>";
	 	$this->db->select('filename')
	 			->from('lectura_dte_email')
	 			->where('filename',$dte['filename']);

		$query = $this->db->get();
		if(count($query->result()) == 0){

			$config = $this->genera_config();
			include_once $this->ruta_libredte();	
			$EnvioDte = new \sasco\LibreDTE\Sii\EnvioDte();
			$EnvioDte->loadXML($dte['content']);

			$empresa = $this->facturaelectronica->get_empresa();

			$receptor_factura = $EnvioDte->getReceptor();
			$array_receptor_factura = explode("-",$receptor_factura);

			$documentos = $EnvioDte->getDocumentos();
			$documento = $documentos[0];
			//print_r($documento->getResumen()); exit;

			if($empresa->rut == $array_receptor_factura[0]){ // validamos que sea una factura de la empresa

				$rut_emisor = $EnvioDte->getEmisor();
				$array_rut_emisor = explode("-",$rut_emisor);


				$path = date('Ym').'/'; // ruta guardado
				$array_insert = array(
									  'path' => $path,
									  'filename' => $dte['filename'],
									  'content' => $dte['content'],
									  'rutemisor' => $array_rut_emisor[0],
									  'dvemisor' => $array_rut_emisor[1],
									  'fecemision' => $EnvioDte->getFechaEmisionFinal()
									  );

				$this->db->insert('lectura_dte_email',$array_insert);


				
				if(!file_exists('./facturacion_electronica/dte_provee_tmp/' . $path . '/')){
					mkdir('./facturacion_electronica/dte_provee_tmp/' . $path . '/',0777,true);
				}				
				$f_archivo = fopen('./facturacion_electronica/dte_provee_tmp/' . $path . '/' .$dte['filename'],'w');
				fwrite($f_archivo,$dte['content']);
				fclose($f_archivo);	

			}

		
		}  

	 }

	public function envio_mail_dte($idfactura){


			$factura = $this->datos_dte($idfactura);
			$track_id = $factura->trackid;
			$path = $factura->path_dte;
			$nombre_dte = $factura->archivo_dte;

			$empresa = $this->get_empresa();
			$datos_empresa_factura = $this->get_empresa_factura($idfactura);

			$messageBody  = 'Envío de DTE<br><br>';
	        $messageBody .= '<b>Datos Emisor:</b><br>';
	        $messageBody .= $empresa->razon_social.'<br>';
	        $messageBody .= 'RUT:'.$empresa->rut.'-'.$empresa->dv .'<br><br>';

	        $messageBody .= '<b>Datos Receptor:</b><br>';
	        $messageBody .= $datos_empresa_factura->nombre_cliente.'<br>';
	        $messageBody .= 'RUT:'.substr($datos_empresa_factura->rut_cliente,0,strlen($datos_empresa_factura->rut_cliente) - 1)."-".substr($datos_empresa_factura->rut_cliente,-1) .'<br><br>';			        

	        $messageBody .= '<a href="'. base_url() .'facturas/exportFePDF_mail/'.$track_id.'" >Ver Factura</a><br><br>';

	        $messageBody .= 'Este correo adjunta Documentos Tributarios Electrónicos (DTE) para el receptor electrónico indicado. Por favor responda con un acuse de recibo (RespuestaDTE) conforme al modelo de intercambio de Factura Electrónica del SII.<br><br>';
	        $messageBody .= 'Facturación Electrónica Infosys SPA.';


	        $email_data = $this->facturaelectronica->get_email();
		    if(count($email_data) > 0 && !is_null($datos_empresa_factura->e_mail)){ //MAIL SE ENVÍA SÓLO EN CASO QUE TENGAMOS REGISTRADOS EMAIL DE ORIGEN Y DESTINO
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
				
			    $this->email->from($email_data->email_intercambio, 'Factura Electrónica '. NOMBRE_EMPRESA);
			    $this->email->to($datos_empresa_factura->e_mail);

			    #$this->email->bcc(array('rodrigo.gonzalez@info-sys.cl','cesar.moraga@info-sys.cl','sergio.arriagada@info-sys.cl','rene.gonzalez@info-sys.cl')); 
			    $this->email->subject('Envio de DTE ' .$track_id . '_'.$empresa->rut.'-'.$empresa->dv."_".substr($datos_empresa_factura->rut_cliente,0,strlen($datos_empresa_factura->rut_cliente) - 1)."-".substr($datos_empresa_factura->rut_cliente,-1));
			    $this->email->message($messageBody);

			    $this->email->attach('./facturacion_electronica/dte/'.$path.$nombre_dte);

			    try {
			      $this->email->send();
			      //var_dump($this->email->print_debugger());
			      	        //exit;
			    } catch (Exception $e) {
			      echo $e->getMessage() . '<br />';
			      echo $e->getCode() . '<br />';
			      echo $e->getFile() . '<br />';
			      echo $e->getTraceAsString() . '<br />';
			      echo "no";

			    }
			    return true;

			}else{

				return false;
			}

	}



	public function guarda_csv($archivo){

			//$this->db->query('truncate guarda_csv'); 
			$codproceso = randomstring_mm(10);
			$fila = 1;
			if (($gestor = fopen($archivo, "r")) !== FALSE) {
			    while (($datos = fgetcsv($gestor, 1000, ",")) !== FALSE) {
			        $numero = count($datos);
			        //echo "<p> $numero de campos en la línea $fila: <br /></p>\n";
			        $fila++;
			        $fechafactura = substr($datos[2],6,4)."-".substr($datos[2],3,2)."-".substr($datos[2],0,2);
			        $array_rut = explode("-",$datos[4]);
			        $array_data = array(
			        					'tipocaf' => $datos[0],
			        					'folio' => $datos[1],
			        					'fechafactura' => $fechafactura,
			        					'condicion' => $datos[3],
			        					'rut' => $array_rut[0],
			        					'dv' => $array_rut[1],
			        					'razonsocial' => $datos[5],
			        					'giro' => $datos[6],
			        					'direccion' => $datos[7],
			        					'comuna' => $datos[8],
			        					'ciudad' => $datos[9],
			        					'cuenta' => $datos[10],
			        					'neto' => $datos[11],
			        					'iva' => $datos[12],
			        					'total' => $datos[13],
			        					'codigo' => $datos[14],
			        					'cantidad' => $datos[15],
			        					'unidad' => $datos[16],
			        					'nombre' => $datos[17],
			        					'preciounit' => $datos[18],
			        					'totaldetalle' => $datos[19],
			        					'codigoproceso' => $codproceso
			        			);

			        $this->db->insert('guarda_csv',$array_data);
			        /*for ($c=0; $c < $numero; $c++) {
			            echo $datos[$c] . "<br />\n";
			        }*/
			    }
			    fclose($gestor);
			}

			return $codproceso;

	}



	public function estado_tipo_documento($tipo_documento){
		$this->db->select('f.id ')
						  ->from('folios_caf f')
						  ->join('caf c','f.idcaf = c.id')
						  ->where('c.tipo_caf',$tipo_documento)
						  ->where("f.estado = 'P'");
		$query = $this->db->get();
		$folios_existentes = $query->result();				

       	return count($folios_existentes);
	 }




	public function crea_dte_csv($codproceso){

		$this->db->select('tipocaf, folio ')
			->from('guarda_csv')
			->where('codigoproceso',$codproceso)
			->group_by('tipocaf')
			->group_by('folio');

		$query = $this->db->get();
		$data_doctos = $query->result();


		$config = $this->genera_config();
		include $this->ruta_libredte();
		$empresa = $this->get_empresa();

		foreach ($data_doctos as $docto) {


			//header('Content-type: text/plain; charset=ISO-8859-1');
			

			$this->db->select('tipocaf, folio, fechafactura, condicion, rut, dv, razonsocial, giro, direccion, comuna, ciudad, cuenta, neto, iva, total, codigo, cantidad, unidad, nombre, preciounit, totaldetalle ')
		  			->from('guarda_csv')
		  			->where('tipocaf',$docto->tipocaf)
		  			->where('folio',$docto->folio);
			$query = $this->db->get();
			$data_csv = $query->result();


			$datos_folio = $this->get_content_caf_folio($docto->folio,$docto->tipocaf);


			if(count($datos_folio) > 0){
				//SÓLO SE CARGA AQUELLOS FOLIOS QUE EXISTEN Y ESTÁN PENDIENTES O TOMADOS
				if($datos_folio->estado == 'P' || $datos_folio->estado == 'T'){

				$tipodocumento = caftotd($docto->tipocaf);

				$this->db->select('id')
			  			->from('clientes')
			  			->where('rut',$data_csv[0]->rut.$data_csv[0]->dv);
				$query = $this->db->get();
				$data_cliente = $query->row();

				if(count($data_cliente) > 0){
					$idcliente = $data_cliente->id;
				}else{ // SI NO EXISTE CLIENTE, SE CREA
						$array_data_cliente = array(
												'rut' => $data_csv[0]->rut.$data_csv[0]->dv,
												'nombres' => $data_csv[0]->razonsocial,
												'direccion' => $data_csv[0]->direccion,

											);

						$this->db->insert('clientes', $array_data_cliente);	
						$idcliente = $this->db->insert_id();											

				}



				$factura_cliente = array(
					'tipo_documento' => $tipodocumento,
			        'id_cliente' => $idcliente,
			        'num_factura' => $docto->folio,
			        'id_vendedor' => 1,
			        'sub_total' => $data_csv[0]->neto,
			        'neto' => $data_csv[0]->neto,
			        'iva' => $data_csv[0]->iva,
			        'totalfactura' => $data_csv[0]->total,
			        'fecha_factura' => $data_csv[0]->fechafactura,
			        'fecha_venc' => $data_csv[0]->fechafactura,
			        'forma' => 1	          
				);

				$this->db->insert('factura_clientes', $factura_cliente); 
				$idfactura = $this->db->insert_id();


				$datos_empresa_factura = $this->get_empresa_factura($idfactura);
				$detalle_factura = $this->get_detalle_factura_glosa($idfactura);

				$i = 0;
				$lista_detalle = array();			
				foreach ($data_csv as $regcsv) {
					$factura_clientes_item = array(
				        'id_factura' => $idfactura,
				        'glosa' => $regcsv->nombre,
				        'neto' => $regcsv->totaldetalle,
				        'iva' => $regcsv->totaldetalle*0.19,
				        'total' => $regcsv->totaldetalle*1.19
					);

					$this->db->insert('detalle_factura_glosa', $factura_clientes_item);

					$lista_detalle[$i]['NmbItem'] = $regcsv->nombre;
					$lista_detalle[$i]['QtyItem'] = $regcsv->cantidad;
					$lista_detalle[$i]['PrcItem'] = $regcsv->preciounit;
					$i++;
					// FIN								
				}// FIN REGCSV


				if($tipodocumento == 101 || $tipodocumento == 103){  // SI ES FACTURA ELECTRONICA O FACTURA EXENTA ELECTRONICA

							$tipo_caf = $docto->tipocaf;

							$factura = [
							    'Encabezado' => [
							        'IdDoc' => [
							            'TipoDTE' => $docto->tipocaf,
							            'Folio' => $docto->folio,
							        ],
							        'Emisor' => [
							            'RUTEmisor' => $empresa->rut.'-'.$empresa->dv,
							            'RznSoc' => substr($empresa->razon_social,0,100), //LARGO DE RAZON SOCIAL NO PUEDE SER SUPERIOR A 100 CARACTERES,
							            'GiroEmis' => substr($empresa->giro,0,80), //LARGO DE GIRO DEL EMISOR NO PUEDE SER SUPERIOR A 80 CARACTERES
							            'Acteco' => $empresa->cod_actividad,
							            'DirOrigen' => substr($empresa->dir_origen,0,70), //LARGO DE DIRECCION DE ORIGEN NO PUEDE SER SUPERIOR A 70 CARACTERES
							            'CmnaOrigen' => substr($empresa->comuna_origen,0,20), //LARGO DE COMUNA DE ORIGEN NO PUEDE SER SUPERIOR A 20 CARACTERES
							        ],
							        'Receptor' => [
							            'RUTRecep' => $data_csv[0]->rut."-".$data_csv[0]->dv,
							            'RznSocRecep' => substr($data_csv[0]->razonsocial,0,100), //LARGO DE RAZON SOCIAL NO PUEDE SER SUPERIOR A 100 CARACTERES
							            'GiroRecep' => substr($data_csv[0]->giro,0,40),  //LARGO DEL GIRO NO PUEDE SER SUPERIOR A 40 CARACTERES
							            'DirRecep' => substr($data_csv[0]->direccion,0,70), //LARGO DE DIRECCION NO PUEDE SER SUPERIOR A 70 CARACTERES
							            'CmnaRecep' => substr($data_csv[0]->comuna,0,20), //LARGO DE COMUNA NO PUEDE SER SUPERIOR A 20 CARACTERES
							        ],
							    ],
								'Detalle' => $lista_detalle
							];

							//FchResol y NroResol deben cambiar con los datos reales de producción
							$caratula = [
							    //'RutEnvia' => '11222333-4', // se obtiene de la firma
							    'RutReceptor' => '60803000-K',
							    'FchResol' => $empresa->fec_resolucion,
							    'NroResol' => $empresa->nro_resolucion
							];								
										// Objetos de Firma y Folios
							$Firma = new sasco\LibreDTE\FirmaElectronica($config['firma']); //lectura de certificado digital		

							$caf = $this->get_content_caf_folio($docto->folio,$tipo_caf);
							
							$Folios = new sasco\LibreDTE\Sii\Folios($caf->caf_content);
								
							$DTE = new \sasco\LibreDTE\Sii\Dte($factura);

							$DTE->timbrar($Folios);
							$DTE->firmar($Firma);		


							// generar sobre con el envío del DTE y enviar al SII
							$EnvioDTE = new \sasco\LibreDTE\Sii\EnvioDte();
							$EnvioDTE->agregar($DTE);
							$EnvioDTE->setFirma($Firma);
							$EnvioDTE->setCaratula($caratula);
							$xml_dte = $EnvioDTE->generar();

							if ($EnvioDTE->schemaValidate()) { // REVISAR PORQUÉ SE CAE CON ESTA VALIDACION
								
								$track_id = 0;
							    $xml_dte = $EnvioDTE->generar();
							    //$track_id = $EnvioDTE->enviar();
							    $tipo_envio = $this->busca_parametro_fe('envio_sii'); //ver si está configurado para envío manual o automático


								$nombre_dte = $docto->folio."_". $tipo_caf ."_".$idfactura."_".date("His").".xml"; // nombre archivo
								$path = date('Ym').'/'; // ruta guardado
								if(!file_exists('./facturacion_electronica/dte/'.$path)){
									mkdir('./facturacion_electronica/dte/'.$path,0777,true);
								}				
								$f_archivo = fopen('./facturacion_electronica/dte/'.$path.$nombre_dte,'w');
								fwrite($f_archivo,$xml_dte);
								fclose($f_archivo);

							    if($tipo_envio == 'automatico'){
								    $track_id = $EnvioDTE->enviar();
							    }



							    $this->db->where('f.folio', $docto->folio);
							    $this->db->where('c.tipo_caf', $tipo_caf);
								$this->db->update('folios_caf f inner join caf c on f.idcaf = c.id',array('dte' => $xml_dte,
																										  'estado' => 'O',
																										  'idfactura' => $idfactura,
																										  'path_dte' => $path,
																										  'archivo_dte' => $nombre_dte,
																										  'trackid' => $track_id
																										  )); 
								if($track_id != 0 && $datos_empresa_factura->e_mail != ''){ //existe track id, se envía correo
									$this->envio_mail_dte($idfactura);
								}




							}

						} //FIN CREACION FACTURA

					} // FIN  if($datos_folio->estado == 'P' || $datos_folio->estado == 'T'){

				} // FIN if(count($datos_folio) > 0){
							

		}




	}



}
