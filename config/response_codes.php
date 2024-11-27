<?php

return [
    "00" => [
        "error_msg_translation" => "Aprobado.",
        "error_msg" => "Approved.",
        "severity" => 0,
    ],
    "01" => [
        "error_msg_translation" => "La transacción no fue autorizada por el emisor por una razón desconocida. El titular de la tarjeta debe contactar a su emisor.",
        "error_msg" => "The transaction was not authorised by the Issuer for an unknown reason. The cardholder should contact their Issuer.",
        "severity" => "Hard",
    ],
    "02" => [
        "error_msg_translation" => "La transacción no fue autorizada por el emisor por una razón desconocida. El titular de la tarjeta debe contactar a su emisor.",
        "error_msg" => "The transaction was not authorised by the Issuer for an unknown reason. The cardholder should contact their Issuer.",
        "severity" => "Hard",
    ],
    "03" => [
        "error_msg_translation" => "La transacción se rechazó ya que no era válida o no habilitada para el comerciante.",
        "error_msg" => "The transaction was declined as it was invalid or not enabled for the merchant.",
        "severity" => "Hard",
    ],
    "04" => [
        "error_msg_translation" => "La transacción no fue autorizada por el emisor muy probablemente porque se sospechaba fraude.",
        "error_msg" => "The transaction was not authorised by the Issuer most likely because fraud was suspected.",
        "severity" => "Soft",
    ],
    "05" => [
        "error_msg_translation" => "La transacción no fue autorizada por el emisor por una razón desconocida. (No honrar)",
        "error_msg" => "The transaction was not authorised by the Issuer for an unknown reason. (Do not honor)",
        "severity" => "Soft",
    ],
    "06" => [
        "error_msg_translation" => "Se produjo un error desconocido al procesar la transacción. Volver a intentar la transacción puede dar lugar a una aprobación.",
        "error_msg" => "An unknown error occurred when processing the transaction. Retrying the transaction may result in an Approval.",
        "severity" => "Soft",
    ],
    "07" => [
        "error_msg_translation" => "La transacción no fue autorizada por el emisor muy probablemente porque se sospechaba fraude.",
        "error_msg" => "The transaction was not authorised by the Issuer most likely because fraud was suspected.",
        "severity" => "Hard",
    ],
    "12" => [
        "error_msg_translation" => "La transacción se rechazó ya que no era válida o no habilitada para el comerciante.",
        "error_msg" => "The transaction was declined as it was invalid or not enabled for the merchant.",
        "severity" => "Soft",
    ],
    "13" => [
        "error_msg_translation" => "La cantidad se establece en cero, es ilegible o excede la cantidad permitida. Asegúarese de que la cantidad sea mayor que cero y en formato adecuado.",
        "error_msg" => "The amount is set to zero, is unreadable or exceeds the allowable amount. Make sure the amount is greater than zero and in suitable format.",
        "severity" => "Hard",
    ],
    "14" => [
        "error_msg_translation" => "El número de tarjeta enviada no incluye el número adecuado de dígitos. El cliente debe intentar nuevamente usando el número de tarjeta correcto.",
        "error_msg" => "The submitted card number does not include the proper number of digits. The customer should try again using the correct card number.",
        "severity" => "Soft",
    ],
    "15" => [
        "error_msg_translation" => "Como no se ha encontrado el emisor relacionado con este número de tarjeta.",
        "error_msg" => "As Issuer related to this card number has not been found.",
        "severity" => "Hard",
    ],
    "25" => [
        "error_msg_translation" => "Terminal no válido",
        "error_msg" => "Invalid Terminal",
        "severity" => "",
    ],
    "28" => [
        "error_msg_translation" => "Por favor, intenta de nuevo",
        "error_msg" => "Please retry",
        "severity" => "",
    ],
    "30" => [
        "error_msg_translation" => "La transacción se rechazó, sin embargo, volver a intentar la transacción puede dar lugar a una aprobación.",
        "error msg" => "The transaction was declined, however, retrying the transaction may result in an Approval.",
        "severity" => "Hard",
    ],
    "33" => [
        "error_msg_translation " => "La tarjeta está expirada. El cliente deberá utilizar un método de pago diferente.",
        "error_msg " => "The card is expired.The customer will need to use a different payment method.",
        "severity " => "Hard ",
    ],
    "39" => [
        "error_msg_translation " => "La transacción se ha rechazado porque no existe dicha cuenta de tarjeta de crédito.",
        "error_msg " => "The transaction has been declined because no such credit card account exists.",
        "severity " => "Soft ",
    ],
    "41" => [
        "error_msg_translation " => "La tarjeta fue identificada como perdida por el emisor y no se procesará.",
        "error_msg " => "The card was identified as lost by the Issuer and will not be processed.",
        "severity " => "Hard ",
    ],
    "43" => [
        "error_msg_translation " => "La tarjeta fue identificada como robada por el emisor y no será procesada.",
        "error_msg " => "The card was identified as stolen by the Issuer and will not be processed.",
        "severity " => "Hard ",
    ],
    "51" => [
        "error_msg_translation " => "La cuenta de los titulares de tarjetas no tiene fondos suficientes para cubrir el monto de la transacción.Los intentos posteriores en una fecha posterior pueden tener éxito.",
        "error_msg " => "The cardholders account does not have sufficient funds to cover the transaction amount.Subsequent attempts at a later date may be successful.",
        "severity " => "Soft ",
    ],
    "54" => [
        "error_msg_translation " => "La tarjeta está expirada.El cliente deberá utilizar un método de pago diferente.",
        "error_msg " => "The card is expired.The customer will need to use a different payment method.",
        "severity " => "Soft ",
    ],
    "55" => [
        "error_msg_translation " => "El PIN o código ingresado es incorrecto.Inténtalo de nuevo con otro.",
        "error_msg " => "The PIN or Code entered is incorrect.Try again with another.",
        "severity " => "Soft ",
    ],
    "57" => [
        "error_msg_translation " => "El emisor no permite la transacción.",
        "error_msg " => "The transaction is not allowed by the Issuer.",
        "severity " => "Hard ",
    ],
    "58" => [
        "error_msg_translation " => "El terminal no se ha configurado para aceptar la transacción.",
        "error_msg " => "The terminal has not been configured to accept the transaction.",
        "severity " => "Soft ",
    ],
    "60" => [
        "error_msg_translation " => "Rechazado ",
        "error_msg " => "Declined ",
        "severity " => "",
    ],
    "61" => [
        "error_msg_translation " => "La cuenta de los titulares de tarjetas no tiene fondos suficientes para cubrir el monto de la transacción.Los intentos posteriores en una fecha posterior pueden tener éxito.",
        "error_msg " => "The cardholders account does not have sufficient funds to cover the transaction amount.Subsequent attempts at a later date may be successful.",
        "severity " => "Soft ",
    ],
    "62" => [
        "error_msg_translation " => "La tarjeta está restringida por el emisor.El titular de la tarjeta debe comunicarse con el emisor para obtener más detalles.",
        "error_msg " => "The card is restricted by the Issuer.The cardholder should contact the Issuer for more details.",
        "severity " => "",
    ],
    "63" => [
        "error_msg_translation " => "Servicio no permitido ",
        "error_msg " => "Service not allowed ",
        "severity " => "",
    ],
    "65" => [
        "error_msg_translation " => "La tarjeta ha excedido la frecuencia de las transacciones permitidas.",
        "error_msg " => "The card has exceed the frequency of transactions allowed.",
        "severity " => "Soft ",
    ],
    "68" => [
        "error_msg_translation " => "La respuesta se recibió demasiado tarde y se produjo un tiempo de espera.Volver a intentar la transacción puede dar lugar a una aprobación.",
        "error_msg " => "The response was received too late and a timeout occurred.Retrying the transaction may result in an Approval ",
        "severity " => "Soft ",
    ],
    "69" => [
        "error_msg_translation " => "Error de la clave del host ",
        "error_msg " => "Host key error ",
        "severity " => "",
    ],
    "75" => [
        "error_msg_translation " => "El emisor ha rechazado la transacción porque el titular de la tarjeta ha ingresado el PIN incorrectamente muchas veces.",
        "error_msg " => "The Issuer has declined the transaction because the cardholder has entered the PIN incorrectly too many times.",
        "severity " => "Soft ",
    ],
    "89" => [
        "error_msg_translation " => "ID de terminal no válido ",
        "error_msg " => "Invalid Terminal ID ",
        "severity " => "",
    ],
    "91" => [
        "error_msg_translation " => "El emisor no está disponible actualmente.Esto puede ser temporal: un intento posterior puede ser exitoso.",
        "error_msg " => "The Issuer is currently unavailable.This may be temporary a subsequent attempt may be successful.",
        "severity " => "Soft ",
    ],
    "92" => [
        "error_msg_translation " => "El emisor no está disponible actualmente.Esto puede ser temporal: un intento posterior puede ser exitoso.",
        "error_msg " => "The Issuer is currently unavailable.This may be temporary a subsequent attempt may be successful.",
        "severity " => "Soft ",
    ],
    "94" => [
        "error_msg_translation " => "La transacción presentada parece ser un duplicado de una transacción presentada previamente y se negó a evitar cargar la misma tarjeta dos veces por el mismo servicio.Verifique sus sistemas para ver si existe un duplicado.",
        "error_msg " => "The submitted transaction appears to be a duplicate of a previously submitted transaction and was declined to prevent charging the same card twice for the same service.Check your systems to see if a duplicate exists.",
        "severity " => "Soft ",
    ],
    "96" => [
        "error_msg_translation " => "Hay un error del sistema que ha impedido que se procese la transacción.Volver a intentar la transacción puede dar lugar a una aprobación.",
        "error_msg " => "There is a system error which has prevented the transaction from being processed.Retrying the transaction may result in an Approval.",
        "severity " => "Soft ",
    ],
    "100" => [
        "error_msg_translation " => "Error interno ",
        "error_msg " => "Internal error ",
        "severity " => "",
    ],
    "1200" => [
        "error_msg_translation " => "El comerciante está configurado para el código de tarjeta obligatorio, pero envía una transacción sin un valor de código de tarjeta.",
        "error_msg " => "The merchant is configured for mandatory card code but sent a transaction without a card code value.",
        "severity " => "",
    ],
    "2303" => [
        "error_msg_translation " => "número de tarjeta de crédito no válido ",
        "error_msg " => "Invalid credit card number ",
        "severity " => "",
    ],
    "2304" => [
        "error_msg_translation " => "La tarjeta de crédito está caducada o la fecha de vencimiento no es válida ",
        "error_msg " => "Credit card is expired or expiration date is invalid ",
        "severity " => "",
    ],
    "5002" => [
        "error_msg_translation " => "El comerciante no tiene una entrada de servicio para la marca de tarjetas de crédito dentro de la solicitud de transacción.",
        "error_msg " => "The merchant does not have a service entry for the credit card brand within the transaction request.",
        "severity " => "",
    ],
    "5003" => [
        "error_msg_translation " => "El pedido ya existe en la base de datos.",
        "error_msg " => "The order already exists in the database.",
        "severity " => "",
    ],
    "5004" => [
        "error_msg_translation " => "El pedido no incluye un preauth autorizado ",
        "error_msg " => "The order does not include an authorised PreAuth ",
        "severity " => "",
    ],
    "5005" => [
        "error_msg_translation " => "Bloqueado debido a la configuración de prevención de fraude ",
        "error_msg" => "Blocked due to fraud prevention setting",
        "severity" => "",
    ],
    "5006" => [
        "error_msg_translation" => "La transacción a anular no se encontró",
        "error_msg" => "Transaction to be voided was not found",
        "severity" => "",
    ],
    "5007" => [
        "error_msg_translation" => "La cantidad dentro de la transacción nula no es válida: omita o corregir",
        "error_msg" => "Amount within void transaction is invalid please omit or correct",
        "severity" => "",
    ],
    "5009" => [
        "error_msg_translation" => "No se encuentra transacción en orden que se pueda devolver",
        "error_msg" => "No transaction found in order which can be returned",
        "severity" => "",
    ],
    "5014" => [
        "error_msg_translation" => "Problema de validación",
        "error_msg" => "Validation problem",
        "severity" => "",
    ],
    "5017" => [
        "error_msg_translation" => "La anulación de las transacciones devueltas no es compatible",
        "error_msg" => "Voiding of returned transactions is not supported",
        "severity" => "",
    ],
    "5018" => [
        "error_msg_translation" => "No se encontró transacción para vacío",
        "error_msg" => "No transaction found for void",
        "severity" => "",
    ],
    "5019" => [
        "error_msg_translation" => "La transacción a anular no es anular",
        "error_msg" => "The transaction to be voided is not voidable",
        "severity" => "",
    ],
    "5022" => [
        "error_msg_translation" => "Los preauthis no se pueden anular siempre que haya un postalón capturado, por favor, anule el posta.",
        "error_msg" => "The PreAuthis not voidable as long as there is a captured PostAuth please void the PostAuthbefore",
        "severity" => "",
    ],
    "5100" => [
        "error_msg_translation" => "Combinación de valor de respuesta segura 3D no válida",
        "error_msg" => "Invalid 3D Secure response value combination",
        "severity" => "",
    ],
    "5101" => [
        "error_msg_translation" => "Transaccion rechazada. Falló la autenticación segura 3D",
        "error_msg" => "Transaction declined. 3D Secure authentication failed",
        "severity" => "",
    ],
    "5102" => [
        "error_msg_translation" => "Transacción fallida. ECI - 7 Las transacciones no son compatibles con el comerciante",
        "error_msg" => "Transaction failed. ECI / 7 transactions are not supported by merchant",
        "severity" => "",
    ],
    "5103" => [
        "error_msg_translation" => "Utilizado para la espera segura 3D a la disminución",
        "error_msg" => "Used for 3D Secure waiting to declined ",
        "severity" => "",
    ],
    "5108" => [
        "error_msg_translation" => "Transacción fallida. Las transacciones Moto no son compatibles con Connect (p/u00e1gina de pago alojada)",
        "error_msg" => "Transaction failed. MOTO transactions are not supported in Connect (Hosted Payment Page)",
        "severity" => "",
    ],
    "5111" => [
        "error_msg_translation" => "Transacción fallida. Las transacciones ECI1 y ECI6 no son compatibles con el comerciante",
        "error_msg" => "Transaction failed. ECI1 and ECI6 transactions are not supported by the merchant ",
        "severity" => "",
    ],
    "5112" => [
        "error_msg_translation " => "Transacción fallida.Tarjeta de débito no autenticada ",
        "error_msg " => "Transaction failed.Debit card not authenticated ",
        "severity" => "",
    ],
    "5115" => [
        "error_msg_translation " => "Transacción fallida ",
        "error_msg " => "Transaction failed ",
        "severity" => "",
    ],
    "5315" => [
        "error_msg_translation " => "La transacción ya está en estado rechazada ",
        "error_msg " => "Transaction is already in state DECLINED ",
        "severity" => "",
    ],
    "5433" => [
        "error_msg_translation " => "Configuración de comerciante incorrectamente ",
        "error_msg " => "Merchant setup incorrectly ",
        "severity" => "",
    ],
    "5993" => [
        "error_msg_translation " => "La transacción ha sido cancelada por el usuario.",
        "error_msg " => "The transaction has been cancelled by the user.",
        "severity" => "",
    ],
    "5994" => [
        "error_msg_translation " => "La marca seleccionada no coincide con el número de tarjeta.",
        "error_msg " => "The selected brand does not match the card number.",
        "severity" => "",
    ],
    "5995" => [
        "error_msg_translation " => "Ordene demasiado viejo para ser referenciado ",
        "error_msg " => "Order too old to be referenced ",
        "severity" => "",
    ],
    "5996" => [
        "error_msg_translation " => "El tipo de marca / tarjeta no es válido o no es compatible ",
        "error_msg " => "Brand / card type is invalid or not supported ",
        "severity" => "",
    ],
    "5998" => [
        "error_msg_translation " => "Otra transacción con la misma ID de pedido que se está procesando actualmente ",
        "error_msg " => "Other transaction with same order ID currently being processed ",
        "severity" => "",
    ],
    "7777" => [
        "error_msg_translation " => "Sistema demasiado ocupado ",
        "error_msg " => "System too busy ",
        "severity" => "",
    ],
    "7778" => [
        "error_msg_translation " => "Transacción tiempo de tiempo fuera y no se ha procesado, vuelva a intentarlo ",
        "error_msg " => "Transaction timed out and has not been processed, please retry ",
        "severity" => "",
    ],
    "10421" => [
        "error_msg_translation " => "El comerciante no está configurado para admitir el envío dividido ",
        "error_msg " => "The merchant is not setup to support split shipment ",
        "severity" => "",
    ],
    "10422" => [
        "error_msg_translation " => "No hay más postouth posible para este ID de pedido ",
        "error_msg " => "No further PostAuth possible for this Order ID ",
        "severity" => "",
    ],
    "10423" => [
        "error_msg_translation " => "No se estableció el envío final todavía ",
        "error_msg " => "No final shipment settled yet ",
        "severity" => "",
    ],
    "10424" => [
        "error_msg_translation " => "No hay más postouth posible para este ID de pedido ",
        "error_msg " => "No further PostAuth possible for this Order ID ",
        "severity" => "",
    ],
    "10425" => [
        "error_msg_translation " => "Información de envío de división obligatoria faltante ",
        "error_msg " => "Missing mandatory split shipment information ",
        "severity" => "",
    ],
    "10426" => [
        "error_msg_translation " => "Envío dividido ya que el postAuth anterior es un envío no dividido ",
        "error_msg " => "Split shipment since the previous PostAuth is non split shipment ",
        "severity" => "",
    ],
    "10501" => [
        "error_msg_translation " => "Solo las transacciones de preauth aprobadas(y aún no capturadas) pueden ser capturadas por un postAuth // PostAuth ya realizado",
        "or_msg" => "Only approved (and not yet captured) PreAuth transactions can be captured by a PostAuth//PostAuth already performed",
        "severity" => "",
    ],
    "10503" => [
        "error_msg_translation" => "Cantidad o moneda no válida",
        "error_msg" => "Invalid amount or currency",
        "severity" => "",
    ],
    "10504" => [
        "error_msg_translation" => "Este regreso no es anual",
        "error_msg" => "This return is not voidable",
        "severity" => "",
    ],
    "10601" => [
        "error_msg_translation" => "La cantidad total pasada es mayor que la cantidad de devolución/nula.",
        "error_msg" => "Total amount passed is more than the Return/Void amount.",
        "severity" => "",
    ],
    "12000" => [
        "error_msg_translation" => "El código de seguridad de la tarjeta es obligatorio",
        "error_msg" => "Card security code is mandatory",
        "severity" => "",
    ],
    "13532" => [
        "error_msg_translation" => "Tipo de tarjeta no válido",
        "error_msg" => "Invalid card type",
        "severity" => "",
    ],
    "20429" => [
        "error_msg_translation" => "El comerciante no está configurado para admitir devolución de efectivo",
        "error_msg" => "The merchant is not setup to support cashback",
        "severity" => "",
    ],
    "20430" => [
        "error_msg_translation" => "Tipo de transacción no admitido para reembolso",
        "error_msg" => "Transaction type not supported for cashback",
        "severity" => "",
    ],
    "20431" => [
        "error_msg_translation" => "Tipo de pago no admitido para reembolso",
        "error_msg" => "Payment type not supported for cashback ",
        "severity" => "",
    ],
    "21281" => [
        "error_msg_translation" => "El comerciante no está configurado para admitir la aerolínea",
        "error_msg" => "The merchant is not setup to support airline",
        "severity" => "",
    ],
    "22812" => [
        "error msg translation" => "Diferentes monedas en el orden",
        "error msg" => "Different currencies in the order",
        "severity" => "",
    ],
    "22813" => [
        "error_msg_translation" => "No se permite la moneda, porque las transacciones aprobadas en el mismo orden tienen otra moneda",
        "error_msg" => "Currency is not allowed, because approved transactions on the same order have another currency",
        "severity" => "",
    ],
    "23032" => [
        "error_msg_translation" => "Tipo de origen de transacción Moto / Minorista No es compatible con MCC7995 Pago de gacias de juegos",
        "error_msg" => "Transaction origin type MOTO / RETAIL not supported for MCC7995 payment of gaming winnings",
        "severity" => "",
    ],
    "28319" => [
        "error_msg_translation" => "Error de autenticación de Rupay",
        "error_msg" => "RuPay authentication error",
        "severity" => "",
    ],
    "30031" => [
        "error_msg_translation" => "Sin configuración de terminal",
        "error_msg" => "No terminal setup",
        "severity" => "",
    ],
    "30050" => [
        "error_msg_translation" => "Transacción tiempo fuera",
        "error_msg" => "Transaction timed out",
        "severity" => "",
    ],
    "30051" => [
        "error_msg_translation" => "Transacción tiempo fuera",
        "error_msg" => "Transaction timed out",
        "severity" => "",
    ],
    "30052" => [
        "error_msg_translation" => "Transacción tiempo fuera",
        "error_msg" => "Transaction timed out",
        "severity" => "",
    ],
    "30053" => [
        "error_msg_translation" => "La tarjeta de crédito que se usa para la transacción ha sido rechazada por el emisor. // El comerciante no tiene una entrada de servicio para la marca de tarjetas de crédito dentro de la solicitud de transacción .//Transaction Toured Out",
        "error_msg" => "The credit card being used for the transaction has been rejected by the issuer. //The merchant does not have a service entry for the credit card brand within the transaction request.//Transaction timed out",
        "severity" => "",
    ],
    "30054" => [
        "error_msg_translation" => "Transacción tiempo fuera",
        "error_msg" => "Transaction timed out",
        "severity" => "",
    ],
    "30055" => [
        "error_msg_translation" => "No configurado para 3D Secure",
        "error_msg" => "Not configured for 3D Secure",
        "severity" => "",
    ],
    "30060" => [
        "error_msg_translation" => "La transacción ha sido cancelada por el usuario.",
        "error_msg" => "The transaction has been cancelled by the user.",
        "severity" => "",
    ],
    "30062" => [
        "error_msg_translation" => "Transacción tiempo fuera",
        "error_msg" => "Transaction timed out",
        "severity" => "",
    ],
    "30157" => [
        "error_msg_translation" => "El comerciante no está configurado para admitir el servicio solicitado",
        "error_msg" => "The merchant is not setup to support the requested service",
        "severity" => "",
    ],
    "30158" => [
        "error_msg_translation" => "Falta del código de aprobación para la transacción fuera de línea",
        "error_msg" => "Missing approval code for offline transaction",
        "severity" => "",
    ],
    "30159" => [
        "error_msg_translation" => "Esta transacción se recibió como se rechazó desde el punto de venta",
        "error_msg" => "This transaction was received as declined from point of sale",
        "severity" => "",
    ],
    "31218" => [
        "error_msg_translation" => "El comerciante no está configurado para admitir el servicio solicitado",
        "error_msg" => "The merchant is not setup to support the requested service",
        "severity" => "",
    ],
    "31319" => [
        "error_msg_translation" => "El comerciante no está configurado para admitir la identificación del contrato para el servicio de seguro",
        "error_msg" => "The merchant is not setup to support contract ID for insurance service",
        "severity" => "",
    ],
    "42217" => [
        "error_msg_translation" => "MCC inválido configurado para comerciante de alojamiento del hotel, MCC debe ser 3501 - 3999 o 7011",
        "error_msg" => "Invalid MCC configured for hotel lodging merchant, MCC must be 3501 / 3999 or7011 ",
        "severity " => "",
    ],
    "42218" => [
        "error_msg_translation" => "El comerciante no está configurado para apoyar al alojamiento del hotel ",
        "error_msg " => "The merchant is not setup to support hotel lodging ",
        "severity " => "",
    ],
    "42317" => [
        "error_msg_translation " => "MCC inválido configurado para comerciante de alquiler de automóviles, MCC debe ser 3351 - 3500, 7512, 7513 o 7519 ",
        "error_msg " => "Invalid MCC configured for car rental merchant, MCC must be 3351/ 3500, 7512, 7513 or 7519 ",
        "severity " => "",
    ],
    "42318" => [
        "error_msg_translation " => "El comerciante no está configurado para admitir el alquiler de automóviles ",
        "error_msg " => "The merchant is not setup to support car rental ",
        "severity " => "",
    ],
    "42741" => [
        "error_msg_translation " => "Iban o número de tarjeta de crédito no válido ",
        "error_msg " => "Invalid IBAN or Credit card number",
        "severity" => "",
    ],
    "42742" => [
        "error_msg_translation" => "Tipo recurrente no es compatible para establecerse en la transacción",
        "error_msg" => "Recurring type not supported to be set in the transaction",
        "severity" => "",
    ],
    "42744" => [
        "error_msg_translation" => "Error interno",
        "error_msg" => "Internal error",
        "severity" => "",
    ],
    "42745" => [
        "error_msg_translation" => "Error interno",
        "error_msg" => "Internal error",
        "severity" => "",
    ],
    "42755" => [
        "error_msg_translation" => "Transacción segura3 D no autenticada ",
        "error_msg " => "3 D Secure transaction not authenticated ",
        "severity " => "",
    ],
    "50002" => [
        "error_msg_translation " => "Error de validación de datos seguro 3 D AAV / CAVV ",
        "error_msg " => "3 D Secure data validation error AAV / CAVV ",
        "severity " => "",
    ],
    "50003" => [
        "error_msg_translation " => "Error de validación de datos seguro 3 D XID - ECI ",
        "error_msg " => "3 D Secure data validation error XID / ECI ",
        "severity " => "",
    ],
    "50004" => [
        "error_msg_translation " => "Solicitud de transacción no válida ",
        "error_msg" => "Invalid transaction request",
        "severity" => "",
    ],
    "50294" => [
        "error_msg_translation" => "Información de tarjeta incorrecta enviada",
        "error_msg" => "Wrong card information sent",
        "severity" => "",
    ],
    "50295" => [
        "error_msg_translation" => "La preauth no es anulable siempre que haya un preauth autorizado, anule la preauth incremental antes",
        "error_msg" => "The PreAuth is not voidable as long as there is an authorised incremental PreAuth / Contact Uplease vs oid the increPrivacy & Legal mental PreAuth before ",
        "severity " => "",
    ],
    "50305" => [
        "error_msg_translation" => "La transacción referenciada no está en el estado de transacción correcto",
        "error_msg" => "Referenced transaction is not in the correct transaction state",
        "severity" => "",
    ],
    "50306" => [
        "error_msg_translation" => "Incapaz de obtener pares",
        "error_msg" => "Unable to get PARes",
        "severity" => "",
    ],
    "50307" => [
        "error_msg_translation" => "Pares vacíos en respuesta",
        "error_msg" => "Empty PARes in response",
        "severity" => "",
    ],
    "50308" => [
        "error_msg_translation" => "Estado de transacción incorrecto",
        "error_msg" => "Wrong transaction state",
        "severity" => "",
    ],
    "50653" => [
        "error_msg_translation" => "El comerciante está configurado para el código de tarjeta obligatorio, pero envía una transacción sin un valor de código de tarjeta.",
        "error_msg" => "The merchant is configured for mandatory card code but sent a transaction without a card code value.",
        "severity" => "",
    ],
    "50655" => [
        "error_msg_translation" => "No se puede verificar la inscripción de la tarjeta",
        "error_msg" => "Unable to verify card enrolment",
        "severity" => "",
    ],
    "50656" => [
        "error_msg_translation" => "Rechazado por fraude detectar",
        "error_msg" => "Declined by Fraud Detect",
        "severity" => "",
    ],
    "50666" => [
        "error_msg_translation" => "Nombre del propietario de la cuenta no proporcionado",
        "error_msg" => "Account owner name not provided",
        "severity" => "",
    ],
    "50677" => [
        "error_msg_translation" => "Incapaz de convertir a transacción nula",
        "error_msg" => "Unable to convert to void transaction",
        "severity" => "",
    ],
    "50678" => [
        "error_msg_translation" => "Error de autenticación del pagador",
        "error_msg" => "Payer authentication error",
        "severity" => "",
    ],
    "50681" => [
        "error_msg_translation" => "Datos faltantes en la respuesta de autenticación del pagador",
        "error_msg" => "Missing data in payer authentication response",
        "severity" => "",
    ],
    "50682" => [
        "error_msg_translation" => "Datos comerciales faltantes en la respuesta de autenticación del pagador",
        "error_msg" => "Missing merchant data in payer authentication response",
        "severity" => "",
    ],
    "50683" => [
        "error_msg_translation" => "Falta de datos 3D seguros",
        "error_msg" => "Missing secure 3D data",
        "severity" => "",
    ],
    "50687" => [
        "error_msg_translation" => "Transacción de autenticación dividida no compatible",
        "error_msg" => "Split authentication transaction not supported",
        "severity" => "",
    ],
    "50688" => [
        "error_msg_translation" => "La tienda no admite MPI a travéss de API",
        "error_msg" => "Store doesn't support MPI via API",
        "severity" => "",
    ],
    "50690" => [
        "error_msg_translation" => "Rechazado por fraude detectar",
        "error_msg" => "Declined by Fraud Detect",
        "severity" => "",
    ],
    "50698" => [
        "error_msg_translation" => "El tipo de transacción de la transacción referenciada es incompatible con la transacción actual",
        "error_msg" => "The transaction type of referenced transaction is incompatible with the current transaction",
        "severity" => "",
    ],
    "50715" => [
        "error_msg_translation" => "La transacción de referencia no se encuentra o no está en el estado adecuado para procesar la solicitud posterior del método 3DS",
        "error_msg" => "Reference transaction is either not found or not in proper status to process subsequent 3Ds method request",
        "severity" => "",
    ],
    "50716" => [
        "error_msg_translation" => "Transaccion rechazada. Falló la autenticación segura 3D",
        "error_msg" => "Transaction declined. 3D Secure authentication failed",
        "severity" => "",
    ],
    "50734" => [
        "error_msg_translation" => "La transacción de transacción de autenticación sin pago debe tener una cantidad cero",
        "error_msg" => "Non-Payment Authentication passthrough transaction should have zero amount",
        "severity" => "",
    ],
    "50735" => [
        "error_msg_translation" => "La transacción de pase de autenticación sin pago debe ser un preeauth",
        "error_msg" => "Non-Payment Authentication passthrough transaction should be a PreAuth",
        "severity" => "",
    ],
    "50736" => [
        "error_msg_translation" => "Para 3D Secure EMVCO Mensaje Tipo NPA No se permite una transacción de venta posterior",
        "error_msg" => "For 3D Secure EMVCO message type NPA a subsequent sale transaction is not allowed",
        "severity" => "",
    ],
    "59365" => [
        "error_msg_translation" => "No se puede guardar los datos de respuesta de EMV en la base de datos",
        "error_msg" => "Unable to save EMV response data into the database",
        "severity" => "",
    ],
    "59366" => [
        "error_msg_translation" => "Falta el número de referencia",
        "error_msg" => "Reference number missing ",
        "severity" => "",
    ],
    "62086" => [
        "error_msg_translation" => "Error de configuración",
        "error_msg" => "Setup Error",
        "severity" => "",
    ],
    "63096" => [
        "error_msg_translation" => "Error de configuración",
        "error_msg" => "Setup Error",
        "severity" => "",
    ],
    "95001" => [
        "error_msg_translation" => "Moneda no admitida para tarjetas locales",
        "error_msg" => "Currency not supported for Local cards",
        "severity" => "",
    ],
    "96000" => [
        "error_msg_translation" => "entrega no compatible",
        "error_msg" => "installment not supported",
        "severity" => "",
    ],
    "1A" => [
        "error_msg_translation" => "Agregar auth requerir",
        "error_msg" => "Add Auth Require",
        "severity" => "",
    ],
    "C2" => [
        "error_msg_translation" => "CVV2 declinó",
        "error_msg" => "CVV2 Declined",
        "severity" => "",
    ],
    "RW" => [
        "error_msg_translation" => "Rev Outside Win",
        "error_msg" => "Rev Outside Win",
        "severity" => "",
    ],
];
