<?php

App::import('model','v1.ProveedorV1');
App::import('model','v1.Producto');
App::import('model','proveedores.Proveedor');

class ProveedoresController extends V1AppController
{
    
    public $name = "Proveedores";
    public $autorizar = array('index','listar_planes','ver_plan');
    
    function beforeFilter(){
        $this->Seguridad->allow($this->autorizar);
        parent::beforeFilter();
    }
    
    public function index() {
        $oPROVEEDOR = new ProveedorV1();
        $proveedores = $oPROVEEDOR->getActivos();
        $this->set('proveedores',$proveedores);
    }
    
    public function listar_planes($codigoProveedor) {
        if(empty($codigoProveedor)){parent::noDisponible();}
        $oPROVEEDOR = new ProveedorV1();
        $oPRODUCTO = new Producto();
        $this->set('proveedor',$oPROVEEDOR->getProveedor($codigoProveedor));
        $this->set('productos',$oPRODUCTO->getProductosActivosByProveedor($codigoProveedor));
    }
    
    public function ver_plan($codigoProveedor,$codigoProducto) {
        $oPROVEEDORV1 = new ProveedorV1();
        $oPRODUCTO = new Producto();
        $oPROVEEDORV2 = new Proveedor();
        
        if(!empty($this->data)){
            //iniciar el copiado de cuotas
            $ret = $oPRODUCTO->copiarCuotas($this->data['Producto']['codigo_proveedor'], $this->data['Producto']['codigo_producto'], $this->data['ProveedorPlan']['id']);
            $this->Mensaje->ok("CUOTAS COPIADAS CORRECTAMENTE");
            $this->redirect('listar_planes/' . $codigoProveedor);
        }
        
        $this->set('proveedorv1',$oPROVEEDORV1->getProveedor($codigoProveedor));
        $this->set('proveedorv2',$oPROVEEDORV2->getProveedorByCuit($codigoProveedor));
        $this->set('producto',$oPRODUCTO->getProducto($codigoProducto));


    }
    
}

