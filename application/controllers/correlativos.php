<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Correlativos extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}

	public function save(){
		$resp = array();

		$data = json_decode($this->input->post('data'));
		$id = $data->nombre;

		$data = array(
	        'nombre' => strtoupper($data->nombre),
	        'correlativo' => $data->correlativo
	          
		);

		$this->db->insert('correlativos', $data); 

        $resp['success'] = true;

         $this->Bitacora->logger("I", 'correlativos', $id);


        echo json_encode($resp);

	}

	public function update(){
		$resp = array();

		$data = json_decode($this->input->post('data'));
		$id = $data->id;
		$data = array(
	        'nombre' => strtoupper($data->nombre),
	        'correlativo' => $data->correlativo
	    );
		$this->db->where('id', $id);
		
		$this->db->update('correlativos', $data); 

        $resp['success'] = true;

         $this->Bitacora->logger("M", 'correlativos', $id);


        echo json_encode($resp);

	}

	public function getAll(){
		$resp = array();

        $start = $this->input->post('start');
        $limit = $this->input->post('limit');


        $nombre = $this->input->get('nombre');

		$countAll = $this->db->count_all_results("correlativos");

		if($nombre){
			$query = $this->db->query('SELECT * FROM correlativos WHERE nombre like "%'.$nombre.'%"
			limit '.$start.', '.$limit.'');
		}else{
			
			$query = $this->db->query('SELECT * FROM correlativos limit '.$start.', '.$limit.'');
			
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

	public function genera(){

		$resp = array();
		$factura = $this->input->get('valida');
		$query = $this->db->query('SELECT * FROM correlativos WHERE id like "'.$factura.'"');

		if($query->num_rows()>0){
	   		$row = $query->first_row();
	   		$resp['cliente'] = $row;
	   		$corr = (($row->correlativo)+1); 
	   		$id = ($row->id);

	   		$data3 = array(
	         'correlativo' => $corr
		    );

		    $this->db->where('id', $id);
		  
		    $this->db->update('correlativos', $data3);

		    $this->Bitacora->logger("M", 'correlativos', $id);

		    $query = $this->db->query('SELECT * FROM preventa WHERE num_ticket like "'.$corr.'"');

			if($query->num_rows()>0){

				$resp['success'] = false;
			    echo json_encode($resp);

			}else{

				$resp['success'] = true;
			    echo json_encode($resp);
				

			}

	   }else{
	   	    $resp['success'] = false;
	   	    echo json_encode($resp);
	        return false;
	   }		

	}

	public function generacoti(){

		$resp = array();
		$factura = $this->input->get('valida');
		$query = $this->db->query('SELECT * FROM correlativos WHERE id like "'.$factura.'"');

		if($query->num_rows()>0){
	   		$row = $query->first_row();
	   		$resp['cliente'] = $row;
	   		$corr = (($row->correlativo)+1); 
	   		$id = ($row->id);

	   		$data3 = array(
	         'correlativo' => $corr
		    );

		    $this->db->where('id', $id);
		  
		    $this->db->update('correlativos', $data3);

		    $this->Bitacora->logger("M", 'correlativos', $id);

		    $query = $this->db->query('SELECT * FROM cotiza_cotizaciones WHERE num_cotiza like "'.$corr.'"');

			if($query->num_rows()>0){

				$resp['success'] = false;
			    echo json_encode($resp);

			}else{

				$resp['success'] = true;
			    echo json_encode($resp);
				

			}

	   }else{
	   	    $resp['success'] = false;
	   	    echo json_encode($resp);
	        return false;
	   }		

	}

	public function generafact(){

		$resp = array();
		$factura = $this->input->get('valida');
		$tipo = 1;
		$query = $this->db->query('SELECT * FROM correlativos WHERE id ="'.$factura.'"');

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
	
	public function generancred(){

		$resp = array();
		$factura = $this->input->get('valida');
		$query = $this->db->query('SELECT * FROM correlativos WHERE id like "'.$factura.'"');

		if($query->num_rows()>0){
	   		$row = $query->first_row();
	   		$resp['cliente'] = $row;
	   		$corr = (($row->correlativo)+1); 
	   		$id = ($row->id);

	   		$data3 = array(
	         'correlativo' => $corr
		    );

		    /*$this->db->where('id', $id);
		  
		    $this->db->update('correlativos', $data3);

		    $this->Bitacora->logger("M", 'correlativos', $id);*/

		    $query = $this->db->query('SELECT * FROM factura_clientes 
		    WHERE num_factura like "'.$corr.'" AND tipo_documento like "12"');

			if($query->num_rows()>0){

				$resp['success'] = false;
			    echo json_encode($resp);

			}else{

				$resp['success'] = true;
			    echo json_encode($resp);
				

			}

	   }else{
	   	    $resp['success'] = false;
	   	    echo json_encode($resp);
	        return false;
	   }		

	}	
}
