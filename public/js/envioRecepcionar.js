//Variables
var envioTemporal = {
    id:'',
    no_guia: '',
    cod_tracking: '',
    peso: 0.0,
    //nacionalidad_remitente
    pais_origen: '',
    //currier
    agencia: '',
    //interes_aduana
    entidad_ctrl_aduana: false,
    provincia: '',
    municipio: '',
    requiere_pareo: false,
    pareo: '',
    //anomalias
    irregularidades: [],
    destinatario: null,
    remitente: null,
    //direcciones: []
    modo_recepcion: ModoRecepcion.MANIFESTADO

};
var listEnviosTemporles = new Array();
class ModoRecepcion {
    static MANIFESTADO = 'MANIFESTADO';
    static SINMANIFESTAR = 'SINMANIFESTAR';
}


/**
 * Buscar un envio en la tabla manisfestados
 */
function buscarEnvioManifestado()
{
    var noGuia = $('#input_noGuia').val()
    var codTracking = $('#input_codTracking').val()
    var sinManifestar = $('#check_envioSinManifestar').is(':checked')

    var ruta = Routing.generate('envio_manifestado')
    $.ajax({
        type: 'POST',
        url: ruta,
        data: {
            noGuia: noGuia,
            codTracking: codTracking,
            sinManifestar: sinManifestar
        },
        async: true,
        dataType: 'json',
        loading: '',
        success: function (data) {
            console.log('success', data)
            if (!(data.estado)) {
                //alert(data.mensaje);
                swal({
                    title: "Error",
                    text: data.mensaje,
                    type: "error"
                });
                console.log(data.mensaje);
                limpiarCampos();
            } else {
                //alert("Recibido OK");
                if (data.data.requiere_pareo){
                    swal({
                        title: "Informacion",
                        text: "Este envio requiere ser pareado.",
                        type: "info"
                    });
                }
                envioTemporal = data.data
                asignarValoresDeEnvioManifestado();
            }

        },
        error: function (error) {
            alert('Error: ' + error.status + ' ' + error.statusText);
            console.log('error', error.responseText)
            limpiarCampos();
        }
    })


}

/**
 * Buscar un envio en la tabla envio por el codigo tracking y que sea del año calendario actual
 */
function buscarEnvioSinManifestar()
{
    var codTracking = $('#input_codTracking').val()
    var sinManifestar = $('#check_envioSinManifestar').is(':checked')

    var ruta = Routing.generate('envio_manifestado')
    $.ajax({
        type: 'POST',
        url: ruta,
        data: {
            codTracking: codTracking,
            sinManifestar: sinManifestar
        },
        async: true,
        dataType: 'json',
        loading: '',
        success: function (data) {
            console.log('success', data)
            if (!(data.estado)) {
                //alert(data.mensaje);
                swal({
                    title: "Error",
                    text: data.mensaje,
                    type: "error"
                });
                console.log(data.mensaje);
                limpiarCampos();
            } else {
                //alert("Recibido OK");
                if (data.data.requiere_pareo){
                    swal({
                        title: "Informacion",
                        text: "Este envio requiere ser pareado.",
                        type: "info"
                    });
                }
                envioTemporal.requiere_pareo = data.data.requiere_pareo
                //asignarValoresDeEnvioManifestado();
            }

        },
        error: function (error) {
            alert('Error: ' + error.status + ' ' + error.statusText);
            console.log('error', error.responseText)
            limpiarCampos();
        }
    })


}

/**
 * Añadir envios trakeados al listado temporal, para posteriormente recepcionarlos
 */
function annadirEnvioAListTemporal()
{
    no_guia = $('#input_noGuia').val()
    cod_tracking = $('#input_codTracking').val().toUpperCase();
    peso = $('#input_peso').val()
    nacionalidad = $('#select_nacionalidadOrigen').val()
    agencia = $('#select_producto').val()
    entidadCtrlAduana = $('#check_entidadControlAduana').is(':checked')
    provincia = $('#select_provincias').val()
    municipio = $('#select_municipios').val()
    pareo = $('#input_pareo').val()

    console.log(municipio, 'log mun')

    //Si hay algun campo vacio o necesita pareo y no ha sido pareado
    if( !( this.validarCamposVacios(no_guia,cod_tracking,peso.toString(),nacionalidad,agencia,provincia,municipio,pareo)) ){

        swal({
            title: "Error",
            text: "Revise que todos los campos requeridos(*) esten correctamente.",
            type: "error"
        });

    //Si todos los campos estan correctos pero ya añadio un envio con ese codigo tracking
    }else if( this.buscarEnvioPorCodTracking(cod_tracking) ){

        swal({
            title: "Error",
            text: "Ya tiene añadido un envio con ese codigo tracking.",
            type: "error"
        });

    //Insertar en el listado y la tabla
    }else {

        this.envioTemporal.cod_tracking = cod_tracking
        this.envioTemporal.peso = peso
        this.envioTemporal.pais_origen = nacionalidad
        this.envioTemporal.agencia = agencia
        this.envioTemporal.entidad_ctrl_aduana = entidadCtrlAduana
        this.envioTemporal.provincia = provincia
        this.envioTemporal.municipio = municipio
        this.envioTemporal.pareo = pareo

        this.envioTemporal['extra'] = {
            nacionalidad: $('#select_nacionalidadOrigen option:selected').text(),
            agencia: $('#select_producto option:selected').text(),
            provincia: $('#select_provincias option:selected').text(),
            municipio: $('#select_municipios option:selected').text()
        }

        this.envioTemporal.irregularidades = [];
        this.envioTemporal.irregularidades = obtenerIrregularidades();

        listEnviosTemporles.push(this.envioTemporal)

        this.pintarTablaEnviosPreRecepcionados();

        this.limpiarCampos();
    }
}

/**
 * Pintar la tabla de envios prerecepcionados a partir del listado de envios prerecepcionados
 */
function pintarTablaEnviosPreRecepcionados() {
    var valor = '';

   for (var i = 0; i < this.listEnviosTemporles.length; i++) {
        console.log(i, 'i')
        console.log(this.listEnviosTemporles.length, 'length')
        valor += '<tr>' +
            '<td>' + (i+1) + '</td>' +
            '<td>' + this.listEnviosTemporles[i].cod_tracking + '</td>' +
            '<td>' + this.listEnviosTemporles[i].peso + '</td>' +
            '<td>' + this.listEnviosTemporles[i].extra.nacionalidad + '</td>' +
            '<td>' + this.listEnviosTemporles[i].extra.agencia + '</td>' +
            '<td>' + this.listEnviosTemporles[i].extra.provincia + '</td>' +
            '<td>' + this.listEnviosTemporles[i].extra.municipio + '</td>' +
            '<td> <button class="btn btn-danger" onclick="eliminarEnvioTablaPreRecepcionados(' + i + ')"><i class="fa fa-trash"></i></button> </td>' +
            '</tr>';
        $("#resultado").html(valor)
    }

}

/**
 * Eliminar envios de la lista de envios prerecepcionados y mandar ha actualizar la tabla visual
 */
function eliminarEnvioTablaPreRecepcionados(postionArray){

    if (this.listEnviosTemporles.length == 1){
        this.listEnviosTemporles.shift();
        limpiarTodosLosItemsDeLaTabla();
    }else {
        this.listEnviosTemporles.splice(postionArray,1)
        this.pintarTablaEnviosPreRecepcionados()
    }
}

/**
* Validar Campos vacios
*/
function validarCamposVacios(no_guia,cod_tracking,peso,nacionalidad,agencia,provincia,municipio,pareo)
{
    //Si hay algun campo vacio o necesita pareo y no ha sido pareado

    if( no_guia.length === 0){
        console.log('no_guia: '+no_guia,'logs')
        return false;
    }else if(cod_tracking.length === 0){
        console.log(' cod_tracking: '+ cod_tracking,'logs')
        return false;
    }else if(peso.length === 0){
        console.log(' peso: '+ peso,'logs')
        return false;
    }else if(nacionalidad.length === 0){
        console.log(' nacionalidad: '+ nacionalidad,'logs')
        return false;
    }else if(agencia.length === 0){
        console.log(' agencia: '+ agencia,'logs')
        return false;
    }else if(provincia.length === 0){
        console.log(' provincia: '+ provincia,'logs')
        return false;
    }else if(municipio.length === 0){
        console.log(' municipio '+ municipio +' ','logs')
        return false;
    }else if( this.envioTemporal.requiere_pareo && pareo.length === 0 ){
        console.log('pareo','logs')
        return false;
    }else {
        console.log( ' else ','logs')
        return true;
    }






    /*if( no_guia.length === 0
        || cod_tracking.length === 0
        || peso.length === 0
        || nacionalidad.length === 0
        || agencia.length === 0
        || provincia.length === 0
        || municipio.length === 0
        || (this.envioTemporal.requiere_pareo && pareo.length === 0 ) ){

        console.log('no_guia: '+no_guia +' cod_tracking: '+ cod_tracking +' peso: '+ peso +' nacionalidad: '+ nacionalidad +' agencia: '+ agencia +' provincia: '+ provincia +' '+ municipio +' ','logs')

        return false;
    }else {

        return true;
    }*/
}

/**
 * Recepcionar envio(s)
 */
function recepcionarEnvios()
{
    if ( this.listEnviosTemporles.length > 0) {

        var listEnvios = this.listEnviosTemporles.map( (envio) => {
            delete envio.extra
            return envio
        } )

        console.log(listEnvios,'listEnvios');

        var ruta = Routing.generate('recepcionar_envios')
        $.ajax({
            type: 'POST',
            url: ruta,
            data: {
                envios: listEnvios
            },
            async: true,
            dataType: 'json',
            loading: '',
            success: function (data) {
                console.log('success', data)
                if (!(data.estado)) {
                    //alert(data.mensaje);
                    swal({
                        title: "Error",
                        text: data.mensaje,
                        type: "error"
                    });

                } else {
                    console.log("Envios receppcionados con exito.");
                    swal({
                        title: "Informacion",
                        text: 'Envios recepcionados con exito.',
                        type: "info"
                    });

                    limpiarTodo();
                }

            },
            error: function (error) {
                console.log('error', error.responseText)
                swal({
                    title: "Error",
                    text: 'Error: ' + error.status + ' ' + error.statusText,
                    type: "error"
                });
            }
        })
    }else {
        swal({
            title: "Error",
            text: 'Debe añadir al menos un envio.',
            type: "error"
        });
    }

}

/**
 * Mostrar u ocultar las irregluraidades
 * */
function mostarUOcultarIrregularidades()
{
    let visble = document.getElementById('div_irregularidades')

    if (visble.style.display == "none"){
        visble.style.display = "";
        document.getElementById("i_irregularidades").className  = "fa fa-chevron-down";
    }else{
        visble.style.display = "none";
        document.getElementById("i_irregularidades").className  = "fa fa-chevron-right";
    }

}

/**
 * Limpiar el campo descripcion de la irregularidad correspondiente en caso de ser desmarcado
 */
function limpiarDescripcionIrregularidad(idAnomalia)
{
   let inputDescripcionAnomalia = document.getElementById('input_'+idAnomalia)

    let checkAnomalia = $('#check_'+idAnomalia).is(':checked')

    if (!checkAnomalia){
        inputDescripcionAnomalia.value = ""
    }
}

/**
 * Limpiar todos los valores de los campos (Menos el campo de la guia)
 */
function limpiarCampos(){

    //$('#input_noGuia').val("");

    $('#input_codTracking').val("");

    $('#input_peso').val("");

    $('#select_nacionalidadOrigen')
        .val("")
        .trigger('change.select2');

    $('#select_producto')
        .val("")
        .trigger('change.select2');

    $('#check_entidadControlAduana').prop('checked',false)

    $('#select_provincias')
        .val("")
        .trigger('change.select2');

    $('#select_municipios')
        .val("")
        .trigger('change.select2');

    $('#input_pareo').val("");

    limpiarIrregularidades();

    limpiarVariableEnvioTemporal();
}

/**
 * Limpiar todos los valores de los campos de las irregularidades y/o anomalias
 */
function limpiarIrregularidades(){

    var divIrregularidades = document.getElementById('div_irregularidades');
    var inputs = divIrregularidades.getElementsByTagName('input');

    for (var i = 0; i < inputs.length; i+=2 ) {
        id = inputs[i].getAttribute("id");
        $('#'+id).prop('checked',false)

        let idInputTextAnomalia = id.toString().substring(6,id.toString().length);
        $('#input_'+idInputTextAnomalia).val("")

    }

}

/**
 * Limpiar la variable de envio temporal
 */
function limpiarVariableEnvioTemporal(){

    this.envioTemporal = {
        id: '',
        no_guia: '',
        cod_tracking: '',
        peso: 0.0,
        //nacionalidad_remitente
        pais_origen: '',
        //currier
        agencia: '',
        //interes_aduana
        entidad_ctrl_aduana: false,
        provincia: '',
        municipio: '',
        requiere_pareo: false,
        pareo: '',
        //anomalias
        irregularidades: [],
        destinatario: null,
        remitente: null,
        //direcciones: []
        modo_recepcion: ModoRecepcion.MANIFESTADO
    }

}

/**
 * Limpiar la variable de lista de envio temporal
 */
function limpiarVariableListEnvioTemporales(){

    this.listEnviosTemporles = []
}

/**
 * Limpiar todos los items de la tabla
 */
function limpiarTodosLosItemsDeLaTabla(){
    $("#resultado").html('');
}

/**
 * Limpiar todos los datos
 */
function limpiarTodo(){

    //Incluye los metodos  [ limpiarIrregularidades y limpiarVariableEnvioTemporal ]
    limpiarCampos();

    limpiarVariableListEnvioTemporales();
    limpiarIrregularidades();
    limpiarTodosLosItemsDeLaTabla();

}

/**
 * Asignar los valores de la variable (envioTemporal) obtenido del envio manifestado a los input correspondientes
 */
function asignarValoresDeEnvioManifestado(){

    $('#input_noGuia').val(envioTemporal.no_guia);

    $('#input_codTracking').val(envioTemporal.cod_tracking);

    $('#input_peso').val(envioTemporal.peso);

    $('#select_nacionalidadOrigen')
        .val(envioTemporal.pais_origen)
        .trigger('change.select2');

    $('#select_producto')
        .val(envioTemporal.agencia)
        .trigger('change.select2');

    if (envioTemporal.provincia) {
        $('#select_provincias')
            .val(envioTemporal.provincia)
            .trigger('change.select2');
    }

    /*if (envioTemporal.municipio){
        $('#select_municipios')
            .val(envioTemporal.municipio)
            .trigger('change.select2');
    }*/

    $('#input_pareo').val(envioTemporal.pareo);

}

/**
 * Buscar en la variable (listEnviosTemporles) si esta un envio dado su cod_tracking
 * @param cod
 * @return {boolean}
 */
function buscarEnvioPorCodTracking(cod){

    const resultado = this.listEnviosTemporles.find( envio => envio.cod_tracking === cod );

    return !!resultado ;
}

/**
 * Retorna el array de irreularidades en caso de que existan si no retorna una array vacio
 * @return array<Irregularidades>
 */
function obtenerIrregularidades(){

    var divIrregularidades = document.getElementById('div_irregularidades');
    var inputs = divIrregularidades.getElementsByTagName('input');

    var irregularidades = []

    for (var i = 0; i < inputs.length; i+=2 ) {
        id = inputs[i].getAttribute("id");
        /*typeElement = inputs[i].getAttribute("type");
        if ( typeElement == "text") {
            console.log($('#'+id).val(),'text')
        }else {
            console.log($('#'+id).is(':checked'),'checked')
        }*/

        if($('#'+id).is(':checked')){
            let idInputTextAnomalia = id.toString().substring(6,id.toString().length);
            var textoDescripcion = $('#input_'+idInputTextAnomalia).val()
            irregularidades.push({
                id: idInputTextAnomalia,
                nombre: $('#'+id).val(),
                descripcion: textoDescripcion ? textoDescripcion : ''
            })
        }

    }

    return irregularidades;
}

/**
 * Buscar municipios de una provincia
 */
function buscarMunDeUnaProv()
{
    var idProv = $('#select_provincias').val()
    var selectMunicipio = $('#select_municipios')
    if (idProv) {
        var ruta = Routing.generate('mun_prov_seleccionada')
        $.ajax({
            type: 'POST',
            url: ruta,
            data: {
                idProvincia: idProv
            },
            async: true,
            dataType: 'json',
            loading: '',
            success: function (data) {
                console.log('success', data)
                if (!(data.estado)) {
                    //alert(data.mensaje);
                    swal({
                        title: "Error",
                        text: data.mensaje,
                        type: "error"
                    });
                    console.log(data.mensaje);
                } else {
                    //alert("Recibido OK");

                    var options = '<option value="">Seleccione</option>';

                    var municipios = []
                    municipios = data.data;

                    for (var i=0; i<municipios.length; i++){
                        options+='<option value="'+municipios[i].id+'">'+municipios[i].nombre+'</option>'
                        console.log('ajax prov-mun')
                    }

                    selectMunicipio.html('');
                    selectMunicipio.html(options);

                    if (envioTemporal.municipio){
                        $('#select_municipios')
                            .val(envioTemporal.municipio)
                            .trigger('change.select2');
                    }
                }

            },
            error: function (error) {
                alert('Error: ' + error.status + ' ' + error.statusText);
                console.log('error', error.responseText)
            }
        })
    }


}

/**
 * Obtener modo de recepcion
 @return {string}
 */
function modoRecepcion(){

    if ( $('#check_envioSinManifestar').is(':checked') ){
        return ModoRecepcion.SINMANIFESTAR;
    }else {
        return ModoRecepcion.MANIFESTADO;
    }
}

