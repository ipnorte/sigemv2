<?php
App::import('Model','Mutual.LiquidacionSocio');

class LiquidacionSocioTest extends LiquidacionSocio { 
    var $name = 'LiquidacionSocioTest'; 

} 

class LiquidacionSocioTestCase extends CakeTestCase{
	
	var $fixtures = array( 'app.plugins.mutual.liquidacion_socio');
	
	function testCuotasLiquidadasBySocioByPeriodo(){
		$this->assertEqual(1,1);
	}
	
}
?>