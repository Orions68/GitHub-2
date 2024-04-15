const fs = require('fs');
const { execSync } = require('child_process');

const conn = require('./includes/conn');
const header = require('./includes/header');
const modalIndex = require('./includes/modal_index.html');

const ip = process.env.ip;
if (ip) {
    const ip = process.env.ip;
    execSync(`nmap ${ip} > data.txt`);
    const mac = loadFile(ip);
    if (mac) {
        const mac_result = mac.split(";");
    } else {
        console.log("<script>toast(2, 'ERROR:', 'No se Han Podido Obtener los Datos Verifica que el Disco no esté Lleno y si Tienes Permisos Para Escribir en la Carpeta del Proyecto.');</script>");
    }

    console.log(`
        <fieldset>
            <legend>Datos de la MAC Sospechosa y su IP</legend>
            <form action="review.php" method="post">
            <label><input type="text" name="mac" value="${mac_result[0]}" required> MAC Address</label>
            <br><br>
            <label><input type="text" name="device" value="${mac_result[1]}" required> Device Name</label>
            <br><br>
            <label><input type="text" name="port" value="${mac_result[2]}"> Open Ports</label>
            <br><br>
            <label><input type="text" name="ip2" value="${ip}" required> IP Address</label>
            <br><br>
            <input type="submit" value="Almacena la MAC y la IP">
            </form>
        </fieldset>
    `);
}

const mac = process.env.mac;
if (mac) {
    const mac = process.env.mac;
    const ip = process.env.ip2;
    const device = process.env.device;
    const port = process.env.port;

    getOui(conn, mac, ip, device, port);
}

function loadFile(ip) {
    let mac = null;
    const filename = "data.txt";
    const file = fs.readFileSync(filename, "utf-8");
    if (file) {
        const lines = file.split("\n");
        let port_index = 0;
        for (let i = 0; i < lines.length; i++) {
            const line = lines[i];
            if (line.startsWith("Nmap scan")) {
                const device = line;
            } else if (line.startsWith("PORT")) {
                const ports = [];
                let port = true;
                ports[port_index] = lines[i + 1];
                while (port) {
                    port_index++;
                    ports[port_index] = lines[i + port_index];
                    if (ports[port_index].startsWith("MAC Address:")) {
                        port = false;
                    }
                }
                mac = ports[port_index].split(" ");
            } else if (line.startsWith("MAC Address:")) {
                mac = line.split(" ");
            }
        }
        const device_name = device.split(" ");
        let port_number = "";
        for (let i = 0; i < port_index; i++) {
            port_number += ports[i];
        }
        mac[2] += ";" + device_name[4] + ";" + port_number;
    }
    return mac[2];
}

function getOui(conn, mac, ip, device, port) {
    if (port === "") {
        port = null;
    }
    const ma_s = mac.substring(0, 13);
    const ma_m = mac.substring(0, 10);
    const ma_l = mac.substring(0, 8);
    let ok = search(conn, ma_s, mac, ip, device, port);
    if (!ok) {
        ok = search(conn, ma_m, mac, ip, device, port);
        if (!ok) {
            ok = search(conn, ma_l, mac, ip, device, port);
            if (!ok) {
                console.log("<script>toast(2, 'CUIDADO:', 'La MAC Detectada no es Valida, puede tratarse de una MAC Virtual o Randomizada, Android, IOS o Virtual.');</script>");
                const date = new Date().toLocaleString('en-US', { timeZone: 'Europe/London' });
                const sql = "INSERT INTO intruder VALUES(:oui, :mac, :ip, :mark, :device, :open_ports, :private, :type, :up_date, :date, :attacks);";
                const stmt = conn.prepare(sql);
                stmt.execute({ oui: ma_l, mac, ip, mark: "Android, IOS, Virtual", device, open_ports: port, private: 1, type: "MA_L", up_date: "1970-01-01", date, attacks: 1 });
            }
        }
    }
}

function search(conn, oui, mac, ip, device, port) {
    const date = new Date().toLocaleString('en-US', { timeZone: 'Europe/London' });
    if (port === "") {
        port = null;
    }
    const sql = "SELECT * FROM mac WHERE macPrefix=:oui";
    const stmt = conn.prepare(sql);
    stmt.execute({ oui });
    if (stmt.rowCount() > 0) {
        const row = stmt.fetch(PDO.FETCH_OBJ);
        const sql = "SELECT oui FROM intruder WHERE mac=:mac AND ip=:ip";
        const stmt = conn.prepare(sql);
        stmt.execute({ mac, ip });
        if (stmt.rowCount() > 0) {
            const sql = "UPDATE intruder SET attacks = attacks + 1, date = NOW() WHERE oui=:oui";
            const stmt = conn.prepare(sql);
            stmt.execute({ oui });
            console.log("<script>toast(1, 'ALERTA:', 'Se Ha Detectado un Ataque de una MAC con una IP ya Registradas.<br>Tomen las Precauciones Necesarias.');</script>");
        } else {
            const sql = "SELECT oui FROM intruder WHERE ip=:ip";
            const stmt = conn.prepare(sql);
            stmt.execute({ ip });
            if (stmt.rowCount() > 0) {
                const sql = "INSERT INTO intruder VALUES(:oui, :mac, :ip, :mark, :device, :open_ports, :private, :type, :up_date, :date, :attacks);";
                const stmt = conn.prepare(sql);
                stmt.execute({ oui: row.macPrefix, mac, ip, mark: row.vendorName, device, open_ports: port, private: row.private, type: row.blockType, up_date: row.lastUpdate, date, attacks: 1 });
                console.log("<script>toast(1, 'ALERTA:', 'Se Ha Detectado un Ataque de una IP ya Registrada pero asignada a otra MAC.<br>Tomen las Precauciones Necesarias.');</script>");
            } else {
                const result = `${row.macPrefix} - ${row.vendorName} - ${row.private} - ${row.blockType} - ${row.lastUpdate}`;
                console.log(`<script>toast(0, 'Resultado:', 'Se ha Encontrado la MAC en la Base de Datos.<br>Estos son los datos de la MAC:<br>${result}<br><br>Se Han Agregado los Datos a la Base de Datos.');</script>`);
                const sql = "INSERT INTO intruder VALUES(:oui, :mac, :ip, :mark, :device, :open_ports, :private, :type, :up_date, :date, :attacks);";
                const stmt = conn.prepare(sql);
                stmt.execute({ oui: row.macPrefix, mac, ip, mark: row.vendorName, device, open_ports: port, private: row.private, type: row.blockType, up_date: row.lastUpdate, date, attacks: 1 });
            }
        }
        return true;
    } else {
        return false;
    }
}