<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Comisiones extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}

	
	public function getAll(){
		$resp = array();
        $start = $this->input->post('start');
        $limit = $this->input->post('limit');
        $nombres = $this->input->post('nombre');

		$countAll = $this->db->count_all_results("vendedores");
		$tipo=1;
		$tipo2=2;

		if($nombres){
			$sql_nombre = "";
	        $arrayNombre =  explode(" ",$nombres);

	        foreach ($arrayNombre as $nombre) {
	        	$sql_nombre .= "and nombre like '%".$nombre."%' ";
	        }
	        
			$query = $this->db->query('SELECT * FROM vendedores	WHERE estado in ( '.$tipo.','.$tipo2.') ' . $sql_nombre . '');

			$total = 0;

		  	foreach ($query->result() as $row)		    
			{
				$total = $total +1;			
			}

			$countAll = $total;
			
		}else{
			
			$query = $this->db->query('SELECT * FROM vendedores	WHERE estado = 1');
			
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
		}
        $resp['success'] = true;
        $resp['total'] = $countAll;
        $resp['data'] = $data;

        echo json_encode($resp);
	}
	
	public function exportarExcelcomision(){

          header("Content-type: application/vnd.ms-excel"); 
          
          	$columnas = json_decode($this->input->get('cols'));
            $fecha = $this->input->get('fecha');
            $id = $this->input->get('id');
            list($dia, $mes, $anio) = explode("/",$fecha);
            $fecha3 = $anio ."-". $mes ."-". $dia;
            $fecha2 = $this->input->get('fecha2');
            list($dia, $mes, $anio) = explode("/",$fecha2);
            $fecha4 = $anio ."-". $mes ."-". $dia;
            $tipo=1;
            $tipo2=2;

          if($id){

	            $query2 = $this->db->query('SELECT * FROM vendedores WHERE id ="'.$id.'"');

				foreach ($query2->result() as $row)
				{
					$nombre = $row->nombre;
					$rut = $row->rut;
					$comision = $row->comision;
						
				}
            }

            header("Content-disposition: attachment; filename=comisiones.xls"); 
      
 			if($fecha){            
                          
                $data = array();
                $query = $this->db->query('SELECT acc.*, c.nombres as nombre_cliente, c.rut as rut_cliente, v.nombre as nom_vendedor  FROM factura_clientes acc
                left join clientes c on (acc.id_cliente = c.id)
                left join vendedores v on (acc.id_vendedor = v.id)
                WHERE acc.tipo_documento in ( '.$tipo.','.$tipo2.') and acc.id_vendedor = "'.$id.'" and acc.fecha_factura between "'.$fecha3.'"  AND "'.$fecha4.'"
                order by acc.tipo_documento');
            };            

            $users = $query->result_array();
                                   
            echo '<table>';
            echo "<td></td>";
            echo "<td>DETALLE COMISION VENDEDOR</td>";
            echo "<tr>";
            echo "<td>VENDEDOR : ".$nombre."</td>";
            echo "<td></td>";
            echo "<td>RUT : ".$rut."</td>";
            echo "</tr>";                  
            echo "<tr>";
            echo "<td>FECHA : ".$fecha2."</td>";
            echo "</tr>";                  
            echo "<tr>";
            echo "<td>FACTURA</td>";
            echo "<td>FECHA</td>";
            echo "<td>VENCIMIENTO</td>";
            echo "<td>RUT</td>";
            echo "<td>NOMBRE</td>";
            echo "<td>NETO</td>";
            echo "<td>COMISION</td>";
              
            foreach($users as $v){

              echo "<tr>";
              echo "<td>".$v['num_factura']."</td>";
              echo "<td>".$v['fecha_factura']."</td>";
              echo "<td>".$v['fecha_venc']."</td>";
              echo "<td>".$v['rut_cliente']."</td>";
              echo "<td>".$v['nombre_cliente']."</td>";
              echo "<td>".$v['sub_total']."</td>";
              $comisiona = (round(($v['sub_total'] * $comision)/100));
              echo "<td>".$comisiona."</td>";
            }
        
              echo '</table>';
         
          
        }
     
	
        public function exportarPdf()
         {            
            $columnas = json_decode($this->input->get('cols'));
            $fecha = $this->input->get('fecha');
            $id = $this->input->get('id');
            list($dia, $mes, $anio) = explode("/",$fecha);
            $fecha3 = $anio ."-". $mes ."-". $dia;
            $fecha2 = $this->input->get('fecha2');
            list($dia, $mes, $anio) = explode("/",$fecha2);
            $fecha4 = $anio ."-". $mes ."-". $dia;
            $tipo=1;
            $tipo2=2;
           
            $this->load->database();

            if($id){

	            $query2 = $this->db->query('SELECT * FROM vendedores WHERE id ="'.$id.'"');

				foreach ($query2->result() as $row)
				{
					$nombre = $row->nombre;
					$rut = $row->rut;
					$comision = $row->comision;
						
				}
            }
            
            if($fecha){            
                          
                $data = array();
                $query = $this->db->query('SELECT acc.*, c.nombres as nombre_cliente, c.rut as rut_cliente, v.nombre as nom_vendedor  FROM factura_clientes acc
                left join clientes c on (acc.id_cliente = c.id)
                left join vendedores v on (acc.id_vendedor = v.id)
                WHERE acc.tipo_documento in ( '.$tipo.','.$tipo2.') and acc.id_vendedor = "'.$id.'" and acc.fecha_factura between "'.$fecha3.'"  AND "'.$fecha4.'"
                order by acc.tipo_documento');
            };


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
			<tr>
			<td style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:center;" colspan="3"><h4>'.$nombre.' </h2></td>
		  </tr>
		  <tr>
			<td style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:center;" colspan="3"><h4>'.$rut.' </h2></td>
		  </tr>
		  </tr>';              
              
		  $header2 = '<tr>
			<td style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:center;" colspan="3"><h2>COMISIONES</h2></td>
			</tr>		 
		  </tr>
			<tr><td colspan="3">&nbsp;</td></tr>
			';              


		$body_header = '<tr>
		    <td colspan="3" >
		    	<table width="987px" cellspacing="0" cellpadding="0" >
		      <tr>
		        <td width="67"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;" >Numero</td>
		        <td width="90px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;" >Fecha</td>
		        <td width="90px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" >Vencimiento</td>
		        <td width="90px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:center;" >Rut</td>
		        <td width="250px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;" >Nombre</td>
		        <td width="80px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" >Neto</td>
		        <td width="80px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;" >Comision</td>
		      </tr>';


		      $sub_total = 0;
		      $comisiona = 0;
		      $neto = 0;
		      $iva = 0;
		      $totalfactura = 0;
              $i = 0;
              $body_detail = '';
              $users = $query->result_array();
		      foreach($users as $v){

		      	    $comisionm = (($v['sub_total'] * $comision) / 100);

					$body_detail .= '<tr>
					<td style="text-align:left;font-size: 14px;">'.$v['num_factura'].'</td>	
					<td style="text-align:left;font-size: 14px;">'.$v['fecha_factura'].'</td>
					<td style="text-align:right;font-size: 14px;">'.$v['fecha_venc'].'</td>
					<td style="text-align:center;font-size: 14px;">'.$v['rut_cliente'].'</td>
					<td style="text-align:left;font-size: 14px;">'.$v['nombre_cliente'].'</td>
					<td align="right" style="font-size: 14px;">$ '.number_format($v['sub_total'], 0, '.', ',').'</td>
					<td align="right" style="font-size: 14px;">$ '.number_format($comisionm, 0, '.', ',').'</td>
					</tr>';
					
			      $sub_total += $v['sub_total'];
			      $comisiona += $comisionm;
			     
		            $i++;
		         }  

				$footer .= '<tr><td colspan="10">&nbsp;</td></tr></table></td>
				  </tr>
				  <tr>
				  	<td colspan="3" >
				    	<table width="987px" cellspacing="0" cellpadding="0" >
				      <tr>
				        <td width="587px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:left;font-size: 14px;" ><b>Totales</b></td>
				        <td width="80px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;font-size: 14px;" ><b>$ '.number_format($sub_total, 0, ',', '.').'</b></td>
				        <td width="80px"  style="border-bottom:1pt solid black;border-top:1pt solid black;text-align:right;font-size: 14px;" ><b>$ '.number_format($comisiona, 0, ',', '.').'</b></td>
				       
				      </tr>
				      	</table>
				  	</td>
				  </tr></table>
				</body>
				</html>';		              
             
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
			$this->mpdf->Output("LibroVentas.pdf", "I");

			exit;            

        }
}
