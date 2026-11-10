<?php
    /* 
    @autor: Thomas Lopera Muñoz
    @fecha: 17/09/2026
    @descripción: Controlador para el modelo 'clientes' a la base de datos
    */

    // Asegúrate de que la línea anterior termine en ';'
    require_once(__DIR__ . '/conexionbd.php');
    require_once(__DIR__ . '/../model/clientes.php');
    class Clientesctrl {
        private $clienteMdl;
        private $conn;

        public function __construct() {
            $this->conn = ConexionBD::getConn();
        }

        // Método que retorna todos los registros de la tabla 'clientes'
        // CRUD - CReadUD
        public function listar() {
            try {
                $clientes = array();
                // Se crea la Sentencia SQL
                $stmt = $this->conn->prepare("SELECT id_cliente, identificacion, nombre, apellidos, celular, direccion, correo FROM clientes");
                // Se ejecuta la sentencia SQL
                $stmt->execute();
                // Recuperamos los registros (si los hay)
                $resultSet =$stmt->fetchAll(PDO::FETCH_ASSOC);

                if($stmt->rowCount() > 0) {
                    foreach($resultSet as $cli) {
                        $this->clienteMdl = new Clientes ($cli ['identificacion'], $cli['nombre'], $cli['apellidos'], $cli['celular'], $cli['direccion'], $cli['correo']);
                        $this->clienteMdl->setIdCliente($cli ['id_cliente']);
                        $clientes[] = $this->clienteMdl;
                    }
                }
            } catch (Exception $exc) {
                die("Error: " . $exc->getMessage());
            }

            return $clientes;

        }// fin del metodo 'listar()'

        public function obtenerPorId($idCliente) {
            try {
                $stmt = $this->conn->prepare(
                    "SELECT id_cliente, identificacion, nombre, apellidos, celular, direccion, correo FROM clientes WHERE id_cliente = ?"
                );
                $stmt->execute(array($idCliente));
                $cli = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$cli) {
                    return null;
                }

                $cliente = new Clientes(
                    $cli['identificacion'],
                    $cli['nombre'],
                    $cli['apellidos'],
                    $cli['celular'],
                    $cli['direccion'],
                    $cli['correo']
                );
                $cliente->setIdCliente($cli['id_cliente']);
                return $cliente;
            } catch (Exception $exc) {
                error_log("Error al consultar cliente: " . $exc->getMessage());
                return null;
            }
        }

        // método para insertar un registro
      public function insertar($cliente) {
          try {
              $stmt = $this->conn->prepare(
                  "INSERT INTO clientes (identificacion, nombre, apellidos, celular, direccion, correo) VALUES (?, ?, ?, ?, ?, ?)"
              );
              return $stmt->execute(array(
                  $cliente->getIdentificacion(),
                  $cliente->getNombre(),
                  $cliente->getApellido(),
                  $cliente->getCelular(),
                  $cliente->getDireccion(),
                  $cliente->getCorreo()
              ));
          } catch(Exception $exc) {
              error_log("Error al insertar cliente: " . $exc->getMessage());
              return false;
          }
      } // fin del método 'insertar()'

      // método para actualizar un registro
        public function actualizar($cliente) {
            try {
                $stmt = $this->conn->prepare(
                    "UPDATE clientes SET identificacion = ?, nombre = ?, apellidos = ?, celular = ?, direccion = ?, correo = ? WHERE id_cliente = ?"
                );
                return $stmt->execute(array(
                    $cliente->getIdentificacion(),
                    $cliente->getNombre(),
                    $cliente->getApellido(),
                    $cliente->getCelular(),
                    $cliente->getDireccion(),
                    $cliente->getCorreo(),
                    $cliente->getIdCliente()
                ));
            } catch (Exception $exc) {
                error_log("Error al actualizar cliente: " . $exc->getMessage());
                return false;
            }
        } // fin del método 'actualizar()'
        // método para eliminar un registro
        public function eliminar($idCliente) {
            $eliminado = 0;
            try {
                $stmt = $this->conn->prepare("DELETE FROM clientes WHERE id_cliente = ?");
                $stmt->execute(array($idCliente));
                $eliminado = $stmt->rowCount();
            }catch (Exception $exc) {
                die("Error: " . $exc->getMessage());
                $eliminado = -1;
            }

            return $eliminado;
        } // fin del método 'eliminar'
    }// fin de la clase 'Clientesctrl'
?>