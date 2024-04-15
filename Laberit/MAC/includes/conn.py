# Conexión con la base de datos en PDO.
import os
import mysql.connector

try:
    conn = mysql.connector.connect(
        host="localhost",
        database="macs",
        user="root",
        password=os.environ["MySQL"]
    )
except mysql.connector.Error as e:
    print("Error:", e.msg)