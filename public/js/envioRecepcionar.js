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
    pareo: '',
    //anomalias
    irregularidades: [],
    destinatario: null,
    remitente: null,
    direcciones: []

};
var listEnviosTemporles = new Array();

function elementId(id){
    return document.getElementById(id.toString());
}

/**
 * Buscar un envio en la tabla manisfestados
 */
function buscarEnvioManifestado()
{
    var noGuia = $('#input_noGuia').val()
    var codTracking = $('#input_codTracking').val()

    if ( noGuia.length == 12 && codTracking.length == 13) {
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
                    alert(data.mensaje);
                } else {
                    alert("Recibido OK");
                    envioTemporal = data.data
                    asignarValoresDeEnvioManifestado();
                }

            },
            error: function (error) {
                alert('Error: ' + error.status + ' ' + error.statusText);
                console.log('error', error.responseText)
            }
        })
    }else {
        alert('Deben estar correcto el No. Guia y el Codigo de Tracking.')
    }

}

/**
 * Añadir envios crakeados al listado temporal para posteriormente recepcionarlos
 */
function annadirEnvioAListTemporal()
{
    no_guia = $('#input_noGuia').val()
    cod_tracking = $('#input_codTracking').val()
    peso = $('#input_peso').val()
    nacionalidad = $('#select_nacionalidadOrigen').val()
    producto = $('#select_producto').val()
    entidadCtrlAduana = $('#check_entidadControlAduana').is(':checked')
    provincia = $('#select_provincias').val()
    municipio = $('#select_municipios').val()
    pareo = $('#input_pareo').val()

    this.listEnviosTemporles.push(this.envioTemporal)

    console.log(this.listEnviosTemporles,'ListTempo')

    campos =    "guia: "+ no_guia+ "\n"+
                "track: "+ cod_tracking+ "\n"+
                "peso: "+ peso+ "\n"+
                "nac: "+ nacionalidad+ "\n"+
                "pro: "+ producto+ "\n"+
                "ent aduana: "+ entidadCtrlAduana+ "\n"+
                "prov: "+ provincia+ "\n"+
                "mun: "+ municipio+ "\n"+
                "pareo: "+ pareo+ "\n";

    alert(campos);



}

/**
 * Recepcionar envio(s)
 */
function recepcionarEnvios()
{
    console.log(this.listEnviosTemporles,'ñññ')

    if ( this.listEnviosTemporles.length > 0) {
        var ruta = Routing.generate('recepcionar_envios')
        $.ajax({
            type: 'POST',
            url: ruta,
            data: {
                envios: this.listEnviosTemporles
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
                alert('Error: ' + error.status + ' ' + error.statusText);
                console.log('error', error.responseText)
            }
        })
    }else {
        alert('Deben estar correcto el No. Guia y el Codigo de Tracking.')
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

function actualizarDatos(){



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


