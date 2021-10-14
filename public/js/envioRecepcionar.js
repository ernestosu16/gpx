//Variables
var envioTemporal = {
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
    direcciones: []

};
var listEnviosTemporles = new Array();




/**
 * Buscar un envio en la tabla manisfestados
 */
function buscarEnvioManifestado()
{
    var noGuia = $('#input_noGuia').val()
    var codTracking = $('#input_codTracking').val()

    var ruta = Routing.generate('envio_manifestado')
    $.ajax({
        type: 'POST',
        url: ruta,
        data: {
            noGuia: noGuia,
            codTracking: codTracking
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
 * Añadir envios crakeados al listado temporal para posteriormente recepcionarlos
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

    //Si hay algun campo vacio o necesita pareo y no ha sido pareado
    if( !( this.validarCamposVacios(no_guia,cod_tracking,peso,nacionalidad,agencia,entidadCtrlAduana,provincia,municipio,pareo)) ){

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

        listEnviosTemporles.push(this.envioTemporal)

        this.actualizarTablaVisual()
    }
}

function actualizarTablaVisual() {
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
            '<td> <button class="btn btn-danger" onclick="eliminarEnvio(' + i + ')"><i class="fa fa-trash"></i></button> </td>' +
            '</tr>';
        $("#resultado").html(valor)
    }

}

/**
 * Eliminar envios de la lista temporal y mandar ha actualizar la tabla visual
 */
function eliminarEnvio(postionArray){

    if (this.listEnviosTemporles.length == 1){
        this.listEnviosTemporles.shift();
        $("#resultado").html('')
    }else {
        this.listEnviosTemporles.splice(postionArray,1)
        this.actualizarTablaVisual()
    }
}

/**
* Validar Campos vacios
*/
function validarCamposVacios(no_guia,cod_tracking,peso,nacionalidad,agencia,entidadCtrlAduana,provincia,municipio,pareo)
{
    //Si hay algun campo vacio o necesita pareo y no ha sido pareado
    if( no_guia == ""
        || cod_tracking == ""
        || peso == ""
        || nacionalidad == ""
        || agencia == ""
        || provincia == ""
        || municipio == ""
        || provincia == ""
        || municipio == ""
        || (this.envioTemporal.requiere_pareo && pareo == "" ) ){

        return false;
    }

    return true;

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
                    alert(data.mensaje);

                } else {
                    alert("Recibido OK");
                    envioTemporal = data.data
                    asignarValoresDeEnvioManifestado();
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
 * Mostrar u ocultar la descripcion de una irregularidad seleccionada
 */
function habilitarDescripcionAnomalia(id)
{
    let arr = id.toString().substring(5,id.toString().length);

    let inputDescripcion = document.getElementById('input'+arr)

    if (inputDescripcion.style.display == "none"){
        inputDescripcion.style.display = "";
    }else{
        inputDescripcion.style.display = "none"
        inputDescripcion.value = ""
    }


}

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

    $('#select_provincias')
        .val("")
        .trigger('change.select2');

    $('#select_municipios')
        .val("")
        .trigger('change.select2');

    $('#input_pareo').val("");

}


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

    if (envioTemporal.municipio){
        $('#select_municipios')
            .val(envioTemporal.municipio)
            .trigger('change.select2');
    }

    $('#input_pareo').val(envioTemporal.pareo);

}

function buscarEnvioPorCodTracking(cod){

    const resultado = this.listEnviosTemporles.find( envio => envio.cod_tracking === cod );

    return !!resultado ;

}


