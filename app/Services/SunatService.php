<?php

namespace App\Services;

use Greenter\See;
use Greenter\Ws\Services\SunatEndpoints;
use Greenter\Model\Company\Company;
use Greenter\Model\Company\Address;
use Greenter\Model\Client\Client;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Model\Sale\Legend;

class SunatService
{
    private $see;
    private $empresa;

    public function __construct()
    {
        $this->see = new See();

        // 1. Entorno BETA de SUNAT
        $this->see->setService(SunatEndpoints::FE_BETA);

        // 2. Credenciales SOL de Prueba (RUC + Usuario, Clave)
        $this->see->setClaveSOL('20000000001', 'MODDATOS', 'moddatos');

        // 3. Cargar el Certificado que acabamos de descargar
        $rutaCertificado = __DIR__ . '/../../certs/certificado.pem';
        if (file_exists($rutaCertificado)) {
            $this->see->setCertificate(file_get_contents($rutaCertificado));
        } else {
            throw new \Exception("No se encontró el certificado en: " . $rutaCertificado);
        }

        // 4. Configurar los Datos de TU EMPRESA (Emisor)
        // SUNAT exige que estos datos vayan dentro de cada XML
        $this->configurarEmpresa();
    }

    private function configurarEmpresa()
    {
        $address = (new Address())
            ->setUbigueo('150101') // Código de Lima, cambia según tu región
            ->setDepartamento('LIMA')
            ->setProvincia('LIMA')
            ->setDistrito('LIMA')
            ->setUrbanizacion('-')
            ->setDireccion('Av. Principal 123'); // Tu dirección real

        $this->empresa = (new Company())
            ->setRuc('20000000001') // RUC DE PRUEBA (Fijo)
            ->setRazonSocial('MASUNO ESTILISTAS S.A.C.')
            ->setNombreComercial('Masuno')
            ->setAddress($address);
    }

    /**
     * Getter para obtener la empresa al armar los comprobantes
     */
    public function getEmpresa()
    {
        return $this->empresa;
    }

    /**
     * Getter para obtener la instancia de SEE
     */
    public function getSee()
    {
        return $this->see;
    }

    public function emitirBoleta($clienteInfo, $servicios, $productos, $correlativo)
    {
        // 1. EL CLIENTE (Receptor)
        // Si no tiene DNI, SUNAT permite usar '00000000' en boletas menores a 700 soles.
        $dni = !empty($clienteInfo['dni']) ? $clienteInfo['dni'] : '00000000';
        $nombre = !empty($clienteInfo['nombre']) ? $clienteInfo['nombre'] : 'CLIENTE VARIOS';

        $client = (new Client())
            ->setTipoDoc('1') // '1' es DNI
            ->setNumDoc($dni)
            ->setRznSocial($nombre);

        // 2. EL COMPROBANTE (Cabecera)
        $invoice = (new Invoice())
            ->setUblVersion('2.1')
            ->setTipoOperacion('0101') // 0101 = Venta Interna
            ->setTipoDoc('03') // 03 = Boleta Electrónica
            ->setSerie('B001') // Serie de tu boleta
            ->setCorrelativo((string) $correlativo) // Ej: 1, 2, 3...
            ->setFechaEmision(new \DateTime())
            ->setTipoMoneda('PEN') // Soles
            ->setCompany($this->empresa) // La empresa que configuramos antes
            ->setClient($client);

        // 3. LOS DETALLES (Servicios y Productos)
        $items = [];
        $totalGravada = 0; // Total sin IGV
        $totalIgv = 0;     // Solo el impuesto

        // --- A. Procesar Servicios ---
        foreach ($servicios as $serv) {
            $precioConIgv = (float) $serv['precio']; // Ej: 30.00
            $valorSinIgv = round($precioConIgv / 1.18, 5); // Ej: 25.42372
            $igvItem = $precioConIgv - round($valorSinIgv, 2); // Ej: 4.58

            $item = (new SaleDetail())
                ->setCodProducto('S' . $serv['id'])
                ->setUnidad('ZZ') // 'ZZ' es el código estándar internacional para "Servicios"
                ->setCantidad(1)
                ->setDescripcion($serv['nombre'])
                ->setMtoBaseIgv(round($valorSinIgv, 2))
                ->setPorcentajeIgv(18.00) // 18% IGV Perú
                ->setIgv($igvItem)
                ->setTipAfeIgv('10') // '10' = Gravado - Operación Onerosa
                ->setTotalImpuestos($igvItem)
                ->setMtoValorVenta(round($valorSinIgv, 2))
                ->setMtoValorUnitario($valorSinIgv)
                ->setMtoPrecioUnitario($precioConIgv);

            $items[] = $item;
            $totalGravada += round($valorSinIgv, 2);
            $totalIgv += $igvItem;
        }

        // --- B. Procesar Productos ---
        foreach ($productos as $prod) {
            $cantidad = (int) $prod['cantidad'];
            $precioUnitarioConIgv = (float) $prod['precio'];

            $precioTotalItem = $precioUnitarioConIgv * $cantidad;
            $valorTotalSinIgv = round($precioTotalItem / 1.18, 5);
            $valorUnitarioSinIgv = round($precioUnitarioConIgv / 1.18, 5);
            $igvItem = $precioTotalItem - round($valorTotalSinIgv, 2);

            $item = (new SaleDetail())
                ->setCodProducto('P' . $prod['id'])
                ->setUnidad('NIU') // 'NIU' es el código para "Bienes/Productos"
                ->setCantidad($cantidad)
                ->setDescripcion($prod['nombre'])
                ->setMtoBaseIgv(round($valorTotalSinIgv, 2))
                ->setPorcentajeIgv(18.00)
                ->setIgv($igvItem)
                ->setTipAfeIgv('10')
                ->setTotalImpuestos($igvItem)
                ->setMtoValorVenta(round($valorTotalSinIgv, 2))
                ->setMtoValorUnitario($valorUnitarioSinIgv)
                ->setMtoPrecioUnitario($precioUnitarioConIgv);

            $items[] = $item;
            $totalGravada += round($valorTotalSinIgv, 2);
            $totalIgv += $igvItem;
        }

        // 4. TOTALES GENERALES
        $totalFinal = $totalGravada + $totalIgv;

        $invoice->setDetails($items)
            ->setMtoOperGravadas($totalGravada)
            ->setMtoIGV($totalIgv)
            ->setTotalImpuestos($totalIgv)
            ->setValorVenta($totalGravada)
            ->setSubTotal($totalFinal)
            ->setMtoImpVenta($totalFinal);

        // 5. LEYENDA (Obligatorio en Perú: Monto en letras)
        // SUNAT exige que el total vaya escrito. Aquí pongo un texto genérico simple.
        // Lo ideal a futuro es usar una librería como "Luecano\NumeroALetras".
        $legend = (new Legend())
            ->setCode('1000') // 1000 = Monto en letras
            ->setValue("SON " . number_format($totalFinal, 2, '.', '') . " SOLES");
        $invoice->setLegends([$legend]);

        // =======================================================
        // 6. ENVIAR A SUNAT Y GUARDAR RESPUESTA
        // =======================================================
        $resultado = $this->see->send($invoice);

        // Rutas físicas para guardar los archivos
        $nombreArchivo = $invoice->getName(); // Ej: 20000000001-03-B001-1
        $rutaXml = __DIR__ . '/../../storage/comprobantes/xml/' . $nombreArchivo . '.xml';

        // Guardamos el XML firmado (Obligatorio por ley)
        file_put_contents($rutaXml, $this->see->getFactory()->getLastXml());

        if (!$resultado->isSuccess()) {
            // SUNAT RECHAZÓ EL COMPROBANTE (Error en datos, DNI inválido, etc.)
            return [
                'exito' => false,
                'codigo_error' => $resultado->getError()->getCode(),
                'mensaje' => $resultado->getError()->getMessage(),
                'ruta_xml' => $rutaXml
            ];
        }

        // SUNAT ACEPTÓ EL COMPROBANTE
        $cdr = $resultado->getCdrResponse();

        // Guardar el CDR (Constancia de Recepción - El ZIP que devuelve SUNAT)
        $rutaCdr = __DIR__ . '/../../storage/comprobantes/cdr/R-' . $nombreArchivo . '.zip';
        file_put_contents($rutaCdr, $resultado->getCdrZip());

        // Extraer el Hash directamente del XML firmado (100% seguro)
        $xmlFirmado = file_get_contents($rutaXml);
        $hashCpe = '';
        if (preg_match('~<ds:DigestValue>(.*?)</ds:DigestValue>~', $xmlFirmado, $matches)) {
            $hashCpe = $matches[1];
        }

        return [
            'exito' => true,
            'estado_sunat' => 1, // 1 = Aceptado
            'codigo_respuesta' => $cdr->getCode(),
            'mensaje' => $cdr->getDescription(),
            'hash_cpe' => $hashCpe,
            'ruta_xml' => $rutaXml,
            'ruta_cdr' => $rutaCdr,
            'totales' => [
                'gravada' => $totalGravada,
                'igv' => $totalIgv,
                'total' => $totalFinal
            ]
        ];
    }
}