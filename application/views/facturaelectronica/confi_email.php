<!--sub-heard-part-->
									  <div class="sub-heard-part">
									   <ol class="breadcrumb m-b-0">
											<li><a href="inicio.html">Inicio</a></li>
											<li class="active">Configuración de Email</li>
										</ol>
									   </div>
								  <!--//sub-heard-part-->
								 
									<div class="graph-visual tables-main">
										<form action="<?php echo base_url();?>facturaselectronicas/registro_email" method="POST" class="form-horizontal">	
										<div class="graph">
											<h3 class="inner-tittle two">Contacto SII</h3>
											<form class="form-horizontal">
											<div class="form-group">
												<label for="smallinput" class="col-sm-2 control-label label-input-sm">Email</label>
												<div class="col-sm-8">
													<input type="text" class="form-control1 input-sm" id="email_contacto" placeholder="Email" name="email_contacto" value="<?php echo $datosform['email_contacto'];?>">
												</div>
											</div>
	
											<div class="form-group">
												<label for="mediuminput" class="col-sm-2 control-label">Contraseña</label>
												<div class="col-sm-8">
													<input type="password" class="form-control1" id="pass_contacto" placeholder="Contraseña" name="pass_contacto" value="<?php echo $datosform['pass_contacto'];?>"> 
												</div>
											</div>
	
											<div class="form-group mb-n">
												<label for="largeinput" class="col-sm-2 control-label label-input-lg">Tipo Server</label>
												<div class="col-sm-8">
													<select name="tipoServer_contacto" id="tipoServer_contacto" class="form-control1">
														<option value="">Seleccione.</option>
														<option value="smtp" <?php echo $datosform['tserver_contacto'] == 'smtp' ? 'selected' : '';?>>SMTP</option>
														<option value="imap" <?php echo $datosform['tserver_contacto'] == 'imap' ? 'selected' : '';?>>IMAP</option>
													</select>
												</div>
											</div>

											<div class="form-group">
												<label for="mediuminput" class="col-sm-2 control-label">Puerto</label>
												<div class="col-sm-8">
													<input type="text" class="form-control1" id="port_contacto" name="port_contacto" placeholder="465" value="<?php echo $datosform['port_contacto'];?>">
												</div>
											</div>

											<div class="form-group">
												<label for="mediuminput" class="col-sm-2 control-label">Host</label>
												<div class="col-sm-8">
													<input type="text" class="form-control1" id="host_contacto" name="host_contacto" placeholder="ssl://smtp.gmail.com" value="<?php echo $datosform['host_contacto'];?>">
												</div>
											</div>
											<br>
											<h3 class="inner-tittle two">Email Intercambio</h3>
											<div class="form-group">
												<label for="smallinput" class="col-sm-2 control-label label-input-sm">Email</label>
												<div class="col-sm-8">
													<input type="text" class="form-control1 input-sm" id="email_intercambio" name="email_intercambio" placeholder="Email" value="<?php echo $datosform['email_intercambio'];?>">
												</div>
											</div>
	
											<div class="form-group">
												<label for="mediuminput" class="col-sm-2 control-label">Contraseña</label>
												<div class="col-sm-8">
													<input type="password" class="form-control1" id="pass_intercambio" name="pass_intercambio" placeholder="Contraseña" value="<?php echo $datosform['pass_intercambio'];?>">
												</div>
											</div>
	
											<div class="form-group mb-n">
												<label for="largeinput" class="col-sm-2 control-label label-input-lg">Tipo Server</label>
												<div class="col-sm-8">
													<select name="tipoServer_intercambio" id="tipoServer_intercambio" class="form-control1">
														<option value="">Seleccione.</option>
														<option value="smtp" <?php echo $datosform['tserver_intercambio'] == 'smtp' ? 'selected' : '';?>>SMTP</option>
														<option value="imap" <?php echo $datosform['tserver_intercambio'] == 'imap' ? 'selected' : '';?>>IMAP</option>
													</select>
												</div>
											</div>

											<div class="form-group">
												<label for="mediuminput" class="col-sm-2 control-label">Puerto</label>
												<div class="col-sm-8">
													<input type="text" class="form-control1" id="port_intercambio" name="port_intercambio" placeholder="465" value="<?php echo $datosform['port_intercambio'];?>">
												</div>
											</div>

											<div class="form-group">
												<label for="mediuminput" class="col-sm-2 control-label">Host</label>
												<div class="col-sm-8">
													<input type="text" class="form-control1" id="host_intercambio" name="host_intercambio" placeholder="ssl://smtp.gmail.com" value="<?php echo $datosform['host_intercambio'];?>">
												</div>
											</div>
										</form>
										<br>
										<button type = "submit" class = "btn btn-info" id="comando">Guardar</button>
										<button type = "button" class = "btn btn-danger" data-dismiss="modal"  id="comando">Cancelar</button>
									</div>	
									</form>
								</div>


								