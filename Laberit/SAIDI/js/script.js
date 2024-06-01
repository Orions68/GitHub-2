function totNumPages() // Función para la paginación
{
    return Math.ceil(window.array_length / window.qtty); // Calcula la cantidad de páginas que habrá, divide la cantidad de datos por 8 resultados a mostrar por página.
}

function prev() // Función para ir a la página anterior.
{
    if (window.page > 1) // Si la página actual es mayor que la página 1.
    {
        window.page--; // Decrementa la variable page, página anterior.
        change(window.page, window.qtty); // Llama a la función change pasandole el número de página a mostrar y la cantidad de datos a mostrar que siempre es 8.
    }
}

function next() // La Función next muestra la página siguiente.
{   
    if (window.page < totNumPages()) // Si la página en la que estoy es menor que la última.
    {
        window.page++; // Incremento la página
        change(window.page, window.qtty); // Llamo a la función que muestra los resultados.
    }
}

function change(page, qtty) // Función que muestra los resultados de a 8 en la tabla, recibe la página page, la cantidad de resultados a mostrar qtty.
{
    if (typeof array_value != "undefined" && array_value.length > 0)
    {
        window.page = page; // Asigno la variable page, a la variable global window.page.
        window.qtty = qtty; // Asigno la variable qtty, a la variable global window.qtty.
        window.array_length = array_value.length;
        const data_length = array_value[0].length;
        window.vlength = 18; // array_length; // Hago global la variable length.
        window.hlength = data_length;

        var html = "<table><tr class='text-center'><th>MAC</th><th>Marca</th><th>OUI</th><th>Fecha</th><th>ARP</th><th>ARP46</th><th>IP de otro Segmento</th><th>Broadcast</th><th>ICMP</th><th>ICPM6</th><th>IPV6</th><th>Multicast</th><th>Nº de Paquetes</th><th>Resto</th><th>Trafico</th><th>TCP</th><th>SSDP</th><th>UDP</th><th>Unicast</th></tr>";
        for (i = 0 + qtty * (page - 1); i < array_length && i < qtty * page; i++)
        {
            html += "<tr>";
            for (j = 0; j < data_length; j++)
            {
                if (array_value[i][j] == null)
                {
                    array_value[i][j] = "";
                }
                html += "<td>" + array_value[i][j] + "</td>";
            }
            html += "</tr>";
        }
        html += "</tr></table>";
        table.innerHTML = html; // Muestro todo en pantalla.

        if (array_length > 8) // Si la cantidad de Artículos es mayor que 5.
        {
            pages.innerHTML = "Página: " + page; // Muestro el número de página.
            if (page == 1) // Si la página es la número 1
            {
                prev_btn.style.visibility = "hidden"; // Escondo el Botón con id prev que mostraría los resultados anteriores.
            }
            else // Si no, estoy en otra página.
            {
                prev_btn.style.visibility = "visible"; // Hago visible el botón para mostrar los resultados anteriores.
            }
            if (page == totNumPages()) // Si estoy en la última página.
            {
                next_btn.style.visibility = "hidden"; // Escondo el botón para mostrar los resultados siguientes.
            }
            else // Si no, estoy en una página intermedia o en la primera.
            {
                next_btn.style.visibility = "visible"; // Hago visible el botón para mostrar los resultados siguientes.
            }
        }
    }
}

function makeTable(data)
{
    window.array_value = data;
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

function screenSize() // Función para dar el tamaño máximo de la pantalla a las vistas.
{
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