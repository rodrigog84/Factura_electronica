<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Clientes extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}

	public function elimina(){

	    $resp = array();
	    $idcliente = $this->input->post('idcliente');

	    $query = $this->db->query('SELECT * FROM factura_clientes WHERE id_cliente ="'.$idcliente.'"');

	    if($query->num_rows()>0){

	    	 $resp['success'] = false;
	    	 echo json_encode($resp);

	    }else{

	    $query = $this->db->query('DELETE FROM clientes WHERE id = "'.$idcliente.'"');
	    $query = $this->db->query('DELETE FROM clientes_sucursales WHERE id_cliente = "'.$idcliente.'"');
	   
	    $resp['success'] = true;
	    echo json_encode($resp);

	    	

	    };

	  }

	public function validaRut(){

		
		$resp = array();
		$rut = $this->input->get('valida');
		$rut1 = $this->input->get('valida');
		$iddocu = 1;
		
		if(strpos($rut,"-")==false){
	        $RUT[0] = substr($rut, 0, -1);
	        $RUT[1] = substr($rut, -1);
	    }else{
	        $RUT = explode("-", trim($rut));
	    }
	    $elRut = str_replace(".", "", trim($RUT[0]));
	    $factor = 2;
	    $suma=0;
	    for($i = strlen($elRut)-1; $i >= 0; $i--):
	        $factor = $factor > 7 ? 2 : $factor;
	        $suma += $elRut{$i}*$factor++;
	    endfor;
	    $resto = $suma % 11;
	    $dv = 11 - $resto;
	    if($dv == 11){
	        $dv=0;
	    }else if($dv == 10){
	        $dv="k";
	    }else{
	        $dv=$dv;
	    }
	   if($dv == trim(strtolower($RUT[1]))){
		  
			$query = $this->db->query('SELECT acc.*, ciu.nombre as nombre_ciudad, com.nombre as nombre_comuna, g.nombre as nom_giro, 
			ven.nombre as nombre_vendedor, g.nombre as giro FROM clientes acc
			left join ciudad c on (acc.id_ciudad = c.id)
			left join cod_activ_econ g on (acc.id_giro = g.id)
			left join comuna com on (acc.id_comuna = com.id)
			left join comuna ciu on (acc.id_ciudad = ciu.id)
			left join vendedores ven on (acc.id_vendedor = ven.id)
		    WHERE acc.rut="'.$rut1.'"');

			
	   		if($query->num_rows()>0){
	   			$row = $query->first_row();
	   			$resp['cliente'] = $row;
	   		}

	   		$resp['success'] = true;
	        echo json_encode($resp);

	   }else{
	   	    $resp['success'] = false;
	   	    echo json_encode($resp);
	        return false;
	   }

	
	 }

	public function save(){
		$resp = array();

		$data = json_decode($this->input->post('data'));
		$id = $data->rut;

		$data = array(
	        'nombres' => strtoupper($data->nombres),
	        'id_giro' => $data->id_giro,
	        'fono' => $data->fono,			
	        'direccion' => strtoupper($data->direccion),
	        'id_ciudad' => $data->id_ciudad,
	        'id_comuna' => $data->id_comuna,
	        'rut' => $data->rut,
	        'id_vendedor' => $data->id_vendedor,
	        'e_mail' => $data->e_mail,
	        'descuento' => $data->descuento,		
	        'fecha_incripcion' => date('Y-m-d'),
            'fecha_ult_actualiz' => date('Y-m-d'),
            'estado' => $data->estado,
          	'id_pago' => $data->id_pago,
          	'cupo_disponible' => $data->cupo_disponible,
          	'imp_adicional' => $data->imp_adicional,
          	'tipo' => 1
              
		);

		
        $this->db->insert('clientes', $data); 

        $this->Bitacora->logger("I", 'clientes', $id);

        echo json_encode($resp);

	}
	
	
	public function update(){

		$resp = array();
		$nombres = $this->input->post('nombre');
		$rut = $this->input->post('rut');
		$id = $this->input->post('idcliente');
		$direccion = $this->input->post('direccion');
		$ciudad = $this->input->post('ciudad');		
		$comuna = $this->input->post('comuna');		
		$giro = $this->input->post('giro');
		$fono = $this->input->post('fono');
		$mail = $this->input->post('mail');
		$vendedor = $this->input->post('vendedor');
		$descuento = $this->input->post('descuento');
		$tipopago = $this->input->post('tipopago');
		$disponible = $this->input->post('disponible');
		$impuesto = $this->input->post('impuesto');
		$fechaincorporacion = $this->input->post('fechaincorporacion');
		$fechaactualiza = $this->input->post('fechaactualiza');
		$estado = $this->input->post('estado');
		$tipocliente = $this->input->post('tipocliente');
		
		
		$data = array(
			'nombres' => strtoupper($nombres),
	        'id_giro' => $giro,
	        'fono' => $fono,			
	        'direccion' => strtoupper($direccion),
	        'id_ciudad' => $ciudad,
	        'id_comuna' => $comuna,
	        'id_vendedor' => $vendedor,
	        'e_mail' => $mail,
	        'descuento' => $descuento,		
	        'fecha_incripcion' => $fechaincorporacion,
            'fecha_ult_actualiz' => date('Y-m-d'),
            'estado' => $estado,
          	'id_pago' => $tipopago,
          	'cupo_disponible' => $disponible,
          	'imp_adicional' => $impuesto,
          	'tipo' => $tipocliente
              
	    );
	    
		$this->db->where('id', $id);
		
		$this->db->update('clientes', $data); 

        $resp['success'] = true;

        $this->Bitacora->logger("M", 'clientes', $id);

        echo json_encode($resp);
	}

	public function listaTipos()
		{
			$this->load->database();

			$query = $this->db->get('estados');
			$result = array();
			foreach ($query->result() as $row)
			{
			   $result['data'][] = $row;
			}
			$result['data'][] = array("id"=>0, "nombre"=>"Todos");
			$result['success'] = true;
			//query total
			$query_total = $this->db->get('estados');
			$result['total'] = $query_total->num_rows();
			echo json_encode($result);
		}

	public function getAll(){
		$resp = array();

        $start = $this->input->get('start');
        $limit = $this->input->get('limit');
        //filtro por nombre
        $nombres = $this->input->get('nombre');
        $idcliente = $this->input->get('idcliente');
		$tipo = $this->input->get('fTipo');
		$opcion = $this->input->get('opcion');


		$countAll = $this->db->count_all_results("clientes");
        
		if($opcion == "Rut"){
			$query = $this->db->query('SELECT acc.*, c.nombre as nombre_ciudad, com.nombre as nombre_comuna,
			ven.nombre as nombre_vendedor, g.nombre as giro, con.nombre as nom_id_pago FROM clientes acc
			left join ciudad c on (acc.id_ciudad = c.id)
			left join cod_activ_econ g on (acc.id_giro = g.id)
			left join comuna com on (acc.id_comuna = com.id)
			left join vendedores ven on (acc.id_vendedor = ven.id)
			left join cond_pago con on (acc.id_pago = con.id)
			WHERE acc.rut = "'.$nombres.'" AND acc.tipo = 1 OR acc.rut = "'.$nombres.'" AND acc.tipo = 3  ');

			$total = 0;

		  foreach ($query->result() as $row)
			{
				$total = $total +1;
			
			}

			$countAll = $total;

		}else if($opcion == "Nombre"){

			$sql_nombre = "";
	        $arrayNombre =  explode(" ",$nombres);

	        foreach ($arrayNombre as $nombre) {
	        	$sql_nombre .= "acc.nombres like '%".$nombre."%' and ";
	        }
	        
			$query = $this->db->query('SELECT acc.*, c.nombre as nombre_ciudad, com.nombre as nombre_comuna,
			ven.nombre as nombre_vendedor, g.nombre as giro, con.nombre as nom_id_pago FROM clientes acc
			left join ciudad c on (acc.id_ciudad = c.id)
			left join cod_activ_econ g on (acc.id_giro = g.id)
			left join comuna com on (acc.id_comuna = com.id)
			left join vendedores ven on (acc.id_vendedor = ven.id)
			left join cond_pago con on (acc.id_pago = con.id)
			WHERE ' . $sql_nombre . ' 1 = 1');

			$total = 0;

		  foreach ($query->result() as $row)


		    
			{
				$total = $total +1;
			
			}

			$countAll = $total;
			
		
		}else if($opcion == "Todos"){
			$query = $this->db->query('SELECT acc.*, c.nombre as nombre_ciudad, com.nombre as nombre_comuna,
			ven.nombre as nombre_vendedor, g.nombre as giro, con.nombre as nom_id_pago FROM clientes acc
			left join ciudad c on (acc.id_ciudad = c.id)
			left join cod_activ_econ g on (acc.id_giro = g.id)
			left join comuna com on (acc.id_comuna = com.id)
			left join vendedores ven on (acc.id_vendedor = ven.id)
			left join cond_pago con on (acc.id_pago = con.id) 
			WHERE acc.tipo = 1 or acc.tipo = 3
			order by acc.nombres asc
            limit '.$start.', '.$limit.'
            '
			);
		}else{
			$query = $this->db->query('SELECT acc.*, c.nombre as nombre_ciudad, com.nombre as nombre_comuna,
			ven.nombre as nombre_vendedor, g.nombre as giro, con.nombre as nom_id_pago FROM clientes acc
			left join ciudad c on (acc.id_ciudad = c.id)
			left join cod_activ_econ g on (acc.id_giro = g.id)
			left join comuna com on (acc.id_comuna = com.id)
			left join vendedores ven on (acc.id_vendedor = ven.id)
			left join cond_pago con on (acc.id_pago = con.id) 
			WHERE acc.tipo = 1 or acc.tipo = 3
			order by acc.nombres asc
            limit '.$start.', '.$limit.''
			);
		}

		$data = array();
		
		foreach ($query->result() as $row)
		{

		if ($row->tipo == 1 or $row->tipo == 3){

			$rutautoriza = $row->rut;
		   	if (strlen($rutautoriza) == 8){
		      $ruta1 = substr($rutautoriza, -1);
		      $ruta2 = substr($rutautoriza, -4, 3);
		      $ruta3 = substr($rutautoriza, -7, 3);
		      $ruta4 = substr($rutautoriza, -8, 1);
		      $row->rut = ($ruta4.".".$ruta3.".".$ruta2."-".$ruta1);
		    };
		    if (strlen($rutautoriza) == 9){
		      $ruta1 = substr($rutautoriza, -1);
		      $ruta2 = substr($rutautoriza, -4, 3);
		      $ruta3 = substr($rutautoriza, -7, 3);
		      $ruta4 = substr($rutautoriza, -9, 2);
		      $row->rut = ($ruta4.".".$ruta3.".".$ruta2."-".$ruta1);
		   
		    };

		     if (strlen($rutautoriza) == 2){
		      $ruta1 = substr($rutautoriza, -1);
		      $ruta2 = substr($rutautoriza, -4, 1);
		      $row->rut = ($ruta2."-".$ruta1);
		     
		    };

		   if (strlen($rutautoriza) == 7){
		      $ruta1 = substr($rutautoriza, -1);
		      $ruta2 = substr($rutautoriza, -4, 3);
		      $ruta3 = substr($rutautoriza, -7, 3);
		      $row->rut = ($ruta3.".".$ruta2."-".$ruta1);
		     
		    };
		    
		    
		    if (strlen($rutautoriza) == 4){
		      $ruta1 = substr($rutautoriza, -1);
		      $ruta2 = substr($rutautoriza, -4, 3);
		      $row->rut = ($ruta2."-".$ruta1);
		     
		    };	


		     if (strlen($rutautoriza) == 6){
		      $ruta1 = substr($rutautoriza, -1);
		      $ruta2 = substr($rutautoriza, -4, 3);
		      $ruta3 = substr($rutautoriza, -6, 2);
		      $row->rut = ($ruta3.".".$ruta2."-".$ruta1);
		     
		    };
			$data[] = $row;
			$resp['cliente'] = $row;
		}
		}
        $resp['success'] = true;
        $resp['total'] = $countAll;
        $resp['data'] = $data;

        echo json_encode($resp);
	}
	
	public function getAllc(){
		$resp = array();

        $start = $this->input->get('start');
        $limit = $this->input->get('limit');
       
        $idcliente = $this->input->get('idcliente');
		
		$countAll = $this->db->count_all_results("clientes");
        
		if($idcliente){
			$query = $this->db->query('SELECT acc.*, c.nombre as nombre_ciudad, com.nombre as nombre_comuna,
			ven.nombre as nombre_vendedor, g.nombre as giro, con.nombre as nom_id_pago FROM clientes acc
			left join ciudad c on (acc.id_ciudad = c.id)
			left join cod_activ_econ g on (acc.id_giro = g.id)
			left join comuna com on (acc.id_comuna = com.id)
			left join vendedores ven on (acc.id_vendedor = ven.id)
			left join cond_pago con on (acc.id_pago = con.id)
			WHERE acc.id = '.$idcliente.'');
		}

		$data = array();
		
		foreach ($query->result() as $row)
		{

	
			$rutautoriza = $row->rut;
		   	if (strlen($rutautoriza) == 8){
		      $ruta1 = substr($rutautoriza, -1);
		      $ruta2 = substr($rutautoriza, -4, 3);
		      $ruta3 = substr($rutautoriza, -7, 3);
		      $ruta4 = substr($rutautoriza, -8, 1);
		      $row->rut = ($ruta4.".".$ruta3.".".$ruta2."-".$ruta1);
		    };
		    if (strlen($rutautoriza) == 9){
		      $ruta1 = substr($rutautoriza, -1);
		      $ruta2 = substr($rutautoriza, -4, 3);
		      $ruta3 = substr($rutautoriza, -7, 3);
		      $ruta4 = substr($rutautoriza, -9, 2);
		      $row->rut = ($ruta4.".".$ruta3.".".$ruta2."-".$ruta1);
		   
		    };

		     if (strlen($rutautoriza) == 2){
		      $ruta1 = substr($rutautoriza, -1);
		      $ruta2 = substr($rutautoriza, -4, 1);
		      $row->rut = ($ruta2."-".$ruta1);
		     
		    };
			$data[] = $row;
			$resp['cliente'] = $row;
	
		}
        $resp['success'] = true;
        //$resp['cliente'] = $row;
        $resp['total'] = $countAll;
        $resp['data'] = $data;

        echo json_encode($resp);
	}
}
