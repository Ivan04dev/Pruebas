<?php

    class actualizaBD{

        ###########################################################
        ###########################################################

        public function actualizaDatos($con,$tb,$cadena,$msj){

        	$this->sentencia = "UPDATE $tb SET $cadena";

        	$this->sentencia;

            $this->stmt = oci_parse($con, $this->sentencia);

            oci_execute($this->stmt);

            if($msj == 1){

               echo "Los datos se actualizaron correctamente.";

            }else{

               echo "";

            }
  
            return $this->stmt;    
        
        }

        ##########################################################
        ##########################################################

        public function liberarDatos(){

        	oci_free_statement($this->stmt);

        }

        ##########################################################
        ##########################################################

    }

    /*
    class consultaBD extends conexionBD{
 
        public function consulta(){

        	parent::__construct();

        }

        public function consultaDatos($tb,$cadena){

        	$this->sentencia = "SELECT * FROM $tb $cadena";

            $this->stmt = oci_parse($this->conexion, $this->sentencia);

            oci_execute($this->stmt);

            $this->array = oci_fetch_array($this->stmt);

            return $this->array;

        }

    }
    */
?>
