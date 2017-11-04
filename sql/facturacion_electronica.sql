INSERT INTO `accesos` (`codigo`, `reg_estado`, `descripcion`) VALUES ('vyf_cert_digital', 1, 'ventas y facturacion->facturacion electronica->carga certificado digital');
CREATE TABLE `param_fe` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`nombre` VARCHAR(30) NULL DEFAULT NULL,
	`valor` VARCHAR(100) NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
;

INSERT INTO `param_fe` (`nombre`) VALUES ('cert_password');

CREATE TABLE `caf` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`tipo_caf` INT(11) NULL DEFAULT '0',
	`fd` INT(11) NULL DEFAULT '0',
	`fh` INT(11) NULL DEFAULT '0',
	`archivo` VARCHAR(50) NULL DEFAULT NULL,
	`caf_content` TEXT NULL,
	`created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
;


CREATE TABLE `folios_caf` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`folio` INT(11) NULL DEFAULT '0',
	`idcaf` INT(11) NOT NULL DEFAULT '0',
	`estado` ENUM('P','T','O') NOT NULL DEFAULT 'P' COMMENT 'P: pendiente (está libre para ocupar). T: Tomado (existe una factura en el momento que está generando con ese folio). O: Ocupado (Ya se usó el folio)',
	`created_at` DATETIME NOT NULL,
	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
;

INSERT INTO `param_fe` (`nombre`, `valor`) VALUES ('rut_empresa', '76369594-8');
INSERT INTO `tipo_documento` (`id`, `descripcion`, `correlativo`) VALUES (101, 'FACTURA ELECTRONICA', 0);

INSERT INTO `param_fe` (`nombre`) VALUES ('cert_password_encrypt');


CREATE TABLE IF NOT EXISTS `empresa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rut` int(11) DEFAULT '0',
  `dv` char(1) DEFAULT '0',
  `razon_social` varchar(100) DEFAULT '',
  `giro` varchar(100) DEFAULT '',
  `cod_actividad` int(11) DEFAULT '0',
  `dir_origen` varchar(100) DEFAULT '',
  `comuna_origen` varchar(100) DEFAULT '',
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

INSERT INTO `empresa` (`id`, `rut`, `dv`, `razon_social`, `giro`, `cod_actividad`, `dir_origen`, `comuna_origen`, `created_at`, `updated_at`) VALUES
	(1, 76369594, '8', 'SERVICIOS INTEGRALES INFOSYS SPA', 'Insumos de Computacion', 726000, '4 Poniente 0280', 'Talca', '2016-01-12 17:02:14', '2016-01-12 17:02:14');


ALTER TABLE `folios_caf`
	ADD COLUMN `dte` TEXT NOT NULL DEFAULT '' AFTER `estado`;
ALTER TABLE `folios_caf`
	ADD COLUMN `idfactura` INT NOT NULL AFTER `dte`;
ALTER TABLE `folios_caf`
	ADD COLUMN `archivo_dte` VARCHAR(50) NOT NULL AFTER `dte`;
ALTER TABLE `folios_caf`
	ADD COLUMN `path_dte` VARCHAR(50) NOT NULL AFTER `dte`;		

ALTER TABLE `folios_caf`
	ADD COLUMN `pdf` VARCHAR(50) NOT NULL AFTER `archivo_dte`;

/******************** FIN PRIMERA SUBIDA **************************/	

ALTER TABLE `empresa`
	ADD COLUMN `fec_resolucion` DATE NULL DEFAULT NULL AFTER `comuna_origen`;
ALTER TABLE `empresa`
	ADD COLUMN `nro_resolucion` INT NULL DEFAULT NULL AFTER `fec_resolucion`;
ALTER TABLE `empresa`
	ADD COLUMN `logo` VARCHAR(50) NULL DEFAULT NULL AFTER `nro_resolucion`;

ALTER TABLE `folios_caf`
	ADD COLUMN `pdf_cedible` VARCHAR(50) NOT NULL AFTER `pdf`;	
ALTER TABLE `folios_caf`
	ADD COLUMN `trackid` VARCHAR(30) NOT NULL AFTER `pdf_cedible`;		

CREATE TABLE `tipo_caf` (
	`id` INT(11) NOT NULL,
	`nombre` VARCHAR(100) NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
)
ENGINE=InnoDB
;


INSERT INTO `tipo_caf` (`id`, `nombre`) VALUES (33, 'Factura Electrónica');
INSERT INTO `tipo_caf` (`id`, `nombre`) VALUES (34, 'Factura No Afecta Electrónica');
INSERT INTO `tipo_caf` (`id`, `nombre`) VALUES (56, 'Nota de Débito Electrónica');
INSERT INTO `tipo_caf` (`id`, `nombre`) VALUES (61, 'Nota de Crédito Electrónica');


/**************************************************/

CREATE TABLE `dte_proveedores` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`idproveedor` INT(11) NULL DEFAULT '0',
	`dte` TEXT NOT NULL,
	`path_dte` VARCHAR(50) NOT NULL,
	`archivo_dte` VARCHAR(50) NOT NULL,
	`envios_recibos` TEXT NOT NULL,
	`recepcion_dte` TEXT NOT NULL,
	`resultado_dte` TEXT NOT NULL,
	`arch_env_rec` VARCHAR(50) NOT NULL,
	`arch_rec_dte` VARCHAR(50) NOT NULL,
	`arch_res_dte` VARCHAR(50) NOT NULL,
	`created_at` DATETIME NOT NULL,
	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
ROW_FORMAT=COMPACT
AUTO_INCREMENT=1
;


/*******************************************************/
ALTER TABLE `dte_proveedores`
	ADD COLUMN `fecha_documento` DATE NOT NULL AFTER `arch_res_dte`;

/**********************************************************/

INSERT INTO `tipo_documento` (`id`, `descripcion`, `correlativo`) VALUES (102, 'NOTAS DE CREDITO ELECTRONICA', 1);

/******************************************************************/

INSERT INTO `correlativos` (`id`, `nombre`, `correlativo`) VALUES (19, 'FACTURA EXENTA', 0);
ALTER TABLE `factura_clientes`
	ADD COLUMN `forma` INT(11) NOT NULL AFTER `estado`;
INSERT INTO `tipo_documento` (`id`, `descripcion`) VALUES (19, 'FACTURA EXENTA');	

CREATE TABLE `detalle_factura_glosa` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`glosa` VARCHAR(300) NOT NULL,
	`id_factura` INT(11) NOT NULL,
	`id_guia` INT(11) NOT NULL,
	`num_guia` TINYINT(10) NOT NULL,
	`neto` INT(10) NOT NULL,
	`iva` INT(10) NOT NULL,
	`total` INT(10) NOT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
;
/***********************************************************************************/
INSERT INTO `tipo_documento` (`id`, `descripcion`) VALUES (103, 'FACTURA EXENTA ELECTRONICA');

/*************************************************************************************/

INSERT INTO `param_fe` (`nombre`, `valor`) VALUES ('envio_sii', 'manual');



/******************************************************************************************/
CREATE TABLE `contribuyentes_autorizados_1` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`rut` INT(11) NULL DEFAULT NULL,
	`dv` CHAR(1) NULL DEFAULT NULL,
	`razon_social` VARCHAR(250) NULL DEFAULT NULL,
	`nro_resolucion` INT(11) NULL DEFAULT NULL,
	`fec_resolucion` DATE NULL DEFAULT NULL,
	`mail` VARCHAR(100) NULL DEFAULT NULL,
	`url` VARCHAR(250) NULL DEFAULT NULL,
	`fecha` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
;



CREATE TABLE `contribuyentes_autorizados_2` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`rut` INT(11) NULL DEFAULT NULL,
	`dv` CHAR(1) NULL DEFAULT NULL,
	`razon_social` VARCHAR(250) NULL DEFAULT NULL,
	`nro_resolucion` INT(11) NULL DEFAULT NULL,
	`fec_resolucion` DATE NULL DEFAULT NULL,
	`mail` VARCHAR(100) NULL DEFAULT NULL,
	`url` VARCHAR(250) NULL DEFAULT NULL,
	`fecha` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
ROW_FORMAT=COMPACT
;


CREATE TABLE `log_cargas_bases_contribuyentes` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`nombre_archivo` VARCHAR(100) NULL DEFAULT NULL,
	`ruta` VARCHAR(50) NULL DEFAULT NULL,
	`fecha_carga` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
)
ENGINE=InnoDB
;


INSERT INTO `param_fe` (`nombre`, `valor`) VALUES ('tabla_contribuyentes', 'contribuyentes_autorizados_2');

CREATE TABLE `contribuyentes_autorizados` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`rut` VARCHAR(20) NOT NULL DEFAULT '0',
	`razon_social` VARCHAR(500) NULL DEFAULT NULL,
	`nro_resolucion` VARCHAR(50) NULL DEFAULT NULL,
	`fec_resolucion` VARCHAR(50) NULL DEFAULT NULL,
	`mail` VARCHAR(100) NULL DEFAULT NULL,
	`url` VARCHAR(250) NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
ROW_FORMAT=COMPACT
;



post_max_size 30


/********************************************************/

CREATE TABLE `log_libros` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`mes` INT(11) NULL DEFAULT NULL,
	`anno` INT(11) NULL DEFAULT NULL,
	`tipo_libro` ENUM('COMPRA','VENTA') NULL DEFAULT NULL,
	`archivo` VARCHAR(50) NULL DEFAULT NULL,
	`created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
;


/**************************************************************/
CREATE TABLE `email_fe` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`email_contacto` VARCHAR(50) NOT NULL DEFAULT '0',
	`pass_contacto` VARCHAR(50) NOT NULL DEFAULT '0',
	`tserver_contacto` ENUM('smtp','imap') NOT NULL,
	`port_contacto` INT(11) NOT NULL DEFAULT '0',
	`host_contacto` VARCHAR(250) NOT NULL DEFAULT '0',
	`email_intercambio` VARCHAR(50) NOT NULL DEFAULT '0',
	`pass_intercambio` VARCHAR(50) NOT NULL DEFAULT '0',
	`tserver_intercambio` ENUM('smtp','imap') NOT NULL,
	`port_intercambio` INT(11) NOT NULL DEFAULT '0',
	`host_intercambio` VARCHAR(250) NOT NULL DEFAULT '0',
	`created_at` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
;
 /******************************************************************/
 INSERT INTO `tipo_documento` (`id`, `descripcion`, `correlativo`) VALUES (104, 'NOTA DE DEBITO ELECTRONICA', 0);
 INSERT INTO `tipo_documento` (`id`, `descripcion`, `correlativo`) VALUES (16, 'NOTAS DE DEBITO', 0);

 INSERT INTO `correlativos` (`id`, `nombre`, `correlativo`) VALUES (101, 'FACTURA ELECTRONICA', 0);
 INSERT INTO `correlativos` (`id`, `nombre`, `correlativo`) VALUES (103, 'FACTURA EXENTA ELECTRONICA', 0);
 INSERT INTO `correlativos` (`id`, `nombre`, `correlativo`) VALUES (105, 'GUIA DE DESPACHO ELECTRONICA', 0);

 /**********************************************************************/

 ALTER TABLE `cod_activ_econ`
	CHANGE COLUMN `nombre` `nombre` VARCHAR(100) NOT NULL AFTER `codigo`;
UPDATE `cod_activ_econ` SET `nombre`='VENTAS AL POR MENOR DE OTROS PRODUCTOS EN ALMACENES ESPECIALIZADOS N.C.P.' WHERE  `id`=430;


/*************************************************************************************/

CREATE TABLE `guarda_csv` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`tipocaf` INT(11) NOT NULL DEFAULT '0',
	`folio` INT(11) NOT NULL DEFAULT '0',
	`fechafactura` DATE NOT NULL DEFAULT '0000-00-00',
	`condicion` VARCHAR(50) NOT NULL DEFAULT '0',
	`rut` INT(10) NOT NULL DEFAULT '0',
	`dv` CHAR(1) NOT NULL DEFAULT '0',
	`razonsocial` VARCHAR(150) NOT NULL DEFAULT '0',
	`giro` VARCHAR(150) NOT NULL DEFAULT '0',
	`direccion` VARCHAR(150) NOT NULL DEFAULT '0',
	`comuna` VARCHAR(100) NOT NULL DEFAULT '0',
	`ciudad` VARCHAR(100) NOT NULL DEFAULT '0',
	`cuenta` VARCHAR(100) NOT NULL DEFAULT '0',
	`neto` INT(11) NOT NULL DEFAULT '0',
	`iva` INT(11) NOT NULL DEFAULT '0',
	`total` INT(11) NOT NULL DEFAULT '0',
	`codigo` VARCHAR(50) NOT NULL DEFAULT '0',
	`cantidad` INT(11) NOT NULL DEFAULT '0',
	`unidad` VARCHAR(50) NOT NULL DEFAULT '0',
	`nombre` VARCHAR(150) NOT NULL DEFAULT '0',
	`preciounit` INT(11) NOT NULL DEFAULT '0',
	`totaldetalle` INT(11) NOT NULL DEFAULT '0',
	`codigoproceso` VARCHAR(30) NOT NULL DEFAULT '',
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
;


/*****************************************************/

ALTER TABLE `factura_clientes`
	ADD COLUMN `path_dte` VARCHAR(50) NULL AFTER `forma`,
	ADD COLUMN `archivo_dte` VARCHAR(50) NULL AFTER `path_dte`;

ALTER TABLE `factura_clientes`
	ADD COLUMN `dte` TEXT NULL DEFAULT NULL AFTER `archivo_dte`;	

INSERT INTO `clientes` (`id`, `rut`, `nombres`, `direccion`, `id_giro`, `fono`, `id_rubro`, `e_mail`, `id_sector`, `id_comuna`, `id_ciudad`, `descuento`, `fecha_incripcion`, `fecha_ult_actualiz`, `estado`, `id_vendedor`, `id_pago`, `cupo_disponible`, `imp_adicional`, `tipo`, `nombre_contacto`, `fono_contacto`, `e_mail_contacto`) VALUES (1, '903800003', 'S.A. FERIA DE LOS AGRICULTORES', 'AV LAS RASTRAS N.948', 173, '712241128', 0, 'safa#agricultorestalca.cl', 0, 314, 314, 0, '2016-01-19', '2016-01-19', 1, 1, 1, 0, 1, 3, '', '', '');
INSERT INTO `clientes` (`id`, `rut`, `nombres`, `direccion`, `id_giro`, `fono`, `id_rubro`, `e_mail`, `id_sector`, `id_comuna`, `id_ciudad`, `descuento`, `fecha_incripcion`, `fecha_ult_actualiz`, `estado`, `id_vendedor`, `id_pago`, `cupo_disponible`, `imp_adicional`, `tipo`, `nombre_contacto`, `fono_contacto`, `e_mail_contacto`) VALUES (2, '894356006', 'CORDERO S.A.', 'ANTOFAGASTA NO.3065', 444, '022', 0, 'S@M.CL', 0, 89, 308, 0, '2016-01-19', '2016-01-19', 1, 1, 1, 5000000, 0, 3, '', '', '');
INSERT INTO `clientes` (`id`, `rut`, `nombres`, `direccion`, `id_giro`, `fono`, `id_rubro`, `e_mail`, `id_sector`, `id_comuna`, `id_ciudad`, `descuento`, `fecha_incripcion`, `fecha_ult_actualiz`, `estado`, `id_vendedor`, `id_pago`, `cupo_disponible`, `imp_adicional`, `tipo`, `nombre_contacto`, `fono_contacto`, `e_mail_contacto`) VALUES (3, '785494504', 'GOTRU ALIMENTOS SPA', '8 ORIENTE', 115, '0712', 0, 'PEDRO@GOTRU.CL', 0, 314, 314, 0, '2016-01-19', '2016-01-19', 1, 1, 1, 8000000, 0, 1, '', '', '');
INSERT INTO `clientes` (`id`, `rut`, `nombres`, `direccion`, `id_giro`, `fono`, `id_rubro`, `e_mail`, `id_sector`, `id_comuna`, `id_ciudad`, `descuento`, `fecha_incripcion`, `fecha_ult_actualiz`, `estado`, `id_vendedor`, `id_pago`, `cupo_disponible`, `imp_adicional`, `tipo`, `nombre_contacto`, `fono_contacto`, `e_mail_contacto`) VALUES (4, '760193534', 'SOCIEDAD COMERCIAL DEIK Y COMP', '8 ORIENTE NO.1378', 430, '071242121', 0, 'DEIKK@GMAIL.COM', 0, 314, 314, 0, '2016-01-26', '2016-01-26', 1, 1, 1, 4000000, 0, 3, '', '', '');
INSERT INTO `clientes` (`id`, `rut`, `nombres`, `direccion`, `id_giro`, `fono`, `id_rubro`, `e_mail`, `id_sector`, `id_comuna`, `id_ciudad`, `descuento`, `fecha_incripcion`, `fecha_ult_actualiz`, `estado`, `id_vendedor`, `id_pago`, `cupo_disponible`, `imp_adicional`, `tipo`, `nombre_contacto`, `fono_contacto`, `e_mail_contacto`) VALUES (5, '777336703', 'SOC. COMERCIAL EL SEMBRADOR LT', 'DELICIAS NORTE 531', 411, '', 0, '', 0, 204, 204, 0, '2016-03-01', '2016-03-01', 1, 1, 1, 0, 0, 1, '', '', '');
INSERT INTO `clientes` (`id`, `rut`, `nombres`, `direccion`, `id_giro`, `fono`, `id_rubro`, `e_mail`, `id_sector`, `id_comuna`, `id_ciudad`, `descuento`, `fecha_incripcion`, `fecha_ult_actualiz`, `estado`, `id_vendedor`, `id_pago`, `cupo_disponible`, `imp_adicional`, `tipo`, `nombre_contacto`, `fono_contacto`, `e_mail_contacto`) VALUES (6, '795219803', 'CARLOS VALENZUELA Y CIA LIMITA', 'HUAMACHUCO #713', 340, '712621295', 0, '', 0, 282, 282, 0, '2016-03-22', '2016-03-22', 1, 1, 1, 0, 0, 1, '', '', '');
INSERT INTO `clientes` (`id`, `rut`, `nombres`, `direccion`, `id_giro`, `fono`, `id_rubro`, `e_mail`, `id_sector`, `id_comuna`, `id_ciudad`, `descuento`, `fecha_incripcion`, `fecha_ult_actualiz`, `estado`, `id_vendedor`, `id_pago`, `cupo_disponible`, `imp_adicional`, `tipo`, `nombre_contacto`, `fono_contacto`, `e_mail_contacto`) VALUES (7, '965163204', 'AGRICOLA Y COMERCIAL LIRCAY SPA', 'AVDA SAN MIGUEL CRUC E LAS RASTRAS S/N', 114, '', 0, '', 0, 314, 314, 0, '2016-09-01', '2016-12-16', 1, 1, 1, 0, 0, 3, '', '', '');


INSERT INTO `productos` (`id`, `codigo`, `nombre`, `id_marca`, `id_ubi_prod`, `id_uni_medida`, `id_bodega`, `p_ult_compra`, `p_may_compra`, `p_promedio`, `p_costo`, `p_venta`, `stock`, `id_familia`, `id_agrupacion`, `id_subfamilia`, `foto`) VALUES (1, '1001011001', 'TUBO PVC HID.20mmC16 TIGR                    ', 1, 1, 1, 1, 887.00, 999.00, 809.00, 731.00, 1590.00, 44337.12, 1, 1, 1, '');
INSERT INTO `productos` (`id`, `codigo`, `nombre`, `id_marca`, `id_ubi_prod`, `id_uni_medida`, `id_bodega`, `p_ult_compra`, `p_may_compra`, `p_promedio`, `p_costo`, `p_venta`, `stock`, `id_familia`, `id_agrupacion`, `id_subfamilia`, `foto`) VALUES (2, '1001011002', 'TUBO PVC HID.63mmC16 TIGR                    ', 1, 1, 1, 1, 10751.00, 10751.00, 3233.00, 3233.00, 14920.00, 10560.81, 1, 1, 1, '');
INSERT INTO `productos` (`id`, `codigo`, `nombre`, `id_marca`, `id_ubi_prod`, `id_uni_medida`, `id_bodega`, `p_ult_compra`, `p_may_compra`, `p_promedio`, `p_costo`, `p_venta`, `stock`, `id_familia`, `id_agrupacion`, `id_subfamilia`, `foto`) VALUES (3, '1001011003', 'TUBO PVC HID.75mmC16 TIGR                    ', 1, 1, 1, 1, 15198.00, 15198.00, 1415.00, 1415.00, 21100.00, 43593.17, 1, 1, 1, '');
INSERT INTO `productos` (`id`, `codigo`, `nombre`, `id_marca`, `id_ubi_prod`, `id_uni_medida`, `id_bodega`, `p_ult_compra`, `p_may_compra`, `p_promedio`, `p_costo`, `p_venta`, `stock`, `id_familia`, `id_agrupacion`, `id_subfamilia`, `foto`) VALUES (4, '1001011004', 'TUBO PVC HID.90mmC16 TIGR                    ', 1, 1, 1, 1, 0.00, 0.00, 15040.00, 15040.00, 30290.00, 64856.30, 1, 1, 1, '');
INSERT INTO `productos` (`id`, `codigo`, `nombre`, `id_marca`, `id_ubi_prod`, `id_uni_medida`, `id_bodega`, `p_ult_compra`, `p_may_compra`, `p_promedio`, `p_costo`, `p_venta`, `stock`, `id_familia`, `id_agrupacion`, `id_subfamilia`, `foto`) VALUES (5, '1001011005', 'TUBO PVC HID.110mmC16 TIG                    ', 1, 1, 1, 1, 0.00, 0.00, 22513.00, 22513.00, 45340.00, 79099.72, 1, 1, 1, '');
INSERT INTO `productos` (`id`, `codigo`, `nombre`, `id_marca`, `id_ubi_prod`, `id_uni_medida`, `id_bodega`, `p_ult_compra`, `p_may_compra`, `p_promedio`, `p_costo`, `p_venta`, `stock`, `id_familia`, `id_agrupacion`, `id_subfamilia`, `foto`) VALUES (6, '1001011006', 'TUBO PVC HID.125mmC16 TIG                    ', 1, 1, 1, 1, 0.00, 0.00, 28848.00, 28848.00, 58100.00, 69835.28, 1, 1, 1, '');
INSERT INTO `productos` (`id`, `codigo`, `nombre`, `id_marca`, `id_ubi_prod`, `id_uni_medida`, `id_bodega`, `p_ult_compra`, `p_may_compra`, `p_promedio`, `p_costo`, `p_venta`, `stock`, `id_familia`, `id_agrupacion`, `id_subfamilia`, `foto`) VALUES (7, '1001011007', 'TUBO PVC HID.140mmC16 TIG                    ', 1, 1, 1, 1, 0.00, 0.00, 36265.00, 36265.00, 73100.00, 84716.86, 1, 1, 1, '');

CREATE TABLE `vendedores` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`nombre` VARCHAR(30) NOT NULL,
	`direccion` VARCHAR(50) NOT NULL,
	`fono` VARCHAR(10) NOT NULL,
	`rut` VARCHAR(11) NOT NULL,
	`comision` TINYINT(5) NOT NULL,
	`estado` TINYINT(2) NOT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
AUTO_INCREMENT=2
;


/**************************************************************************/

CREATE TABLE `lectura_dte_email` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`filename` VARCHAR(150) NULL DEFAULT NULL,
	`content` TEXT NULL,
	`rutemisor` INT(11) NULL DEFAULT NULL,
	`dvemisor` CHAR(1) NULL DEFAULT NULL,
	`fecemision` DATE NULL DEFAULT NULL,
	`procesado` ENUM('Y','N') NULL DEFAULT 'N',
	`created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=MyISAM
;



CREATE TABLE `email_fe` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`email_contacto` VARCHAR(50) NOT NULL DEFAULT '0',
	`pass_contacto` VARCHAR(50) NOT NULL DEFAULT '0',
	`tserver_contacto` ENUM('smtp','imap') NOT NULL,
	`port_contacto` INT(11) NOT NULL DEFAULT '0',
	`host_contacto` VARCHAR(250) NOT NULL DEFAULT '0',
	`email_intercambio` VARCHAR(50) NOT NULL DEFAULT '0',
	`pass_intercambio` VARCHAR(50) NOT NULL DEFAULT '0',
	`tserver_intercambio` ENUM('smtp','imap') NOT NULL,
	`port_intercambio` INT(11) NOT NULL DEFAULT '0',
	`host_intercambio` VARCHAR(250) NOT NULL DEFAULT '0',
	`created_at` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
ROW_FORMAT=DYNAMIC
;


	INSERT INTO `email_fe` (`id`, `email_contacto`, `pass_contacto`, `tserver_contacto`, `port_contacto`, `host_contacto`, `email_intercambio`, `pass_intercambio`, `tserver_intercambio`, `port_intercambio`, `host_intercambio`, `created_at`, `updated_at`) VALUES (1, 'contactodeik@gmail.com', 'deikk1965', 'smtp', 465, 'ssl://smtp.gmail.com', 'contactodeik@gmail.com', 'deikk1965', 'smtp', 465, 'ssl://smtp.gmail.com', '2016-07-12 15:11:20', '2016-07-12 15:40:38');


/*************************************************************************/

ALTER TABLE `lectura_dte_email`
	ADD COLUMN `path` VARCHAR(50) NULL DEFAULT NULL AFTER `id`;


/****************************************************************************/

ALTER TABLE `lectura_dte_email`
	ADD COLUMN `envios_recibos` TEXT NULL DEFAULT NULL AFTER `fecemision`,
	ADD COLUMN `recepcion_dte` TEXT NULL DEFAULT NULL AFTER `envios_recibos`,
	ADD COLUMN `resultado_dte` TEXT NULL DEFAULT NULL AFTER `recepcion_dte`,
	ADD COLUMN `arch_env_rec` VARCHAR(50) NULL DEFAULT NULL AFTER `resultado_dte`,
	ADD COLUMN `arch_rec_dte` VARCHAR(50) NULL DEFAULT NULL AFTER `arch_env_rec`,
	ADD COLUMN `arch_res_dte` VARCHAR(50) NULL DEFAULT NULL AFTER `arch_rec_dte`;	


/*******************************************************************************/


ALTER TABLE `lectura_dte_email`
	ADD COLUMN `estado_res_dte` INT NULL DEFAULT NULL AFTER `arch_res_dte`;

ALTER TABLE `lectura_dte_email`
	ADD COLUMN `trackid_env_rec` VARCHAR(30) NULL DEFAULT NULL AFTER `estado_res_dte`,
	ADD COLUMN `trackid_rec_dte` VARCHAR(30) NULL DEFAULT NULL AFTER `trackid_env_rec`,
	ADD COLUMN `trackid_res_dte` VARCHAR(30) NULL DEFAULT NULL AFTER `trackid_rec_dte`;