function elementId(id){
    return document.getElementById(id.toString());
}

function buscarFacturaSacas()
{
    $('#sacas').html('')
    let noFactura = elementId('text_no_factura').value;

    if (noFactura !== '') {
        var l = Ladda.create(elementId('button_no_factura'));
        l.start();
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
                l.stop();
                if (data === 'null'){
                    swal({
                        title: "No se encuentra",
                        text: 'No existe factura con ese numero o ya ha sido entregada',
                        type: "warning"
                    });

                }else {
                    $('#sacas').html(data)
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
            text: 'Debe escribir el numero de la factura',
            type: "warning"
        });
    }
}

function recepcionarFactura(noFactura)
{
    let inputsacas = document.getElementsByClassName("checkbox-sacas");
    let sacas = [];
    for (let check of inputsacas) {
        if (check.checked)
            sacas.push(check.value)
    }

    let inputenvios = document.getElementsByClassName("checkbox-envios");
    let envios = [];
    for (let check of inputenvios) {
        if (check.checked)
            envios.push(check.value)
    }

    if (sacas.length === 0 && envios.length === 0){
        swal({
            title: "Ningun seleccionado",
            text: 'Debe seleccionar al menos una saca o envio',
            type: "warning"
        });
    }
    else{
        let todos = sacas.length === inputsacas.length && envios.length === inputenvios.length;
        let ruta = Routing.generate('recepcionar_sacas_factura');
        var l = Ladda.create(elementId('button_recepcionar_factura'));
        l.start();
        $.ajax({
            type: 'POST',
            url: ruta,
            data: {
                noFactura: noFactura,
                sacas: sacas,
                envios: envios,
                todos: todos
            },
            async: true,
            dataType: 'html',
            loading: '',
            success: function (data) {
                swal({
                    title: "OK",
                    text: data,
                    type: "success"
                });
                $('#sacas').html('')
                l.stop();
            },
            error: function (error) {
                alert('Error: ' + error.status + ' ' + error.statusText);
                console.log('error', error.responseText)
                l.stop();
            }

        })
    }

}

function guardarAnomalia(sacaID){

    var table = document.getElementById("anomaliaslist"+sacaID);
    var cells = table.getElementsByClassName("check-anomalia");

    let key = [];
    let value = [];

    for (let check of cells) {
        if(check.checked){
            let className = check.className.split(' ');
            let a = table.getElementsByClassName(className[2]);

            key.push(a[1].innerText);
            value.push(a[2].value);

            if (a[1].innerText === 'DIFERENCIA DE PESO' && a[2].value==='')
            {
                swal({
                    title: "Diferencia de peso",
                    text: 'Debe escribir la diferencia de peso',
                    type: "warning"
                });
                return;
            }
        }
    }

    if (key.length === 0)
    {
        swal({
            title: "Ningun seleccionado",
            text: 'Debe seleccionar al menos una anomalia',
            type: "warning"
        });
    }else {

        let i = 0;
        let anomalias = {};

        while (i < key.length) {
            anomalias[key[i]] = value[i]
            i++;
        }

        var l = Ladda.create(elementId('btn-anomalia-saca-'+sacaID));
        l.start();
        let ruta = Routing.generate('saca_anomalia');
        $.ajax({
            type: 'POST',
            url: ruta,
            data: {
                id: sacaID,
                anomalias: anomalias
            },
            async: true,
            dataType: 'json',
            loading: '',
            success: function (data) {
                l.stop();
                swal({
                    title: "OK",
                    text: data,
                    type: "success"
                });
            },
            error: function (error) {
                alert('Error: ' + error.status + ' ' + error.statusText);
                console.log('error', error.responseText)
                l.stop();
            }

        })
    }

}

function guardarAnomaliaEnvio(envioID){

    var table = document.getElementById("envioanomaliaslist"+envioID);
    var cells = table.getElementsByClassName("check-anomalia");

    let key = [];
    let value = [];

    for (let check of cells) {
        if(check.checked){
            let className = check.className.split(' ');
            let a = table.getElementsByClassName(className[2]);

            key.push(a[0].value);
            value.push(a[2].value);
        }
    }

    if (key.length === 0)
    {
        swal({
            title: "Ningun seleccionado",
            text: 'Debe seleccionar al menos una anomalia',
            type: "warning"
        });
    }else {

        let i = 0;
        let anomalias = {};

        while (i < key.length) {
            anomalias[key[i]] = value[i]
            i++;
        }

        let ruta = Routing.generate('envio_anomalia');
        var l = Ladda.create(elementId('btn-anomalia-envio-'+envioID));
        l.start();
        $.ajax({
            type: 'POST',
            url: ruta,
            data: {
                id: envioID,
                anomalias: anomalias
            },
            async: true,
            dataType: 'json',
            loading: '',
            success: function (data) {
                l.stop();
                swal({
                    title: "OK",
                    text: data,
                    type: "success"
                });
            },
            error: function (error) {
                alert('Error: ' + error.status + ' ' + error.statusText);
                console.log('error', error.responseText)
                l.stop();
            }

        })
    }
}

function recepcionarFactura(noFactura)
{
    let inputs = document.getElementsByClassName("checkbox-sacas");
    let sacas = [];
    for (let check of inputs) {
        if (check.checked)
            sacas.push(check.value)
    }

    if (sacas.length === 0){
        swal({
            title: "Ningun seleccionado",
            text: 'Debe seleccionar al menos una saca',
            type: "warning"
        });
    }
    else{
        let todos = sacas.length === inputs.length;
        let ruta = Routing.generate('recepcionar_sacas_factura');
        $.ajax({
            type: 'POST',
            url: ruta,
            data: {
                noFactura: noFactura,
                sacas: sacas,
                todos: todos
            },
            async: true,
            dataType: 'html',
            loading: '',
            success: function (data) {
                swal({
                    title: "OK",
                    text: data,
                    type: "success"
                });
                $('#sacas').html('')
            },
            error: function (error) {
                alert('Error: ' + error.status + ' ' + error.statusText);
                console.log('error', error.responseText)
            }
        });
    }
}

function AgregarSaca() {
    var ruta = Routing.generate('AnnadirSaca')
    var res = $("#input_sello").val();
    const oficina = $("#select_oficinas_factura").val();

    const resultado = listSacas.find( saca => saca.cod === res );

    if (resultado){
        toastr.warning('El envío o la saca ya se encuentra en la tabla');
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
                    toastr.warning(respuesta.mensaje);
                }else{
                    listSacas.push(respuesta)
                    ActualizarListSaca();
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
                toastr.success('Se ha creado la factura correctamente');
                document.getElementById('generar_factura').setAttribute('href', 'imprimir/' + data);
            }
        });
    }else{
        toastr.warning('Por favor revise que los datos esten correctos');
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

// Toastr options
toastr.options = {
    "debug": false,
    "newestOnTop": false,
    "positionClass": "toast-top-center",
    "closeButton": true,
    "toastClass": "animated fadeInDown",
};

