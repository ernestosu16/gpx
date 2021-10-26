function elementId(id){
    return document.getElementById(id.toString());
}

function buscarEnviosSacas()
{
    let codTracking = elementId('text_cod_tracking').value;

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
        }

    })


}

function recepcionarSaca(codTracking)
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
            }

        })
    }

}

