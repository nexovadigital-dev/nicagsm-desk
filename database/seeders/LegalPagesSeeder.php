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
                'meta_description' => 'Términos y condiciones de uso de Nexova Desk. Leemos la Ley 1480 de 2011 y la Ley 527 de 1999 para protegerte.',
                'status'           => 'published',
                'content'          => <<<'MD'
# Términos y Condiciones de Uso

**Última actualización:** 1 de abril de 2026

Bienvenido a **Nexova Desk**. Antes de empezar a usar el servicio, le pedimos que lea con atención estos términos. Son el acuerdo entre usted y nosotros, y definen cómo funciona la relación.

Si tiene preguntas, escríbanos a **legal@nexovadesk.com** — respondemos en máximo un día hábil.

---

## 1. ¿Quiénes somos?

Nexova Desk es un servicio de chat en vivo e inteligencia artificial para soporte al cliente, operado bajo el nombre comercial **Nexova Digital Solutions**, con actividad en la República de Colombia.

---

## 2. ¿A quién aplican estos términos?

A toda persona que cree una cuenta, instale el widget, o utilice cualquier función de la plataforma — ya sea como organización contratante, agente de soporte, o visitante del chat.

Cuando decimos **"usted"** o **"el cliente"**, nos referimos a la persona o empresa que tiene la cuenta. Cuando decimos **"visitante"**, nos referimos a quien usa el chat en su sitio web.

---

## 3. Cómo funciona el servicio

Nexova Desk le permite:
- Instalar un widget de chat en su sitio web o tienda en línea.
- Configurar un bot con inteligencia artificial para responder preguntas frecuentes.
- Recibir y atender conversaciones desde un panel unificado junto a su equipo.
- Conectar canales adicionales como Telegram.

El servicio funciona sobre infraestructura en la nube. Hacemos nuestro mejor esfuerzo para mantenerlo disponible 24/7, aunque pueden ocurrir ventanas de mantenimiento programado — siempre avisamos con al menos 24 horas de anticipación.

---

## 4. Su cuenta y sus credenciales

Al registrarse, usted acepta proporcionar información veraz y mantenerla actualizada. Es responsable de la confidencialidad de su contraseña y de todo lo que ocurra bajo su cuenta.

Si detecta un acceso no autorizado, avísenos de inmediato a **soporte@nexovadesk.com**.

---

## 5. Uso aceptable

Puede usar Nexova Desk para cualquier negocio legítimo. No está permitido:

- Enviar spam, mensajes masivos no solicitados, o contenido engañoso.
- Usar el bot para recopilar datos personales sin consentimiento.
- Intentar acceder a cuentas o datos de otras organizaciones.
- Usar el servicio para actividades que infrinjan la ley colombiana o los derechos de terceros.

Si incumple estas condiciones, podemos suspender o cancelar su cuenta sin previo aviso, sin perjuicio de las acciones legales que correspondan.

---

## 6. Planes, precios y pagos

Ofrecemos un plan gratuito con funciones básicas y planes de pago con más capacidades. Los precios están en dólares estadounidenses (USD) o pesos colombianos (COP) según se indique en la página de precios.

Los planes de pago se cobran de forma recurrente (mensual o anual). Al contratar un plan, usted autoriza los cobros periódicos hasta que cancele.

**Derecho de retracto:** Conforme a la **Ley 1480 de 2011 (Estatuto del Consumidor)**, tiene derecho a retractarse dentro de los 5 días hábiles siguientes a la contratación, siempre que no haya hecho uso extensivo del servicio. Para ejercerlo, escríbanos a legal@nexovadesk.com.

No hacemos reembolsos parciales por períodos no utilizados fuera del derecho de retracto.

---

## 7. Sus datos y los de sus visitantes

Usted conserva la propiedad de todos los datos que cargue o transmita a través del servicio. Nosotros los procesamos únicamente para prestarle el servicio, conforme a nuestra [Política de Privacidad](/p/privacidad).

Como responsable del tratamiento de los datos de sus visitantes, es su obligación informarles sobre el uso del chat e implementar los avisos de privacidad que exija la ley.

---

## 8. Propiedad intelectual

El código, diseño, marca y logotipos de Nexova Desk son de nuestra propiedad o de nuestros licenciantes. No puede copiarlos, modificarlos ni usarlos fuera del contexto del servicio.

---

## 9. Limitación de responsabilidad

En la medida que la ley lo permita, Nexova Digital Solutions no responde por daños indirectos, pérdida de ingresos o datos, derivados del uso o la imposibilidad de uso del servicio. Nuestra responsabilidad máxima no superará el valor pagado por el servicio en los últimos 3 meses.

---

## 10. Modificaciones

Podemos actualizar estos términos. Le avisaremos por correo electrónico o mediante un aviso en la plataforma con al menos **15 días de anticipación** antes de que entren en vigencia. Si continúa usando el servicio después de ese plazo, entendemos que acepta los cambios.

---

## 11. Ley aplicable

Estos términos se rigen por las leyes de la República de Colombia, incluyendo:
- **Ley 1480 de 2011** — Estatuto del Consumidor
- **Ley 527 de 1999** — Comercio Electrónico
- **Ley 1581 de 2012** — Protección de Datos Personales

Cualquier controversia se resolverá entre las partes de forma directa y, si no hay acuerdo, ante los jueces competentes de Colombia.

---

## 12. Contacto

- **Correo:** legal@nexovadesk.com
- **Soporte:** soporte@nexovadesk.com
- **Sitio:** [nexovadesk.com/p/contacto](/p/contacto)
MD,
            ],

            [
                'slug'             => 'privacidad',
                'title'            => 'Política de Privacidad',
                'meta_title'       => 'Política de Privacidad — Nexova Desk',
                'meta_description' => 'Cómo tratamos sus datos personales en Nexova Desk. Cumplimos con la Ley 1581 de 2012 de protección de datos personales.',
                'status'           => 'published',
                'content'          => <<<'MD'
# Política de Tratamiento de Datos Personales

**Última actualización:** 1 de abril de 2026

En **Nexova Digital Solutions** (nombre comercial, titular de la marca Nexova Desk) nos tomamos muy en serio la privacidad de quienes usan nuestra plataforma. Esta política explica qué datos recopilamos, para qué los usamos y cómo los protegemos, en cumplimiento de la **Ley 1581 de 2012** y el **Decreto 1377 de 2013**.

Si tiene preguntas sobre cómo manejamos sus datos, escríbanos a **datos@nexovadesk.com**.

---

## 1. Responsable del tratamiento

| | |
|---|---|
| **Nombre comercial** | Nexova Digital Solutions |
| **Marca registrada** | Nexova Desk |
| **País de operación** | Colombia |
| **Correo de datos personales** | datos@nexovadesk.com |
| **Sitio web** | nexovadesk.com |

---

## 2. Qué datos recopilamos

Dependiendo de cómo use el servicio, tratamos diferentes tipos de datos:

**Si usted es cliente (administrador de una cuenta):**
- Nombre, correo electrónico y contraseña (almacenada en hash bcrypt — nunca en texto plano).
- Información de facturación procesada por pasarelas certificadas. Nexova Desk **no almacena datos de tarjetas de crédito**.
- Registros de uso: cuándo inicia sesión, qué funciones utiliza, con qué frecuencia.

**Si usted es agente de soporte:**
- Nombre, correo electrónico y foto de perfil (opcional).
- Conversaciones atendidas y tiempos de respuesta (para métricas internas).

**Si usted es visitante de un chat instalado por uno de nuestros clientes:**
- Los mensajes que envía en el chat.
- Nombre y correo electrónico, si los proporciona voluntariamente.
- Dirección IP (seudonimizada) y agente de usuario del navegador.

---

## 3. Para qué usamos sus datos

Usamos sus datos exclusivamente para:

1. Prestarle el servicio de soporte al cliente que contrató.
2. Gestionar su cuenta, facturación y renovaciones.
3. Enviarle comunicaciones del servicio: actualizaciones importantes, facturas y alertas de seguridad.
4. Mejorar la plataforma con análisis agregados y anonimizados (nunca a nivel individual).
5. Cumplir con obligaciones legales colombianas.
6. Prevenir fraudes y proteger la seguridad de todos los usuarios.

**No vendemos sus datos.** Nunca. A nadie.

---

## 4. Base legal del tratamiento

Tratamos sus datos basándonos en:

- **Ejecución del contrato:** Para prestarle el servicio que contrató.
- **Consentimiento:** Para comunicaciones de marketing, que puede retirar en cualquier momento.
- **Obligación legal:** Cuando la ley colombiana nos lo exige.
- **Interés legítimo:** Para seguridad de la plataforma y mejora del servicio.

---

## 5. Por cuánto tiempo conservamos sus datos

Mientras su cuenta esté activa. Si cancela o elimina su cuenta, eliminamos sus datos de los servidores activos en un plazo máximo de **30 días**, salvo lo que debamos conservar por obligaciones legales (por ejemplo, registros de facturación que exige la DIAN).

---

## 6. Con quién compartimos sus datos

Solo compartimos datos en estos casos:

- **Proveedores de infraestructura** (servicios de hosting, Cloudflare) que actúan como encargados del tratamiento bajo nuestras instrucciones y sin acceso independiente a sus datos.
- **Autoridades colombianas** cuando una norma o una orden judicial nos lo exija.

No compartimos datos con terceros para fines comerciales propios.

---

## 7. Sus derechos como titular de datos

Conforme a la **Ley 1581 de 2012**, usted puede en cualquier momento:

- **Conocer** qué datos tenemos sobre usted.
- **Actualizar o rectificar** información inexacta.
- **Solicitar prueba** del consentimiento que otorgó.
- **Revocar** su consentimiento para comunicaciones de marketing.
- **Solicitar la supresión** de sus datos cuando no sean necesarios para la finalidad original.
- **Presentar una queja** ante la **Superintendencia de Industria y Comercio (SIC)**: [www.sic.gov.co](https://www.sic.gov.co) / Línea gratuita: 01 8000 910165.

Para ejercer cualquiera de estos derechos, escríbanos a **datos@nexovadesk.com** con el asunto **"Derechos ARCO"** e incluya una copia de su documento de identidad. Atendemos su solicitud en máximo **10 días hábiles**.

---

## 8. Seguridad

Implementamos medidas técnicas y organizativas acordes con el riesgo del tratamiento:

- Cifrado TLS en todas las comunicaciones.
- Contraseñas almacenadas con bcrypt.
- Acceso a datos basado en roles (nadie ve más de lo que necesita).
- Auditorías de acceso y copias de seguridad cifradas.
- Revisiones periódicas de seguridad.

---

## 9. Cookies

Usamos cookies estrictamente necesarias para el funcionamiento del servicio (sesión, seguridad) y cookies analíticas anonimizadas para entender cómo se usa la plataforma. Puede gestionar las cookies desde la configuración de su navegador.

---

## 10. Cambios en esta política

Si hacemos cambios importantes, le avisaremos por correo electrónico con al menos **15 días de anticipación**.

---

## 11. Contacto

- **Correo de datos personales:** datos@nexovadesk.com
- **Superintendencia de Industria y Comercio:** [www.sic.gov.co](https://www.sic.gov.co)
MD,
            ],

            [
                'slug'             => 'habeas-data',
                'title'            => 'Habeas Data',
                'meta_title'       => 'Habeas Data — Nexova Desk',
                'meta_description' => 'Cómo ejercer su derecho de Habeas Data ante Nexova Desk, conforme a la Ley 1266 de 2008 y la Ley 1581 de 2012.',
                'status'           => 'published',
                'content'          => <<<'MD'
# Habeas Data

**Nexova Digital Solutions**, nombre comercial titular de la marca **Nexova Desk**, garantiza el derecho fundamental de Habeas Data consagrado en el **artículo 15 de la Constitución Política de Colombia**, reglamentado por la **Ley 1266 de 2008** y la **Ley 1581 de 2012**.

---

## ¿Qué es el Habeas Data?

Es su derecho a saber qué información tienen sobre usted, corregirla si está mal, y pedir que la eliminen cuando ya no sea necesaria. En Colombia es un derecho fundamental, no un trámite opcional.

---

## Sus derechos frente a Nexova Digital Solutions

| Derecho | En qué consiste |
|---|---|
| **Conocer** | Acceder gratuitamente a sus datos personales en nuestras bases de datos. |
| **Actualizar** | Corregir datos desactualizados o incompletos. |
| **Rectificar** | Exigir la corrección de datos incorrectos o que no corresponden a la realidad. |
| **Suprimir** | Pedir la eliminación de sus datos cuando no exista obligación legal de conservarlos. |
| **Revocar** | Retirar el consentimiento que otorgó para el tratamiento de sus datos. |
| **Quejarse** | Presentar una queja ante la Superintendencia de Industria y Comercio si considera que no atendimos su solicitud correctamente. |

---

## Cómo hacer una solicitud

**1. Envíe un correo a datos@nexovadesk.com** con:
- Asunto: `Solicitud Habeas Data — [su nombre completo]`
- Su nombre completo y número de documento de identidad.
- Descripción clara de los datos sobre los que ejerce el derecho.
- Tipo de solicitud: conocer, actualizar, rectificar, suprimir o revocar.
- Copia de su documento de identidad (puede enviarla como adjunto).

**2. Tiempos de respuesta:**

| Tipo de solicitud | Plazo de respuesta |
|---|---|
| Consulta (conocer sus datos) | Máximo **10 días hábiles** |
| Reclamo (rectificar / suprimir / revocar) | Máximo **15 días hábiles** (prorrogables 8 días adicionales con notificación previa) |

Respondemos siempre al correo desde el que hizo la solicitud.

---

## Qué datos tratamos

Solo tratamos los datos necesarios para prestar el servicio de soporte al cliente. Consulte nuestra [Política de Privacidad](/p/privacidad) para el detalle completo de qué datos recopilamos, para qué los usamos y con quién los compartimos.

---

## Autoridad de control

Si considera que no atendimos su solicitud de forma adecuada, puede presentar una queja directamente ante:

**Superintendencia de Industria y Comercio (SIC)**
- Sitio web: [www.sic.gov.co](https://www.sic.gov.co)
- Línea gratuita nacional: **01 8000 910165**
- Correo: contactenos@sic.gov.co
- Dirección: Carrera 13 No. 27-00, pisos 1–5, Bogotá D.C.

---

## Marco legal

- **Constitución Política de Colombia** — Artículo 15 (derecho a la intimidad y al buen nombre)
- **Ley 1266 de 2008** — Disposiciones generales del Habeas Data financiero
- **Ley 1581 de 2012** — Protección de Datos Personales
- **Decreto 1377 de 2013** — Reglamentación parcial de la Ley 1581
- **Decreto 1074 de 2015** — Decreto Único Reglamentario del Sector Comercio
MD,
            ],

            [
                'slug'             => 'contacto',
                'title'            => 'Contacto',
                'meta_title'       => 'Contacto — Nexova Desk',
                'meta_description' => 'Contáctenos. Soporte técnico, ventas y solicitudes de datos personales. Le respondemos en máximo 1 día hábil.',
                'status'           => 'published',
                'content'          => <<<'MD'
# Contáctenos

Estamos disponibles de lunes a viernes de **8:00 a.m. a 6:00 p.m.** (hora Colombia, UTC−5). Para lo urgente, use el chat en vivo en la esquina inferior derecha de esta página.

---

## Soporte técnico

¿Algo no funciona o tiene preguntas sobre la plataforma?

- **Chat en vivo:** Widget en esta página — respuesta inmediata en horario hábil.
- **Correo:** soporte@nexovadesk.com
- **Tiempo de respuesta:** Máximo 1 día hábil.

---

## Ventas y planes

¿Quiere conocer las opciones para su empresa o necesita una cotización?

- **Correo:** ventas@nexovadesk.com
- **Tiempo de respuesta:** Máximo 2 días hábiles.

---

## Datos personales y Habeas Data

Para solicitudes de acceso, rectificación, supresión o revocación de datos:

- **Correo:** datos@nexovadesk.com
- **Asunto sugerido:** `Solicitud Habeas Data — [su nombre]`
- Consulte el proceso detallado en nuestra página de [Habeas Data](/p/habeas-data).

---

## Asuntos legales

Para contratos, términos y consultas legales:

- **Correo:** legal@nexovadesk.com

---

## Nexova Digital Solutions

Nombre comercial · Actividad en Colombia
- **Correo general:** hola@nexovadesk.com

---

*Le respondemos siempre desde el canal que usted usa para escribirnos.*
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
