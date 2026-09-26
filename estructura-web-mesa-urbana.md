# Estructura Web — MESA URBANA SAS

Guía completa de la landing page para ordenar su construcción sección por sección en Claude Code. Cada bloque incluye propósito, contenido necesario y elementos de interfaz/interacción.

## Datos base de la empresa (para usar en el contenido)
- **Empresa:** MESA URBANA SAS
- **Socios:** Helena Guerrero, Carlos Prada y Rafael Serrano — 33% de participación cada uno
- **Cobertura:** Corredor Ibagué–Bogotá
- **Líneas de servicio:** Diseño arquitectónico, urbanismo, consultorías, construcción, soluciones inmobiliarias, visualización arquitectónica

## Navegación (menú fijo)
`Inicio` `Portafolio` `Servicios` `Quiénes somos` `Recursos` `Blog` `Contacto`
- Menú tipo sticky/fixed con cambio de estilo al hacer scroll (fondo transparente → sólido)
- Botón CTA destacado en el menú: "Agenda una consultoría"

---

## 1. Hero (Inicio)
**Propósito:** posicionar a MESA URBANA en 3 segundos.
**Contenido:**
- Titular corto y directo (propuesta de valor, no un eslogan genérico)
- Subtítulo de una línea explicando las líneas de servicio
- Video de fondo o carrusel de renders de alto impacto
- CTA principal: "Ver portafolio" / CTA secundario: "Agenda una consultoría"
**Interacción:** fade-in al cargar, scroll indicator sutil hacia la siguiente sección.

## 2. Portafolio / Proyectos
**Propósito:** sostener el enganche visual del Hero con prueba real de trabajo — sube aquí porque es el mayor gancho de la firma y aún no hay trayectoria extensa que mostrar en "Quiénes somos".
**Contenido:**
- Grid de proyectos con imagen, nombre, categoría, ubicación
- Filtro por tipo de servicio (Diseño / Urbanismo / Construcción / Inmobiliario / Visualización) — funciona también como mapa implícito de servicios
- Vista de detalle por proyecto: galería, descripción, renders, y si aplica, comparador antes/después
**Interacción:** filtros dinámicos sin recargar página; slider antes/después en proyectos que lo permitan; lightbox para imágenes en alta resolución.

## 3. Servicios
**Propósito:** una vez el visitante ya está enganchado con el portafolio, responder "¿exactamente qué hacen?".
**Contenido (una tarjeta o bloque por línea):**
1. Diseño arquitectónico
2. Urbanismo y planificación territorial
3. Consultorías
4. Construcción
5. Soluciones inmobiliarias
6. Visualización arquitectónica (destacada aparte, es diferencial de la firma)
- Cada servicio con: ícono, descripción corta, link a más detalle o al portafolio filtrado por esa categoría
**Interacción:** grid de tarjetas con hover/expand; opción de acordeón en mobile.

## 4. Quiénes somos
**Propósito:** generar confianza en el equipo una vez el usuario ya decidió que el trabajo le gusta.
**Contenido:**
- Historia breve de fundación y visión de la firma
- Presentación de los 3 socios (Helena Guerrero, Carlos Prada, Rafael Serrano), con foto, rol y enfoque de cada uno
- Filosofía de trabajo / diferenciadores (ej. integración de visualización + normativa + ejecución)
**Interacción:** tarjetas de socios con hover que revela más info o rol específico.

## 5. Proceso / Metodología
**Propósito:** dar seguridad sobre cómo se trabaja, no solo qué se entrega.
**Contenido:**
- Línea de tiempo simple: Diagnóstico → Diseño → Visualización → Construcción/Entrega
- Breve descripción de qué incluye cada etapa
**Interacción:** timeline horizontal (desktop) / vertical (mobile) con animación al hacer scroll.

## 6. Recursos (gancho de redes sociales)
**Propósito:** capturar tráfico de redes y contactos con contenido gratuito de valor.
**Contenido — tipos de recursos descargables:**
- Checklists (ej. trámite de licencia de construcción en Ibagué)
- Guías PDF de normativa (POT, índices de ocupación y construcción explicados simple)
- Plantillas (programa arquitectónico, checklist de recepción de obra)
- Glosario de términos urbanísticos
- Muestras de visualización en alta resolución
**Estructura:**
- Grid de tarjetas: imagen/ícono + título + descripción corta + botón "Descargar"
- Filtros por categoría: Normativa / Diseño / Visualización / Construcción
- Captura de correo antes de la descarga (formulario simple)
- Cada recurso debe tener URL propia para poder promocionarse individualmente en redes
**Interacción:** modal o página propia por recurso; contador simple de "nuevo" en los recientes.

## 7. Blog
**Propósito:** posicionar a los socios como referentes y generar tráfico orgánico constante con artículos propios.
**Contenido:**
- Listado de artículos con imagen destacada, título, categoría, autor (Helena / Carlos / Rafael) y fecha
- Vista de artículo individual con contenido enriquecido (texto, imágenes, renders de apoyo)
- Categorías temáticas (ej. normativa, diseño, urbanismo, visualización, construcción)
- Buscador o filtro por autor/categoría
**Interacción:** grid o lista de tarjetas con paginación; artículos relacionados al final de cada nota; botón de compartir en redes.

## 8. Cobertura / Zona de trabajo
**Propósito:** dejar claro el alcance geográfico.
**Contenido:** mapa o gráfico simple del corredor Ibagué–Bogotá con las ciudades/municipios de mayor actividad.

## 9. Testimonios / Clientes
**Propósito:** prueba social.
**Contenido:** citas breves de clientes, logos de aliados o clientes si existen.
**Interacción:** carrusel simple.

## 10. Contacto
**Propósito:** convertir la visita en un contacto real.
**Contenido:**
- Formulario (nombre, correo, tipo de servicio de interés, mensaje)
- Botón directo de WhatsApp
- Ubicación / mapa
- CTA repetido: "Agenda una consultoría"

## 11. Footer
**Contenido:** logo, datos legales (MESA URBANA SAS), redes sociales, enlaces rápidos al menú, correo de contacto.

---

## Consideraciones técnicas generales
- **Responsive first:** validar cada sección en mobile antes que en desktop (la mayoría del tráfico de redes llega desde celular)
- **Velocidad de carga:** comprimir renders e imágenes pesadas, usar lazy loading en galerías
- **SEO básico:** títulos y meta descripciones por página, especialmente en Recursos, Portafolio y Blog (tráfico orgánico local)
- **Animaciones:** microanimaciones al hacer scroll (fade-in, parallax leve), sin saturar — la prioridad visual son los renders
- **Modo oscuro (opcional):** evaluar si el portafolio de visualización se ve mejor con fondo oscuro

## Sugerencia de orden para dar las órdenes en Claude Code
1. Estructura base + navegación + Hero
2. Portafolio (con sistema de filtros)
3. Servicios
4. Quiénes somos
5. Recursos (con captura de correo)
6. Blog (listado + vista de artículo individual)
7. Proceso + Cobertura + Testimonios
8. Contacto + Footer
9. Pulido responsive, animaciones y SEO al final
