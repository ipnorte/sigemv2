<?php 
// debug($orden);
// exit;

//debug($_SESSION['MUTUAL_INI']);
//exit;

App::import('Vendor','solicitud_credito_general_pdf');

$PDF = new SolicitudCreditoGeneralPDF();

$PDF->SetTitle("SOLICITUD DE CREDITO #".$id);
$PDF->SetFontSizeConf(8.5);
$PDF->PIE = false;


$PDF->Open();

// $membrete = array(
// 		'L1' => Configure::read('APLICACION.nombre_fantasia'),
// 		'L2' => Configure::read('APLICACION.domi_fiscal'),
// 		'L3' => "TEL: " . Configure::read('APLICACION.telefonos') ." - email: ".Configure::read('APLICACION.email')
// );

$membrete = array(
		'L1' => $orden['MutualProductoSolicitud']['proveedor_full_name'],
		'L2' => $orden['MutualProductoSolicitud']['proveedor_domicilio'],
		'L3' => $orden['MutualProductoSolicitud']['proveedor_localidad'] ." ".$orden['MutualProductoSolicitud']['proveedor_telefono']
);

$PDF->AddPage();
$PDF->SetY(10);
$PDF->SetFillColor(220,220,220);

// $PDF->SetFont('courier','',12);
$PDF->image(__DIR__ . DIRECTORY_SEPARATOR . "logotipo". DIRECTORY_SEPARATOR . "mis.png",10,10,30);
$PDF->SetY(25);
$PDF->Cell(0,5,"Matrícula 1361 SF-INACyM",0);
$PDF->SetX(10);
$PDF->SetY(28);
$PDF->Cell(0,5,"(Actualmente INAES)",0);

$PDF->SetFont(PDF_FONT_NAME_MAIN,'B',14);
$PDF->SetY(10);
$PDF->SetX(90);
$PDF->Cell(0,5,"SOLICITUD DE AYUDA ECONOMICA",0,0,'C');
$PDF->SetY(15);
$PDF->SetX(90);
$PDF->SetFont(PDF_FONT_NAME_MAIN,'',12);
$PDF->Cell(0,5,"Convenio 22 de SEPTIEMBRE - MIS",0,0,'C');

$PDF->SetFont(PDF_FONT_NAME_MAIN,'',7);

$PDF->INI_Y = 40;
$PDF->H = 4;

$PDF->SetLinea(1);
$PDF->SetX(70);

$PDF->Cell(25,$PDF->H,"Fecha",'LRT');

$PDF->SetLinea(2);
$PDF->SetX(70);

$PDF->Cell(25,$PDF->H,date('d/m/Y',strtotime($orden['MutualProductoSolicitud']['fecha'])),'LR',0,'C',1);

$PDF->SetLinea(1);
$PDF->SetX(95);
$PDF->Cell(50,8,"",1);

// $PDF->SetY(25);
$PDF->SetX(145);
$PDF->Cell(60,$PDF->H,"",1);
$PDF->SetLinea(2);
$PDF->SetX(145);
$PDF->Cell(60,$PDF->H,"",1);



$PDF->SetLinea(3);
$PDF->SetX(10);
$PDF->Cell(85,$PDF->H,"Motivo de la Solicitud",'LRT');
$PDF->SetX(95);
$PDF->Cell(25,$PDF->H,"Imp. Solicitado",'LR',0,'C');
$PDF->SetX(120);
$PDF->Cell(25,$PDF->H,"Cant. De Cuotas",'LR',0,'C');
$PDF->SetX(145);
$PDF->Cell(20,$PDF->H,"Imp.Cuota",'L',0,'C');
$PDF->SetX(165);
$PDF->Cell(20,$PDF->H,"Monto Otorgado",'L',0,'C');
$PDF->SetX(185);
$PDF->Cell(20,$PDF->H,"Tasa de Serv.",'LR',0,'C');

$PDF->SetLinea(4);
$PDF->SetX(10);
$PDF->Cell(85,$PDF->H,"",'LRB');
$PDF->SetX(95);
$PDF->Cell(25,$PDF->H,$orden['MutualProductoSolicitud']['importe_solicitado'],'LRB',0,'C');
$PDF->SetX(120);
$PDF->Cell(25,$PDF->H,$orden['MutualProductoSolicitud']['cuotas_print'],'LRB',0,'C');
$PDF->SetX(145);
$PDF->Cell(20,$PDF->H,$orden['MutualProductoSolicitud']['importe_cuota'],'LB',0,'C');
$PDF->SetX(165);
// $PDF->Cell(20,$PDF->H,$orden['MutualProductoSolicitud']['importe_total'],'LB',0,'C');
$PDF->Cell(20,$PDF->H,"",'LB',0,'C');
$PDF->SetX(185);
$PDF->Cell(20,$PDF->H,"",'LRB',0,'C');

$PDF->SetLinea(5);
$PDF->SetFont(PDF_FONT_NAME_MAIN,'B',8);
$PDF->SetFillColor(105,105,105);
$PDF->SetTextColor(255,255,255);
$PDF->Cell(195,$PDF->H,"Datos Personales del Solicitante (Declaración Jurada)",'LRB',0,'C',1);

$PDF->SetLinea(6);
$PDF->SetX(10);
$PDF->SetFont(PDF_FONT_NAME_MAIN,'',7);
$PDF->SetFillColor(220,220,220);
$PDF->SetTextColor(0,0,0);
$PDF->Cell(135,$PDF->H,"Apellido y Nombres:   " . $orden['MutualProductoSolicitud']['beneficiario_apenom'],'LRB');
$PDF->SetX(145);
$PDF->Cell(60,$PDF->H,"Apellido Materno:",'LRB',0,'L');

$PDF->SetLinea(7);
$PDF->SetX(10);
$PDF->Cell(20,$PDF->H,"Documento:",'LRB',0,'L');
$PDF->SetX(30);
$PDF->Cell(20,$PDF->H,"DNI   LC   LE",'LRB',0,'C',1);
$PDF->SetX(50);
$PDF->Cell(45,$PDF->H,"N°  " . $orden['MutualProductoSolicitud']['beneficiario_tdocndoc'],'LRB',0,'L');
$PDF->SetX(95);
$PDF->Cell(25,$PDF->H,"CUIL   CUIT   CDI",'LRB',0,'C',1);
$PDF->SetX(120);
$PDF->Cell(45,$PDF->H,"N°  " . $orden['MutualProductoSolicitud']['beneficiario_cuit_cuil'],'LRB',0,'L');
$PDF->SetX(165);
$PDF->Cell(40,$PDF->H,"Sexo:   " . $orden['MutualProductoSolicitud']['beneficiario_sexo'],'LRB',0,'L');


$PDF->SetLinea(8);
$PDF->SetX(10);
$PDF->Cell(85,$PDF->H,"Domicilio Real: " . $orden['MutualProductoSolicitud']['beneficiario_calle']."  N° ".$orden['MutualProductoSolicitud']['beneficiario_numero_calle'],'LRB',0,'L');
$PDF->SetX(95);
$PDF->Cell(70,$PDF->H,"Localidad: " . $orden['MutualProductoSolicitud']['beneficiario_localidad'],'LRB',0,'L');
$PDF->SetX(165);
$PDF->Cell(40,$PDF->H,"C. Postal:   " . $orden['MutualProductoSolicitud']['beneficiario_cp'],'LRB',0,'L');


$PDF->SetLinea(9);
$PDF->SetX(10);
$PDF->Cell(85,$PDF->H,"Provincia: " . $orden['MutualProductoSolicitud']['beneficiario_provincia'],'LRB',0,'L');
$PDF->SetX(95);
$PDF->Cell(70,$PDF->H,"Fecha de Nacimiento: " . date('d / m / Y',strtotime($orden['MutualProductoSolicitud']['beneficiario_fecha_nacimiento'])),'LRB',0,'L');
$PDF->SetX(165);
$PDF->Cell(40,$PDF->H,"Lugar:",'LRB',0,'L');

$PDF->SetLinea(10);
$PDF->SetX(10);
$PDF->Cell(85,$PDF->H,"Teléfono Fijo",'L',0,'L');
$PDF->SetX(95);
$PDF->Cell(70,$PDF->H,"Celular",'',0,'L');
$PDF->SetX(165);
$PDF->Cell(40,$PDF->H,"Dirección de Correo Electrónico",'R',0,'L');

$PDF->SetLinea(11);
$PDF->SetX(10);
$PDF->Cell(85,$PDF->H,$orden['MutualProductoSolicitud']['beneficiario_telefono_fijo'],'LB',0,'L');
$PDF->SetX(95);
$PDF->Cell(70,$PDF->H,$orden['MutualProductoSolicitud']['beneficiario_telefono_movil'],'B',0,'L');
$PDF->SetX(165);
$PDF->Cell(40,$PDF->H,$orden['MutualProductoSolicitud']['beneficiario_e_mail'],'RB',0,'L');


$PDF->SetLinea(12);
$PDF->SetX(10);
$PDF->Cell(40,$PDF->H,"Estado Civil: " . $orden['MutualProductoSolicitud']['beneficiario_estado_civil'],'LRB',0,'L');
$PDF->SetX(50);
$PDF->Cell(70,$PDF->H,"Nacionalidad:",'LRB',0,'L');
$PDF->SetX(120);
$PDF->Cell(85,$PDF->H,"Nombre Tarjeta de Crédito:",'LRB',0,'L');

$PDF->SetLinea(13);
$PDF->SetX(10);
$PDF->Cell(40,$PDF->H,"Reviste Calidad de PEP'S",'LR',0,'L');
$PDF->SetX(50);
$PDF->Cell(45,$PDF->H,"Vivienda",'LR',0,'C');
$PDF->SetX(95);
$PDF->Cell(50,$PDF->H,"Vehículo Marca",'LR',0,'C');
$PDF->SetX(145);
$PDF->Cell(20,$PDF->H,"Modelo",'LR',0,'C');
$PDF->SetX(165);
$PDF->Cell(20,$PDF->H,"Año",'LR',0,'C');
$PDF->SetX(185);
$PDF->Cell(20,$PDF->H,"Dominio",'LR',0,'C');

$PDF->SetLinea(14);
$PDF->SetX(10);
$PDF->Cell(40,$PDF->H,"SI           (*** NO ***)",'LRB',0,'C');
$PDF->SetX(50);
$PDF->Cell(45,$PDF->H,"Propia            Alquila",'LRB',0,'C');
$PDF->SetX(95);
$PDF->Cell(50,$PDF->H,"",'LRB',0,'C');
$PDF->SetX(145);
$PDF->Cell(20,$PDF->H,"",'LRB',0,'C');
$PDF->SetX(165);
$PDF->Cell(20,$PDF->H,"",'LRB',0,'C');
$PDF->SetX(185);
$PDF->Cell(20,$PDF->H,"",'LRB',0,'C');


$PDF->SetLinea(15);
$PDF->SetFont(PDF_FONT_NAME_MAIN,'B',8);
$PDF->SetFillColor(105,105,105);
$PDF->SetTextColor(255,255,255);
$PDF->Cell(195,$PDF->H,"Datos Laborales del Solicitante",'LRB',0,'C',1);

$PDF->SetLinea(16);
$PDF->SetX(10);
$PDF->SetFont(PDF_FONT_NAME_MAIN,'',7);
$PDF->SetFillColor(220,220,220);
$PDF->SetTextColor(0,0,0);
$PDF->Cell(60,$PDF->H,"Clave",'LRB',0,'L');
$PDF->SetX(70);
$PDF->Cell(75,$PDF->H,"Actividad",'LRB',0,'L');
$PDF->SetX(145);
$PDF->Cell(20,$PDF->H,"Titular",'LB',0,'L');
$PDF->SetX(165);
$PDF->Cell(20,$PDF->H,"",'B',0,'L');
$PDF->SetX(185);
$PDF->Cell(20,$PDF->H,"Jubilado",'BR',0,'L');


$PDF->SetLinea(17);
$PDF->SetX(10);
$PDF->Cell(135,$PDF->H,"Nombre del Establecimiento:",'LRB',0,'L');
$PDF->SetX(145);
$PDF->Cell(60,$PDF->H,"Ingreso Mensual:",'LRB',0,'L');

$PDF->SetLinea(18);
$PDF->SetX(10);
$PDF->Cell(85,$PDF->H,"Domicilio:",'LB',0,'L');
$PDF->SetX(95);
$PDF->Cell(50,$PDF->H,"Localidad:",'B',0,'L');
$PDF->SetX(145);
$PDF->Cell(20,$PDF->H,"CP:",'RB',0,'L');
$PDF->SetX(165);
$PDF->Cell(40,$PDF->H,"Tel. Laboral:",'R',0,'L');

$PDF->SetLinea(19);
$PDF->SetX(10);
$PDF->Cell(110,$PDF->H,"Otras Actividades:",'LBR',0,'L');
$PDF->SetX(120);
$PDF->Cell(45,$PDF->H,"Otros Ingresos:",'LBR',0,'L');
$PDF->SetX(165);
$PDF->Cell(40,$PDF->H,"",'RB',0,'L');


$PDF->SetLinea(20);
$PDF->SetFont(PDF_FONT_NAME_MAIN,'B',8);
$PDF->SetFillColor(105,105,105);
$PDF->SetTextColor(255,255,255);
$PDF->Cell(195,$PDF->H,"Datos del Cónyuge del Solicitante",'LRB',0,'C',1);

$PDF->SetLinea(21);
$PDF->SetX(10);
$PDF->SetFont(PDF_FONT_NAME_MAIN,'',7);
$PDF->SetFillColor(220,220,220);
$PDF->SetTextColor(0,0,0);
$PDF->Cell(135,$PDF->H,"Apellido y Nombres:",'LRB',0,'L');
$PDF->SetX(145);
$PDF->Cell(20,8,"CUIL CUIT CDI",'RBL',0,'C',1);
$PDF->SetX(165);
$PDF->Cell(40,8,"N°",'RBL',0,'L');

$PDF->SetLinea(22);
$PDF->SetX(10);
$PDF->Cell(20,$PDF->H,"Sexo:  M    F",'LRB',0,'L');
$PDF->SetX(30);
$PDF->Cell(40,$PDF->H,"Fecha Nac.:      /         /       ",'LRB',0,'L');
$PDF->SetX(70);
$PDF->Cell(75,$PDF->H,"Actividad",'LRB',0,'L');



$PDF->SetLinea(23);
$PDF->SetFont(PDF_FONT_NAME_MAIN,'B',8);
$PDF->SetFillColor(105,105,105);
$PDF->SetTextColor(255,255,255);
$PDF->Cell(195,$PDF->H,"Datos Personales del Cosolicitante Propuesto (Declaración Jurada)",'LRB',0,'C',1);

$PDF->SetLinea(24);
$PDF->SetX(10);
$PDF->SetFont(PDF_FONT_NAME_MAIN,'',7);
$PDF->SetFillColor(220,220,220);
$PDF->SetTextColor(0,0,0);
$PDF->Cell(135,$PDF->H,"Apellido y Nombres:",'LRB');
$PDF->SetX(145);
$PDF->Cell(60,$PDF->H,"Apellido Materno:",'LRB',0,'L');


$PDF->SetLinea(25);
$PDF->SetX(10);
$PDF->Cell(20,$PDF->H,"Documento:",'LRB',0,'L');
$PDF->SetX(30);
$PDF->Cell(20,$PDF->H,"DNI   LC   LE",'LRB',0,'C',1);
$PDF->SetX(50);
$PDF->Cell(45,$PDF->H,"N°  ",'LRB',0,'L');
$PDF->SetX(95);
$PDF->Cell(25,$PDF->H,"CUIL   CUIT   CDI",'LRB',0,'C',1);
$PDF->SetX(120);
$PDF->Cell(45,$PDF->H,"N°  ",'LRB',0,'L');
$PDF->SetX(165);
$PDF->Cell(40,$PDF->H,"Sexo:   ",'LRB',0,'L');


$PDF->SetLinea(26);
$PDF->SetX(10);
$PDF->Cell(85,$PDF->H,"Domicilio Real: ",'LRB',0,'L');
$PDF->SetX(95);
$PDF->Cell(70,$PDF->H,"Localidad: ",'LRB',0,'L');
$PDF->SetX(165);
$PDF->Cell(40,$PDF->H,"C. Postal:   ",'LRB',0,'L');


$PDF->SetLinea(27);
$PDF->SetX(10);
$PDF->Cell(85,$PDF->H,"Provincia: ",'LRB',0,'L');
$PDF->SetX(95);
$PDF->Cell(70,$PDF->H,"Fecha de Nacimiento:       /       /   ",'LRB',0,'L');
$PDF->SetX(165);
$PDF->Cell(40,$PDF->H,"Lugar:",'LRB',0,'L');

$PDF->SetLinea(28);
$PDF->SetX(10);
$PDF->Cell(85,$PDF->H,"Teléfono Fijo",'L',0,'L');
$PDF->SetX(95);
$PDF->Cell(70,$PDF->H,"Celular",'',0,'L');
$PDF->SetX(165);
$PDF->Cell(40,$PDF->H,"Dirección de Correo Electrónico",'R',0,'L');

$PDF->SetLinea(29);
$PDF->SetX(10);
$PDF->Cell(85,$PDF->H,"",'LB',0,'L');
$PDF->SetX(95);
$PDF->Cell(70,$PDF->H,"",'B',0,'L');
$PDF->SetX(165);
$PDF->Cell(40,$PDF->H,"",'RB',0,'L');


$PDF->SetLinea(30);
$PDF->SetX(10);
$PDF->Cell(40,$PDF->H,"Estado Civil: ",'LRB',0,'L');
$PDF->SetX(50);
$PDF->Cell(70,$PDF->H,"Nacionalidad:",'LRB',0,'L');
$PDF->SetX(120);
$PDF->Cell(85,$PDF->H,"Nombre Tarjeta de Crédito:",'LRB',0,'L');

$PDF->SetLinea(31);
$PDF->SetX(10);
$PDF->Cell(40,$PDF->H,"Reviste Calidad de PEP'S",'LR',0,'L');
$PDF->SetX(50);
$PDF->Cell(45,$PDF->H,"Vivienda",'LR',0,'C');
$PDF->SetX(95);
$PDF->Cell(50,$PDF->H,"Vehículo Marca",'LR',0,'C');
$PDF->SetX(145);
$PDF->Cell(20,$PDF->H,"Modelo",'LR',0,'C');
$PDF->SetX(165);
$PDF->Cell(20,$PDF->H,"Año",'LR',0,'C');
$PDF->SetX(185);
$PDF->Cell(20,$PDF->H,"Dominio",'LR',0,'C');

$PDF->SetLinea(32);
$PDF->SetX(10);
$PDF->Cell(40,$PDF->H,"SI            NO",'LRB',0,'C');
$PDF->SetX(50);
$PDF->Cell(45,$PDF->H,"Propia            Alquila",'LRB',0,'C');
$PDF->SetX(95);
$PDF->Cell(50,$PDF->H,"",'LRB',0,'C');
$PDF->SetX(145);
$PDF->Cell(20,$PDF->H,"",'LRB',0,'C');
$PDF->SetX(165);
$PDF->Cell(20,$PDF->H,"",'LRB',0,'C');
$PDF->SetX(185);
$PDF->Cell(20,$PDF->H,"",'LRB',0,'C');


$PDF->SetLinea(33);
$PDF->SetFont(PDF_FONT_NAME_MAIN,'B',8);
$PDF->SetFillColor(105,105,105);
$PDF->SetTextColor(255,255,255);
$PDF->Cell(195,$PDF->H,"Datos Laborales del Cosolicitante",'LRB',0,'C',1);

$PDF->SetLinea(34);
$PDF->SetX(10);
$PDF->SetFont(PDF_FONT_NAME_MAIN,'',7);
$PDF->SetFillColor(220,220,220);
$PDF->SetTextColor(0,0,0);
$PDF->Cell(60,$PDF->H,"Clave",'LRB',0,'L');
$PDF->SetX(70);
$PDF->Cell(75,$PDF->H,"Actividad",'LRB',0,'L');
$PDF->SetX(145);
$PDF->Cell(20,$PDF->H,"Titular",'LB',0,'L');
$PDF->SetX(165);
$PDF->Cell(20,$PDF->H,"",'B',0,'L');
$PDF->SetX(185);
$PDF->Cell(20,$PDF->H,"Jubilado",'BR',0,'L');


$PDF->SetLinea(35);
$PDF->SetX(10);
$PDF->Cell(135,$PDF->H,"Nombre del Establecimiento:",'LRB',0,'L');
$PDF->SetX(145);
$PDF->Cell(60,$PDF->H,"Ingreso Mensual:",'LRB',0,'L');

$PDF->SetLinea(36);
$PDF->SetX(10);
$PDF->Cell(85,$PDF->H,"Domicilio:",'LB',0,'L');
$PDF->SetX(95);
$PDF->Cell(50,$PDF->H,"Localidad:",'B',0,'L');
$PDF->SetX(145);
$PDF->Cell(20,$PDF->H,"CP:",'RB',0,'L');
$PDF->SetX(165);
$PDF->Cell(40,$PDF->H,"Tel. Laboral:",'R',0,'L');

$PDF->SetLinea(37);
$PDF->SetX(10);
$PDF->Cell(110,$PDF->H,"Otras Actividades:",'LBR',0,'L');
$PDF->SetX(120);
$PDF->Cell(45,$PDF->H,"Otros Ingresos:",'LBR',0,'L');
$PDF->SetX(165);
$PDF->Cell(40,$PDF->H,"",'RB',0,'L');


$PDF->SetLinea(38);
$PDF->SetFont(PDF_FONT_NAME_MAIN,'B',8);
$PDF->SetFillColor(105,105,105);
$PDF->SetTextColor(255,255,255);
$PDF->Cell(195,$PDF->H,"Datos del Cónyuge del Cosolicitante",'LRB',0,'C',1);

$PDF->SetLinea(39);
$PDF->SetX(10);
$PDF->SetFont(PDF_FONT_NAME_MAIN,'',7);
$PDF->SetFillColor(220,220,220);
$PDF->SetTextColor(0,0,0);
$PDF->Cell(135,$PDF->H,"Apellido y Nombres:",'LRB',0,'L');
$PDF->SetX(145);
$PDF->Cell(20,8,"CUIL CUIT CDI",'RBL',0,'C',1);
$PDF->SetX(165);
$PDF->Cell(40,8,"N°",'RBL',0,'L');

$PDF->SetLinea(40);
$PDF->SetX(10);
$PDF->Cell(20,$PDF->H,"Sexo:  M    F",'LRB',0,'L');
$PDF->SetX(30);
$PDF->Cell(40,$PDF->H,"Fecha Nac.:      /         /       ",'LRB',0,'L');
$PDF->SetX(70);
$PDF->Cell(75,$PDF->H,"Actividad",'LRB',0,'L');


$PDF->SetLinea(45);
$PDF->SetX(40);
$PDF->Cell(60,$PDF->H,"Firma del Solicitante",'T',0,'C');
$PDF->SetX(140);
$PDF->Cell(60,$PDF->H,"Firma del Cosolicitante",'T',0,'C');

$PDF->SetLinea(50);
$PDF->SetX(10);
$PDF->Cell(155,$PDF->H,"Reservado 22 de Septiembre",'LRT',0,'L');
$PDF->SetX(165);
$PDF->Cell(40,$PDF->H,"Fecha",'LRT',0,'L');


$PDF->SetLinea(51);
$PDF->SetX(10);
$PDF->Cell(155,$PDF->H,"Observaciones:",'LR',0,'L');
$PDF->SetX(165);
$PDF->Cell(40,$PDF->H,"            /            /         ",'LRB',0,'L',1);

$PDF->SetLinea(52);
$PDF->SetX(10);
$PDF->Cell(195,$PDF->H,"Aconsejamos su aprobación y certificamos que los datos son correctos, y que la solicitud, documental y firmas exigidas se hallen correctamente perfeccionadas.",'LR',0,'L');

$PDF->SetLinea(53);
$PDF->SetX(10);
$PDF->Cell(195,$PDF->H,"",'LR',0,'L');

$PDF->SetLinea(54);
$PDF->SetX(10);
$PDF->Cell(195,$PDF->H,"",'LR',0,'L');

$PDF->SetLinea(55);
$PDF->SetX(10);
$PDF->Cell(195,$PDF->H,"",'LR',0,'L');

$PDF->SetLinea(56);
$PDF->SetX(10);
$PDF->Cell(30,$PDF->H,"",'L',0,'L');
$PDF->SetX(40);
$PDF->Cell(60,$PDF->H,"Firma del Responsable",'T',0,'C');
$PDF->SetX(140);
$PDF->Cell(60,$PDF->H,"Firma del Responsable Adm. Central",'T',0,'C');
$PDF->SetX(200);
$PDF->Cell(5,$PDF->H,"",'R',0,'L');

$PDF->SetLinea(57);
$PDF->SetX(10);
$PDF->Cell(195,$PDF->H,"",'LR',0,'L');

$PDF->SetLinea(58);
$PDF->SetX(10);
$PDF->Cell(195,$PDF->H,"",'LR',0,'L');

$PDF->SetLinea(59);
$PDF->SetX(10);
$PDF->Cell(30,$PDF->H,"",'L',0,'L');
$PDF->SetX(40);
$PDF->Cell(60,$PDF->H,"Aclaración y Sello",'T',0,'C');
$PDF->SetX(140);
$PDF->Cell(60,$PDF->H,"Aclaración y Sello",'T',0,'C');
$PDF->SetX(200);
$PDF->Cell(5,$PDF->H,"",'R',0,'L');

$PDF->SetLinea(60);
$PDF->SetX(10);
$PDF->Cell(195,$PDF->H,"",'LRB',0,'L');


//----------------------------------------------------------------------------------
$PDF->AddPage();
$PDF->image(__DIR__ . DIRECTORY_SEPARATOR . "logotipo". DIRECTORY_SEPARATOR . "mis.png",10,10,30);
$PDF->SetY(30);
$PDF->SetX(10);


$PDF->SetFont(PDF_FONT_NAME_MAIN,'B',13);
$PDF->Cell(0,5,"Declaración Jurada sobre la condición de Persona Expuesta Políticamente",0,0,'C');

$PDF->SetFont(PDF_FONT_NAME_MAIN,'',13);
$PDF->SetY(45);
$PDF->Cell(0,5,"IDENTIFICACIÓN DEL SUJETO OBLIGADO",0,0,'L');

$PDF->SetFont(PDF_FONT_NAME_MAIN,'',11);
$PDF->SetY(60);
$TEXTO = "El/La (1) que suscribe, .........................................................(2) declara bajo juramento que los datos consignados en la presente son correctos, completos y fiel expresión de la verdad y que SI/NO(1) se encuentra incluido y/o alcanzado dentro de la \"Nómina de Funciones de Personas Expuestas Políticamente\" aprobada por la Unidad de Información Financiera, que ha leído y suscripto.";
$TEXTO .= "\n";
$TEXTO .= "\n";
$TEXTO .= "En caso afirmativo indicar: Cargo/ Función/ Jerarquía, o relación (con la Persona Expuesta Políticamente) (1): .............................................................................................................................................";
$TEXTO .= "\n";
$TEXTO .= "\n";
$TEXTO .= "Además, asume el compromiso de informar cualquier modificación que se produzca a este respecto, dentro de los treinta (30) días de ocurrida, mediante la presentación de una nueva declaración jurada.";
$TEXTO .= "\n";
$TEXTO .= "\n";
$TEXTO .= "Documento: Tipo (3) ........... N° ......................";
$TEXTO .= "\n";
$TEXTO .= "País y Autoridad de Emisión:................................................................................................................................";
$TEXTO .= "\n";
$TEXTO .= "Carácter invocado (4):..........................................................................................................................................";
$TEXTO .= "\n";
$TEXTO .= "Denominación de la persona jurídica (5):.............................................................................................................";
$TEXTO .= "\n";
$TEXTO .= "CUIT / CUIL / CDI (1) N°:.....................................................................................................................................";
$TEXTO .= "\n";
$TEXTO .= "Lugar y fecha:......................................................................................................................................................";
$TEXTO .= "\n";
$TEXTO .= "\n";
$TEXTO .= "\n";
$TEXTO .= "Firma:..................................................................";
$TEXTO .= "\n";
$TEXTO .= "\n";
$TEXTO .= "Certifico/Certificamos que la firma que antecede concuerda con la registrada en nuestros libros/fue puesta en mi/nuestra presencia (1).";
$TEXTO .= "\n";
$TEXTO .= "Firma y sello del Sujeto Obligado o de los funcionarios del Sujeto Obligado autorizados.";
$TEXTO .= "\n";
$TEXTO .= "\n";
$TEXTO .= "Observaciones:.....................................................................................................................................................";
$TEXTO .= "\n";
$TEXTO .= "..............................................................................................................................................................................";
$TEXTO .= "\n";
$TEXTO .= "..............................................................................................................................................................................";
$TEXTO .= "\n";
$TEXTO .= "..............................................................................................................................................................................";
$TEXTO .= "\n";
$TEXTO .= "..............................................................................................................................................................................";
$TEXTO .= "\n";
$TEXTO .= "\n";
$TEXTO .= "(1) Tachar lo que no corresponda. (2) Integrar con el nombre y apellido del cliente, en el caso de personas físicas, aun cuando en su representación firme un apoderado. (3) Indicar DNI, LE o LC para argentinos nativos. Para extranjeros: DNI extranjeros, Carné internacional, Pasaporte, Certificado provisorio, Documento de identidad del respectivo país, según corresponda. (4) Indicar titular, representante legal, apoderado. Cuando se trate de apoderado, el poder otorgado debe ser amplio y general y estar vigente a la fecha en que se suscriba la presente declaración. (5) Integrar sólo en los casos en que el firmante lo hace en carácter de apoderado o representante legal de una persona jurídica.";
$TEXTO .= "\n";


$PDF->MultiCell(0,0,$TEXTO,0,'J');


//----------------------------------------------------------------------------------
$PDF->AddPage();
$PDF->SetY(15);
$PDF->SetX(10);


$PDF->SetFont(PDF_FONT_NAME_MAIN,'B',13);
$PDF->Cell(0,5,"CONDICIONES GENERALES",0,0,'C');
$PDF->SetFont(PDF_FONT_NAME_MAIN,'',11);
$PDF->SetY(25);

$TEXTO = "1) El solicitante y cosolicitante suscribirá/n un pagaré sin protesto a favor de MIS por el total del préstamo de Ayuda Económica recibida, con mas la tasa de servicio, cargos, gastos y demás accesorios pactados, indicados en la presente solicitud.-";
$TEXTO .= "\n";
$TEXTO .= "2) El solicitante y cosolicitante reintegrará/n dicha suma en moneda de curso legal en cuotas mensuales y consecutivas hasta completar el pago total del importe recibido y sus accesorios. El número de dichas cuotas, la fecha de vencimiento de cada unas de ellas, su importe y la tasa de servicio, serán los indicados en la liquidación de la presente Ayuda Económica, documento que será suscrito por el solicitante y cosolicitante junto al pagaré indicado en la cláusula anterior.-";
$TEXTO .= "\n";
$TEXTO .= "3) Las cuotas serán abonadas de la siguiente forma: a) En efectivo, en horario bancario, en el domicilio de MIS sito en Gdor. Iriondo 2072 Local 2 Santo Tome Pcia. Santa Fe o donde lo indique la Mutual en el futuro; b) Mediante el procedimiento de débito directo por el cual autorizarán a la Mutual 22 de setiembre en forma irrevocable a que le/s practique los descuentos correspondientes en sus cuentas bancarias. Queda establecido que dicha autorización no lo/s desobliga/n frente a MIS, por cuanto la cancelación de las cuotas se producirá en la medida que los fondos retenidos ingresen efectivamente a MIS.-";
$TEXTO .= "\n";
$TEXTO .= "4) El solicitante y cosolicitante incurrirá/n en mora automática de pleno derecho, sin necesidad de requerimiento previo, en los siguientes supuestos: ";
$TEXTO .= "a) Falta de pago o pago parcial o defectuoso de una (1) sola de las cuotas pactadas a su respectivo vencimiento y/o los cargos convenidos, en la forma y plazo indicados. ";
$TEXTO .= "b) Si por cualquier causa resultase que hubo falsedad en los datos proporcionados al solicitar el préstamo. ";
$TEXTO .= "c) Si se ordenase alguna medida cautelar sobre el Solicitante y/o cosolicitante o sobre sus bienes u ocurrieran algunas otras circunstancias que afectaren la solvencia moral y material del Solicitante y/o cosolicitante. ";
$TEXTO .= "d) Si el Solicitante y o cosolicitante peticionara/n la formación de concurso preventivo o acuerdo preconcursal, su propia quiebra o ésta les fuere requeridas por terceros o dejare de cumplir cualquier otro préstamo u obligación hacia MIS. ";
$TEXTO .= "e) La extinción por cualquier causa del contrato de trabajo que lo vincula con la empleadora. ";
$TEXTO .= "f) La suspensión por cualquier causa que fuere del derecho del  Solicitante y/o cosolicitante a percibir la totalidad de su remuneración de su empleadora.";
$TEXTO .= "\n";
$TEXTO .= "El Solicitante y cosolicitante se obliga/n a notificar en forma fehaciente a MIS la existencia de cualesquiera de las causas indicadas en los incisos precedentes, dentro de las cuarenta y ocho (48) horas de producido o de la toma de conocimiento, según corresponda.";
$TEXTO .= "\n";
$TEXTO .= "5) Producida la mora MIS queda facultada para: ";
$TEXTO .= "a) Percibir un cargo moratorio que se establece en el 50 % de la tasa de servicio. ";
$TEXTO .= "b) Dar por decaídos todos los plazos, considerar a la obligación como si fuere de plazo vencido y a exigir la cancelación del monto total de la deuda, con más la tasa de servicio, cargo moratorios y demás accesorios, gastos, costas y costos, y a ejecutar el pagaré que garantiza esta obligación según se indica.";
$TEXTO .= "\n";
$TEXTO .= "6) El solicitante y cosolicitante, renuncia/n a reclamar u oponer la teoría de la imprevisión, lesión, abuso del derecho y a cuestionar la legalidad de esta Ayuda Económica. Renuncia/n en forma expresa a invocar la imposibilidad de pago y reconoce/n que las obligaciones asumidas se mantendrán vigentes y subsistentes, y resultarán exigibles por MIS hasta la efectiva y total cancelación de las sumas otorgadas por MIS en calidad de Ayuda Económica al solicitante, con mas sus accesorios legales. Asimismo el solicitante y cosolicitante autoriza/n a MIS, en caso de falta de pago o insolvencia manifiesta, a solicitar embargo sobre sus haberes y otros bienes libres de su propiedad, solicitar inhibiciones, anotación de litis, inclusive el desapoderamiento de bienes y la designación de depositario y de martillero para el caso de subasta.-";
$TEXTO .= "\n";
$TEXTO .= "7) A efectos de hacer uso de los servicios, el solicitante revestirá la calidad de Socio Adherente de MIS. La solicitud quedará confirmada con motivo del pedido y otorgamiento de una Ayuda Económica. La mutual considerará que es voluntad del Socio renunciar apenas culmine la total amortización del Crédito y sus accesorios o en el supuesto que el solicitante se encuentre en mora en el pago de la Ayuda Económica tomada.-";
$TEXTO .= "8) MIS podrá transferir y/o ceder la presente Ayuda Económica por cualquiera de los medios previstos en la ley, adquiriendo ella o los cesionarios los mismos derecho y/o beneficios y/o acciones de la cedente. De optar por la cesión prevista en los artículos 70 a 72 de la ley 24.441, la cesión de la Ayuda Económica  y su garantía podrá hacerse sin notificación al deudor y tendrá validez desde la fecha de su formalización. La cesión tendrá efecto desde la fecha en que opere la misma y sólo podrán oponerse contra el cesionario las excepciones previstas en los artículos indicados. No obstante en el supuesto que la cesión implique modificación del domicilio de pago deberá notificarse en forma fehaciente al deudor por cualquier medio, siendo válidas las notificaciones efectuadas por medio del Correo Argentino y/o cualquier otro servicio postal debidamente autorizado. Habiendo mediado modificación del domicilio de pago, no podrá oponerse excepción de pago documentado, con relación a los pagos practicados a anteriores cedentes con posterioridad a la notificación del nuevo domicilio de pago.-";
$TEXTO .= "\n";
$TEXTO .= "9) Conforme a lo establecido en los Art. 26 y 27 del reglamento del servicio de Ayuda Económica de MIS, el solicitante y cosolicitante autoriza/n a la Mutual a descontar, en el momento del otorgamiento de la presente Ayuda Económica, un importe que será destinado a la conformación del Fondo de Garantía Común con la finalidad de que en caso de fallecimiento del solicitante quede automáticamente cancelado el saldo de la Ayuda Económica. Dicho importe surge de la aplicación de la siguiente fórmula: (Capital x 0.3%o x Nº de cuotas). Se deja expresamente establecido que este fondo de Garantía Común no se aplicará a la cancelación de las cuotas que hallan vencido y se encuentran impagas a la fecha del fallecimiento del solicitante.-";
$TEXTO .= "\n";
$TEXTO .= "10) El solicitante y cosolicitante autorizan a MIS y/o a la Mutual 22 de Setiembre a debitar el importe adeudado mediante el débito directo de la Caja de Ahorro / Cuenta Corriente cuyo CBU es: ".$orden['MutualProductoSolicitud']['beneficio_cbu']." sirviendo la presente solicitud de autorización a dicho débito.";
$TEXTO .= "\n";
$TEXTO .= "11) El solicitante y cosolicitante declara/n bajo Juramento que: a) Los datos personales, laborares y/o comerciales, patrimoniales y económicos consignados en la presente solicitud son correctos, ciertos y verdaderos; b) No se encuentra/n fallido/s, concursado/s, inhabilitado/s ni sujeto/s a medidas cautelares y no cuenta/n con mas bienes que los denunciados; c) que el origen de los fondos con lo que cancelará la Ayuda Económica aquí requerida proviene de actividad lícita d) Conoce y acepta de plena conformidad las condiciones que regulan la  presente Ayuda Económica, así como las normas establecidas en el acuerdo suscripto entre la Mutual 22 de Setiembre y MIS;  e) Durante la vigencia de la Ayuda Económica se compromete a no solicitar ningún otro Préstamo, ni otorgar garantías, fianzas o avales cuando la suma de todos ellos supere el cincuenta por ciento (50%)  del valor afectable de su sueldo neto o ingresos mensuales comprobables.-";
$TEXTO .= "\n";
$TEXTO .= "12) Al momento de otorgar la Ayuda Económica se retendrá un doce por ciento (12%) del monto total del préstamo acordado en carácter de gastos administrativos. Asimismo se retendrá el valor de los impuestos correspondientes. Todos estos gastos, como así también todo impuesto que se deba abonar como consecuencia de esta solicitud, el otorgamiento del préstamo o su instrumentación están a cargo exclusivo del solicitante.-";
$TEXTO .= "\n";
$TEXTO .= "13) Los solicitantes deudores podrán cancelar anticipadamente, total o parcialmente el saldo de su deuda, lo que deberá ser comunicado a MIS con diez (10) días de anticipación. Para ello se calculará el valor de la deuda más los gastos administrativos al momento en que se efectivice el pago o acreditación de los fondos en la cuenta de la MUTUAL. El deudor se hará cargo de todos los gastos y costos que dicha cancelación originare.-";
$TEXTO .= "\n";
$TEXTO .= "14) El desembolso del préstamo constituirá la aceptación de la presente en todos sus términos quedando el solicitante y cosolicitante obligado/s por el presente Instrumento.-";
$TEXTO .= "\n";
$TEXTO .= "15) Para todos los efectos judiciales o extrajudiciales del presente, las partes constituyen domicilio en los precedentemente indicados, donde resultarán válidas todas las notificaciones e interpelaciones en caso de ejecución, y acuerdan someterse a la Jurisdicción de los Tribunales Ordinarios de la Ciudad de Santa Fe, renunciando expresamente a cualquier otro fuero o jurisdicción, que por cualquier causa pudiera corresponderles, inclusive el Federal.-";
$TEXTO .= "\n";

$PDF->MultiCell(0,0,$TEXTO,0,'J');
$PDF->ln(15);
$PDF->Cell(60,$PDF->H,"Firma del Solicitante",'T',0,'C');
$PDF->SetX(140);
$PDF->Cell(60,$PDF->H,"Firma del Cosolicitante",'T',0,'C');
$PDF->ln(10);
$PDF->Cell(60,$PDF->H,"Aclaración",'T',0,'C');
$PDF->SetX(140);
$PDF->Cell(60,$PDF->H,"Aclaración",'T',0,'C');
$y = $PDF->GetY() + 10;

$PDF->SetY($y);
$PDF->Cell(195,40,"",'TLRB',0,'C');
$PDF->ln(3);
$PDF->Cell(130,$PDF->H,"Reservado MIS",'',0,'L');
$PDF->SetX(140);
$PDF->Cell(55,$PDF->H,"Fecha Aprobación:      /        /",'',0,'L');
$PDF->ln(5);
$PDF->SetX(140);
$PDF->Cell(55,$PDF->H,"Ayuda Económica N°:",'',0,'L');
$PDF->ln(10);
$PDF->Cell(130,$PDF->H,"Observaciones",'',0,'L');

$PDF->ln(15);
$PDF->Cell(100,$PDF->H,"Aprobación Gerencia",'',0,'L');
$PDF->SetX(120);
$PDF->Cell(55,$PDF->H,"Firma del Responsable",'T',0,'C');


//----------------------------------------------------------------------------------
$PDF->AddPage();
$PDF->image(__DIR__ . DIRECTORY_SEPARATOR . "logotipo". DIRECTORY_SEPARATOR . "mis.png",170,10,30);
$PDF->SetY(30);
$PDF->SetX(10);

// $PDF->Cell(195,250,"",'TLRB',0,'C');
$PDF->Rect($PDF->GetX(),$PDF->GetY(),195,250);
$PDF->SetY(40);
$PDF->SetFont(PDF_FONT_NAME_MAIN,'B',15);
$PDF->Cell(0,5,"PAGARE A LA VISTA",0,0,'C');

$PDF->SetY(50);
$PDF->SetX(130);
$PDF->SetFont(PDF_FONT_NAME_MAIN,'B',12);
$PDF->Cell(0,5,"Por     $ _________________",0,0,'C');
$PDF->SetY(60);
$PDF->SetFont(PDF_FONT_NAME_MAIN,'I',10);
$PDF->Cell(0,5,"Santo Tomé, ______ de _______________________ de 20___.-",0,0,'L');
$PDF->ln(10);
$TEXTO = "Por suma recibida en préstamo a mi/nuestra entera satisfacción, PAGARE/MOS A LA VISTA, SIN PROTESTO (Art. 50 Decreto Ley 5965/63) a su sola presentación, a  Mutual Integral de Servicios (M.I.S), o a su orden,  la cantidad de pesos ______________________________________________________ $(______________) con más un interés compensatorio del ______ % nominal mensual. En caso de mora se devengará un interés punitorio del 50% de la tasa del interés compensatorio.";
$TEXTO .= "\n";
$TEXTO .= "Se amplía el plazo de presentación para el pago de este pagaré hasta 4 años a contar de la fecha de libramiento.- ";
$TEXTO .= "\n";
$TEXTO .= "Lugar de Pago: Gdor. Iriondo Nº 2072 Local 2, Santo Tomé, Pcia. de Santa Fe.-";
$TEXTO .= "\n";
$TEXTO .= "Mutual Integral de Servicios (M.I.S.) podrá transferir el presente pagaré por cualquiera de los medios previstos en la ley. De optar por la cesión prevista en los artículos 70 a 72 de la ley 24.441, la cesión del crédito y su garantía podrá hacerse sin notificación al deudor y tendrá validez desde la fecha de su formalización. La cesión tendrá efecto desde la fecha en que opere la misma y sólo podrán oponerse contra el cesionario las excepciones previstas en los artículos indicados. No obstante en el supuesto que la cesión implique modificación del domicilio de pago deberá notificarse en forma fehaciente al deudor por cualquier medio, siendo válidas las notificaciones efectuadas por medio del Correo Argentino y/o cualquier otro servicio postal debidamente autorizado. Habiendo mediado modificación del domicilio de pago, no podrá oponerse excepción de pago documentado, con relación a los pagos practicados a anteriores titulares con posterioridad a la notificación del nuevo domicilio de pago.-";
$TEXTO .= "\n";

$PDF->MultiCell(0,0,$TEXTO,0,'J');

$PDF->ln(10);
$PDF->SetFont(PDF_FONT_NAME_MAIN,'B',12);
$PDF->Cell(0,5,"Solicitante",0,0,'L');
$PDF->SetFont(PDF_FONT_NAME_MAIN,'I',10);
$PDF->ln(5);
$PDF->Cell(0,5,"Apellido y Nombre: " . $orden['MutualProductoSolicitud']['beneficiario_apenom'],0,0,'L');
$PDF->ln(5);
$PDF->Cell(0,5,"Domicilio: " . $orden['MutualProductoSolicitud']['beneficiario_calle']." N° ".$orden['MutualProductoSolicitud']['beneficiario_numero_calle'],0,0,'L');
$PDF->ln(5);
$PDF->Cell(0,5,"Localidad: " . $orden['MutualProductoSolicitud']['beneficiario_localidad'] . " (CP " . $orden['MutualProductoSolicitud']['beneficiario_cp'] . ") - ". $orden['MutualProductoSolicitud']['beneficiario_provincia'],0,0,'L');
$PDF->ln(5);
$PDF->Cell(80,5,"Documento: " . $orden['MutualProductoSolicitud']['beneficiario_tdocndoc'],0,0,'L');
$PDF->SetX(140);
$PDF->Cell(50,5,"Firma",'T',0,'C');



$PDF->ln(10);
$PDF->SetFont(PDF_FONT_NAME_MAIN,'B',12);
$PDF->Cell(0,5,"Cosolicitante",0,0,'L');
$PDF->SetFont(PDF_FONT_NAME_MAIN,'I',10);
$PDF->ln(5);
$PDF->Cell(0,5,"Apellido y Nombre: ______________________________________________",0,0,'L');
$PDF->ln(5);
$PDF->Cell(0,5,"Domicilio: _____________________________________________________",0,0,'L');
$PDF->ln(5);
$PDF->Cell(0,5,"Localidad: ____________________________________________________",0,0,'L');
$PDF->ln(5);
$PDF->Cell(0,5,"Documento: ___________________________________________________",0,0,'L');
$PDF->SetX(140);
$PDF->Cell(50,5,"Firma",'T',0,'C');


//----------------------------------------------------------------------------------
$PDF->AddPage();
$PDF->image(__DIR__ . DIRECTORY_SEPARATOR . "logotipo". DIRECTORY_SEPARATOR . "mis.png",170,10,30);
$PDF->SetY(30);
$PDF->SetX(10);

// $PDF->Cell(195,90,"",'TLRB',0,'C');
$PDF->Rect($PDF->GetX(),$PDF->GetY(),195,90);
$PDF->SetY(40);
$PDF->SetFont(PDF_FONT_NAME_MAIN,'I',11);
$PDF->MultiCell(0,0,"AUTORIZO a Mutual Integral de Servicios (M.I.S.) a que acredite, en la siguiente cuenta bancaria, la Ayuda Económica solicitada. A tales efectos, informo los datos de la misma.\n",0,'J');

$PDF->ln(10);
$PDF->Cell(0,5,"Nombre de la Entidad Bancaria: " . $orden['MutualProductoSolicitud']['beneficio_banco'],0,0,'L');
$PDF->ln(5);
$PDF->Cell(0,5,"Nombre y N° de Sucursal: " . $orden['MutualProductoSolicitud']['beneficio_sucursal'],0,0,'L');
$PDF->ln(5);
$PDF->Cell(0,5,"N° de Caja de Ahorro: " . $orden['MutualProductoSolicitud']['beneficio_cuenta'],0,0,'L');
$PDF->ln(5);
$PDF->Cell(0,5,"CBU: " . $orden['MutualProductoSolicitud']['beneficio_cbu'],0,0,'L');


$PDF->ln(15);
$PDF->Cell(0,5,"Apellido y Nombre: " . $orden['MutualProductoSolicitud']['beneficiario_apenom'],0,0,'L');
$PDF->ln(5);
$PDF->Cell(0,5,"Domicilio: " . $orden['MutualProductoSolicitud']['beneficiario_calle']." N° ".$orden['MutualProductoSolicitud']['beneficiario_numero_calle'],0,0,'L');
$PDF->ln(5);
$PDF->Cell(0,5,"Localidad: " . $orden['MutualProductoSolicitud']['beneficiario_localidad'] . " (CP " . $orden['MutualProductoSolicitud']['beneficiario_cp'] . ") - ". $orden['MutualProductoSolicitud']['beneficiario_provincia'],0,0,'L');
$PDF->ln(5);
$PDF->Cell(80,5,"Documento: " . $orden['MutualProductoSolicitud']['beneficiario_tdocndoc'],0,0,'L');
$PDF->SetX(140);
$PDF->Cell(50,5,"Firma",'T',0,'C');


$PDF->ln(20);
$PDF->SetFont(PDF_FONT_NAME_MAIN,'B',12);
$PDF->Cell(0,5,"RECIBO",0,0,'L');
$PDF->ln(10);

$PDF->SetFont(PDF_FONT_NAME_MAIN,'I',11);



//$PDF->Cell(195,0,"",'TLRB',0,'C');
$PDF->Rect($PDF->GetX(),$PDF->GetY(),195,100);

$TEXTO = "RECIBI/MOS de Mutual Integral de Servicios ( M.I.S),  la cantidad de Pesos _____________________________________________ ($ _____________) en concepto de Ayuda Económica,  conforme los términos y condiciones de nuestra solicitud de Ayuda Económica N°______________.-";
$TEXTO .= "\n";
$TEXTO .= "\n";
$TEXTO .= "Santo Tomé, ____ /____/ 20___.-";
$TEXTO .= "\n";

$PDF->MultiCell(0,11,$TEXTO,0,'J');

$PDF->ln(10);
$PDF->SetFont(PDF_FONT_NAME_MAIN,'B',12);
$PDF->Cell(0,5,"Solicitante",0,0,'L');
$PDF->SetFont(PDF_FONT_NAME_MAIN,'I',10);
$PDF->ln(5);
$PDF->Cell(0,5,"Apellido y Nombre: " . $orden['MutualProductoSolicitud']['beneficiario_apenom'],0,0,'L');
$PDF->ln(5);
$PDF->Cell(0,5,"Domicilio: " . $orden['MutualProductoSolicitud']['beneficiario_calle']." N° ".$orden['MutualProductoSolicitud']['beneficiario_numero_calle'],0,0,'L');
$PDF->ln(5);
$PDF->Cell(0,5,"Localidad: " . $orden['MutualProductoSolicitud']['beneficiario_localidad'] . " (CP " . $orden['MutualProductoSolicitud']['beneficiario_cp'] . ") - ". $orden['MutualProductoSolicitud']['beneficiario_provincia'],0,0,'L');
$PDF->ln(5);
$PDF->Cell(80,5,"Documento: " . $orden['MutualProductoSolicitud']['beneficiario_tdocndoc'],0,0,'L');
$PDF->SetX(140);
$PDF->Cell(50,5,"Firma",'T',0,'C');



$PDF->ln(10);
$PDF->SetFont(PDF_FONT_NAME_MAIN,'B',12);
$PDF->Cell(0,5,"Cosolicitante",0,0,'L');
$PDF->SetFont(PDF_FONT_NAME_MAIN,'I',10);
$PDF->ln(5);
$PDF->Cell(0,5,"Apellido y Nombre: ______________________________________________",0,0,'L');
$PDF->ln(5);
$PDF->Cell(0,5,"Domicilio: _____________________________________________________",0,0,'L');
$PDF->ln(5);
$PDF->Cell(0,5,"Localidad: ____________________________________________________",0,0,'L');
$PDF->ln(5);
$PDF->Cell(0,5,"Documento: ___________________________________________________",0,0,'L');
$PDF->SetX(140);
$PDF->Cell(50,5,"Firma",'T',0,'C');


#############################################################################################
# AUTORIZACION COBRO POR DEBITO DIRECTO
#############################################################################################

$nroSolicitud = $orden['MutualProductoSolicitud']['nro_print'];
$fecha = $util->armaFecha($orden['MutualProductoSolicitud']['fecha']);
$usuario = $orden['MutualProductoSolicitud']['user_created'];
$vendedor = $orden['MutualProductoSolicitud']['vendedor_nombre_min'];

$membrete = array(
'L1' => Configure::read('APLICACION.nombre_fantasia'),
'L2' => Configure::read('APLICACION.domi_fiscal'),
'L3' => "TEL: " . Configure::read('APLICACION.telefonos') ." - email: ".Configure::read('APLICACION.email')
);

$PDF->AddPage();
$PDF->reset();

$PDF->SetFont('courier','',12);


$PDF->SetFont(PDF_FONT_NAME_MAIN,'B',14);
$PDF->Cell(0,5,$membrete['L1'],0);
$PDF->Ln(5);
$PDF->SetFont(PDF_FONT_NAME_MAIN,'',8);
$PDF->Cell(0,5,$membrete['L2'],0);
$PDF->Ln(3);
$PDF->SetFont(PDF_FONT_NAME_MAIN,'',8);
$PDF->Cell(0,5,$membrete['L3'],0);
$PDF->Ln(4);

$PDF->SetY(10);
$PDF->imprimirDatosGenerales($nroSolicitud,$fecha,$usuario,$vendedor);
$size = 16;
$PDF->linea[1] = array(
		'posx' => 10,
		'ancho' => 190,
		'texto' => "AUTORIZACION DE COBRANZA POR DEBITO DIRECTO",
		'borde' => '',
		'align' => 'C',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#D8DBD4',
		'size' => $size
);
$PDF->Imprimir_linea();


$PDF->SetFontSize(10);
$TEXTO = "Por la presente, AUTORIZO a ".Configure::read('APLICACION.nombre_fantasia').", a debitar de mi Cuenta Corriente / Caja de ahorros detallada en la presente, los montos que correspondan según el plan comercial al que adhiero, en forma mensual y consecutiva a partir de la fecha de aprobación de la operación solicitada, en un todo de acuerdo con los datos consignados en esta autorización.\n";
$PDF->MultiCell(0,11,$TEXTO);

$PDF->SetY(62);
$PDF->imprimirDatosTitular($orden);
$size = 10;
$sized = 13;

#############################################################################################
# CUENTA PARA DEBITO
#############################################################################################
$PDF->imprimirDatosCuentaDebito($orden);



#############################################################################################
# DATOS PLAN COMERCIAL
#############################################################################################
$PDF->ln(4);
$PDF->linea[1] = array(
		'posx' => 10,
		'ancho' => 190,
		'texto' => utf8_decode("Datos del Plan Comercial"),
		'borde' => 'B',
		'align' => 'L',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#D8DBD4',
		'size' => $size
);
$PDF->Imprimir_linea();
$PDF->ln(4);


$PDF->linea[1] = array(
		'posx' => 10,
		'ancho' => 55,
		'texto' => utf8_decode("EMPRESA PROVEEDORA:"),
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#D8DBD4',
		'size' => $sized
);
$PDF->linea[2] = array(
		'posx' => 65,
		'ancho' => 90,
		'texto' => utf8_decode($orden['MutualProductoSolicitud']['proveedor_full_name']),
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#D8DBD4',
		'size' => $sized
);
$PDF->linea[3] = array(
		'posx' => 155,
		'ancho' => 15,
		'texto' => utf8_decode("CUIT:"),
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#D8DBD4',
		'size' => $sized
);
$PDF->linea[4] = array(
		'posx' => 170,
		'ancho' => 30,
		'texto' => utf8_decode($orden['MutualProductoSolicitud']['proveedor_cuit']),
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#D8DBD4',
		'size' => $sized
);
$PDF->Imprimir_linea();


$PDF->linea[1] = array(
		'posx' => 10,
		'ancho' => 55,
		'texto' => utf8_decode("Monto Total Autorizado: $"),
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#D8DBD4',
		'size' => $size
);
$PDF->linea[2] = array(
		'posx' => 65,
		'ancho' => 22,
		'texto' => $util->nf($orden['MutualProductoSolicitud']['importe_total']),
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#D8DBD4',
		'size' => $size
);
$PDF->linea[3] = array(
		'posx' => 87,
		'ancho' => 43,
		'texto' => utf8_decode("Cantidad de Cuotas:"),
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#D8DBD4',
		'size' => $size
);
$PDF->linea[4] = array(
		'posx' => 130,
		'ancho' => 7,
		'texto' => utf8_decode($orden['MutualProductoSolicitud']['cuotas_print']),
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#D8DBD4',
		'size' => $size
);
$PDF->linea[5] = array(
		'posx' => 137,
		'ancho' => 40,
		'texto' => utf8_decode("Monto de Cuota: $"),
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#D8DBD4',
		'size' => $size
);
$PDF->linea[6] = array(
		'posx' => 177,
		'ancho' => 23,
		'texto' => $util->nf($orden['MutualProductoSolicitud']['importe_cuota']),
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#D8DBD4',
		'size' => $size
);
$PDF->Imprimir_linea();

#############################################################################################
# TEXTO
#############################################################################################
$PDF->ln(4);
$PDF->SetFontSize(10);
$TEXTO = "Dejo Expresa constancia que en caso de no poderse realizar la cobranza de la forma pactada, AUTORIZO en forma expresa a ".Configure::read('APLICACION.nombre_fantasia')." o a la empresa proveedora, a seguir realizando los descuentos correspondientes a la total cancelación de las obligaciones por mi contraídas en este acto, con mas los intereses y gastos por mora que pudieren corresponder.-\n\n";
$TEXTO .= "Asimismo, AUTORIZO en forma expresa a ".Configure::read('APLICACION.nombre_fantasia')." a que en caso de no poseer fondos de manera consecutiva en la cuenta indicada, o de cambiar la cuenta en la que se acreditan mis haberes, la cobranza sea direccionada a la cuenta de mi titularidad que posea fondos para la cancelación de las obligaciones contraídas.-\n";
$PDF->MultiCell(0,11,$TEXTO);


//FONDO DE ASISTENCIA


if(!isset($orden['MutualProductoSolicitud']['OrdenDescuentoSeguro']) && !empty($orden['MutualProductoSolicitud']['OrdenDescuentoSeguro']['importe_total'])):
$PDF->ln(2);
$PDF->linea[1] = array(
		'posx' => 10,
		'ancho' => 190,
		'texto' => "COBERTURA POR RIESGO CONTINGENTE",
		'borde' => '',
		'align' => 'C',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#D8DBD4',
		'size' => 11
);
$PDF->Imprimir_linea();
$PDF->SetFontSize(11);
$TEXTO = "AUTORIZO en forma expresa a ".Configure::read('APLICACION.nombre_fantasia')." ";
$TEXTO .= "a descontar de mis haberes la cantidad de ".$orden['MutualProductoSolicitud']['OrdenDescuentoSeguro']['cuotas']." (".$orden['MutualProductoSolicitud']['OrdenDescuentoSeguro']['total_cuota_cantidad_letras'].") cuotas mensuales y consecutivas de $ ".$orden['MutualProductoSolicitud']['OrdenDescuentoSeguro']['importe_cuota']." (PESOS ".$orden['MutualProductoSolicitud']['OrdenDescuentoSeguro']['total_cuota_letras'].") cada una a favor de ______________________ en concepto de pago de la cobertura por Riesgo Contingente a partir del ___/_____.-\n";
$PDF->MultiCell(0,11,$TEXTO);
elseif($orden['MutualProductoSolicitud']['fdoas']):
$PDF->ln(2);
$PDF->linea[1] = array(
		'posx' => 10,
		'ancho' => 190,
		'texto' => "COBERTURA POR RIESGO CONTINGENTE",
		'borde' => '',
		'align' => 'C',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#D8DBD4',
		'size' => 11
);
$PDF->Imprimir_linea();
$PDF->SetFontSize(11);
$TEXTO = "AUTORIZO en forma expresa a ".Configure::read('APLICACION.nombre_fantasia')." ";
$TEXTO .= "a descontar de mis haberes la cantidad de ".$orden['MutualProductoSolicitud']['fdoas_total_cuota_cantidad']." (".$orden['MutualProductoSolicitud']['fdoas_total_cuota_cantidad_letras'].") cuotas mensuales y consecutivas de $ ".$orden['MutualProductoSolicitud']['fdoas_total_cuota']." (PESOS ".$orden['MutualProductoSolicitud']['fdoas_total_cuota_letras'].") cada una a favor de ______________________ en concepto de pago de la cobertura por Riesgo Contingente a partir del ___/_____.-\n";
$PDF->MultiCell(0,11,$TEXTO);

endif;



$PDF->ln(20);
$PDF->firmaSocio();
$PDF->ln(4);
$PDF->barCode($orden['MutualProductoSolicitud']['barcode']);


// $PDF->ln(40);

// $PDF->firmaSocio();

############################
# INSTRUCCION DE PAGO
############################
$PDF->AddPage();
$PDF->reset();
$PDF->ln(4);
$PDF->imprimirDatosGenerales($nroSolicitud,$fecha,$usuario,$vendedor);
$size = 10;
$size = 16;
$PDF->linea[1] = array(
		'posx' => 10,
		'ancho' => 190,
		'texto' => "INSTRUCCION DE PAGO",
		'borde' => '',
		'align' => 'C',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#D8DBD4',
		'size' => $size
);
$PDF->Imprimir_linea();
$PDF->ln(4);
$size = 11;
$PDF->SetFontSizeConf($size);
$TEXTO = "Por medio de la presente, la que suscribe ".$orden['MutualProductoSolicitud']['beneficiario_apenom'].", con ".$orden['MutualProductoSolicitud']['beneficiario_tdocndoc'].", como solicitante y adjudicatario del crédito según Solicitud Nº ".$orden['MutualProductoSolicitud']['nro_print'].", INSTRUYO y ORDENO irrevocablemente a _______________________, para que los fondos netos resultantes del mismo sean pagados de la siguiente manera:\n";
$PDF->MultiCell(0,11,$TEXTO);

//$orden['MutualProductoSolicitudInstruccionPago'] = null;

if(!isset($orden['MutualProductoSolicitudInstruccionPago']) || empty($orden['MutualProductoSolicitudInstruccionPago'])):
$PDF->linea[1] = array(
		'posx' => 10,
		'ancho' => 102,
		'texto' => "1) A mi orden personal, el importe de pesos",
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#D8DBD4',
		'size' => $size
);
$PDF->linea[2] = array(
		'posx' => 112,
		'ancho' => 88,
		'texto' => "",
		'borde' => 'B',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#D8DBD4',
		'size' => $size
);
$PDF->Imprimir_linea();

$PDF->linea[1] = array(
		'posx' => 10,
		'ancho' => 40,
		'texto' => "2) A la orden de",
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#D8DBD4',
		'size' => $size
);
$PDF->linea[2] = array(
		'posx' => 50,
		'ancho' => 150,
		'texto' => "",
		'borde' => 'B',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#D8DBD4',
		'size' => $size
);
$PDF->Imprimir_linea();


$PDF->linea[1] = array(
		'posx' => 10,
		'ancho' => 40,
		'texto' => "3) A la orden de",
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#D8DBD4',
		'size' => $size
);
$PDF->linea[2] = array(
		'posx' => 50,
		'ancho' => 150,
		'texto' => "",
		'borde' => 'B',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#D8DBD4',
		'size' => $size
);
$PDF->Imprimir_linea();


$PDF->linea[1] = array(
		'posx' => 10,
		'ancho' => 40,
		'texto' => "4) A la orden de",
		'borde' => '',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#D8DBD4',
		'size' => $size
);
$PDF->linea[2] = array(
		'posx' => 50,
		'ancho' => 150,
		'texto' => "",
		'borde' => 'B',
		'align' => 'L',
		'fondo' => 0,
		'style' => '',
		'colorf' => '#D8DBD4',
		'size' => $size
);
$PDF->Imprimir_linea();
else:
$n = 1;
$TEXTO = "";
foreach($orden['MutualProductoSolicitudInstruccionPago'] as $instruccion):
$TEXTO .= "$n)" . $instruccion['a_la_orden_de'] . ", en concepto de " . $instruccion['concepto'] . " por un importe de PESOS $ " . $util->nf($instruccion['importe']) . ".-\n";
$n++;
endforeach;
$PDF->MultiCell(0,11,$TEXTO);
endif;
$TEXTO = "Sin mas, saludo a Uds. muy atentamente.\n";
$PDF->MultiCell(0,11,$TEXTO);

$PDF->ln(20);

$PDF->firmaSocio();

$PDF->ln(4);
$PDF->barCode($orden['MutualProductoSolicitud']['barcode']);

$PDF->ln(20);

$PDF->linea[1] = array(
		'posx' => 10,
		'ancho' => 190,
		'texto' => "",
		'borde' => 'T',
		'align' => 'L',
		'fondo' => 0,
		'style' => 'B',
		'colorf' => '#D8DBD4',
		'size' => $size
);
$PDF->Imprimir_linea();

////////////////////////////////////
// IMPRIMIR SOLICITUD DE AFILIACION
///////////////////////////////////
if(!isset($orden['Socio']['id']) || empty($orden['Socio']['id'])):

	$membrete = array(
			'L1' => Configure::read('APLICACION.nombre_fantasia'),
			'L2' => Configure::read('APLICACION.domi_fiscal'),
			'L3' => Configure::read('APLICACION.telefonos')." - INAES ".Configure::read('APLICACION.matricula_inaes')
	);
	
	$PDF->AddPage();
	$PDF->reset();
	// $PDF->ln(4);
	
	$PDF->SetY(10);
	
	
	$PDF->SetFont('courier','',12);
	
	
	$PDF->SetFont(PDF_FONT_NAME_MAIN,'B',14);
	$PDF->Cell(0,5,$membrete['L1'],0);
	$PDF->Ln(5);
	$PDF->SetFont(PDF_FONT_NAME_MAIN,'',8);
	$PDF->Cell(0,5,$membrete['L2'],0);
	$PDF->Ln(3);
	$PDF->SetFont(PDF_FONT_NAME_MAIN,'',8);
	$PDF->Cell(0,5,$membrete['L3'],0);
	$PDF->Ln(4);
	
	
	$size = 20;
	$PDF->linea[1] = array(
			'posx' => 10,
			'ancho' => 190,
			'texto' => "SOLICITUD DE AFILIACION",
			'borde' => '',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => $size
	);
	$PDF->Imprimir_linea();
	
	$PDF->SetFontSize(12);
	$TEXTO = "Sres. Miembros del Consejo Directivo:\n\nPor la Presente me dirijo a Uds., a los efectos de solicitarles mi inscripción como socio/a de la ".Configure::read('APLICACION.nombre_fantasia').",de acuerdo a lo estipulado en los Estatutos Sociales de la Entidad, para lo cual, adjunto los datos que a continuación se detallan:.\n";
	$PDF->MultiCell(0,11,$TEXTO,0,'J');
	
	$PDF->imprimirDatosTitular($orden);
	$PDF->imprimirDatosCuentaDebito($orden);
	$PDF->Ln(4);
	$PDF->SetFontSize(12);
	$TEXTO = "Asimismo, autorizo a la ".Configure::read('APLICACION.nombre_fantasia')." a descontar de mis haberes mensuales, a través de los códigos que correspondan, los montos equivalentes a la Cuota Social (en forma mensual y consecutiva), como así también los que se generen por Fondos de asistencia, seguros, y todo otro consumo o gasto que surja de los comprobantes pertinentes.-\n\nDeclaro bajo juramento que los datos consignados en la presente planilla son auténticos, comprometiéndome a comunicar a la MUTUAL todo cambio que se produzca, dentro de las 72 hs. de acontecido el mismo, adjuntando para ello la documentación que la entidad me solicite a tal efecto.-\n";
	$PDF->MultiCell(0,11,$TEXTO,0,'J');
	
	$PDF->ln(20);
	
	$PDF->firmaSocio();
	
	$PDF->ln(4);
	$PDF->barCode($orden['MutualProductoSolicitud']['barcode']);

endif;


if(isset($orden['MutualProductoSolicitud']['proveedor_plan_anexos']) && !empty($orden['MutualProductoSolicitud']['proveedor_plan_anexos'])){
    foreach($orden['MutualProductoSolicitud']['proveedor_plan_anexos'] as $anexo){
        if($anexo == "ocom_imprime_auto_debito_nacion") $PDF->imprimirAutorizacionBancoNacion(__DIR__ . DIRECTORY_SEPARATOR . "logotipo". DIRECTORY_SEPARATOR . "bna2.png",$orden);
        if($anexo == "ocom_imprime_auto_debito_bcocba") $PDF->imprimirAutorizacionDebitoBcoCordoba($orden); 
        if($anexo == "ocom_imprime_auto_debito_margen") $PDF->imprimirAutorizacionDebitoMargenComercial($orden); 
        if($anexo == "ocom_imprime_pago_directo_rio") $PDF->imprimeAutoPagoDirectoSantanderRio($orden);
        if($anexo == "ocom_imprime_mutuo_minuta") $PDF->imprimeMinutaMutuo($orden);
        if($anexo == "ocom_imprime_mutuo") $PDF->imprimeContratoMutuo($orden);
        if($anexo == "ocom_imprime_pago_directo_bco_pcia_bsas") $PDF->imprimeAutoPagoDirectoBancoPciaBsAs(__DIR__ . DIRECTORY_SEPARATOR . "logotipo". DIRECTORY_SEPARATOR . "logo_bco_pcia_bsas.png",$orden);   
        if($anexo == "ocom_modelo_liquidacion") $PDF->imprime_modelo_liquidacion_cuotas($orden);
        if($anexo == "ocom_imprime_auto_debito_cuenca") $PDF->imprime_autorizacion_debito_cuenca($orden);
        if($anexo == "ocom_imprime_auto_tarjeta_debito") {$PDF->imprimeAutoDebitoTarjeta($orden);}
        if($anexo == "ocom_imprime_auto_tarjeta_debito_ii") {$PDF->imprimeAutoDebitoTarjetaModeloII($orden);}
    }
}


//$INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
//
////$PDF->imprimirAutorizacionBancoNacion(__DIR__ . DIRECTORY_SEPARATOR . "logotipo". DIRECTORY_SEPARATOR . "bco_nacion.jpg",$orden);
////$PDF->imprimirAutorizacionDebitoBcoCordoba($orden);
//
////$PDF->imprimirAutorizacionDebitoMargenComercial($orden);
//
//if(isset($INI_FILE['general']['ocom_imprime_auto_debito_nacion']) && $INI_FILE['general']['ocom_imprime_auto_debito_nacion'] == 1){
//    $PDF->imprimirAutorizacionBancoNacion(__DIR__ . DIRECTORY_SEPARATOR . "logotipo". DIRECTORY_SEPARATOR . "bco_nacion.jpg",$orden);
//}
//if(isset($INI_FILE['general']['ocom_imprime_auto_debito_bcocba']) && $INI_FILE['general']['ocom_imprime_auto_debito_bcocba'] == 1){
//    $PDF->imprimirAutorizacionDebitoBcoCordoba($orden);
//}
//
//if(isset($INI_FILE['general']['ocom_imprime_auto_debito_margen']) && $INI_FILE['general']['ocom_imprime_auto_debito_margen'] == 1){
//    $PDF->imprimirAutorizacionDebitoMargenComercial($orden);
//}
//
//if(isset($INI_FILE['general']['ocom_imprime_pago_directo_rio']) && $INI_FILE['general']['ocom_imprime_pago_directo_rio'] == 1){
//    $PDF->imprimeAutoPagoDirectoSantanderRio($orden);
//}

$PDF->Output("solicitud_credito_".$orden['MutualProductoSolicitud']['id'].".pdf");

?>