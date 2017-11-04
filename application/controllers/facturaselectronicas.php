<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Facturaselectronicas extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->database();
		$this->load->model('facturaelectronica');
	}

	
	public function empresas(){


		
		$empresa = $this->facturaelectronica->get_empresa();
		$existe = count($empresa) > 0 ? true : false;

        if($existe){



        	$form['rut'] = $empresa->rut."-".$empresa->dv;
        	$form['razon_social'] = $empresa->razon_social;
        	$form['giro'] = $empresa->giro;
        	$form['cod_actividad'] = $empresa->cod_actividad;
        	$form['direccion'] = $empresa->dir_origen;
        	$form['comuna'] = $empresa->comuna_origen;
        	$form['fec_resolucion'] = $empresa->fec_resolucion;
        	$form['nro_resolucion'] = $empresa->nro_resolucion;
        	$form['logo'] = base_url() . "facturacion_electronica/images/" . $empresa->logo;

        }else{

        	$form['rut'] = "";
        	$form['razon_social'] = "";
        	$form['giro'] = "";
        	$form['cod_actividad'] = "";
        	$form['direccion'] = "";
        	$form['comuna'] = "";
        	$form['fec_resolucion'] = "";
        	$form['nro_resolucion'] = "";
        	$form['logo'] = base_url() . "facturacion_electronica/images/sinimagen.jpg";
        }


        $vars['datosform'] = $form;
		$template = "template";
		$vars['content_view'] = 'facturaelectronica/empresas';
		$this->load->view($template,$vars);	
	}

	public function put_empresa(){
				//print_r($this->input->post(NULL,true)); exit;
		$this->load->model('facturaelectronica');
		$empresa = $this->facturaelectronica->get_empresa();
		$tipo_caf = $this->input->post('tipoCaf');
        $config['upload_path'] = "./facturacion_electronica/images/"	;
        $config['file_name'] = 'logo_empresa';
        $config['allowed_types'] = "*";
        $config['max_size'] = "10240";
        $config['overwrite'] = TRUE;


        $this->load->library('upload', $config);

        $error = false;
        $carga = false;
        if (!$this->upload->do_upload("logo") && is_null($empresa->logo)) { // si no hay descarga y no tiene archivo cargado
            print_r($this->upload->data()); 
            print_r($this->upload->display_errors());
            $error = true;
            $message = "Error en subir archivo.  Intente nuevamente";
        }else{
        	
        	//$empresa = $this->facturaelectronica->get_empresa();
    		$rut = $this->input->post('rut');
    		$array_rut = explode("-",$rut);
    		//$fecha_resolucion = $this->input->post('fec_resolucion');
    		//$fec_resolucion = substr($fecha_resolucion,6,4)."-".substr($fecha_resolucion,3,2)."-".substr($fecha_resolucion,0,2);
    		$fec_resolucion = $this->input->post('fec_resolucion');
    		$data_empresa = array(
    					'rut' => $array_rut[0],
    					'dv' => $array_rut[1],
    					'razon_social' => $this->input->post('razon_social'),
    					'giro' => $this->input->post('giro'),
    					'cod_actividad' => $this->input->post('cod_actividad'),
    					'dir_origen' => $this->input->post('direccion'),
    					'comuna_origen' => $this->input->post('comuna'),
    					'fec_resolucion' => $fec_resolucion,
    					'nro_resolucion' => $this->input->post('nro_resolucion'),
    					'logo' => 'logo_empresa.png'
    			);
        	if(count($empresa) > 0){ //actualizar
        		$this->db->where('id',1);
        		$this->db->update('empresa',$data_empresa);

        	}else{ //insertar


	        	$carga = true;
				$this->db->insert('empresa',$data_empresa);

        	}






        }



		if($error && $carga){
			unlink($config['upload_path'].$config['file_name'].$data_file_upload['file_ext']);
		}

		redirect('facturaselectronicas/empresas');	
   		/*$resp['success'] = true;
   		$resp['message'] = $error ? $message : "Carga realizada correctamente";
   		echo json_encode($resp);*/
	 }	



	public function cargacertificado(){
		//print_r($_FILES);
		//print_r($this->input->post(NULL,true)); exit;
		$password = $this->input->post('password');

        $password_encrypt = md5($password.SALT);
        $config['upload_path'] = "./facturacion_electronica/certificado/"	;

        $config['file_name'] = "certificado";
        $config['allowed_types'] = "*";
        $config['max_size'] = "10240";
        $config['overwrite'] = TRUE;
        //$config['max_width'] = "2000";
        //$config['max_height'] = "2000";

        $this->load->library('upload', $config);
       // $this->upload->do_upload("certificado");


        if (!$this->upload->do_upload("certificado")) {
            //*** ocurrio un error
            print_r($this->upload->data()); 
            //print_r($this->upload->display_errors());
            //redirect('accounts/add_cuenta/2');
            //return;
        }else{
			/*$this->db->where('nombre', 'cert_password');
			$this->db->update('param_fe',array('valor' => $password)); 

			$this->db->where('nombre', 'cert_password_encrypt'); //veremos si se puede usar la password encriptada
			$this->db->update('param_fe',array('valor' => $password_encrypt)); 
            echo $this->db->last_query(); exit;*/

        }

        $this->db->where('nombre', 'cert_password');
        $this->db->update('param_fe',array('valor' => $password)); 

        $this->db->where('nombre', 'cert_password_encrypt'); //veremos si se puede usar la password encriptada
        $this->db->update('param_fe',array('valor' => $password_encrypt));         
   		$dataupload = $this->upload->data();


		

   		redirect('');
   		//$resp['success'] = true;
   		//echo json_encode($resp);
	 }


}
