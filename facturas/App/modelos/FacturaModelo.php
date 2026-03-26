<?php

class FacturaModelo {
    private $db;

    public function __construct(){
        $this->db = new Base;
    }


    public function getProfesor($id_profesor){
        $this->db->query("SELECT * FROM cpifp_profesor
                                WHERE id_profesor=:id_profesor");

        $this->db->bind(':id_profesor',$id_profesor);

        return $this->db->registro();
    }



    public function getRolesProfesor($id_profesor){
        $this->db->query("SELECT * 
                                    FROM cpifp_profesor_departamento
                                        NATURAL JOIN cpifp_rol
                                        NATURAL JOIN cpifp_departamento
                                    WHERE id_profesor=:id_profesor");

        $this->db->bind(':id_profesor',$id_profesor);

        return $this->db->registros();
    }
    
    
   
    
    
    /**
     * Método para obtener los destinos en los que puede justificar
     * fracturas un profesor
     */
    public function getDestinos($id_profesor){

       /* $this->db->query("SELECT * FROM fact_destino 
                         where fact_destino.Destino_Id in 
                                        (SELECT fact_profesor_destino.id_destino
                                         FROM fact_profesor_destino
                                         WHERE fact_profesor_destino.id_profesor=:id_profesor)");
        */
    

        $this->db->query("SELECT * FROM fact_destino 
                            where Activo ='S'
                            AND fact_destino.CodigoDepartamento IN (SELECT id_departamento 
							FROM cpifp_profesor_departamento
							WHERE id_profesor =:id_profesor
							AND id_rol=30)"); //el rol 30 es jefe de deparatmento
        $this->db->bind(':id_profesor',$id_profesor);

        return $this->db->registros();
    }

    /**
     * Un miembro del Equipo directivo puede justificar facturas de todos los departamentos
     */
    public function getDestinosEquipoDirectivo(){

     
         $this->db->query("SELECT * FROM fact_destino 
                             where Activo ='S'
                             Order by fact_destino.CodigoDepartamento desc"); //el rol 30 es jefe de deparatmento
       
 
         return $this->db->registros();
     }


    
    /**Función para obtener el numero de proveedores */

    public function getNumProveedores($id_departamento ,$cif, $nombre){
        $sql="SELECT count(*) as Nproveedores FROM fact_proveedor p";
            
            if($id_departamento>0)
                {
                    $sql.=" LEFT JOIN fact_factura f ON f.CIF = p.CIF";
                    $sql.=" WHERE  Destino_Id =:id_departamento";
                    $sql.="  AND p.Nombre LIKE '%$nombre%' AND p.CIF LIKE '%$cif%'";
                                        
                    $this->db->query($sql);
                    $this->db->bind(':id_departamento', $id_departamento);  
                }
            else{
                    $sql.=" WHERE p.Nombre LIKE '%$nombre%' AND p.CIF LIKE '%$cif%'";
                    $this->db->query($sql);
            }    

        $resultado=$this->db->registro();
        return $resultado->Nproveedores;
         
    }    
    
    /**
     * FUNCIÓN getProveedores obtinene los proveedores de un departamento dado
     * si el departamento pasado por parámetro es <=0 muestra todos los proveedores
     */
    
     public function getProveedores($id_departamento){
    
       
        if($id_departamento<=0)
        {
            $sql="SELECT * FROM fact_proveedor p";
            
            
           $this->db->query($sql);
        }    
        else
        {
            
            $sql="SELECT * 
            FROM fact_proveedor p
            WHERE CIF IN ( SELECT CIF FROM fact_factura
                            WHERE  Destino_Id =:id_departamento)";
            
            $this->db->query($sql);
            
            $this->db->bind(':id_departamento',$id_departamento);    
        }

   
        
        return $this->db->registros();
    }


 /**
     * FUNCIÓN getProveedores obtinene los proveedores de un departamento dado
     * si el departamento pasado por parámetro es <=0 muestra todos los proveedores
     */
    
     public function getProveedoresConValoraciones($id_departamento, $numPag, $cif, $nombre){
    
        $start=NUM_ITEMS_BY_PAGE*($numPag-1);
        if($id_departamento<=0)
        {
            $sql="SELECT p.CIF, p.Nombre,
            AVG(f.Item1) AS MItem1, 
            AVG(f.Item2) AS MItem2, 
            AVG(f.Item3) AS MItem3, 
            AVG(f.Item4) AS MItem4, 
            AVG(CASE WHEN f.Ffactura >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR) THEN f.Item1 END) AS MItem1_UltimoAnio,
            AVG(CASE WHEN f.Ffactura >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR) THEN f.Item2 END) AS MItem2_UltimoAnio,
            AVG(CASE WHEN f.Ffactura >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR) THEN f.Item3 END) AS MItem3_UltimoAnio,
            AVG(CASE WHEN f.Ffactura >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR) THEN f.Item4 END) AS MItem4_UltimoAnio
            FROM fact_proveedor p
            LEFT JOIN fact_factura f ON f.CIF = p.CIF
            WHERE p.Nombre LIKE '%$nombre%' 
            AND p.CIF LIKE '%$cif%' 
            GROUP BY p.CIF, p.Nombre
            LIMIT $start , ".NUM_ITEMS_BY_PAGE." ;";
            
           $this->db->query($sql);
        }    
        else
        {
            
            $sql="SELECT p.CIF, p.Nombre,
            AVG(f.Item1) AS MItem1, 
            AVG(f.Item2) AS MItem2, 
            AVG(f.Item3) AS MItem3, 
            AVG(f.Item4) AS MItem4, 
            AVG(CASE WHEN f.Ffactura >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR) THEN f.Item1 END) AS MItem1_UltimoAnio,
            AVG(CASE WHEN f.Ffactura >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR) THEN f.Item2 END) AS MItem2_UltimoAnio,
            AVG(CASE WHEN f.Ffactura >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR) THEN f.Item3 END) AS MItem3_UltimoAnio,
            AVG(CASE WHEN f.Ffactura >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR) THEN f.Item4 END) AS MItem4_UltimoAnio
            FROM fact_proveedor p
            LEFT JOIN fact_factura f ON f.CIF = p.CIF
            WHERE  Destino_Id =:id_departamento 
            AND p.Nombre LIKE '%$nombre%' 
            AND p.CIF LIKE '%$cif%' 
            GROUP BY p.CIF, p.Nombre
            LIMIT $start , ".NUM_ITEMS_BY_PAGE.";";
            
            $this->db->query($sql);
            
            $this->db->bind(':id_departamento',$id_departamento);    
        }

   
        
        return $this->db->registros();
    }







    public function calculaMediaItems($cif){
        $sql="Select avg(Item1) as MItem1, avg(Item2) as MItem2 , avg(Item3) as MItem3, avg(Item4) as MItem4 from fact_factura where CIF=:cif";
        $this->db->query($sql);
        $this->db->bind(':cif',$cif);   
        return $this->db->registro();

    }


    public function calculaMediaItemsUltimoAnio($cif){
        $sql="Select avg(Item1) as MItem1, avg(Item2) as MItem2 , avg(Item3) as MItem3, avg(Item4) as MItem4 from fact_factura where CIF=:cif";
        
        $this->db->query($sql);
        $this->db->bind(':cif',$cif);   
        return $this->db->registro();

    }


    /**
     * FUNCIÓN getProveedor obtinene los dadtos de un proveedor dado el cif    */
    public function getProveedor($cif)
    {
        $this->db->query("SELECT * FROM fact_proveedor WHERE CIF=:cif");
        $this->db->bind(':cif',$cif);   
        return $this->db->registro();
    }

    public function getFactura($nAsiento){
        $sql="SELECT * FROM fact_factura f , fact_proveedor p, fact_destino d  WHERE p.CIF=f.CIF and f.Destino_Id = d.Destino_Id and N_Asiento = :N_Asiento";
        $this->db->query($sql);
        $this->db->bind(':N_Asiento',$nAsiento);
        return $this->db->registro();
      
    }


    /**
     * Función getFacturas obtiene todas las facturas de un departamento dado entre dos fechas
     * Si no se pasan parámetros se obtienen todas.
     */
    public function getFacturas($id_destino, $proveedor, $fecha_ini, $fecha_fin){
        
        //$sql="SELECT * FROM fact_factura WHERE Destino_Id =:id_destino";
        
        $sql="SELECT N_Asiento, NFactura, Importe, fact_factura.CIF AS CIF, Ffactura, Faprobacion, Destino_Id, fact_proveedor.Nombre AS Nombre
        FROM fact_factura, fact_proveedor
        WHERE fact_factura.CIF=fact_proveedor.CIF
        AND Destino_Id =:id_destino";
        
        
        /*escribimos la consulta en función de los parámetros pasados*/
        //if($id_destino>0) $sql.=" AND Destino_Id =:id_destino";
        if($fecha_ini!="") $sql.=" AND Ffactura>=:fecha_ini";
        if($fecha_fin!="") $sql.=" AND Ffactura<=:fecha_fin";
        if($proveedor!="") $sql.=" AND Nombre LIKE '%:proveedor%'";
        $sql.=" ORDER BY Ffactura DESC";
        
        $this->db->query($sql);
        
        /**vinculamos los parámetros */
        $this->db->bind(':id_destino',$id_destino);
        if($fecha_ini!="") $this->db->bind(':fecha_ini',$fecha_ini);
        if($fecha_fin!="") $this->db->bind(':fecha_fin',$fecha_fin);
        if($proveedor!="") $this->db->bind(':Nombre',$proveedor);

       return $this->db->registros();
    }




    /**
     * función para añadir factura
     * le pasamos el vector datos
     */
    public function addFactura($datos)
    {
                                     
                    $sql="insert into fact_factura (NFactura,importe, CIF,Ffactura, Faprobacion, Destino_Id,Responsable,Inventariable,Item1,Item2,Item3,Item4) ";
                    $sql.=" values(:NFactura,:importe, :CIF,:Ffactura,:Faprobacion,:Destino_Id,:responsable,:inventariable,:Item1,:Item2,:Item3,:Item4) ";
                    
                    $this->db->query($sql);
                    //vinculamos los valores
                    $this->db->bind(':NFactura',trim($datos['confirmarFactura']['NFactura']));
                    $this->db->bind(':importe',trim($datos['confirmarFactura']['importe']));
                    $this->db->bind(':CIF',trim($datos['confirmarFactura']['CIF']));
                    $this->db->bind(':Ffactura',trim($datos['confirmarFactura']['Ffactura']));
                    $this->db->bind(':Faprobacion',trim($datos['confirmarFactura']['Fconformidad']));
                    $this->db->bind(':Destino_Id',trim($datos['persistencia']['idDestinoSeleccionado']));
                    $this->db->bind(':responsable',trim($datos['confirmarFactura']['responsable']));
                    $this->db->bind(':inventariable',trim($datos['confirmarFactura']['inventariable']));
                    $this->db->bind(':Item1',trim($datos['confirmarFactura']['Item1']));
                    $this->db->bind(':Item2',trim($datos['confirmarFactura']['Item2']));
                    $this->db->bind(':Item3',trim($datos['confirmarFactura']['Item3']));
                    $this->db->bind(':Item4',trim($datos['confirmarFactura']['Item4']));

                        
                    
                    try {
                        $nAsiento= $this->db->executeLastId();
                            
                        return $nAsiento;
                    } catch (Exception $e) {
                        return false;
                        
                        // $this->db->query_error($e);
                    }
                    
        }


    /**
     * función para añadir proveedor
     * le pasamos el vector datos
     */
        public function addProveedor($datos)
        {
                        print_r($datos);
                                                
                        $sql="insert into fact_proveedor (CIF, Nombre, Alias, Telefono, Direccion, CP, Localidad, Provincia ,Pais,Item1,Item2,Item3,Item4, Penalizacion, Asiento, Externo) ";
                        $sql.=" values(:CIF,:Nombre,:Alias,:Telefono,:Direccion,:CP, :Localidad, :Provincia,:Pais, :Item1, :Item2, :Item3, :Item4, :Penalizacion, :Asiento, :Externo) ";
                        
                        $this->db->query($sql);
                        //vinculamos los valores
                        $this->db->bind(':CIF',trim($datos['CIF']));
                        $this->db->bind(':Nombre',trim($datos['Nombre']));
                        $this->db->bind(':Alias',trim($datos['Alias']));

                        $this->db->bind(':Telefono',trim($datos['Telefono']));
                        $this->db->bind(':Direccion',trim($datos['Direccion']));
                        $this->db->bind(':CP',trim($datos['CP']));
                        $this->db->bind(':Localidad',trim($datos['Localidad']));
                        $this->db->bind(':Provincia',trim($datos['Provincia']));
                        $this->db->bind(':Pais',trim($datos['Pais']));
                        $this->db->bind(':Item1',5);
                        $this->db->bind(':Item2',5);
                        $this->db->bind(':Item3',5);
                        $this->db->bind(':Item4',5);
                        $this->db->bind(':Penalizacion',"");
                        $this->db->bind(':Asiento',NULL);
                        $this->db->bind(':Externo',trim($datos['Externo']));

                        
                        
                        try {
                            $nAsiento= $this->db->executeLastId();
                            echo "Estoy en el try;";   
                            return $nAsiento;
                        } catch (Exception $e) {
                            echo $e;
                            return false;
                            
                            // $this->db->query_error($e);
                        }
                        
            }
    
    
        






    public function getAsesores(){

        $this->db->query("SELECT * FROM cpifp_profesor");

        $profesores = $this->db->registros();

        foreach($profesores as $profesor){
            $profesor->roles = $this->getRolesProfesor($profesor->id_profesor);
        }

        $asesores = [];
        foreach($profesores as $profesor){
            $rolProfesor = obtenerRol($profesor->roles);
            if ($rolProfesor == 200){
                $asesores[] = $profesor;
            }
        }
        return $asesores;
    }



    /************************ EDITAR PROVEEDOR ****************************/

    public function editarProveedor($cif, $datos) {
        $this->db->query("UPDATE fact_proveedor SET
            Nombre    = :Nombre,
            Alias     = :Alias,
            Telefono  = :Telefono,
            Direccion = :Direccion,
            CP        = :CP,
            Localidad = :Localidad,
            Provincia = :Provincia,
            Pais      = :Pais,
            Externo   = :Externo
            WHERE CIF = :CIF");
        $this->db->bind(':Nombre',    trim($datos['Nombre']));
        $this->db->bind(':Alias',     trim($datos['Alias']));
        $this->db->bind(':Telefono',  trim($datos['Telefono']));
        $this->db->bind(':Direccion', trim($datos['Direccion']));
        $this->db->bind(':CP',        trim($datos['CP']));
        $this->db->bind(':Localidad', trim($datos['Localidad']));
        $this->db->bind(':Provincia', trim($datos['Provincia']));
        $this->db->bind(':Pais',      trim($datos['Pais']));
        $this->db->bind(':Externo',   trim($datos['Externo']));
        $this->db->bind(':CIF',       $cif);
        return $this->db->execute();
    }


    /************************ BORRAR PROVEEDOR ****************************/

    public function borrarProveedor($cif) {
        $this->db->query("DELETE FROM fact_proveedor WHERE CIF = :CIF");
        $this->db->bind(':CIF', $cif);
        return $this->db->execute();
    }

    /************************ AÑADIR ABONO ****************************/

    public function addAbono($datos) {
        $sql  = "INSERT INTO fact_abono (NAbono, NFactura, Importe, CIF, Faprobacion, Destino_Id, Responsable, Motivos) ";
        $sql .= "VALUES (:NAbono, :NFactura, :Importe, :CIF, :Faprobacion, :Destino_Id, :Responsable, :Motivos)";
        $this->db->query($sql);
        $this->db->bind(':NAbono',      trim($datos['NAbono']));
        $this->db->bind(':NFactura',    trim($datos['NFactura']));
        $this->db->bind(':Importe',     trim($datos['Importe']));
        $this->db->bind(':CIF',         trim($datos['CIF']));
        $this->db->bind(':Faprobacion', trim($datos['Faprobacion']));
        $this->db->bind(':Destino_Id',  trim($datos['Destino_Id']));
        $this->db->bind(':Responsable', trim($datos['Responsable']));
        $this->db->bind(':Motivos',     trim($datos['Motivos']));
        try {
            return $this->db->executeLastId();
        } catch (Exception $e) {
            return false;
        }
    }


    /************************ OBTENER ABONO POR ID ****************************/

    public function getAbono($id) {
        $this->db->query("SELECT a.*, p.Nombre FROM fact_abono a
                          LEFT JOIN fact_proveedor p ON a.CIF = p.CIF
                          WHERE a.Id = :id");
        $this->db->bind(':id', $id);
        return $this->db->registro();
    }


    /************************ AÑADIR RETENCIÓN (NCF) ****************************/

    public function addRetencion($datos) {
        $sql  = "INSERT INTO fact_factura_ncf (NFactura, Importe, CIF, Faprobacion, Destino_Id, Responsable, Motivos) ";
        $sql .= "VALUES (:NFactura, :Importe, :CIF, :Faprobacion, :Destino_Id, :Responsable, :Motivos)";
        $this->db->query($sql);
        $this->db->bind(':NFactura',    trim($datos['NFactura']));
        $this->db->bind(':Importe',     trim($datos['Importe']));
        $this->db->bind(':CIF',         trim($datos['CIF']));
        $this->db->bind(':Faprobacion', trim($datos['Faprobacion']));
        $this->db->bind(':Destino_Id',  trim($datos['Destino_Id']));
        $this->db->bind(':Responsable', trim($datos['Responsable']));
        $this->db->bind(':Motivos',     trim($datos['Motivos']));
        try {
            return $this->db->executeLastId();
        } catch (Exception $e) {
            return false;
        }
    }


    /************************ OBTENER RETENCIÓN POR ID ****************************/

    public function getRetencion($id) {
        $this->db->query("SELECT n.*, p.Nombre FROM fact_factura_ncf n
                          LEFT JOIN fact_proveedor p ON n.CIF = p.CIF
                          WHERE n.Id = :id");
        $this->db->bind(':id', $id);
        return $this->db->registro();
    }


    /************************ FACTURAS PAGINADAS ****************************/

    private function _sqlFacturas($id_destino, $proveedor, $fecha_ini, $fecha_fin) {
        $sql = "SELECT f.N_Asiento, f.NFactura, f.Importe, f.CIF, f.Ffactura,
                       f.Faprobacion, f.Destino_Id, f.Responsable, f.Inventariable,
                       p.Nombre, d.Depart_Servicio
                FROM fact_factura f
                INNER JOIN fact_proveedor p ON f.CIF = p.CIF
                INNER JOIN fact_destino   d ON f.Destino_Id = d.Destino_Id
                WHERE 1=1";
        if ($id_destino > 0) $sql .= " AND f.Destino_Id = :id_destino";
        if ($fecha_ini  !== '') $sql .= " AND f.Ffactura >= :fecha_ini";
        if ($fecha_fin  !== '') $sql .= " AND f.Ffactura <= :fecha_fin";
        if ($proveedor  !== '') $sql .= " AND p.Nombre LIKE :proveedor";
        return $sql;
    }

    private function _bindFacturas($id_destino, $proveedor, $fecha_ini, $fecha_fin) {
        if ($id_destino > 0) $this->db->bind(':id_destino', $id_destino);
        if ($fecha_ini  !== '') $this->db->bind(':fecha_ini', $fecha_ini);
        if ($fecha_fin  !== '') $this->db->bind(':fecha_fin', $fecha_fin);
        if ($proveedor  !== '') $this->db->bind(':proveedor', '%' . $proveedor . '%');
    }

    public function getNumFacturas($id_destino, $proveedor, $fecha_ini, $fecha_fin) {
        $base = $this->_sqlFacturas($id_destino, $proveedor, $fecha_ini, $fecha_fin);
        $this->db->query("SELECT COUNT(*) AS total FROM ($base) AS sub");
        $this->_bindFacturas($id_destino, $proveedor, $fecha_ini, $fecha_fin);
        $row = $this->db->registro();
        return $row ? (int)$row->total : 0;
    }

    public function getFacturasPaginadas($id_destino, $proveedor, $fecha_ini, $fecha_fin, $pagina) {
        $offset = ($pagina - 1) * NUM_ITEMS_BY_PAGE;
        $sql = $this->_sqlFacturas($id_destino, $proveedor, $fecha_ini, $fecha_fin);
        $sql .= " ORDER BY f.Ffactura DESC LIMIT :limit OFFSET :offset";
        $this->db->query($sql);
        $this->_bindFacturas($id_destino, $proveedor, $fecha_ini, $fecha_fin);
        $this->db->bind(':limit',  NUM_ITEMS_BY_PAGE);
        $this->db->bind(':offset', $offset);
        return $this->db->registros();
    }

    public function getFacturasTodas($id_destino, $proveedor, $fecha_ini, $fecha_fin) {
        $sql = $this->_sqlFacturas($id_destino, $proveedor, $fecha_ini, $fecha_fin);
        $sql .= " ORDER BY f.Ffactura DESC";
        $this->db->query($sql);
        $this->_bindFacturas($id_destino, $proveedor, $fecha_ini, $fecha_fin);
        return $this->db->registros();
    }

// ════════════════════════════════════════════════════════
    // INVENTARIO — tablas auxiliares
    // ════════════════════════════════════════════════════════

    public function invGetCategorias(): array {
        $this->db->query("SELECT CodCat, denominacion FROM fact_inv_categoria ORDER BY denominacion");
        return $this->db->registros();
    }

    public function invGetArticulosPorCategoria(int $codCat): array {
        $this->db->query("SELECT CodArt, nombre FROM fact_inv_articulo WHERE CodCat = :c ORDER BY nombre");
        $this->db->bind(':c', $codCat);
        return $this->db->registros();
    }

    public function invGetUbicaciones(): array {
        $this->db->query(
            "SELECT d.id_destino, d.NomLocal,
                    COALESCE(fd.Depart_Servicio,'') AS siglas_dep
             FROM fact_inv_destino d
             LEFT JOIN fact_destino fd ON fd.Destino_Id = d.Depart_Servicio
             WHERE d.Activo = 'S'
             ORDER BY d.NomLocal"
        );
        return $this->db->registros();
    }

    public function invAddCategoria(string $denominacion): void {
        $this->db->query("INSERT INTO fact_inv_categoria (denominacion) VALUES (:d)");
        $this->db->bind(':d', trim($denominacion));
        $this->db->execute();
    }

    public function invAddArticulo(int $codCat, string $nombre): void {
        $this->db->query("INSERT INTO fact_inv_articulo (CodCat, nombre) VALUES (:c, :n)");
        $this->db->bind(':c', $codCat);
        $this->db->bind(':n', trim($nombre));
        $this->db->execute();
    }

    public function invAddUbicacion(string $nomLocal, ?int $depart): void {
        $this->db->query(
            "INSERT INTO fact_inv_destino (NomLocal, Depart_Servicio, Activo) VALUES (:n, :d, 'S')"
        );
        $this->db->bind(':n', trim($nomLocal));
        $this->db->bind(':d', $depart);
        $this->db->execute();
    }

    // ════════════════════════════════════════════════════════
    // INVENTARIO — facturas inventariables
    // ════════════════════════════════════════════════════════

    public function invGetFacturasInventariables(): array {
        $this->db->query(
            "SELECT f.N_Asiento, f.NFactura, f.CIF,
                    COALESCE(p.Nombre, f.CIF) AS nombre_proveedor,
                    f.Importe, f.Faprobacion
             FROM fact_factura f
             LEFT JOIN fact_proveedor p ON p.CIF = f.CIF
             WHERE f.Inventariable = 1
               AND f.Faprobacion IS NOT NULL
             ORDER BY f.N_Asiento DESC"
        );
        return $this->db->registros();
    }

    public function invGetFacturaPorAsiento(int $asiento): ?object {
        $this->db->query(
            "SELECT f.N_Asiento, f.NFactura, f.CIF,
                    COALESCE(p.Nombre, f.CIF) AS nombre_proveedor,
                    f.Importe, f.Faprobacion
             FROM fact_factura f
             LEFT JOIN fact_proveedor p ON p.CIF = f.CIF
             WHERE f.N_Asiento = :a"
        );
        $this->db->bind(':a', $asiento);
        $r = $this->db->registros();
        return $r[0] ?? null;
    }

    // ════════════════════════════════════════════════════════
    // INVENTARIO — alta
    // ════════════════════════════════════════════════════════

    public function invAltaCabecera(array $d): int {
        $this->db->query(
            "INSERT INTO fact_inventario
                (N_Asiento, NFactura, CIF, Procedencia, Observaciones, Fecha_Alta)
             VALUES
                (:asiento, :nfactura, :cif, :proc, :obs, :fecha)"
        );
        $this->db->bind(':asiento',  $d['N_Asiento']     ?? null);
        $this->db->bind(':nfactura', $d['NFactura']      ?? null);
        $this->db->bind(':cif',      $d['CIF']           ?? null);
        $this->db->bind(':proc',     $d['Procedencia']   ?? null);
        $this->db->bind(':obs',      $d['Observaciones'] ?? null);
        $this->db->bind(':fecha',    date('Y-m-d'));
        return (int)$this->db->executeLastId();
    }

    public function invAltaDetalle(array $d): void {
        $this->db->query(
            "INSERT INTO fact_inv_detalle
                (NEntrada, CodCat, CodArt, Unidades, Individual,
                 Dep_Responsable, Local_Ini, Descripcion)
             VALUES
                (:ne, :cat, :art, :uni, :ind, :dep, :loc, :desc)"
        );
        $this->db->bind(':ne',   $d['NEntrada']);
        $this->db->bind(':cat',  $d['CodCat']);
        $this->db->bind(':art',  $d['CodArt']);
        $this->db->bind(':uni',  max(1, (int)$d['Unidades']));
        $this->db->bind(':ind',  ($d['Individual'] === 'I') ? 'I' : 'B');
        $this->db->bind(':dep',  $d['Dep_Responsable'] ?? null);
        $this->db->bind(':loc',  $d['Local_Ini']       ?? null);
        $this->db->bind(':desc', $d['Descripcion']     ?? null);
        $this->db->execute();
    }

    // ════════════════════════════════════════════════════════
    // INVENTARIO — consulta y paginación
    // ════════════════════════════════════════════════════════

    private function invBuildWhere(array $f): array {
        $conds  = [];
        $params = [];

        if (!empty($f['dep'])) {
            $conds[]        = 'd.Dep_Responsable = :dep';
            $params[':dep'] = (int)$f['dep'];
        }
        if (!empty($f['cat'])) {
            $conds[]        = 'd.CodCat = :cat';
            $params[':cat'] = (int)$f['cat'];
        }
        if (!empty($f['buscar'])) {
            $conds[]       = '(a.nombre LIKE :bq OR d.Descripcion LIKE :bq OR c.denominacion LIKE :bq OR i.NFactura LIKE :bq)';
            $params[':bq'] = '%' . $f['buscar'] . '%';
        }

        if (isset($f['baja']) && $f['baja'] === '1') {
            $conds[] = 'd.Baja = 1';
        } elseif (isset($f['baja']) && $f['baja'] === 'todos') {
            // sin filtro de estado
        } else {
            $conds[] = 'd.Baja = 0';
        }

        $where = $conds ? 'WHERE ' . implode(' AND ', $conds) : '';
        return [$where, $params];
    }

    private function invBaseSelect(): string {
        return "FROM fact_inv_detalle d
                JOIN  fact_inventario      i   ON i.NEntrada    = d.NEntrada
                LEFT JOIN fact_inv_categoria   c   ON c.CodCat      = d.CodCat
                LEFT JOIN fact_inv_articulo    a   ON a.CodArt      = d.CodArt
                LEFT JOIN fact_inv_destino     loc ON loc.id_destino = d.Local_Ini
                LEFT JOIN fact_destino         dep ON dep.Destino_Id = d.Dep_Responsable
                LEFT JOIN fact_proveedor       p   ON p.CIF          = i.CIF";
    }

    public function invCount(array $f): int {
        [$where, $params] = $this->invBuildWhere($f);
        $this->db->query("SELECT COUNT(*) AS total " . $this->invBaseSelect() . " $where");
        foreach ($params as $k => $v) $this->db->bind($k, $v);
        $row = $this->db->registro();
        return $row ? (int)$row->total : 0;
    }

    public function invGetPaginado(array $f, int $pag, int $porPag): array {
        [$where, $params] = $this->invBuildWhere($f);
        $offset = ($pag - 1) * $porPag;
        $sql = "SELECT d.id, d.NEntrada,
                       d.CodCat, d.CodArt, d.Unidades, d.Individual,
                       d.Dep_Responsable, d.Local_Ini,
                       d.Descripcion, d.Baja, d.Fecha_Baja, d.Motivo_Baja,
                       i.N_Asiento, i.NFactura, i.CIF,
                       i.Procedencia, i.Observaciones, i.Fecha_Alta,
                       c.denominacion  AS NombreCat,
                       a.nombre        AS NombreArt,
                       loc.NomLocal,
                       dep.Depart_Servicio  AS NombreDep,
                       COALESCE(p.Nombre, i.Procedencia, i.CIF) AS NombreOrigen
                " . $this->invBaseSelect() . "
                $where
                ORDER BY i.Fecha_Alta DESC, d.NEntrada DESC, d.id
                LIMIT :lim OFFSET :off";

        $this->db->query($sql);
        foreach ($params as $k => $v) $this->db->bind($k, $v);
        $this->db->bind(':lim', $porPag,  PDO::PARAM_INT);
        $this->db->bind(':off', $offset,  PDO::PARAM_INT);
        return $this->db->registros();
    }

    // ════════════════════════════════════════════════════════
    // INVENTARIO — detalle individual
    // ════════════════════════════════════════════════════════

    public function invGetDetalleById(int $id): ?object {
        $sql = "SELECT d.*,
                       i.N_Asiento, i.NFactura, i.CIF, i.Procedencia,
                       i.Observaciones, i.Fecha_Alta,
                       c.denominacion AS NombreCat,
                       a.nombre       AS NombreArt,
                       loc.NomLocal,
                       dep.Depart_Servicio AS NombreDep,
                       COALESCE(p.Nombre, i.Procedencia, i.CIF) AS NombreOrigen
                " . $this->invBaseSelect() . "
                WHERE d.id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->registro();
    }

    // ════════════════════════════════════════════════════════
    // INVENTARIO — modificación
    // ════════════════════════════════════════════════════════

    public function invModificarDetalle(array $d): void {
        $this->db->query(
            "UPDATE fact_inv_detalle
             SET CodCat=:cat, CodArt=:art, Unidades=:uni, Individual=:ind,
                 Dep_Responsable=:dep, Local_Ini=:loc, Descripcion=:desc
             WHERE id=:id"
        );
        $this->db->bind(':cat',  $d['CodCat']);
        $this->db->bind(':art',  $d['CodArt']);
        $this->db->bind(':uni',  max(1, (int)$d['Unidades']));
        $this->db->bind(':ind',  ($d['Individual'] === 'I') ? 'I' : 'B');
        $this->db->bind(':dep',  $d['Dep_Responsable'] ?? null);
        $this->db->bind(':loc',  $d['Local_Ini']       ?? null);
        $this->db->bind(':desc', $d['Descripcion']     ?? null);
        $this->db->bind(':id',   (int)$d['id']);
        $this->db->execute();
    }

    public function invModificarCabecera(int $nEntrada, string $obs): void {
        $this->db->query(
            "UPDATE fact_inventario SET Observaciones = :obs WHERE NEntrada = :ne"
        );
        $this->db->bind(':obs', $obs);
        $this->db->bind(':ne',  $nEntrada);
        $this->db->execute();
    }

    // ════════════════════════════════════════════════════════
    // INVENTARIO — baja / reactivación
    // ════════════════════════════════════════════════════════

    public function invDarDeBaja(int $id, string $motivo): bool {
        $this->db->query(
            "UPDATE fact_inv_detalle
             SET Baja=1, Fecha_Baja=:fecha, Motivo_Baja=:mot
             WHERE id=:id AND Baja=0"
        );
        $this->db->bind(':fecha', date('Y-m-d'));
        $this->db->bind(':mot',   $motivo ?: null);
        $this->db->bind(':id',    $id);
        return $this->db->rowCount() > 0;
    }

    public function invReactivar(int $id): bool {
        $this->db->query(
            "UPDATE fact_inv_detalle
             SET Baja=0, Fecha_Baja=NULL, Motivo_Baja=NULL
             WHERE id=:id"
        );
        $this->db->bind(':id', $id);
        return $this->db->rowCount() > 0;
    }

   

}


