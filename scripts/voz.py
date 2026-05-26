import sys
import os
import re
import time
import tempfile
import ctypes
from gtts import gTTS

def play_mp3(filepath):
    """Reproduce un archivo MP3 de forma nativa en Windows usando mciSendString"""
    try:
        mciSendString = ctypes.windll.winmm.mciSendStringW
        # Abrir el archivo de audio
        alias = "myaudio"
        open_command = f'open "{filepath}" type mpegvideo alias {alias}'
        mciSendString(open_command, None, 0, 0)
        
        # Reproducir y esperar a que termine
        play_command = f'play {alias} wait'
        mciSendString(play_command, None, 0, 0)
        
        # Cerrar el dispositivo
        close_command = f'close {alias}'
        mciSendString(close_command, None, 0, 0)
    except Exception as e:
        print(f"(Fallo reproductor nativo: {e})", flush=True)

def speak(text):
    """Genera y reproduce voz en perfecto español usando Google TTS"""
    print(f"[Asistente de Voz]: {text}", flush=True)
    try:
        # Generar audio usando la voz de Google
        tts = gTTS(text=text, lang='es-us', tld='com.mx') 
        
        # Crear archivo temporal
        fd, temp_path = tempfile.mkstemp(suffix=".mp3")
        os.close(fd)
        tts.save(temp_path)
        
        # Reproducir
        play_mp3(temp_path)
        
        # Pequeña pausa para asegurar que el SO soltó el archivo antes de borrarlo
        time.sleep(0.2)
        try:
            os.remove(temp_path)
        except:
            pass
    except Exception as e:
        print(f"(Error de TTS: {e})", flush=True)

def parse_test_results(filepath):
    error_meanings = {
        r"404": "Error 404: La ruta solicitada es inexistente o está mal definida.",
        r"(?i)Class.*not found": "Error crítico: Existe una clase o modelo faltante en tu código.",
        r"(?i)SQLSTATE": "Error SQL: Hay un problema en la base de datos, probablemente falte ejecutar migraciones o la tabla no existe.",
        r"(?i)Authentication failed": "Fallo de seguridad: Hubo un error de autenticación o las credenciales usadas son inválidas.",
        r"(?i)Undefined variable": "Error de sintaxis: Estás intentando usar una variable que no ha sido definida en el código.",
        r"(?i)Call to undefined method": "Error de método: Estás intentando llamar a un método que no existe en la clase.",
        r"(?i)Expected response status code": "Error de aserción: La respuesta del servidor no fue la esperada. Probablemente recibiste un error 500 en lugar del estado esperado."
    }

    results = {
        'failed': 0,
        'passed': 0,
        'errors': [],
        'passed_tests': []
    }

    if not os.path.exists(filepath):
        print(f"Error: No se encontró el archivo de log: {filepath}", flush=True)
        return results

    with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
        lines = f.readlines()

    current_failed_test = None
    error_buffer = []

    fail_pattern = re.compile(r'^\s*(?:FAIL|⨯|F)\s+(.+)$')
    phpunit_fail_pattern = re.compile(r'^\d+\)\s+(.+)$')
    pass_pattern = re.compile(r'^\s*(?:✓|OK)\s+(.+)$')

    for line in lines:
        clean_line = line.strip()
        clean_line = re.sub(r'\x1b\[[0-9;]*m', '', clean_line) 

        match_pass = pass_pattern.match(clean_line)
        if match_pass:
            test_name = match_pass.group(1).strip()
            test_name = re.sub(r'\s+[\d\.]+s\s*$', '', test_name)
            results['passed_tests'].append(test_name)
            results['passed'] += 1
            continue

        match = fail_pattern.match(clean_line)
        match2 = phpunit_fail_pattern.match(clean_line)

        if match or match2:
            if current_failed_test:
                results['errors'].append({'name': current_failed_test, 'details': " ".join(error_buffer)})
            
            current_failed_test = match.group(1).strip() if match else match2.group(1).strip()
            current_failed_test = current_failed_test.split('  ')[0].strip()
            current_failed_test = re.sub(r'\s+[\d\.]+s\s*$', '', current_failed_test)
            
            error_buffer = []
            results['failed'] += 1
            continue

        if current_failed_test:
            if clean_line and not clean_line.startswith('Tests:') and not clean_line.startswith('Time:'):
                error_buffer.append(clean_line)

    if current_failed_test:
        results['errors'].append({'name': current_failed_test, 'details': " ".join(error_buffer)})

    for error in results['errors']:
        explanations = []
        for regex, meaning in error_meanings.items():
            if re.search(regex, error['details']):
                explanations.append(meaning)
        if not explanations:
            if "Expected" in error['details'] and "Actual" in error['details']:
                explanations.append("El valor esperado en la prueba no coincide con el valor devuelto por tu sistema.")
            else:
                explanations.append("Se detectó un error lógico no clasificado. Por favor, revisa la consola para más detalles.")
        error['explanations'] = explanations

    return results

def main():
    if len(sys.argv) < 2:
        print("Uso: python voz.py <ruta_al_log_de_tests>", flush=True)
        return

    log_filepath = sys.argv[1]

    print("\n--- INICIANDO ASISTENTE DE VOZ INTELIGENTE (Google TTS) ---", flush=True)
    print("Analizando resultados, por favor espera...", flush=True)
    results = parse_test_results(log_filepath)

    if results['failed'] == 0:
        if results['passed'] > 0:
            # Diccionario de significados para los tests exitosos (ordenados de más específico a menos)
            test_explanations = {
                "users no puede autenticarse": "Se ha comprobado la seguridad. El sistema rechaza correctamente los intentos de acceso con datos erróneos.",
                "users puede autenticarse": "El sistema de validación de credenciales funciona. Los trabajadores pueden entrar a sus paneles con normalidad.",
                "users puede cerrar sesión": "El cierre de sesión seguro es funcional. Esto asegura que nadie pueda usar una cuenta después de que el usuario se retire.",
                "login screen": "La interfaz de acceso ha sido validada. Esto garantiza que tus usuarios siempre verán el formulario de entrada.",
                "dni duplicado": "Regla de negocio validada. El sistema impide registros duplicados de personal, manteniendo tu base de datos limpia.",
                "estado de un trabajador a inactivo": "La gestión de personal funciona. Ahora puedes dar de baja a trabajadores de forma segura sin borrar sus registros históricos.",
                "successful response": "La conexión con el servidor es estable y la página principal responde sin demoras.",
                "that true is true": "Esta es una prueba de integridad básica para confirmar que el motor de tests está encendido."
            }

            speak(f"He analizado el sistema y he ejecutado {results['passed']} pruebas exitosamente.")
            
            speak("A continuación, detallaré qué significa cada resultado obtenido.")
            for test_name in results['passed_tests']:
                # Limpieza y traducción básica para el nombre del test
                test_name_clean = test_name.replace("_", " ")
                test_name_clean = test_name_clean.replace("can be rendered", "puede visualizarse")
                test_name_clean = test_name_clean.replace("can authenticate", "puede autenticarse")
                test_name_clean = test_name_clean.replace("can logout", "puede cerrar sesión")
                test_name_clean = test_name_clean.replace("can not authenticate", "no puede autenticarse")
                
                speak(f"Resultado: {test_name_clean}.")
                
                # Buscar una explicación detallada basada en palabras clave
                encontrado = False
                for key, meaning in test_explanations.items():
                    if key.lower() in test_name.lower() or key.lower() in test_name_clean.lower():
                        speak(meaning)
                        encontrado = True
                        break
                
                if not encontrado:
                    speak("Este test confirma que una función específica del código responde según lo programado.")
                
                # Pequeña pausa entre explicaciones para no saturar
                time.sleep(0.3)
            
            speak("Excelente trabajo. Todos los procesos críticos han sido validados y tu sistema es ahora más robusto.")
        else:
            with open(log_filepath, 'r', encoding='utf-8', errors='ignore') as f:
                content = f.read()
                if "PASS" in content or "OK" in content:
                    speak("Todos los tests fueron ejecutados correctamente. Tu sistema está estable.")
                else:
                    speak("Atención. No se encontraron resultados de tests en el archivo o hubo un error fatal.")
    else:
        speak(f"Atención. Se han encontrado {results['failed']} errores en los tests de tu proyecto.")
        
        for idx, error in enumerate(results['errors']):
            test_name = error['name'].split("::")[-1] 
            test_name_clean = test_name.replace("_", " ")
            
            speak(f"Error número {idx + 1}. El test '{test_name_clean}' ha fallado.")
            for explanation in error['explanations']:
                speak(explanation)

    print("--- ANÁLISIS FINALIZADO ---\n", flush=True)

if __name__ == "__main__":
    main()
