									<!--sub-heard-part-->
									  <div class="sub-heard-part">
									   <ol class="breadcrumb m-b-0">
											<li><a href="inicio.html">Inicio</a></li>
											<li class="active">Registro de Empresa</li>
										</ol>
									   </div>
								  <!--//sub-heard-part-->
									<div class="graph-visual tables-main">
											
										<h3 class="inner-tittle two">Registro de Empresa</h3>

										<form action="<?php echo base_url();?>facturaselectronicas/put_empresa" method="POST">			
											<div class="graph">
												<div class="tables">
													<table class="table">
	      												<thead> 
															<tr> 
																<th>Rut:</th> 
																<th>Razón Social:</th>
															</tr> 
														</thead>
														<tbody>
															<td>
																<input type="text" name="rut" class="form-control" id="rut" placeholder="98.123.456-7" value="<?php echo $datosform['rut'];?>">
															</td>
															<td>
																<input type="text" name="razon_social" class="form-control" id="razon_social" placeholder="Razón Social" value="<?php echo $datosform['razon_social'];?>">
															</td>
														</tbody>
	      											</table>

	      											<table class="table">
	      												<thead> 
															<tr> 
																<th>Dirección:</th> 
																<th>Teléfono:</th>
															</tr> 
														</thead>
														<tbody>
															<td>
																<input type="text" name="direccion" class="form-control" id="direccion" placeholder="Dirección" value="<?php echo $datosform['direccion'];?>">
															</td>
															<td>
																<input type="text" name="telefono" class="form-control" id="telefono" placeholder="Teléfono" >
															</td>
														</tbody>
	      											</table>

	      											<table class="table">
	      												<thead> 
															<tr> 
																<th>Código Actividad:</th> 
																<th>Giro:</th>
																<th>Comuna:</th>
						
															</tr> 
														</thead>
														<tbody>
															<td>
																<input type="text" name="codigo_actividad" class="form-control" id="codigo_actividad" placeholder="Código Actividad" value="<?php echo $datosform['cod_actividad'];?>">
															</td>

															<td>
																<input type="text" name="giro" class="form-control" id="giro" placeholder="Giro" value="<?php echo $datosform['giro'];?>">
															</td>
															<td>
																<select name="selector1" id="selector1" class="form-control1">
																	<option>Seleccione.</option>
																	<option>Dolore, ab unde modi est!</option>
																	<option>Illum, fuga minus sit eaque.</option>
																	<option>Consequatur ducimus maiores voluptatum min</option>
																</select>
															</td>
														</tbody>
	      											</table>

	      											<table class="table">
	      												<thead> 
															<tr> 
																<th>Fecha Resolución:</th> 
																<th>Nº Resolución:</th>
																 
						
															</tr> 
														</thead>
														<tbody>
					
															<td>
																<input placeholder="Fecha Inicio" class="form-control" id="datepicker" type="text" name="fec_resolucion" onfocus="this.value = '';" onblur="if (this.value == '') {this.value = '';}" required=""  value="<?php echo $datosform['fec_resolucion'];?>" />
															</td>

															<td>
																<input type="text" name="nu_resolucion" class="form-control" id="nu_resolucion" placeholder="Nº Resolución" value="<?php echo $datosform['nro_resolucion'];?>" >
															</td>
																
															
														</tbody>
	      											</table>

	      											<table class="table">
	      												<thead>
	      													<th>Subir Imagén:</th>
	      												</thead>

	      												<tbody>
	      													<td>
	      														<div id="seleccion">
	      															<p class="seleccion">Examinar <i class="fa fa-upload" aria-hidden="true"></i></p>
																	<input type="file" name="file" id="file">
																</div>
	      													</td>
	      												</tbody>
	      											</table>
	      											<br>

	      											<button type="submit" class = "btn btn-info" id="comando">Guardar</button>
												</div>
											</div>
										</form>
									</div>
								<!--//graph-visual-->