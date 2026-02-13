<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boleta de Venta #
        <?= str_pad($reserva->id, 6, '0', STR_PAD_LEFT) ?>
    </title>
    <style>
        /* RESET BÁSICO */
        * {
            box-sizing: border-box;
            /* Vital para que el padding no aumente el ancho */
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            background: #f3f4f6;
            padding: 20px;
            margin: 0;
        }

        .ticket {
            max-width: 300px;
            /* Ancho estándar de ticketera (80mm aprox) */
            margin: 0 auto;
            background: white;
            padding: 10px 15px;
            /* Espacio interno seguro */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* ESTILOS DE TEXTO */
        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
        }

        .header p {
            margin: 2px 0;
            font-size: 11px;
        }

        .info-group {
            margin-bottom: 5px;
            font-size: 12px;
        }

        .info-group strong {
            display: inline-block;
            width: 65px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-top: 10px;
        }

        th {
            text-align: left;
            border-bottom: 1px solid #000;
            padding: 2px 0;
        }

        td {
            padding: 4px 0;
            border-bottom: 1px dashed #ccc;
        }

        .text-right {
            text-align: right;
        }

        .total-section {
            margin-top: 15px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin-bottom: 2px;
        }

        .total-final {
            font-size: 16px;
            font-weight: bold;
            margin-top: 5px;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 10px;
            border-top: 1px dashed #000;
            padding-top: 10px;
        }

        .btn-print {
            display: block;
            width: 100%;
            background: #333;
            color: white;
            text-align: center;
            padding: 10px;
            text-decoration: none;
            margin-top: 20px;
            border-radius: 4px;
            font-size: 14px;
        }

        /* --- CONFIGURACIÓN DE IMPRESIÓN (LA CLAVE) --- */
        @media print {
            @page {
                margin: 0;
                /* Elimina encabezados/pies de página del navegador */
                size: auto;
                /* Se adapta al papel seleccionado */
            }

            body {
                background: white;
                padding: 0;
                margin: 0;
            }

            .ticket {
                width: 100%;
                max-width: 100%;
                /* Ocupa todo el ancho del papel */
                box-shadow: none;
                border: none;
                margin: 0;
                padding: 5px;
                /* Pequeño margen interno para que no toque el borde físico */
            }

            .btn-print,
            .back-link {
                display: none !important;
                /* Ocultar botones */
            }
        }
    </style>
</head>

<body>

    <div class="ticket">
        <div class="header">
            <h1>MASUNO</h1>
            <p>Estilistas Profesionales</p>
            <p>Av. Principal 123, Lima - Perú</p>
            <p>RUC: 20123456789</p>
        </div>

        <div class="info">
            <div class="info-group">
                <strong>Ticket:</strong> #
                <?= str_pad($reserva->id, 6, '0', STR_PAD_LEFT) ?>
            </div>
            <div class="info-group">
                <strong>Fecha:</strong>
                <?= date('d/m/Y H:i', strtotime($reserva->fecha_cita . ' ' . $reserva->hora_cita)) ?>
            </div>
            <div class="info-group">
                <strong>Cliente:</strong>
                <?= htmlspecialchars($reserva->cliente_nombre ?? 'Cliente General') ?>
            </div>
            <div class="info-group">
                <strong>Estilista:</strong>
                <?= htmlspecialchars($reserva->estilista_nombre ?? 'Sin asignar') ?>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th class="text-right">Cant.</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php $granTotal = 0; ?>

                <?php if (!empty($servicios)): ?>
                    <?php foreach ($servicios as $svc): ?>
                        <tr>
                            <td>[S]
                                <?= htmlspecialchars($svc->nombre) ?>
                            </td>
                            <td class="text-right">1</td>
                            <td class="text-right">S/.
                                <?= number_format($svc->precio, 2) ?>
                            </td>
                        </tr>
                        <?php $granTotal += $svc->precio; ?>
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php if (!empty($productos)): ?>
                    <?php foreach ($productos as $prod): ?>
                        <?php
                        // Calcular subtotal de línea
                        $subtotal = $prod->cantidad * $prod->precio_unitario;
                        $granTotal += $subtotal;
                        ?>
                        <tr>
                            <td>[P]
                                <?= htmlspecialchars($prod->nombre) ?>
                            </td>
                            <td class="text-right">
                                <?= $prod->cantidad ?>
                            </td>
                            <td class="text-right">S/.
                                <?= number_format($subtotal, 2) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-row total-final">
                <span>TOTAL A PAGAR</span>
                <span>S/.
                    <?= number_format($granTotal, 2) ?>
                </span>
            </div>
            <div class="total-row" style="font-size: 11px; color: #666; margin-top:5px;">
                <span>Método de pago:</span>
                <span>Efectivo / Tarjeta</span>
            </div>
        </div>

        <div class="footer">
            <p>¡Gracias por su preferencia!</p>
            <p>Este documento no es un comprobante fiscal oficial.</p>
            <p>Sistema desarrollado por Masuno Devs</p>
        </div>

        <a href="#" onclick="window.print(); return false;" class="btn-print">IMPRIMIR / GUARDAR PDF</a>
        <a href="<?= BASE_URL ?>/index.php?url=Reservation/index"
            style="display:block; text-align:center; margin-top:10px; text-decoration:none; color:#666; font-family:sans-serif; font-size:12px;">&larr;
            Volver al Sistema</a>
    </div>

    <script>
        // Opcional: Imprimir automáticamente al abrir
        // window.onload = function() { window.print(); }
    </script>
</body>

</html>