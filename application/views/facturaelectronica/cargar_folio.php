									<!--sub-heard-part-->
									  <div class="sub-heard-part">
									   <ol class="breadcrumb m-b-0">
											<li><a href="inicio.html">Inicio</a></li>
											<li class="active">Carga de Folio</li>
										</ol>
									   </div>
								  <!--//sub-heard-part-->
									<div class="graph-visual tables-main">
											
													<h3 class="inner-tittle two">Carga de Folio <button type="button" class="btn btn-primary btn-flat btn-pri btn-lg" data-toggle="modal" data-target="#myModal4"><i class="fa fa-plus" aria-hidden="true"></i> Nueva Carga</button></h3>

													<h3 class="inner-tittle two">Descripción</h3>
														  <div class="graph">

														  	
															<div class="tables">
																<table class="table"> 
																	<thead> 
																		<tr> 
																			<th>Facturas Electrónicas</th> 
																			<th>Facturas no Afectadas</th> 
																			<th>Nota de Débito </th> 
																			<th>Nota de Crédito</th> 

																		</tr> 
																	</thead> 
																	<tbody> 
																		<tr class="active"> 
																			<td ><p class="<?php echo $datos_folios[33]['style'];?>"><?php echo $datos_folios[33]['message'];?></p></td> 
																			<td><p class="<?php echo $datos_folios[34]['style'];?>"><?php echo $datos_folios[34]['message'];?></p></td> 
																			<td><p class="<?php echo $datos_folios[56]['style'];?>"><?php echo $datos_folios[56]['message'];?></p></td> 
																			<td><p class="<?php echo $datos_folios[61]['style'];?>"><?php echo $datos_folios[61]['message'];?></p></td> 
																		</tr> 	
																	</tbody> 
																</table> 
															</div>
												
													</div>
										</div>

										<!--//graph-visual-->


<!-- //Modal certificado -->
<!-- //Modal certificado -->
<form action="<?php echo base_url();?>facturaselectronicas/cargacaf" method="POST" enctype="multipart/form-data">	
	<div class="modal fade bs-example-modal-md" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" id="myModal4">
	  <div class="modal-dialog modal-md" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="exampleModalLabel">Carga de Folio</h4>
	      </div>

	      <div class="modal-body">
	      	<div>
				<label>Tipo de Carga:</label>
				<select name="tipoCaf" id="tipoCaf" class="form-control1">
					<option value="">Seleccione tipo CAF.</option>
					<option value="33">(33) Factura Electr&oacute;nica</option>
					<option value="34">(34) Factura No Afecta Electr&oacute;nica</option>
					<option value="56">(56) Nota de Débito Electr&oacute;nica</option>
					<option value="61">(61) Nota de Crédito Electr&oacute;nica</option>
				</select>
			</div>
			<br>
	      	<div id="seleccion">
	      		<p class="seleccion">Subir Folio <i class="fa fa-upload" aria-hidden="true"></i></p>
				<input type="file" name="caf" id="file">
			</div>
			<br>
			<button type="submit" class = "btn btn-info" id="comando">Cargar Folio</button>
			<button type = "button" class = "btn btn-danger" data-dismiss="modal"  id="comando">Cancelar</button>
	      </div>
	    </div>
	  </div>
	</div>										
</form>