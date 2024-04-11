const { exec } = require('child_process'); 

const ip = 'tu_direccion_ip_interna';
 
exec(`nmap 192.168.83.1`, (error, stdout, stderr) => {
  if (error) {
    console.error(`Error al ejecutar el comando: ${error}`);
    return;
  }
  console.log(`Output del comando nmap:\n${stdout}`);
});