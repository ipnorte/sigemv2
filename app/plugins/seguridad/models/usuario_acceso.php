<?php 
class UsuarioAcceso extends SeguridadAppModel {

    var $name = 'UsuarioAcceso';
    // var $belongsTo = array('Usuario');


    public function registrar($userId)
    {
        // $registros = $this->find('count',
        // array('conditions' => array("date_format(logon_at,'%Y%m%d')" => date('Ymd')))
        // );
        // if($registros > 0){return true;}
        $IP = filter_input(INPUT_SERVER,'REMOTE_ADDR');
        $host = gethostbyaddr($IP);
        $agente = filter_input(INPUT_SERVER,'HTTP_USER_AGENT');
        $data = array(
            'UsuarioAcceso' => array(
                'usuario_id' => $userId,
                'ip' => $IP,
                'host' => $host,
                'agente' => $agente
            )
        );
        return $this->save($data);  
    }

    public function getByUserId($userId,$days = 30){
        return $this->find('all',array(
            'conditions' => array(
                'usuario_id' => $userId,
                'TIMESTAMPDIFF(DAY, logon_at, now()) <= ' => $days
            )
            ,'order' => array('logon_at' =>'DESC')));
    }

    public function listarByFechas($fDesde = null,$fHasta = null){
        $fDesde = (empty($fDesde) ? date('Y-m-d') : $fDesde);
        $fHasta = (empty($fHasta) ? date('Y-m-d') : $fHasta);
        $this->bindModel(array('belongsTo' => array('Usuario')));
        return $this->find('all',array(
            'conditions' => array(
                "date_format(logon_at,'%Y%m%d') > " >= $fDesde,
                "date_format(logon_at,'%Y%m%d') > " <= $fHasta,
            )
            ,'order' => array('logon_at' =>'DESC')));        
    }
    

}
?>