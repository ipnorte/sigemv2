/******************************************************************************************************************
LIBRERIA DE FUNCIONES GENERALES
*******************************************************************************************************************/

Number.prototype.formatMoney = function(c, d, t){
	var n = this, c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
	return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};

function FormatCurrency(numero){return (numero).formatMoney(2, '.', ',')}

// FormatCurrency
//function FormatCurrency(Expression)
//{
//    var iNumDecimals = 2;
//    var dbInVal = Expression;
//    var bNegative = false;
//    var iInVal = 0;
//    var strInVal
//    var strWhole = "", strDec = "";
//    var strTemp = "", strOut = "";
//    var iLen = 0;
//
//    if (dbInVal < 0)
//    {
//        bNegative = true;
//        dbInVal *= -1;
//    }
//
//    dbInVal = dbInVal * Math.pow(10, iNumDecimals)
//    iInVal = parseInt(dbInVal);
//    if ((dbInVal - iInVal) >= 0.5)
//    {
//        iInVal++;
//    }
//    strInVal = iInVal + "";
//    strWhole = strInVal.substring(0, (strInVal.length - iNumDecimals));
//    strDec = strInVal.substring((strInVal.length - iNumDecimals), strInVal.length);
//    while (strDec.length < iNumDecimals)
//    {
//        strDec = "0" + strDec;
//    }
//    iLen = strWhole.length;
//    if (iLen >= 3)
//    {
//        while (iLen > 0)
//        {
//            strTemp = strWhole.substring(iLen - 3, iLen);
//            if (strTemp.length == 3)
//            {
//                //strOut = "," + strTemp + strOut;
//                strOut = strTemp + strOut;
//                iLen -= 3;
//            }
//            else
//            {
//                strOut = strTemp + strOut;
//                iLen = 0;
//            }
//        }
//        if (strOut.substring(0, 1) == ",")
//        {
//            strWhole = strOut.substring(1, strOut.length);
//        }
//        else
//        {
//            strWhole = strOut;
//        }
//    }
//    if (bNegative)
//    {
//        return "-" + strWhole + "." + strDec;
//    }
//    else
//    {
//        return strWhole + "." + strDec;
//    }
//}

//soloNumeros
function soloNumeros(e,decimal,negativo){
	 var key = (document.all) ? e.keyCode : e.which;
	 if(!decimal){
		 return (key <= 13 || (key >= 48 && key <= 57 || key == 118 || key == 99));
	 }else if(negativo){
		 return (key <= 13 || (key >= 48 && key <= 57 || key == 45 || key == 46 || key == 118 || key == 99));
	 }else{
		 return (key <= 13 || (key >= 48 && key <= 57 || key == 46 || key == 118 || key == 99));
	 }
	 
}




function Left(str, n){
	if (n <= 0)
	    return "";
	else if (n > String(str).length)
	    return str;
	else
	    return String(str).substring(0,n);
}

function Right(str, n){
    if (n <= 0)
       return "";
    else if (n > String(str).length)
       return str;
    else {
       var iLen = String(str).length;
       return String(str).substring(iLen, iLen - n);
    }
}


function fillDocumento(personaDocumentoDomId){
    if($(personaDocumentoDomId).getValue()!== '')document.getElementById(personaDocumentoDomId).value = rellenar($(personaDocumentoDomId).getValue(),'0',8,'L');
    return true;
}


function rellenar(str,fill,len,dir){
	var relleno = '';
	var cadenaFormateada = '';
	str = Trim(str);
	for (j=0; j < len; j++){
		relleno = relleno + fill;
	}
	if(dir=='L'){
		cadenaFormateada = relleno + str;
		return Right(cadenaFormateada,len);
	}else if(dir=='R'){
		cadenaFormateada = str + relleno;
		return Left(cadenaFormateada,len);
	}else{
		return "";
	}
}

function Trim(strToTrim) {
  while(strToTrim.charAt(0)==' '){strToTrim = strToTrim.substring(1,strToTrim.length);}
  while(strToTrim.charAt(strToTrim.length-1)==' '){strToTrim = strToTrim.substring(0,strToTrim.length-1);}
  return strToTrim;
}



function currencyMaskFormat(fld, milSep, decSep, e,callFuncEnter) {
	  var sep = 0;
	  var key = '';
	  var i = 0;
	  var j = 0;
	  var len = 0;
	  var len2 = 0;
	  var strCheck = '0123456789';
	  var aux = '';
	  var aux2 = '';
	  
	  var whichCode = (document.all) ? e.keyCode : e.which; // 2
	  //var whichCode = window.Event ? e.which : e.keyCode;

	  if (whichCode == 13){
	  	window.setTimeout(callFuncEnter,1);
		return true;  // Enter
	  }

	  if (whichCode == 8 || whichCode == 0) return true;  // Delete

	  key = String.fromCharCode(whichCode);  // Get key value from key code

	  if (strCheck.indexOf(key) == -1) return false;  // Not a valid key
	  len = fld.value.length;
	  for(i = 0; i < len; i++)
	  if ((fld.value.charAt(i) != '0') && (fld.value.charAt(i) != decSep)) break;
	  aux = '';
	  for(; i < len; i++)
	  if (strCheck.indexOf(fld.value.charAt(i))!=-1) aux += fld.value.charAt(i);
	  aux += key;
	  len = aux.length;
	  if (len == 0) fld.value = '';
	  if (len == 1) fld.value = '0'+ decSep + '0' + aux;
	  if (len == 2) fld.value = '0'+ decSep + aux;
	  if (len > 2) {
	    aux2 = '';
	    for (j = 0, i = len - 3; i >= 0; i--) {
	      if (j == 3) {
	        aux2 += milSep;
	        j = 0;
	      }
	      aux2 += aux.charAt(i);
	      j++;
	    }
	    fld.value = '';
	    len2 = aux2.length;
	    for (i = len2 - 1; i >= 0; i--)
	    fld.value += aux2.charAt(i);
	    fld.value += decSep + aux.substr(len - 2, len);
	  }
	  return false;
	}

/**
 * @deprecated
 * @param idRw
 * @param OnOff
 * @param css
 * @return
 */
function cambiarColorRwTabla(idRw,OnOff,css){
	var celdas = $(idRw).immediateDescendants();
	if(OnOff)celdas.each(function(i){i.addClassName(css)})
	else celdas.each(function(i){i.removeClassName(css)})
}


function toggleCell(idRw, oChk){
	var check = oChk.checked;
	var celdas = $(idRw).immediateDescendants();
	if(check)celdas.each(function(td){td.addClassName("selected");});
	else celdas.each(function(td){td.removeClassName("selected");});
}




function fechaToMkTime(fecha,separador){
	var elems = fecha.split(separador);
	var m = parseInt(elems[1],10);
	var d = parseInt(elems[0],10);
	var y = parseInt(elems[2],10);	
	var oFecha = null;
	oFecha = new Date(y,m,d);
	return mktime2(0, 0, 0, oFecha.getMonth(), oFecha.getDate(), oFecha.getFullYear());	
}


function mktime() {
    // Get UNIX timestamp for a date  
    // 
    // version: 905.3122
    // discuss at: http://phpjs.org/functions/mktime
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: baris ozdil
    // +      input by: gabriel paderni
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: FGFEmperor
    // +      input by: Yannoo
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: jakes
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   bugfixed by: Marc Palau
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // *     example 1: mktime(14, 10, 2, 2, 1, 2008);
    // *     returns 1: 1201871402
    // *     example 2: mktime(0, 0, 0, 0, 1, 2008);
    // *     returns 2: 1196463600
    // *     example 3: make = mktime();
    // *     example 3: td = new Date();
    // *     example 3: real = Math.floor(td.getTime()/1000);
    // *     example 3: diff = (real - make);
    // *     results 3: diff < 5
    // *     example 4: mktime(0, 0, 0, 13, 1, 1997)
    // *     returns 4: 883609200
    // *     example 5: mktime(0, 0, 0, 1, 1, 1998)
    // *     returns 5: 883609200
    // *     example 6: mktime(0, 0, 0, 1, 1, 98)
    // *     returns 6: 883609200
    var no=0, i = 0, ma=0, mb=0, d = new Date(), dn = new Date(), argv = arguments, argc = argv.length;

    var dateManip = {
        0: function(tt){ return d.setHours(tt); },
        1: function(tt){ return d.setMinutes(tt); },
        2: function(tt){ var set = d.setSeconds(tt); mb = d.getDate() - dn.getDate(); return set;},
        3: function(tt){ var set = d.setMonth(parseInt(tt, 10)-1); ma = d.getFullYear() - dn.getFullYear(); return set;},
        4: function(tt){ return d.setDate(tt+mb);},
        5: function(tt){
            if (tt >= 0 && tt <= 69) {
                tt += 2000;
            }
            else if (tt >= 70 && tt <= 100) {
                tt += 1900;
            }
            return d.setFullYear(tt+ma);
        }
        // 7th argument (for DST) is deprecated
    };

    for( i = 0; i < argc; i++ ){
        no = parseInt(argv[i]*1, 10);
        if (isNaN(no)) {
            return false;
        } else {
            // arg is number, let's manipulate date object
            if(!dateManip[i](no)){
                // failed
                return false;
            }
        }
    }
    for (i = argc; i < 6; i++) {
        switch(i) {
            case 0:
                no = dn.getHours();
                break;
            case 1:
                no = dn.getMinutes();
                break;
            case 2:
                no = dn.getSeconds();
                break;
            case 3:
                no = dn.getMonth()+1;
                break;
            case 4:
                no = dn.getDate();
                break;
            case 5:
                no = dn.getFullYear();
                break;
        }
        dateManip[i](no);
    }

    return Math.floor(d.getTime()/1000);
}



function toggle(elementId){
	$(elementId).toggle();
}


function getTextoSelect(id){
	var sel = document.getElementById(id);
	return sel.options[sel.selectedIndex].text;
}

function getStrFecha(id){
	var idDia = id + "Day";
	var idMes = id + "Month";
	var idAnio = id + "Year";
	return getTextoSelect(idDia) + "-" + getTextoSelect(idMes) + "-" + getTextoSelect(idAnio);
}

function getStrPeriodo(id){
	var idMes = id + "Month";
	var idAnio = id + "Year";
	return getTextoSelect(idMes) + "/" + getTextoSelect(idAnio);
}




function mktime2() {
    // Get UNIX timestamp for a date  
    // 
    // version: 1009.2513
    // discuss at: http://phpjs.org/functions/mktime    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: baris ozdil
    // +      input by: gabriel paderni
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: FGFEmperor    // +      input by: Yannoo
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: jakes
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   bugfixed by: Marc Palau    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +      input by: 3D-GRAF
    // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
    // +      input by: Chris
    // +    revised by: Theriault    // %        note 1: The return values of the following examples are
    // %        note 1: received only if your system's timezone is UTC.
    // *     example 1: mktime(14, 10, 2, 2, 1, 2008);
    // *     returns 1: 1201875002
    // *     example 2: mktime(0, 0, 0, 0, 1, 2008);    // *     returns 2: 1196467200
    // *     example 3: make = mktime();
    // *     example 3: td = new Date();
    // *     example 3: real = Math.floor(td.getTime() / 1000);
    // *     example 3: diff = (real - make);    // *     results 3: diff < 5
    // *     example 4: mktime(0, 0, 0, 13, 1, 1997)
    // *     returns 4: 883612800 
    // *     example 5: mktime(0, 0, 0, 1, 1, 1998)
    // *     returns 5: 883612800     // *     example 6: mktime(0, 0, 0, 1, 1, 98)
    // *     returns 6: 883612800 
    // *     example 7: mktime(23, 59, 59, 13, 0, 2010)
    // *     returns 7: 1293839999
    // *     example 8: mktime(0, 0, -1, 1, 1, 1970)    // *     returns 8: -1
    var d = new Date(), r = arguments, i = 0,
        e = ['Hours', 'Minutes', 'Seconds', 'Month', 'Date', 'FullYear'];
 
    for (i = 0; i < e.length; i++) {        if (typeof r[i] === 'undefined') {
            r[i] = d['get' + e[i]]();
            r[i] += (i === 3); // +1 to fix JS months.
        } else {
            r[i] = parseInt(r[i], 10);            if (isNaN(r[i])) {
                return false;
            }
        }
    }    
    // Map years 0-69 to 2000-2069 and years 70-100 to 1970-2000.
    r[5] += (r[5] >= 0 ? (r[5] <= 69 ? 2e3 : (r[5] <= 100 ? 1900 : 0)) : 0);
    
    // Set year, month (-1 to fix JS months), and date.    // !This must come before the call to setHours!
    d.setFullYear(r[5], r[3] - 1, r[4]);
    
    // Set hours, minutes, and seconds.
    d.setHours(r[0], r[1], r[2]); 
    // Divide milliseconds by 1000 to return seconds and drop decimal.
    // Add 1 second if negative or it'll be off from PHP by 1 second.
    return (d.getTime() / 1e3 >> 0) - (d.getTime() < 0);
}



