<?php

namespace App\Utils;

class FieldsExcelReport
{
    const DMR = [
        "DISTIRTO" => "postulate_id",
        "No_CARGO" => "type_postulate",
        "CARGO" => "type_postulate",
        "APELLIDO_PATERNO" => "father_lastname",
        "APELLIDO_MATERNO" => "mother_lastname",
        "NOMBRE" => "name",
        "SOBRENOMBRE" => "nickname",
        "VIALIDAD" => "roads",
        "NOMBRE_VIALIDAD" => "roads_name",
        "NUM_EXTERIOR" => "outdoor_number",
        "NUM_INTERIOR" => "interior_number",
        "COLONIA" => "neighborhood",
        "CODIGO_POSTAL" => "zipcode",
        "MUNICIPIO" => "municipality",
        "CLAVE_ELECTOR" => "elector_key",
        "OCR" => "ocr",
        "CIC" => "cic",
        "EMISION" => "emission",
        "ENTIDAD" => "entity",
        "SECCION" => "section",
        "FECHA_NACIMIENTO" => "date_birth",
        "GENERO" => "gender",
        "LUGAR_NACIMIENTO" => "birthplace",
        "TIEMPO_RESIDENCIA_AÑOS" => "residence_time_year",
        "TIEMPO_RESIDENCIA_MESES" => "residence_time_month",
        "OCUPACION" => "occupation",
        "REELECCION" => "re_election",
    ];
    const DRP = [
        "No_CARGO" => "type_postulate",
        "CARGO" => "type_postulate",
        "APELLIDO_PATERNO" => "father_lastname",
        "APELLIDO_MATERNO" => "mother_lastname",
        "NOMBRE" => "name",
        "SOBRENOMBRE" => "nickname",
        "VIALIDAD" => "roads",
        "NOMBRE_VIALIDAD" => "roads_name",
        "NUM_EXTERIOR" => "outdoor_number",
        "NUM_INTERIOR" => "interior_number",
        "COLONIA" => "neighborhood",
        "CODIGO_POSTAL" => "zipcode",
        "MUNICIPIO" => "municipality",
        "CLAVE_ELECTOR" => "elector_key",
        "OCR" => "ocr",
        "CIC" => "cic",
        "EMISION" => "emission",
        "ENTIDAD" => "entity",
        "SECCION" => "section",
        "FECHA_NACIMIENTO" => "date_birth",
        "GENERO" => "gender",
        "LUGAR_NACIMIENTO" => "birthplace",
        "TIEMPO_RESIDENCIA_AÑOS" => "residence_time_year",
        "TIEMPO_RESIDENCIA_MESES" => "residence_time_month",
        "OCUPACION" => "occupation",
        "REELECCION" => "re_election",
        "GRUPO_INDIGENA" => "indigenous_group",
        "GRUPO_DIVERSIDAD_SEXUAL" => "group_sexual_diversity",
        "GRUPO_DISCAPACITADOS" => "disabled_group",
    ];
    const AYU = [
        "DISTRITO" => "postulate_id",
        "NO_MPIO" => "postulate_id",
        "MUNICIPIO" => "municipality",
        "No_CARGO" => "type_postulate",
        "CARGO" => "type_postulate",
        "APELLIDO_PATERNO" => "father_lastname",
        "APELLIDO_MATERNO" => "mother_lastname",
        "NOMBRE" => "name",
        "SOBRENOMBRE" => "nickname",
        "VIALIDAD" => "roads",
        "NOMBRE_VIALIDAD" => "roads_name",
        "NUM_EXTERIOR" => "outdoor_number",
        "NUM_INTERIOR" => "interior_number",
        "COLONIA" => "neighborhood",
        "CODIGO_POSTAL" => "zipcode",
        "MUNICIPIO" => "municipality",
        "CLAVE_ELECTOR" => "elector_key",
        "OCR" => "ocr",
        "CIC" => "cic",
        "EMISION" => "emission",
        "ENTIDAD" => "entity",
        "SECCION" => "section",
        "FECHA_NACIMIENTO" => "date_birth",
        "GENERO" => "gender",
        "LUGAR_NACIMIENTO" => "birthplace",
        "TIEMPO_RESIDENCIA_AÑOS" => "residence_time_year",
        "TIEMPO_RESIDENCIA_MESES" => "residence_time_month",
        "OCUPACION" => "occupation",
        "REELECCION" => "re_election",
        "GRUPO_INDIGENA" => "indigenous_group",
        "GRUPO_DIVERSIDAD_SEXUAL" => "group_sexual_diversity",
        "GRUPO_DISCAPACITADOS" => "disabled_group"
    ];
    const INE = [
        "Número de línea" => "number_line",
        "Tipo candidatura" => "postulate",
        "Entidad" => "entity",
        "Circunscripción" => "circumscription",
        "Distrito" => "postulate_id",
        "Municipio" => "municipality",
        "Localidad" => "locality",
        "Demarcación" => "demarcation",
        "Juntas Municipales" => "municipalities_council",
        "Lema de campaña" => "campaign_slogan",
        "Número de lista" => "list_number",
        "Clave de elector" => "elector_key",
        "Nombre" => "name",
        "Primer apellido" => "father_lastname",
        "Segundo apellido" => "mother_lastname",
        "Sexo" => "gender",
        "¿Realizará Campaña?" => "campaign",
        "Opta por reelección" => "re_election",
        "Fecha de nacimiento" => "date_birth",
        "CURP" => "curp",
        "Confirmación de CURP" => "curp_confirmation",
        "RFC" => "rfc",
        "Ocupación" => "occupation",
        "Identificación OCR" => "ocr",
        "Lugar de nacimiento" => "birthplace",
        "Sobrenombre" => "nickname",
        "Tipo de residencia en años" => "residence_time_year",
        "Tipo de residencia en meses" => "residence_time_month",
        "Tipo de teléfono" => "phone_type",
        "LADA" => "lada",
        "Teléfono" => "phone",
        "Extensión" => "extension",
        "Correo electrónico" => "email",
        "Confirmación correo electronico" => "email_confirmation",
        "Total de ingresos anuales" => "total_annual_income",
        "Salarios de ingresos anuales" => "salary_annual_income",
        "Rendimeintos financieros" => "financial_performances",
        "Utilidad anual de actividad profesional" => "annual_profit_professional_activity",
        "Ganancias anuales de arrendamiento inmuebles" => "annual_real_estate_lease_earnings",
        "Honorarios de servicios profesionales" => "professional_services_fees",
        "Otros ingresos" => "other_income",
        "Total de egresos anuales" => "total_annual_expenses",
        "Gastos personales" => "personal_expenses",
        "Pagos de bienes inmuebles" => "real_estate_payments",
        "Pago de deudas" => "debt_payments",
        "Perdidas de actividad profesional" => "loss_personal_activity",
        "Otros egresos" => "other_expenses",
        "Bienes inmuebles" => "property",
        "Vehiculos" => "vehicles",
        "Otros bienes muebles" => "other_movable_property",
        "Cuentas bancarias e inversiones en México y en el exterior" => "bank_accounts",
        "Otros activos" => "other_assets",
        "Monto de adeudo de pago" => "payment_debt_amount",
        "Otros pasivos" => "other_passives",
        "Registra suplencia" => "",
        "Nombre" => "name",
        "Primer apellido" => "father_lastname",
        "Segundo apellido" => "mother_lastname",
        "Sobrenombre" => "nickname",
        "Fecha de nacimiento" => "date_birth",
        "Lugar de nacimiento" => "birthplace",
        "Sexo" => "gender",
        "CURP" => "curp",
        "Confirmación de CURP" => "curp_confirmation",
        "RFC" => "rfc",
        "Clave de Elector" => "elector_key",
        "Ocupación" => "occupation",
        "Identificador OCR" => "ocr",
        "Tipo de residencia en años" => "residence_time_year",
        "Tipo de residencia en meses" => "residence_time_month",
        "Tipo de teléfono" => "phone_type",
        "LADA" => "lada",
        "Teléfono" => "phone",
        "Extensión" => "extension",
        "Correo electrónico" => "email",
        "Confirmación de correo electrónico" => "email_confirmation",
        "Otros" => "others",
        "Consideraciones" => "considerations",
    ];
    const INE_2 = [
        "NÚMERO_LÍNEA" => "number_line",
        "TIPO_CANDIDATURA" => "postulate",
        "NÚMERO_LISTA/PLANILLA" => "number_list",
        "CLAVE_ELECTOR" => "elector_key",
        "NOMBRE" => "name",
        "PRIMER_APELLIDO" => "father_lastname",
        "SEGUNDO_APELLIDO" => "mother_lastname",
        "SEXO" => "gender",
        "FECHA_NACIMIENTO" => "date_birth",
        "CURP" => "curp",
        "CONFIRMAR_CURP" => "curp_confirmation",
        "RFC" => "rfc",
        "OCUPACIÓN" => "occupation",
        "IDENTIFICADOR_OCR" => "ocr",
        "LUGAR_NACIMIENTO" => "birthplace",
        "SOBRENOMBRE" => "nickname",
        "TIEMPO_RESIDENCIA_AÑOS" => "residence_time_year",
        "TIEMPO_RESIDENCIA_MESES" => "residence_time_month",
        "TIPO_TELÉFONO" => "phone_type",
        "LADA" => "lada",
        "TELÉFONO" => "phone",
        "EXTENSIÓN" => "extension",
        "CORREO_ELECTRÓNICO" => "email",
        "CONFIRMACIÓN_CORREO" => "email_confirmation",
        "REGISTRA_SUPLENCIA" => "",
        "NOMBRE_SUPLENCIA" => "name",
        "PRIMER_APELLIDO_SUPLENCIA" => "father_lastname",
        "SEGUNDO_APELLIDO_SUPLENCIA" => "mother_lastname",
        "SOBRENOMBRE_SUPLENCIA" => "nickname",
        "FECHA_NACIMIENTO_SUPLENCIA" => "date_birth",
        "LUGAR_NACIMIENTO_SUPLENCIA" => "birthplace",
        "SEXO_SUPLENCIA" => "gender",
        "CURP_SUPLENCIA" => "curp",
        "CONFIRMAR_CURP_SUPLENCIA" => "curp_confirmation",
        "RFC_SUPLENCIA" => "rfc",
        "CLAVE_ELECTOR_SUPLENCIA" => "elector_key",
        "OCUPACIÓN_SUPLENCIA" => "occupation",
        "IDENTIFICADOR_OCR_SUPLENCIA" => "ocr",
        "RESIDENCIA_AÑOS_SUPLENCIA" => "residence_time_year",
        "RESIDENCIA_MESES_SUPLENCIA" => "residence_time_month",
        "TIPO_TELÉFONO_SUPLENCIA" => "phone_type",
        "LADA_SUPLENCIA" => "lada",
        "TELÉFONO_SUPLENCIA" => "phone",
        "EXTENSIÓN_SUPLENCIA" => "extension",
        "CORREO_ELECTRÓNICO_SUPLENCIA" => "email",
        "CONFIRMACIÓN_CORREO_SUPLENCIA" => "email_confirmation"
    ];
}
