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
        dataType: 'json',
        loading: '',
        success: function (data) {
            console.log('success', data)
        },
        error: function (error) {
            alert('Error: ' + error.status + ' ' + error.statusText);
            console.log('error', error.responseText)
        }

        })
}