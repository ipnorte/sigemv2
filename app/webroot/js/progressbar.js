/**
* @author Adrian
* @copyright 2012 CordobasSoft IT www.cordobasoft.com
* @license MIT
* @require prototype.js, livepipe.js
*/

if(typeof(Prototype) === "undefined") { throw "Control.ProgressBar requires Prototype to be loaded."; }
if(typeof(Object.Event) === "undefined") { throw "Control.ProgressBar requires Object.Event to be loaded."; }

Control.ProgressBar = Class.create({
	
    initialize: function(container,responseURL,actionURL,actionTarget,pid,uuid,options){
    	
    	this.errores = 0;
    	
    	this.btnStartId = "progress_bar_start_" + uuid;
    	this.btnStopId = "progress_bar_stop_" + uuid;
    	this.btnActionId = "progress_bar_action_" + uuid;
    	this.progressPorcId = "progress_bar_porcentaje_" + uuid;
    	this.progressMsgId = "progress_bar_mensaje_" + uuid;
    	this.progressMsgError = $("progress_bar_errores_" + uuid);
    	
    	this.progressMsgError.hide();
    	
		//////////////////////////////////////////////////////////////////////////
    	//SETEO LOS VALORES INICIALES
		//////////////////////////////////////////////////////////////////////////
    	$(this.btnStartId).enable();
    	$(this.btnStopId).disable();
    	$(this.btnActionId).disable();
    	
    	this.mensaje = "";
    	this.estado = "C";
    	this.urlStatus = responseURL + '?PID=' + pid + "&ACTION=STATUS";
    	this.urlStart = responseURL + '?PID=' + pid + "&ACTION=START";
    	this.urlStop = responseURL + '?PID=' + pid + "&ACTION=STOP";
    	this.urlAction = actionURL + '/?pid=' + pid;
    	this.actionTarget = actionTarget || "";
    	this.pid = pid;
    	//////////////////////////////////////////////////////////////////////////
        this.progress = 0;
        this.executer = false;
        this.active = false;
        this.poller = false;
        this.container = $(container);
        this.containerWidth = this.container.getDimensions().width - (parseInt(this.container.getStyle('border-right-width').replace(/px/,''), 10) + parseInt(this.container.getStyle('border-left-width').replace(/px/,''), 10));
        this.progressContainer = $(document.createElement('div'));
        this.progressContainer.setStyle({
            width: this.containerWidth + 'px',
            height: '29px',
            position: 'absolute',
            top: '0px',
            right: '0px'
        });
        this.container.appendChild(this.progressContainer);
        this.options = {
            afterChange: Prototype.emptyFunction,
            interval: JSON.parse(options).interval,
            step: 1,
            classNames: {
                active: 'progress_bar_active',
                inactive: 'progress_bar_inactive'
            }
        };
        Object.extend(this.options,options || {});
        this.container.addClassName(this.options.classNames.inactive);
        this.active = false;
        this.setProgress(0);
    },
    setProgress: function(value,mensaje,estado){
    	this.progress = value;
        this.mensaje = mensaje;
        this.estado = estado;
        this.draw();
        if(this.progress >= 100 && this.estado == 'F') {
            //////////////////////////////////////////////////////////////////////////
            $(this.btnActionId).enable();
            $(this.btnStartId).disable();
            $(this.btnStopId).disable();
            //////////////////////////////////////////////////////////////////////////
        }
        this.notify('afterChange',this.progress,this.active);
    },
    poll: function (url, interval, ajaxOptions){
        // Extend the passed ajax options and success callback with our own.
        ajaxOptions = ajaxOptions || {};
        var success = ajaxOptions.onSuccess || Prototype.emptyFunction;
        ajaxOptions.onSuccess = success.wrap(function (callOriginal, request) {
            var resp = request.responseText.evalJSON();
            this.errores = parseInt(resp.ERRORES); 
        	//////////////////////////////////////////////////////////////////////////
            this.setProgress(parseInt(resp.PORCENTAJE, 10),resp.MENSAJE,resp.ESTADO);
            //////////////////////////////////////////////////////////////////////////
            if(!this.active || resp.ESTADO == 'S' || resp.ESTADO == 'F') { 
                this.poller.stop();
            	if(resp.ESTADO == 'S'){
            		this.mensaje = "PROCESO DETENIDO POR EL USUARIO!";
            		$(this.progressMsgId).update(this.mensaje);
            }
            	if(resp.ESTADO == 'F'){$(this.btnActionId).focus();}
            }
            callOriginal(request);
        }).bind(this);
        
        if(this.errores !== 0) {
            console.log(`Errores detectados: ${this.errores}`);
            this.progressMsgError.update(`${this.errores} ERROR/es DETECTADOS!`);
            this.progressMsgError.show();
        }        
        
        this.active = true;
        this.poller = new PeriodicalExecuter(function(){
        var a = new Ajax.Request(url, ajaxOptions);
        }.bind(this),interval || 1);
    },
    
    start: function() {
    $(this.btnStartId).disable();
    this.active = true;

    // Arranco el proceso remoto
    new Ajax.Request(this.urlStart, {
        method: 'get',
        onSuccess: function(transport) {
            var response = transport.responseText || "";
            $(this.progressMsgId).update("PROCESO CREADO / INICIADO CORRECTAMENTE!");
            
            // Llamo al control de estado después de una respuesta exitosa
            this.poll(this.urlStatus, this.options['interval'], {});
            this.container.removeClassName(this.options.classNames.inactive);
            this.container.addClassName(this.options.classNames.active);
        }.bind(this),
        onFailure: function() { alert('ERROR AL CREAR / INICIAR EL PROCESO REMOTO'); }
    });
    },
    
    stop: function(reset) {
        $(this.btnStopId).disable();
        $(this.progressMsgId).update("PROCESO DETENIDO POR EL USUARIO!");

        // Envio el comando de detener
        new Ajax.Request(this.urlStop, {
            method: 'get',
            onSuccess: function(transport) {
                var response = transport.responseText || "";
                if (response === "1") {
                    this.mensaje = "PROCESO DETENIDO POR EL USUARIO!";
                    this.active = false;
                    if (this.executer) { this.executer.stop(); }
                    this.poller.stop();
                    this.container.removeClassName(this.options.classNames.active);
                    this.container.addClassName(this.options.classNames.inactive);
                    if (typeof reset === 'undefined' || reset === true) { this.reset(); }
                    this.notify('afterChange', this.progress, this.active);
                    $(this.progressMsgId).update(this.mensaje);
                }
            }.bind(this),
            onFailure: function() { alert('ERROR AL INTENTAR DETENER EL PROCESO REMOTO'); }
        });
    },

    
    
    
    step: function(amount){
        this.active = true;
        this.setProgress(Math.min(100,this.progress + amount));
    },
    action: function(){
        this.active = false;
        if(this.errores !== 0){
        	if(confirm("ATENCION!!\nSE DETECTARON " + this.errores + " ERRORES EN EL PROCESO #" + this.pid + ".\nDESEA CONTINUAR?"))window.open(this.urlAction,this.actionTarget);
        }else{
        	window.open(this.urlAction,this.actionTarget);
        }
    	$(this.btnStartId).disable();
    	$(this.btnStopId).disable();
    	$(this.btnActionId).enable();
    },
    draw: function(){
        this.progressContainer.setStyle({
            width: (this.containerWidth - Math.floor((parseInt(this.progress, 10) / 100) * this.containerWidth)) + 'px'
        });
        //nuestro los valores
        $(this.progressPorcId).update(this.progress + "%");
        $(this.progressMsgId).update(this.mensaje);
        if(this.errores !== 0){
        	this.progressMsgError.update(this.errores + " ERROR/es DETECTADOS!");
        	this.progressMsgError.show();
        }
    },
    notify: function(event_name){
        if(this.options[event_name]) {return [this.options[event_name].apply(this.options[event_name],$A(arguments).slice(1))]; }
    }
    

    
});
Object.Event.extend(Control.ProgressBar);



