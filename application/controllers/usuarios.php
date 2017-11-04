<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Usuarios extends CI_Controller {



	public function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}

	public function save(){
		$resp = array();
		$nombre = strtoupper($this->input->post('nombre')); 
		$apellido = strtoupper($this->input->post('apellido')); 
		$username = $this->input->post('username'); 
		$password = $this->input->post('password'); 

		$roles = json_decode($this->input->post('roles')); 

		$data = array(
		   'nombre' => $nombre,'apellido' => $apellido,
		   'username' => $username,'password' => $password,
		   'reg_estado' => 1
		);

		$this->db->insert('usuario', $data); 
		$id = $this->db->insert_id();
        foreach($roles as $v){
        	$this->insert_usuarios_rol($id, $v->id);
        }

        $resp['success'] = true;

         $this->Bitacora->logger("I", 'usuario', $nombre);

        echo json_encode($resp);
	}

	public function update(){
		$resp = array();
		$nombre = strtoupper($this->input->post('nombre')); 
		$apellido = strtoupper($this->input->post('apellido')); 
		$username = $this->input->post('username'); 
		$password = $this->input->post('password'); 

        $id = $_REQUEST['id'];
        $roles = json_decode($this->input->post('roles')); 

        $this->delete_usuarios_rol($id);

        foreach($roles as $v){
        	$this->insert_usuarios_rol($id, $v->id);
        }

		$data = array(
		   'nombre' => $nombre,'apellido' => $apellido,
		   'username' => $username,'password' => $password
		);


		$this->db->where('id', $id);
		
		$this->db->update('usuario', $data); 
        $resp['success'] = true;

        $this->Bitacora->logger("M", 'usuario', $id);
        echo json_encode($resp);
	}
	
	public function enabled(){
		$resp = array();

        $id = $_REQUEST['id'];

		$data = array(
		   'reg_estado' => 1
		);

		$this->db->where('id', $id);
		
		$this->db->update('usuario', $data); 
        $resp['success'] = true;
        echo json_encode($resp);
	}

	public function delete_usuarios_rol($id_usuario){
		$this->db->delete('usuario_rol', array('id_usuario' => $id_usuario)); 
	}
	public function insert_usuarios_rol($id_usuario, $id_rol){
		$data = array(
		   'id_rol' => $id_rol,'id_usuario' => $id_usuario
		);
		$this->db->insert('usuario_rol', $data); 	
	}

	public function delete(){
		$resp = array();

        $id = $_REQUEST['id'];

		$data = array(
		   'reg_estado' => 0
		);

		$this->db->where('id', $id);
		
		$this->db->update('usuario', $data); 
        $resp['success'] = true;
        echo json_encode($resp);
	}

	public function getAll(){
		$resp = array();

        $start = $this->input->get('start');
        $limit = $this->input->get('limit');

        $filter = @$_REQUEST['nombre'];

		$countAll = $this->db->count_all_results("usuario");

		if($filter){
			$str_q = 'SELECT usuario.*  FROM usuario ';
			$str_q .= '';
			$str_q.= ' WHERE usuario.apellido like "'.$filter.'%" order by apellido asc ';
			if($limit){
				$str_q.= " LIMIT $start,  $limit";
			}
			$query = $this->db->query($str_q);
		}else{
			$str_q = 'SELECT usuario.*  FROM usuario order by apellido asc';
			$str_q .= '';
			if($limit){
				$str_q.= " LIMIT $start,  $limit ";
			}
			$query = $this->db->query($str_q);
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
	public function getAllWithNotFilter(){
		$resp = array();

		$countAll = $this->db->count_all_results("usuario");


		$str_q = 'SELECT usuario.*  FROM usuario ';
		$str_q .= '';
		$str_q.= '  WHERE usuario.reg_estado=1 order by nombre asc';
		$query = $this->db->query($str_q);


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
	public function getAllRoles(){
		$resp = array();

		$usuario_id = $_REQUEST['usuario_id'];

		$str_accesos = 'SELECT acc.*, rla.id_rol as existe 
			FROM roles acc 
			left join usuario_rol rla on (acc.id = rla.id_rol and rla.id_usuario = '.$usuario_id.')  
			where acc.reg_estado = 1 order by acc.nombre asc';

		$query_modulos = $this->db->query($str_accesos);

		$mods = array();
		foreach ($query_modulos->result() as $rowm)
		{
			$mods[] = $rowm;
		}

        $resp['success'] = true;
        $resp['data'] = $mods;

        echo json_encode($resp);
	}
}

