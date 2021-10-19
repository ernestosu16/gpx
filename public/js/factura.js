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

function recepcionarFactura(noFactura)
{
    let inputs = document.getElementsByClassName("checkbox-sacas");
    let sacas = [];
    for (let check of inputs) {
        if (check.checked)
            sacas.push(check.value)
    }

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
            console.log(1);
        },
        error: function (error) {
            alert('Error: ' + error.status + ' ' + error.statusText);
            console.log('error', error.responseText)
        }

    })

}

function guardarAnomalia(sacaID){

    var table = document.getElementById("anomaliaslist");
    var cells = table.getElementsByTagName("input");

    let key = [];
    let value = [];

    for (let check of cells) {
        if(check.value){
            let className = check.className.split(' ');
            let a = table.getElementsByClassName(className[1]);
            key.push(a[0].innerText);
            value.push(a[1].value);
        }
    }
    let i = 0;
    let anomalias = {};

    while ( i < key.length)
    {
        anomalias[key[i]]=value[i]
        i++;
    }
    console.log(anomalias);

    let ruta = Routing.generate('saca_anomalia');
    $.ajax({
        type: 'POST',
        url: ruta,
        data: {
            id: sacaID,
            saca: ['qwe','rty'],
            anomalias: anomalias
        },
        async: true,
        dataType: 'json',
        loading: '',
        success: function (data) {
            console.log(1);
        },
        error: function (error) {
            alert('Error: ' + error.status + ' ' + error.statusText);
            console.log('error', error.responseText)
        }

    })


}
