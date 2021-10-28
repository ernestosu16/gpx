
let listEnvios = [];

//-----------------Funciones de Saca---------------------------------------------------------------------------

function AgregarEnvios() {
    var ruta = Routing.generate('Annadir')
    var res = $("#input_codTracking").val();
    const oficina = $("#select_oficinas").val();

    const resultado = listEnvios.find( envio => envio.cod === res );

    if (resultado){
        toastr.warning('El envío ya se encuentra en la tabla');
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
                    toastr.warning(respuesta.mensaje);
                }else{
                    listEnvios.push(respuesta)
                    ActualizarList();
                    $("#mensaje_tabla").html('')
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
    const oficina = $("#select_oficinas").val();
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
                toastr.success('Se ha creado la saca correctamente');
                document.getElementById('enlace').setAttribute('href', 'imprimir/' + data);
            }
        });
    }else {
        toastr.warning('Por favor revise que los datos esten correctos');
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
    }else
    {
        $('#mensaje_sello').html('');
    }
    if($("#input_peso").val() == ""){
        var v = '';
        v = '<p style="color: #d62c1a">* Campo vacío</p>'
        $('#mensaje_peso').html(v);
        $("#input_peso").focus();
        return false;
    }else
    {
        $('#mensaje_peso').html('');
    }

    return true; // Si todo está correcto
}

// Toastr options
toastr.options = {
    "debug": false,
    "newestOnTop": false,
    "positionClass": "toast-top-center",
    "closeButton": true,
    "toastClass": "animated fadeInDown",
};

function elementId(id){
    return document.getElementById(id.toString());
}

function buscarEnviosSacas()
{
    $('#envios').html('')
    let codTracking = elementId('text_cod_tracking').value;

    if (codTracking !== '') {
        var l = Ladda.create(elementId('button_cod_tracking'));
        l.start();
        let ruta = Routing.generate('find_envios_saca')
        $.ajax({
            type: 'POST',
            url: ruta,
            data: {
                codTracking: codTracking
            },
            async: true,
            dataType: 'html',
            loading: '',
            success: function (data) {
                l.stop();
                if (data === 'null'){
                    swal({
                        title: "No se encuentra",
                        text: 'No existe saca con ese codigo o ya ha sido entregada',
                        type: "warning"
                    });

                }else {
                    $('#envios').html(data)
                }
            },
            error: function (error) {
                alert('Error: ' + error.status + ' ' + error.statusText);
                console.log('error', error.responseText)
                l.stop();
            }

        })
    }
    else {
        swal({
            title: "Campo vacio",
            text: 'Debe escribir el codigo de la saca',
            type: "warning"
        });
    }


}

function aperturarSaca(codTracking)
{
    let inputs = document.getElementsByClassName("checkbox-envios");
    let envios = [];
    for (let check of inputs) {
        if (check.checked)
            envios.push(check.value)
    }

    if (envios.length === 0){
        swal({
            title: "Ningun seleccionado",
            text: 'Debe seleccionar al menos un envio',
            type: "warning"
        });
    }
    else{
        let todos = envios.length === inputs.length;
        let ruta = Routing.generate('recepcionar_envios_saca');
        var l = Ladda.create(elementId('button_aperturar_saca'));
        l.start();
        $.ajax({
            type: 'POST',
            url: ruta,
            data: {
                codTracking: codTracking,
                envios: envios,
                todos: todos
            },
            async: true,
            dataType: 'html',
            loading: '',
            success: function (data) {
                l.stop();
                swal({
                    title: "OK",
                    text: data,
                    type: "success"
                });
                $('#envios').html('')
            },
            error: function (error) {
                alert('Error: ' + error.status + ' ' + error.statusText);
                console.log('error', error.responseText)
                l.stop();
            }

        })
    }

}

