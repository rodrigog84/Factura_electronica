<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class General extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->database();
		$this->load->model('facturaelectronica');
	}

	

    public function clientes(){


        
        $template = "template";
        $vars['content_view'] = 'general/clientes';
        $this->load->view($template,$vars); 
    }     
	


    public function vendedores(){


        
        $template = "template";
        $vars['content_view'] = 'general/vendedores';
        $this->load->view($template,$vars); 
    }     
    

    public function condicion_pago(){


        
        $template = "template";
        $vars['content_view'] = 'general/condicion_pago';
        $this->load->view($template,$vars); 
    }     
    

    public function sucursales(){


        
        $template = "template";
        $vars['content_view'] = 'general/sucursales';
        $this->load->view($template,$vars); 
    }     
    
}
