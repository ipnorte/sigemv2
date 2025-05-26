
var card_types = [
	{
		name: 'amex',
		pattern: /^3[47]/,
		valid_length: [15]
	},
	{
		name: 'diners_club_carte_blanche',
		pattern: /^30[0-5]/,
		valid_length: [14]
	},
	{
		name: 'diners_club_international',
		pattern: /^36/,
		valid_length: [14]
	},
	// {
	//   name: 'jcb',
	//   pattern: /^35(2[89]|[3-8][0-9])/,
	//   valid_length: [16]
	// }, 
	{
		name: 'laser',
		pattern: /^(6304|670[69]|6771)/,
		valid_length: [16, 17, 18, 19]
	},
	{
		name: 'visa_electron',
		pattern: /^(4026|417500|4508|4844|491(3|7))/,
		valid_length: [16]
	},
	{
		name: 'visa',
		pattern: /^4/,
		valid_length: [16]
	},
	{
		name: 'mastercard',
		pattern: /^5[1-5]/,
		valid_length: [16]
	},
	{
		name: 'maestro',
		pattern: /^(5010|5018|5020|5038|6304|6759|676[1-3])/,
		valid_length: [12, 13, 14, 15, 16, 17, 18, 19]
	},
	{
		name: 'discover',
		pattern: /^(6011|622(12[6-9]|1[3-9][0-9]|[2-8][0-9]{2}|9[0-1][0-9]|92[0-5]|64[4-9])|65)/,
		valid_length: [16]
	}
];

/**
 * 
 * @param {*} ccNumb 
 */
function Mod10(ccNumb) {
	var valid = "0123456789"  // Valid digits in a credit card number
	var len = ccNumb.length;  // The length of the submitted cc number

	var iCCN = parseInt(ccNumb);  // integer of ccNumb
	var sCCN = ccNumb.toString();  // string of ccNumb
	sCCN = sCCN.replace(/^\s+|\s+$/g, '');  // strip spaces

	// //tomar los primeros 18 numeros
	// alert(len);
	// if(len > 16){
	//   sCCN = sCCN.substring(0,16);
	//   iCCN = parseInt(sCCN);
	//   len = sCCN.length;  
	//   alert(len + ' * ' + iCCN); 

	// }

	var iTotal = 0;  // integer total set at zero
	var bNum = true;  // by default assume it is a number
	var bResult = false;  // by default assume it is NOT a valid cc
	var temp;  // temp variable for parsing string
	var calc;  // used for calculation of each digit

	// Determine if the ccNumb is in fact all numbers
	for (var j = 0; j < len; j++) {
		temp = "" + sCCN.substring(j, j + 1);
		if (valid.indexOf(temp) == "-1") { bNum = false; }
	}

	if (!bNum) {
		bResult = false;
	}

	// Determine if it is the proper length 
	if ((len == 0) && (bResult)) {  // nothing, field is blank AND passed above # check
		bResult = false;
	} else {  // ccNumb is a number and the proper length - let's see if it is a valid card number
		if (len >= 15) {  // 15 or 16 for Amex or V/MC

			for (var i = len; i > 0; i--) {  // LOOP throught the digits of the card
				calc = parseInt(iCCN) % 10;  // right most digit
				calc = parseInt(calc);  // assure it is an integer
				iTotal += calc;  // running total of the card number as we loop - Do Nothing to first digit
				i--;  // decrement the count - move to the next digit in the card
				iCCN = iCCN / 10;                               // subtracts right most digit from ccNumb
				calc = parseInt(iCCN) % 10;    // NEXT right most digit
				calc = calc * 2;                                 // multiply the digit by two
				// Instead of some screwy method of converting 16 to a string and then parsing 1 and 6 and then adding them to make 7,
				// I use a simple switch statement to change the value of calc2 to 7 if 16 is the multiple.
				switch (calc) {
					case 10: calc = 1; break;       //5*2=10 & 1+0 = 1
					case 12: calc = 3; break;       //6*2=12 & 1+2 = 3
					case 14: calc = 5; break;       //7*2=14 & 1+4 = 5
					case 16: calc = 7; break;       //8*2=16 & 1+6 = 7
					case 18: calc = 9; break;       //9*2=18 & 1+8 = 9
					default: calc = calc;           //4*2= 8 &   8 = 8  -same for all lower numbers
				}
				iCCN = iCCN / 10;  // subtracts right most digit from ccNum
				iTotal += calc;  // running total of the card number as we loop
			}  // END OF LOOP
			if ((iTotal % 10) == 0) {  // check to see if the sum Mod 10 is zero
				bResult = true;  // This IS (or could be) a valid credit card number.
			} else {
				bResult = false;  // This could NOT be a valid credit card number
			}
		}
	}
	// // change alert to on-page display or other indication as needed.
	// if(bResult) {
	//     alert("This IS a valid Credit Card Number!");
	// }
	// if(!bResult){
	//     alert("This is NOT a valid Credit Card Number!");
	// }
	return bResult; // Return the results        

}


function get_card_type(number) {
	card_type = null;
	for (_j = 0, _len1 = card_types.length; _j < _len1; _j++) {
		card_type = card_types[_j];
		if (number.match(card_type.pattern)) {
			return card_type;
		}
	}
	return card_type;
}

/**
 * 
 * @param {*} ccNumb 
 */
function validateNumber(ccNumb) {
	validate = true;
	$(ccNumb.id).removeClassName('form-error');
	$('card_icon').toggleClassName('empty');
	var myClass = $("card_icon").readAttribute("class");
	$('card_icon').removeClassName(myClass);

	// alert(ccNumb);
	if (!isNaN(ccNumb.value) && ccNumb.value !== '') {
		validate = isValidCard(ccNumb.value);
		if (!validate) {
			$(ccNumb.id).focus();
			$(ccNumb.id).addClassName('form-error');
			alert("El numero de Tarjeta es incorrecto");
			return false;
		} else {
			var a = get_card_type(ccNumb.value);
			if (myClass !== a.name) {
				$('card_icon').toggleClassName(a.name);
			}
		}
	}
}

/**
 * 
 * @param {*} ccNumb 
 */
function validateNumberJQUERY(ccNumb) {
	validate = true;
	$(ccNumb.id).removeClass('form-error');
	// $('#card_icon').addClass('empty');
	$("#card_icon").toggleClass("empty");
	var myClass = $("#card_icon").attr("class");
	$('#card_icon').removeClass(myClass);


	if (!isNaN(ccNumb.value) && ccNumb.value !== '') {
		validate = isValidCard(ccNumb.value);
		if (!validate) {
			$(ccNumb.id).focus();
			$(ccNumb.id).addClass('form-error');
			alert("El numero de Tarjeta es incorrecto");
			return false;
		} else {
			var a = get_card_type(ccNumb.value);
			if (myClass !== a.name) {
				$("#card_icon").toggleClass(a.name);
			}

			//   $('#card_icon').removeClass('empty');
			//   $('#card_icon').addClass(a.name);

		}
	}
	return validate;
}

/**
 * Algoritmo de Luhn
 * @param {*} tarjeta 
 */
function isValidCard(tarjeta) {
	//Se coloca una variable numerica para poder hacer las operaciones
	var numero = 0;
	//Ingresar los datos, si tiene espacios o esta vacio y si no son numeros regresar a prompt
	// tarjeta = prompt ('Ingresa un número de tarjeta');
	//   if (tarjeta === null || (isNaN(tarjeta) === true) ){
	//     alert('Ingrese un número de tarjeta válido');
	//     return isValidCard(tarjeta);
	//     }
	//Se van a iterar caracter numerico por caracter
	for (var j = 0; j < tarjeta.length; j++) {
		if (isNaN(tarjeta.charAt(j)) === true)
			numero++
	}
	if (numero > 0) {
		alert('El número de tarjeta contiene letras o carácteres especiales, ingrese un número de tarjeta válido.');
		// return isValidCard(tarjeta);
		return false;
	}
	//Despues de validar si los digitos con correcto
    /*Se estara iterando numero a numero, con un array inverso, se estaran sumando y invirtiendo el array
    con los numeros pares*/
	var sum = 0,
		alt = false,
		i = tarjeta.length - 1,
		num;
	//Si el numero de caracteres de la tarjeta proporcionada son menores a 13 o mayores a 19
	//la tarjeta se regresa al prompt
	if (tarjeta.length < 13 || tarjeta.length > 19) {
		alert('El número de tarjeta tiene que ser mayor a 13 y menor a 19 dígitos.');
		// return isValidCard(tarjeta);
		return false;
	}
	//Mientras los numeros sea mayor o igual a 0 se estara tomando cada caracter
	while (i >= 0) {
		//Se estaran tomando cada caracter numerico enteros ingresado en tarjeta
		num = parseInt(tarjeta.charAt(i), 10);
		//Valida que el número sea válido
		if (isNaN(num)) {
			return false;
		}
		//Válida el cambio true o false de imparidad
		if (alt) {
			num *= 2;
			if (num > 9) {
				num = (num % 10) + 1;
			}
		}
		//Voltea el bit de paridad
		alt = !alt;
		//Agrega el número
		sum += num;
		//Continúa con el siguiente dígito
		i--;
	}

	//para las caja 40 del BNA
	if (tarjeta.length > 18) return true;

	//Determina si la tarjeta es válida
	return (sum % 10 === 0 && sum !== 0);
	// if (sum % 10 === 0 && sum!==0){
	//   return alert('Tarjeta válida');
	// }
	//   else{
	//   return alert('Tarjeta inválida');
	//   }


}


function controlVigenciaTarjetaDebito(mes, anio, mesesMinimo) {

	var mesNumber = new Number(parseInt(mes));
	var anioNumber = new Number(parseInt(anio));
	var mesesMinNumber = new Number(parseInt(mesesMinimo));


	var hoy = new Date();
	var fechaMinima = new Date(hoy.getFullYear(), hoy.getMonth() + mesesMinNumber + 1, hoy.getDay());
	var fechaTarjeta = new Date((2000 + anioNumber), (mesNumber), hoy.getDay());

	//alert(mesNumber + '/' + anioNumber + '\n' + hoy + '\n' + fechaMinima + '\n' + fechaTarjeta + '\n' + '\n\n' +  
	//hoy.getTime() + '\n' + fechaMinima.getTime() + '\n' + fechaTarjeta.getTime() + '\n');

	if (fechaTarjeta.getTime() < hoy.getTime()) {
		alert('La tarjeta vencida!');
		return false;
	} else if (fechaTarjeta.getTime() < fechaMinima.getTime()) {
		var msg = 'El vencimiento de la tarjeta debe ser posterior o igual a: ';
		msg = msg + ' ** ' + (fechaMinima.getMonth() + 1) + '/' + fechaMinima.getFullYear() + ' **';
		alert(msg);
		return false;
	}
	return true;
}

function validarTarjetaRequiredJQuery() {

}

function validarTarjetaRequired(mesesMinimo) {

	$('TarjetaDebitoCardNumber').removeClassName('form-error');
	$('TarjetaDebitoCardHolderName').removeClassName('form-error');
	$('TarjetaDebitoSecurityCode').removeClassName('form-error');

	var ccNumb = document.getElementById('TarjetaDebitoCardNumber').value;
	var ccNomb = document.getElementById('TarjetaDebitoCardHolderName').value;
	var ccSC = document.getElementById('TarjetaDebitoSecurityCode').value;

	if (ccNomb === '') {
		$('TarjetaDebitoCardHolderName').addClassName('form-error');
		$('TarjetaDebitoCardHolderName').focus();
		return false;
	}

	if (isNaN(ccNumb) || ccNumb === '') {
		$('TarjetaDebitoCardNumber').addClassName('form-error');
		$('TarjetaDebitoCardNumber').focus();
		return false;
	}
	if (isNaN(ccSC) || ccSC === '') {
		$('TarjetaDebitoSecurityCode').addClassName('form-error');
		$('TarjetaDebitoSecurityCode').focus();
		return false;
	}
	return validarTarjeta(mesesMinimo);
}


function validarTarjeta(mesesMinimo) {

	ret = true;
	$('TarjetaDebitoCardExpirationMonth').removeClassName('form-error');
	$('TarjetaDebitoCardExpirationYear').removeClassName('form-error');
	$('TarjetaDebitoCardHolderName').removeClassName('form-error');
	$('TarjetaDebitoSecurityCode').removeClassName('form-error');

	var ccNumb = document.getElementById('TarjetaDebitoCardNumber').value;
	var ccNomb = document.getElementById('TarjetaDebitoCardHolderName').value;
	var ccSC = document.getElementById('TarjetaDebitoSecurityCode').value;

	if (!isNaN(ccNumb) && ccNumb !== '') {

		if (ccNomb === '') {
			$('TarjetaDebitoCardHolderName').focus();
			$('TarjetaDebitoCardHolderName').addClassName('form-error');
			return false;
		}

		if (isNaN(ccSC) || ccSC === '') {
			$('TarjetaDebitoSecurityCode').focus();
			$('TarjetaDebitoSecurityCode').addClassName('form-error');
			return false;
		}

		var mes = document.getElementById('TarjetaDebitoCardExpirationMonth').value;
		var anio = document.getElementById('TarjetaDebitoCardExpirationYear').value;
		ret = controlVigenciaTarjetaDebito(mes, anio, mesesMinimo);
		if (!ret) {
			$('TarjetaDebitoCardExpirationMonth').focus();
			$('TarjetaDebitoCardExpirationMonth').addClassName('form-error');
			$('TarjetaDebitoCardExpirationYear').addClassName('form-error');
		}

	}
	return ret;

}


