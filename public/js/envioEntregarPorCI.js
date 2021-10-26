/**
 * Buscar envios de una persona por CI
 * (Envios de una persona, con
 * estrucutra de destino la del user autenticado &&
 * en estado recepcionado &&
 * envio_aduana.datos_despacho != null)
 */
function buscarEnvioPreRecepcion()
{
    var noCI = $('#input_no_identidad').val()

    var ruta = Routing.generate('buscar_envioe_para_entrega_por_CI')
    $.ajax({
        type: 'POST',
        url: ruta,
        data: {
            noCI: noCI,
        },
        async: true,
        dataType: 'json',
        loading: '',
        success: function (data) {
            console.log('success', data)


           swal({
                title: "Informacion",
                text: data.mensaje,
                type: "info"
            });

        },
        error: function (error) {
            alert('Error: ' + error.status + ' ' + error.statusText);
            console.log('error', error.responseText)
            limpiarCampos();
        }
    })
}