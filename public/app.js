// Progressive Web Apps (PWA)
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.min.js')
        .then(reg => console.log("Registro de SW exitoso", reg))
        .catch(err => console.log("Error al tratar de registrar el sw", err))
}