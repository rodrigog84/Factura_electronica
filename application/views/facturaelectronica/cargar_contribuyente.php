							<!--sub-heard-part-->
									  <div class="sub-heard-part">
									   <ol class="breadcrumb m-b-0">
											<li><a href="inicio.html">Inicio</a></li>
											<li class="active">Carga Constribuyente</li>
										</ol>
									   </div>
								  <!--//sub-heard-part-->
									<div class="graph-visual tables-main">
											
													<h3 class="inner-tittle two">Carga Constribuyente <button type="button" class="btn btn-primary btn-flat btn-pri btn-lg" data-toggle="modal" data-target="#myModal4"><i class="fa fa-plus" aria-hidden="true"></i> Nueva Carga</button></h3>

													<h3 class="inner-tittle two">Descripción</h3>
														  <div class="graph">

														  	
															<div class="tables">
																<table class="table"> 
																	<thead> 
																		<tr>
																			<th>Rut</th> 
																			<th>Razón Social</th> 
																			<th>N° Resolución</th> 
																			<th>Email</th> 
																			<th>URL</th>
																			<th>Opciones</th>

																		</tr> 
																	</thead> 
																	<tbody> 
																		<tr class="active">
																			
																			<th scope="row">95.516.320-4</th> 
																			<td>Agricola y Comercial</td> 
																			<td>2001</td> 
																			<td>matalarga@gmail.com</td> 
																			<td><span id="url">www.matalarga.com</span></td>
																			<td>
																				<button type="button" class="btn btn-info" id="opciones" title="Editar"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>
        																		<button type="button" class="btn btn-success" id="opciones" title="Exportar" data-toggle="modal" data-target="#myModal1"><i class="fa fa-share-square-o" aria-hidden="true"></i></button>
        																		<button type="button" class="btn btn-danger" id="opciones" title="Eliminar" data-toggle="modal" data-target="#myModal2"><i class="fa fa-times" aria-hidden="true"></i></button>
																			</td>
																		</tr> 	
																	</tbody> 
																</table> 
															</div>
												
													</div>
										</div>
<!-- //Modal constribuyente -->
<div class="modal fade bs-example-modal-md" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" id="myModal4">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">Carga Constribuyente</h4>
      </div>

      <div class="modal-body">
      	<h4 class="modal-body">Subir Archivo</h4>
      	<div id="seleccion">
      		<p class="seleccion">Subir Archivo <i class="fa fa-upload" aria-hidden="true"></i></p>
			<input type="file" name="file" id="file">

		</div>
		
		<br>
		<button type = "button" class = "btn btn-info" id="comando">Subir</button>
		<button type = "button" class = "btn btn-danger" data-dismiss="modal"  id="comando">Cancelar</button>
      </div>
    </div>
  </div>
</div>