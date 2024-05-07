function totNumPages1() // Función para la paginación
{
    return Math.ceil(window.no / window.qtty_no); // Calcula la cantidad de páginas que habrá, divide la cantidad de datos por 5 resultados a mostrar por página.
}

function prev1() // Función para ir a la página anterior.
{
    if (window.page_no > 1) // Si la página actual es mayor que la página 1.
    {
        window.page_no--; // Decrementa la variable page, página anterior.
        change(window.page_no, window.qtty_no);
    }
}

function next1() // La Función next muestra la página siguiente.
{
    if (window.page_no < totNumPages()) // Si la página en la que estoy es menor que la última.
    {
        window.page_no++; // Incremento la página
        change(window.page_no, window.qtty_no);
    }
}

function change1(page1, qtty1) // Función que muestra los resultados de a 5 en la tabla, recibe la página page, la cantidad de resultados a mostrar qtty y true si viene de index y false si viene de profile.
{
    window.page_no = page1; // Asigno la variable page, a la variable global window.page.
    window.qtty_no = qtty1; // Asigno la variable qtty, a la variable global window.qtty.
    var no = no_email.length; // El Tamaño del Array de los Mensajes que NO Llegaron.
    window.no = no; // Hago global la variable no.
    var html1 = "<table><tr><th>Nada de :</th></tr>";

    for (i = (page1 - 1) * qtty1; i < page1 * qtty1; i++) // Aquí hago el bucle desde la página donde esté, a la cantidad de resultados a mostrar.
    {
        if (i < no) // Si i es menor que el tamaño del array.
        {
            html1 += "<tr><td>" + no_email[i] + "</td></tr>";
        }
    }
    html1 += "</table>";
    table1.innerHTML = html1; // Muestro todo en pantalla.

    if (no > 8) // Si la cantidad de Artículos es mayor que 5.
    {
        pages1.innerHTML = "Página: " + page1; // Muestro el número de página.
        if (page1 == 1) // Si la página es la número 1
        {
            prev_btn1.style.visibility = "hidden"; // Escondo el Botón con id prev que mostraría los resultados anteriores.
        }
        else // Si no, estoy en otra página.
        {
            prev_btn1.style.visibility = "visible"; // Hago visible el botón para mostrar los resultados anteriores.
        }
        if (page1 == totNumPages()) // Si estoy en la última página.
        {
            next_btn1.style.visibility = "hidden"; // Escondo el botón para mostrar los resultados siguientes.
        }
        else // Si no, estoy en una página intermedia o en la primera.
        {
            next_btn1.style.visibility = "visible"; // Hago visible el botón para mostrar los resultados siguientes.
        }
    }
}

function totNumPages2() // Función para la paginación
{
    return Math.ceil(window.yes / window.qtty_yes); // Calcula la cantidad de páginas que habrá, divide la cantidad de datos por 5 resultados a mostrar por página.
}

function prev2() // Función para ir a la página anterior.
{
    if (window.page_yes > 1) // Si la página actual es mayor que la página 1.
    {
        window.page_yes--; // Decrementa la variable page, página anterior.
        change(window.page_yes, window.qtty_yes);
    }
}

function next2() // La Función next muestra la página siguiente.
{
    if (window.page_yes < totNumPages()) // Si la página en la que estoy es menor que la última.
    {
        window.page_yes++; // Incremento la página
        change(window.page_yes, window.qtty_yes);
    }
}

function change2(page2, qtty2)
{
    window.page_yes = page2; // Asigno la variable page, a la variable global window.page.
    window.qtty_yes = qtty2; // Asigno la variable qtty, a la variable global window.qtty.
    var yes = address.length; // El Tamaño del Array de los Mensajes que Llegaron.
    window.yes = yes; // Hago global la variable yes.
    var html2 = "<table><tr><th>Dirección</th><th>Asunto</th></tr>";

    for (i = (page2 - 1) * qtty2; i < page2 * qtty2; i++) // Aquí hago el bucle desde la página donde esté, a la cantidad de resultados a mostrar.
    {
        if (i < yes) // Si i es menor que el tamaño del array.
        {
            html2 += "<tr><td>" + address[i] + "</td><td>" + subject[i] + "</td></tr>";
        }
    }
    html2 += "</table>";
    table2.innerHTML = html2; // Muestro todo en pantalla.

    if (yes > 8) // Si la cantidad de Artículos es mayor que 5.
    {
        pages2.innerHTML = "Página: " + page2; // Muestro el número de página.
        if (page2 == 1) // Si la página es la número 1
        {
            prev_btn2.style.visibility = "hidden"; // Escondo el Botón con id prev que mostraría los resultados anteriores.
        }
        else // Si no, estoy en otra página.
        {
            prev_btn2.style.visibility = "visible"; // Hago visible el botón para mostrar los resultados anteriores.
        }
        if (page2 == totNumPages2()) // Si estoy en la última página.
        {
            next_btn2.style.visibility = "hidden"; // Escondo el botón para mostrar los resultados siguientes.
        }
        else // Si no, estoy en una página intermedia o en la primera.
        {
            next_btn2.style.visibility = "visible"; // Hago visible el botón para mostrar los resultados siguientes.
        }
    }
}

function wait() // Se muestra una alerta para indicar que verificar la IP demora unos 10 segundos.
{
    alert("Verificar la IP demora unos segundos.\nHaz Click en Aceptar y Se Cargará una Nueva Página Después de Aproximadamente 10 Segundos.");
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
    let view4 = document.getElementById("view4");
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

function verify() // Función para validar las contraseñas de registro de alumnos y las de modificación.
{
    if (pass1.value != pass2.value) // Verifico si los valores en los input pass y pass2 no coinciden.
    {
        toast(1, "Hay un Error", "Las contraseñas no coinciden, has escrito: " + pass1.value + " y " + pass2.value); // Si no coinciden muestro error.
        return false; // Devuelvo false, el formulario no se envía.
    }
    else // Si son iguales.
    {
        return true; // Devuelvo true, envía el formulario.
    }
}

function showEye(which) // Función para mostrar el ojo de los input de las contraseñas, recibe el número del elemento que contiene el ojo.
{
    let eye = document.getElementById("togglePassword" + which); // Asigno a eye la id del elemento que contiene el ojo.
    eye.style.visibility = "visible"; // Hago visible el elemento, el ojo.
}

function spy(which) // Función para el ojito de las Contraseñas al hacer click en el ojito, recibe el número de la ID del input de la password.
{
    const togglePassword = document.querySelector('#togglePassword' + which); // Asigno a la constante togglePassword el input con ID togglePassword + which.
    const password = document.querySelector('#pass' + which); // Asigno a password la ID del input con ID pass + which.
    
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password'; // Asigno a type el resultado de un operador ternario, si presiono el ojito y el tipo del input es password
    // lo cambia a text, si es text lo cambia a password.
    password.setAttribute('type', type); // Le asigno el atributo al input password.
    togglePassword.classList.toggle('fa-eye-slash'); // Cambia el aspecto del ojito, al cambiar el input a tipo texto, el ojo aparece con una raya.
}

function showImg(src) // Not in Use but a Good One
{
    var alertaImg = document.getElementById("alertaImg"); // La ID del botón del dialogo.
    var img = document.getElementById("show_pic"); // Asigno a la variable title el h4 con id title.
        
    img.src = src; // Muestro los mensajes en el diálogo.
    alertaImg.click(); // Lo hago aparecer pulsando el botón con ID alerta.
}

function changeit() // Función para la página de contacto.
{
    // var changes = document.getElementById("changes"); // En la variable button obtengo la ID del input type submit change.

    if (contact.value != "") // Si el valor en el selector ha cambiado.
    {
        switch (contact.value) // Hago un switch al valor en el selector.
        {
            case "Teléfono":
                email.style.visibility = "hidden";
                phone.style.visibility = "visible";
                em.required = false;
                ph.required = true;
                changes.value = "Llamame!";
                break;
            case "Whatsapp":
                email.style.visibility = "hidden";
                phone.style.visibility = "visible";
                em.required = false;
                ph.required = true;
                changes.value = "Mandame un Guasap";
                break;
            default:
                email.style.visibility = "visible";
                phone.style.visibility = "hidden";
                ph.required = false;
                ph.value = 1;
                em.required = true;
                changes.value = "Espero tu E-mail";
                break;
        }
    }
}

function connect(how)
{
    let mssg = document.getElementById('mssg').value;
    let num = 664774821;
    var win = window.open('https://wa.me/' + num + '?text=Por Favor contactame por: ' + how + ' al: ' + mssg + ' Mi nombre es: ', '_blank');
}

function screen() // Esta función comprueba si el ancho de la pantalla es de Ordenador o de Teléfono.
{
    let width = innerWidth;
    if (width < 965) // Si el ancho es inferior a 965.
    {
        pc.style.visibility = "hidden"; // Oculta el menú de Ordenador
        mobile.style.visibility = "visible"; // Muestra el menú de Teléfono.
    }
    else // Si es mayor o igual a 965;
    {
        pc.style.visibility = "visible"; // Muestra el menú para Ordenador
        mobile.style.visibility = "hidden"; // Oculta el menú para Teléfono.
    }
}

function goThere() // Cuando cambia el selector del menú para Teléfono.
{
    var change = document.getElementById("change").value; // Change obtiene el valor en el selector.
    switch(change)
    {
        case "contact":
            window.open("contact.php", "_blank");
        break;
        case "request":
            window.open("request.php", "_self");
        break;
        case "profile" :
            window.open("profile.php", "_self");
        break;
        case "view3" :
            window.open("index.php#view3", "_self");
        break;
        case "view2" :
            window.open("index.php#view2", "_self");
        break;
        default :
            window.open("index.php#view1", "_self");
        break;
    }
}

function printIt(number)
{
    if (number != -1) // Si el numero que llega es distinto de -1.
    {
        var img = document.getElementById("img" + number); // Asigno a la variable img la ID del elemento img + numero de factura.
    }
    else // Si llega -1.
    {
        var img = document.getElementById("img0"); // Estoy viedo la última factura, es la imagen 0, Asigno a la variable img la ID del elemento img0.
    }
    const src = img.src; // Asigno a la constante src la imagen.
    const win = window.open(''); // Asigno a la constante win una nueva ventana abierta.
    win.document.write('<img src="' + src + '" onload="window.print(); window.close();">'); // Escribo en la ventana abierta un elemento img con la imagen a imprimir y la envía a la impresora y al terminar cierra la ventana.
}

function capture(number)
{
    const print = document.getElementById("printable" + number); // Asigna a print el Div con ID printable + number
    const image = document.getElementById("image" + number); // Asigna a image el Div con ID image + number, contendrá el elemento img con la factura.

    html2canvas(print).then((canvas) => {
        const base64image = canvas.toDataURL('image/png'); // genera la imagen base64image a partir del contenido de print, el div que contiene la factura.
        image.setAttribute("href", base64image);
        const img = document.createElement("img");
        img.id = "img" + number;
        img.src = base64image;
        img.alt = "Factura: " + number;
        print.remove();
        image.appendChild(img);
    });
}

function pdfDown(number)
{
    const image = document.getElementById("img" + number); // Div con ID printable0, contiene la factura.

    var doc = new jsPDF();
    doc.addImage(image, 'png', 10, 10, 240, 60, '', 'FAST');
    doc.save();
}

function givemeData(oui)
{
    alert("los datos de esta MAC son: " + oui);
}