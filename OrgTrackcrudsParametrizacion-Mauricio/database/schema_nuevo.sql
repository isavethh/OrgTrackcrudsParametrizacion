-- ===============================
-- ESQUEMA POSTGRESQL NUEVO
-- Refactorización completa
-- ===============================

-- DROP de todas las tablas en orden correcto (respetando foreign keys)
DROP TABLE IF EXISTS firmatransportista CASCADE;
DROP TABLE IF EXISTS firmaenvio CASCADE;
DROP TABLE IF EXISTS incidentes_transporte CASCADE;
DROP TABLE IF EXISTS checklist_condicion_detalle CASCADE;
DROP TABLE IF EXISTS checklist_condicion CASCADE;
DROP TABLE IF EXISTS asignacioncarga CASCADE;
DROP TABLE IF EXISTS qrtoken CASCADE;
DROP TABLE IF EXISTS asignacionmultiple CASCADE;
DROP TABLE IF EXISTS carga CASCADE;
DROP TABLE IF EXISTS historialestados CASCADE;
DROP TABLE IF EXISTS envios CASCADE;
DROP TABLE IF EXISTS direccionsegmento CASCADE;
DROP TABLE IF EXISTS direccion CASCADE;
DROP TABLE IF EXISTS recogidaentrega CASCADE;
DROP TABLE IF EXISTS transportistas CASCADE;
DROP TABLE IF EXISTS vehiculos CASCADE;
DROP TABLE IF EXISTS admin CASCADE;
DROP TABLE IF EXISTS cliente CASCADE;
DROP TABLE IF EXISTS usuarios CASCADE;
DROP TABLE IF EXISTS persona CASCADE;
DROP TABLE IF EXISTS tipos_incidente_transporte CASCADE;
DROP TABLE IF EXISTS condiciones_transporte CASCADE;
DROP TABLE IF EXISTS catalogo_carga CASCADE;
DROP TABLE IF EXISTS tipotransporte CASCADE;
DROP TABLE IF EXISTS estados_asignacion_multiple CASCADE;
DROP TABLE IF EXISTS estados_qrtoken CASCADE;
DROP TABLE IF EXISTS estados_envio CASCADE;
DROP TABLE IF EXISTS estados_transportista CASCADE;
DROP TABLE IF EXISTS estados_vehiculo CASCADE;
DROP TABLE IF EXISTS tipos_vehiculo CASCADE;
DROP TABLE IF EXISTS roles_usuario CASCADE;

-- ===============================
-- TABLAS DE CATÁLOGO
-- ===============================

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
    nombre      VARCHAR(50)  NOT NULL,
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

-- Tablas adicionales de catálogo para envíos y productos
CREATE TABLE tipo_empaque (
    id          SERIAL PRIMARY KEY,
    nombre      VARCHAR(100) NOT NULL UNIQUE,
    descripcion VARCHAR(255)
);

CREATE TABLE unidad_medida (
    id          SERIAL PRIMARY KEY,
    nombre      VARCHAR(100) NOT NULL UNIQUE,
    abreviatura VARCHAR(20)
);

CREATE TABLE categoria (
    id          SERIAL PRIMARY KEY,
    nombre      VARCHAR(100) NOT NULL UNIQUE,
    descripcion VARCHAR(255)
);

CREATE TABLE producto (
    id            SERIAL PRIMARY KEY,
    id_categoria  INTEGER REFERENCES categoria(id),
    nombre        VARCHAR(150) NOT NULL,
    descripcion   VARCHAR(255),
    peso_estimado NUMERIC(12,2) DEFAULT 0
);

CREATE TABLE peso_soportado (
    id          SERIAL PRIMARY KEY,
    nombre      VARCHAR(100) NOT NULL UNIQUE,
    peso_maximo NUMERIC(12,2) NOT NULL
);

CREATE TABLE tamano_transporte (
    id          SERIAL PRIMARY KEY,
    nombre      VARCHAR(100) NOT NULL UNIQUE,
    descripcion VARCHAR(255)
);
-- ===============================
-- TABLAS PRINCIPALES
-- ===============================

-- Nota: El modelo `Persona` se eliminó; sus campos se migraron a `usuarios`.
CREATE TABLE usuarios (
    id             SERIAL PRIMARY KEY,
    correo         VARCHAR(100) NOT NULL UNIQUE,
    contrasena     VARCHAR(100) NOT NULL,
    id_rol         INTEGER      NOT NULL,
    nombre         VARCHAR(100) NOT NULL,
    apellido       VARCHAR(100) NOT NULL,
    ci             VARCHAR(20)  NOT NULL UNIQUE,
    telefono       VARCHAR(20),
    fecha_registro TIMESTAMPTZ  NOT NULL DEFAULT now(),
    CONSTRAINT fk_usuarios_roles
        FOREIGN KEY (id_rol)     REFERENCES roles_usuario(id)
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
    CONSTRAINT fk_admin_usuario
        FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE vehiculos (
    id                 SERIAL PRIMARY KEY,
    id_tipo_vehiculo   INTEGER       NOT NULL,
    placa              VARCHAR(20)   NOT NULL UNIQUE,
    capacidad          NUMERIC(10,2) NOT NULL,
    id_estado_vehiculo INTEGER       NOT NULL,
    fecha_registro     TIMESTAMPTZ   NOT NULL DEFAULT now(),
    CONSTRAINT fk_vehiculos_tipo
        FOREIGN KEY (id_tipo_vehiculo)   REFERENCES tipos_vehiculo(id),
    CONSTRAINT fk_vehiculos_estado
        FOREIGN KEY (id_estado_vehiculo) REFERENCES estados_vehiculo(id)
);

CREATE TABLE transportistas (
    id                      SERIAL PRIMARY KEY,
    id_usuario              INTEGER      UNIQUE,
    ci                      VARCHAR(20)  NOT NULL,
    telefono                VARCHAR(20),
    id_estado_transportista INTEGER      NOT NULL,
    fecha_registro          TIMESTAMPTZ  NOT NULL DEFAULT now(),
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
    nombreorigen  VARCHAR(200),
    origen_lng    DOUBLE PRECISION,
    origen_lat    DOUBLE PRECISION,
    nombredestino VARCHAR(200),
    destino_lng   DOUBLE PRECISION,
    destino_lat   DOUBLE PRECISION,
    rutageojson   TEXT
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
    id                       SERIAL PRIMARY KEY,
    id_usuario               INTEGER     NOT NULL,
    fecha_creacion           TIMESTAMPTZ NOT NULL DEFAULT now(),
    fecha_inicio             TIMESTAMPTZ,
    fecha_entrega            TIMESTAMPTZ,
    fecha_entrega_aproximada DATE,
    hora_entrega_aproximada  TIME,
    id_direccion             INTEGER     NOT NULL,
    peso_total_envio         NUMERIC(12,2) DEFAULT 0,
    costo_total_envio        NUMERIC(12,2) DEFAULT 0,
    codigo_qr                VARCHAR(255),
    estado_tracking          VARCHAR(50) DEFAULT 'pendiente',
    fecha_inicio_tracking    TIMESTAMPTZ,
    fecha_fin_tracking       TIMESTAMPTZ,
    ubicacion_actual_lat     NUMERIC(10,7),
    ubicacion_actual_lng     NUMERIC(10,7),
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
    observaciones   TEXT,
    CONSTRAINT fk_historial_envio
        FOREIGN KEY (id_envio)        REFERENCES envios(id),
    CONSTRAINT fk_historial_estado
        FOREIGN KEY (id_estado_envio) REFERENCES estados_envio(id)
);

-- Tabla para productos asociados a un envío
CREATE TABLE envio_productos (
    id               SERIAL PRIMARY KEY,
    id_envio         INTEGER NOT NULL REFERENCES envios(id) ON DELETE CASCADE,
    categoria        VARCHAR(100) NOT NULL,
    producto         VARCHAR(100) NOT NULL,
    cantidad         INTEGER NOT NULL DEFAULT 1,
    peso_por_unidad  NUMERIC(12,2) NOT NULL DEFAULT 0,
    peso_total       NUMERIC(12,2) NOT NULL DEFAULT 0,
    costo_unitario   NUMERIC(12,2) NOT NULL DEFAULT 0,
    costo_total      NUMERIC(12,2) NOT NULL DEFAULT 0,
    id_tipo_empaque  INTEGER REFERENCES tipo_empaque(id),
    id_unidad_medida INTEGER REFERENCES unidad_medida(id),
    created_at       TIMESTAMPTZ DEFAULT now(),
    updated_at       TIMESTAMPTZ DEFAULT now()
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

-- ===============================
-- ÍNDICES
-- ===============================

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

-- ===============================
-- DATOS INICIALES
-- ===============================

INSERT INTO roles_usuario (codigo, nombre, descripcion) VALUES 
('ADMIN', 'Administrador', 'Usuario administrador del sistema'),
('CLIENT', 'Cliente', 'Cliente que solicita envíos'),
('TRANSP', 'Transportista', 'Transportista que realiza entregas');

INSERT INTO estados_vehiculo (nombre) VALUES 
('Disponible'),
('En uso'),
('Mantenimiento'),
('Fuera de servicio');

INSERT INTO estados_transportista (nombre) VALUES 
('Disponible'),
('En ruta'),
('Inactivo'),
('En camino'),
('Ocurrió un accidente');

INSERT INTO estados_envio (nombre) VALUES 
('Pendiente'),
('En proceso'),
('En tránsito'),
('Entregado'),
('Cancelado');

INSERT INTO estados_asignacion_multiple (nombre) VALUES 
('Pendiente'),
('Asignada'),
('En curso'),
('Completada'),
('Cancelada');

INSERT INTO estados_qrtoken (nombre) VALUES 
('Activo'),
('Usado'),
('Expirado'),
('Cancelado');

INSERT INTO tipotransporte (nombre, descripcion) VALUES 
('Terrestre', 'Transporte por carretera'),
('Aéreo', 'Transporte por avión'),
('Marítimo', 'Transporte por barco');

-- Datos iniciales para catálogos de envíos/productos
INSERT INTO tipo_empaque (nombre, descripcion) VALUES
('Caja', 'Caja de cartón o madera'),
('Saco', 'Saco o bolsa grande'),
('Bolsa', 'Bolsa plástica o de tela'),
('Pallet', 'Mercadería sobre pallet'),
('Contenedor', 'Contenedor marítimo'),
('Bundle', 'Paquete agrupado');

INSERT INTO unidad_medida (nombre, abreviatura) VALUES
('Kilogramo', 'kg'),
('Gramo', 'g'),
('Libra', 'lb'),
('Unidad', 'u'),
('Litro', 'l'),
('Mililitro', 'ml'),
('Metro cúbico', 'm3'),
('Pieza', 'pz'),
('Centímetro', 'cm');

INSERT INTO categoria (nombre, descripcion) VALUES
('Alimentos', 'Productos alimenticios'),
('Electrónica', 'Aparatos y componentes electrónicos'),
('Ropa', 'Prendas de vestir'),
('Hogar', 'Artículos para el hogar'),
('Materiales', 'Materiales de construcción y ferretería'),
('Farmacéutico', 'Medicamentos y productos de salud');

INSERT INTO producto (id_categoria, nombre, descripcion, peso_estimado) VALUES
(1, 'Arroz 1kg', 'Bolsa de arroz', 1.0),
(1, 'Aceite 1L', 'Botella de aceite', 1.1),
(2, 'Cargador USB', 'Cargador universal', 0.2),
(2, 'Auriculares', 'Auriculares con cable', 0.15),
(3, 'Camiseta', 'Camiseta algodón talla M', 0.25),
(3, 'Pantalón', 'Pantalón jeans talla 32', 0.8),
(4, 'Almohada', 'Almohada poliéster', 0.6),
(4, 'Plato Cerámica', 'Plato para comedor', 0.4),
(5, 'Cemento 25kg', 'Saco de cemento', 25.0),
(6, 'Jarabe 250ml', 'Jarabe medicinal', 0.3);

INSERT INTO peso_soportado (nombre, peso_maximo) VALUES
('Ligero', 5.00),
('Medio', 50.00),
('Pesado', 200.00),
('Extra Pesado', 1000.00);

INSERT INTO tamano_transporte (nombre, descripcion) VALUES
('Pequeño', 'Capacidad pequeña, ideal para paquetería'),
('Mediano', 'Vehículos medianos y furgonetas'),
('Grande', 'Camiones y tráileres'),
('Contenedor', 'Grandes contenedores marítimos');
