def totNumPages():
    return math.ceil(len(window) / window.qtty)

def prev():
    if window.page > 1:
        window.page -= 1
        change(window.page, window.qtty)

def next():
    if window.page < totNumPages():
        window.page += 1
        change(window.page, window.qtty)

def change(page, qtty):
    window.page = page
    window.qtty = qtty
    length = len(oui)
    window.length = length

    html = "<table><tr><th>OUI</th><th>Dirección MAC</th><th>Fabricante</th><th>IP</th><th>Dispositivo</th><th>Puertos</th><th>Privada</th><th>Tipo</th><th>Actualizada</th><th>Ataques</th><th>Fecha</th></tr>"
    for i in range((page - 1) * qtty, page * qtty):
        if i < length:
            html += "<tr><td>" + oui[i] + "</td><td>" + mac[i] + "</td><td>" + mark[i] + "</td><td>" + ip[i] + "</td><td>" + device[i] + "</td><td>" + port[i] + "</td><td>" + private[i] + "</td><td>" + type[i] + "</td><td>" + update[i] + "</td><td>" + attacks[i] + "</td><td>" + date[i] + "</td></tr>"
    html += "</table>"
    table.innerHTML = html

    if length > 5:
        pages.innerHTML = "Página: " + str(page)
        if page == 1:
            prev_btn.style.visibility = "hidden"
        else:
            prev_btn.style.visibility = "visible"
        if page == totNumPages():
            next_btn.style.visibility = "hidden"
        else:
            next_btn.style.visibility = "visible"

def wait():
    alert("Verificar la IP demora unos segundos.\nHaz Click en Aceptar y Se Cargará una Nueva Página Después de Aproximadamente 10 Segundos.")

def toast(warn, ttl, msg):
    if warn == 1:
        title.style.backgroundColor = "#000000"
        title.style.color = "yellow"
    elif warn == 0:
        title.style.backgroundColor = "#FFFFFF"
        title.style.color = "blue"
    else:
        title.style.backgroundColor = "#000000"
        title.style.color = "red"
    title.innerHTML = ttl
    message.innerHTML = msg
    alerta.click()

def screenSize():
    view3 = document.getElementById("view3")
    height = window.innerHeight

    if view1.offsetHeight < height:
        view1.style.height = str(height) + "px"

    if view2 != None:
        if view2.offsetHeight < height:
            view2.style.height = str(height) + "px"
        if view3 != None:
            if view3.offsetHeight < height:
                view3.style.height = str(height) + "px"
            if view4 != None:
                if view4.offsetHeight < height:
                    view4.style.height = str(height) + "px"

def verify():
    if pass1.value != pass2.value:
        toast(1, "Hay un Error", "Las contraseñas no coinciden, has escrito: " + pass1.value + " y " + pass2.value)
        return False
    else:
        return True

def showEye(which):
    eye = document.getElementById("togglePassword" + which)
    eye.style.visibility = "visible"

def spy(which):
    togglePassword = document.querySelector('#togglePassword' + which)
    password = document.querySelector('#pass' + which)
    
    type = password.getAttribute('type')
    if type == 'password':
        type = 'text'
    else:
        type = 'password'
    password.setAttribute('type', type)
    togglePassword.classList.toggle('fa-eye-slash')

def showImg(src):
    alertaImg = document.getElementById("alertaImg")
    img = document.getElementById("show_pic")
        
    img.src = src
    alertaImg.click()

def changeit():
    if contact.value != "":
        if contact.value == "Teléfono":
            email.style.visibility = "hidden"
            phone.style.visibility = "visible"
            em.required = False
            ph.required = True
            changes.value = "Llamame!"
        elif contact.value == "Whatsapp":
            email.style.visibility = "hidden"
            phone.style.visibility = "visible"
            em.required = False
            ph.required = True
            changes.value = "Mandame un Guasap"
        else:
            email.style.visibility = "visible"
            phone.style.visibility = "hidden"
            ph.required = False
            ph.value = 1
            em.required = True
            changes.value = "Espero tu E-mail"

def connect(how):
    mssg = document.getElementById('mssg').value
    num = 664774821
    win = window.open('https://wa.me/' + str(num) + '?text=Por Favor contactame por: ' + how + ' al: ' + mssg + ' Mi nombre es: ', '_blank')

def screen():
    width = innerWidth
    if width < 965:
        pc.style.visibility = "hidden"
        mobile.style.visibility = "visible"
    else:
        pc.style.visibility = "visible"
        mobile.style.visibility = "hidden"

def goThere():
    change = document.getElementById("change").value
    if change == "contact":
        window.open("contact.php", "_blank")
    elif change == "request":
        window.open("request.php", "_self")
    elif change == "profile":
        window.open("profile.php", "_self")
    elif change == "view3":
        window.open("index.php#view3", "_self")
    elif change == "view2":
        window.open("index.php#view2", "_self")
    else:
        window.open("index.php#view1", "_self")

def printIt(number):
    if number != -1:
        img = document.getElementById("img" + str(number))
    else:
        img = document.getElementById("img0")
    src = img.src
    win = window.open('')
    win.document.write('<img src="' + src + '" onload="window.print(); window.close();">')

def capture(number):
    print = document.getElementById("printable" + str(number))
    image = document.getElementById("image" + str(number))

    html2canvas(print).then((canvas) => {
        base64image = canvas.toDataURL('image/png')
        image.setAttribute("href", base64image)
        img = document.createElement("img")
        img.id = "img" + str(number)
        img.src = base64image
        img.alt = "Factura: " + str(number)
        print.remove()
        image.appendChild(img)
    })

def pdfDown(number):
    image = document.getElementById("img" + str(number))

    doc = jsPDF()
    doc.addImage(image, 'png', 10, 10, 240, 60, '', 'FAST')
    doc.save()

def givemeData(oui):
    alert("los datos de esta MAC son: " + oui)