/***********************************************************************************************************************************
 * 
 * LIBRERIA JS DE VALIDADORES
 * 
 ************************************************************************************************************************************/

//variables globales

var classFormError = "form-error";
var contenedorMsgError = 'mensaje_error_js';


/**
 * validarEmail
 * 
 * @param email
 * @param idCtrl
 * @param msgId
 * @return
 */

function validarEmail(email,idCtrl,msgId) {
	if(msgId==='')msgId = contenedorMsgError;
	var filtro  = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	if (!filtro.test(email)){
		$(idCtrl).addClassName(classFormError);
		$(idCtrl).focus();
		$(msgId).update('La direcci&oacute;n de E-mail es incorrecta');
		$(msgId).show();	
		return false;
	}
	return true;
}

/**
 * validarCBU_BANCO
 * valida si el CBU pasado por parametro pertenece al codigo de banco pasado por parametro
 * esta funcion llena los campos
 * 	sucursal
 * 	nro_cta_bco
 * 	tipo_cta_bco
 * @param strcbu
 * @param codigoBco
 * @param msgId
 * @return
 */
function validarCBU_BANCO(strcbu,codigoBco,msgId){
//	var ret = new Array(3);

	var cbu = strcbu.toString();
	var codBco = codigoBco.toString();

	if(msgId==='')msgId = contenedorMsgError;

	//saco los datos del banco del CBU//
	var cbu_bco = cbu.substring(0,3);
	var cbu_suc = cbu.substring(3,7);
	var cbu_tcta = cbu.substring(8,10);
	var cbu_ncta = cbu.substring(10,21);

	$('cbu').removeClassName(classFormError);
	$(msgId).update('');
	$(msgId).hide();
	
//	alert(cbu);
//	alert(cbu_bco);
//	alert(cbu_suc);
//	alert(cbu_tcta);
//	alert(cbu_ncta);
	
	//controlo que el CBU sea del banco seleccionado//
	if(rellenar(cbu_bco,'0',5,'L')!== codBco){
	//if(parseInt(cbu_bco)!=parseInt(codBco)){
		$('cbu').addClassName(classFormError);
		$(msgId).update('El CBU no corresponde al Banco seleccionado!.');
		$(msgId).show();
		return false;
	}
	//cargo los datos en base al CBU//
	document.getElementById('sucursal').value = cbu_suc;
	document.getElementById('nro_cta_bco').value = cbu_ncta;
	document.getElementById('tipo_cta_bco').value = cbu_tcta;
	return true;

}

function validarCBU(ElemId,Mensaje,required,msgId,chkBco){
	/*
	* USOS:
	*   ValidarCBU("2650450-2 0214505639667-6")
	*   ValidarCBU("0200931911000076418280")
	*   ValidarCBU("26504502/02145056396676")
	*------------------------------------------------------------
	*-- Formato del CBU:
	*--   EEESSSS-V TTTTTTTTTTTTT-V
	*-- Bloque 1:
	*--   EEE - Numero de entidad (3 posiciones)
	*--   SSSS - Numero de sucursal (4 posiciones)
	*--   V - Digito verificador de las primeras 7 posiciones
	*-- Bloque 2:
	*--   XX - tipo de cuenta (2)
	*--   TTTTTTTTTTT - Identificacion de la cuenta individual (11)
	*--   V - Digito verificador de las anteriores 13 posiciones
	*--
	*-- Para el calculo de los digitos verificadores se
	*-- debe aplicar la clave 10 con el ponderador 9713
	*------------------------------------------------------------
	*/
	if(msgId=='')msgId = contenedorMsgError;
	//var lcCBU;
	var lcBloque1;
	var lcBloque2;
	$(ElemId).removeClassName(classFormError);
	$(msgId).update('');
	$(msgId).hide();
	var strCBU = $(ElemId).getValue();
	strCBU = strCBU.toString();
	var acum = 0;
	var j = 0;
	for (j; j < strCBU.length; j++){
		caracter = parseInt(strCBU.charAt(j));
		acum = acum + caracter;

	}
	if(acum==0){
		$(msgId).update('CBU incorrecto');
		$(msgId).show();
		$(ElemId).addClassName(classFormError);
		$(ElemId).focus();
		return false;
	}
	if(strCBU.length!=22){
		$(msgId).update('CBU incompleto');
		$(msgId).show();
		$(ElemId).addClassName(classFormError);
		$(ElemId).focus();
		return false;
	}

	if(chkBco!=''){
		if(!validarCBU_BANCO($(ElemId).getValue(),chkBco,msgId))return false;
	}

	lcBloque1 = strCBU.substring(0,8);
	lcBloque2 = strCBU.substring(8,strCBU.length);
	var error = true;
	if(!ValidarDigito_CBU(lcBloque1)){
		error = false;
	}else if(!ValidarDigito_CBU(lcBloque2)){
		error = false;
	}
	if(!error){
		$(msgId).update('CBU incorrecto');
		$(msgId).show();
		$(ElemId).addClassName(classFormError);
		$(ElemId).focus();
		return false;
	}else{
		return true;
	}
}

function ValidarDigito_CBU(Bloque){
	var Pond = '9713';
	var lnSuma = 0;
	var tcBloque = Bloque.toString();
	var lnLargo = tcBloque.length;
	var lcDigito = tcBloque.charAt(lnLargo - 1);
	var lcBloque = tcBloque.substring(0,lnLargo - 1);
	var lenLcBloque = lcBloque.length;
	var ln = 1;
	for (ln;ln<=lenLcBloque;ln++){
		var i = lenLcBloque - ln;
		var v1 = parseInt(lcBloque.charAt(i));
		var resto = ((4 - ln)%4);
		if(resto<0)resto += 4;
		var v2 = parseInt(Pond.charAt(resto));
		var producto = v1 * v2;
		lnSuma += producto;
	}
	var digito = 10 - (lnSuma % 10);
	digito = digito.toString();
	digito = digito.charAt(parseInt(digito.length) - 1);
	if(lcDigito==digito)return true;
	else return false;
}


function validDate(ElemId,msgId){
	if(msgId=='')msgId = contenedorMsgError;
	var result = true;
	var msgError = "";
	$(ElemId).removeClassName(classFormError);
	$(msgId).update('');
	$(msgId).hide();
	if(!validRequired(ElemId,'Debe indicar la Fecha','',msgId)) return false;
	var elems = document.getElementById(ElemId).value.split('/');
	fecha = (elems.length == 3);
	if(fecha){
		var month = parseInt(elems[1],10);
		var day = parseInt(elems[0],10);
		var year = parseInt(elems[2],10);
		result = allDigits(elems[1]) && (month > 0) && (month < 13) &&
		allDigits(elems[0]) && (day > 0) && (day < 32) &&
		allDigits(elems[2]) && ((elems[2].length == 2) || (elems[2].length == 4));
		if(result){
			//controlar si es bisiesto//
			var bisiesto = (year % 4 == 0 && (year % 100 != 0 || year % 400 == 0));
			if(month == 2){
				if (day > 29 || ( day == 29 && !bisiesto)){
					msgError = 'El d�a no es correcto para el mes de Febrero';
					result = false;
				}			
			}
		}else{
			msgError = 'Fecha Incorrecta - dd/mm/aaaa';
		}

	}else if($(ElemId).getValue()!=''){
		msgError = 'Fecha Incorrecta - dd/mm/aaaa';
		result = false;
	}
	
	if(!result){
		$(msgId).update(msgError);
		$(msgId).show();
		$(ElemId).addClassName(classFormError);
		$(ElemId).focus();
	}
	return result;
}

function isValidDate(dateStr) {
	// Date validation function courtesty of 
	// Sandeep V. Tamhankar (stamhankar@hotmail.com) -->

	// Checks for the following valid date formats:
	// MM/DD/YY   MM/DD/YYYY   MM-DD-YY   MM-DD-YYYY

	var datePat = /^(\d{1,2})(\/|-)(\d{1,2})\2(\d{4})$/; // requires 4 digit year

	var matchArray = dateStr.match(datePat); // is the format ok?
	if (matchArray == null) {
		alert(dateStr + " La Fecha no tiene un formato válido.")
		return false;
	}
	
	month = matchArray[3]; // parse date into variables
	day = matchArray[1];
	year = matchArray[4];
	
	if (month < 1 || month > 12) { // check month range
		alert("El mes debe estar entre 1 y 12.");
		return false;
	}
	if (day < 1 || day > 31) {
		alert("El día debe estar entre 1 y 31.");
		return false;
	}
	if ((month==4 || month==6 || month==9 || month==11) && day==31) {
		alert("El mes "+month+" no tiene 31 dias!")
		return false;
	}
	if (month == 2) { // check for february 29th
		var isleap = (year % 4 == 0 && (year % 100 != 0 || year % 400 == 0));
		if (day>29 || (day==29 && !isleap)) {
			alert("Febrero de " + year + " no tiene " + day + " dias!");
			return false;
		}
	}
	return true;
}

function isValidTime(timeStr) {
	// Time validation function courtesty of 
	// Sandeep V. Tamhankar (stamhankar@hotmail.com) -->

	// Checks if time is in HH:MM:SS AM/PM format.
	// The seconds and AM/PM are optional.

	var timePat = /^(\d{1,2}):(\d{2})(:(\d{2}))?(\s?(AM|am|PM|pm))?$/;

	var matchArray = timeStr.match(timePat);
	if (matchArray == null) {
		alert("La hora no tiene un formato válido.");
		return false;
	}
	hour = matchArray[1];
	minute = matchArray[2];
	second = matchArray[4];
	ampm = matchArray[6];

	if (second=="") { second = null; }
	if (ampm=="") { ampm = null }

	if (hour < 0  || hour > 23) {
		alert("La hora debe estar entre 1 y 12. (o 0 y 23 para el formato militar)");
		return false;
	}
	if (hour <= 12 && ampm == null) {
		if (confirm("Por favor indicar cual es el formato de hora que esta utilizando.  OK = Formato Estándar, CANCEL = Formato Militar")) {
			alert("Ud debe especificar AM o PM");
			return false;
		}
	}
	if  (hour > 12 && ampm != null) {
		alert("Ud no puede especificar AM o PM para el formato militar.");
		return false;
	}
	if (minute < 0 || minute > 59) {
		alert ("el minuto debe estar entre 0 y 59.");
		return false;
	}
	if (second != null && (second < 0 || second > 59)) {
		alert ("Los segundos deben estar entre 0 y 59.");
		return false;
	}
	return true;
}

function dateDiff(fecha_1,fecha_2) {
	date1 = new Date();
	date2 = new Date();
	diff  = new Date();

	if (isValidDate(fecha_1)) { // Validates first date 
		date1temp = new Date(fecha_1);
		date1.setTime(date1temp.getTime());
	}
	else return false; // otherwise exits

	if (isValidDate(fecha_2)){ // Validates second date 
		date2temp = new Date(fecha_2);
		date2.setTime(date2temp.getTime());
	}
	else return false; // otherwise exits

// sets difference date to difference of first date and second date

	diff.setTime(Math.abs(date1.getTime() - date2.getTime()));

	timediff = diff.getTime();

	weeks = Math.floor(timediff / (1000 * 60 * 60 * 24 * 7));
	timediff -= weeks * (1000 * 60 * 60 * 24 * 7);

	days = Math.floor(timediff / (1000 * 60 * 60 * 24)); 
	timediff -= days * (1000 * 60 * 60 * 24);

	hours = Math.floor(timediff / (1000 * 60 * 60)); 
	timediff -= hours * (1000 * 60 * 60);

	mins = Math.floor(timediff / (1000 * 60)); 
	timediff -= mins * (1000 * 60);

	secs = Math.floor(timediff / 1000); 
	timediff -= secs * 1000;

	alert(weeks + " weeks, " + days + " days, " + hours + " hours, " + mins + " minutes, and " + secs + " seconds");

	return false; // form should never submit, returns false
}



function validNumber(ElemId,Mensaje,Zero,msgId){

	if(msgId=='')msgId = contenedorMsgError;

	var num = parseFloat($(ElemId).getValue());

	$(ElemId).removeClassName(classFormError);
	$(msgId).update('');
	$(msgId).hide();
	
	if(Zero && num == 0){
		$(msgId).update(Mensaje);
		$(msgId).show();
		$(ElemId).addClassName(classFormError);
		$(ElemId).focus();
		return false;
	}else if (!/^\d+\.?\d*$/.test(num) && num != 0){
		$(msgId).update(Mensaje);
		$(msgId).show();
		$(ElemId).addClassName(classFormError);
		$(ElemId).focus();
		return false;
	}else{
		return true;
	}

}



function validRequired(ElemId,valorcontrol){
	var result = true;
	$(ElemId).removeClassName(classFormError);
	$(contenedorMsgError).update('');
	$(contenedorMsgError).hide();
	if ($(ElemId).getValue()==valorcontrol){
		$(contenedorMsgError).update('Requerido');
		$(contenedorMsgError).show();
		$(ElemId).addClassName(classFormError);
		$(ElemId).focus();
		result = false;
	}
	return result;
}


function controlCUIT(nroCUIT,ElemId,msgId){
	if(msgId=='')msgId = contenedorMsgError;
	$(ElemId).removeClassName(classFormError);
	$(msgId).update('');
	$(msgId).hide();
	var result = true;
	var cuit = nroCUIT;
	var vec = new Array(10);
	var esCuit = false;
	var cuit_rearmado = '';
	var errors = '';
	for (j=0; j < cuit.length; j++) {
		caracter=cuit.charAt(j);
		if ( caracter.charCodeAt(0) >= 48 && caracter.charCodeAt(0) <= 57 ){
			cuit_rearmado += caracter;
		}
	}
	cuit = cuit_rearmado;
	if (!allDigits(nroCUIT)) {
		$(msgId).update('CUIT/CUIL incorrecto');
		$(msgId).show();
		$(ElemId).addClassName(classFormError);
		$(ElemId).focus();
		result = false;
	}else if ( cuit.length != 11) {
		$(msgId).update('El CUIT/CUIL debe ser de 11 n&uacute;meros');
		$(msgId).show();
		$(ElemId).addClassName(classFormError);
		$(ElemId).focus();
		result = false;
	} else {
		x=k=dv=0;
		vec[0] = cuit.charAt(0) * 5;
		vec[1] = cuit.charAt(1) * 4;
		vec[2] = cuit.charAt(2) * 3;
		vec[3] = cuit.charAt(3) * 2;
		vec[4] = cuit.charAt(4) * 7;
		vec[5] = cuit.charAt(5) * 6;
		vec[6] = cuit.charAt(6) * 5;
		vec[7] = cuit.charAt(7) * 4;
		vec[8] = cuit.charAt(8) * 3;
		vec[9] = cuit.charAt(9) * 2;
		for( k = 0;k<=9; k++) {
			x += vec[k];
		}

		dv = (11 - (x % 11)) % 11;
		if ( dv == cuit.charAt( 10) ) {
			result = true;
		}else{
			$(msgId).update('CUIT/CUIL Invalido');
			$(msgId).show();
			$(ElemId).addClassName(classFormError);
			$(ElemId).focus();
			result = false;
		}
	}
	return result
}

function allDigits(str){
	return inValidCharSet(str,'0123456789');
}

function allDigitss(str){
	return inValidCharSet(str,'0123456789.');
}

function inValidCharSet(str,charset){
	var result = true;
	for (var s=0;s<str.length;s++)
		if (charset.indexOf(str.substr(s,1))<0){
			result = false;
			break;
	}
return result;
}

function cuit_ndoc(IdnDoc,IdnCuit,msgId){
	var result = true;
	if(msgId=='')msgId = contenedorMsgError;
	
	$(IdnCuit).removeClassName(classFormError);
	$(msgId).update('');
	$(msgId).hide();

	var doc = $(IdnDoc).getValue();
	var cuit = $(IdnCuit).getValue();
	var ndoc_cuit = cuit.substr(2,8);
	if(doc!=ndoc_cuit){
		$(IdnCuit).focus();
		$(msgId).update('El CUIT no coincide con el Documento. VERIFIQUE!');
		$(msgId).show();
		$(IdnCuit).addClassName(classFormError);
		result = false;
	}
	return result;
}


// CONTROL DE FECHAS

function validDiaPosterior(fecha,hoy,msgId){
	
	if(msgId=='')msgId = contenedorMsgError;
	
	var result = true;
	
	var hoyStr 		= fechaToMkTime(hoy,'/');
	var fechaStr 	= fechaToMkTime(fecha,'/');	

	$(msgId).removeClassName(classFormError);
	
//	alert(hoy + " ** " + fecha);
//	alert(parseInt(hoyStr) + " ** " + parseInt(fechaStr));
	
	if(parseInt(hoyStr) > parseInt(fechaStr)){
		$(msgId).addClassName(classFormError);	
		$(msgId).update('La fecha debe ser posterior o igual a ' + hoy);
		$(msgId).show();
		result = false;
	
	}
	
	return result;
}

function fechaMayor(fecha, fecha2){
	var xMes=fecha.substring(3, 5);
	var xDia=fecha.substring(0, 2);
	var xAnio=fecha.substring(6,10);
	var yMes=fecha2.substring(3, 5);
	var yDia=fecha2.substring(0, 2);
	var yAnio=fecha2.substring(6,10);
	if (xAnio > yAnio){
		return(true);
	}else{
		if (xAnio == yAnio){
			if (xMes > yMes){
      			return(true);
			}
 			if (xMes == yMes){
				if (xDia > yDia){
					return(true);
				}else{
					return(false);
				}
			}else{
				return(false);
			}
		}else{
			return(false);
		}
	}
}

/**
 * 
 * @param {type} urlValidador
 * @param {type} cbu
 * @param {type} ndoc
 * @param {type} btnId
 * @param {type} spinnerId
 * @param {type} divId
 * @param {type} cbuInputId
 * @param {type} hiddenResultadoId
 * @returns {.response.responseText@call;evalJSON.Cbu.Validado|Boolean}
 */
function validateCbuNosis(urlValidador,cbu,ndoc,btnId,spinnerId,divId,cbuInputId,hiddenResultadoId){
    var url = urlValidador + '/' + ndoc + '/' + cbu;
    var validado = false;
    var msg = '';
    var error = false;
    if(cbu.length !== ''){
        $(btnId).disable();
        new Ajax.Request(url, {
            asynchronous:false,
            evalScripts:true,
            onInteractive: function(request){
                Element.show(spinnerId);
                Element.show(divId);
                $(btnId).disable();
            },  
            onSuccess: function(response) {
                $(spinnerId).hide();
                $(btnId).enable(); 
                
                var json = response.responseText.evalJSON();
                if(json.Resultado === null){
                    error = true;
                    validado = true;
                    msg = 'ATENCION:\nNo se pudo efecutar la validación del CBU';
                    msg = msg + '\n\n' + ' *** ERROR NO ESPECIFICADO EN LA COMUNICACION CON EL SERVICIO ***';
                    msg = msg + '\n\n' + 'Guardar de todos modos?';
                    
                }else if(json.Resultado.Estado >= '400'){
                    validado = true;
                    error = true;
                    msg = 'ATENCION:\nNo se pudo efecutar la validación del CBU';
                    msg = msg + '\n\nNOSIS: *** ' + json.Resultado.Novedad + ' ***';
                    msg = msg + '\n' + 'Transacción: ' + json.Resultado.Transaccion;
                    msg = msg + '\n' + 'Ref: ' + json.Resultado.Referencia;
                    msg = msg + '\n\n' + 'Guardar de todos modos?';                    
                }else{
                    validado = json.Datos.Cbu.Validado;
                    msg = 'NOSIS :: Servicio de Validación\n\n';
                    msg = msg + 'CBU: ' + json.Pedido.Cbu;
                    msg = msg + '\n\n';
                    msg = msg + json.Datos.Persona.Documento + ' ' + json.Datos.Persona.RazonSocial + ' (' + json.Datos.Persona.Sexo + ') ';
                    msg = msg + '\n\n' + json.Datos.Cbu.Estado;  
                    alert(msg);
                }
          }
        });
    }
    if(error){
        return confirm(msg);
    }
//    Element.show(divId);
//    $(cbuInputId).addClassName((validado ? 'notices_ok' : 'notices_error'));
//    $(divId).addClassName((validado ? 'notices_ok' : 'notices_error2'));
//    document.getElementById(hiddenResultadoId).value = (validado ? 1 : 0);
//    $(divId).update(msg);
    return validado;
}

/**
 * 
 * @param {type} urlValidador
 * @param {type} celular
 * @param {type} ndoc
 * @param {type} spinnerId
 * @returns {Number|.response.responseText@call;evalJSON.Datos.IdConsulta}
 */
function validateSMSNosis(urlValidador,celular,ndoc,spinnerId){
    var url = urlValidador + '/' + ndoc + '/' + celular;
    var validado = false;
    var msg = '';
    var error = false;
    var consultaId = 0;
    $(spinnerId).show();
//    $(btnId).disable();
    new Ajax.Request(url, {
        asynchronous:false,
        evalScripts:true,
        onInteractive: function(request){
            $(spinnerId).show();
//            Element.show(divId);
//            $(btnId).disable();
        },  
        onSuccess: function(response) {
            $(spinnerId).hide();
//            $(btnId).enable(); 

            var json = response.responseText.evalJSON();

            if(json.Resultado === null){
                error = true;
                validado = true;
                msg = 'ATENCION:\nNo se pudo efecutar la validación del Celular';
                msg = msg + '\n\n' + ' *** ERROR NO ESPECIFICADO EN LA COMUNICACION CON EL SERVICIO ***';
                msg = msg + '\n\n' + 'Guardar de todos modos?';

            }else if(json.Resultado.Estado >= '400'){
                validado = true;
                error = true;
                msg = 'ATENCION:\nNo se pudo efecutar la validación del Celular';
                msg = msg + '\n\nNOSIS: *** ' + json.Resultado.Novedad + ' ***';
                msg = msg + '\n' + 'Transacción: ' + json.Resultado.Transaccion;
                msg = msg + '\n' + 'Ref: ' + json.Resultado.Referencia;
                msg = msg + '\n\n' + 'Guardar de todos modos?';                    
            }else{
                validado = json.Datos.Sms.TokenEnviado;
                consultaId = json.Datos.IdConsulta;
                msg = 'NOSIS :: Servicio de Validación\n\n';
                msg = msg + json.Datos.Sms.Novedad; 
                if(validado === 0){
                    consultaId = null;
                    msg = msg + ' | Estado: ' + json.Datos.Sms.Estado;
                }else{
                    msg = msg + ' | PIN ENVIADO: ' + json.Datos.Sms.Prefijo + "#####";
                }
                alert(msg);
            }
      }
    });
    if(error){
        return confirm(msg);
    }
    return consultaId;    
}

/**
 * 
 * @param {type} urlValidador
 * @param {type} consultaId
 * @param {type} pin
 * @returns {.response.responseText@call;evalJSON.Datos.Sms.Validado|Boolean}
 */
function evaluateSMSNosis(urlValidador,consultaId,pin){
    var url = urlValidador + '/' + consultaId + '/' + pin;
    var validado = false;
    var msg = '';
    var error = false; 
    new Ajax.Request(url, {
        asynchronous:false,
        evalScripts:true,
        onInteractive: function(request){
        },  
        onSuccess: function(response) {
            var json = response.responseText.evalJSON();

            if(json.Resultado === null){
                error = true;
                validado = true;
                msg = 'ATENCION:\nNo se pudo efecutar la validación del Celular';
                msg = msg + '\n\n' + ' *** ERROR NO ESPECIFICADO EN LA COMUNICACION CON EL SERVICIO ***';
                msg = msg + '\n\n' + 'Guardar de todos modos?';

            }else if(json.Resultado.Estado >= '400'){
                validado = false;
                error = true;
                msg = 'ATENCION:\nNo se pudo efecutar la validación del Celular';
                msg = msg + '\n\nNOSIS: *** ' + json.Resultado.Novedad + ' ***';
                msg = msg + '\n' + 'Transacción: ' + json.Resultado.Transaccion;
                msg = msg + '\n' + 'Ref: ' + json.Resultado.Referencia;
                msg = msg + '\n\n' + 'Guardar de todos modos?';                    
            }else{
                validado = json.Datos.Sms.Validado;
                msg = 'NOSIS :: Servicio de Validación\n\n';
                msg = msg + 'El PIN ' + pin + ' fué ' + json.Datos.Sms.Estado;
                alert(msg);
            }
      }
    });
    if(error){
        return confirm(msg);
    }
    return validado;    
    
}

/**
 * 
 * @param {type} strcbu
 * @returns {Array}
 */
function cbu_split(strcbu){
    var cbu = strcbu.toString();
    //saco los datos del banco del CBU//
    var cbu_bco = cbu.substring(0,3);
    var cbu_suc = cbu.substring(3,7);
    var cbu_tcta = cbu.substring(8,10);
    var cbu_ncta = cbu.substring(10,21); 
    return [cbu_bco,cbu_suc,cbu_tcta,cbu_ncta];
}


