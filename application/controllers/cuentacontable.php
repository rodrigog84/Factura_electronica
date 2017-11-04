<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cuentacontable extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}

	public function save(){
		$resp = array();

		$data = json_decode($this->input->post('data'));
		$id = strtoupper($data->nombre); 

		$data = array(
	      	'codigo' => $data->codigo,
	        'nombre' => strtoupper($data->nombre),
	        'id_agrupacion' => $data->id_agrupacion,
	        'id_imputacion' => $data->id_imputacion,
	        'flujo_fondos' => $data->flujo_fondos,
	        'id_estado_situacion' => $data->id_estado_situacion
		);

		$this->db->insert('cuenta_contable', $data); 

        $resp['success'] = true;

         $this->Bitacora->logger("I", 'cuenta_contable', $id);


        echo json_encode($resp);

	}

	public function update(){
		$resp = array();

		$data = json_decode($this->input->post('data'));
		$id = $data->id;
		$data = array(
	        'codigo' => $data->codigo,
	        'nombre' => strtoupper($data->nombre),
	        'id_agrupacion' => $data->id_agrupacion,
	        'id_imputacion' => $data->id_imputacion,
	        'flujo_fondos' => $data->flujo_fondos,
	        'id_estado_situacion' => $data->id_estado_situacion
	    );
		$this->db->where('id', $id);
		
		$this->db->update('cuenta_contable', $data); 

        $resp['success'] = true;

         $this->Bitacora->logger("M", 'cuenta_contable', $id);


        echo json_encode($resp);

	}

	public function getAll(){
		
		$resp = array();
        $start = $this->input->post('start');
        $limit = $this->input->post('limit');
        $nombres = $this->input->post('nombre');

		$countAll = $this->db->count_all_results("cuenta_contable");

		if($nombres){
			$query = $this->db->query("SELECT id, codigo, nombre, id_agrupacion, id_imputacion, flujo_fondos, id_estado_situacion, cancelaabono, cancelacargo, if(id_imputacion=1,'SI','NO') as tiene_imputacion, if(cancelaabono=1,'ABONO','CARGO') as tipo_cancelacion FROM cuenta_contable WHERE nombre like '%".$nombres."%' limit ".$start.", ".$limit);
		}else{
			
			$query = $this->db->query("SELECT id, codigo, nombre, id_agrupacion, id_imputacion, flujo_fondos, id_estado_situacion, cancelaabono, cancelacargo, if(id_imputacion=1,'SI','NO') as tiene_imputacion, if(cancelaabono=1,'ABONO','CARGO') as tipo_cancelacion  FROM cuenta_contable");
			
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


	public function getByName(){
		$nombre = $this->input->post('nombre');
		$resp = array();
		$countAll = $this->db->count_all_results("cuenta_contable");

		$query = $this->db->query("SELECT id, codigo, nombre, id_agrupacion, id_imputacion, flujo_fondos, id_estado_situacion, cancelaabono, cancelacargo, if(id_imputacion=1,'SI','NO') as tiene_imputacion, if(cancelaabono=1,'ABONO','CARGO') as tipo_cancelacion FROM cuenta_contable 
								  where nombre  like '%".$nombre."%'");

		$data = array();
		foreach ($query->result() as $row)
		{
			$data[] = $row;
		}
        $resp['success'] = true;
        $resp['total'] = count($data);
        $resp['data'] = $data;

        echo json_encode($resp);
	}


	public function getCuentaById(){
		$idcuenta = $this->input->post('idcuenta');
		//$countAll = $this->db->count_all_results("cuenta_contable");

		$query = $this->db->query("SELECT id, nombre, if(id_imputacion=1,'SI','NO') as imputacion, if(cancelaabono=1,'ABONO','CARGO') as tipo_cancelacion FROM cuenta_contable 
								  where id = '$idcuenta'");

		$data = array();
		foreach ($query->result() as $row)
		{
			$data[] = $row;
		}
        $resp['success'] = true;
        $resp['total'] = count($data);
        $resp['data'] = $data;

        echo json_encode($resp);
	}


	public function actualizaCuenta(){
		$resp = array();
		$idcuenta = $this->input->post('idcuenta');
		$imputacion = $this->input->post('imputacion') == 'SI' ? 1 : 0;
		$tipocancelacion = $this->input->post('tipocancelacion');
		if($tipocancelacion == 'CARGO'){
			$cancelacargo = 1;
			$cancelaabono = 0;
		}else{
			$cancelacargo = 0;
			$cancelaabono = 1;			
		}
		//$countAll = $this->db->count_all_results("cuenta_contable");

		$query = $this->db->query("update cuenta_contable set
									id_imputacion = '$imputacion',
									cancelacargo = '$cancelacargo',
									cancelaabono = '$cancelaabono'
								  where id = '$idcuenta'");
		echo "update cuenta_contable set
									id_imputacion = '$imputacion',
									cancelacargo = '$cancelacargo',
									cancelaabono = '$cancelaabono'
								  where id = '$idcuenta'";
        echo json_encode($resp);
	}	
	

}
