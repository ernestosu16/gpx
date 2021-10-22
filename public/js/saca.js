
let listEnvios = [];

//-----------------Funciones de Saca---------------------------------------------------------------------------

function AgregarEnvios() {
    $("#mensaje").html('')
    var ruta = Routing.generate('Annadir')
    var res = $("#input_codTracking").val();
    const oficina = $("#select_oficinas").val();

    const resultado = listEnvios.find( envio => envio.cod === res );

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
            '-webkit-border-radius: 20px 20px 20px 20px;">'+'<p>'+ 'El envío ya se encuentra en la tabla' +'</p>'+'</div>'
        $("#mensaje").html(valor);
        $("#input_codTracking").val('');
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
                    $("#mensaje").html(valor)
                }else{
                    listEnvios.push(respuesta)
                    ActualizarList();
                    $("#mensaje").html('')
                }

            },
            complete: function (){
                $("#input_codTracking").val('');
            }
        });
    }
}

function ActualizarList() {
    var valor = '';
    for (var i = 0; i < listEnvios.length; i++) {
        valor += '<tr>' +
            '<td>' + listEnvios[i].cod + '</td>' +
            '<td>' + listEnvios[i].peso + '</td>' +
            '<td> <button class="btn btn-danger" onclick="eliminarEnvio(' + i + ')"><i class="fa fa-trash"></i></button> </td>' +
            '</tr>';
        //$("#resultado").html(data['cod']);
        $("#resultado").html(valor)
    }
}

/**
 * Eliminar envios de la lista temporal y mandar ha actualizar la tabla visual
 */
function eliminarEnvio(postionArray){

    if (listEnvios.length == 1){
        listEnvios.shift();
        $("#resultado").html('')
    }else {
        listEnvios.splice(postionArray,1)
        ActualizarList()
    }
}

function GuardarSaca() {
    var ruta = Routing.generate('Guardar');
    const oficina = $("#select_oficinas_saca").val();
    var sello = $("#input_sello").val();
    var peso = $("#input_peso").val();
    let l = [];
    for (let envio of listEnvios){
        l.push(envio.id);
    }

    var valido = validaForm();

    if (valido == true){
        $.ajax({
            type: 'POST',
            url: ruta,
            data: ({oficina: oficina, list: l, sello: sello, peso: peso }),
            async: true,
            dataType: "json",
            success: function (data) {
                limpiarFormulario()
                mi_funcion();
                swal({
                    title: "",
                    text: "Se ha creado la saca correctamente",
                    type: "success"
                });
                document.getElementById('enlace').setAttribute('href', 'imprimir/' + data);
            }
        });
    }else {
        alert('Revise los campos')
    }
}

$(document).ready(function(){
    $("#input_codTracking").keypress(function(e) {
        //no recuerdo la fuente pero lo recomiendan para
        //mayor compatibilidad entre navegadores.
        var code = (e.keyCode ? e.keyCode : e.which);
        if(code==13){
            AgregarEnvios();
        }
    });
});

function limpiarFormulario() {
    //$("#select_oficinas_saca")[0].selectedIndex = 0;
    $('#select_oficinas_saca')
        .val("Seleccione")
        .trigger('change.select2');
    document.getElementById('input_codTracking').value = "";
    document.getElementById('input_sello').value = "";
    document.getElementById('input_peso').value = "";
    listEnvios = [];
}

document.addEventListener("DOMContentLoaded", function(){
    limpiarFormulario();

});

function mi_funcion() {
    jQuery('#select_oficinas_saca').prop('disabled', true);
    jQuery('#input_codTracking').prop('disabled', true);
    jQuery('#tabla_envios').prop('disabled', true)
    jQuery('#button_anndir_envios').prop('disabled', true);
    jQuery('#input_sello').prop('disabled', true);
    jQuery('#input_peso').prop('disabled', true);
    jQuery('#button_crear_saca').prop('disabled', true);
}

function validaForm(){
    if($("#select_oficinas").val() == "0"){
        var v = '';
        v = '<p style="color: #d62c1a">* Campo vacío</p>'
        $('#mensaje_oficina').html(v);
        return false;
    }else
    {
        $('#mensaje_oficina').html('');
    }
    if(listEnvios.length == 0){
        var v = '';
        v = '<p style="color: #d62c1a">* Tabla vacío</p>'
        $('#mensaje_tabla').html(v);
        return false;
    }else
    {
        $('#mensaje_tabla').html('');
    }
    if($("#input_sello").val() == ""){
        var v = '';
        v = '<p style="color: #d62c1a">* Campo vacío</p>'
        $('#mensaje_sello').html(v);
        $("#input_sello").focus();
        return false;
    }
    if($("#input_peso").val() == ""){
        var v = '';
        v = '<p style="color: #d62c1a">* Campo vacío</p>'
        $('#mensaje_peso').html(v);
        $("#input_peso").focus();
        return false;
    }

    return true; // Si todo está correcto
}

