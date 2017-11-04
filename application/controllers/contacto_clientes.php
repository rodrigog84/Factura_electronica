<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class contacto_clientes extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}

	public function save(){
		$resp = array();

		$idcliente = $this->input->post('idcliente');
		$mail = $this->input->post('email');
		$nombre = $this->input->post('nombre');
		$fono = $this->input->post('fono');

		$data = array(
	        'id_cliente' => $idcliente,
	        'email' => $mail,
	        'fono' => $fono,
          	'nombre' => $nombre              
		);

		
        $this->db->insert('contactos', $data); 

        $this->Bitacora->logger("I", 'contactos', $idcliente);

        echo json_encode($resp);

	}
	
	
	public function update(){

		$resp = array();
		$idcliente = $this->input->post('idcliente');
		$mail = $this->input->post('email');
		$nombre = $this->input->post('nombre');
		$fono = $this->input->post('fono');

		$data = array(
	        'id_cliente' => $idcliente,
	        'email' => $mail,
	        'fono' => $fono,
          	'nombre' => $nombre              
		);

		$this->db->where('id', $id);
		
		$this->db->update('contactos', $data); 

        $resp['success'] = true;

        $this->Bitacora->logger("M", 'contactos', $id);

        echo json_encode($resp);
	}

	public function getAll(){

		$resp = array();
		
        $nombres = $this->input->post('nombre');
        $countAll = $this->db->count_all_results("contactos");
       
		if($nombres){
			$query = $this->db->query('SELECT acc.*, cli.rut as rut, cli.nombres as nombres FROM contactos acc
			left join clientes cli on (acc.id_cliente = cli.id)
			WHERE acc.id_cliente="'.$nombres.'"');
		}else{
			$query = $this->db->query('SELECT acc.*, cli.rut as rut, cli.nombres as nombres FROM contactos acc
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
