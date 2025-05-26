<h1>PROCESO DE GENERACION DE CUOTAS DE LOS EXPEDIENTES MIGRADOS</h1>
<?
$pUID = $this->requestAction("/shells/asincronos/crear/process:arma_vencimientos/action:.home/target:/btn_label:/titulo:PROCESO ARMADO DE VENCIMIENTOS/subtitulo:/p1:/p2:/p3:/p4:/p5:/p6:/p7:/p8:/p9:/p10:/p11:/p12:/p13:/");
print $this->requestAction('/shells/asincronos/show/'.$pUID);
?>