-- Base de datos OrgTrack
-- Crea la base (ejecútalo como superusuario, p. ej. postgres)
CREATE DATABASE orgtrack;

-- Conéctate a orgtrack (si usas psql: \c orgtrack)

-- ========================
-- Tablas base
-- ========================

CREATE TABLE usuarios (
  id               SERIAL PRIMARY KEY,
  nombre           VARCHAR(100),
  apellido         VARCHAR(100),
  correo           VARCHAR(100) UNIQUE,
  contrasena       VARCHAR(100),
  rol              VARCHAR(20),
  fecha_registro   TIMESTAMPTZ DEFAULT now(),
  CONSTRAINT chk_usuarios_rol CHECK (rol IN ('transportista','cliente','admin'))
);

CREATE TABLE vehiculos (
  id               SERIAL PRIMARY KEY,
  tipo             VARCHAR(50) NOT NULL,
  placa            VARCHAR(20) NOT NULL UNIQUE,
  capacidad        NUMERIC(10,2) NOT NULL,
  estado           VARCHAR(20) NOT NULL,
  fecha_registro   TIMESTAMPTZ DEFAULT now(),
  CONSTRAINT chk_vehiculos_estado CHECK (
    estado IN ('Mantenimiento','No Disponible','En ruta','Disponible')
  ),
  CONSTRAINT ck_vehiculos_tipo_detallado CHECK (
    tipo IN (
      'Pesado - Ventilado','Pesado - Aislado','Pesado - Refrigerado',
      'Mediano - Ventilado','Mediano - Aislado','Mediano - Refrigerado',
      'Ligero - Ventilado','Ligero - Aislado','Ligero - Refrigerado'
    )
  )
);

CREATE TABLE tipotransporte (
  id           SERIAL PRIMARY KEY,
  nombre       VARCHAR(50) NOT NULL,
  descripcion  VARCHAR(255)
);

CREATE TABLE recogidaentrega (
  id                         SERIAL PRIMARY KEY,
  fecha_recogida             DATE NOT NULL,
  hora_recogida              TIME NOT NULL,
  hora_entrega               TIME NOT NULL,
  instrucciones_recogida     VARCHAR(255),
  instrucciones_entrega      VARCHAR(255)
);

CREATE TABLE direccion (
  id                SERIAL PRIMARY KEY,
  id_usuario        INTEGER NOT NULL,
  nombreorigen      VARCHAR(200),
  origen_lng        DOUBLE PRECISION,
  origen_lat        DOUBLE PRECISION,
  nombredestino     VARCHAR(200),
  destino_lng       DOUBLE PRECISION,
  destino_lat       DOUBLE PRECISION,
  rutageojson       TEXT
);

CREATE TABLE direccionsegmento (
  id               SERIAL PRIMARY KEY,
  direccion_id     INTEGER NOT NULL,
  segmentogeojson  TEXT NOT NULL
);

CREATE TABLE envios (
  id               SERIAL PRIMARY KEY,
  id_usuario       INTEGER NOT NULL,
  estado           VARCHAR(50) NOT NULL,
  fecha_creacion   TIMESTAMPTZ DEFAULT now(),
  fecha_inicio     TIMESTAMPTZ,
  fecha_entrega    TIMESTAMPTZ,
  id_direccion     INTEGER NOT NULL,
  CONSTRAINT chk_envios_estado CHECK (
    estado IN ('Parcialmente entregado','Entregado','En curso','Asignado','Pendiente')
  )
);

CREATE TABLE historialestados (
  id         SERIAL PRIMARY KEY,
  id_envio   INTEGER NOT NULL,
  estado     VARCHAR(50) NOT NULL,
  fecha      TIMESTAMPTZ DEFAULT now()
);

CREATE TABLE transportistas (
  id               SERIAL PRIMARY KEY,
  id_usuario       INTEGER UNIQUE,
  ci               VARCHAR(20) NOT NULL UNIQUE,
  telefono         VARCHAR(20),
  estado           VARCHAR(20) NOT NULL,
  fecha_registro   TIMESTAMPTZ DEFAULT now(),
  CONSTRAINT chk_transportistas_estado CHECK (
    estado IN ('Inactivo','No Disponible','En ruta','Disponible')
  )
);

CREATE TABLE carga (
  id           SERIAL PRIMARY KEY,
  tipo         VARCHAR(50) NOT NULL,
  variedad     VARCHAR(50) NOT NULL,
  cantidad     INTEGER NOT NULL,
  empaquetado  VARCHAR(50) NOT NULL,
  peso         NUMERIC(10,2) NOT NULL
);

CREATE TABLE asignacionmultiple (
  id                   SERIAL PRIMARY KEY,
  id_envio             INTEGER NOT NULL,
  id_transportista     INTEGER,
  id_vehiculo          INTEGER,
  id_recogida_entrega  INTEGER NOT NULL,
  id_tipo_transporte   INTEGER NOT NULL,
  estado               VARCHAR(50) NOT NULL DEFAULT 'Pendiente',
  fecha_asignacion     TIMESTAMPTZ DEFAULT now(),
  fecha_inicio         TIMESTAMPTZ,
  fecha_fin            TIMESTAMPTZ,
  CONSTRAINT chk_asignacion_multiple_estado CHECK (
    estado IN ('Entregado','En curso','Pendiente')
  )
);

CREATE TABLE asignacioncarga (
  id_asignacion  INTEGER NOT NULL,
  id_carga       INTEGER NOT NULL,
  PRIMARY KEY (id_asignacion, id_carga)
);

CREATE TABLE checklistcondicionESTRANSPORTE (
  id                          SERIAL PRIMARY KEY,
  id_asignacion               INTEGER NOT NULL UNIQUE,
  temperatura_controlada      BOOLEAN NOT NULL,
  embalaje_adecuado           BOOLEAN NOT NULL,
  carga_segura                BOOLEAN NOT NULL,
  vehiculo_limpio             BOOLEAN NOT NULL,
  documentos_presentes        BOOLEAN NOT NULL,
  ruta_conocida               BOOLEAN NOT NULL,
  combustible_completo        BOOLEAN NOT NULL,
  gps_operativo               BOOLEAN NOT NULL,
  comunicacion_funcional      BOOLEAN NOT NULL,
  estado_general_aceptable    BOOLEAN NOT NULL,
  observaciones               VARCHAR(255),
  fecha                       TIMESTAMPTZ DEFAULT now()
);

CREATE TABLE checklistincidentestransporte (
  id                           SERIAL PRIMARY KEY,
  id_asignacion                INTEGER NOT NULL UNIQUE,
  retraso                      BOOLEAN NOT NULL,
  problema_mecanico            BOOLEAN NOT NULL,
  accidente                    BOOLEAN NOT NULL,
  perdida_carga                BOOLEAN NOT NULL,
  condiciones_climaticas_adversas BOOLEAN NOT NULL,
  ruta_alternativa_usada       BOOLEAN NOT NULL,
  contacto_cliente_dificultoso BOOLEAN NOT NULL,
  parada_imprevista            BOOLEAN NOT NULL,
  problemas_documentacion      BOOLEAN NOT NULL,
  otros_incidentes             BOOLEAN NOT NULL,
  descripcion_incidente        VARCHAR(255),
  fecha                        TIMESTAMPTZ DEFAULT now()
);

CREATE TABLE firmaenvio (
  id             SERIAL PRIMARY KEY,
  id_asignacion  INTEGER NOT NULL UNIQUE,
  imagenfirma    TEXT NOT NULL,
  fechafirma     TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE firmatransportista (
  id             SERIAL PRIMARY KEY,
  id_asignacion  INTEGER NOT NULL UNIQUE,
  imagenfirma    TEXT NOT NULL,
  fechafirma     TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE qrtoken (
  id                   SERIAL PRIMARY KEY,
  id_asignacion        INTEGER NOT NULL UNIQUE,
  id_usuario_cliente   INTEGER NOT NULL,
  token                TEXT NOT NULL UNIQUE,
  imagenqr             TEXT NOT NULL,
  usado                BOOLEAN NOT NULL DEFAULT FALSE,
  fecha_creacion       TIMESTAMPTZ NOT NULL DEFAULT now(),
  fecha_expiracion     TIMESTAMPTZ NOT NULL,
  CHECK (fecha_expiracion > fecha_creacion)
);

-- ========================
-- Claves foráneas
-- ========================

ALTER TABLE direccion           ADD FOREIGN KEY (id_usuario)        REFERENCES usuarios(id);
ALTER TABLE direccionsegmento   ADD FOREIGN KEY (direccion_id)      REFERENCES direccion(id) ON DELETE CASCADE;

ALTER TABLE envios              ADD FOREIGN KEY (id_usuario)        REFERENCES usuarios(id);
ALTER TABLE envios              ADD FOREIGN KEY (id_direccion)      REFERENCES direccion(id);

ALTER TABLE historialestados    ADD FOREIGN KEY (id_envio)          REFERENCES envios(id);

ALTER TABLE transportistas      ADD FOREIGN KEY (id_usuario)        REFERENCES usuarios(id);

ALTER TABLE asignacionmultiple  ADD FOREIGN KEY (id_envio)          REFERENCES envios(id);
ALTER TABLE asignacionmultiple  ADD FOREIGN KEY (id_transportista)  REFERENCES transportistas(id);
ALTER TABLE asignacionmultiple  ADD FOREIGN KEY (id_vehiculo)       REFERENCES vehiculos(id);
ALTER TABLE asignacionmultiple  ADD FOREIGN KEY (id_recogida_entrega) REFERENCES recogidaentrega(id);
ALTER TABLE asignacionmultiple  ADD FOREIGN KEY (id_tipo_transporte)  REFERENCES tipotransporte(id);

ALTER TABLE asignacioncarga     ADD FOREIGN KEY (id_asignacion)     REFERENCES asignacionmultiple(id);
ALTER TABLE asignacioncarga     ADD FOREIGN KEY (id_carga)          REFERENCES carga(id);

ALTER TABLE checklistcondicionESTRANSPORTE ADD FOREIGN KEY (id_asignacion) REFERENCES asignacionmultiple(id);
ALTER TABLE checklistincidentestransporte  ADD FOREIGN KEY (id_asignacion) REFERENCES asignacionmultiple(id);

ALTER TABLE firmaenvio          ADD FOREIGN KEY (id_asignacion)     REFERENCES asignacionmultiple(id) ON DELETE CASCADE;
ALTER TABLE firmatransportista  ADD FOREIGN KEY (id_asignacion)     REFERENCES asignacionmultiple(id) ON DELETE CASCADE;

ALTER TABLE qrtoken             ADD FOREIGN KEY (id_asignacion)     REFERENCES asignacionmultiple(id) ON DELETE CASCADE;
ALTER TABLE qrtoken             ADD FOREIGN KEY (id_usuario_cliente)REFERENCES usuarios(id) ON DELETE CASCADE;

-- ========================
-- Índices útiles
-- ========================
CREATE INDEX idx_qrtoken_usado ON qrtoken(usado);
CREATE INDEX idx_qrtoken_exp   ON qrtoken(fecha_expiracion);
CREATE INDEX idx_envios_id_direccion ON envios(id_direccion);
