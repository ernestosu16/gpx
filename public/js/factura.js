function elementId(id){
    return document.getElementById(id.toString());
}

function buscarFacturaSacas()
{
    let noFactura = elementId('text_no_factura').value;

    if (noFactura !== '') {
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
            },
            error: function (error) {
                alert('Error: ' + error.status + ' ' + error.statusText);
                console.log('error', error.responseText)
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

}

function guardarAnomaliaEnvio(envioID){

    var table = document.getElementById("envioanomaliaslist"+envioID);
    var cells = table.getElementsByClassName("check-anomalia");

    let key = [];
    let value = [];
    console.log(table);
    console.log(cells)
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
        console.log(anomalias)
        let ruta = Routing.generate('envio_anomalia');
        console.log(ruta)
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
}
