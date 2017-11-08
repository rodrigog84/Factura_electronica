<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Procesos extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('format');
		$this->load->database();
	}


	public function lectura_csv_fe(){

		 	$archivo = "./facturacion_electronica/csv/facturas.csv";
			$this->load->model('facturaelectronica');
			$codproceso = $this->facturaelectronica->guarda_csv($archivo);
			$this->facturaelectronica->crea_dte_csv($codproceso);


	}



	public function lectura_mail(){

		set_time_limit(4000); 
		 
		// Connect to gmail
		$this->load->model('facturaelectronica');
		$email_data = $this->facturaelectronica->get_email();
		if(count($email_data) > 0){



				$imapPath = '{imap.gmail.com:993/imap/ssl/novalidate-cert}INBOX';
				$username = $email_data->email_contacto;
				$password = $email_data->pass_contacto;
				 
				// try to connect 
				$inbox = imap_open($imapPath,$username,$password) or die('Cannot connect to Gmail: ' . imap_last_error());


			   // $emails = imap_search($inbox,'SUBJECT "Envio de DTEs"  SINCE "01-08-2017" UNSEEN' );
			    $date = date ( "j F Y", strToTime ( "-5 days" ) );
			   // echo $date; exit;
			     $emails = imap_search($inbox,'SUBJECT "Envio de DTEs" SINCE "' . $date . '" ' );
			     
				 
				$output = '';
				$array_dtes = array();
				$num_dtes = 0;

				 //	echo count($emails); exit;
				foreach($emails as $mail) {
				    
				    $headerInfo = imap_headerinfo($inbox,$mail);
				    //print_r($headerInfo);
				    $output .= $headerInfo->subject.'<br/>';
				    $output .= $headerInfo->toaddress.'<br/>';
				    $output .= $headerInfo->date.'<br/>';
				    $output .= $headerInfo->fromaddress.'<br/>';
				    $output .= $headerInfo->reply_toaddress.'<br/>';
				    
				    $emailStructure = imap_fetchstructure($inbox,$mail);
				    //print_r($emailStructure); 
					if (isset($emailStructure->parts) && count($emailStructure->parts)) {
						
						// loop through all attachments
							for ($i = 0; $i < count($emailStructure->parts); $i++) {

								// set up an empty attachment
								$attachments[$i] = array(
									'is_attachment' => FALSE,
									'filename'      => '',
									'name'          => '',
									'attachment'    => ''
								);


								if ($emailStructure->parts[$i]->ifdparameters) {
									foreach ($emailStructure->parts[$i]->dparameters as $object) {
										// if this attachment is a file, mark the attachment and filename
										if (strtolower($object->attribute) == 'filename') {
											$attachments[$i]['is_attachment'] = TRUE;
											$attachments[$i]['filename']      = $object->value;
										}
									}
								}


								// if this attachment has ifparameters, then proceed as above
								if ($emailStructure->parts[$i]->ifparameters) {
									foreach ($emailStructure->parts[$i]->parameters as $object) {
										if (strtolower($object->attribute) == 'name') {
											$attachments[$i]['is_attachment'] = TRUE;
											$attachments[$i]['name']          = $object->value;
										}
									}
								}


								// if we found a valid attachment for this 'part' of the email, process the attachment
								if ($attachments[$i]['is_attachment']) {
									// get the content of the attachment
									$attachments[$i]['attachment'] = imap_fetchbody($inbox, $mail, $i+1);

									// check if this is base64 encoding
									if ($emailStructure->parts[$i]->encoding == 3) { // 3 = BASE64
										$attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
									}
									// otherwise, check if this is "quoted-printable" format
									elseif ($emailStructure->parts[$i]->encoding == 4) { // 4 = QUOTED-PRINTABLE
										$attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
									}
								}


								//print_r($attachments);
								foreach ($attachments as $attachment) {
									if($attachment['is_attachment'] == 1 && substr($attachment['filename'],-3) == 'xml'){
										$array_dtes[$num_dtes]['filename'] = $attachment['filename'];
										$array_dtes[$num_dtes]['content'] = $attachment['attachment'];
										$num_dtes++;
									}
								}



								//	echo "<br><br>";

							}

					}

				}



				foreach ($array_dtes as $dte) {
					$codproceso = $this->facturaelectronica->dte_compra($dte);
				}		
				 
				// colse the connection
				imap_expunge($inbox);
				imap_close($inbox);

		}


	}



	public function get_contribuyentes(){

		set_time_limit(0);
		$this->load->model('facturaelectronica');
		$this->facturaelectronica->get_contribuyentes();
	}		




}









