import os

# Función para renombrar la imagen dentro de cada carpeta
def renombrar_imagen(carpeta, numero_serie):
    # Iterar sobre los archivos en la carpeta
    for archivo in os.listdir(carpeta):
        # Verificar si el archivo es un número sin ninguna terminación
        nombre_archivo, extension = os.path.splitext(archivo)
        
        # Si el nombre del archivo es solo numérico y no tiene extensión ni sufijos adicionales
        if nombre_archivo.isdigit() and not extension:
            # Obtener la ruta completa del archivo actual
            archivo_viejo = os.path.join(carpeta, archivo)
            # Nueva ruta del archivo con el número de serie (sin cambiar extensión)
            archivo_nuevo = os.path.join(carpeta, f"{numero_serie}")
            
            # Renombrar el archivo
            os.rename(archivo_viejo, archivo_nuevo)
            print(f"Archivo renombrado: {archivo_viejo} -> {archivo_nuevo}")
            # Solo se renombra uno, por lo que rompemos el bucle
            break

# Obtener el directorio actual donde se encuentra el script
directorio_actual = os.getcwd()

# Iterar sobre todas las carpetas dentro del directorio actual
for carpeta in os.listdir(directorio_actual):
    carpeta_ruta = os.path.join(directorio_actual, carpeta)
    
    # Verificar si es una carpeta y si el nombre es numérico (asumiendo que son los números de serie)
    if os.path.isdir(carpeta_ruta) and carpeta.isdigit():
        numero_serie = carpeta  # El nombre de la carpeta es el número de serie
        print(f"Procesando carpeta: {carpeta_ruta}")
        
        # Renombrar la imagen dentro de esta carpeta
        renombrar_imagen(carpeta_ruta, numero_serie)
