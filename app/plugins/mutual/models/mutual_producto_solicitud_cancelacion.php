<?php
class MutualProductoSolicitudCancelacion extends  MutualAppModel{
	
	var $name = 'MutualProductoSolicitudCancelacion';
	var $belongsTo = array('MutualProductoSolicitud','CancelacionOrden');
	
}