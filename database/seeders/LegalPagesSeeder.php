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
                'meta_description' => 'Términos y condiciones de uso de Nexova Desk. Ley 1480 de 2011 y Ley 527 de 1999.',
                'status'           => 'published',
                'content'          => <<<'MD'
# Términos y Condiciones de Uso

**Última actualización:** 1 de abril de 2026

En Nexova Desk creamos una plataforma para que las empresas atiendan mejor a sus clientes — con chat en vivo, inteligencia artificial y un panel unificado para su equipo. Estos Términos son el acuerdo entre usted y nosotros. Le pedimos leerlos; son claros y no tienen letra pequeña.

¿Tiene preguntas? Escríbanos a **legal@nexovadesk.com** — respondemos en máximo un día hábil.

---

## ¿Quiénes somos?

Nexova Desk es operado bajo el nombre comercial **Nexova Digital Solutions**, con actividad en la República de Colombia.

---

## ¿A quién aplican estos términos?

A toda persona que cree una cuenta, instale el widget o use cualquier función de la plataforma — ya sea como empresa contratante, agente de soporte o visitante del chat.

Cuando decimos **"usted"** o **"el cliente"**, nos referimos a la persona o empresa que tiene la cuenta. Cuando decimos **"visitante"**, nos referimos a quien usa el chat instalado en su sitio web.

---

## ¿Qué ofrece Nexova Desk?

En Nexova Desk usted puede:

- Instalar un widget de chat en su sitio web o tienda en línea.
- Configurar un bot con inteligencia artificial para responder preguntas frecuentes.
- Recibir y gestionar conversaciones desde un panel unificado con su equipo.
- Conectar canales adicionales como Telegram.
- Crear tickets de soporte y hacer seguimiento por correo.

El servicio funciona sobre infraestructura en la nube. Hacemos nuestro mejor esfuerzo para mantenerlo disponible 24/7. Si hay mantenimiento programado, avisamos con al menos 24 horas de anticipación.

---

## Registro y seguridad de su cuenta

Al registrarse, usted acepta proporcionar información veraz y mantenerla actualizada. Es responsable de la confidencialidad de su contraseña y de lo que ocurra bajo su cuenta.

Si detecta un acceso no autorizado, avísenos de inmediato a **soporte@nexovadesk.com**.

---

## Uso aceptable del servicio

Puede usar Nexova Desk para cualquier negocio legítimo. No está permitido:

- Enviar spam o mensajes masivos no solicitados.
- Usar el bot para recopilar datos personales sin consentimiento.
- Intentar acceder a cuentas o datos de otras organizaciones.
- Usar el servicio para actividades que infrinjan la ley colombiana o los derechos de terceros.

Si incumple estas condiciones, podemos suspender o cancelar su cuenta, sin perjuicio de las acciones legales que correspondan.

---

## Planes, precios y pagos

Ofrecemos un plan gratuito con funciones básicas y planes de pago con mayor capacidad. Los precios están expresados en dólares estadounidenses (USD) o pesos colombianos (COP) según se indique en la página de precios.

Los planes de pago se cobran de forma recurrente (mensual o anual). Al contratar un plan, usted autoriza los cobros periódicos hasta que cancele.

**Derecho de retracto:** Conforme a la **Ley 1480 de 2011 (Estatuto del Consumidor)**, tiene derecho a retractarse dentro de los 5 días hábiles siguientes a la contratación, siempre que no haya hecho uso extensivo del servicio. Para ejercerlo, escríbanos a legal@nexovadesk.com.

No hacemos reembolsos parciales por períodos no utilizados fuera del plazo de retracto.

---

## Sus datos y los de sus visitantes

Usted conserva la propiedad de todos los datos que cargue o transmita a través del servicio. Nosotros los procesamos únicamente para prestarle el servicio, conforme a nuestra [Política de Privacidad](/p/privacidad).

Como empresa que instala el widget en su sitio, es su responsabilidad informar a sus visitantes sobre el uso del chat e implementar los avisos de privacidad que exija la ley.

---

## Propiedad intelectual

El código, diseño, marca y logotipos de Nexova Desk son de nuestra propiedad o de nuestros licenciantes. No puede copiarlos, modificarlos ni usarlos fuera del contexto del servicio.

---

## Limitación de responsabilidad

En la medida que la ley lo permita, Nexova Digital Solutions no responde por daños indirectos, pérdida de ingresos o datos derivados del uso o la imposibilidad de uso del servicio. Nuestra responsabilidad máxima no superará el valor pagado en los últimos 3 meses.

---

## Cambios a estos términos

Podemos actualizar estos términos. Le avisaremos por correo electrónico o mediante un aviso en la plataforma con al menos **15 días de anticipación** antes de que entren en vigencia. Si continúa usando el servicio después de ese plazo, entendemos que acepta los cambios.

---

## Ley aplicable

Estos términos se rigen por las leyes de la República de Colombia, incluyendo la **Ley 1480 de 2011** (Estatuto del Consumidor), la **Ley 527 de 1999** (Comercio Electrónico) y la **Ley 1581 de 2012** (Protección de Datos Personales). Cualquier controversia se resolverá entre las partes de forma directa y, si no hay acuerdo, ante los jueces competentes de Colombia.

---

## PQRS — Peticiones, quejas, reclamos y solicitudes

Si tiene alguna petición, queja, reclamo o sugerencia relacionada con el servicio, puede escribirnos a **soporte@nexovadesk.com** o **legal@nexovadesk.com**. Respondemos en máximo 1 día hábil para soporte técnico y 3 días hábiles para asuntos contractuales.

MD,
            ],

            [
                'slug'             => 'privacidad',
                'title'            => 'Política de Privacidad',
                'meta_title'       => 'Política de Privacidad — Nexova Desk',
                'meta_description' => 'Cómo tratamos sus datos personales en Nexova Desk. Cumplimos con la Ley 1581 de 2012.',
                'status'           => 'published',
                'content'          => <<<'MD'
# Política de Privacidad

**Última actualización:** 1 de abril de 2026

En Nexova Desk nos tomamos muy en serio la privacidad de quienes usan nuestra plataforma. Esta política explica qué datos recopilamos, para qué los usamos y cómo los protegemos, en cumplimiento de la **Ley 1581 de 2012** y el **Decreto 1377 de 2013**.

¿Tiene preguntas? Escríbanos a **datos@nexovadesk.com** — respondemos siempre.

---

## ¿Quién responde por sus datos?

El responsable del tratamiento de sus datos personales es:

| | |
|---|---|
| **Nombre comercial** | Nexova Digital Solutions |
| **Marca** | Nexova Desk |
| **País** | Colombia |
| **Correo de datos personales** | datos@nexovadesk.com |
| **Sitio web** | nexovadesk.com |

En Nexova Desk somos quienes decidimos cómo y para qué se usan sus datos. Operamos como nombre comercial con actividad en Colombia y cumplimos con la normativa colombiana de protección de datos.

---

## ¿Qué datos recopilamos?

Depende de cómo use el servicio:

**Si usted es cliente (tiene una cuenta):**
- Nombre, correo electrónico y contraseña (almacenada con cifrado bcrypt — nunca en texto plano).
- Información de pago procesada por pasarelas certificadas. En Nexova Desk **no almacenamos datos de tarjetas de crédito**.
- Registros de uso: cuándo inicia sesión, qué funciones utiliza.

**Si usted es agente de soporte:**
- Nombre, correo electrónico y foto de perfil (opcional).
- Conversaciones atendidas (para métricas de su organización).

**Si usted es visitante de un chat instalado por uno de nuestros clientes:**
- Los mensajes que envía en el chat.
- Nombre y correo electrónico, si los proporciona voluntariamente.
- Dirección IP (seudonimizada) y tipo de navegador.

---

## ¿Para qué usamos sus datos?

En Nexova Desk usamos sus datos exclusivamente para:

1. Prestarle el servicio que contrató.
2. Gestionar su cuenta, facturación y renovaciones.
3. Enviarle comunicaciones del servicio: actualizaciones importantes, facturas y alertas de seguridad.
4. Mejorar la plataforma con análisis agregados y anonimizados — nunca a nivel individual.
5. Cumplir con obligaciones legales colombianas.
6. Prevenir fraudes y proteger la seguridad de la plataforma.

**En Nexova Desk no vendemos sus datos. Nunca. A nadie.**

---

## ¿Con quién compartimos sus datos?

Solo en estos casos:

- **Proveedores de infraestructura** (servicios de hosting, Cloudflare) que actúan como encargados del tratamiento bajo nuestras instrucciones, sin acceso independiente a sus datos.
- **Autoridades colombianas** cuando una norma o una orden judicial nos lo exija.

No compartimos datos con terceros para fines comerciales propios.

---

## ¿Por cuánto tiempo conservamos sus datos?

Mientras su cuenta esté activa. Si cancela o elimina su cuenta, eliminamos sus datos de los servidores activos en un plazo máximo de **30 días**, salvo lo que debamos conservar por obligaciones legales (como registros de facturación).

---

## ¿Cómo protegemos su información?

En Nexova Desk implementamos medidas técnicas acordes con el riesgo del tratamiento:

- Cifrado TLS en todas las comunicaciones.
- Contraseñas almacenadas con bcrypt.
- Acceso a datos basado en roles — nadie accede a más de lo que necesita.
- Copias de seguridad cifradas.
- Revisiones periódicas de seguridad.

---

## ¿Usamos cookies?

Usamos cookies estrictamente necesarias para el funcionamiento del servicio (sesión, seguridad) y cookies analíticas anonimizadas para entender cómo se usa la plataforma. Puede gestionar las cookies desde la configuración de su navegador.

---

## ¿Cuáles son sus derechos como titular de datos?

Conforme a la **Ley 1581 de 2012**, usted puede en cualquier momento:

- **Conocer** qué datos tenemos sobre usted y para qué los usamos.
- **Actualizar o rectificar** información inexacta o incompleta.
- **Solicitar prueba** del consentimiento que otorgó.
- **Revocar** su consentimiento para comunicaciones de marketing.
- **Solicitar la supresión** de sus datos cuando no sean necesarios para la finalidad original.
- **Presentar una queja** ante la Superintendencia de Industria y Comercio (SIC).

Para ejercer cualquiera de estos derechos, escríbanos a **datos@nexovadesk.com** con el asunto **"Solicitud Habeas Data"**. Respondemos en máximo **10 días hábiles**, tal como lo exige la ley.

---

## Cambios a esta política

Si hacemos cambios importantes, le avisaremos por correo electrónico con al menos **15 días de anticipación**.

---

## Autoridad de control

**Superintendencia de Industria y Comercio (SIC)**
Sitio web: [www.sic.gov.co](https://www.sic.gov.co) · Línea gratuita: 01 8000 910165

MD,
            ],

            [
                'slug'             => 'habeas-data',
                'title'            => 'Habeas Data',
                'meta_title'       => 'Habeas Data — Nexova Desk',
                'meta_description' => 'Cómo ejercer su derecho de Habeas Data ante Nexova Desk. Ley 1266 de 2008 y Ley 1581 de 2012.',
                'status'           => 'published',
                'content'          => <<<'MD'
# Habeas Data

En Nexova Desk cumplimos con la **Ley 1581 de 2012** — la ley colombiana de protección de datos personales — y reconocemos el derecho fundamental de Habeas Data consagrado en el **artículo 15 de la Constitución Política de Colombia**.

Esta página explica cómo puede usted ejercer sus derechos sobre los datos personales que tenemos.

---

## ¿Qué es el Habeas Data?

Es su derecho a saber qué información tenemos sobre usted, corregirla si está incorrecta, y pedir que la eliminemos cuando ya no sea necesaria. En Colombia es un derecho fundamental — no un trámite opcional.

---

## ¿Cuáles son sus derechos?

Como titular de sus datos personales frente a **Nexova Digital Solutions**, usted tiene derecho a:

- **Conocer** sus datos personales que reposan en nuestras bases de datos, de forma gratuita.
- **Actualizar** datos desactualizados o incompletos.
- **Rectificar** datos incorrectos o que no corresponden a la realidad.
- **Solicitar prueba** de la autorización que nos otorgó para tratar sus datos.
- **Revocar** el consentimiento y solicitar que dejemos de usar sus datos para ciertos fines.
- **Suprimir** sus datos cuando no exista obligación legal de conservarlos.
- **Quejarse** ante la Superintendencia de Industria y Comercio (SIC) si considera que no atendimos su solicitud correctamente.

---

## ¿Cómo hacer una solicitud?

Escríbanos a **datos@nexovadesk.com** con:

- **Asunto:** `Solicitud Habeas Data — [su nombre completo]`
- Su nombre completo y número de documento de identidad.
- Descripción clara de los datos sobre los que ejerce el derecho.
- Tipo de solicitud: conocer, actualizar, rectificar, suprimir o revocar.
- Copia de su documento de identidad (como adjunto al correo).

Respondemos siempre al correo desde el que hizo la solicitud.

---

## ¿En cuánto tiempo respondemos?

| Tipo de solicitud | Plazo máximo |
|---|---|
| Consulta — conocer sus datos | **10 días hábiles** |
| Reclamo — rectificar, suprimir o revocar | **15 días hábiles** (prorrogables 8 días adicionales con notificación previa) |

Estos plazos son los exigidos por la **Ley 1581 de 2012**.

---

## ¿Qué datos tratamos?

Solo tratamos los datos necesarios para prestar el servicio de soporte al cliente. Consulte nuestra [Política de Privacidad](/p/privacidad) para el detalle completo.

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

- Constitución Política de Colombia — Artículo 15
- Ley 1581 de 2012 — Protección de Datos Personales
- Decreto 1377 de 2013 — Reglamentario de la Ley 1581
- Ley 1266 de 2008 — Habeas Data general
- Decreto 1074 de 2015 — Decreto Único Reglamentario del Sector Comercio

MD,
            ],

            [
                'slug'             => 'contacto',
                'title'            => 'Contacto',
                'meta_title'       => 'Contacto — Nexova Desk',
                'meta_description' => 'Contáctenos. Soporte técnico, ventas, PQRS y datos personales. Respondemos en máximo 1 día hábil.',
                'status'           => 'published',
                'content'          => <<<'MD'
# Contáctenos

En Nexova Desk estamos disponibles de lunes a viernes de **8:00 a.m. a 6:00 p.m.** (hora Colombia, UTC−5). Para lo urgente, use el chat en vivo en la esquina inferior derecha de esta página — respuesta inmediata en horario hábil.

---

## Soporte técnico

¿Algo no funciona o tiene preguntas sobre la plataforma?

- **Chat en vivo:** Widget en esta página.
- **Correo:** soporte@nexovadesk.com
- **Tiempo de respuesta:** Máximo 1 día hábil.

---

## Ventas y planes

¿Quiere conocer las opciones para su empresa o necesita una cotización personalizada?

- **Correo:** ventas@nexovadesk.com
- **Tiempo de respuesta:** Máximo 2 días hábiles.

---

## PQRS — Peticiones, quejas, reclamos y solicitudes

¿Tiene alguna petición, queja o reclamo sobre el servicio? En Nexova Desk atendemos todos los casos.

- **Correo:** soporte@nexovadesk.com
- **Asunto sugerido:** `PQRS — [descripción breve]`
- **Tiempo de respuesta:** Máximo 3 días hábiles.

---

## Datos personales y Habeas Data

Para solicitudes de acceso, rectificación, supresión o revocación de sus datos:

- **Correo:** datos@nexovadesk.com
- **Asunto:** `Solicitud Habeas Data — [su nombre]`
- Consulte el proceso detallado en nuestra página de [Habeas Data](/p/habeas-data).
- **Tiempo de respuesta:** 10 días hábiles (consulta) / 15 días hábiles (reclamo).

---

## Asuntos legales y contratos

- **Correo:** legal@nexovadesk.com
- **Tiempo de respuesta:** Máximo 3 días hábiles.

---

## Nexova Digital Solutions

Nombre comercial con actividad en Colombia.

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
