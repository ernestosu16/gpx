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
            $('#sacas').html(data)
        },
        error: function (error) {
            alert('Error: ' + error.status + ' ' + error.statusText);
            console.log('error', error.responseText)
        }

        })


}

function recepcionarFactura() {

}

function guardarAnomalia(){

    var table = document.getElementById("anomaliaslist");
    var cells = table.getElementsByTagName("input");

    console.log(cells);
}