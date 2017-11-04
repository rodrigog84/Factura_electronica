<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sucursales_clientes extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}

	public function save(){
		$resp = array();

		$idcliente = $this->input->post('idcliente');
		$direccion = $this->input->post('direccion');
		$idcomuna = $this->input->post('comuna');
		$idciudad = $this->input->post('ciudad');
		$mail = $this->input->post('email');
		$contacto = $this->input->post('contacto');
		$fono = $this->input->post('fono');

		$data = array(
	        'id_cliente' => $idcliente,
	        'direccion' => strtoupper($direccion),
	        'id_ciudad' => $idciudad,
	        'id_comuna' => $idcomuna,
	        'mail_contacto' => $mail,
	        'fono_contacto' => $fono,
          	'nombre_contacto' => $contacto              
		);

		
        $this->db->insert('clientes_sucursales', $data); 

        $this->Bitacora->logger("I", 'clientes_sucursales', $idcliente);

        echo json_encode($resp);

	}
	
	
	public function update(){

		$resp = array();
		$idcliente = $this->input->post('idcliente');
		$direccion = $this->input->post('direccion');
		$idcomuna = $this->input->post('comuna');
		$idciudad = $this->input->post('ciudad');
		$mail = $this->input->post('email');
		$contacto = $this->input->post('contacto');
		$fono = $this->input->post('fono');
		$id = $data->id;
		$data = array(
			'id_cliente' => $id_cliente,
	        'direccion' => strtoupper($direccion),
	        'id_ciudad' => $idciudad,
	        'id_comuna' => $idcomuna,
	        'mail_contacto' => $mail,
	        'fono_contacto' => $fono,
          	'nombre_contacto' => $contacto    
              
	    );
		$this->db->where('id', $id);
		
		$this->db->update('clientes_sucursales', $data); 

        $resp['success'] = true;

        $this->Bitacora->logger("M", 'clientes_sucursales', $id);

        echo json_encode($resp);
	}

	public function getAll(){

		$resp = array();
		
        $nombres = $this->input->post('nombre');
        $countAll = $this->db->count_all_results("clientes_sucursales");
       
		if($nombres){
			$query = $this->db->query('SELECT acc.*, c.nombre as nombre_ciudad,
			cli.rut as rut, com.nombre as nombre_comuna, cli.nombres as nombres FROM clientes_sucursales acc
			left join ciudad c on (acc.id_ciudad = c.id)
			left join comuna com on (acc.id_comuna = com.id)
			left join clientes cli on (acc.id_cliente = cli.id)
			WHERE acc.id_cliente="'.$nombres.'"');
		}else{
			$query = $this->db->query('SELECT acc.*, c.nombre as nombre_ciudad,
			cli.rut as rut, com.nombre as nombre_comuna, cli.nombres as nombres FROM clientes_sucursales acc
			left join ciudad c on (acc.id_ciudad = c.id)
			left join comuna com on (acc.id_comuna = com.id)
			left join clientes cli on (acc.id_cliente = cli.id)');
		}
	
		$data = array();
		
		foreach ($query->result() as $row)
		{
			$data[] = $row;
			$resp['cliente'] = $row;
		}
        $resp['success'] = true;
        $resp['total'] = $countAll;
        $resp['data'] = $data;

        echo json_encode($resp);
	}
}
