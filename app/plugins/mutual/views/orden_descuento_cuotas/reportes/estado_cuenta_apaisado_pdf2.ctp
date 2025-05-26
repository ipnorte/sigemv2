<?php 

//debug($cuotas);
//exit;

App::import('Vendor','listado_pdf');

$PDF = new ListadoPDF("L");
$PDF->PIEUser=true;

$PDF->SetTitle("Estado de Cuenta Socio #".$socio['Socio']['id']);
$PDF->SetFontSizeConf(8.5);

//$PDF->AddPage();

$PDF->Open();

#TITULO DEL REPORTE#
$PDF->textoHeader =  'ESTADO DE '.($solo_deuda == 1 ? 'DEUDA': 'CUENTA').' DEL SOCIO (S.E.U.O.)';
$PDF->titulo['titulo1'] = '';
$PDF->titulo['titulo3'] = $this->requestAction('/config/global_datos/valor/'.$socio['Persona']['tipo_documento'].'/concepto_1') .' '. $socio['Persona']['documento'] . '-' . $socio['Persona']['apellido'] .', '.$socio['Persona']['nombre'];
$PDF->titulo['titulo2'] = "PERIODO ".$util->periodo($periodo_d,true,'/')." A " . $util->periodo($periodo_h,true,'/');


$cero = 0;
// ORDEN  	TIPO / NUMERO  	PROVEEDOR / PRODUCTO  	CUOTA  	CONCEPTO  	VTO  	ESTADO  	SIT  	IMPORTE  	PAGADO  	SALDO CUOTA 
// anchos de columnas 
// 277
//$W = array(11,20,25,70,12,35,20,13,20,17,17,17);
$W = array(11,20,25,66,18,10,32,20,3,20,17,17,17);
$L1 = $PDF->armaAnchoColumnas($W);


$PDF->bMargen = 10;
//$PDF->AddPage();	

$fontSize = 8;
$PDF->encabezado = array();

#imprimo los datos del socio

$backColor = "#D8DBD4";
$sizeSocio = 9;

$PDF->encabezado[0] = array();
$PDF->encabezado[0][0] = array(
			'posx' => $L1[0],
			'ancho' => 15,
			'texto' => 'SOCIO ',
			'borde' => '',
			'align' => 'L',
			'fondo' => 1,
			'style' => '',
			'colorf' => $backColor,
			'size' => $sizeSocio
	);
$PDF->encabezado[0][1] = array(
			'posx' => $L1[0] + 15,
			'ancho' => 15,
			'texto' => '#'.$socio['Socio']['id'],
			'borde' => '',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => $backColor,
			'size' => $sizeSocio
	);	
$PDF->encabezado[0][2] = array(
			'posx' => $L1[0] + 30,
			'ancho' => 20,
			'texto' => 'ESTADO: ',
			'borde' => '',
			'align' => 'R',
			'fondo' => 1,
			'style' => '',
			'colorf' => $backColor,
			'size' => $sizeSocio
	);
$PDF->encabezado[0][3] = array(
			'posx' => $L1[0] + 50,
			'ancho' => 20,
			'texto' => ($socio['Socio']['activo'] == 1 ? 'VIGENTE' : 'NO VIGENTE'),
			'borde' => '',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => $backColor,
			'size' => $sizeSocio
	);	
$PDF->encabezado[0][4] = array(
			'posx' => $L1[0] + 70,
			'ancho' => 40,
			'texto' => 'ULTIMA CALIFICACION: ',
			'borde' => '',
			'align' => 'R',
			'fondo' => 1,
			'style' => '',
			'colorf' => $backColor,
			'size' => $sizeSocio
	);
$PDF->encabezado[0][5] = array(
			'posx' => $L1[0] + 110,
			'ancho' => 50,
			'texto' => $util->globalDato($socio['Socio']['calificacion']),
			'borde' => '',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => 9
	);
$PDF->encabezado[0][6] = array(
			'posx' => $L1[0] + 160,
			'ancho' => 40,
			'texto' => 'FECHA CALIFICACION: ',
			'borde' => '',
			'align' => 'R',
			'fondo' => 1,
			'style' => '',
			'colorf' => $backColor,
			'size' => $sizeSocio
	);
$PDF->encabezado[0][7] = array(
			'posx' => $L1[0] + 200,
			'ancho' => 77,
			'texto' => $util->armaFecha($socio['Socio']['fecha_calificacion']),
			'borde' => '',
			'align' => 'L',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => $backColor,
			'size' => $sizeSocio
	);						
$PDF->ln(5);
#imprimo los titulos de las columnas
$PDF->encabezado[1] = array();	

$PDF->encabezado[1][0] = array(
			'posx' => $L1[0],
			'ancho' => $W[0],
			'texto' => 'ORDEN',
			'borde' => 'LTB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSize
	);	
$PDF->encabezado[1][1] = array(
			'posx' => $L1[1],
			'ancho' => $W[1],
			'texto' => 'ORGANISMO',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSize
	);	
$PDF->encabezado[1][2] = array(
			'posx' => $L1[2],
			'ancho' => $W[2],
			'texto' => 'TIPO #NUMERO',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSize
	);
$PDF->encabezado[1][3] = array(
			'posx' => $L1[3],
			'ancho' => $W[3],
			'texto' => 'PROVEEDOR / PRODUCTO',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSize
	);
$PDF->encabezado[1][4] = array(
			'posx' => $L1[4],
			'ancho' => $W[4],
			'texto' => 'SOLICITADO',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSize
	);
$PDF->encabezado[1][5] = array(
			'posx' => $L1[5],
			'ancho' => $W[5],
			'texto' => 'CUOTA',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSize
	);
$PDF->encabezado[1][6] = array(
			'posx' => $L1[6],
			'ancho' => $W[6],
			'texto' => 'CONCEPTO',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSize
	);

$PDF->encabezado[1][7] = array(
			'posx' => $L1[7],
			'ancho' => $W[7],
			'texto' => 'VTO / PAGO',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSize
	);	
	
$PDF->encabezado[1][8] = array(
			'posx' => $L1[8],
			'ancho' => $W[8],
			'texto' => '',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSize
	);	
	
$PDF->encabezado[1][9] = array(
			'posx' => $L1[9],
			'ancho' => $W[9],
			'texto' => 'SITUACION',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSize
	);
$PDF->encabezado[1][10] = array(
			'posx' => $L1[10],
			'ancho' => $W[10],
			'texto' => 'IMPORTE',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSize
	);
$PDF->encabezado[1][11] = array(
			'posx' => $L1[11],
			'ancho' => $W[11],
			'texto' => 'PAGADO',
			'borde' => 'TB',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSize
	);
$PDF->encabezado[1][12] = array(
			'posx' => $L1[12],
			'ancho' => $W[12],
			'texto' => 'SALDO',
			'borde' => 'TBR',
			'align' => 'C',
			'fondo' => 1,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSize
	);						
$PDF->AddPage();	
//$PDF->Imprimir_linea();

$PDF->Reset();
$fontSize = 8;

if(!empty($proveedor_razon_social)):

	$PDF->linea[0] = array(
				'posx' => $L1[0],
				'ancho' => 25,
				'texto' => "PROVEEDOR: ",
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => '',
				'colorf' => $backColor,
				'size' => 10
		);

	$PDF->linea[1] = array(
				'posx' => $L1[0] + 25,
				'ancho' => 277 - 25,
				'texto' => $proveedor_razon_social,
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => $backColor,
				'size' => 10
		);
//
	$PDF->Imprimir_linea();	

endif;

if(!empty($codigo_organismo)):

	$PDF->linea[0] = array(
				'posx' => $L1[0],
				'ancho' => 25,
				'texto' => "ORGANISMO: ",
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => '',
				'colorf' => $backColor,
				'size' => 10
		);

	$PDF->linea[1] = array(
				'posx' => $L1[0] + 25,
				'ancho' => 277 - 25,
				'texto' => $util->globalDato($codigo_organismo),
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => $backColor,
				'size' => 10
		);
//
	$PDF->Imprimir_linea();	

endif;

$periodo = null;
$primero = true;
$ACU_IMPO_CUOTA = $ACU_PAGO_CUOTA = $ACU_SALDO_CUOTA = $ACU_SALDO_CUOTA_ACUM = 0;

foreach($cuotas as $cuota):

    if($cuota['tipo_registro'] == 'SALDO_ANTERIOR'):
        $ACU_SALDO_CUOTA = $ACU_SALDO_CUOTA_ACUM = $cuota['saldo_conciliado'];
    endif;    
    
    if($periodo != $cuota['periodo']):
        $periodo = $cuota['periodo'];
        if($primero):
            $primero = false;
            $PDF->linea[0] = array(
                        'posx' => $L1[0],
                        'ancho' => 277,
                        'texto' => $util->periodo($cuota['periodo'],true,'/'),
                        'borde' => '',
                        'align' => 'L',
                        'fondo' => 1,
                        'style' => 'B',
                        'colorf' => $backColor,
                        'size' => 10
                );

            $PDF->Imprimir_linea();        
        else:
            
            //IMPRIMO EL TOTAL DEL PERIODO
            $PDF->linea[9] = array(
                        'posx' => $L1[9],
                        'ancho' => $W[9],
                        'texto' => 'TOTAL PERIODO',
                        'borde' => 'T',
                        'align' => 'C',
                        'fondo' => 0,
                        'style' => '',
                        'colorf' => '#ccc',
                        'size' => $fontSize
                );
            $PDF->linea[10] = array(
                        'posx' => $L1[10],
                        'ancho' => $W[10],
                        'texto' => number_format($ACU_IMPO_CUOTA,2),
                        'borde' => 'T',
                        'align' => 'R',
                        'fondo' => 0,
                        'style' => '',
                        'colorf' => '#ccc',
                        'size' => $fontSize
                );
            $PDF->linea[11] = array(
                        'posx' => $L1[11],
                        'ancho' => $W[11],
                        'texto' => number_format($ACU_PAGO_CUOTA,2),
                        'borde' => 'T',
                        'align' => 'R',
                        'fondo' => 0,
                        'style' => '',
                        'colorf' => '#ccc',
                        'size' => $fontSize
                );
            $PDF->linea[12] = array(
                        'posx' => $L1[12],
                        'ancho' => $W[12],
                        'texto' => number_format($ACU_SALDO_CUOTA,2),
                        'borde' => 'T',
                        'align' => 'R',
                        'fondo' => 0,
                        'style' => '',
                        'colorf' => '#ccc',
                        'size' => $fontSize
                );		
            $PDF->Imprimir_linea();
            
            $PDF->linea[9] = array(
                        'posx' => $L1[9],
                        'ancho' => $W[9],
                        'texto' => 'TOTAL ACUMULADO A ' . $util->periodo($periodo_actual,true,'/'),
                        'borde' => '',
                        'align' => 'C',
                        'fondo' => 0,
                        'style' => 'B',
                        'colorf' => '#ccc',
                        'size' => $fontSize
                );
            $PDF->linea[10] = array(
                        'posx' => $L1[10],
                        'ancho' => $W[10],
                        'texto' => '',
                        'borde' => '',
                        'align' => 'R',
                        'fondo' => 0,
                        'style' => '',
                        'colorf' => '#ccc',
                        'size' => $fontSize
                );
            $PDF->linea[11] = array(
                        'posx' => $L1[11],
                        'ancho' => $W[11],
                        'texto' => '',
                        'borde' => '',
                        'align' => 'R',
                        'fondo' => 0,
                        'style' => '',
                        'colorf' => '#ccc',
                        'size' => $fontSize
                );
            $PDF->linea[12] = array(
                        'posx' => $L1[12],
                        'ancho' => $W[12],
                        'texto' => number_format($ACU_SALDO_CUOTA_ACUM,2),
                        'borde' => '',
                        'align' => 'R',
                        'fondo' => 0,
                        'style' => 'B',
                        'colorf' => '#ccc',
                        'size' => $fontSize
                );		
            $PDF->Imprimir_linea();	            
            
            //imprimo el periodo
            
            $PDF->linea[0] = array(
                        'posx' => $L1[0],
                        'ancho' => 277,
                        'texto' => $util->periodo($cuota['periodo'],true,'/'),
                        'borde' => '',
                        'align' => 'L',
                        'fondo' => 1,
                        'style' => 'B',
                        'colorf' => $backColor,
                        'size' => 10
                );

            $PDF->Imprimir_linea();	            
        endif;
        
        
        if($ACU_SALDO_CUOTA_ACUM != 0):
            
            $PDF->linea[11] = array(
                        'posx' => $L1[11],
                        'ancho' => $W[11],
                        'texto' => 'SALDO ANTERIOR',
                        'borde' => '',
                        'align' => 'R',
                        'fondo' => 0,
                        'style' => 'B',
                        'colorf' => '#ccc',
                        'size' => $fontSize
                );
            $PDF->linea[12] = array(
                        'posx' => $L1[12],
                        'ancho' => $W[12],
                        'texto' => number_format($ACU_SALDO_CUOTA_ACUM,2),
                        'borde' => 'B',
                        'align' => 'R',
                        'fondo' => 0,
                        'style' => 'B',
                        'colorf' => '#ccc',
                        'size' => $fontSize
                );			
            $PDF->Imprimir_linea();		            
            
        endif;
        $periodo_actual = $cuota['periodo'];
        $ACU_IMPO_CUOTA = $ACU_PAGO_CUOTA = $ACU_SALDO_CUOTA = 0;
        
    endif;
    
    if($cuota['tipo_registro'] == 'CUOTA'):

        $ACU_IMPO_CUOTA += $cuota['importe'];
        $ACU_SALDO_CUOTA += $cuota['saldo'];
        $ACU_SALDO_CUOTA_ACUM += $cuota['saldo'];
        
        
		$PDF->linea[0] = array(
					'posx' => $L1[0],
					'ancho' => $W[0],
					'texto' => $cuota['orden_descuento_id'],
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSize
			);	
		$PDF->linea[1] = array(
					'posx' => $L1[1],
					'ancho' => $W[1],
					'texto' => substr($cuota['organismo'],0,11),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSize
			);
		$PDF->linea[2] = array(
					'posx' => $L1[2],
					'ancho' => $W[2],
					'texto' => $cuota['tipo_numero'],
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSize
			);			
		$PDF->linea[3] = array(
					'posx' => $L1[3],
					'ancho' => $W[3],
					'texto' => substr($cuota['proveedor_producto'],0,39),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSize
			);
		$PDF->linea[4] = array(
					'posx' => $L1[4],
					'ancho' => $W[4],
					'texto' => ($cuota['importe_solicitado']!=0 ? number_format($cuota['importe_solicitado'],0,'.','') : ""),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSize
			);                 
		$PDF->linea[5] = array(
					'posx' => $L1[5],
					'ancho' => $W[5],
					'texto' => ($cuota['permanente'] ? '00/00' : $cuota['cuota']),
					'borde' => '',
					'align' => 'C',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSize
			); 
		$PDF->linea[6] = array(
					'posx' => $L1[6],
					'ancho' => $W[6],
					'texto' => substr($cuota['tipo_cuota'],0,15),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSize
			);
		
		$PDF->linea[7] = array(
					'posx' => $L1[7],
					'ancho' => $W[7],
					'texto' => $util->armaFecha($cuota['vencimiento']),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSize
			);	
			
		$PDF->linea[8] = array(
					'posx' => $L1[8],
					'ancho' => $W[8],
					'texto' => $cuota['estado'],
					'borde' => '',
					'align' => 'C',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSize
			);	
			
		$PDF->linea[9] = array(
					'posx' => $L1[9],
					'ancho' => $W[9],
					'texto' => substr($cuota['situacion_cuota'],0,13),
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSize - 1
			);
		$PDF->linea[10] = array(
					'posx' => $L1[10],
					'ancho' => $W[10],
					'texto' => number_format($cuota['importe'],2),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSize
			);
		$PDF->linea[11] = array(
					'posx' => $L1[11],
					'ancho' => $W[11],
					'texto' => number_format($cuota['pagado'],2),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSize
			);
		$PDF->linea[12] = array(
					'posx' => $L1[12],
					'ancho' => $W[12],
					'texto' => number_format($cuota['saldo'],2),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#ccc',
					'size' => $fontSize
			);	
	
		$PDF->Imprimir_linea();        
        
        
    endif;
    
    if($cuota['tipo_registro'] == 'PAGO'):
        
        $ACU_PAGO_CUOTA += $cuota['pagado'];

        $sizePago = $fontSize - 2;
        $style = "I";        
        $PDF->linea[7] = array(
                    'posx' => $L1[6],
                    'ancho' => $W[6],
                    'texto' => $util->periodo($cuota['periodo']),
                    'borde' => '',
                    'align' => 'R',
                    'fondo' => 0,
                    'style' => $style,
                    'colorf' => '#ccc',
                    'size' => $sizePago
            );
        $PDF->linea[8] = array(
                    'posx' => $L1[7],
                    'ancho' => $W[7],
                    'texto' => $util->armaFecha($cuota['vencimiento']),
                    'borde' => '',
                    'align' => 'L',
                    'fondo' => 0,
                    'style' => $style,
                    'colorf' => '#ccc',
                    'size' => $sizePago
            );	
        $PDF->linea[9] = array(
                    'posx' => $L1[9],
                    'ancho' => $W[9] + $W[10],
                    'texto' => substr($cuota['tipo_cobro'],0,20),
                    'borde' => '',
                    'align' => 'L',
                    'fondo' => 0,
                    'style' => $style,
                    'colorf' => '#ccc',
                    'size' => $sizePago
            );
        $PDF->linea[11] = array(
                    'posx' => $L1[11],
                    'ancho' => $W[11],
                    'texto' => $util->nf($cuota['pagado']),
                    'borde' => '',
                    'align' => 'R',
                    'fondo' => 0,
                    'style' => $style,
                    'colorf' => '#ccc',
                    'size' => $sizePago
            );
            if($cuota['reversado'] == 1):
                $PDF->linea[12] = array(
                            'posx' => $L1[12],
                            'ancho' => $W[12],
                            'texto' => "Reversado",
                            'borde' => '',
                            'align' => 'L',
                            'fondo' => 0,
                            'style' => $style,
                            'colorf' => '#ccc',
                            'size' => $sizePago
                    );
            endif;        
        $PDF->Imprimir_linea();	        
        
    endif;

endforeach;

//IMPRIMO EL TOTAL DEL PERIODO
$PDF->linea[9] = array(
            'posx' => $L1[9],
            'ancho' => $W[9],
            'texto' => 'TOTAL PERIODO',
            'borde' => 'T',
            'align' => 'C',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#ccc',
            'size' => $fontSize
    );
$PDF->linea[10] = array(
            'posx' => $L1[10],
            'ancho' => $W[10],
            'texto' => number_format($ACU_IMPO_CUOTA,2),
            'borde' => 'T',
            'align' => 'R',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#ccc',
            'size' => $fontSize
    );
$PDF->linea[11] = array(
            'posx' => $L1[11],
            'ancho' => $W[11],
            'texto' => number_format($ACU_PAGO_CUOTA,2),
            'borde' => 'T',
            'align' => 'R',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#ccc',
            'size' => $fontSize
    );
$PDF->linea[12] = array(
            'posx' => $L1[12],
            'ancho' => $W[12],
            'texto' => number_format($ACU_SALDO_CUOTA,2),
            'borde' => 'T',
            'align' => 'R',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#ccc',
            'size' => $fontSize
    );		
$PDF->Imprimir_linea();

$PDF->linea[9] = array(
            'posx' => $L1[9],
            'ancho' => $W[9],
            'texto' => 'TOTAL ACUMULADO A ' . $util->periodo($periodo,true,'/'),
            'borde' => '',
            'align' => 'C',
            'fondo' => 0,
            'style' => 'B',
            'colorf' => '#ccc',
            'size' => $fontSize
    );
$PDF->linea[10] = array(
            'posx' => $L1[10],
            'ancho' => $W[10],
            'texto' => '',
            'borde' => '',
            'align' => 'R',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#ccc',
            'size' => $fontSize
    );
$PDF->linea[11] = array(
            'posx' => $L1[11],
            'ancho' => $W[11],
            'texto' => '',
            'borde' => '',
            'align' => 'R',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#ccc',
            'size' => $fontSize
    );
$PDF->linea[12] = array(
            'posx' => $L1[12],
            'ancho' => $W[12],
            'texto' => number_format($ACU_SALDO_CUOTA_ACUM,2),
            'borde' => '',
            'align' => 'R',
            'fondo' => 0,
            'style' => 'B',
            'colorf' => '#ccc',
            'size' => $fontSize
    );		
$PDF->Imprimir_linea();	 

$PDF->Ln(10);
//IMPRIMO EL RESUMEN
if(isset($resumen)){
	
	$PDF->linea[0] = array(
				'posx' => $L1[0],
				'ancho' => 70,
				'texto' => 'RESUMEN GENERAL DE DEUDA *** S.E.U.O.***',
				'borde' => '',
				'align' => 'C',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#D8DBD4',
				'size' => 7
		);

	$PDF->Imprimir_linea();
	

	$PDF->linea[0] = array(
				'posx' => $L1[0],
				'ancho' => 30,
				'texto' => 'SITUACION',
				'borde' => '',
				'align' => 'L',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#D8DBD4',
				'size' => 6
		);
	$PDF->linea[1] = array(
				'posx' => $L1[0] + 30,
				'ancho' => 20,
				'texto' => 'VENCIDA',
				'borde' => '',
				'align' => 'C',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#D8DBD4',
				'size' => 6
		);		
	$PDF->linea[2] = array(
				'posx' => $L1[0] + 50,
				'ancho' => 20,
				'texto' => 'A VENCER',
				'borde' => '',
				'align' => 'C',
				'fondo' => 1,
				'style' => 'B',
				'colorf' => '#D8DBD4',
				'size' => 6
		);
	$PDF->Imprimir_linea();		
	
	foreach($resumen as $item){
		
		$PDF->linea[0] = array(
					'posx' => $L1[0],
					'ancho' => 30,
					'texto' => $item['descripcion_situacion'],
					'borde' => '',
					'align' => 'L',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#D8DBD4',
					'size' => 6
			);
		$PDF->linea[1] = array(
					'posx' => $L1[0] + 30,
					'ancho' => 20,
					'texto' => $util->nf($item['total_adeudado_vencido']),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#D8DBD4',
					'size' => 6
			);		
		$PDF->linea[2] = array(
					'posx' => $L1[0] + 50,
					'ancho' => 20,
					'texto' => $util->nf($item['total_adeudado_avencer']),
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#D8DBD4',
					'size' => 6
			);
		$PDF->Imprimir_linea();			
		
	}
}

$PDF->Ln(15);

// MANDO LAS LEYENDAS
$PDF->linea[0] = array(
			'posx' => $L1[0],
			'ancho' => $W[0],
			'texto' => '*** S.E.U.O. (SALVO ERROR U OMISION) ***',
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSize
	);

$PDF->Imprimir_linea();
$PDF->linea[0] = array(
			'posx' => $L1[0],
			'ancho' => $W[0],
			'texto' => '(*)Cuota Vencida',
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#ccc',
			'size' => $fontSize
	);

$PDF->Imprimir_linea();		

$PDF->Output("estado_cuenta_socio_#".$socio['Socio']['id']."_".$socio['Persona']['apellido']."_".$socio['Persona']['nombre'].".pdf");
?>