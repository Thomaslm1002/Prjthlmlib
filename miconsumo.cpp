/*
 * =====================================================================
 * PROGRAMA: MiConsumo - cuanto me cuesta la energia de mi casa
 * CURSO:    Programacion Orientada a Objetos - IU Digital - 2026-2
 * =====================================================================
 */

#include <iostream>   // cout y cin
#include <string>     // tipo string
#include <vector>     // tipo vector
#include <iomanip>    // manipulación de formato (setw, setprecision)

using namespace std;

// ---------------------------------------------------------------------
// CONSTANTES
// ---------------------------------------------------------------------
const int DIAS = 7;          // filas: lunes a domingo
const int MAX_EQUIPOS = 4;   // columnas: equipos de la casa

const string NOMBRE_DIA[DIAS] = {"Lun", "Mar", "Mie", "Jue", "Vie", "Sab", "Dom"};

// =====================================================================
// CLASE Equipo
// =====================================================================
class Equipo {
private:
    string nombre;
    float potenciaW;
    float horas[DIAS];

public:
    Equipo() {
        nombre = "";
        potenciaW = 0;
        for (int d = 0; d < DIAS; d++) {
            horas[d] = 0;
        }
    }

    Equipo(string n, float p, const float h[DIAS]) {
        nombre = n;
        potenciaW = p;
        for (int d = 0; d < DIAS; d++) {
            horas[d] = h[d];
        }
    }

    string getNombre() const {
        return nombre;
    }

    void mostrar() const {
        cout << nombre << " (" << potenciaW << " W) - horas:";
        for (int d = 0; d < DIAS; d++) {
            cout << " " << horas[d];
        }
        cout << endl;
    }

    float energiaDia(int d) const {
        return potenciaW * horas[d] / 1000;
    }

    float energiaSemana() const {
        float total = 0;
        for (int d = 0; d < DIAS; d++) {
            total += energiaDia(d);
        }
        return total;
    }

    float horasSemana() const {
        float total = 0;
        for (int d = 0; d < DIAS; d++) {
            total += horas[d];
        }
        return total;
    }

    string diaDeMayorUso() const {
        int maxIndex = 0;
        for (int d = 1; d < DIAS; d++) {
            if (horas[d] > horas[maxIndex]) {
                maxIndex = d;
            }
        }
        return NOMBRE_DIA[maxIndex];
    }
};

// =====================================================================
// CLASE Hogar
// =====================================================================
class Hogar {
private:
    string nombre;
    float tarifa;
    Equipo equipos[MAX_EQUIPOS];
    int cantidad;
    float consumo[DIAS][MAX_EQUIPOS];

public:
    Hogar(string n, float t) {
        nombre = n;
        tarifa = t;
        cantidad = 0;
    }

    void setDatos(string n, float t) {
        nombre = n;
        tarifa = t;
    }

    int getCantidad() const {
        return cantidad;
    }

    Equipo getEquipo(int index) const {
        return equipos[index];
    }

    void vaciar() {
        cantidad = 0;
    }

    void mostrarEquipos() const {
        cout << defaultfloat << setprecision(6);
        if (cantidad == 0) {
            cout << "Todavia no hay equipos" << endl;
            return;
        }
        for (int e = 0; e < cantidad; e++) {
            cout << e + 1 << ". "; 
            equipos[e].mostrar();
        }
    }

    void agregarEquipo(Equipo e) {
        if (cantidad < MAX_EQUIPOS) {
            equipos[cantidad] = e;
            cantidad++;
        } else {
            cout << "No caben mas equipos" << endl;
        }
    }

    void calcularMatriz() {
        for (int d = 0; d < DIAS; d++) {
            for (int e = 0; e < cantidad; e++) {
                consumo[d][e] = equipos[e].energiaDia(d);
            }
        }
    }

    float totalDia(int d) const {
        float total = 0;
        for (int e = 0; e < cantidad; e++) {
            total += consumo[d][e];
        }
        return total;
    }

    float totalEquipo(int e) const {
        float total = 0;
        for (int d = 0; d < DIAS; d++) {
            total += consumo[d][e];
        }
        return total;
    }

    float totalSemana() const {
        float total = 0;
        for (int d = 0; d < DIAS; d++) {
            total += totalDia(d);
        }
        return total;
    }

    void mostrarMatriz() {
        cout << fixed << setprecision(2);
        cout << endl << "Consumo en kWh (filas = dias, columnas = equipos)" << endl;

        cout << setw(5) << "Dia";
        for (int e = 0; e < cantidad; e++) {
            cout << setw(18) << equipos[e].getNombre();
        }
        cout << setw(10) << "Total" << endl;

        for (int d = 0; d < DIAS; d++) {
            cout << setw(5) << NOMBRE_DIA[d];
            for (int e = 0; e < cantidad; e++) {
                cout << setw(18) << consumo[d][e];
            }
            cout << setw(10) << totalDia(d) << endl;
        }

        cout << setw(5) << "Total";
        for (int e = 0; e < cantidad; e++) {
            cout << setw(18) << totalEquipo(e);
        }
        cout << setw(10) << totalSemana() << endl;
    }

    int diaMasCaro() const {
        int mayor = 0;
        for (int d = 1; d < DIAS; d++) {
            if (totalDia(d) > totalDia(mayor)) {
                mayor = d;
            }
        }
        return mayor;
    }

    int equipoQueMasGasta() const {
        int mayor = 0;
        for (int e = 1; e < cantidad; e++) {
            if (totalEquipo(e) > totalEquipo(mayor)) {
                mayor = e;
            }
        }
        return mayor;
    }

    float porcentajeEquipo(int e) const {
        float totSemana = totalSemana();
        if (totSemana == 0) return 0;
        return (totalEquipo(e) / totSemana) * 100.0f;
    }

    void reporte() {
        int dia = diaMasCaro();
        int equipo = equipoQueMasGasta();
        float costoSemana = totalSemana() * tarifa;

        cout << fixed << setprecision(0);
        cout << endl << "===== REPORTE: " << nombre << " =====" << endl;
        cout << "Tarifa: " << tarifa << " COP/kWh" << endl;
        cout << "Dia mas caro: " << NOMBRE_DIA[dia]
             << " (" << totalDia(dia) * tarifa << " COP)" << endl;
        cout << "Equipo que mas gasta: " << equipos[equipo].getNombre()
             << " (" << totalEquipo(equipo) * tarifa << " COP en la semana)" << endl;
        cout << "Costo de la semana: " << costoSemana << " COP" << endl;
        cout << "Proyeccion del mes (x 4): " << costoSemana * 4 << " COP" << endl;
    }

    float getTarifa() const { return tarifa; }
};

// =====================================================================
// PROGRAMA PRINCIPAL
// =====================================================================
int main() {
    Hogar casa("Mi casa", 0);
    int opcion = 0;

    do {
        cout << endl << "===== MiConsumo =====" << endl;
        cout << "1. Datos de la casa (nombre y tarifa)" << endl;
        cout << "2. Agregar un equipo" << endl;
        cout << "3. Ver los equipos" << endl;
        cout << "4. Ver la matriz de consumo" << endl;
        cout << "5. Ver el reporte" << endl;
        cout << "6. Cargar la casa de ejemplo" << endl;
        cout << "7. Simular un ahorro" << endl;
        cout << "8. Ver un equipo" << endl;
        cout << "0. Salir" << endl;
        cout << "Opcion: ";
        cin >> opcion;

        switch (opcion) {

        case 1: {
            string nombre;
            float tarifa;
            cout << "Nombre de la casa: ";
            getline(cin >> ws, nombre);
            do {
                cout << "Tarifa de su factura (COP por kWh): ";
                cin >> tarifa;
            } while (tarifa <= 0);
            casa.setDatos(nombre, tarifa);
            cout << "Datos guardados" << endl;
            break;
        }

        case 2: {
            if (casa.getCantidad() == MAX_EQUIPOS) {
                cout << "Ya hay " << MAX_EQUIPOS << " equipos: no caben mas" << endl;
                break;
            }
            string nombre;
            float potencia;
            float horas[DIAS];
            cout << "Nombre del equipo: ";
            getline(cin >> ws, nombre);
            do {
                cout << "Potencia en vatios (W), segun la etiqueta: ";
                cin >> potencia;
            } while (potencia <= 0);

            for (int d = 0; d < DIAS; d++) {
                do {
                    cout << "Horas de uso el " << NOMBRE_DIA[d] << " (0 a 24): ";
                    cin >> horas[d];
                } while (horas[d] < 0 || horas[d] > 24);
            }
            casa.agregarEquipo(Equipo(nombre, potencia, horas));
            cout << "Equipo agregado" << endl;
            break;
        }

        case 3:
            casa.mostrarEquipos();
            break;

        case 4:
            if (casa.getCantidad() == 0) {
                cout << "Primero agregue equipos (opcion 2 o 6)" << endl;
            } else {
                casa.calcularMatriz();
                casa.mostrarMatriz();
            }
            break;

        case 5:
            if (casa.getCantidad() == 0) {
                cout << "Primero agregue equipos (opcion 2 o 6)" << endl;
            } else {
                casa.calcularMatriz();
                casa.reporte();
            }
            break;

        case 6: {
            float hDucha[DIAS]      = {0.5, 0.5, 0.5, 0.5, 0.5, 1.0, 1.0};
            float hPlancha[DIAS]    = {0, 1, 0, 1, 0, 2, 0};
            float hComputador[DIAS] = {6, 6, 6, 6, 6, 2, 2};
            float hTelevisor[DIAS]  = {3, 3, 3, 3, 4, 6, 6};
            casa.vaciar();
            casa.setDatos("Casa de ejemplo", 950);
            casa.agregarEquipo(Equipo("Ducha electrica", 3500, hDucha));
            casa.agregarEquipo(Equipo("Plancha", 1200, hPlancha));
            casa.agregarEquipo(Equipo("Computador", 250, hComputador));
            casa.agregarEquipo(Equipo("Televisor", 120, hTelevisor));
            cout << "Casa de ejemplo cargada: 4 equipos" << endl;
            break;
        }

        case 7: {
            if (casa.getCantidad() == 0) {
                cout << "No hay equipos registrados en el hogar." << endl;
                break;
            }
            casa.calcularMatriz();
            float pctAhorro;
            cout << "Ingrese el porcentaje de ahorro estimado en tiempo de uso (0 a 100%): ";
            cin >> pctAhorro;
            if (pctAhorro < 0 || pctAhorro > 100) {
                cout << "Porcentaje no valido." << endl;
                break;
            }
            float costoActualSemana = casa.totalSemana() * casa.getTarifa();
            float ahorroSemana = costoActualSemana * (pctAhorro / 100.0f);
            float costoNuevoSemana = costoActualSemana - ahorroSemana;

            cout << fixed << setprecision(0);
            cout << "\n--- SIMULACION DE AHORRO (" << pctAhorro << "%) ---" << endl;
            cout << "Costo actual semanal: " << costoActualSemana << " COP" << endl;
            cout << "Nuevo costo semanal: " << costoNuevoSemana << " COP" << endl;
            cout << "Ahorro proyectado al mes: " << ahorroSemana * 4 << " COP" << endl;
            break;
        }

        case 8: {
            int totalEquipos = casa.getCantidad();
            if (totalEquipos == 0) {
                cout << "No hay equipos registrados en el hogar" << endl;
                break;
            }

            casa.calcularMatriz();
            casa.mostrarEquipos();

            int numEquipo;
            do {
                cout << "Ingrese el numero del equipo (1 a " << totalEquipos << "): ";
                cin >> numEquipo;
                if (numEquipo < 1 || numEquipo > totalEquipos) {
                    cout << "Error: El equipo no existe. Intente de nuevo." << endl;
                }
            } while (numEquipo < 1 || numEquipo > totalEquipos);

            int idx = numEquipo - 1;
            Equipo eq = casa.getEquipo(idx);

            cout << "\n--- DETALLES DEL EQUIPO: " << eq.getNombre() << " ---" << endl;
            cout << "• Horas de uso en la semana: " << eq.horasSemana() << " hrs" << endl;
            cout << "• Dia de mayor uso: " << eq.diaDeMayorUso() << endl;
            cout << fixed << setprecision(2);
            cout << "• Porcentaje del consumo total del hogar: " 
                 << casa.porcentajeEquipo(idx) << "%" << endl;
            break;
        }

        case 0:
            cout << "Hasta luego" << endl;
            break;

        default:
            cout << "Opcion no valida" << endl;
        }

    } while (opcion != 0);

    return 0;
}