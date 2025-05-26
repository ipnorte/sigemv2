<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

class Notificacion extends MutualAppModel {
    var $name = 'Notificacion';
    var $actsAs = array('Containable');

    
    public $hasMany = array(
        'NotificacionSocio' => array(
            'className' => 'NotificacionSocio',
            'foreignKey' => 'notificacion_id',
            'dependent' => true
        )
    );

    function borrarPorPeriodo($periodo) {
        // Borrar notificaciones de un período y sus socios relacionados
        $notificaciones = $this->find('all', array(
            'conditions' => array('Notificacion.periodo' => $periodo),
            'fields' => array('Notificacion.id')
        ));
        foreach ($notificaciones as $n) {
            $this->delete($n['Notificacion']['id'], true); // true => cascade delete
        }
    }
    
    function listar() {
        $this->NotificacionSocio->bindModel(array('belongsTo' => array('Notificacion')));

        $resultados = $this->NotificacionSocio->find('all', array(
            'fields' => array(
                'Notificacion.id',
                'Notificacion.periodo',
                'Notificacion.fecha',
                'COUNT(NotificacionSocio.id) AS total_notificados',
                'SUM(CASE WHEN NotificacionSocio.error = 1 THEN 1 ELSE 0 END) AS total_con_error',
                'SUM(NotificacionSocio.saldo) AS total_deuda'
            ),
            'group' => array(
                'Notificacion.id',
                'Notificacion.periodo',
                'Notificacion.fecha'
            ),
            'order' => array('Notificacion.periodo' => 'DESC'),
            'recursive' => -1,
            'joins' => array(
                array(
                    'table' => 'notificaciones',
                    'alias' => 'Notificacion',
                    'type' => 'RIGHT',
                    'conditions' => array('Notificacion.id = NotificacionSocio.notificacion_id')
                )
            )
        ));
        return $resultados;
    }
    
    
    function cargarConDetalle($notificacion_id) {
        $this->Behaviors->attach('Containable');

        $this->NotificacionSocio->bindModel(array('belongsTo' => array('Socio')));
        $this->NotificacionSocio->Socio->bindModel(array('belongsTo' => array('Persona')));

        $notificacion = $this->find('first', array(
            'conditions' => array('Notificacion.id' => $notificacion_id),
            'fields' => array(
                'Notificacion.id',
                'Notificacion.fecha',
                'Notificacion.periodo',
                'Notificacion.user_created'
            ),
            'contain' => array(
                'NotificacionSocio' => array(
                    'fields' => array(
                        'NotificacionSocio.id',
                        'NotificacionSocio.socio_id',
                        'NotificacionSocio.email',
                        'NotificacionSocio.error',
                        'NotificacionSocio.stop_debit',
                        'NotificacionSocio.saldo',
                        'NotificacionSocio.pagado',
                        'NotificacionSocio.detalle',
                        'NotificacionSocio.notificacion_id'
                    ),
                    'Socio' => array(
                        'fields' => array('Socio.id', 'Socio.persona_id'),
                        'Persona' => array(
                            'fields' => array('Persona.apellido', 'Persona.nombre', 'Persona.documento')
                        )
                    )
                )
            )
        ));

        if (!empty($notificacion['NotificacionSocio'])) {
            usort($notificacion['NotificacionSocio'], function ($a, $b) {
                $aKey = $a['Socio']['Persona']['apellido'] . $a['Socio']['Persona']['nombre'];
                $bKey = $b['Socio']['Persona']['apellido'] . $b['Socio']['Persona']['nombre'];
                return strcasecmp($aKey, $bKey);
            });
        }    

        return $notificacion;
    }

    
}
