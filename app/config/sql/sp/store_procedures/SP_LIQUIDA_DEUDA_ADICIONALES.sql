DROP PROCEDURE IF EXISTS SP_LIQUIDA_DEUDA_CBU_ADICIONALES;
DELIMITER $$
CREATE DEFINER=`sigem_sa`@`%` PROCEDURE `SP_LIQUIDA_DEUDA_CBU_ADICIONALES`(
	vSOCIO_ID INT(11),
	vPERIODO VARCHAR(6),
	vORGANISMO VARCHAR(12),
	vLIQUIDACION_ID INT(11)
)
BEGIN
-- CALL SP_LIQUIDA_DEUDA_ADICIONALES(97,'201502','MUTUCORG2202',127);
DECLARE l_last_row INT DEFAULT 0;
DECLARE vPROVEEDOR_ID INT(11) DEFAULT 0;
DECLARE vIMPUTAR_PROVEEDOR_ID INT(11) DEFAULT 0;
DECLARE vTIPO CHAR(1);
DECLARE vVALOR DECIMAL(10,2);
DECLARE vDEVENGA BOOLEAN;
DECLARE vCALCULO INT(11);
DECLARE vTIPO_CUOTA VARCHAR(12);
DECLARE vACTIVO BOOLEAN;
DECLARE c_adicionales CURSOR FOR 
select proveedor_id,imputar_proveedor_id,
tipo,valor,devengado_previo,deuda_calcula,tipo_cuota,activo
from mutual_adicionales
where codigo_organismo = vORGANISMO and valor > 0
and ifnull(periodo_desde,'000000') <= vPERIODO
and ifnull(periodo_hasta,'999912') >= vPERIODO;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET l_last_row=1;
OPEN c_adicionales;
c1_loop: LOOP
	FETCH c_adicionales INTO vPROVEEDOR_ID,vIMPUTAR_PROVEEDOR_ID,vTIPO,vVALOR,vDEVENGA,vCALCULO,vTIPO_CUOTA,vACTIVO;
		
        IF (l_last_row = 1) THEN
			LEAVE c1_loop; 
		END IF;	    
        
		-- select vPROVEEDOR_ID,vIMPUTAR_PROVEEDOR_ID,vTIPO,vVALOR,vDEVENGA,vCALCULO,vTIPO_CUOTA,vACTIVO;
		
        IF (vVALOR <> 0) THEN
        
			set @saldo = 0;
            set @ordenDtoId = 0;
            set @beneficioId = 0;
            set @tipoProducto = null;
        
			-- SACAR LA ORDEN DE DESCUENTO A DONDE SE VA A CARGAR EN BASE AL PROVEEDOR AL CUAL SE IMPUTA
			set @STMT = CONCAT('select persona_beneficio_id,(select id from orden_descuentos where tipo_orden_dto = \'CMUTU\' and activo = 1 and socio_id = liquidacion_cuotas.socio_id order by id desc limit 1) as orden_id,(select tipo_producto from orden_descuentos where tipo_orden_dto = \'CMUTU\' and activo = 1 and socio_id = liquidacion_cuotas.socio_id order by id desc limit 1) as tipo_producto,sum(saldo_actual) into @beneficioId,@ordenDtoId,@tipoProducto,@saldo FROM liquidacion_cuotas where liquidacion_id = ? and socio_id = ? and ifnull(mutual_adicional_pendiente_id,0) = 0');
			
            IF vPROVEEDOR_ID is not null THEN
				SET @STMT = CONCAT(@STMT,' AND proveedor_id = ? ');             
			END IF;
            IF vCALCULO = 1 THEN
				SET @STMT = CONCAT(@STMT,' AND periodo_cuota <= ? '); 
            END IF;
            IF vCALCULO = 2 THEN
				SET @STMT = CONCAT(@STMT,' AND periodo_cuota < ? '); 
            END IF;            
            IF vCALCULO = 3 THEN
				SET @STMT = CONCAT(@STMT,' AND periodo_cuota = ? '); 
            END IF;

            PREPARE STMT FROM @STMT;
            
            SET @LIQ = vLIQUIDACION_ID;
            SET @SOCIO = vSOCIO_ID;
            SET @PERIODO = vPERIODO;
            
            EXECUTE STMT USING @LIQ,@SOCIO,@PERIODO;
            
            DEALLOCATE PREPARE STMT;
			SET @ADICIONAL = 0;
			IF vVALOR <> 0 AND @saldo <> 0 THEN
				SET @ADICIONAL = IF(vTIPO = 'P',ROUND(@saldo * vVALOR / 100,2),vVALOR);
            END IF;
            
			IF @ADICIONAL <> 0 AND vDEVENGA = 0 AND vACTIVO = TRUE THEN
                
                
                insert into mutual_adicional_pendientes(liquidacion_id,socio_id,codigo_organismo,proveedor_id,
                tipo,deuda_calcula,valor,tipo_cuota,periodo,total_deuda,importe,
                orden_descuento_id,persona_beneficio_id)
                values(vLIQUIDACION_ID,vSOCIO_ID,vORGANISMO,vIMPUTAR_PROVEEDOR_ID,
                vTIPO,vCALCULO,vVALOR,vTIPO_CUOTA,vPERIODO,@saldo,@ADICIONAL,@ordenDtoId,@beneficioId);
                
                set @adicional_id = last_insert_id();
                
                insert into liquidacion_cuotas(liquidacion_id,socio_id,persona_beneficio_id,orden_descuento_id,
						orden_descuento_cuota_id,tipo_orden_dto,tipo_producto,tipo_cuota,
						periodo_cuota,proveedor_id,vencida,importe,saldo_actual,codigo_organismo,
                        mutual_adicional_pendiente_id
				)
                values(vLIQUIDACION_ID,vSOCIO_ID,@beneficioId,
                @ordenDtoId,null,'CMUTU',@tipoProducto,vTIPO_CUOTA,
                vPERIODO,vIMPUTAR_PROVEEDOR_ID,0,@ADICIONAL,@ADICIONAL,vORGANISMO,@adicional_id);
				
                -- SELECT vLIQUIDACION_ID,vSOCIO_ID,vORGANISMO,vIMPUTAR_PROVEEDOR_ID,
                -- vTIPO,vCALCULO,vVALOR,vTIPO_CUOTA,vPERIODO,@saldo,@ADICIONAL,@ordenDtoId,@beneficioId;
                -- select @saldo,@ADICIONAL,@orden_dto_id,@beneficio_id;
                
            END IF;
            
            IF @ADICIONAL <> 0 AND vDEVENGA = 1 THEN
            
				-- VERIFICAR QUE NO EXISTA LA CUOTA
                SET @CUOTA_EXISTENTE = NULL;
                SELECT id INTO @CUOTA_EXISTENTE FROM orden_descuento_cuotas WHERE socio_id = vSOCIO_ID
                AND orden_descuento_id = @ordenDtoId AND proveedor_id = vIMPUTAR_PROVEEDOR_ID
                AND tipo_producto = @tipoProducto AND tipo_cuota = vTIPO_CUOTA AND periodo = vPERIODO;
                
                IF @CUOTA_EXISTENTE IS NULL AND vACTIVO = TRUE THEN
                
                    -- INSERTO LA CUOTA
                    INSERT INTO orden_descuento_cuotas(orden_descuento_id,socio_id,persona_beneficio_id,
                    tipo_orden_dto,tipo_producto,tipo_cuota,periodo,estado,situacion,vencimiento,vencimiento_proveedor,
                    nro_cuota,importe,proveedor_id)
                    VALUES(@ordenDtoId,vSOCIO_ID,@beneficioId,'CMUTU',@tipoProducto,vTIPO_CUOTA,vPERIODO,'A','MUTUSICUMUTU',
                    DATE_FORMAT(now(),'%Y-%m-%d'),DATE_FORMAT(now(),'%Y-%m-%d'),0,@ADICIONAL,vIMPUTAR_PROVEEDOR_ID);
                    
                    SELECT LAST_INSERT_ID() INTO @CUOTA_EXISTENTE;
                    
					insert into liquidacion_cuotas(liquidacion_id,socio_id,persona_beneficio_id,orden_descuento_id,
							orden_descuento_cuota_id,tipo_orden_dto,tipo_producto,tipo_cuota,
							periodo_cuota,proveedor_id,vencida,importe,saldo_actual,codigo_organismo
					)
					values(vLIQUIDACION_ID,vSOCIO_ID,@beneficioId,
					@ordenDtoId,@CUOTA_EXISTENTE,'CMUTU',@tipoProducto,vTIPO_CUOTA,
					vPERIODO,vIMPUTAR_PROVEEDOR_ID,0,@ADICIONAL,@ADICIONAL,vORGANISMO);                     

                ELSE
                
					/*
					SELECT 'DEVENGADO PREVIO - UPDATE',vLIQUIDACION_ID,vSOCIO_ID,vORGANISMO,vIMPUTAR_PROVEEDOR_ID,
					vTIPO,vCALCULO,vVALOR,vTIPO_CUOTA,vPERIODO,@saldo,@ADICIONAL,@ordenDtoId,@beneficioId; 
                    */
                    
                    IF vACTIVO = TRUE THEN
                    
						-- ACTUALIZO EL VALOR
						UPDATE orden_descuento_cuotas set importe = @ADICIONAL WHERE id = @CUOTA_EXISTENTE;
						
						-- VERIFICO SI EXISTE EN LA LIQUIDACION_CUOTAS
						IF (SELECT COUNT(*) FROM liquidacion_cuotas WHERE liquidacion_id = vLIQUIDACION_ID AND socio_id = vSOCIO_ID AND orden_descuento_cuota_id = @CUOTA_EXISTENTE) > 0 THEN
							UPDATE liquidacion_cuotas set importe = @ADICIONAL, saldo_actual = (@ADICIONAL - IFNULL((SELECT SUM(importe) FROM orden_descuento_cobro_cuotas WHERE orden_descuento_cuota_id = @CUOTA_EXISTENTE),0)) 
							WHERE liquidacion_id = vLIQUIDACION_ID
							AND socio_id = vSOCIO_ID AND orden_descuento_cuota_id = @CUOTA_EXISTENTE;
						ELSE
						
							insert into liquidacion_cuotas(liquidacion_id,socio_id,persona_beneficio_id,orden_descuento_id,
									orden_descuento_cuota_id,tipo_orden_dto,tipo_producto,tipo_cuota,
									periodo_cuota,proveedor_id,vencida,importe,saldo_actual,codigo_organismo
							)
							values(vLIQUIDACION_ID,vSOCIO_ID,@beneficioId,
							@ordenDtoId,@CUOTA_EXISTENTE,'CMUTU',@tipoProducto,vTIPO_CUOTA,
							vPERIODO,vIMPUTAR_PROVEEDOR_ID,0,@ADICIONAL,@ADICIONAL,vORGANISMO);                    
						
						END IF;
                    
                    ELSE
                    
						IF (SELECT COUNT(*) FROM orden_descuento_cobro_cuotas WHERE orden_descuento_cuota_id = @CUOTA_EXISTENTE) = 0 THEN
							DELETE FROM liquidacion_cuotas WHERE liquidacion_id = vLIQUIDACION_ID AND socio_id = vSOCIO_ID AND orden_descuento_cuota_id = @CUOTA_EXISTENTE;
                            DELETE FROM orden_descuento_cuotas WHERE id = @CUOTA_EXISTENTE;
                        END IF;
                    
                    END IF;
                    
                
                END IF;
            

            
            END IF;
            

        END IF;

END LOOP c1_loop;
CLOSE c_adicionales;
END$$
DELIMITER ;
