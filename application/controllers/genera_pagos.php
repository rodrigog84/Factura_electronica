<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Genera_pagos extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}

	public function leer(){

		$resp = array();
		$caja = $this->input->post('caja');
		$cajero = $this->input->post('cajero');
		$fecha = $this->input->post('fecha');
		
        if ($fecha == 0){
		    $fecha = date("Y-m-d");
		}
		
             
		$query = $this->db->query('SELECT * FROM control_caja 
		WHERE id_caja = "'.$caja.'" and id_cajero = "'.$cajero.'" and fecha = "'.$fecha.'"');

		if($query->num_rows()>0){

			$row = $query->first_row();
	   		$resp['caja'] = $row;
	   		$resp['success'] = true;


		}else{

			 $resp['success'] = false;
			

		}

		echo json_encode($resp);		
				
	}

	public function grabar(){

		$resp = array();
		$caja = $this->input->post('caja');
		$cajero = $this->input->post('cajero');
		$fecha = $this->input->post('fecha');
		$efectivo = $this->input->post('efectivo');
		$cheques = $this->input->post('cheques');
		$otros = $this->input->post('otros');		
		
	    $query = $this->db->query('SELECT * FROM control_caja 
		WHERE id_caja = "'.$caja.'" and id_cajero = "'.$cajero.'" and fecha = "'.$fecha.'"');

		if($query->num_rows()>0){

			$row = $query->first_row();
			$idrecauda = ($row->id);

			$cajas = array(
	         'efectivo' => $efectivo,
	         'cheques' => $cheques,
	         'otros' => $otros
		    );

		    $this->db->where('id', $idrecauda);
		  
		    $this->db->update('control_caja', $cajas);
	   		


		}else{

			$cajas2 = array(
	    	 'id_caja' => $caja,
	    	 'id_cajero' => $cajero,
	    	 'fecha' => $fecha,
	         'efectivo' => $efectivo,
	         'cheques' => $cheques,
	         'otros' => $otros
	    	);

	    	$this->db->insert('control_caja', $cajas2);
	    	$idrecauda = $this->db->insert_id();
		};

		$resp['success'] = true;
		$resp['recauda'] = $idrecauda;
		echo json_encode($resp);		
				
	}

	public function update(){
		

	}

	public function getAll(){
		$resp = array();

        $ticket = $this->input->post('ticket');

		if($ticket){
			$query = $this->db->query('SELECT acc.*, c.nombre as nom_producto	FROM preventa_detalle acc
			left join productos c on (acc.id_producto = c.id)
			WHERE id_ticket like "'.$ticket.'"
			');
		

		$data = array();
		foreach ($query->result() as $row)
		{
			$data[] = $row;
		}
        $resp['success'] = true;
        $resp['data'] = $data;

        echo json_encode($resp);

        }

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

	        $resp['success'] = true;
	        echo json_encode($resp);

	   }else{
	   	    $resp['success'] = false;
	   	    echo json_encode($resp);
	        return false;
	   }

	}
}
