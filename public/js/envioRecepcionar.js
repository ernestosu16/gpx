//Variables
var envioTemporal = {
    noGuia: "19357",
    codTracking: "123",
    peso: 1.5,
    //nacionalidad_remitente
    paisOrigen: "",
    //currier
    agencia: '',
    //interes_aduana
    entidadCtrlAduana: false,
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
    noGuia = elementId('input_noGuia').value
    codTracking = elementId('input_codTracking').value
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

    campos =    "guia: "+ noGuia+ "\n"+
                "track: "+ codTracking+ "\n"+
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

    this.listEnviosTemporles.push(12);
    console.log(this.listEnviosTemporles);
    var ruta = Routing.generate('envio_manifestado')
    $.ajax({
        type: 'POST',
        url: ruta,
        data: {
            noGuia: '136-62555942',
            codTracking: 'CP002767682SA'
        },
        async: true,
        dataType: 'json',
        loading: '',
        success: function (data) {
           console.log('success',data)
            if(!(data.estado)){
                alert('No hay envio con esa guia y ese código');
            }else {
                alert(data.noGuia+' '+data.codTracking);
            }

        },
        error: function (error){
            alert('Error: '+ error.status+ ' '+ error.statusText);
            console.log('error',error.responseText)
        }
    })
}
