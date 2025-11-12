-- ===============================
-- Catálogos parametrizables
-- ===============================
DROP TABLE IF EXISTS sessions CASCADE;
DROP TABLE IF EXISTS cache CASCADE;
DROP TABLE IF EXISTS cache_locks CASCADE;
DROP TABLE IF EXISTS jobs CASCADE;
DROP TABLE IF EXISTS job_batches CASCADE;
DROP TABLE IF EXISTS failed_jobs CASCADE;
DROP TABLE IF EXISTS estado_transportista CASCADE;
DROP TABLE IF EXISTS tipo_transporte CASCADE;
DROP TABLE IF EXISTS tamano_transporte CASCADE;
DROP TABLE IF EXISTS tipo_empaque CASCADE;
DROP TABLE IF EXISTS unidad_medida CASCADE;
DROP TABLE IF EXISTS usuario CASCADE;
DROP TABLE IF EXISTS admin CASCADE;
DROP TABLE IF EXISTS cliente CASCADE;
DROP TABLE IF EXISTS transportista CASCADE;
DROP TABLE IF EXISTS qrtoken CASCADE;
DROP TABLE IF EXISTS vehiculo CASCADE;
DROP TABLE IF EXISTS peso_soportado CASCADE;
DROP TABLE IF EXISTS envio CASCADE;
DROP TABLE IF EXISTS direccion CASCADE;
DROP TABLE IF EXISTS asignacionmultiple CASCADE;
DROP TABLE IF EXISTS checklistcondicioncliente CASCADE;
DROP TABLE IF EXISTS checklistcondiciontransportista CASCADE;
DROP TABLE IF EXISTS checklistincidentetransporte CASCADE;

-- ===============================
-- Tablas de Laravel (sesiones, cache, jobs)
-- ===============================
CREATE TABLE sessions (
  id VARCHAR(255) PRIMARY KEY,
  user_id BIGINT NULL,
  ip_address VARCHAR(45) NULL,
  user_agent TEXT NULL,
  payload TEXT NOT NULL,
  last_activity INTEGER NOT NULL
);

CREATE INDEX sessions_user_id_index ON sessions(user_id);
CREATE INDEX sessions_last_activity_index ON sessions(last_activity);

CREATE TABLE cache (
  key VARCHAR(255) PRIMARY KEY,
  value TEXT NOT NULL,
  expiration INTEGER NOT NULL
);

CREATE TABLE cache_locks (
  key VARCHAR(255) PRIMARY KEY,
  owner VARCHAR(255) NOT NULL,
  expiration INTEGER NOT NULL
);

CREATE TABLE jobs (
  id BIGSERIAL PRIMARY KEY,
  queue VARCHAR(255) NOT NULL,
  payload TEXT NOT NULL,
  attempts SMALLINT NOT NULL,
  reserved_at INTEGER NULL,
  available_at INTEGER NOT NULL,
  created_at INTEGER NOT NULL
);

CREATE INDEX jobs_queue_index ON jobs(queue);

CREATE TABLE job_batches (
  id VARCHAR(255) PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  total_jobs INTEGER NOT NULL,
  pending_jobs INTEGER NOT NULL,
  failed_jobs INTEGER NOT NULL,
  failed_job_ids TEXT NOT NULL,
  options TEXT NULL,
  cancelled_at INTEGER NULL,
  created_at INTEGER NOT NULL,
  finished_at INTEGER NULL
);

CREATE TABLE failed_jobs (
  id BIGSERIAL PRIMARY KEY,
  uuid VARCHAR(255) UNIQUE NOT NULL,
  connection TEXT NOT NULL,
  queue TEXT NOT NULL,
  payload TEXT NOT NULL,
  exception TEXT NOT NULL,
  failed_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE estado_transportista (
  id SERIAL PRIMARY KEY,
  nombre VARCHAR(50) UNIQUE NOT NULL
);
 
CREATE TABLE tipo_transporte (
  id SERIAL PRIMARY KEY,
  nombre VARCHAR(50) UNIQUE NOT NULL
);
 
CREATE TABLE tamano_transporte (
  id SERIAL PRIMARY KEY,
  nombre VARCHAR(50) UNIQUE NOT NULL
);
 
CREATE TABLE tipo_empaque (
  id SERIAL PRIMARY KEY,
  nombre VARCHAR(50) UNIQUE NOT NULL
);
 
CREATE TABLE unidad_medida (
  id SERIAL PRIMARY KEY,
  nombre VARCHAR(20) UNIQUE NOT NULL
);
 
INSERT INTO estado_transportista (nombre) VALUES ('Disponible'), ('En ruta'), ('Inactivo'), ('En camino'), ('Ocurrió un accidente');
INSERT INTO unidad_medida (nombre) VALUES ('kg'), ('litros'), ('toneladas');
 
-- ===============================
-- Usuarios y extensiones
-- ===============================
CREATE TABLE usuario (
  id SERIAL PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  apellido VARCHAR(100),
  correo VARCHAR(100) UNIQUE NOT NULL,
  contrasena VARCHAR(100) NOT NULL,
  fecha_registro TIMESTAMPTZ DEFAULT now()
);
 
CREATE TABLE admin (
  id SERIAL PRIMARY KEY,
  usuario_id INTEGER UNIQUE NOT NULL REFERENCES usuario(id) ON DELETE CASCADE
);
 
CREATE TABLE cliente (
  id SERIAL PRIMARY KEY,
  usuario_id INTEGER UNIQUE NOT NULL REFERENCES usuario(id) ON DELETE CASCADE,
  telefono VARCHAR(20),
  direccion_entrega VARCHAR(255)
);
 
CREATE TABLE transportista (
  id SERIAL PRIMARY KEY,
  usuario_id INTEGER UNIQUE NOT NULL REFERENCES usuario(id) ON DELETE CASCADE,
  ci VARCHAR(20) NOT NULL UNIQUE,
  placa VARCHAR(20) NOT NULL UNIQUE,
  telefono VARCHAR(20),
  estado_id INTEGER NOT NULL REFERENCES estado_transportista(id),
  fecha_registro TIMESTAMPTZ DEFAULT now()
);
 
-- ===============================
-- QR Token (vinculado solo a cliente)
-- ===============================
CREATE TABLE qrtoken (
  id SERIAL PRIMARY KEY,
  cliente_id INTEGER NOT NULL REFERENCES cliente(id) ON DELETE CASCADE,
  token TEXT NOT NULL UNIQUE,
  imagenqr TEXT NOT NULL,
  usado BOOLEAN NOT NULL DEFAULT FALSE,
  fecha_creacion TIMESTAMPTZ NOT NULL DEFAULT now(),
  fecha_expiracion TIMESTAMPTZ NOT NULL,
  CHECK (fecha_expiracion > fecha_creacion)
);
 
-- ===============================
-- Vehículo y Peso soportado
-- ===============================
CREATE TABLE vehiculo (
  id SERIAL PRIMARY KEY,
  tipo_transporte_id INTEGER NOT NULL REFERENCES tipo_transporte(id),
  tamano_transporte_id INTEGER NOT NULL REFERENCES tamano_transporte(id),
  placa VARCHAR(20) UNIQUE NOT NULL,
  marca VARCHAR(50),
  modelo VARCHAR(50),
  estado VARCHAR(50) NOT NULL,
  admin_id INTEGER REFERENCES admin(id),
  fecha_registro TIMESTAMPTZ DEFAULT now()
);
 
CREATE TABLE peso_soportado (
  id SERIAL PRIMARY KEY,
  vehiculo_id INTEGER NOT NULL REFERENCES vehiculo(id) ON DELETE CASCADE,
  valor NUMERIC(10,2) NOT NULL,
  unidad VARCHAR(20) NOT NULL CHECK (unidad IN ('kg', 'toneladas')),
  descripcion VARCHAR(100)
);
 
-- ===============================
-- Envío
-- ===============================
CREATE TABLE envio (
  id SERIAL PRIMARY KEY,
  estado VARCHAR(50) NOT NULL,
  tipo_empaque_id INTEGER REFERENCES tipo_empaque(id),
  peso NUMERIC(10,2),
  volumen NUMERIC(10,2),
  unidad_medida_id INTEGER REFERENCES unidad_medida(id),
  admin_id INTEGER REFERENCES admin(id),
  fecha_envio TIMESTAMPTZ DEFAULT now(),
  fecha_entrega_estimada TIMESTAMPTZ
);
 
-- ===============================
-- Dirección asociada a envío
-- ===============================
CREATE TABLE direccion (
  id SERIAL PRIMARY KEY,
  envio_id INTEGER NOT NULL REFERENCES envio(id) ON DELETE CASCADE,
  nombre_ruta VARCHAR(100) NOT NULL,
  descripcion TEXT,
  latitud NUMERIC(10,8),
  longitud NUMERIC(11,8),
  orden INTEGER DEFAULT 1,
  ruta_geojson TEXT,
  fecha_creacion TIMESTAMPTZ DEFAULT now()
);
 
-- ===============================
-- Asignación múltiple
-- ===============================
CREATE TABLE asignacionmultiple (
  id SERIAL PRIMARY KEY,
  envio_id INTEGER REFERENCES envio(id),
  qrtoken_id INTEGER REFERENCES qrtoken(id),
  transportista_id INTEGER REFERENCES transportista(id),
  tipo_transporte_id INTEGER REFERENCES tipo_transporte(id),
  recogida_entrega_id INTEGER,
  estado VARCHAR(50),
  fecha_asignacion TIMESTAMPTZ DEFAULT now(),
  fecha_inicio TIMESTAMPTZ,
  fecha_fin TIMESTAMPTZ
);
 
-- ===============================
-- Checklist
-- ===============================
CREATE TABLE checklistcondicioncliente (
  id SERIAL PRIMARY KEY,
  asignacion_id INTEGER NOT NULL UNIQUE REFERENCES asignacionmultiple(id) ON DELETE CASCADE,
  recibido_correcto BOOLEAN NOT NULL,
  sin_danos BOOLEAN NOT NULL,
  entrega_puntual BOOLEAN NOT NULL,
  instrucciones_cumplidas BOOLEAN NOT NULL,
  observaciones VARCHAR(255),
  fecha TIMESTAMPTZ DEFAULT now()
);
 
CREATE TABLE checklistcondiciontransportista (
  id SERIAL PRIMARY KEY,
  asignacion_id INTEGER NOT NULL UNIQUE REFERENCES asignacionmultiple(id) ON DELETE CASCADE,
  temperatura_controlada BOOLEAN NOT NULL,
  embalaje_adecuado BOOLEAN NOT NULL,
  carga_segura BOOLEAN NOT NULL,
  vehiculo_limpio BOOLEAN NOT NULL,
  documentos_presentes BOOLEAN NOT NULL,
  ruta_conocida BOOLEAN NOT NULL,
  combustible_completo BOOLEAN NOT NULL,
  gps_operativo BOOLEAN NOT NULL,
  comunicacion_funcional BOOLEAN NOT NULL,
  estado_general_aceptable BOOLEAN NOT NULL,
  observaciones VARCHAR(255),
  fecha TIMESTAMPTZ DEFAULT now()
);
 
CREATE TABLE checklistincidentetransporte (
  id SERIAL PRIMARY KEY,
  asignacion_id INTEGER NOT NULL UNIQUE REFERENCES asignacionmultiple(id) ON DELETE CASCADE,
  retraso BOOLEAN NOT NULL,
  problema_mecanico BOOLEAN NOT NULL,
  accidente BOOLEAN NOT NULL,
  perdida_carga BOOLEAN NOT NULL,
  condiciones_climaticas_adversas BOOLEAN NOT NULL,
  ruta_alternativa_usada BOOLEAN NOT NULL,
  contacto_cliente_dificultoso BOOLEAN NOT NULL,
  parada_imprevista BOOLEAN NOT NULL,
  problemas_documentacion BOOLEAN NOT NULL,
  otros_incidentes BOOLEAN NOT NULL,
  descripcion_incidente VARCHAR(255),
  fecha TIMESTAMPTZ DEFAULT now()
);
 
-- ===============================
-- Índices útiles
-- ===============================
CREATE INDEX idx_envio_admin ON envio(admin_id);
CREATE INDEX idx_vehiculo_admin ON vehiculo(admin_id);
CREATE INDEX idx_asignacion_qrtoken ON asignacionmultiple(qrtoken_id);
CREATE INDEX idx_asignacion_transportista ON asignacionmultiple(transportista_id);
CREATE INDEX idx_asignacion_envio ON asignacionmultiple(envio_id);
CREATE INDEX idx_envio_estado ON envio(estado);
CREATE INDEX idx_vehiculo_estado ON vehiculo(estado);
CREATE INDEX idx_direccion_envio ON direccion(envio_id);
CREATE INDEX idx_qrtoken_cliente ON qrtoken(cliente_id);
CREATE INDEX idx_qrtoken_token ON qrtoken(token);
CREATE INDEX idx_qrtoken_usado ON qrtoken(usado);
CREATE INDEX idx_qrtoken_expiracion ON qrtoken(fecha_expiracion);
