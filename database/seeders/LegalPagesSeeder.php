<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class LegalPagesSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'slug'             => 'terminos',
                'title'            => 'Términos y Condiciones',
                'meta_title'       => 'Términos y Condiciones — Nexova Desk',
                'meta_description' => 'Términos y condiciones de uso del servicio Nexova Desk. Ley 1480 de 2011 y Ley 527 de 1999.',
                'status'           => 'published',
                'content'          => <<<'MD'
# Términos y Condiciones de Uso

**Última actualización:** 31 de marzo de 2026

Bienvenido a **Nexova Desk**, un servicio operado por **Nexova Digital Solutions S.A.S.**, empresa constituida en Colombia bajo las leyes de la República de Colombia, con domicilio en la ciudad de Bogotá D.C.

Al acceder y utilizar este servicio, usted acepta estos Términos y Condiciones en su totalidad. Si no está de acuerdo, le solicitamos no continuar utilizando el servicio.

---

## 1. Definiciones

- **Servicio:** La plataforma de soporte al cliente mediante chat en vivo y atención con inteligencia artificial disponible en nexovadesk.com.
- **Usuario / Cliente:** Persona natural o jurídica que contrata el Servicio.
- **Agente:** Colaborador designado por el Cliente para atender conversaciones a través del panel de administración.
- **Visitor / Visitante:** Persona que interactúa con el widget de chat instalado en el sitio web del Cliente.
- **Contenido:** Mensajes, archivos, imágenes y cualquier otro dato transmitido a través del Servicio.

---

## 2. Acceso y Uso del Servicio

2.1. El acceso al Servicio requiere el registro de una cuenta con información veraz y actualizada.

2.2. El Cliente es responsable de mantener la confidencialidad de sus credenciales de acceso y de todas las actividades realizadas bajo su cuenta.

2.3. Queda prohibido el uso del Servicio para actividades ilícitas, envío de spam, distribución de malware, o cualquier actividad que infrinja derechos de terceros o la normativa colombiana vigente.

2.4. Nexova Digital Solutions S.A.S. se reserva el derecho de suspender o cancelar cuentas que infrinjan estos términos, sin perjuicio de las acciones legales correspondientes.

---

## 3. Planes y Pagos

3.1. El Servicio ofrece planes gratuitos y de pago. Las características de cada plan se describen en la página de precios disponible en el sitio web.

3.2. Los pagos se procesan de forma recurrente (mensual o anual) según el plan contratado. Los precios están expresados en dólares estadounidenses (USD) o pesos colombianos (COP) según se indique.

3.3. De conformidad con la **Ley 1480 de 2011 (Estatuto del Consumidor)**, el Cliente tiene derecho a retractarse de la compra dentro de los **5 días hábiles** siguientes a la contratación del plan, siempre que no haya hecho uso extensivo del servicio.

3.4. No se realizan reembolsos parciales por períodos no utilizados, salvo en los casos contemplados por la Ley 1480 de 2011.

---

## 4. Propiedad Intelectual

4.1. Nexova Desk y todos sus componentes (código fuente, diseño, marcas, logotipos) son propiedad exclusiva de Nexova Digital Solutions S.A.S. o sus licenciantes.

4.2. El Cliente conserva la propiedad de los datos y contenidos que carga o transmite a través del Servicio.

4.3. El Cliente otorga a Nexova Digital Solutions S.A.S. una licencia limitada, no exclusiva y no transferible para procesar dichos contenidos únicamente con el fin de prestar el Servicio.

---

## 5. Disponibilidad del Servicio

5.1. Nexova Digital Solutions S.A.S. hará sus mejores esfuerzos para mantener el Servicio disponible de forma continua, sin garantizar un nivel de disponibilidad específico en el plan gratuito.

5.2. Podemos realizar mantenimientos programados notificando con al menos 24 horas de anticipación.

5.3. No seremos responsables por interrupciones causadas por terceros (proveedores de internet, servicios en la nube, casos de fuerza mayor).

---

## 6. Limitación de Responsabilidad

En la máxima medida permitida por la ley colombiana, Nexova Digital Solutions S.A.S. no será responsable por daños indirectos, incidentales, especiales o consecuentes derivados del uso o imposibilidad de uso del Servicio.

---

## 7. Legislación Aplicable y Resolución de Disputas

Estos Términos se rigen por las leyes de la República de Colombia. Cualquier controversia se someterá en primera instancia a mediación ante la Cámara de Comercio de Bogotá. De no lograrse acuerdo, será resuelta por los jueces competentes de Bogotá D.C.

El Servicio cumple con:
- **Ley 1480 de 2011** — Estatuto del Consumidor
- **Ley 527 de 1999** — Comercio Electrónico
- **Ley 1581 de 2012** — Protección de Datos Personales

---

## 8. Modificaciones

Nos reservamos el derecho de modificar estos Términos en cualquier momento. Los cambios serán notificados por correo electrónico o mediante aviso en la plataforma con al menos 15 días de anticipación.

---

## 9. Contacto

Para cualquier consulta relacionada con estos Términos:

- **Correo:** legal@nexovadesk.com
- **Sitio web:** [nexovadesk.com/p/contacto](/p/contacto)
- **Domicilio:** Bogotá D.C., Colombia
MD,
            ],

            [
                'slug'             => 'privacidad',
                'title'            => 'Política de Privacidad',
                'meta_title'       => 'Política de Privacidad — Nexova Desk',
                'meta_description' => 'Política de tratamiento de datos personales de Nexova Desk conforme a la Ley 1581 de 2012.',
                'status'           => 'published',
                'content'          => <<<'MD'
# Política de Tratamiento de Datos Personales

**Última actualización:** 31 de marzo de 2026

En cumplimiento de la **Ley 1581 de 2012**, el **Decreto 1377 de 2013** y demás normas concordantes sobre protección de datos personales en Colombia, **Nexova Digital Solutions S.A.S.** (en adelante "Nexova" o "el Responsable") informa su política de tratamiento de datos personales.

---

## 1. Responsable del Tratamiento

| Campo | Detalle |
|---|---|
| **Razón Social** | Nexova Digital Solutions S.A.S. |
| **Domicilio** | Bogotá D.C., Colombia |
| **Correo de datos** | datos@nexovadesk.com |
| **Sitio web** | nexovadesk.com |

---

## 2. Datos Personales que Recopilamos

Recopilamos los siguientes datos según el rol del titular:

**Clientes (administradores de la plataforma):**
- Nombre completo, correo electrónico, contraseña (almacenada en hash irreversible)
- Información de pago (procesada por pasarelas certificadas PCI-DSS; Nexova no almacena datos de tarjetas)
- Registros de uso y actividad dentro del panel

**Agentes:**
- Nombre, correo electrónico, foto de perfil (opcional)
- Registros de conversaciones atendidas

**Visitantes del widget (usuarios finales de los Clientes):**
- Mensajes de chat, nombre e email si el visitante los proporciona voluntariamente
- Dirección IP (seudonimizada), agente de usuario del navegador

---

## 3. Finalidad del Tratamiento

Los datos se tratan para:

1. Prestar el Servicio de soporte y atención al cliente.
2. Gestionar la relación contractual y facturación.
3. Enviar comunicaciones del servicio (alertas, facturas, actualizaciones críticas).
4. Mejorar y optimizar la plataforma mediante análisis agregados y anonimizados.
5. Cumplir con obligaciones legales y fiscales colombianas.
6. Prevenir fraudes y garantizar la seguridad de la plataforma.

No realizamos tratamiento de datos para perfilamiento comercial de terceros ni vendemos datos personales.

---

## 4. Base Legitimadora

El tratamiento se fundamenta en:
- **Ejecución del contrato** de servicio suscrito con el Cliente.
- **Consentimiento** del titular para comunicaciones de marketing (siempre revocable).
- **Obligación legal** derivada de normativa colombiana aplicable.
- **Interés legítimo** para la seguridad y mejora del Servicio.

---

## 5. Conservación de los Datos

Los datos se conservan mientras la cuenta esté activa y durante el período adicional exigido por obligaciones legales (mínimo 5 años para datos contables según normativa colombiana). Al eliminar la cuenta, los datos de chat se eliminan de los servidores activos en un plazo máximo de 30 días.

---

## 6. Transferencias de Datos

Nexova puede compartir datos con:

- **Proveedores de infraestructura cloud** bajo acuerdos de procesamiento de datos (AWS, Cloudflare). Estos proveedores actúan como encargados del tratamiento y no tienen acceso independiente a sus datos.
- **Autoridades competentes** cuando sea requerido por ley colombiana.

No realizamos transferencias internacionales de datos a países sin nivel adecuado de protección sin las salvaguardas correspondientes.

---

## 7. Derechos del Titular

De conformidad con la Ley 1581 de 2012, usted tiene derecho a:

- **Conocer** los datos que tenemos sobre usted.
- **Actualizar y rectificar** datos inexactos o incompletos.
- **Solicitar prueba** del consentimiento otorgado.
- **Revocar** el consentimiento en cualquier momento.
- **Suprimir** sus datos cuando no sean necesarios para la finalidad del tratamiento.
- **Presentar quejas** ante la **Superintendencia de Industria y Comercio (SIC)**: www.sic.gov.co

Para ejercer sus derechos, escriba a: **datos@nexovadesk.com** con asunto "Derechos ARCO" adjuntando copia de su documento de identidad. Atenderemos su solicitud en un plazo máximo de **10 días hábiles**.

---

## 8. Seguridad

Implementamos medidas técnicas y organizativas para proteger sus datos: cifrado TLS en tránsito, hashing de contraseñas (bcrypt), acceso basado en roles, registros de auditoría y copias de seguridad cifradas.

---

## 9. Cookies y Tecnologías Similares

Utilizamos cookies estrictamente necesarias para el funcionamiento del servicio y cookies analíticas (anonimizadas). Puede gestionar sus preferencias de cookies desde la configuración de su navegador.

---

## 10. Cambios en esta Política

Notificaremos cambios sustanciales con al menos 15 días de anticipación por correo electrónico.

---

## 10. Contacto y Quejas

**Delegado de Protección de Datos:**
- Correo: datos@nexovadesk.com
- Superintendencia de Industria y Comercio: www.sic.gov.co / Línea nacional: 01 8000 910165
MD,
            ],

            [
                'slug'             => 'habeas-data',
                'title'            => 'Habeas Data',
                'meta_title'       => 'Habeas Data — Nexova Desk',
                'meta_description' => 'Ejercicio del derecho de Habeas Data conforme a la Ley 1266 de 2008 y Ley 1581 de 2012.',
                'status'           => 'published',
                'content'          => <<<'MD'
# Derecho de Habeas Data

**Última actualización:** 31 de marzo de 2026

**Nexova Digital Solutions S.A.S.** reconoce y garantiza el derecho fundamental de **Habeas Data** consagrado en el artículo 15 de la Constitución Política de Colombia, desarrollado por la **Ley 1266 de 2008** y la **Ley 1581 de 2012**.

---

## ¿Qué es el Habeas Data?

Es el derecho que tiene toda persona a conocer, actualizar y rectificar la información que sobre ella exista en bases de datos o archivos de entidades públicas y privadas.

---

## Sus Derechos

Como titular de datos personales, usted tiene los siguientes derechos frente a Nexova Digital Solutions S.A.S.:

| Derecho | Descripción |
|---|---|
| **Conocer** | Acceder gratuitamente a sus datos personales que reposan en nuestras bases de datos. |
| **Actualizar** | Solicitar la corrección de datos inexactos, incompletos o que induzcan a error. |
| **Rectificar** | Exigir la corrección de datos que no corresponden a la realidad. |
| **Suprimir** | Solicitar la eliminación de sus datos cuando no exista obligación legal de conservarlos. |
| **Revocar** | Retirar el consentimiento otorgado para el tratamiento de sus datos. |
| **Quejarse** | Presentar quejas ante la Superintendencia de Industria y Comercio. |

---

## Cómo Ejercer sus Derechos

**Paso 1 — Envíe su solicitud escrita a:**

- **Correo electrónico:** datos@nexovadesk.com
- **Asunto:** "Solicitud Habeas Data — [su nombre completo]"

**Paso 2 — Incluya en su solicitud:**
- Nombre completo y número de documento de identidad.
- Descripción clara de los datos sobre los que ejerce el derecho.
- Tipo de solicitud (conocer, actualizar, rectificar, suprimir o revocar).
- Copia de su documento de identidad.

**Paso 3 — Tiempos de respuesta:**

| Tipo de solicitud | Plazo máximo |
|---|---|
| Consulta (conocer datos) | 10 días hábiles |
| Reclamo (rectificar / suprimir / revocar) | 15 días hábiles (prorrogables 8 días más con notificación) |

---

## Datos que Tratamos

Nexova procesa únicamente los datos necesarios para la prestación del servicio de soporte al cliente. Consulte nuestra [Política de Privacidad](/p/privacidad) para el detalle completo.

---

## Autoridad de Control

Si considera que sus derechos no han sido atendidos adecuadamente, puede presentar queja ante la:

**Superintendencia de Industria y Comercio (SIC)**
- Sitio web: [www.sic.gov.co](https://www.sic.gov.co)
- Línea gratuita nacional: **01 8000 910165**
- Correo: contactenos@sic.gov.co
- Dirección: Carrera 13 No. 27-00 Pisos 1-5, Bogotá D.C.

---

## Marco Legal

Esta política se rige por:

- **Constitución Política de Colombia** — Artículo 15 (Derecho a la intimidad y al buen nombre)
- **Ley 1266 de 2008** — Disposiciones generales del Habeas Data
- **Ley 1581 de 2012** — Protección de Datos Personales
- **Decreto 1377 de 2013** — Reglamentación parcial de la Ley 1581 de 2012
- **Decreto 1074 de 2015** — Decreto Único Reglamentario del Sector Comercio
MD,
            ],

            [
                'slug'             => 'contacto',
                'title'            => 'Contacto',
                'meta_title'       => 'Contacto — Nexova Desk',
                'meta_description' => 'Ponte en contacto con el equipo de Nexova Desk. Soporte, ventas y consultas generales.',
                'status'           => 'published',
                'content'          => <<<'MD'
# Contáctanos

Estamos aquí para ayudarte. Elige el canal que mejor se adapte a tu necesidad.

---

## Soporte Técnico

¿Tienes un problema con la plataforma o necesitas asistencia?

- **Chat en vivo:** Usa el widget en la esquina inferior derecha de esta página.
- **Correo:** soporte@nexovadesk.com
- **Horario de atención:** Lunes a viernes, 8:00 a.m. – 6:00 p.m. (hora Colombia, UTC-5)

---

## Ventas y Planes

¿Quieres conocer más sobre nuestros planes o necesitas una cotización personalizada?

- **Correo:** ventas@nexovadesk.com

---

## Datos Personales y Habeas Data

Para solicitudes relacionadas con el tratamiento de sus datos personales:

- **Correo:** datos@nexovadesk.com
- **Asunto sugerido:** "Solicitud Habeas Data — [su nombre]"
- Consulte nuestra [Política de Privacidad](/p/privacidad) y la página de [Habeas Data](/p/habeas-data).

---

## Información Legal

**Nexova Digital Solutions S.A.S.**
- **Domicilio:** Bogotá D.C., Colombia
- **Correo legal:** legal@nexovadesk.com

---

## Redes Sociales

Síguenos y escríbenos por nuestras redes:

- LinkedIn: linkedin.com/company/nexovadigital
- Instagram: @nexovadigital

---

*Tiempo de respuesta estimado: máximo 1 día hábil para soporte y 2 días hábiles para consultas de ventas.*
MD,
            ],
        ];

        foreach ($pages as $data) {
            Page::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }

        $this->command->info('Legal pages seeded: terminos, privacidad, habeas-data, contacto');
    }
}
