<!DOCTYPE HTML>
<html>
<head>
<title>Facturación Electrónica</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="Augment Responsive web template, Bootstrap Web Templates, Flat Web Templates, Android Compatible web template, 
Smartphone Compatible web template, free webdesigns for Nokia, Samsung, LG, SonyEricsson, Motorola web design" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
 <!-- Bootstrap Core CSS -->
<link href="<?php echo base_url();?>css/bootstrap.min.css" rel='stylesheet' type='text/css' />
<!-- Custom CSS -->
<link href="<?php echo base_url();?>css/style.css" rel='stylesheet' type='text/css' />
<link href="<?php echo base_url();?>css/style_grid.css" rel="stylesheet" type="text/css" media="all" />

<!-- Graph CSS -->
<link href="<?php echo base_url();?>css/font-awesome.css" rel="stylesheet"> 
<!-- jQuery -->
<link href='//fonts.googleapis.com/css?family=Roboto:700,500,300,100italic,100,400' rel='stylesheet' type='text/css'>
<!-- lined-icons -->
<link rel="stylesheet" href="<?php echo base_url();?>css/icon-font.min.css" type='text/css' />
<!-- //lined-icons -->
<script src="<?php echo base_url();?>js/jquery-1.10.2.min.js"></script>
<script src="<?php echo base_url();?>js/amcharts.js"></script>	
<script src="<?php echo base_url();?>js/serial.js"></script>	
<script src="<?php echo base_url();?>js/light.js"></script>	
<script src="<?php echo base_url();?>js/radar.js"></script>	
<link href="<?php echo base_url();?>css/barChart.css" rel='stylesheet' type='text/css' />
<link href="<?php echo base_url();?>css/fabochart.css" rel='stylesheet' type='text/css' />
<!--clock init-->
<script src="<?php echo base_url();?>js/css3clock.js"></script>
<!--Easy Pie Chart-->
<!--skycons-icons-->
<script src="<?php echo base_url();?>js/skycons.js"></script>

<script src="<?php echo base_url();?>js/jquery.easydropdown.js"></script>

<!--//skycons-icons-->
</head> 
<body>
	<div class="page-container">
		<div class="left-content">
	   		<div class="inner-content">
				<div class="header-section">
					<div class="top_menu">
						<div class="main-search">
							<form>
								<input type="text" value="Search" onFocus="this.value = '';" onBlur="if (this.value == '') {this.value = 'Search';}" class="text"/>
								<input type="submit" value="">
							</form>
							<div class="close"><img src="<?php echo base_url();?>images/cross.png" /></div>
						</div>
						<div class="srch"><button></button></div>
						<script type="text/javascript">
							$('.main-search').hide();
							$('button').click(function (){
								$('.main-search').show();
								$('.main-search text').focus();
							});
							$('.close').click(function(){
								('.main-search').hide();
							});
						</script>

						<div class="profile_details_left">
							<ul class="nofitications-dropdown">
							<li class="dropdown note">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-power-off" aria-hidden="true"></i> <span class="badge"></span></a>
								<ul class="dropdown-menu two first">
									<li>
										<div class="notification_header">
											<h3>Cerrar Sesión</h3> 
										</div>
									</li>
									<li>
										<a href="<?php echo base_url();?>login/salir">
											<div class="user_img">
												<img src="<?php echo base_url();?>images/cerrar.png" alt="cerrar">
											</div>
											<div class="notification_desc">
												
												<p><span>Cerrar Sesión</span></p>
											</div>
											<div class="clearfix"></div>	
										</a>
									</li>
								</ul>
							</li>
										
								
							
							</div>
							<div class="clearfix"></div>	
							<!--//profile_details-->
						</div>
						<!--//menu-right-->
					<div class="clearfix"></div>
				</div>
					<!-- //header-ends -->
						<div class="outter-wp">
							<?php $this->load->view($content_view); ?>
						</div>
									<!--/charts-inner-->
									</div>
										<!--//outer-wp-->
									</div>
								</div>
							</div>
				<!--//content-inner-->

<!-- //Modal apertura de caja -->
<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" id="myModal1">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">Apertura de Caja</h4>
      </div>

      <div class="modal-body">
      	<input type="text" name="caja" class="form-control" id="caja" placeholder="Caja:">
      	<br>
      	<input type="text" name="cajero" class="form-control" id="cajero" placeholder="Cajero:">
      	<br>
		<input type="text" name="efectivo" class="form-control" id="efectivo" placeholder="Efectivo:">
		<br>
		<input type="text" name="cheque" class="form-control" id="cheque" placeholder="Cheques:">
		<br>
		<input type="text" name="otros" class="form-control" id="otros" placeholder="Otros:">
		<br>
		<button type = "button" class = "btn btn-primary" id="comando">Ingresar</button>
		<button type = "button" class = "btn btn-primary" data-dismiss="modal"  id="comando">Cancelar</button>
      </div>
    </div>
  </div>
</div>
<!-- //Modal apertura de caja -->

<!-- //Modal certificado -->

<div class="modal fade bs-example-modal-md" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" id="myModal2">

<form action="<?php echo base_url();?>facturaselectronicas/cargacertificado" method="POST" enctype="multipart/form-data">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">Carga de Certificado</h4>
      </div>

      <div class="modal-body">
      	<div id="seleccion">
      		<p class="seleccion">Examinar <i class="fa fa-upload" aria-hidden="true"></i></p>
			<input type="file" name="certificado" id="file">
		</div>
		<br>
		<div>
			<label>Password:</label>
			<input type="password" name="password" class="form-control" id="contrasena" placeholder="Password">
		</div>
		<br>
		<button type = "submit" class = "btn btn-info" id="comando">Ingresar</button>
		<button type = "button" class = "btn btn-danger" data-dismiss="modal"  id="comando">Cancelar</button>
      </div>
    </div>
  </div>
 </form>
</div>
<!--Comienzo Modal -->
<!-- //Modal certificado -->
			<!--/sidebar-menu-->
				<div class="sidebar-menu">
					<header class="logo">
					<a href="#" class="sidebar-icon"> <span class="fa fa-bars"></span> </a> <a href="inicio.html"> <span id="logo"> <h1>Fact Elec</h1></span> 
					<!--<img id="logo" src="" alt="Logo"/>--> 
				  </a> 
				</header>
			<div style="border-top:1px solid rgba(69, 74, 84, 0.7)"></div>
			<!--/down-->
			
							   <!--//down-->
                           <div class="menu">
									<ul id="menu" >
										<li><a href="<?php echo base_url();?>"><i class="fa fa-tachometer"></i> <span>Menú</span></a></li>

										 <li id="menu-academico" ><a href="#"><i class="fa fa-table"></i> <span>General</span> <span class="fa fa-angle-right" style="float: right"></span></a>
										   <ul id="menu-academico-sub" >
											<li id="menu-academico-avaliacoes" ><a href="clientes.html"> Clientes</a></li>
											<li id="menu-academico-boletim" ><a href="vendedores.html">Vendedores</a></li>
											<li id="menu-academico-avaliacoes" ><a href="condicion_pago.html">Condición de Pago</a></li>
											<li id="menu-academico-avaliacoes" ><a href="sucursales.html">Sucursales</a></li>
											
										  </ul>
										</li>

										 <li id="menu-academico" ><a href="#"><i class="fa fa-file-text-o"></i> <span>Facturación</span> <span class="fa fa-angle-right" style="float: right"></span></a>
											 <ul id="menu-academico-sub" >
												<li id="menu-academico-avaliacoes" ><a href="<?php echo base_url();?>facturaselectronicas/empresas">Registro Empresa</a></li>
												<li id="menu-academico-boletim" ><a href="#"  data-toggle="modal" data-target="#myModal2">Certificado Digital</a></li>
												<li id="menu-academico-boletim" ><a href="<?php echo base_url();?>facturaselectronicas/cargar_folio">Carga de CAF</a></li>
												<li id="menu-academico-boletim" ><a href="factura_proveedor.html">Carga DTE Compras</a></li>
												<li id="menu-academico-boletim" ><a href="cargar_constribuyente.html">Carga Constribuyentes</a></li>
												<li id="menu-academico-boletim" ><a href="confi_email.html">Configuración de Email</a></li>
											  </ul>
										 </li>

										<li id="menu-comunicacao" ><a href="#"><i class="fa fa-list"></i> <span>Mov Facturación</span><span class="fa fa-angle-right" style="float: right"></span></a>
									  		<ul id="menu-comunicacao-sub" >
												<li id="menu-mensagens" style="width:150px" ><a href="project.html">Ventas</a>
										  			<ul id="menu-mensagens-sub" >
														<li id="menu-mensagens-enviadas" style="width:140px" ><a href="facturacion.html">Venta Directa</a></li>
														<li id="menu-mensagens-recebidas"  style="width:140px"><a href="guia_despacho.html">Guia Despacho</a></li>
														<li id="menu-mensagens-recebidas"  style="width:140px"><a href="nota_credito.html">Nota Crédito</a></li>
														<li id="menu-mensagens-recebidas"  style="width:140px"><a href="nota_debito.html">Nota Débito</a></li>
										  			</ul>
												</li>

												<li id="menu-mensagens" style="width:150px" ><a href="#">Punto de Ventas</a>
										  			<ul id="menu-mensagens-sub" >
														<li id="menu-mensagens-enviadas" style="width:140px" ><a href="nota_venta.html">Nota Venta</a></li>
										  			</ul>
												</li>

												<li id="menu-mensagens" style="width:150px" ><a href="project.html">Factura Lotes</a>
										  			<ul id="menu-mensagens-sub" >
														<li id="menu-mensagens-enviadas" style="width:140px" ><a href="guia_despacho.html">Fact Guia</a></li>
														<li id="menu-mensagens-recebidas"  style="width:140px"><a href="nota_venta.html">Fact Nota Venta</a></li>
										  			</ul>
												</li>
									  		</ul>
									</li>

									<li id="menu-academico" ><a href="#"><i class="fa fa-user"></i> <span>Usuario</span> <span class="fa fa-angle-right" style="float: right"></span></a>
										  <ul id="menu-academico-sub" >
										    <li id="menu-academico-avaliacoes" ><a href="usuarios.html">Usuarios</a></li>
										    <li id="menu-academico-boletim" ><a href="nuevo_usuario.html">Nuevo Usuario</a></li>
										    <li id="menu-academico-avaliacoes" ><a href="roles.html">Roles</a></li>
											
										  </ul>
									 </li>	
								</ul>
								</div>
							  </div>
							  <div class="clearfix"></div>		
							</div>
							<script>
							var toggle = true;
										
							$(".sidebar-icon").click(function() {                
							  if (toggle)
							  {
								$(".page-container").addClass("sidebar-collapsed").removeClass("sidebar-collapsed-back");
								$("#menu span").css({"position":"absolute"});
							  }
							  else
							  {
								$(".page-container").removeClass("sidebar-collapsed").addClass("sidebar-collapsed-back");
								setTimeout(function() {
								  $("#menu span").css({"position":"relative"});
								}, 400);
							  }
											
											toggle = !toggle;
										});
							</script>
<!--js -->
<link rel="stylesheet" href="<?php echo base_url();?>css/vroom.css">
<script type="text/javascript" src="<?php echo base_url();?>js/vroom.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/TweenLite.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/CSSPlugin.min.js"></script>
<script src="<?php echo base_url();?>js/jquery.nicescroll.js"></script>
<script src="<?php echo base_url();?>js/scripts.js"></script>

<!-- Bootstrap Core JavaScript -->
   <script src="<?php echo base_url();?>js/bootstrap.min.js"></script>

     <!-- /Script Modal -->
<script>
	$('#myModal1').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget)  
})
</script>
<!-- /Script Modal -->
</body>
</html>