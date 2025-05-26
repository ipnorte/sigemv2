<?php

App::import('Vendor','pago_facil/pago_facil');
/**
 * 
 * @author ADRIAN TORRES
 * @package mutual
 * @subpackage model
 */
class OrdenCajaCobro extends MutualAppModel{
	
	var $name = 'OrdenCajaCobro';
	var $actsAs   = array('Mutual.transaction');
	
//	var $hasMany = array('OrdenCajaCobroCuota');
	

	function marca_procesada($id){
		$data = $this->read(null,$id);
		$data['OrdenCajaCobro']['estado'] = 'P';
		return parent::save($data);
	}
	
	function isProcesada($id){
		$data = $this->read(null,$id);
		if($data['OrdenCajaCobro']['estado']=='P') return true;
		else return false;
	}
	

	function generarOrdenImputaByImporte($data){
		
		$this->begin();
		if(!parent::save($data)){
			$this->rollback();
			return false;
		}

		$nCed = $this->getLastInsertID();
		$m1vto = $data['OrdenCajaCobro']['importe'];
		$f1vto = $data['OrdenCajaCobro']['fecha_vto'];
		
		// genero el codigo de barras
		$PF = new PagoFacil('000',$nCed,$m1vto,$f1vto);
		
		$data['OrdenCajaCobro']['barcode'] = $PF->getCodigo();
		$data['OrdenCajaCobro']['id'] = $nCed;		
		if(!parent::save($data)){
			$this->rollback();
			return false;
		}

		$this->bindModel(array('hasMany' => array('OrdenCajaCobroCuota')));	
		
		$socioId = $data['OrdenCajaCobro']['socio_id'];

		App::import('Model','Mutual.OrdenDescuentoCuota');
		$oOrdenDescuentoCuota = new OrdenDescuentoCuota();
		
		foreach($data['OrdenCajaCobroCuota']['orden_descuento_cuota_id'] as $orden_descuento_cuota_id => $impEntero){
			

			// verificar el socio
			$cuotaSocioId = $oOrdenDescuentoCuota->field('socio_id',"OrdenDescuentoCuota.id = ".$orden_descuento_cuota_id);

			if(intval($socioId) !== intval($cuotaSocioId)){
				$ret = false;
				break;				
			}

			$montoCuota = $impEntero / 100;
			$importe_entregado = $data['OrdenCajaCobroCuota']['orden_descuento_cuota_id1'][$orden_descuento_cuota_id];
			$saldoCuota = $montoCuota - $importe_entregado;
			
			$ordenCajaCobroCuota = array();
			$ordenCajaCobroCuota['OrdenCajaCobroCuota']['orden_caja_cobro_id'] = $nCed;
			$ordenCajaCobroCuota['OrdenCajaCobroCuota']['orden_descuento_cuota_id'] = $orden_descuento_cuota_id;
			$ordenCajaCobroCuota['OrdenCajaCobroCuota']['importe'] = $montoCuota;
			$ordenCajaCobroCuota['OrdenCajaCobroCuota']['importe_abonado'] = $importe_entregado;
			$ordenCajaCobroCuota['OrdenCajaCobroCuota']['saldo_cuota'] = $saldoCuota;

		
			if($this->OrdenCajaCobroCuota->save($ordenCajaCobroCuota)):
				$ret = true;
				$this->OrdenCajaCobroCuota->id = 0;
			else:
				$ret = false;
				break;
			endif;			
			
		}
		
		if(!$ret)$this->rollback();
		else $this->commit();
				
		return $ret;
		
	}
	
	function generarOrdenImputaByVencido($data){
		
		$this->begin();
		
		$ret = parent::save($data);
		
		if(!$ret){
			$this->rollback();
			return $ret;
		}
		
		$nCed = $this->getLastInsertID();
		$m1vto = $data['OrdenCajaCobro']['importe'];
		$f1vto = $data['OrdenCajaCobro']['fecha_vto'];
		
		// genero el codigo de barras
		$PF = new PagoFacil('000',$nCed,$m1vto,$f1vto);

		$data['OrdenCajaCobro']['barcode'] = $PF->getCodigo();
		$data['OrdenCajaCobro']['id'] = $nCed;
		
		$ret = parent::save($data);
		
		if(!$ret){
			$this->rollback();
			return $ret;
		}		
		
		$importe_entregado = 0;
		$saldoCuota = 0;
		$resto = $data['OrdenCajaCobro']['importe_cobrado'];
		
		// grabo el detalle de cuotas

		$this->bindModel(array('hasMany' => array('OrdenCajaCobroCuota')));
		foreach($data['OrdenCajaCobroCuota']['orden_descuento_cuota_id'] as $orden_descuento_cuota_id => $impEntero){
			
			$montoCuota = $impEntero / 100;
			
			if($resto == 0) break;
			
			if($resto >= $montoCuota){
				$importe_entregado = $montoCuota;
				$resto -= $montoCuota;
				$saldoCuota = 0;
			}else{
				$importe_entregado = $resto;
				$saldoCuota = $montoCuota - $importe_entregado;
				$resto -= $importe_entregado;
			}
			
			
			if($this->OrdenCajaCobroCuota->save(array('OrdenCajaCobroCuota' => array(
				'orden_caja_cobro_id' => $nCed,
				'orden_descuento_cuota_id' => $orden_descuento_cuota_id,
				'importe' => $montoCuota,
				'importe_abonado' => $importe_entregado,
				'saldo_cuota' => $saldoCuota
			)))):
				$ret = true;
				$this->OrdenCajaCobroCuota->id = 0;
			else:
				$ret = false;
				break;
			endif;
			
		}

		if(!$ret)$this->rollback();
		else $this->commit();
				
		return $ret;		
	}
	
	
	function cargarOrdenConCuotasDetalladas($id){
		$this->recursive = 2;
		$this->OrdenCajaCobroCuota->bindModel(array('belongsTo' => array('OrdenDescuentoCuota')));
		$orden = $this->read(null,$id);
		if(isset($orden['OrdenCajaCobroCuota']) && count($orden['OrdenCajaCobroCuota'])!=0){
			App::import('Model','Mutual.OrdenDescuentoCuota');
			$oCuota = new OrdenDescuentoCuota();
			foreach($orden['OrdenCajaCobroCuota'] as $idx => $cuotaCobro){
				$cuotaCobro['OrdenDescuentoCuota'] = $oCuota->infoCuota($cuotaCobro['OrdenDescuentoCuota']);
				$orden['OrdenCajaCobroCuota'][$idx] = $cuotaCobro;
			}
		}
		return $orden;
	}
	
	
	function getByOrdenDescuentoCobro($descuentoCobroId){
			$ordenCajaCobro = $this->find('all',array('conditions' => array('OrdenCajaCobro.orden_descuento_cobro_id' => $descuentoCobroId)));
			
			return $ordenCajaCobro;
	}
	
	
//	function afterFind($resultados){
//		debug($resultados);
//	}
	
	function cargarOrdenConDetalleCuotas($id){

		$orden = $this->read(null,$id);


		$sql = "select global_datos.concepto_1, globaldatos.concepto_1, orden_descuentos.*, orden_descuentos.numero, orden_descuentos.cuotas, sum(orden_caja_cobro_cuotas.importe) as importe,sum(orden_caja_cobro_cuotas.importe_abonado) as importe_abonado
				from orden_descuentos
				inner join orden_descuento_cuotas
				on	orden_descuentos.id = orden_descuento_cuotas.orden_descuento_id
				inner join orden_caja_cobro_cuotas
				on	orden_descuento_cuotas.id = orden_caja_cobro_cuotas.orden_descuento_cuota_id
				inner join global_datos
				on global_datos.id = orden_descuentos.tipo_producto
				inner join global_datos as globaldatos
				on globaldatos.id = global_datos.concepto_2
				where	orden_caja_cobro_cuotas.orden_caja_cobro_id = '$id'
				group	by orden_descuentos.id
				order	by orden_descuentos.id";
		
		$aOrdenDescuentos =  $this->query($sql);

		
		$aTmpDetalle = array();
		$aOrdenDescuentoDetalle = array();
		foreach($aOrdenDescuentos as $aOrDescuento):
			$OrdenDescuentoId = $aOrDescuento['orden_descuentos']['id'];
			$sqlCuotas = "select	OrdenDescuentoCuota.nro_cuota as nro_cuota, OrdenDescuentoCuota.periodo
							from	orden_descuento_cuotas OrdenDescuentoCuota
							inner	join orden_caja_cobro_cuotas OrdenCajaCobroCuota
							on	OrdenDescuentoCuota.id = OrdenCajaCobroCuota.orden_descuento_cuota_id
							where	OrdenDescuentoCuota.orden_descuento_id = '$OrdenDescuentoId' and OrdenCajaCobroCuota.orden_caja_cobro_id = '$id'";
			$aOrdenDescuentoCuotas = $this->query($sqlCuotas);



			$cuotas = Set::extract('/OrdenDescuentoCuota/nro_cuota',$aOrdenDescuentoCuotas);
			$strDesc = implode('-', $cuotas) ."/" . $aOrDescuento['orden_descuentos']['cuotas'];
			if($aOrDescuento[0]['importe_abonado'] < $aOrDescuento[0]['importe']){
				$strDesc .= " *** PAGO PARCIAL ***";
			}


			$periodo = Set::extract('/OrdenDescuentoCuota/periodo', $aOrdenDescuentoCuotas);
			foreach($periodo as $key => $valor):
				$periodo[$key] = substr($valor,0,4) . "/" . substr($valor,-2);
			endforeach;
			$strPeriodo = implode('-', $periodo);


			$aTmpDetalle['proveedor_id'] = $aOrDescuento['orden_descuentos']['proveedor_id'];
			$aTmpDetalle['orden_descuento_id'] = $aOrDescuento['orden_descuentos']['id'];
			$aTmpDetalle['orden_descuento_cobro_id'] = $orden['OrdenCajaCobro']['orden_descuento_cobro_id'];
			$aTmpDetalle['concepto'] = 'EXPTE: ' . $aOrDescuento['orden_descuentos']['numero'] . ' - ' . $aOrDescuento['global_datos']['concepto_1'] . ' - ctas: ' . $strDesc;
			if($aOrDescuento['orden_descuentos']['cuotas'] == 0):
				$aTmpDetalle['concepto'] = $aOrDescuento['globaldatos']['concepto_1'] . ' - PER.: ' . $strPeriodo;
			endif; 
			$aTmpDetalle['importe'] = $aOrDescuento[0]['importe_abonado'];
			array_push($aOrdenDescuentoDetalle, $aTmpDetalle);
		endforeach;

		$orden['OrdenDescuentoDetalle'] = $aOrdenDescuentoDetalle;
		
		return $orden;
	}
	


    public function generarAndRecaudar($socioId,$cuotas,$proveedorOrigenFondoId,$fecha = NULL){

        if(empty($cuotas)){return NULL;}
        $fecha = (empty($fecha) ? date('Y-m-d') : $fecha);

        ##################################################################################
        #GENERAR UNA ORDEN DE COBRO POR CAJA
        ##################################################################################
        $oCobroCaja = array(
            'OrdenCajaCobro' => array(
                'id' => 0,
                'fecha_vto' => $fecha,
                'socio_id' => intval($socioId),
                'importe' => 0,
                'tipo_imputacion' => 0,
                'importe_cobrado' => 0
            ),
            'OrdenCajaCobroCuota' => array(
                'orden_descuento_cuota_id' => array()
            )
        );
        $IMPORTE = 0;
        foreach($cuotas as $id => $importe){
            $IMPORTE += floatVal($importe);
            $oCobroCaja['OrdenCajaCobroCuota']['orden_descuento_cuota_id'][$id] = floatVal($importe) * 100;
            $oCobroCaja['OrdenCajaCobroCuota']['orden_descuento_cuota_id1'][$id] = floatVal($importe);
        }
        $oCobroCaja['OrdenCajaCobro']['importe'] = $IMPORTE;
        $oCobroCaja['OrdenCajaCobro']['importe_cobrado'] = $IMPORTE;

        if(!$this->generarOrdenImputaByImporte($oCobroCaja)){return NULL;}
        $oCobroCaja['OrdenCajaCobro']['id'] = $this->getLastInsertID();

        ##################################################################################
        # GENERAR EL COBRO
        ##################################################################################
        $fechaToArray = array(
            'day' => date('d',strtotime($oCobroCaja['OrdenCajaCobro']['fecha_vto'])),
            'month' => date('m',strtotime($oCobroCaja['OrdenCajaCobro']['fecha_vto'])),
            'year' => date('Y',strtotime($oCobroCaja['OrdenCajaCobro']['fecha_vto'])),
        );

        $renglon = array(
                'OrdenDescuentoCobro' => array(
                    'fecha_comprobante' => $fechaToArray,
                    'observacion' => '',
                    'forma_cobro' => 'EF',
                    'fdeposito' => NULL,
                    'banco_id' => NULL,
                    'plaza' => NULL,
                    'numero_cheque' => NULL,
                    'fcheque' => NULL,
                    'fvenc' => $fechaToArray,
                    'librador' => NULL,
                    'importe' => $oCobroCaja['OrdenCajaCobro']['importe_cobrado'],
                    'tipo_documento' => 'REC',
                    'orden_caja_cobro_id' => $oCobroCaja['OrdenCajaCobro']['id'],
                    'cabecera_socio_id' => $oCobroCaja['OrdenCajaCobro']['socio_id'],
                    'proveedor_origen_fondo_id' => $proveedorOrigenFondoId,
                    'forma_cobro_desc' => 'EFECTIVO',
                    'importe_cobro' => $oCobroCaja['OrdenCajaCobro']['importe_cobrado'],
                    'importe_total' => $oCobroCaja['OrdenCajaCobro']['importe_cobrado'],
                    'denominacion' => 'CAJA',
                ),
                'Referencia' => array('modelo' => 'OrdenDescuentoCobro')
        );
        
        $cobro = array(
            'OrdenDescuentoCobro' => array(
                'fecha_comprobante' => $fechaToArray,
                'observacion' => '',
                'forma_cobro' => NULL,
                'fdeposito' => NULL,
                'banco_id' => NULL,
                'plaza' => NULL,
                'numero_cheque' => NULL,
                'fcheque' => NULL,
                'fvenc' => $fechaToArray,
                'librador' => NULL,
                'importe' => $oCobroCaja['OrdenCajaCobro']['importe_cobrado'],
                'tipo_documento' => 'REC',
                'orden_caja_cobro_id' => $oCobroCaja['OrdenCajaCobro']['id'],
                'cabecera_socio_id' => $oCobroCaja['OrdenCajaCobro']['socio_id'],
                'proveedor_origen_fondo_id' => $proveedorOrigenFondoId,
                'forma_cobro_desc' => 'EFECTIVO',
                'importe_cobro' => $oCobroCaja['OrdenCajaCobro']['importe_cobrado'],
                'importe_total' => $oCobroCaja['OrdenCajaCobro']['importe_cobrado'],
                'banco_cuenta_movimiento_id' => NULL,
            ),
            'Referencia' => array('modelo' => 'OrdenDescuentoCobro'),
        );

        if($proveedorOrigenFondoId == MUTUALPROVEEDORID ){
            $cobro['Recibo']['renglonesSerialize'] = base64_encode(serialize(array($renglon)));
        }

        App::import('Model','Mutual.OrdenDescuentoCobro');
        $oCOBRO = new OrdenDescuentoCobro();   
        $nReciboId = $oCOBRO->orden_cobro_caja($cobro);
        return $nReciboId;

    }

	
}
?>