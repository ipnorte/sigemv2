<?php
class Ejercicio extends ContabilidadAppModel {
	var $name = 'Ejercicio';
	var $useTable = 'co_ejercicios';
	var $uses = array('contabilidad.co_ejercicios', 'mutual.global_datos');

	function guardar($datos){
		return parent::save($datos);
	}
	
	function traerEjercicio(){
		$datos = $this->find('all');
		return $datos;
	}

	function antesGrabar($datos){
		$ret = true;
		$rws = $this->find('count',array('conditions' => array('Ejercicio.fecha_desde >=' => $datos['Ejercicio']['fecha_desde']['year'] .'-' . $datos['Ejercicio']['fecha_desde']['month'] .'-' . $datos['Ejercicio']['fecha_desde']['day'], 'Ejercicio.fecha_hasta <=' => $datos['Ejercicio']['fecha_hasta']['year'] .'-' . $datos['Ejercicio']['fecha_hasta']['month'] .'-' . $datos['Ejercicio']['fecha_hasta']['day'])));
		if($rws != 0):
			$ret = false;
		endif;
		return $ret;
	}
	
	
        function trasladar_ejercicio($datos){
//            debug($datos);
//            exit;
            $exito = 0;
            $id = $datos['Ejercicio']['pos_ejercicio_id'];
            $fecha = $datos['Ejercicio']['fecha_desde'];

            $this->begin();

            $sqlUpdadte = "UPDATE co_plan_cuentas p, co_plan_cuentas p1
                SET	p.co_plan_cuenta_id = p1.id
                WHERE p.co_ejercicio_id = '$id' AND p.co_ejercicio_id = p1.co_ejercicio_id AND p.sumariza = p1.cuenta
            ";
            $this->query($sqlUpdate);

            
            $sqlUpdadte = "UPDATE co_plan_cuentas p, banco_cuenta_movimientos t
                SET	t.co_plan_cuenta_id = p.id
                WHERE p.vincula_co_plan_cuenta_id = t.co_plan_cuenta_id AND t.co_plan_cuenta_id > 0 AND t.fecha_operacion >= '$fecha'
            ";
            $this->query($sqlUpdate);

            
            $sqlUpdate = "UPDATE co_plan_cuentas p, banco_cuentas t
                SET t.co_plan_cuenta_id = p.id
                WHERE p.vincula_co_plan_cuenta_id = t.co_plan_cuenta_id AND t.co_plan_cuenta_id > 0
            ";
            $this->query($sqlUpdate);

            
            $sqlUpdate = "UPDATE co_plan_cuentas p, cliente_factura_detalles t, cliente_facturas f
                SET	t.co_plan_cuenta_id = p.id
                WHERE p.vincula_co_plan_cuenta_id = t.co_plan_cuenta_id AND t.co_plan_cuenta_id > 0 AND t.cliente_factura_id = f.id AND f.fecha_comprobante >= '$fecha' 
            ";
            $this->query($sqlUpdate);

            
            $sqlUpdate = "UPDATE co_plan_cuentas p, cliente_facturas t
                SET t.co_plan_cuenta_id = p.id
                WHERE p.vincula_co_plan_cuenta_id = t.co_plan_cuenta_id AND t.co_plan_cuenta_id > 0 AND t.fecha_comprobante >= '$fecha'
            ";
            $this->query($sqlUpdate);

            
            $sqlUpdate = "UPDATE co_plan_cuentas p, cliente_tipo_asiento_renglones t
                SET t.co_plan_cuenta_id = p.id
                WHERE p.vincula_co_plan_cuenta_id = t.co_plan_cuenta_id AND t.co_plan_cuenta_id > 0
            ";
            $this->query($sqlUpdate);

            
            $sqlUpdate = "UPDATE co_plan_cuentas p, clientes t
                SET t.co_plan_cuenta_id = p.id
                WHERE p.vincula_co_plan_cuenta_id = t.co_plan_cuenta_id AND t.co_plan_cuenta_id > 0
            ";
            $this->query($sqlUpdate);

            
            $sqlUpdate = "UPDATE co_plan_cuentas p, mutual_cuenta_asientos t
                SET t.co_plan_cuenta_id = p.id
                WHERE p.vincula_co_plan_cuenta_id = t.co_plan_cuenta_id AND t.co_plan_cuenta_id > 0
            ";
            $this->query($sqlUpdate);

            
            $sqlUpdate = "UPDATE co_plan_cuentas p, mutual_tipo_asiento_renglones t
                SET t.co_plan_cuenta_id = p.id
                WHERE p.vincula_co_plan_cuenta_id = t.co_plan_cuenta_id AND t.co_plan_cuenta_id > 0
            ";
            $this->query($sqlUpdate);

            
            $sqlUpdate = "UPDATE co_plan_cuentas p, proveedor_facturas t
                SET t.co_plan_cuenta_id = p.id
                WHERE p.vincula_co_plan_cuenta_id = t.co_plan_cuenta_id AND t.co_plan_cuenta_id > 0 AND t.fecha_comprobante >= '$fecha'
            ";
            $this->query($sqlUpdate);

            
            $sqlUpdate = "UPDATE co_plan_cuentas p, proveedor_tipo_asiento_renglones t
                SET t.co_plan_cuenta_id = p.id
                WHERE p.vincula_co_plan_cuenta_id = t.co_plan_cuenta_id AND t.co_plan_cuenta_id > 0
            ";
            $this->query($sqlUpdate);

            
            $sqlUpdate = "UPDATE co_plan_cuentas p, proveedores t
                SET t.co_plan_cuenta_id = p.id
                WHERE p.vincula_co_plan_cuenta_id = t.co_plan_cuenta_id AND t.co_plan_cuenta_id > 0
            ";
            $this->query($sqlUpdate);

            
            $sqlUpdate = "UPDATE co_plan_cuentas p, recibo_detalles t, recibos r
                SET t.co_plan_cuenta_id = p.id
                WHERE p.vincula_co_plan_cuenta_id = t.co_plan_cuenta_id AND t.co_plan_cuenta_id > 0 AND t.recibo_id = r.id AND r.fecha_comprobante >= '$fecha' 
            ";
            $this->query($sqlUpdate);
            
            
            $sqlUpdate = "
                    UPDATE global_datos GlobalDato
                    SET GlobalDato.entero_1 = '$id' 
                    WHERE GlobalDato.id = 'CONTEVIG'
            ";
            $update = $this->query($sqlUpdate);

            
            $sqlUpdate = "UPDATE co_ejercicios Ejercicio SET Ejercicio.activo = '1' WHERE Ejercicio.id = '$id';
            ";
            $this->query($sqlUpdate);

            
            $id = $datos['Ejercicio']['co_ejercicio_id'];
            $sqlUpdate = "UPDATE co_ejercicios SET activo = '0' WHERE id = '$id'
            ";
            $this->query($sqlUpdate);

            
            $this->commit();

            $exito = 1;
            return $exito;

                
        }
	
}
?>