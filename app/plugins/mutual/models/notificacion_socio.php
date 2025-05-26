<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

class NotificacionSocio extends MutualAppModel {
    var $name = 'NotificacionSocio';
    var $belongsTo = array(
        'Socio' => array(
            'className' => 'Socio',
            'foreignKey' => 'socio_id'
        )
    );

}