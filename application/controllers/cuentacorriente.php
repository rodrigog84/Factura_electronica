<?php header('Access-Control-Allow-Origin: *'); ?>
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cuentacorriente extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		error_reporting(0);
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
		$countAll = $this->db->count_all_results("cuenta_corriente");

		$query = $this->db->query('SELECT cc.id, c.nombres as cliente, c.rut, cco.nombre as cuentacontable, cc.saldo,
									(select if(sum(saldo) is not null, sum(saldo),0) from detalle_cuenta_corriente where idctacte = cc.id and fechavencimiento < curdate()) as deudavencida
								 FROM cuenta_corriente cc
								  inner join clientes c on cc.idcliente = c.id
								  inner join cuenta_contable cco on cc.idcuentacontable = cco.id
								  where saldo > 0
								  order by nombres');


		$data = array();
		foreach ($query->result() as $row)
		{
			$row->rut_sf = $row->rut;
			$row->dato_cliente = substr($row->rut, 0, strlen($row->rut) - 1)."-".substr($row->rut,-1)." ".$row->cliente;
			$row->rut = number_format(substr($row->rut, 0, strlen($row->rut) - 1),0,".",".")."-".substr($row->rut,-1); //formatea rut

			$data[] = $row;
		}
        $resp['success'] = true;
        $resp['total'] = count($data);
        $resp['data'] = $data;

        echo json_encode($resp['data']);
	}


	public function getAllCartolas(){
		
		$resp = array();
		$countAll = $this->db->count_all_results("cuenta_corriente");

		$query = $this->db->query('SELECT cc.id, c.nombres as cliente, c.rut, cco.nombre as cuentacontable, cc.saldo,
									(select if(sum(saldo) is not null, sum(saldo),0) from detalle_cuenta_corriente where idctacte = cc.id and fechavencimiento < curdate()) as deudavencida
								 FROM cuenta_corriente cc
								  inner join clientes c on cc.idcliente = c.id
								  inner join cuenta_contable cco on cc.idcuentacontable = cco.id
								  order by c.nombres, cc.fechaactualiza desc');

		$data = array();
		foreach ($query->result() as $row)
		{
			$row->rut_sf = $row->rut;
			$row->rut = number_format(substr($row->rut, 0, strlen($row->rut) - 1),0,".",".")."-".substr($row->rut,-1); //formatea rut
			$data[] = $row;
		}
        $resp['success'] = true;
        $resp['total'] = count($data);
        $resp['data'] = $data;

        echo json_encode($resp);
	}

	public function getIdCuentaCorriente(){
		$cuenta = $this->input->post('cuenta');
		$cliente = $this->input->post('cliente');

		$resp = array();

		$query = $this->db->query("SELECT id from cuenta_corriente 
								  where idcliente = '" . $cliente . "' and idcuentacontable = '". $cuenta . "'");

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


	public function getByNameCartola(){
		$nombre = $this->input->post('nombre');
		$resp = array();
		$countAll = $this->db->count_all_results("cuenta_corriente");

		$query = $this->db->query("SELECT cc.id, c.nombres as cliente, c.rut, cco.nombre as cuentacontable, cc.saldo FROM cuenta_corriente cc
								  inner join clientes c on cc.idcliente = c.id
								  inner join cuenta_contable cco on cc.idcuentacontable = cco.id
								  where c.nombres like '%" . $nombre . "%'");

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

	public function getByName(){
		$nombre = $this->input->post('nombre');
		$resp = array();
		$countAll = $this->db->count_all_results("cuenta_corriente");

		$query = $this->db->query("SELECT cc.id, c.nombres as cliente, c.rut, cco.nombre as cuentacontable, cc.saldo FROM cuenta_corriente cc
								  inner join clientes c on cc.idcliente = c.id
								  inner join cuenta_contable cco on cc.idcuentacontable = cco.id
								  where saldo > 0 and c.nombres like '%" . $nombre . "%'
								  order by cc.fechaactualiza desc");

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


	public function getDocumentoById(){

		$idDocumento = $this->input->post('idDocumento');
		
		$resp = array();
		//$countAll = $this->db->count_all_results("cuenta_corriente")->where('idcliente',$idCliente)->where('idcuentacontable',$idCuenta);
		$query = $this->db->query('SELECT d.* FROM cuenta_corriente c 
									inner join detalle_cuenta_corriente d on d.idctacte = c.id 
									where d.id = ' . $idDocumento . ' limit 1');
		//$query = $this->db->query('SELECT * FROM cuenta_corriente where idcliente = ' . $idCliente . ' AND idcuentacontable = ' . $idCuenta . ' limit 1');

		$data = array();
		if(count($query->result()) > 0){
			foreach ($query->result() as $row)
			{
				$data[] = $row;
			}
		}else{
			$data[] = array('saldo' => 0);
		}
        $resp['success'] = true;
        //$resp['total'] = $countAll;
        $resp['data'] = $data;

        echo json_encode($resp);
	}



	public function getCuentaCorrienteById($idctacte = ''){
		
		$resp = array();
		$sqlCuentaCorriente =  $idctacte != '' ? " and cc.id = '" . $idctacte . "'" : "";

		$query = $this->db->query('SELECT cc.id, c.nombres as cliente, c.id as idcliente, c.rut, cco.nombre as cuentacontable, format(cc.saldo,0,"de_DE") as saldo,
									(select if(sum(saldo) is not null, sum(saldo),0) from detalle_cuenta_corriente where idctacte = cc.id and fechavencimiento < curdate()) as deudavencida
								 FROM cuenta_corriente cc
								  inner join clientes c on cc.idcliente = c.id
								  inner join cuenta_contable cco on cc.idcuentacontable = cco.id
								  where saldo > 0 '.$sqlCuentaCorriente);


		$data = array();

		//$row->rut = number_format(substr($row->rut, 0, strlen($row->rut) - 1),0,".",".")."-".substr($row->rut,-1); //formatea rut		
		$data = $query->row();
        $resp['success'] = true;
        $resp['total'] = count($data);
        $resp['data'] = $data;

        echo json_encode($resp['data']);
	}



	public function getImputacionByDocto(){

		$idCuenta = $this->input->post('idCuenta');
		
		$resp = array();
		//$countAll = $this->db->count_all_results("cuenta_corriente")->where('idcliente',$idCliente)->where('idcuentacontable',$idCuenta);
		$query = $this->db->query('SELECT id_imputacion FROM cuenta_contable c 
              					   where c.id = ' . $idCuenta . ' limit 1');

		$data = array();
		if(count($query->result()) > 0){
			foreach ($query->result() as $row)
			{

				$data[] = $row;
			}
		}else{
			$data[] = array('id_imputacion' => 0);
		}
        $resp['success'] = true;
        //$resp['total'] = $countAll;
        $resp['data'] = $data;

        echo json_encode($resp);
	}

	public function getDocumentos(){
		
		//$idcuentacorriente = $this->input->post('idcuentacorriente');
		$cuenta = $this->input->post('cuenta');
		$cliente = $this->input->post('cliente');	


		$sqlCuentaCorriente = $cliente != '' ? " and c.id = '" . $cliente . "'": "";
		$resp = array();
		//$countAll = $this->db->count_all_results("cuenta_corriente")->where('idcliente',$idCliente)->where('idcuentacontable',$idCuenta);

		if($cuenta == 18){ // CHEQUES POR COBRAR
			$query = $this->db->query("select dc.id, concat(t.descripcion,' ',dc.numdocumento) as nombre, date_format(dc.fecha,'%d/%m/%Y') as fecha_factura, dc.saldo
									  from detalle_cuenta_corriente dc 
									  inner join tipo_documento t on dc.tipodocumento = t.id
									  inner join cuenta_corriente c on dc.idctacte = c.id
									  where  dc.saldo > 0 " . $sqlCuentaCorriente . " and t.id = 12	");
		}else{ //FACTURAS POR COBRAR
			$query = $this->db->query("select dc.id, concat(t.descripcion,' ',dc.numdocumento) as nombre, date_format(fc.fecha_factura,'%d/%m/%Y') as fecha_factura, dc.saldo
									  from detalle_cuenta_corriente dc 
									  inner join tipo_documento t on dc.tipodocumento = t.id
									  inner join cuenta_corriente c on dc.idctacte = c.id
									  left join factura_clientes fc on dc.numdocumento = fc.num_factura		
									  where  dc.saldo > 0 " . $sqlCuentaCorriente . " and t.id <> 12");
		}

		$data = array();
		if(count($query->result()) > 0){
			foreach ($query->result() as $row)
			{
				$row->documento = $row->nombre." | ".$row->fecha_factura." | $ ". number_format($row->saldo,0,".",".");
				$data[] = $row;
			}
		}else{
			$data[] = array('id' => '','nombre' => '','saldo' => 0, 'documento' => 0);
		}
        $resp['success'] = true;
        //$resp['total'] = $countAll;
        $resp['data'] = $data;

        echo json_encode($resp);
	}	



	public function getDocumentosByCtacte(){
		
		$idcuentacorriente = $this->input->post('idcuentacorriente');

		$sqlCuentaCorriente = $idcuentacorriente != '' ? " and c.id = '" . $idcuentacorriente . "'": "";
		$resp = array();

		$query = $this->db->query("select dc.id, concat(t.descripcion,' ',dc.numdocumento) as nombre, date_format(fc.fecha_factura,'%d/%m/%Y') as fecha_factura, dc.saldo
								  from detalle_cuenta_corriente dc 
								  inner join tipo_documento t on dc.tipodocumento = t.id
								  inner join cuenta_corriente c on dc.idctacte = c.id
								  left join factura_clientes fc on dc.numdocumento = fc.num_factura AND dc.tipodocumento = fc.tipo_documento								  
								  where  dc.saldo > 0 " . $sqlCuentaCorriente);


		$data = array();
		if(count($query->result()) > 0){
			foreach ($query->result() as $row)
			{
				$row->documento = $row->nombre." | ".$row->fecha_factura." | $ ". number_format($row->saldo,0,".",".");
				$data[] = $row;
			}
		}else{
			$data[] = array('id' => '','nombre' => '','saldo' => 0, 'documento' => 0);
		}

        $resp['success'] = true;
        //$resp['total'] = $countAll;
        $resp['data'] = $data;

        echo json_encode($resp);
	}	


	public function saveCancelacion(){

		$resp = array();
		$items = json_decode($this->input->post('items'));

		$arrayFecha = explode("T",$this->input->post('fecha'));

		$ctacteId = $this->input->post('ctacteId');
		$fecha = $arrayFecha[0];


		// guarda movimiento cuenta corriente
		$data = array(
	      	'numcomprobante' => $this->input->post('numero'),
	        'tipo' => $this->input->post('tipoComprobante'),
	        'proceso' => $this->input->post('origen'),
	        'glosa' => $this->input->post('detalle'),
	        'fecha' => date("Y-m-d H:i:s")
		);

		$this->db->insert('movimiento_cuenta_corriente', $data); 
		$idMovimiento = $this->db->insert_id();


		$arrayCuentasCorrientes = array();
		$i = 0;
		foreach($items as $item){
			if(($item->debe != 0 || $item->haber != 0) && $item->documento != 0){  // SIEMPRE QUEDA UNA FILA EN BLANCO
				$query = $this->db->query('SELECT c.id, c.saldo, dc.id as iddoctocta, dc.tipodocumento, dc.numdocumento  FROM cuenta_corriente c 
										  inner join detalle_cuenta_corriente dc on dc.idctacte = c.id 
										  where c.id = ' . $ctacteId . ' AND dc.id = ' . $item->documento . ' limit 1');
				foreach ($query->result() as $row)
					{
						$arrayCuentasCorrientes[$i]['id'] = $row->id;
						$arrayCuentasCorrientes[$i]['saldo'] = $row->saldo;
						$arrayCuentasCorrientes[$i]['tipodocumento'] = $row->tipodocumento;
						$arrayCuentasCorrientes[$i]['numdocumento'] = $row->numdocumento;
						$arrayCuentasCorrientes[$i]['iddoctocta'] = $row->iddoctocta;
					}				
				$i++;
			}

		}
		// VER QUE PASA CUANDO SE CANCELA MAS DE UNA CUENTA
		$i = 0;
		foreach($items as $item){
			if(($item->debe != 0 || $item->haber != 0) ){  // SIEMPRE QUEDA UNA FILA EN BLANCO
				$valor = $item->debe != 0 ? $item->debe : $item->haber;
				$side = $item->debe != 0 ? "debe" : "haber";
				if(isset($item->fecvenc)){					
					$arrayFecvencimiento = explode("T",$item->fecvenc);
					$fecvencimiento = $arrayFecvencimiento[0];				
				}else{
					$fecvencimiento = null;
				}

				if($item->documento == 0){
					$tipoMovimiento = 'CUADRATURA';
 					//$fecvencimiento = null;
 					$cuentaContableMov = $item->cuenta; 

				}else{
					$tipoMovimiento = 'CTACTE';
					$cuentaContableMov = $arrayCuentasCorrientes[$i]['id'];
					$fecvencimiento = null;
					$data = array(
				      	'idctacte' => $arrayCuentasCorrientes[$i]['id'],
				        'idcuenta' => $item->cuenta,
				        'idmovimiento' => $idMovimiento,
				        'tipodocumento' => $arrayCuentasCorrientes[$i]['tipodocumento'],
				        'numdocumento' => $arrayCuentasCorrientes[$i]['numdocumento'],
				        'fecvencimiento' => $fecvencimiento,
				        'glosa' => $item->glosa,		        
				        'valor' => $valor,
				        'origen' => 'CTACTE',
				        'fecha' => $fecha
					);

					$this->db->insert('cartola_cuenta_corriente', $data);
										
					// REBAJA SALDO
					if($arrayCuentasCorrientes[$i]['tipodocumento'] == 1 || $arrayCuentasCorrientes[$i]['tipodocumento'] == 2 || $arrayCuentasCorrientes[$i]['tipodocumento'] == 19 || $arrayCuentasCorrientes[$i]['tipodocumento'] == 101 || $arrayCuentasCorrientes[$i]['tipodocumento'] == 103){  // FACTURA, BOLETA, FACTURA EXENTA, FACTURA ELECTRONICA, FACTURA EXENTA ELECTRONICA
						if($side == "debe"){
							$query = $this->db->query("UPDATE cuenta_corriente SET saldo = saldo + " . $valor . " where id = " .  $arrayCuentasCorrientes[$i]['id'] );
							$query = $this->db->query("UPDATE detalle_cuenta_corriente SET saldo = saldo + " . $valor . " where id = " .  $arrayCuentasCorrientes[$i]['iddoctocta'] );
						}else{// SI ES FACTURA AL HABER SE REBAJA
							$query = $this->db->query("UPDATE cuenta_corriente SET saldo = saldo - " . $valor . " where id = " .  $arrayCuentasCorrientes[$i]['id'] );
							$query = $this->db->query("UPDATE detalle_cuenta_corriente SET saldo = saldo - " . $valor . " where id = " .  $arrayCuentasCorrientes[$i]['iddoctocta'] );
						}

					}else if($arrayCuentasCorrientes[$i]['tipodocumento'] == 16){ // SI ES NOTA DE DEBITO, SÓLO SE ABONA A LA FACTURA
						// ESTO ES UN TRUCO, SOLO PARA QUE AL REBAJAR POR FACTURA, EL MOVIMIENTO QUEDE EN CERO
						$query = $this->db->query("UPDATE cuenta_corriente SET saldo = saldo - " . $valor . " where id = " .  $arrayCuentasCorrientes[$i]['id'] );
						$query = $this->db->query("UPDATE detalle_cuenta_corriente SET saldo = saldo - " . $valor . " where id = " .  $arrayCuentasCorrientes[$i]['iddoctocta'] );
					}else if($arrayCuentasCorrientes[$i]['tipodocumento'] == 11 || $arrayCuentasCorrientes[$i]['tipodocumento'] == 102){ // SI ES NOTA DE CREDITO O NOTA DE CRÉDITO ELECTRÓNICA SÓLO SE REBAJA LA FACTURA
						// ESTO ES UN TRUCO, SOLO PARA QUE AL REBAJAR POR FACTURA, EL MOVIMIENTO QUEDE EN CERO
						$query = $this->db->query("UPDATE cuenta_corriente SET saldo = saldo + " . $valor . " where id = " .  $arrayCuentasCorrientes[$i]['id'] );

						$query = $this->db->query("UPDATE detalle_cuenta_corriente SET saldo = saldo - " . $valor . " where id = " .  $arrayCuentasCorrientes[$i]['iddoctocta'] );
					}
				}

					$data = array(
				      	'idmovimiento' => $idMovimiento,
				        'tipo' => $tipoMovimiento,
				        'idctacte' => $arrayCuentasCorrientes[$i]['id'],
				        'idcuenta' => $item->cuenta,
				        'tipodocumento' => $arrayCuentasCorrientes[$i]['tipodocumento'],
				        'numdocumento' => $arrayCuentasCorrientes[$i]['numdocumento'],		
				        'docpago' => $item->docpago,		        
				        'glosa' => $item->glosa,		        
				        'fecvencimiento' => $fecvencimiento,		        
				        'debe' => $item->debe,
				        'haber' => $item->haber,
					);

					$this->db->insert('detalle_mov_cuenta_corriente', $data); 				

			}
			$i++;
		}

        $resp['success'] = true;
        echo json_encode($resp);

	}		

	public function saveCuentaCorriente(){

		$resp = array();
		$items = json_decode($this->input->post('items'));
		//print_r($items); exit;
		$arrayFecha = explode("T",$this->input->post('fecha'));
		$fecha = $arrayFecha[0];


		// guarda movimiento cuenta corriente
		$data = array(
	      	'numcomprobante' => $this->input->post('numero'),
	        'tipo' => $this->input->post('tipoComprobante'),
	        'proceso' => $this->input->post('origen'),
	        'glosa' => $this->input->post('detalle'),
	        'fecha' => date("Y-m-d H:i:s")
		);

		$this->db->insert('movimiento_cuenta_corriente', $data); 
		$idMovimiento = $this->db->insert_id();


		$arrayCuentasCorrientes = array();
		$i = 0;


		$idctacte_cancela = 0;

		foreach($items as $item){
			if(($item->debe != 0 || $item->haber != 0) && $item->cliente != 0){  // SIEMPRE QUEDA UNA FILA EN BLANCO
				$query = $this->db->query('SELECT c.id, c.saldo, dc.id as iddoctocta, dc.tipodocumento, dc.numdocumento  FROM cuenta_corriente c 
										  inner join detalle_cuenta_corriente dc on dc.idctacte = c.id 
										  where c.id = ' . $item->cliente . ' AND dc.id = ' . $item->documento . ' limit 1');
				foreach ($query->result() as $row)
					{
						$arrayCuentasCorrientes[$i]['id'] = $row->id;
						$arrayCuentasCorrientes[$i]['saldo'] = $row->saldo;
						$arrayCuentasCorrientes[$i]['tipodocumento'] = $row->tipodocumento;
						$arrayCuentasCorrientes[$i]['numdocumento'] = $row->numdocumento;
						$arrayCuentasCorrientes[$i]['iddoctocta'] = $row->iddoctocta;
						$idctacte_cancela = $row->id; // REVISAR QUE PASA AL CANCELAR MÁS DE UNA CUENTA
					}				
				$i++;
			}

		}
		// VER QUE PASA CUANDO SE CANCELA MAS DE UNA CUENTA
		$i = 0;
		foreach($items as $item){
			if(($item->debe != 0 || $item->haber != 0) ){  // SIEMPRE QUEDA UNA FILA EN BLANCO
				$valor = $item->debe != 0 ? $item->debe : $item->haber;
				$side = $item->debe != 0 ? "debe" : "haber";
				if(isset($item->fecvenc)){					
					$arrayFecvencimiento = explode("T",$item->fecvenc);
					$fecvencimiento = $arrayFecvencimiento[0];				
				}else{
					$fecvencimiento = null;
				}
				$idcliente = null;
				if($item->documento == 0){
					$tipoMovimiento = 'CUADRATURA';
 					//$fecvencimiento = null;
 					$cuentaContableMov = $item->cuenta;
					$query_cliente = $this->db->query('SELECT c.id, c.idcliente FROM cuenta_corriente c 
											  where c.id = ' . $item->cliente . ' limit 1');
					$datos_cliente = $query_cliente->row();

 					$idcliente =  $datos_cliente->idcliente;

 					if($item->cuenta == 18){ // SI ES CHEQUE POR COBRAR, DEBE AGREGAR EL DOCUMENTO AL DETALLE DE CUENTA CORRIENTE
 						$arrayCuentasCorrientes[$i]['tipodocumento'] = 12;
 						$arrayCuentasCorrientes[$i]['numdocumento'] = $item->docpago;
						$query = $this->db->query("UPDATE cuenta_corriente SET saldo = saldo + " . $valor . " where id = " .  $idctacte_cancela );
						$data = array(
					      	'idctacte' => $idctacte_cancela,
					        'tipodocumento' => 12,
					        'numdocumento' => $item->docpago,
					        'saldoinicial' => $valor,
					        'saldo' => $valor,
					        'fechavencimiento' => $fecvencimiento,
					        'fecha' => $fecha
						);			
						$this->db->insert('detalle_cuenta_corriente', $data);			


						$data = array(
					      	'idctacte' => $idctacte_cancela,
					        'idcuenta' => $item->cuenta,
					        'idmovimiento' => $idMovimiento,
					        'tipodocumento' => 12,
					        'numdocumento' => $item->docpago,
					        'fecvencimiento' => $fecvencimiento,
					        'glosa' => 'Registro de Cheque por Cobrar en Cuenta Corriente',
					        'valor' => $valor,
					        'origen' => 'CTACTE',
					        'fecha' => $fecha
						);

						$this->db->insert('cartola_cuenta_corriente', $data);						
 					}



				}else{
					$tipoMovimiento = 'CTACTE';
					$cuentaContableMov = $arrayCuentasCorrientes[$i]['id'];
					$fecvencimiento = null;
					$data = array(
				      	'idctacte' => $arrayCuentasCorrientes[$i]['id'],
				        'idcuenta' => $item->cuenta,
				        'idmovimiento' => $idMovimiento,
				        'tipodocumento' => $arrayCuentasCorrientes[$i]['tipodocumento'],
				        'numdocumento' => $arrayCuentasCorrientes[$i]['numdocumento'],
				        'fecvencimiento' => $fecvencimiento,
				        'glosa' => $item->glosa,		        
				        'valor' => $valor,
				        'origen' => 'CTACTE',
				        'fecha' => $fecha
					);

					$this->db->insert('cartola_cuenta_corriente', $data);
										
					// REBAJA SALDO
					if($arrayCuentasCorrientes[$i]['tipodocumento'] == 1  || $arrayCuentasCorrientes[$i]['tipodocumento'] == 2 || $arrayCuentasCorrientes[$i]['tipodocumento'] == 19 || $arrayCuentasCorrientes[$i]['tipodocumento'] == 101 || $arrayCuentasCorrientes[$i]['tipodocumento'] == 103 || $arrayCuentasCorrientes[$i]['tipodocumento'] == 12){ // SI ES FACTURA, FACTURA EXENTA,FACTURA ELECTRONICA, FACTURA EXENTA ELECTRONICA O CHEQUE POR COBRAR SE REBAJA

						if($side == "debe"){
						$query = $this->db->query("UPDATE cuenta_corriente SET saldo = saldo + " . $valor . " where id = " .  $arrayCuentasCorrientes[$i]['id'] );
							$query = $this->db->query("UPDATE detalle_cuenta_corriente SET saldo = saldo + " . $valor . " where id = " .  $arrayCuentasCorrientes[$i]['iddoctocta'] );
						}else{// SI ES FACTURA AL HABER SE REBAJA
							$query = $this->db->query("UPDATE cuenta_corriente SET saldo = saldo - " . $valor . " where id = " .  $arrayCuentasCorrientes[$i]['id'] );
							$query = $this->db->query("UPDATE detalle_cuenta_corriente SET saldo = saldo - " . $valor . " where id = " .  $arrayCuentasCorrientes[$i]['iddoctocta'] );
						}

					}else if($arrayCuentasCorrientes[$i]['tipodocumento'] == 16){ // SI ES NOTA DE DEBITO, SÓLO SE ABONA A LA FACTURA
						// ESTO ES UN TRUCO, SOLO PARA QUE AL REBAJAR POR FACTURA, EL MOVIMIENTO QUEDE EN CERO
						$query = $this->db->query("UPDATE cuenta_corriente SET saldo = saldo - " . $valor . " where id = " .  $arrayCuentasCorrientes[$i]['id'] );
						$query = $this->db->query("UPDATE detalle_cuenta_corriente SET saldo = saldo - " . $valor . " where id = " .  $arrayCuentasCorrientes[$i]['iddoctocta'] );
					}else if($arrayCuentasCorrientes[$i]['tipodocumento'] == 11 || $arrayCuentasCorrientes[$i]['tipodocumento'] == 102){ // SI ES NOTA DE CREDITO, SÓLO SE REBAJA LA FACTURA
						// ESTO ES UN TRUCO, SOLO PARA QUE AL REBAJAR POR FACTURA, EL MOVIMIENTO QUEDE EN CERO
						$query = $this->db->query("UPDATE cuenta_corriente SET saldo = saldo + " . $valor . " where id = " .  $arrayCuentasCorrientes[$i]['id'] );						
						
						$query = $this->db->query("UPDATE detalle_cuenta_corriente SET saldo = saldo - " . $valor . " where id = " .  $arrayCuentasCorrientes[$i]['iddoctocta'] );
					}
				}

					$data = array(
				      	'idmovimiento' => $idMovimiento,
				        'tipo' => $tipoMovimiento,
				        'idctacte' => $arrayCuentasCorrientes[$i]['id'],
				        'idcuenta' => $item->cuenta,
				        'tipodocumento' => $arrayCuentasCorrientes[$i]['tipodocumento'],
				        'numdocumento' => $arrayCuentasCorrientes[$i]['numdocumento'],		
				        'idcliente' => $idcliente,	
				        'docpago' => $item->docpago,	
				        'glosa' => $item->glosa,		        
				        'fecvencimiento' => $fecvencimiento,		        
				        'debe' => $item->debe,
				        'haber' => $item->haber,
					);

					$this->db->insert('detalle_mov_cuenta_corriente', $data); 				

			}
			$i++;
		}

        $resp['success'] = true;
        echo json_encode($resp);

	}		

	public function cuentascancela(){
		
		$resp = array();
        $start = $this->input->post('start');
        $limit = $this->input->post('limit');
        $nombres = $this->input->post('nombre');

		$query = $this->db->query('SELECT * FROM cuenta_contable WHERE cancelaabono = 1 or cancelacargo = 1');

		$data = array();
		foreach ($query->result() as $row)
		{
			$data[] = $row;
		}
        $resp['success'] = true;
        $resp['data'] = $data;

        echo json_encode($resp);
	}	



	public function cuentasabono(){
		
		$resp = array();
        $start = $this->input->post('start');
        $limit = $this->input->post('limit');
        $nombres = $this->input->post('nombre');

		$query = $this->db->query('SELECT * FROM cuenta_contable WHERE cancelaabono = 1');

		$data = array();
		foreach ($query->result() as $row)
		{
			$data[] = $row;
		}
        $resp['success'] = true;
        $resp['data'] = $data;

        echo json_encode($resp);
	}	


	public function getTotalCartola(){
		$idcuentacorriente = $this->input->get('idcuentacorriente');

		$sqlCuentaCorriente = $idcuentacorriente != '' && $idcuentacorriente != 0 ? " c.idctacte = '" . $idcuentacorriente . "' ": "";
		// si son cancelaciones, el valor es negativo
		$query = $this->db->query("select 
									(select COALESCE(sum(if(dm.debe is not null,dm.debe,if((c.origen='VENTA' and c.tipodocumento in (1,2,19,101,103,16,104)) or (c.origen = 'CTACTE' and c.tipodocumento not in (1,2,19,101,103,16,104)),c.valor,0))),0) as valor 
																	  from cartola_cuenta_corriente c 
																	  left join detalle_mov_cuenta_corriente dm on c.idmovimiento = dm.idmovimiento and c.idcuenta = dm.idcuenta and c.tipodocumento = dm.tipodocumento and c.numdocumento = dm.numdocumento
																	where ". $sqlCuentaCorriente . " )
																	
									as debe,
									(select COALESCE(SUM(if(dm.haber is not null,dm.haber,if((c.origen='CTACTE' and c.tipodocumento in (1,2,19,101,103,16,104)) or (c.origen = 'VENTA' and c.tipodocumento not in (1,2,19,101,103,16,104)),c.valor,0))),0) as valor
																	  from cartola_cuenta_corriente c 
																	  left join detalle_mov_cuenta_corriente dm on c.idmovimiento = dm.idmovimiento and c.idcuenta = dm.idcuenta and c.tipodocumento = dm.tipodocumento and c.numdocumento = dm.numdocumento
																	where ". $sqlCuentaCorriente . " )
																	
									as haber, (select c.saldo from cuenta_corriente c where c.id = '" . $idcuentacorriente . "') as saldo 
									");

        $resp['success'] = true;
        //$resp['total'] = $countAll;
        $resp['data'] = $query->row();

        echo json_encode($resp);
	}	




	public function getCartola(){
		$idcuentacorriente = $this->input->get('idcuentacorriente');

		$sqlCuentaCorriente = $idcuentacorriente != '' && $idcuentacorriente != 0 ? " where c.idctacte = '" . $idcuentacorriente . "'": "";
		$resp = array();
		// si son cancelaciones, el valor es negativo
		$query = $this->db->query("select concat(tc.descripcion,' ',c.numdocumento) as origen, concat(tc2.descripcion,' ',c.numdocumento_asoc) as referencia, if(dm.debe is not null,dm.debe,if((c.origen='VENTA' and c.tipodocumento in (1,2,19,101,103,16,104)) or (c.origen = 'CTACTE' and c.tipodocumento not in (1,2,19,101,103,16,104)),c.valor,0)) as debe, if(dm.haber is not null,dm.haber,if((c.origen='CTACTE' and c.tipodocumento in (1,2,19,101,103,16,104)) or (c.origen = 'VENTA' and c.tipodocumento not in (1,2,19,101,103,16,104)),c.valor,0)) as haber, c.glosa, DATE_FORMAT(c.fecvencimiento,'%d/%m/%Y') as fecvencimiento, DATE_FORMAT(c.fecha,'%d/%m/%Y') as fecha, concat(m.tipo,' ',m.numcomprobante) as comprobante, m.id as idcomprobante
								  from cartola_cuenta_corriente c 
								  inner join tipo_documento tc on c.tipodocumento = tc.id
								  left join tipo_documento tc2 on c.tipodocumento_asoc = tc2.id
								  left join movimiento_cuenta_corriente m on c.idmovimiento = m.id
								  left join detalle_mov_cuenta_corriente dm on c.idmovimiento = dm.idmovimiento and c.idcuenta = dm.idcuenta and c.tipodocumento = dm.tipodocumento and c.numdocumento = dm.numdocumento
									". $sqlCuentaCorriente . " order by c.tipodocumento, c.numdocumento, c.created_at");

		$data = array();
		if(count($query->result()) > 0){
			foreach ($query->result() as $row)
			{
				$data[] = $row;
			}
		}else{
			$data[] = array('tipodocumento' => '','numdocumento' => '','debe' => 0,'haber' => 0);
		}
        $resp['success'] = true;
        //$resp['total'] = $countAll;
        $resp['data'] = $data;

        echo json_encode($resp);
	}	


	public function getComprobante(){
		$idcuentacorriente = $this->input->get('idcuentacorriente');

		$sqlCuentaCorriente = $idcuentacorriente != '' && $idcuentacorriente != 0 ? " where c.id = '" . $idcuentacorriente . "'": "";
		$resp = array();

		$query = $this->db->query("select m.id as idcomprobante, m.numcomprobante, m.tipo, m.proceso, m.glosa, DATE_FORMAT(m.fecha,'%d/%m/%Y') as fecha
								  from movimiento_cuenta_corriente m
								  inner join detalle_mov_cuenta_corriente dm on m.id = dm.idmovimiento
								  inner join cuenta_corriente c on dm.idctacte = c.id  
									". $sqlCuentaCorriente . "
									group by m.id
									order by numcomprobante");

		$data = array();
		if(count($query->result()) > 0){
			foreach ($query->result() as $row)
			{
				$data[] = $row;
			}
		}else{
			$data[] = array('idcomprobante' => '','numcomprobante' => '','tipo' => '','proceso' => '', 'glosa' => '', 'fecha' => '');
		}
        $resp['success'] = true;
        //$resp['total'] = $countAll;
        $resp['data'] = $data;

        echo json_encode($resp);
	}	



	public function getMovimientos(){

        $arrayfecdesde = $this->input->post('fecdesde');
        $arrayfechasta = $this->input->post('fechasta');

        $fecdesde = explode("T",$arrayfecdesde);
        $fechasta = explode("T",$arrayfechasta);


        $fecdesde = $fecdesde[0];
        $fechasta = $fechasta[0];

		$resp = array();

		$query = $this->db->query("SELECT cuentacontable, sum(cancelaciones) as cancelaciones, sum(depositos) as depositos, sum(otrosingresos) as otrosingresos, sum(cargos) as cargos, sum(abonos) as abonos from
									(select cc.id, cc.nombre as cuentacontable, if(mcc.proceso='CANCELACION',if(debe=0,haber,debe),0) as cancelaciones, if(mcc.proceso='DEPOSITO',if(debe=0,haber,debe),0) as depositos, if(mcc.proceso='OTRO',if(debe=0,haber,debe),0) as otrosingresos, haber as cargos, debe as abonos from detalle_mov_cuenta_corriente dm 
									inner join cuenta_contable cc on dm.idcuenta = cc.id 
									inner join movimiento_cuenta_corriente mcc on dm.idmovimiento = mcc.id
									where left(mcc.fecha,10) between '$fecdesde' and '$fechasta') as tmp
									group by id");

		$data = array();
		if(count($query->result()) > 0){
			foreach ($query->result() as $row)
			{
				$data[] = $row;
			}
		}else{
			$data[] = array();
		}
        $resp['success'] = true;
        //$resp['total'] = $countAll;
        $resp['data'] = $data;

        echo json_encode($resp);
	}



	public function getComprobantes(){

		if($this->input->post('fecdesde') != ""){
	        $arrayfecdesde = $this->input->post('fecdesde');
	        $arrayfechasta = $this->input->post('fechasta');

	        $fecdesde = explode("T",$arrayfecdesde);
	        $fechasta = explode("T",$arrayfechasta);


	        $fecdesde = $fecdesde[0];
	        $fechasta = $fechasta[0];
        }else{
        	$fecdesde = date("Y-m-d");
        	$fechasta = date("Y-m-d");

        }
		$resp = array();

		$query = $this->db->query("select id, concat(proceso,': ',numcomprobante) as numcomprobante from movimiento_cuenta_corriente 
									where left(fecha,10) between '" . $fecdesde . "' and '" . $fechasta . "'
									order by proceso, fecha");

		$data = array();	
		if(count($query->result()) > 0){
			foreach ($query->result() as $row)
			{
				$data[] = $row;
			}
		}else{
			$data[] = array();
		}
        $resp['success'] = true;
        //$resp['total'] = $countAll;
        $resp['data'] = $data;

        echo json_encode($resp);
	}


	public function getLibroDiarioByTipoComprobante(){


        $comprobante = $this->input->post('comprobante');
        $arrayfecdesde = $this->input->post('fecdesde');
        $arrayfechasta = $this->input->post('fechasta');

        $fecdesde = explode("T",$arrayfecdesde);
        $fechasta = explode("T",$arrayfechasta);


        $fecdesde = $fecdesde[0];
        $fechasta = $fechasta[0];        


        $sql_comprobantes = $comprobante == 'TODOS' ? "" : "and m.proceso = '" . $comprobante . "'";
		$resp = array();


		$query = $this->db->query("select if(m.proceso = 'OTRO','OTROS INGRESOS',m.proceso) as tipocomprobante, m.numcomprobante as nrocomprobante, left(m.fecha,10) as fecha, cc.nombre as cuentacontable, c.rut as rut, concat(t.descripcion,' ',dm.numdocumento) as documento, DATE_FORMAT(dm.fecvencimiento,'%d/%m/%Y') as fechavencimiento, haber as cargos, debe as abonos from movimiento_cuenta_corriente m
									inner join detalle_mov_cuenta_corriente dm on m.id = dm.idmovimiento 
									inner join cuenta_contable cc on dm.idcuenta = cc.id 
									left join tipo_documento t on dm.tipodocumento = t.id
					                left join cuenta_corriente cco on dm.idctacte = cco.id
					                left join clientes c on cco.idcliente = c.id									
									where left(m.fecha,10) between '" . $fecdesde . "' and '" . $fechasta . "' " . $sql_comprobantes 
									. " order by m.proceso, m.numcomprobante, m.fecha asc, dm.tipo");

		$data = array();
		if(count($query->result()) > 0){
			foreach ($query->result() as $row)
			{

				$row->rut = empty($row->rut) ? "" : number_format(substr($row->rut, 0, strlen($row->rut) - 1),0,".",".")."-".substr($row->rut,-1); //formatea rut
				$data[] = $row;
			}
		}else{
			$data[] = array();
		}
        $resp['success'] = true;
        //$resp['total'] = $countAll;
        $resp['data'] = $data;

        echo json_encode($resp);
	}



        public function exportarResMovPDF(){
            
            
            $columnas = json_decode($this->input->get('cols'));

            $fecdesde = $this->input->get('fecdesde');
            $fechasta = $this->input->get('fechasta');

            $fecdesde = substr($fecdesde,6,4)."-".substr($fecdesde,0,2)."-".substr($fecdesde,3,2);
            $fechasta = substr($fechasta,6,4)."-".substr($fechasta,0,2)."-".substr($fechasta,3,2);
            $this->load->database();

            
            $query = $this->db->query("SELECT cuentacontable, sum(cancelaciones) as cancelaciones, sum(depositos) as depositos, sum(otrosingresos) as otrosingresos, sum(cargos) as cargos, sum(abonos) as abonos from
                          (select cc.id, cc.nombre as cuentacontable, if(mcc.proceso='CANCELACION',if(debe=0,haber,debe),0) as cancelaciones, if(mcc.proceso='DEPOSITO',if(debe=0,haber,debe),0) as depositos, if(mcc.proceso='OTRO',if(debe=0,haber,debe),0) as otrosingresos, haber as cargos, debe as abonos from detalle_mov_cuenta_corriente dm 
                          inner join cuenta_contable cc on dm.idcuenta = cc.id 
                          inner join movimiento_cuenta_corriente mcc on dm.idmovimiento = mcc.id
                          where left(mcc.fecha,10) between '$fecdesde' and '$fechasta') as tmp
                          group by id");

            $datas = $query->result_array();
            
			$dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
			$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

           $header = '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Untitled Document</title>
		<style type="text/css">
		td {
			font-size: 16px;
		}
		p {
		}
		</style>
		</head>

		<body>
		<table width="987px" height="602" border="0">
		  <tr>
		    <td width="197px"><img src="http://localhost/Infosys_web/Infosys_web/resources/images/logoinfo&sys.jpg" width="150" height="136" /></td>
		    <td width="493px" style="font-size: 14px;text-align:center;vertical-align:text-top"	>
		    <p>GOTRU ALIMENTOS SPA.</p>
		    <p>RUT:78.549.450-4</p>
		    <p>8 ORIENTE, TALCA</p>
		    <p>Fonos: </p>
		    <p>http://www.gotru.cl</p>
		    </td>
		    <td width="296px" style="font-size: 16px;text-align:left;vertical-align:text-top"	>
		          <p>FECHA EMISION : '.date('d/m/Y').'</p>
			</td>
		  </tr>';


		  $header2 = '<tr>
			<td style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:center;" colspan="3"><h2>RESUMEN DE MOVIMIENTOS</h2></td>
		  </tr>
		  <tr ><td colspan="3">&nbsp;</td></tr>
			';

		$body_header = '<tr>
		    <td colspan="3" >
		    	<table width="987px" cellspacing="0" cellpadding="0" >
		      <tr>
		        <td width="250px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;" >Cuenta Contable</td>
		        <td width="147px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" >Cancelaciones</td>
		        <td width="147px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" >Depositos</td>
		        <td width="147px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" >Otros Ingresos</td>
		        <td width="147px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" >Cargos</td>
		        <td width="147px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" >Abonos</td>
		      </tr>';

		$cargos = 0;
        $abonos = 0;

      foreach($datas as $data){
			$body_detail .= '<tr>
			<td style="text-align:left">'.$data['cuentacontable'].'</td>			
			<td align="right">$ '.number_format($data['cancelaciones'], 0, ',', '.').'</td>
			<td align="right">$ '.number_format($data['depositos'], 0, ',', '.').'</td>
			<td align="right">$ '.number_format($data['otrosingresos'], 0, ',', '.').'</td>
			<td align="right">$ '.number_format($data['cargos'], 0, ',', '.').'</td>
			<td align="right">$ '.number_format($data['abonos'], 0, ',', '.').'</td>
			</tr>';

	          $cargos += $data['cargos'];
	          $abonos += $data['abonos'];
         }        	      


$footer .= '<tr><td colspan="6">&nbsp;</td></tr></table></td>
		  </tr>
		  <tr>
		  	<td colspan="3" >
		    	<table width="987px" cellspacing="0" cellpadding="0" >
		      <tr>
		        <td width="693px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;" ><b>Totales</b></td>
		        <td width="147px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" ><b>$ '.number_format($cargos, 0, ',', '.').'</b></td>
		        <td width="147px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" ><b>$ '.number_format($abonos, 0, ',', '.').'</b></td>
		      </tr>
		      	</table>
		  	</td>
		  </tr></table>
		</body>
		</html>';

			$html = $header.$header2.$body_header.$body_detail.$footer;
              						
        

			$this->load->library("mpdf");
			//include(defined('BASEPATH')."/libraries/MPDF54/mpdf.php");
			//include(dirname(__FILE__)."/../libraries/MPDF54/mpdf.php");

			$this->mpdf->mPDF(
				'',    // mode - default ''
				'',    // format - A4, for example, default ''
				0,     // font size - default 0
				'',    // default font family
				15,    // margin_left
				15,    // margin right
				16,    // margin top
				16,    // margin bottom
				9,     // margin header
				9,     // margin footer
				'L'    // L - landscape, P - portrait
				);  

			$this->mpdf->WriteHTML($html);
			$this->mpdf->Output("ResumenMovimientos.pdf", "I");

			exit;                    
        }




        public function exportarLibroDiarioPDF(){
            
            $columnas = json_decode($this->input->get('cols'));
            $comprobante = $this->input->get('comprobante');
            $fecdesde = $this->input->get('fecdesde');
            $fechasta = $this->input->get('fechasta');

            $fecdesde = substr($fecdesde,6,4)."-".substr($fecdesde,0,2)."-".substr($fecdesde,3,2);
            $fechasta = substr($fechasta,6,4)."-".substr($fechasta,0,2)."-".substr($fechasta,3,2);    
            
            $sql_comprobantes = $comprobante == 'TODOS' ? "" : "and m.proceso = '" . $comprobante . "'";                 

            $this->load->database();

            $query = $this->db->query("select if(m.proceso = 'OTRO','OTROS INGRESOS',if(m.proceso = 'CANCELACION','CANCELACIONES',m.proceso)) as tipocomprobante, m.numcomprobante as nrocomprobante, left(m.fecha,10) as fecha, cc.nombre as cuentacontable, c.rut as rut, concat(t.descripcion,' ',dm.numdocumento) as documento, DATE_FORMAT(dm.fecvencimiento,'%d/%m/%Y') as fechavencimiento, haber as cargos, debe as abonos from movimiento_cuenta_corriente m
                  inner join detalle_mov_cuenta_corriente dm on m.id = dm.idmovimiento 
                  inner join cuenta_contable cc on dm.idcuenta = cc.id 
                  left join tipo_documento t on dm.tipodocumento = t.id
                  left join cuenta_corriente cco on dm.idctacte = cco.id
                  left join clientes c on cco.idcliente = c.id                  
                  where left(m.fecha,10) between '" . $fecdesde . "' and '" . $fechasta . "' " . $sql_comprobantes 
                  . " order by m.proceso, m.numcomprobante, m.fecha asc, dm.tipo");


            $datas = $query->result_array();

			$dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
			$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");


$header = '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Untitled Document</title>
		<style type="text/css">
		td {
			font-size: 16px;
		}
		p {
		}
		</style>
		</head>

		<body>
		<table width="987px" height="602" border="0">
		  <tr>
		    <td width="197px"><img src="http://localhost/Infosys_web/Infosys_web/resources/images/logoinfo&sys.jpg" width="150" height="136" /></td>
		    <td width="493px" style="font-size: 14px;text-align:center;vertical-align:text-top"	>
		    <p>GOTRU ALIMENTOS SPA.</p>
		    <p>RUT:78.549.450-4</p>
		    <p>8 ORIENTE, TALCA</p>
		    <p>Fonos: </p>
		    <p>http://www.gotru.cl</p>
		    </td>
		    <td width="296px" style="font-size: 16px;text-align:left;vertical-align:text-top"	>
		          <p>FECHA EMISION : '.date('d/m/Y').'</p>
			</td>
		  </tr>';

		  $header2 = '<tr>
			<td style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:center;" colspan="3"><h2>CARTOLA CUENTA CORRIENTE</h2></td>
		  </tr>
			<tr><td colspan="3">&nbsp;</td></tr>		  
			';

			  $body_data = "";
              $cargos = 0;
              $abonos = 0;
              $tipocomprobante = "";
              foreach($datas as $data){
                 if($data['tipocomprobante'] != $tipocomprobante){
                 	if($tipocomprobante != ""){

						$body_data .= '<tr><td colspan="8">&nbsp;</td></tr></table></td>
								  </tr>
								  <tr>
								  	<td colspan="3" >
								    	<table width="987px" cellspacing="0" cellpadding="0" >
								      <tr>
								        <td width="777px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;" ><b>Totales</b></td>
								        <td width="105px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" ><b>$ '.number_format($cargos, 0, ',', '.').'</b></td>
								        <td width="105px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" ><b>$ '.number_format($abonos, 0, ',', '.').'</b></td>
								      </tr>
								      	</table>
								  	</td>
								  </tr>
								  <tr><td colspan="3">&nbsp;</td></tr>';						                		

					    $cargos = 0;
	       			    $abonos = 0;
			         }


					$body_data .= '<tr>
					    <td colspan="3" >
					    	<table width="987px" cellspacing="0" cellpadding="0" >
					    <tr>
					    	<td colspan="8" style="border-top:1pt solid black;text-align:center;">' . $data['tipocomprobante'] . '</td>
					    </tr>
					      <tr>
					        <td width="105px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;" >Comprobante</td>
					        <td width="105px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;" >Fecha</td>
					        <td width="250px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;" >Cuenta Contable</td>
					        <td width="105px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;" >Rut</td>
					        <td width="105px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;" >Documento</td>
					        <td width="105px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;" >Fecha Vencimiento</td>
					        <td width="105px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" >Cargos</td>
					        <td width="105px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" >Abonos</td>
					      </tr>';

                 }   

				$body_data .= '<tr>
				<td style="text-align:left">'.$data['nrocomprobante'].'</td>			
				<td style="text-align:left">'.$data['fecha'].'</td>
				<td style="text-align:left">'.$data['cuentacontable'].'</td>
				<td style="text-align:left">'.$data['rut'].'</td>
				<td style="text-align:left">'.$data['documento'].'</td>
				<td style="text-align:left">'.$data['fechavencimiento'].'</td>
				<td align="right">$ '.number_format($data['cargos'], 0, '.', ',').'</td>
				<td align="right">$ '.number_format($data['abonos'], 0, '.', ',').'</td>
				</tr>';

	              $cargos += $data['cargos'];
	              $abonos += $data['abonos'];
	              $tipocomprobante = $data['tipocomprobante'];

                 }


			$body_data .= '<tr><td colspan="8">&nbsp;</td></tr></table></td>
					  </tr>
					  <tr>
					  	<td colspan="3" >
					    	<table width="987px" cellspacing="0" cellpadding="0" >
					      <tr>
					        <td width="777px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;" ><b>Totales</b></td>
					        <td width="105px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" ><b>$ '.number_format($cargos, 0, ',', '.').'</b></td>
					        <td width="105px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" ><b>$ '.number_format($abonos, 0, ',', '.').'</b></td>
					      </tr>
					      	</table>
					  	</td>
					  </tr></table>
					</body>
					</html>';	

			$html = $header.$header2.$body_data; 
			$this->load->library("mpdf");
			//include(defined('BASEPATH')."/libraries/MPDF54/mpdf.php");
			//include(dirname(__FILE__)."/../libraries/MPDF54/mpdf.php");

			$this->mpdf->mPDF(
				'',    // mode - default ''
				'',    // format - A4, for example, default ''
				8,     // font size - default 0
				'',    // default font family
				10,    // margin_left
				5,    // margin right
				16,    // margin top
				16,    // margin bottom
				9,     // margin header
				9,     // margin footer
				'L'    // L - landscape, P - portrait
				);  
			//echo $html; exit;
			$this->mpdf->WriteHTML($html);
			$this->mpdf->Output("LibroDiario.pdf", "I");

			exit;            
        }




	public function getCorrelativo(){


        $tipoCorrelativo = $this->input->post('tipoCorrelativo');


		$resp = array();

		$query = $this->db->query("select (correlativo + 1) as correlativo from correlativos where nombre = '" . $tipoCorrelativo . "'");

		$data = array();
		if(count($query->result()) > 0){
			$this->db->query("update correlativos set correlativo = correlativo + 1 where nombre = '" . $tipoCorrelativo . "'");			
			foreach ($query->result() as $row)
			{

				$data[] = $row;
			}
		}else{
			$data[] = array();
		}
        $resp['success'] = true;
        //$resp['total'] = $countAll;
        $resp['data'] = $data;

        echo json_encode($resp);
	}



	public function getSaldoDocumentos(){


        $rutcliente = $this->input->post('rutcliente');
        $nombrecliente = $this->input->post('nombrecliente');
        $cuentacontable = $this->input->post('cuentacontable');


        $sql_filtro = '';
        if($rutcliente != ''){
        	$sql_filtro .= "and c.rut = '" . $rutcliente . "'";

        }

        if($nombrecliente != ''){
        	$sql_filtro .= "and c.nombres like '%" . $nombrecliente . "%'";

        }if($cuentacontable != ''){

        	$sql_filtro .= "and cco.id = '" . $cuentacontable . "'";
        }



		$resp = array();

		$query = $this->db->query("SELECT cco.nombre as cuentacontable, dcc.numdocumento as documento, dcc.fecha, dcc.fechavencimiento, 
									if(datediff(curdate(),dcc.fechavencimiento)<=0,dcc.saldo,0) as saldoporvencer,
									if(datediff(curdate(),dcc.fechavencimiento)>0,dcc.saldo,0)  as saldovencido,
									if(datediff(curdate(),dcc.fechavencimiento)>0,datediff(curdate(),dcc.fechavencimiento),0) as dias,
									dcc.saldo as saldodocto,
									c.rut,
									c.nombres as cliente
									FROM cuenta_corriente cc
									inner join detalle_cuenta_corriente dcc on dcc.idctacte = cc.id
									inner join clientes c on cc.idcliente = c.id
									inner join cuenta_contable cco on cc.idcuentacontable = cco.id
									where cc.saldo > 0 " . $sql_filtro . " and dcc.tipodocumento not in (11,102)
									order by c.nombres, cco.id, c.id, dcc.id"); // SE ELIMINAN NOTAS DE CREDITO

		$data = array();
		if(count($query->result()) > 0){
			foreach ($query->result() as $row)
			{
				$row->rut = number_format(substr($row->rut, 0, strlen($row->rut) - 1),0,".",".")."-".substr($row->rut,-1); //formatea rut
				$data[] = $row;
			}
		}else{
			$data[] = array();
		}
        $resp['success'] = true;
        //$resp['total'] = $countAll;
        $resp['data'] = $data;

        echo json_encode($resp);
	}



        public function exportarSaldoDocumentosPDF(){
            
            
            $columnas = json_decode($this->input->get('cols'));
            $rutcliente = $this->input->get('rutcliente');
            $nombrecliente = $this->input->get('nombrecliente');
            $cuentacontable = $this->input->get('cuentacontable');

            $sql_filtro = '';
            if($rutcliente != '' && $rutcliente != 'null'){
              $sql_filtro .= "and c.rut = '" . $rutcliente . "'";

            }

            if($nombrecliente != '' && $nombrecliente != 'null'){
              $sql_filtro .= "and c.nombres like '%" . $nombrecliente . "%'";

            }if($cuentacontable != '' && $cuentacontable != 'null'){
              $sql_filtro .= "and cco.id = '" . $cuentacontable . "'";
            }                

            $this->load->database();
            

            $query = $this->db->query("SELECT cco.nombre as cuentacontable, dcc.numdocumento as documento, dcc.fecha, dcc.fechavencimiento, 
                  if(datediff(curdate(),dcc.fechavencimiento)<=0,dcc.saldo,0) as saldoporvencer,
                  if(datediff(curdate(),dcc.fechavencimiento)>0,dcc.saldo,0)  as saldovencido,
                  if(datediff(curdate(),dcc.fechavencimiento)>0,datediff(curdate(),dcc.fechavencimiento),0) as dias,
                  dcc.saldo as saldodocto,
                  c.rut,
                  c.nombres as cliente
                  FROM cuenta_corriente cc
                  inner join detalle_cuenta_corriente dcc on dcc.idctacte = cc.id
                  inner join clientes c on cc.idcliente = c.id
                  inner join cuenta_contable cco on cc.idcuentacontable = cco.id
                  where cc.saldo > 0 " . $sql_filtro . "
                  order by cco.id, c.id, dcc.id");




            $datas = $query->result_array();

			$dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
			$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

$header = '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Untitled Document</title>
		<style type="text/css">
		td {
			font-size: 16px;
		}
		p {
		}
		</style>
		</head>

		<body>
		<table width="987px" height="602" border="0">
		  <tr>
		    <td width="197px"><img src="http://localhost/Infosys_web/Infosys_web/resources/images/logoinfo&sys.jpg" width="150" height="136" /></td>
		    <td width="493px" style="font-size: 14px;text-align:center;vertical-align:text-top"	>
		    <p>GOTRU ALIMENTOS SPA.</p>
		    <p>RUT:78.549.450-4</p>
		    <p>8 ORIENTE, TALCA</p>
		    <p>Fonos: </p>
		    <p>http://www.gotru.cl</p>
		    </td>
		    <td width="296px" style="font-size: 16px;text-align:left;vertical-align:text-top"	>
		          <p>FECHA EMISION : '.date('d/m/Y').'</p>
			</td>
		  </tr>';

		  $header2 = '<tr>
			<td style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:center;" colspan="3"><h2>SALDO DE DOCUMENTOS</h2></td>
		  </tr>
			<tr><td colspan="3">&nbsp;</td></tr>		  
			';

		$body_data = "";
      	$rutcliente = "";
        $saldoporvencer = 0;
		$saldovencido = 0;
		$saldodocto = 0;      	
		foreach($datas as $data){
			if($data['rut'] != $rutcliente){

                 	if($rutcliente != ""){


						$body_data .= '<tr><td colspan="8">&nbsp;</td></tr></table></td>
								  </tr>
								  <tr>
								  	<td colspan="3" >
								    	<table width="987px" cellspacing="0" cellpadding="0" >
								      <tr>
								        <td width="551px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;" ><b>Totales</b></td>
								        <td width="109px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" ><b>$ '.number_format($saldoporvencer, 0, ',', '.').'</b></td>
								        <td width="109px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" ><b>$ '.number_format($saldovencido, 0, ',', '.').'</b></td>
								        <td width="109px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" ><b>-</b></td>
								        <td width="109px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" ><b>$ '.number_format($saldodocto, 0, ',', '.').'</b></td>
								      </tr>
								      	</table>
								  	</td>
								  </tr>
								  <tr><td colspan="3">&nbsp;</td></tr>';					                   
				        $saldoporvencer = 0;
              			$saldovencido = 0;
              			$saldodocto = 0;


						//$html .=  "</table></td></tr><br><br><br>";                 		
			         }				

					$body_data .= '<tr>
					    <td colspan="3" >
					    	<table width="987px" cellspacing="0" cellpadding="0" >
					    <tr>
					    	<td colspan="4" style="border-top:1pt solid black;text-align:center;">Rut: ' . number_format( substr ($data['rut'], 0 , -1 ) , 0, "", ".") . '-' . substr ( $data['rut'], strlen( $data['rut']) -1 , 1 ) . '</td>
					    	<td colspan="4" style="border-top:1pt solid black;text-align:center;">Cliente: ' . $data['cliente'] . '</td>
					    </tr>
					      <tr>
					        <td width="250px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;" >Cuenta Contable</td>
					        <td width="100px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;" >Documento</td>
					        <td width="100px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;" >Fecha</td>
					        <td width="100px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;" >Fecha Vencimiento</td>
					        <td width="109px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" >Saldo por Vencer</td>
					        <td width="109px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" >Saldo Vencido</td>
					        <td width="109px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" >Dias Morosidad</td>
					        <td width="109px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" >Saldo Documento</td>
					      </tr>';

             // $saldoporvencer = 0;
             // $saldovencido = 0;
             // $saldodocto = 0;
          }


				$body_data .= '<tr>
				<td style="text-align:left">'.$data['cuentacontable'].'</td>			
				<td style="text-align:left">'.$data['documento'].'</td>
				<td style="text-align:left">'.$data['fecha'].'</td>
				<td style="text-align:left">'.$data['fechavencimiento'].'</td>
				<td align="right">$ '.number_format($data['saldoporvencer'], 0, '.', ',').'</td>
				<td align="right">$ '.number_format($data['saldovencido'], 0, '.', ',').'</td>
				<td align="right">'.$data['dias'].'</td>
				<td align="right">$ '.number_format($data['saldodocto'], 0, '.', ',').'</td>
				</tr>';


            $saldoporvencer += $data['saldoporvencer'];
            $saldovencido += $data['saldovencido'];
            $saldodocto += $data['saldodocto'];
            $rutcliente = $data['rut'];
         }                 

			$body_data .= '<tr><td colspan="8">&nbsp;</td></tr></table></td>
					  </tr>
					  <tr>
					  	<td colspan="3" >
					    	<table width="987px" cellspacing="0" cellpadding="0" >
					      <tr>
					        <td width="551px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;" ><b>Totales</b></td>
					        <td width="109px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" ><b>$ '.number_format($saldoporvencer, 0, ',', '.').'</b></td>
					        <td width="109px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" ><b>$ '.number_format($saldovencido, 0, ',', '.').'</b></td>
					        <td width="109px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" ><b>-</b></td>
					        <td width="109px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" ><b>$ '.number_format($saldodocto, 0, ',', '.').'</b></td>
					      </tr>
					      	</table>
					  	</td>
					  </tr></table>
					</body>
					</html>';	


			$html = $header.$header2.$body_data; 
			$this->load->library("mpdf");
			//include(defined('BASEPATH')."/libraries/MPDF54/mpdf.php");
			//include(dirname(__FILE__)."/../libraries/MPDF54/mpdf.php");

			$this->mpdf->mPDF(
				'',    // mode - default ''
				'',    // format - A4, for example, default ''
				8,     // font size - default 0
				'',    // default font family
				10,    // margin_left
				5,    // margin right
				16,    // margin top
				16,    // margin bottom
				9,     // margin header
				9,     // margin footer
				'L'    // L - landscape, P - portrait
				);  
			//echo $html; exit;
			$this->mpdf->WriteHTML($html);
			$this->mpdf->Output("SaldoDocumentos.pdf", "I");

			exit;            
        }



        public function mailSaldoDocumentosPDF(){
            


            $email = $this->input->post('email');
            $rutcliente = $this->input->post('rutcliente');
            $nombrecliente = $this->input->post('nombrecliente');
            $cuentacontable = $this->input->post('cuentacontable');
            $mensaje = $this->input->post('mensaje') != '' ? $this->input->post('mensaje') : "Envio de reporte de saldo de documentos";
            

            $sql_filtro = '';
            if($rutcliente != '' && $rutcliente != 'null'){
              $sql_filtro .= "and c.rut = '" . $rutcliente . "'";

            }

            if($nombrecliente != '' && $nombrecliente != 'null'){
              $sql_filtro .= "and c.nombres like '%" . $nombrecliente . "%'";

            }if($cuentacontable != '' && $cuentacontable != 'null'){
              $sql_filtro .= "and cco.id = '" . $cuentacontable . "'";
            }                

            $this->load->database();
            

            $query = $this->db->query("SELECT cco.nombre as cuentacontable, dcc.numdocumento as documento, dcc.fecha, dcc.fechavencimiento, 
                  if(datediff(curdate(),dcc.fechavencimiento)<=0,dcc.saldo,0) as saldoporvencer,
                  if(datediff(curdate(),dcc.fechavencimiento)>0,dcc.saldo,0)  as saldovencido,
                  if(datediff(curdate(),dcc.fechavencimiento)>0,datediff(curdate(),dcc.fechavencimiento),0) as dias,
                  dcc.saldo as saldodocto,
                  c.rut,
                  c.nombres as cliente
                  FROM cuenta_corriente cc
                  inner join detalle_cuenta_corriente dcc on dcc.idctacte = cc.id
                  inner join clientes c on cc.idcliente = c.id
                  inner join cuenta_contable cco on cc.idcuentacontable = cco.id
                  where cc.saldo > 0 " . $sql_filtro . "
                  order by cco.id, c.id, dcc.id");




            $datas = $query->result_array();

			$dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
			$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

$header = '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Untitled Document</title>
		<style type="text/css">
		td {
			font-size: 16px;
		}
		p {
		}
		</style>
		</head>

		<body>
		<table width="987px" height="602" border="0">
		  <tr>
		    <td width="197px"><img src="http://localhost/Infosys_web/Infosys_web/resources/images/logoinfo&sys.jpg" width="150" height="136" /></td>
		    <td width="493px" style="font-size: 14px;text-align:center;vertical-align:text-top"	>
		    <p>GOTRU ALIMENTOS SPA.</p>
		    <p>RUT:78.549.450-4</p>
		    <p>8 ORIENTE, TALCA</p>
		    <p>Fonos: </p>
		    <p>http://www.gotru.cl</p>
		    </td>
		    <td width="296px" style="font-size: 16px;text-align:left;vertical-align:text-top"	>
		          <p>FECHA EMISION : '.date('d/m/Y').'</p>
			</td>
		  </tr>';

		  $header2 = '<tr>
			<td style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:center;" colspan="3"><h2>SALDO DE DOCUMENTOS</h2></td>
		  </tr>
			<tr><td colspan="3">&nbsp;</td></tr>		  
			';

		$body_data = "";
      	$rutcliente = "";
        $saldoporvencer = 0;
		$saldovencido = 0;
		$saldodocto = 0;      	
		foreach($datas as $data){
			if($data['rut'] != $rutcliente){

                 	if($rutcliente != ""){


						$body_data .= '<tr><td colspan="8">&nbsp;</td></tr></table></td>
								  </tr>
								  <tr>
								  	<td colspan="3" >
								    	<table width="987px" cellspacing="0" cellpadding="0" >
								      <tr>
								        <td width="551px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;" ><b>Totales</b></td>
								        <td width="109px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" ><b>$ '.number_format($saldoporvencer, 0, ',', '.').'</b></td>
								        <td width="109px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" ><b>$ '.number_format($saldovencido, 0, ',', '.').'</b></td>
								        <td width="109px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" ><b>-</b></td>
								        <td width="109px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" ><b>$ '.number_format($saldodocto, 0, ',', '.').'</b></td>
								      </tr>
								      	</table>
								  	</td>
								  </tr>
								  <tr><td colspan="3">&nbsp;</td></tr>';					                   
				        $saldoporvencer = 0;
              			$saldovencido = 0;
              			$saldodocto = 0;


						//$html .=  "</table></td></tr><br><br><br>";                 		
			         }				

					$body_data .= '<tr>
					    <td colspan="3" >
					    	<table width="987px" cellspacing="0" cellpadding="0" >
					    <tr>
					    	<td colspan="4" style="border-top:1pt solid black;text-align:center;">Rut: ' . number_format( substr ($data['rut'], 0 , -1 ) , 0, "", ".") . '-' . substr ( $data['rut'], strlen( $data['rut']) -1 , 1 ) . '</td>
					    	<td colspan="4" style="border-top:1pt solid black;text-align:center;">Cliente: ' . $data['cliente'] . '</td>
					    </tr>
					      <tr>
					        <td width="250px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;" >Cuenta Contable</td>
					        <td width="100px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;" >Documento</td>
					        <td width="100px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;" >Fecha</td>
					        <td width="100px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;" >Fecha Vencimiento</td>
					        <td width="109px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" >Saldo por Vencer</td>
					        <td width="109px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" >Saldo Vencido</td>
					        <td width="109px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" >Dias Morosidad</td>
					        <td width="109px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" >Saldo Documento</td>
					      </tr>';

             // $saldoporvencer = 0;
             // $saldovencido = 0;
             // $saldodocto = 0;
          }


				$body_data .= '<tr>
				<td style="text-align:left">'.$data['cuentacontable'].'</td>			
				<td style="text-align:left">'.$data['documento'].'</td>
				<td style="text-align:left">'.$data['fecha'].'</td>
				<td style="text-align:left">'.$data['fechavencimiento'].'</td>
				<td align="right">$ '.number_format($data['saldoporvencer'], 0, '.', ',').'</td>
				<td align="right">$ '.number_format($data['saldovencido'], 0, '.', ',').'</td>
				<td align="right">'.$data['dias'].'</td>
				<td align="right">$ '.number_format($data['saldodocto'], 0, '.', ',').'</td>
				</tr>';


            $saldoporvencer += $data['saldoporvencer'];
            $saldovencido += $data['saldovencido'];
            $saldodocto += $data['saldodocto'];
            $rutcliente = $data['rut'];
         }                 

			$body_data .= '<tr><td colspan="8">&nbsp;</td></tr></table></td>
					  </tr>
					  <tr>
					  	<td colspan="3" >
					    	<table width="987px" cellspacing="0" cellpadding="0" >
					      <tr>
					        <td width="551px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;" ><b>Totales</b></td>
					        <td width="109px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" ><b>$ '.number_format($saldoporvencer, 0, ',', '.').'</b></td>
					        <td width="109px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" ><b>$ '.number_format($saldovencido, 0, ',', '.').'</b></td>
					        <td width="109px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" ><b>-</b></td>
					        <td width="109px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" ><b>$ '.number_format($saldodocto, 0, ',', '.').'</b></td>
					      </tr>
					      	</table>
					  	</td>
					  </tr></table>
					</body>
					</html>';	


			$html = $header.$header2.$body_data; 
			$this->load->library("mpdf");
			//include(defined('BASEPATH')."/libraries/MPDF54/mpdf.php");
			//include(dirname(__FILE__)."/../libraries/MPDF54/mpdf.php");

			$this->mpdf->mPDF(
				'',    // mode - default ''
				'',    // format - A4, for example, default ''
				8,     // font size - default 0
				'',    // default font family
				10,    // margin_left
				5,    // margin right
				16,    // margin top
				16,    // margin bottom
				9,     // margin header
				9,     // margin footer
				'L'    // L - landscape, P - portrait
				);  
			//echo $html; exit;
			$this->mpdf->WriteHTML($html);
			$file = date("YmdHis").".pdf";
			$this->mpdf->Output('./tmp/'.$file, 'F');

			$this->load->model('facturaelectronica');
			$email_data = $this->facturaelectronica->get_email();

			if(count($email_data) > 0){
				$this->load->library('email');
				$config['protocol']    = $email_data->tserver_intercambio;
				$config['smtp_host']    = $email_data->host_intercambio;
				$config['smtp_port']    = $email_data->port_intercambio;
				$config['smtp_timeout'] = '7';
				$config['smtp_user']    = $email_data->email_intercambio;
				$config['smtp_pass']    = $email_data->pass_intercambio;
				$config['charset']    = 'utf-8';
				$config['newline']    = "\r\n";
				$config['mailtype'] = 'html'; // or html
				$config['validation'] = TRUE; // bool whether to validate email or not      			

				$this->email->initialize($config);		  		

			    $this->email->from($email_data->email_intercambio, NOMBRE_EMPRESA);
			    $this->email->to($email);

			    //$this->email->bcc(array('rodrigo.gonzalez@info-sys.cl','cesar.moraga@info-sys.cl','sergio.arriagada@info-sys.cl','rene.gonzalez@info-sys.cl')); 
			    $this->email->subject('Envio de Saldo de Documentos');
			    $this->email->message($mensaje);

			    $this->email->attach('./tmp/'.$file,'attachment', 'SaldoDocumentos.pdf');			


			    try {
			      $this->email->send();
			      //var_dump($this->email->print_debugger());
			      unlink('./tmp/'.$file);
			      	        exit;
			    } catch (Exception $e) {
			      echo $e->getMessage() . '<br />';
			      echo $e->getCode() . '<br />';
			      echo $e->getFile() . '<br />';
			      echo $e->getTraceAsString() . '<br />';
			      echo "no";

			    }
		    }
			exit;  

        }

        public function verComprobantePDF(){
            
            $idmovimiento = $this->input->get('idmov');

            $this->load->database();
            
            $queryencabezado = $this->db->query("select numcomprobante, tipo, proceso, glosa, DATE_FORMAT(fecha,'%d/%m/%Y') as fecha from movimiento_cuenta_corriente where id = " . $idmovimiento . " limit 1");


            $datas_encabezado = $queryencabezado->row_array();

            if($datas_encabezado['proceso'] == "OTRO"){
            	$tipo_proceso = "OTROS INGRESO";
            }else if($datas_encabezado['proceso'] == "CANCELACION"){
            	$tipo_proceso = "CANCELACIONES";
            }else if ($datas_encabezado['proceso'] == "DEPOSITO") {
            	$tipo_proceso = "";
            }else{
            	$tipo_proceso = "";
            }

            $querydetalle = $this->db->query("select cco.nombre as nombrecuenta, c.nombres as nombrecliente, concat(td.descripcion,' ',dm.numdocumento) as documento, dm.glosa,  DATE_FORMAT(dm.fecvencimiento,'%d/%m/%Y') as fecvencimiento, dm.debe, dm.haber 
            								from detalle_mov_cuenta_corriente dm 
            								left join cuenta_corriente cc on dm.idctacte = cc.id
            								left join clientes c on cc.idcliente = c.id
            								inner join cuenta_contable cco on dm.idcuenta = cco.id
            								left join tipo_documento td on dm.tipodocumento = td.id
            								where dm.idmovimiento = " . $idmovimiento );
            $datas_detalle = $querydetalle->result_array();

            //print_r($datas_encabezado); exit;
			//$dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
			//$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

            $header = '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Untitled Document</title>
		<style type="text/css">
		td {
			font-size: 16px;
		}
		p {
		}
		</style>
		</head>

		<body>
		<table width="987px" height="602" border="0">
		  <tr>
		    <td width="197px"><img src="http://localhost/Infosys_web/Infosys_web/resources/images/logoinfo&sys.jpg" width="150" height="136" /></td>
		    <td width="493px" style="font-size: 14px;text-align:center;vertical-align:text-top"	>
		    <p>GOTRU ALIMENTOS SPA.</p>
		    <p>RUT:78.549.450-4</p>
		    <p>8 ORIENTE, TALCA</p>
		    <p>Fonos: </p>
		    <p>http://www.gotru.cl</p>
		    </td>
		    <td width="296px" style="font-size: 16px;text-align:left;vertical-align:text-top"	>
		          <p>COMPROBANTE N°: '.$datas_encabezado['numcomprobante'].'</p>
		          <!--p>&nbsp;</p-->
		          <p>FECHA EMISION : '.date('d/m/Y').'</p>
			</td>
		  </tr>';


		  $header2 = '<tr>
			<td style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:center;" colspan="3"><h2>COMPROBANTE DE MOVIMIENTO '.$tipo_proceso . '</h2></td>
		  </tr>
		  <tr>
		    <td colspan="3" width="987px" >
		    	<table width="987px" border="0">
		    		<tr>
		    			<td width="197px">Tipo Comprobante:</td>
		    			<td width="395px">'.$datas_encabezado['tipo'].'</td>
		    		</tr>
		    		<tr>
		    			<td width="197px">Fecha Comprobante:</td>
		    			<td width="395px">'.$datas_encabezado['fecha'].'</td>
		    		</tr>		    		
		    		<tr>
		    			<td width="197px">Glosa:</td>
		    			<td width="395px">'.$datas_encabezado['glosa'].'</td>
		    		</tr>		    				    		
		    	</table>
			</td>
		  </tr>';

$body_header = '<tr>
		    <td colspan="3" >
		    	<table width="987px" cellspacing="0" cellpadding="0" >
		      <tr>
		        <td width="126px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;" >Cuenta Contable</td>
		        <td width="250px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;" >Cliente</td>
		        <td width="100px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" >Documento</td>
		        <td width="250px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:center;" >Glosa</td>
		        <td width="100px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" >Fecha Vencimiento</td>
		        <td width="80px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" >Debe</td>
		        <td width="80px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" >Haber</td>
		      </tr>';



              $debe = 0;
              $haber = 0;
              $i = 0;
              $body_detail = '';
      foreach($datas_detalle as $detalle){

			$body_detail .= '<tr>
			<td style="text-align:left;font-size: 14px;">'.$detalle['nombrecuenta'].'</td>			
			<td style="text-align:left;font-size: 14px;">'.$detalle['nombrecliente'].'</td>
			<td style="text-align:right;font-size: 14px;">'.$detalle['documento'].'</td>
			<td style="text-align:center;font-size: 14px;">'.$detalle['glosa'].'</td>
			<td style="text-align:right;font-size: 14px;">'.$detalle['fecvencimiento'].'</td>
			<td align="right" style="font-size: 14px;">$ '.number_format($detalle['debe'], 0, '.', ',').'</td>
			<td align="right" style="font-size: 14px;">$ '.number_format($detalle['haber'], 0, '.', ',').'</td>
			</tr>';
			
            $debe += $detalle['debe'];
            $haber += $detalle['haber'];
            $i++;
         }       

         //$body_detail .= '</table><td></tr></table></body></html>';
		// RELLENA ESPACIO
		while($i < 30){
			$spaces .= '<tr><td colspan="7">&nbsp;</td></tr>';
			$i++;
		}     

		$footer .= '<tr><td colspan="7">&nbsp;</td></tr></table></td>
		  </tr>
		  <tr>
		  	<td colspan="3" >
		    	<table width="987px" cellspacing="0" cellpadding="0" >
		      <tr>
		        <td width="827px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;font-size: 14px;" ><b>Totales</b></td>
		        <td width="80px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;font-size: 14px;" ><b>$ '.number_format($debe, 0, ',', '.').'</b></td>
		        <td width="80px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;font-size: 14px;" ><b>$ '.number_format($haber, 0, ',', '.').'</b></td>
		      </tr>
		      	</table>
		  	</td>
		  </tr></table>
		</body>
		</html>';


	   /* $html .=  "<tr>";
	      $html .=  '<td bgcolor="#002221" style="color: #FFF" scope="col" colspan="5"><b>TOTALES</b></td>';
	      $html .=  '<td bgcolor="#002221" style="color: #FFF;text-align: right;" scope="col" ><b>'.number_format($debe, 0, ',', '.').'</b></td>';
	      $html .=  '<td bgcolor="#002221" style="color: #FFF;text-align: right;" scope="col"><b>'.number_format($haber, 0, ',', '.').'</b></td>';
	      $html .=  '</tr>';
	    $html .= '</table></td>';
        $html .= "</tr></table>";
*/

        $html = $header.$header2.$body_header.$body_detail.$footer;
        //echo $html; exit;
        //$html = $header.$header2.$body_header.$body_detail.$spaces;
			$this->load->library("mpdf");
			//include(defined('BASEPATH')."/libraries/MPDF54/mpdf.php");
			//include(dirname(__FILE__)."/../libraries/MPDF54/mpdf.php");

			$this->mpdf->mPDF(
				'',    // mode - default ''
				'',    // format - A4, for example, default ''
				8,     // font size - default 0
				'',    // default font family
				10,    // margin_left
				5,    // margin right
				16,    // margin top
				16,    // margin bottom
				9,     // margin header
				9,     // margin footer
				'L'    // L - landscape, P - portrait
				);  
			//echo $html; exit;
			$this->mpdf->WriteHTML($html);
			$this->mpdf->Output("Comprobante".$datas_encabezado['numcomprobante']."pdf", "I");

			exit;            
        }





        public function exportarCartolaPDF(){
            
            
            $idctacte = $this->input->get('idctacte');
            $sqlCuentaCorriente = $idctacte != '' && $idctacte != 0 ? " where c.idctacte = '" . $idctacte . "'": "";
            $sqlCliente = $idctacte != '' && $idctacte != 0 ? " where c.id = '" . $idctacte . "'": "";

            $this->load->database();

            $query = $this->db->query("select concat(tc.descripcion,' ',c.numdocumento) as origen, concat(tc2.descripcion,' ',c.numdocumento_asoc) as referencia, if(dm.debe is not null,dm.debe,if((c.origen='VENTA' and c.tipodocumento in (1,2,19,101,103,16,104)) or (c.origen = 'CTACTE' and c.tipodocumento not in (1,2,19,101,103,16,104)),c.valor,0)) as debe, if(dm.haber is not null,dm.haber,if((c.origen='CTACTE' and c.tipodocumento in (1,2,19,101,103,16,104)) or (c.origen = 'VENTA' and c.tipodocumento not in (1,2,19,101,103,16,104)),c.valor,0)) as haber, c.glosa, DATE_FORMAT(c.fecvencimiento,'%d/%m/%Y') as fecvencimiento, DATE_FORMAT(c.fecha,'%d/%m/%Y') as fecha, concat(m.tipo,' ',m.numcomprobante) as comprobante, m.id as idcomprobante
                          from cartola_cuenta_corriente c 
                          inner join tipo_documento tc on c.tipodocumento = tc.id
                          left join tipo_documento tc2 on c.tipodocumento_asoc = tc2.id
                          left join movimiento_cuenta_corriente m on c.idmovimiento = m.id
                          left join detalle_mov_cuenta_corriente dm on c.idmovimiento = dm.idmovimiento and c.idcuenta = dm.idcuenta and c.tipodocumento = dm.tipodocumento and c.numdocumento = dm.numdocumento
                          ". $sqlCuentaCorriente . " order by c.tipodocumento, c.numdocumento, c.created_at");            


            $datas = $query->result_array();


            $query_cliente = $this->db->query("select cl.rut, cl.nombres
                          from clientes cl 
                          inner join cuenta_corriente c on cl.id = c.idcliente
                          ". $sqlCliente );            
            $data_cliente = $query_cliente->row_array();

			$dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
			$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

           $header = '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Untitled Document</title>
		<style type="text/css">
		td {
			font-size: 16px;
		}
		p {
		}
		</style>
		</head>

		<body>
		<table width="987px" height="602" border="0">
		  <tr>
		   <td width="197px"><img src="http://localhost/Infosys_web/Infosys_web/resources/images/logoinfo&sys.jpg" width="150" height="136" /></td>
		    <td width="493px" style="font-size: 14px;text-align:center;vertical-align:text-top"	>
		    <p>GOTRU ALIMENTOS SPA.</p>
		    <p>RUT:78.549.450-4</p>
		    <p>8 ORIENTE, TALCA</p>
		    <p>Fonos: </p>
		    <p>http://www.gotru.cl</p>
		    </td>
		    <td width="296px" style="font-size: 16px;text-align:left;vertical-align:text-top"	>
		          <p>FECHA EMISION : '.date('d/m/Y').'</p>
			</td>
		  </tr>';

		  $header2 = '<tr>
			<td style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:center;" colspan="3"><h2>CARTOLA CUENTA CORRIENTE</h2></td>
		  </tr>
 			<tr>
		    <td colspan="3" width="987px" >
		    	<table width="987px" border="0">
		    		<tr>
		    			<td width="197px">Rut:</td>
		    			<td width="395px">'.number_format( substr ($data_cliente['rut'], 0 , -1 ) , 0, "", ".") . '-' . substr ( $data_cliente['rut'], strlen( $data_cliente['rut']) -1 , 1 ).'</td>
		    		</tr>
		    		<tr>
		    			<td width="197px">Nombre Cliente:</td>
		    			<td width="395px">'.$data_cliente['nombres'].'</td>
		    		</tr>		    		
		    	</table>
			</td>
		  </tr>		  
			';


        $total_debe = 0;
		$total_haber = 0;

		$body_header = '<tr>
		    <td colspan="3" >
		    	<table width="987px" cellspacing="0" cellpadding="0" >
		      <tr>
		        <td width="160px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;" >Origen</td>
		        <td width="90px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;" >Referencia</td>
		        <td width="90px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;" >Comprobante</td>
		        <td width="287px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;" >Glosa</td>
		        <td width="100px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;" >Vencimiento</td>
		        <td width="100px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;" >Fecha</td>
		        <td width="75px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" >Debe</td>
		        <td width="75px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" >Haber</td>
		      </tr>';


		foreach($datas as $data){
			$body_detail .= '<tr>
			<td style="text-align:left;font-size: 14px;">'.$data['origen'].'</td>			
			<td style="text-align:left;font-size: 14px;">'.$data['referencia'].'</td>
			<td style="text-align:left;font-size: 14px;">'.$data['comprobante'].'</td>
			<td style="text-align:left;font-size: 14px;">'.$data['glosa'].'</td>
			<td style="text-align:left;font-size: 14px;">'.$data['fecvencimiento'].'</td>
			<td style="text-align:left;font-size: 14px;">'.$data['fecha'].'</td>
			<td align="right" style="font-size: 14px;" >$ '.number_format($data['debe'], 0, '.', ',').'</td>
			<td align="right" style="font-size: 14px;">$ '.number_format($data['haber'], 0, '.', ',').'</td>
			</tr>';


              $total_debe += $data['debe'];
              $total_haber += $data['haber'];
         }                 


$footer .= '<tr><td colspan="8">&nbsp;</td></tr></table></td>
		  </tr>
		  <tr>
		  	<td colspan="3" >
		    	<table width="987px" cellspacing="0" cellpadding="0" >
		      <tr>
		        <td width="837px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;font-size: 14px;" ><b>Totales</b></td>
		        <td width="75px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;font-size: 14px;" ><b>$ '.number_format($total_debe, 0, ',', '.').'</b></td>
		        <td width="75px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;font-size: 14px;" ><b>$ '.number_format($total_haber, 0, ',', '.').'</b></td>
		      </tr>
		      	</table>
		  	</td>
		  </tr></table>
		</body>
		</html>';



			$html = $header.$header2.$body_header.$body_detail.$footer;
			$this->load->library("mpdf");
			//include(defined('BASEPATH')."/libraries/MPDF54/mpdf.php");
			//include(dirname(__FILE__)."/../libraries/MPDF54/mpdf.php");

			$this->mpdf->mPDF(
				'',    // mode - default ''
				'',    // format - A4, for example, default ''
				8,     // font size - default 0
				'',    // default font family
				10,    // margin_left
				5,    // margin right
				16,    // margin top
				16,    // margin bottom
				9,     // margin header
				9,     // margin footer
				'L'    // L - landscape, P - portrait
				);  
			//echo $html; exit;
			$this->mpdf->WriteHTML($html);
			$this->mpdf->Output("Cartola.pdf", "I");

			exit;            
        }

}
