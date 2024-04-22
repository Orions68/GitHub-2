function screenSize() // Función para dar el tamaño máximo de la pantalla a las vistas.
{
    let view3 = document.getElementById("view3");
    let height = window.innerHeight; // window.innerHeight es el tamaño vertical de la pantalla.

    if (view1.offsetHeight < height) // Si el tamaño vertical de la vista es menor que el tamaño vertical de la pantalla.
    {
        view1.style.height = height + "px"; // Asigna a la vista el tamaño vertical de la pantalla.
    }

    if (view2 != null) // Si existe el div view2
    {
        if (view2.offsetHeight < height)
        {
            view2.style.height = height + "px";
        }
        if (view3 != null)
        {
            if (view3.offsetHeight < height)
            {
                view3.style.height = height + "px";
            }
            if (view4 != null)
            {
                if (view4.offsetHeight < height)
                {
                    view4.style.height = height + "px";
                }
            }
            
        }
    }
}

function toast(warn, ttl, msg) // Función para mostrar el Diálogo con los mensajes de alerta, recibe, Código, Título y Mensaje.
{
    if (warn == 1) // Si el código es 1, es una alerta.
    {
        title.style.backgroundColor = "#000000"; // Pongo los atributos, color de fondo negro.
        title.style.color = "yellow"; // Y color del texto amarillo.
    }
    else if (warn == 0) // Si no, si el código es 0 es un mensaje satisfactorio.
    {
        title.style.backgroundColor = "#FFFFFF"; // Pongo los atributos, color de fondo blanco.
        title.style.color = "blue"; // Y el color del texto azul.
    }
    else // Si no, viene un 2, es una alerta de error.
    {
        title.style.backgroundColor = "#000000"; // Pongo los atributos, color de fondo negro.
        title.style.color = "red"; // Y color del texto rojo.
    }
    title.innerHTML = ttl; // Muestro el Título del dialogo.
    message.innerHTML = msg; // Muestro los mensajes en el diálogo.
    alerta.click(); // Lo hago aparecer pulsando el botón con ID alerta.
}

function wait() // Se muestra una alerta para indicar que verificar la IP demora unos 10 segundos.
{
    toast(1, "Paciencia", "Verificar la IP demora unos segundos.\nHaz Click en Cerrar y Se Cargará una Nueva Página Después de Aproximadamente 10 Segundos.");
}