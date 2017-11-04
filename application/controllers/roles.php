<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Roles extends CI_Controller {



	public function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}

	public function save(){
		$resp = array();
		$nombre = strtoupper($this->input->post('nombre')); 
		$descripcion = strtoupper($this->input->post('descripcion')); 

		$accesos = json_decode($this->input->post('accesos')); 

		$data = array(
		   'nombre' => $nombre,'descripcion' => $descripcion, 'reg_estado'=>1
		);
		$data['reg_fecha_modificacion'] = date('Y-m-d H:i:s');

		$this->db->insert('roles', $data); 
		$id = $this->db->insert_id();
        foreach($accesos as $v){
        	if($v->existe)
        		$this->insert_roles_accesos($id, $v->id);
        }

        $resp['success'] = true;
        echo json_encode($resp);
	}

	public function update(){
		$resp = array();

		$nombre = strtoupper($this->input->post('nombre')); 
		$descripcion = strtoupper($this->input->post('descripcion')); 
        $id = $_REQUEST['id'];
        $accesos = json_decode($this->input->post('accesos')); 

        $this->delete_roles_accesos($id);

        foreach($accesos as $v){
        	if($v->existe)
        		$this->insert_roles_accesos($id, $v->id);
        }

		$data = array(
		   'nombre' => $nombre,'descripcion' => $descripcion
		);

		$data['reg_fecha_modificacion'] = date('Y-m-d H:i:s');

		$this->db->where('id', $id);
		
		$this->db->update('roles', $data); 
        $resp['success'] = true;
        echo json_encode($resp);
	}
	
	public function enabled(){
		$resp = array();

        $id = $_REQUEST['id'];

		$data = array(
		   'reg_estado' => 1
		);

		$data['reg_fecha_modificacion'] = date('Y-m-d H:i:s');
		$this->db->where('id', $id);
		
		$this->db->update('roles', $data); 
        $resp['success'] = true;
        echo json_encode($resp);
	}

	public function delete_roles_accesos($id_rol){
		$this->db->delete('rol_acceso', array('id_rol' => $id_rol)); 
	}
	public function insert_roles_accesos($id_rol, $id_acceso){

		$data = array(
		   'id_rol' => $id_rol,'id_acceso' => $id_acceso
		);
		$this->db->insert('rol_acceso', $data); 	
	}

	public function delete(){
		$resp = array();

        $id = $_REQUEST['id'];

		$data = array(
		   'reg_estado' => 0
		);

		$data['reg_fecha_modificacion'] = date('Y-m-d H:i:s');
		$this->db->where('id', $id);
		
		$this->db->update('roles', $data); 
        $resp['success'] = true;
        echo json_encode($resp);
	}

	public function getAll(){
		$resp = array();

        $start = $this->input->get('start');
        $limit = $this->input->get('limit');

        $filter = @$_REQUEST['nombre'];

		$countAll = $this->db->count_all_results("roles");

		if($filter){
			$str_q = 'SELECT roles.*  FROM roles ';
			$str_q .= '';
			$str_q.= ' WHERE roles.nombre like "'.$filter.'%" order by nombre asc';
			if($limit){
				$str_q.= " LIMIT $start,  $limit";
			}
			$query = $this->db->query($str_q);
		}else{
			$str_q = 'SELECT roles.*  FROM roles order by nombre asc';
			$str_q .= '';
			if($limit){
				$str_q.= " LIMIT $start,  $limit";
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

		$countAll = $this->db->count_all_results("roles");


		$str_q = 'SELECT roles.*  FROM roles ';
		$str_q .= '';
		$str_q.= '  WHERE roles.reg_estado=1 order by nombre asc';
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

	public function getAllAccesos(){
		$resp = array();

		$rol_id = $_REQUEST['rol_id'];

		$str_accesos = 'SELECT acc.*,rla.id_rol as existe 
			FROM accesos acc 
			left join rol_acceso rla on (acc.id = rla.id_acceso and rla.id_rol = '.$rol_id.')  
			where acc.reg_estado = 1 order by acc.descripcion asc';

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
