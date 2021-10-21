//Variables

let listSacas = [];


//-------------Funciones de Factura------------------------------------------------------------------------------

function elementId(id){
    return document.getElementById(id.toString());
}

function buscarFacturaSacas()
{
    let noFactura = elementId('text_no_factura').value;

    let ruta = Routing.generate('find_sacas_factura')
    $.ajax({
        type: 'POST',
        url: ruta,
        data: {
            noFactura: noFactura
        },
        async: true,
        dataType: 'html',
        loading: '',
        success: function (data) {
            console.log('success', data)
            $('#prueba').html(data)
        },
        error: function (error) {
            alert('Error: ' + error.status + ' ' + error.statusText);
            console.log('error', error.responseText)
        }

        })


}

function recepcionarFactura() {

}

function AgregarSaca() {
    var ruta = Routing.generate('AnnadirSaca')
    var res = $("#input_sello").val();
    const oficina = $("#select_oficinas_factura").val();

    const resultado = listSacas.find( saca => saca.cod === res );

    if (resultado){
        var valor = '';
        valor = '<div style="font-family: Century Gothic,CenturyGothic,AppleGothic,sans-serif; \n' +
            'color: #ffffff; \n' +
            'font-size: 18px; \n' +
            'font-weight: 400; \n' +
            'text-align: center; \n' +
            'background: #889ccf; \n' +
            'margin: 0 0 25px; \n' +
            'overflow: hidden; \n' +
            'padding: 20px; \n' +
            'border-radius: 20px 20px 20px 20px; \n' +
            '-moz-border-radius: 20px 20px 20px 20px; \n' +
            '-webkit-border-radius: 20px 20px 20px 20px;">'+'<p>'+ respuesta.mensaje +'</p>'+'</div>'
        $("#mensaje_saca").html(valor)
        $("#input_sello").val('');
    }else{
        $.ajax({
            type: 'POST',
            url: ruta,
            data: ({codTracking: res, oficina_dest: oficina}),
            async: true,
            dataType: "json",
            success: function (respuesta) {
                if (respuesta.respuesta == true){
                    var valor = '';
                    valor = '<div style="font-family: Century Gothic,CenturyGothic,AppleGothic,sans-serif; \n' +
                        'color: #ffffff; \n' +
                        'font-size: 18px; \n' +
                        'font-weight: 400; \n' +
                        'text-align: center; \n' +
                        'background: #889ccf; \n' +
                        'margin: 0 0 25px; \n' +
                        'overflow: hidden; \n' +
                        'padding: 20px; \n' +
                        'border-radius: 20px 20px 20px 20px; \n' +
                        '-moz-border-radius: 20px 20px 20px 20px; \n' +
                        '-webkit-border-radius: 20px 20px 20px 20px;">'+'<p>'+ respuesta.mensaje +'</p>'+'</div>'
                    $("#mensaje_saca").html(valor)
                }else{
                    listSacas.push(respuesta)
                    ActualizarListSaca();
                    $("#mensaje_saca").html('')
                    $("#mensaje_tabla").html('')
                }
            },
            complete: function (){
                $("#input_sello").val('');
            }
        });
    }
}

function ActualizarListSaca() {
    var valor = '';
    var sello = '';
    for (var i = 0; i < listSacas.length; i++) {
        valor += '<tr>' +
            '<td>' + listSacas[i].cod + '</td>' +
            '<td>' + listSacas[i].peso + '</td>' +
            '<td> <button class="btn btn-danger" onclick="eliminarEnvio(' + i + ')"><i class="fa fa-trash"></i></button> </td>' +
            '</tr>';
        //$("#resultado").html(data['cod']);
        $("#resultado").html(valor)
    }
}

/**
 * Eliminar envios y sacas de la lista y mandar ha actualizar la tabla visual
 */
function eliminarEnvio(postionArray){

    if (listSacas.length == 1){
        listSacas.shift();
        $("#resultado").html('')
    }else {
        listSacas.splice(postionArray,1)
        ActualizarListSaca()
    }
}

function GuardarFactura() {
    var ruta = Routing.generate('GuardarFactura');
    const oficina = $("#select_oficinas_factura").val();
    const tipo_vehiculo = $("#select_tipo_vehiculo").val();
    var chapa = $("#input_chapa").val();
    const chofer = $("#select_choferes").val();
    let l = [];
    for (let saca of listSacas){
        l.push(saca.id);
    }

    var valido = validaForm();

    if (valido == true){
        $.ajax({
            type: 'POST',
            url: ruta,
            data: ({oficina: oficina, list: l, tipo_vehiculo: tipo_vehiculo, chapa: chapa, chofer: chofer }),
            async: true,
            dataType: "json",
            success: function (data) {
                limpiarFormulario()
                mi_funcion();
                swal({
                    title: "",
                    text: "Se ha creado la factura correctamente",
                    type: "success"
                });
                document.getElementById('generar_factura').setAttribute('href', 'imprimir/' + data);
            }
        });
    }


}

function validaForm(){
    if($("#select_oficinas_factura").val() == "0"){
        var v = '';
        v = '<p style="color: #d62c1a">* Campo vacío</p>'
        $('#mensaje_oficina').html(v);
        return false;
    }else
    {
        $('#mensaje_oficina').html('');
    }
    if(listSacas.length == 0){
        var v = '';
        v = '<p style="color: #d62c1a">* Tabla vacío</p>'
        $('#mensaje_tabla').html(v);
        return false;
    }else
    {
        $('#mensaje_tabla').html('');
    }
    if($("#select_tipo_vehiculo").val() == "0"){
        var v = '';
        v = '<p style="color: #d62c1a">* Campo vacío</p>'
        $('#mensaje_tipo_vehiculo').html(v);
        return false;
    }else
    {
        $('#mensaje_tipo_vehiculo').html('');
    }
    if($("#input_chapa").val() == ""){
        var v = '';
        v = '<p style="color: #d62c1a">* Campo vacío</p>'
        $('#mensaje_chapa').html(v);
        $("#input_chapa").focus();
        return false;
    }else
    {
        $('#mensaje_chapa').html('');
    }
    if($("#select_choferes").val() == "0"){
        var v = '';
        v = '<p style="color: #d62c1a">* Campo vacío</p>'
        $('#mensaje_choferes').html(v);
        return false;
    }else
    {
        $('#mensaje_choferes').html('');
    }

    return true; // Si todo está correcto
}

$(document).ready(function(){
    $("#input_sello").keypress(function(e) {
        var code = (e.keyCode ? e.keyCode : e.which);
        if(code==13){
            AgregarSaca();
        }
    });
});

function limpiarFormulario() {
    $("#select_oficinas_factura")[0].selectedIndex = 0;
    document.getElementById('input_sello').value = "";
    $("#select_tipo_vehiculo")[0].selectedIndex = 0;
    $("#select_choferes")[0].selectedIndex = 0;
    document.getElementById('input_chapa').value = "";
    listEnvios = [];
}

document.addEventListener("DOMContentLoaded", function(){
    limpiarFormulario()
});

function mi_funcion() {
    jQuery('#select_oficinas_factura').prop('disabled', true);
    jQuery('#input_sello').prop('disabled', true);
    jQuery('#tabla_sacas').prop('disabled', true)
    jQuery('#button_anndir_sacas').prop('disabled', true);
    jQuery('#select_tipo_vehiculo').prop('disabled', true);
    jQuery('#input_chapa').prop('disabled', true);
    jQuery('#select_choferes').prop('disabled', true);
    jQuery('#button_crear_factura').prop('disabled', true);
}
