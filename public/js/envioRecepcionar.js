//Variables
var envioTemporal = {
    no_guia: "19357",
    cod_tracking: "123",
    peso: 1.5,
    //nacionalidad_remitente
    pais_origen: "",
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

}

/**
 * Añadir envios crakeados al listado temporal para posteriormente recepcionarlos
 */
function annadirEnvioAListTemporal()
{
    no_guia = elementId('input_noGuia').value
    cod_tracking = elementId('input_codTracking').value
    peso = elementId('input_peso').value
    nacionalidad = elementId('select_nacionalidadOrigen').value
    producto = elementId('select_producto').value
    entidadCtrlAduana = elementId('check_entidadControlAduana').checked
    provincia = elementId('select_provincias').value
    municipio = elementId('select_municipios').value

    pareo = "";

    if ( elementId('input_pareo') != null ){
        pareo = elementId('input_pareo').value == "" ? "" : elementId('input_pareo').value
    }

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

    //Seleccionar un pais
    $('#select_nacionalidadOrigen')
        .val('a5cc4eff-26b0-11ec-a331-0242ac120002')
        .trigger('change.select2')

    this.listEnviosTemporles.push(12);
    console.log(this.listEnviosTemporles);

    noGuia = document.getElementById('input_noGuia').value
    codTracking = document.getElementById('input_codTracking').value
    console.log(codTracking,'length')

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


