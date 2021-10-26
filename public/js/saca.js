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
    let inputs = document.getElementsByClassName("checkbox-sacas");
    let envios = [];
    for (let check of inputs) {
        if (check.checked)
            envios.push(check.value)
    }

    if (envios.length === 0){
        swal({
            title: "Ningun seleccionado",
            text: 'Debe seleccionar al menos una saca',
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

function guardarAnomaliaEnvio(envioID){

    var table = document.getElementById("anomaliaslist"+envioID);
    var cells = table.getElementsByClassName("check-anomalia");

    let key = [];
    let value = [];

    for (let check of cells) {
        if(check.checked){
            let className = check.className.split(' ');
            let a = table.getElementsByClassName(className[2]);

            key.push(a[0].value);
            value.push(a[2].value);

            /*if (a[1].innerText === 'DIFERENCIA DE PESO' && a[2].value==='')
            {
                swal({
                    title: "Diferencia de peso",
                    text: 'Debe escribir la diferencia de peso',
                    type: "warning"
                });
                return;
            }*/
        }
    }

    let i = 0;
    let anomalias = {};

    while (i < key.length) {
        anomalias[key[i]] = value[i]
        i++;
    }

    let ruta = Routing.generate('envio_anomalia');
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
            swal({
                title: "OK",
                text: data,
                type: "success"
            });
        },
        error: function (error) {
            alert('Error: ' + error.status + ' ' + error.statusText);
            console.log('error', error.responseText)
        }

    })

}