CREATE TABLE roles_usuario (
    id          SERIAL PRIMARY KEY,
    codigo      VARCHAR(30)  NOT NULL UNIQUE,
    nombre      VARCHAR(50)  NOT NULL,
    descripcion VARCHAR(150)
);

CREATE TABLE tipos_vehiculo (
    id          SERIAL PRIMARY KEY,
    nombre      VARCHAR(50)  NOT NULL UNIQUE,
    descripcion VARCHAR(150)
);

CREATE TABLE estados_vehiculo (
    id      SERIAL PRIMARY KEY,
    nombre  VARCHAR(30) NOT NULL UNIQUE
);

CREATE TABLE estados_transportista (
    id      SERIAL PRIMARY KEY,
    nombre  VARCHAR(30) NOT NULL UNIQUE
);

CREATE TABLE estados_envio (
    id      SERIAL PRIMARY KEY,
    nombre  VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE estados_asignacion_multiple (
    id      SERIAL PRIMARY KEY,
    nombre  VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE tipotransporte (
    id          SERIAL PRIMARY KEY,
    nombre      VARCHAR(50)  NOT NULL UNIQUE,
    descripcion VARCHAR(255)
);

CREATE TABLE catalogo_carga (
    id          SERIAL PRIMARY KEY,
    tipo        VARCHAR(50) NOT NULL,
    variedad    VARCHAR(50) NOT NULL,
    empaque     VARCHAR(50) NOT NULL,
    descripcion VARCHAR(150)
);

CREATE TABLE condiciones_transporte (
    id          SERIAL PRIMARY KEY,
    codigo      VARCHAR(50)  NOT NULL UNIQUE,
    titulo      VARCHAR(100) NOT NULL,
    descripcion VARCHAR(255)
);

CREATE TABLE tipos_incidente_transporte (
    id          SERIAL PRIMARY KEY,
    codigo      VARCHAR(50)  NOT NULL UNIQUE,
    titulo      VARCHAR(100) NOT NULL,
    descripcion VARCHAR(255)
);

CREATE TABLE estados_qrtoken (
    id      SERIAL PRIMARY KEY,
    nombre  VARCHAR(30) NOT NULL UNIQUE
);

CREATE TABLE persona (
    id        SERIAL PRIMARY KEY,
    nombre    VARCHAR(100) NOT NULL,
    apellido  VARCHAR(100) NOT NULL,
    ci        VARCHAR(20)  NOT NULL UNIQUE,
    telefono  VARCHAR(20)
);

CREATE TABLE usuarios (
    id             SERIAL PRIMARY KEY,
    correo         VARCHAR(100) NOT NULL UNIQUE,
    contrasena     VARCHAR(100) NOT NULL,
    id_rol         INTEGER      NOT NULL,
    fecha_registro TIMESTAMPTZ  NOT NULL DEFAULT now(),
    id_persona     INTEGER      NOT NULL,
    CONSTRAINT fk_usuarios_roles
        FOREIGN KEY (id_rol)     REFERENCES roles_usuario(id),
    CONSTRAINT fk_usuarios_persona
        FOREIGN KEY (id_persona) REFERENCES persona(id)
);

CREATE TABLE cliente (
    id         SERIAL PRIMARY KEY,
    id_usuario INTEGER NOT NULL UNIQUE,
    CONSTRAINT fk_cliente_usuario
        FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE admin (
    id           SERIAL PRIMARY KEY,
    id_usuario   INTEGER NOT NULL UNIQUE,
    nivel_acceso INTEGER DEFAULT 1,
    CONSTRAINT fk_admin_usuario
        FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE vehiculos (
    id                 SERIAL PRIMARY KEY,
    id_tipo_vehiculo   INTEGER       NOT NULL,
    id_tipo_transporte INTEGER,
    placa              VARCHAR(20)   NOT NULL UNIQUE,
    capacidad          NUMERIC(10,2) NOT NULL,
    id_estado_vehiculo INTEGER       NOT NULL,
    fecha_registro     TIMESTAMPTZ   NOT NULL DEFAULT now(),
    CONSTRAINT fk_vehiculos_tipo
        FOREIGN KEY (id_tipo_vehiculo)   REFERENCES tipos_vehiculo(id),
    CONSTRAINT fk_vehiculos_tipo_transporte
        FOREIGN KEY (id_tipo_transporte) REFERENCES tipotransporte(id),
    CONSTRAINT fk_vehiculos_estado
        FOREIGN KEY (id_estado_vehiculo) REFERENCES estados_vehiculo(id)
);

CREATE TABLE transportistas (
    id                      SERIAL PRIMARY KEY,
    id_usuario              INTEGER      NOT NULL UNIQUE,
    id_estado_transportista INTEGER     NOT NULL,
    fecha_registro          TIMESTAMPTZ NOT NULL DEFAULT now(),
    CONSTRAINT fk_transportistas_usuario
        FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE,
    CONSTRAINT fk_transportistas_estado
        FOREIGN KEY (id_estado_transportista) REFERENCES estados_transportista(id)
);

CREATE TABLE recogidaentrega (
    id                      SERIAL PRIMARY KEY,
    fecha_recogida          DATE NOT NULL,
    hora_recogida           TIME NOT NULL,
    hora_entrega            TIME NOT NULL,
    instrucciones_recogida  VARCHAR(255),
    instrucciones_entrega   VARCHAR(255)
);

CREATE TABLE direccion (
    id            SERIAL PRIMARY KEY,
    id_usuario    INTEGER,
    nombreorigen  VARCHAR(200),
    origen_lng    DOUBLE PRECISION,
    origen_lat    DOUBLE PRECISION,
    nombredestino VARCHAR(200),
    destino_lng   DOUBLE PRECISION,
    destino_lat   DOUBLE PRECISION,
    rutageojson   TEXT,
    CONSTRAINT fk_direccion_usuario
        FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE direccionsegmento (
    id              SERIAL PRIMARY KEY,
    direccion_id    INTEGER NOT NULL,
    segmentogeojson TEXT    NOT NULL,
    CONSTRAINT fk_direccionsegmento_direccion
        FOREIGN KEY (direccion_id) REFERENCES direccion(id) ON DELETE CASCADE
);

CREATE TABLE carga (
    id                SERIAL PRIMARY KEY,
    id_catalogo_carga INTEGER       NOT NULL,
    cantidad          INTEGER       NOT NULL,
    peso              NUMERIC(10,2) NOT NULL,
    CONSTRAINT fk_carga_catalogo
        FOREIGN KEY (id_catalogo_carga) REFERENCES catalogo_carga(id)
);

CREATE TABLE envios (
    id             SERIAL PRIMARY KEY,
    id_usuario     INTEGER     NOT NULL,
    fecha_creacion TIMESTAMPTZ NOT NULL DEFAULT now(),
    fecha_inicio   TIMESTAMPTZ,
    fecha_entrega  TIMESTAMPTZ,
    id_direccion   INTEGER     NOT NULL,
    CONSTRAINT fk_envios_usuario
        FOREIGN KEY (id_usuario)   REFERENCES usuarios(id),
    CONSTRAINT fk_envios_direccion
        FOREIGN KEY (id_direccion) REFERENCES direccion(id)
);

CREATE TABLE historialestados (
    id              SERIAL PRIMARY KEY,
    id_envio        INTEGER     NOT NULL,
    id_estado_envio INTEGER     NOT NULL,
    fecha           TIMESTAMPTZ NOT NULL DEFAULT now(),
    CONSTRAINT fk_historial_envio
        FOREIGN KEY (id_envio)        REFERENCES envios(id),
    CONSTRAINT fk_historial_estado
        FOREIGN KEY (id_estado_envio) REFERENCES estados_envio(id)
);

CREATE TABLE asignacionmultiple (
    id                   SERIAL PRIMARY KEY,
    id_envio             INTEGER     NOT NULL,
    id_transportista     INTEGER,
    id_vehiculo          INTEGER,
    id_recogida_entrega  INTEGER     NOT NULL,
    id_tipo_transporte   INTEGER     NOT NULL,
    id_estado_asignacion INTEGER     NOT NULL,
    fecha_asignacion     TIMESTAMPTZ NOT NULL DEFAULT now(),
    fecha_inicio         TIMESTAMPTZ,
    fecha_fin            TIMESTAMPTZ,
    CONSTRAINT fk_asig_envio
        FOREIGN KEY (id_envio)             REFERENCES envios(id),
    CONSTRAINT fk_asig_transportista
        FOREIGN KEY (id_transportista)     REFERENCES transportistas(id),
    CONSTRAINT fk_asig_vehiculo
        FOREIGN KEY (id_vehiculo)          REFERENCES vehiculos(id),
    CONSTRAINT fk_asig_recogida
        FOREIGN KEY (id_recogida_entrega)  REFERENCES recogidaentrega(id),
    CONSTRAINT fk_asig_tipotransporte
        FOREIGN KEY (id_tipo_transporte)   REFERENCES tipotransporte(id),
    CONSTRAINT fk_asig_estado
        FOREIGN KEY (id_estado_asignacion) REFERENCES estados_asignacion_multiple(id)
);

CREATE TABLE asignacioncarga (
    id_asignacion INTEGER NOT NULL,
    id_carga      INTEGER NOT NULL,
    PRIMARY KEY (id_asignacion, id_carga),
    CONSTRAINT fk_asigcarga_asig
        FOREIGN KEY (id_asignacion) REFERENCES asignacionmultiple(id),
    CONSTRAINT fk_asigcarga_carga
        FOREIGN KEY (id_carga)      REFERENCES carga(id)
);

CREATE TABLE checklist_condicion (
    id            SERIAL PRIMARY KEY,
    id_asignacion INTEGER     NOT NULL UNIQUE,
    fecha         TIMESTAMPTZ NOT NULL DEFAULT now(),
    observaciones VARCHAR(255),
    CONSTRAINT fk_check_asig
        FOREIGN KEY (id_asignacion) REFERENCES asignacionmultiple(id)
);

CREATE TABLE checklist_condicion_detalle (
    id           SERIAL PRIMARY KEY,
    id_checklist INTEGER     NOT NULL,
    id_condicion INTEGER     NOT NULL,
    valor        BOOLEAN     NOT NULL,
    comentario   VARCHAR(255),
    CONSTRAINT fk_checkdet_check
        FOREIGN KEY (id_checklist) REFERENCES checklist_condicion(id),
    CONSTRAINT fk_checkdet_cond
        FOREIGN KEY (id_condicion) REFERENCES condiciones_transporte(id)
);

CREATE TABLE incidentes_transporte (
    id                    SERIAL PRIMARY KEY,
    id_asignacion         INTEGER     NOT NULL,
    id_tipo_incidente     INTEGER     NOT NULL,
    descripcion_incidente VARCHAR(255),
    fecha                 TIMESTAMPTZ NOT NULL DEFAULT now(),
    CONSTRAINT fk_incid_asig
        FOREIGN KEY (id_asignacion)     REFERENCES asignacionmultiple(id),
    CONSTRAINT fk_incid_tipo
        FOREIGN KEY (id_tipo_incidente) REFERENCES tipos_incidente_transporte(id)
);

CREATE TABLE firmaenvio (
    id            SERIAL PRIMARY KEY,
    id_asignacion INTEGER     NOT NULL UNIQUE,
    imagenfirma   TEXT        NOT NULL,
    fechafirma    TIMESTAMPTZ NOT NULL DEFAULT now(),
    CONSTRAINT fk_firmaenvio_asig
        FOREIGN KEY (id_asignacion) REFERENCES asignacionmultiple(id) ON DELETE CASCADE
);

CREATE TABLE firmatransportista (
    id            SERIAL PRIMARY KEY,
    id_asignacion INTEGER     NOT NULL UNIQUE,
    imagenfirma   TEXT        NOT NULL,
    fechafirma    TIMESTAMPTZ NOT NULL DEFAULT now(),
    CONSTRAINT fk_firmatransp_asig
        FOREIGN KEY (id_asignacion) REFERENCES asignacionmultiple(id) ON DELETE CASCADE
);

CREATE TABLE qrtoken (
    id                SERIAL PRIMARY KEY,
    id_asignacion     INTEGER      NOT NULL UNIQUE,
    id_estado_qrtoken INTEGER      NOT NULL,
    token             VARCHAR(500) NOT NULL UNIQUE,
    imagenqr          TEXT         NOT NULL,
    fecha_creacion    TIMESTAMPTZ  NOT NULL DEFAULT now(),
    fecha_expiracion  TIMESTAMPTZ  NOT NULL,
    CONSTRAINT ck_qrtoken_fecha
        CHECK (fecha_expiracion > fecha_creacion),
    CONSTRAINT fk_qrtoken_asig
        FOREIGN KEY (id_asignacion)     REFERENCES asignacionmultiple(id) ON DELETE CASCADE,
    CONSTRAINT fk_qrtoken_estado
        FOREIGN KEY (id_estado_qrtoken) REFERENCES estados_qrtoken(id)
);

CREATE INDEX ix_envios_id_direccion 
    ON envios(id_direccion);

CREATE INDEX ix_envios_id_usuario 
    ON envios(id_usuario);

CREATE INDEX ix_historial_envio 
    ON historialestados(id_envio, fecha);

CREATE INDEX ix_qrtoken_exp 
    ON qrtoken(fecha_expiracion);

CREATE INDEX ix_qrtoken_estado 
    ON qrtoken(id_estado_qrtoken);

CREATE INDEX ix_direccion_id_usuario 
    ON direccion(id_usuario);